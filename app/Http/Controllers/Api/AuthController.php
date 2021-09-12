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
use Carbon\CarbonInterval;
use Config;
use DB;
use Exception;
use Hash;
use Helper;
use Socialite;
use Validator;

class AuthController extends Controller
{

  /**
   * Register new pelapor
   *
   * @param Request $request
   * @return json
   */
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

    // retrieve password
    $password = trim($request->password);
    // retrive firstname from email username
    $extract_username = explode("@", $request->email);
    $firstname = $extract_username[0];

    /**
     * Setup auth provider instance
     * This will treat "pelapors-api" guard as session
     * To fix multiple auth guard for using passport.
     */
    config(['auth.guards.pelapors-api.driver' => 'session']);
    // begin transaction
    DB::beginTransaction();
    try {
      // saving pelapor
      $pelapor = new Pelapor;
      $pelapor->firstname = $firstname;
      $pelapor->email = $request->email;
      $pelapor->password = Hash::make($password);
      $pelapor->provider = 'manual';
      $pelapor->device = $request->header('User-Agent');
      $pelapor->last_login_ip = $request->getClientIp();
      $pelapor->last_login_at = Carbon::now()->toDateTimeString();
      $pelapor->save();
      // logging in pelapor
      Auth::guard('pelapors-api')->login($pelapor);
      // get oauth clients
      $client = DB::table('oauth_clients')->where('provider', 'pelapors')->first();
      $data = [
        'grant_type' => 'password',
        'client_id' => $client->id,
        'client_secret' => $client->secret,
        'username' => $request->email,
        'password' => $request->password,
      ];
      // requesting token
      $request_token = Request::create('/oauth/token', 'POST', $data);
      $content = json_decode(app()->handle($request_token)->getContent());
    } catch (Exception $e) {
      // begin transaction
      DB::rollback();
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    // if no error commit data saving
    DB::commit();
    // All good give 'em token
    return response()->json([
      'success' => true,
      'user' => $pelapor,
      'token' => [
        'token_type' => $content->token_type,
        'expires_in' => $content->expires_in,
        'access_token' => $content->access_token,
        'refresh_token' => $content->refresh_token,
      ]
    ]);
  }

  public function LoginPelapor(Request $request)
  {
    // Get credentials
    $credentials = $request->only('email', 'password');
    // Validation rules
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

    /**
     * Setup auth provider instance
     * This will treat "pelapors-api" guard as session
     * To fix multiple auth guard for using passport.
     */
    config(['auth.guards.pelapors-api.driver' => 'session']);
    DB::beginTransaction();
    try {
      // Check Email if exists
      if (Auth::guard('pelapors-api')->attempt($credentials)) {
        // get oauth clients
        $client = DB::table('oauth_clients')->where('provider', 'pelapors')->first();
        $data = [
          'grant_type' => 'password',
          'client_id' => $client->id,
          'client_secret' => $client->secret,
          'username' => $request->email,
          'password' => $request->password,
        ];
        // requesting token
        $request_token = Request::create('/oauth/token', 'POST', $data);
        $content = json_decode(app()->handle($request_token)->getContent());
        // get last login for tracking purpose
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
    // All good give 'em token
    return response()->json([
      'success' => true,
      'token' => [
        'token_type' => $content->token_type,
        'expires_in' => $content->expires_in,
        'access_token' => $content->access_token,
        'refresh_token' => $content->refresh_token,
      ]
    ]);
  }

  public function RefreshToken(Request $request)
  {
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
      // throw error message if content contains error
      if (isset($content->error)) {
        return response()->json([
          'success' => false,
          'message' => $content,
        ]);
      }
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    return response()->json([
      'success' => true,
      'message' => $content
    ]);
  }

  // public function CreateTokenForSocialLogin($provider, Request $request)
  // {
  //   $service_token = $request->get('service-token');
  //   $auth_pelapor = Socialite::driver($provider)->userFromToken($service_token);
  //   // split name to be first and last name
  //   $name = $auth_pelapor->user['name'];
  //   $parts = explode(" ", $name);
  //   if (count($parts) > 1) {
  //     $lastname = array_pop($parts);
  //     $firstname = implode(" ", $parts);
  //   } else {
  //     $firstname = $name;
  //     $lastname = " ";
  //   }
  //   // Check if a user exists with email response
  //   $pelapor = Pelapor::where('email', $auth_pelapor->email)->first();
  //   // if not exists create new account and return token
  //   if (!$pelapor) {
  //     // begin transaction
  //     DB::beginTransaction();
  //     try {
  //       $pelapor = Pelapor::create([
  //         'firstname' => $firstname,
  //         'lastname' => $lastname,
  //         'email'    => !empty($auth_pelapor->user['email']) ? $auth_pelapor->user['email'] : '',
  //         'provider' => $provider,
  //         'avatar' => $auth_pelapor->avatar,
  //         'last_login_at' => Carbon::now()->toDateTimeString(),
  //         'last_login_ip' => $request->getClientIp(),
  //       ]);
  //       $token = JWTAuth::fromUser($pelapor);
  //     } catch (Exception $e) {
  //       // begin transaction
  //       DB::rollback();
  //       return response()->json([
  //         'success' => false,
  //         'message' => $e->getMessage(),
  //       ]);
  //     }
  //     // if no error commit data saving
  //     DB::commit();
  //     return response()->json([
  //       'success' => true,
  //       'token' => [
  //         'access_token' => $token,
  //         'expires_in' => JWTAuth::factory()->getTTL(),
  //       ],
  //     ]);
  //   }
  //   // if exists return token
  //   else {
  //     $token = JWTAuth::fromUser($pelapor);
  //     return response()->json([
  //       'success' => true,
  //       'token' => [
  //         'access_token' => $token,
  //         'expires_in' => JWTAuth::factory()->getTTL(),
  //       ]
  //     ]);
  //   }
  // }

  /**
   * Get pelapor data
   * @param  Request $request [description]
   * @return [type]           [description]
   */
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
    // get email
    $credentials = $request->only('email');
    // Validation rules
    $rules = [
      'email' => 'required|email',
    ];
    // validation
    $validator = Validator::make($credentials, $rules);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->messages(),
      ], 400);
    }
    // select pelapor
    $pelapor = Pelapor::where('email', $request->email)->first();
    // if not found throw error
    if (!$pelapor) {
      return response()->json([
        "success" => false,
        "message" => 'Email tidak terdaftar di dalam sistem, harap masukkan email yang terdaftar',
      ]);
    }
    // try to send OTP to email
    try {
      // generate OTP
      $OTP =  Otp::generate($request->email);
      // prepare email parameter
      $details = [
        'email' => $pelapor->email,
        'otp' => $OTP->token,
        'expiry' => env('OTP_VALIDITY_TIME')
      ];
      // send email
      $pelapor->notify(new SendOTPNotification($details));
    } catch (Exception $e) {
      return response()->json([
        "success" => false,
        "message" => $e->getMessage(),
      ]);
    }
    return response()->json([
      "success" => true,
      "message" => 'Instruksi perubahan password sudah terkirim ke email terdaftar',
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

    // validation
    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validator->messages(),
      ]);
    }
    // check if new password is match with confirmation password
    if (strcmp($request->get('new-password'), $request->get('confirm-password')) !== 0) {
      return response()->json([
        'success' => false,
        'message' => 'Password tidak cocok dengan konfirmasi',
      ]);
    }
    try {
      $OtpModel = OtpModel::where('token', $request->otp)->where('expired', false)->first();
      $verify = $this->VerifyOtp($OtpModel->identifier, $request->otp);
      if ($verify->status == true) {
        $pelapor = Pelapor::where('email', $OtpModel->identifier)->first();
        $pelapor->password = Hash::make($request->get('confirm-password'));
        $pelapor->save();
      }
      return response()->json([
        "success" => $verify->status,
        "message" => $verify->message,
      ]);
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    return response()->json([
      "success" => true,
      "message" => 'Password berhasil diubah, silahkan login',
    ]);
  }

  public function UpdateName(Request $request)
  {
    $pelapor = Helper::pelapor();
    $update_pelapor = Pelapor::uuid($pelapor->uuid);
    if ($request->get('firstname')) {
      $update_pelapor->firstname = $request->firstname;
    }

    if ($request->get('lastname')) {
      $update_pelapor->lastname = $request->lastname;
    }
    try {
      $update_pelapor->save();
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
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
    // check user current password if match
    if (!Hash::check($request->get('old-password'), $pelapor->password)) {
      return response()->json([
        'success' => false,
        'message' => 'Password lama anda tidak cocok',
      ]);
    }
    // check if user using same fucking password for the new password
    if (strcmp($request->get('old-password'), $request->get('new-password')) == 0) {
      return response()->json([
        'success' => false,
        'message' => 'Password baru anda sama dengan password lama, harap gunakan password yang berbeda',
      ]);
    }
    // check if new password is match with confirmation password
    if (strcmp($request->get('new-password'), $request->get('confirm-password')) !== 0) {
      return response()->json([
        'success' => false,
        'message' => 'Password baru anda tidak cocok',
      ]);
    }
    try {
      $update_pelapor = Pelapor::uuid($pelapor->uuid);
      $update_pelapor->password = Hash::make($request->get('confirm-password'));
      $update_pelapor->save();
    } catch (Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
    return response()->json([
      'success' => true,
      'message' => 'Password berhasil diubah',
    ]);
  }

  public function Logout(Request $request)
  {
    $tokenRepository = app(TokenRepository::class);
    $refreshTokenRepository = app(RefreshTokenRepository::class);
    try {
      // Get token
      $accessToken = Helper::pelapor()->token();
      // Revoke all of the token's refresh tokens...
      $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($accessToken->id);
      // Revoke an access token...
      $tokenRepository->revokeAccessToken($accessToken->id);
    } catch (Exception $e) {
      // something went wrong whilst attempting to encode the token
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

  public function DeletePelapor()
  {
    $tokenRepository = app(TokenRepository::class);
    $refreshTokenRepository = app(RefreshTokenRepository::class);

    DB::beginTransaction();
    try {
      // get pelapor details based on auth token
      $pelapor = Helper::pelapor();
      // get access token
      $accessToken = $pelapor->token();
      // Revoke all of the token's refresh tokens...
      $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($accessToken->id);
      // Revoke an access token...
      $tokenRepository->revokeAccessToken($accessToken->id);
      $deletePelapor = Pelapor::uuid($pelapor->uuid);
      // delete pelapor
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
