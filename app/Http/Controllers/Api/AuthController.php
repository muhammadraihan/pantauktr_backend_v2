<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Notifications\SendOTPNotification;
use App\Models\Pelapor;
use App\Models\Laporan;
use Carbon\Carbon;
use Seshac\Otp\Otp;
use Seshac\Otp\Models\Otp as OtpModel;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;


use Auth;
use DB;
use Defuse\Crypto\Crypto;
use Exception;
use Hash;
use Helper;
use Log;
use Validator;

class AuthController extends Controller
{
  public function RegisterPelapor(Request $request)
  {
    $rules = [
      'email' => 'bail|required|email|unique:pelapors',
      'password' => 'required|min:8',
    ];

    $messages = [
      '*.required' => 'Tidak boleh kosong',
      '*.email' => 'Format email salah,mohon gunakan alamat email',
      '*.unique' => 'Email sudah digunakan,harap gunakan alamat email yang lain',
      '*.min' => 'Password minimal 8 karakter',
    ];
    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->messages()
      ]);
    }
    $password = trim($request->password);
    $extract_username = explode("@", $request->email);
    $firstname = $extract_username[0];
    config(['auth.guards.pelapors-api.driver' => 'session']);

    DB::beginTransaction();
    try {
      $pelapor = new Pelapor;
      $pelapor->firstname = $firstname;
      $pelapor->email = $request->email;
      $pelapor->password = Hash::make($password);
      $pelapor->provider = 'manual';
      $pelapor->device = $request->header('User-Agent');
      $pelapor->last_login_ip = $request->getClientIp();
      $pelapor->last_login_at = Carbon::now()->toDateTimeString();
      $pelapor->save();
      Auth::guard('pelapors-api')->login($pelapor);
      $client = DB::table('oauth_clients')->where('provider', 'pelapors')->first();
      $data = [
        'grant_type' => 'password',
        'client_id' => $client->id,
        'client_secret' => $client->secret,
        'username' => $request->email,
        'password' => $request->password,
      ];
      $request_token = Request::create('/oauth/token', 'POST', $data);
      $content = json_decode(app()->handle($request_token)->getContent());
      $app_key = env('APP_KEY');
      $enc_key = base64_decode(substr($app_key, 7));
      $crypto = Crypto::decryptWithPassword($content->refresh_token, $enc_key);
      $decode = json_decode($crypto, true);
    } catch (Exception $e) {
      DB::rollback();
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    DB::commit();
    return response()->json([
      'success' => true,
      'pelapor' => $pelapor,
      'token' => [
        'token_type' => $content->token_type,
        'expires_in' => $content->expires_in,
        'access_token' => $content->access_token,
        'refresh_token' => $content->refresh_token,
        'refresh_expired' => $decode['expire_time'],
      ],
    ]);
  }

  public function LoginPelapor(Request $request)
  {
    $credentials = $request->only('email', 'password');
    $rules = [
      'email' => 'bail|required|email',
      'password' => 'required',
    ];

    $messages = [
      '*.required' => 'Tidak boleh kosong',
      '*.email' => 'Format email salah,mohon gunakan alamat email yang valid',
    ];
    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->messages(),
      ]);
    }

    config(['auth.guards.pelapors-api.driver' => 'session']);
    DB::beginTransaction();
    try {
      if (Auth::guard('pelapors-api')->attempt($credentials)) {
        $client = DB::table('oauth_clients')->where('provider', 'pelapors')->first();
        $data = [
          'grant_type' => 'password',
          'client_id' => $client->id,
          'client_secret' => $client->secret,
          'username' => $request->email,
          'password' => $request->password,
        ];
        $request_token = Request::create('/oauth/token', 'POST', $data);
        $content = json_decode(app()->handle($request_token)->getContent());
        $app_key = env('APP_KEY');
        $enc_key = base64_decode(substr($app_key, 7));
        $crypto = Crypto::decryptWithPassword($content->refresh_token, $enc_key);
        $decode = json_decode($crypto, true);
        $loggedInPelapor = Auth::guard('pelapors-api')->user();
        $pelapor = Pelapor::uuid($loggedInPelapor->uuid);
        $pelapor->device = $request->header('User-Agent');
        $pelapor->last_login_at = Carbon::now()->toDateTimeString();
        $pelapor->last_login_ip = $request->getClientIp();
        $pelapor->save();
      } else {
        return response()->json([
          'success' => false,
          'message' => 'Email atau password salah',
        ]);
      }
    } catch (Exception $e) {
      DB::rollback();
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    DB::commit();
    return response()->json([
      'success' => true,
      'token' => [
        'token_type' => $content->token_type,
        'expires_in' => $content->expires_in,
        'access_token' => $content->access_token,
        'refresh_token' => $content->refresh_token,
        'refresh_expired' => $decode['expire_time'],
      ],
    ]);
  }

  public function CreateTokenForSocialLogin($provider, Request $request)
  {
    $service_token = $request->get('service-token');
    DB::beginTransaction();
    try {
      $client = DB::table('oauth_clients')->where('provider', 'pelapors')->first();
      $data = [
        'grant_type' => 'social',
        'client_id' => $client->id,
        'client_secret' => $client->secret,
        'provider' => $provider,
        'access_token' => $service_token,
      ];
      $request_token = Request::create('/oauth/token', 'POST', $data);
      $content = json_decode(app()->handle($request_token)->getContent());
      $app_key = env('APP_KEY');
      $enc_key = base64_decode(substr($app_key, 7));
      $crypto = Crypto::decryptWithPassword($content->refresh_token, $enc_key);
      $decode = json_decode($crypto, true);

      $loggedInPelapor = Auth::guard('pelapors-api')->user();

      $pelapor = Pelapor::uuid($loggedInPelapor->uuid);
      $pelapor->device = $request->header('User-Agent');
      $pelapor->last_login_at = Carbon::now()->toDateTimeString();
      $pelapor->last_login_ip = $request->getClientIp();
      $pelapor->save();
    } catch (Exception $e) {
      DB::rollback();
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    DB::commit();
    return response()->json([
      'success' => true,
      'pelapor' => $pelapor,
      'token' => [
        'token_type' => $content->token_type,
        'expires_in' => $content->expires_in,
        'access_token' => $content->access_token,
        'refresh_token' => $content->refresh_token,
        'refresh_expired' => $decode['expire_time'],
      ],
    ]);
  }

  public function RefreshToken(Request $request)
  {
    DB::beginTransaction();
    try {
      $client = DB::table('oauth_clients')->where('provider', 'pelapors')->first();
      $data = [
        'grant_type' => 'refresh_token',
        'refresh_token' => $request->refresh_token,
        'client_id' => $client->id,
        'client_secret' => $client->secret,
      ];
      $request_token = Request::create('/oauth/token', 'POST', $data);
      $content = json_decode(app()->handle($request_token)->getContent());
      $app_key = env('APP_KEY');
      $enc_key = base64_decode(substr($app_key, 7));
      $crypto = Crypto::decryptWithPassword($request->refresh_token, $enc_key);
      $decode = json_decode($crypto, true);
      $pelapor = Pelapor::where('id', $decode['user_id'])->first();
      if (isset($content->error)) {
        return response()->json([
          'success' => false,
          'message' => $content,
        ]);
      }
      $pelapor->device = $request->header('User-Agent');
      $pelapor->last_login_at = Carbon::now()->toDateTimeString();
      $pelapor->last_login_ip = $request->getClientIp();
      $pelapor->save();
    } catch (Exception $e) {
      DB::rollback();
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    DB::commit();
    return response()->json([
      'success' => true,
      'token' => [
        'token_type' => $content->token_type,
        'expires_in' => $content->expires_in,
        'access_token' => $content->access_token,
        'refresh_token' => $content->refresh_token,
        'refresh_expired' => $decode['expire_time'],
      ],
    ]);
  }

  public function Pelapor(Request $request)
  {
    try {
      $pelapor = Helper::pelapor();
      $jumlah_laporan =  Laporan::where('created_by', $pelapor->uuid)->count();
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    return response()->json([
      'success' => true,
      'pelapor' => [
        'uuid' => $pelapor->uuid,
        'email' => $pelapor->email,
        'provider' => $pelapor->provider,
        'firstname' => $pelapor->firstname,
        'lastname' => $pelapor->lastname,
        'avatar' => $pelapor->avatar,
        'jumlah_laporan' => $jumlah_laporan,
        'reward_point' => $pelapor->reward_point,
        'last_login' => $pelapor->last_login_at,
      ],
    ]);
  }

  public function ResetPasswordOTP(Request $request)
  {
    $credentials = $request->only('email');
    $rules = [
      'email' => 'required|email',
    ];
    $validator = Validator::make($credentials, $rules);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->messages(),
      ], 400);
    }
    $pelapor = Pelapor::where('email', $request->email)->first();
    if (!$pelapor) {
      return response()->json([
        "success" => false,
        "message" => 'Email tidak terdaftar di dalam sistem, harap masukkan email yang terdaftar',
      ]);
    }
    try {
      $OTP =  Otp::generate($request->email);
      $details = [
        'email' => $pelapor->email,
        'otp' => $OTP->token,
        'expiry' => env('OTP_VALIDITY_TIME')
      ];
      $pelapor->notify(new SendOTPNotification($details));
    } catch (Exception $e) {
      return response()->json([
        "success" => false,
        "message" => $e->getMessage(),
      ]);
    }
    return response()->json([
      "success" => true,
      "message" => 'Kode untuk perubahan password sudah dikirim ke email anda',
    ]);
  }

  private function VerifyOtp($identifier, $otp)
  {
    return Otp::validate($identifier, $otp);
  }

  public function UpdateForgotPassword(Request $request)
  {
    $rules = [
      'otp' => 'required',
      'new-password' => 'required|string|min:8',
      'confirm-password' => 'required|same:new-password',
    ];
    $messages = [
      '*.required' => 'Tidak boleh kosong',
      '*.min' => 'Password minimal 8 karakter',
      '*.same' => 'Password tidak sama'
    ];
    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->messages(),
      ]);
    }
    if (strcmp($request->get('new-password'), $request->get('confirm-password')) !== 0) {
      return response()->json([
        'success' => false,
        'message' => 'Password tidak cocok dengan konfirmasi',
      ]);
    }
    DB::beginTransaction();
    try {
      $OtpModel = OtpModel::where('token', $request->otp)->first();
      if (!$OtpModel == null) {
        $pelapor = Pelapor::where('email', $OtpModel->identifier)->first();
        $verify = $this->VerifyOtp($OtpModel->identifier, $request->otp);
        if ($verify->status == true) {
          $pelapor->password = Hash::make($request->get('confirm-password'));
          $pelapor->save();
        } else {
          return response()->json([
            'success' => $verify->status,
            'message' => $verify->message,
          ]);
        }
      } else {
        return response()->json([
          'success' => false,
          'message' => 'Kode OTP tidak valid',
        ]);
      }
    } catch (Exception $e) {
      DB::rollback();

      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    DB::commit();
    return response()->json([
      "success" => true,
      "message" => 'Password berhasil diubah, silahkan login',
    ]);
  }

  public function UpdateName(Request $request)
  {
    $pelapor = Helper::pelapor();
    DB::beginTransaction();
    try {
      $update_pelapor = Pelapor::uuid($pelapor->uuid);
      if ($request->get('firstname')) {
        $update_pelapor->firstname = $request->firstname;
      }

      if ($request->get('lastname')) {
        $update_pelapor->lastname = $request->lastname;
      }
      $update_pelapor->save();
    } catch (Exception $e) {
      DB::rollback();
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    DB::commit();
    return response()->json([
      'success' => true,
      'message' => 'Nama berhasil diubah',
    ]);
  }

  public function UpdatePassword(Request $request)
  {
    $pelapor = Helper::pelapor();
    $rules = [
      'old-password' => 'required',
      'new-password' => 'required|string|min:8',
      'confirm-password' => 'required',
    ];

    $messages = [
      '*.required' => 'This field can not be empty',
      'new-password.min' => 'New Password must be at least 8 characters'
    ];
    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->messages(),
      ]);
    }
    if (!Hash::check($request->get('old-password'), $pelapor->password)) {
      return response()->json([
        'success' => false,
        'message' => 'Password lama anda tidak cocok',
      ]);
    }
    if (strcmp($request->get('old-password'), $request->get('new-password')) == 0) {
      return response()->json([
        'success' => false,
        'message' => 'Password baru anda sama dengan password lama, harap gunakan password yang berbeda',
      ]);
    }
    if (strcmp($request->get('new-password'), $request->get('confirm-password')) !== 0) {
      return response()->json([
        'success' => false,
        'message' => 'Password baru anda tidak cocok',
      ]);
    }
    DB::beginTransaction();
    try {
      $update_pelapor = Pelapor::uuid($pelapor->uuid);
      $update_pelapor->password = Hash::make($request->get('confirm-password'));
      $update_pelapor->save();
    } catch (Exception $e) {
      DB::rollback();
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    DB::commit();
    return response()->json([
      'success' => true,
      'message' => 'Password berhasil diubah',
    ]);
  }

  public function logout(Request $request)
  {
    $tokenRepository = app(TokenRepository::class);
    $refreshTokenRepository = app(RefreshTokenRepository::class);
    try {
      $pelapor = Helper::pelapor();
      if (is_null($pelapor)) {
        return response()->json([
          'success' => false,
          'message' => 'Unauthenticated',
        ]);
      }
      $accessToken = $pelapor->token();
      $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($accessToken->id);
      $tokenRepository->revokeAccessToken($accessToken->id);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    return response()->json([
      'success' => true,
      'message' => "Logged out",
    ]);
  }

  public function DeletePelapor(Request $request)
  {
    $tokenRepository = app(TokenRepository::class);
    $refreshTokenRepository = app(RefreshTokenRepository::class);

    DB::beginTransaction();
    try {
      $pelapor = Helper::pelapor();
      $accessToken = $pelapor->token();
      $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($accessToken->id);
      $tokenRepository->revokeAccessToken($accessToken->id);
      $deletePelapor = Pelapor::uuid($pelapor->uuid);
      $deletePelapor->delete();
    } catch (Exception $e) {
      DB::rollback();
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    DB::commit();
    return response()->json([
      'success' => true,
      'message' => "Account Deleted, all access revoked",
    ]);
  }
}
