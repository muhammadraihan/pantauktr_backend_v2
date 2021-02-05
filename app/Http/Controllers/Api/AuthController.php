<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pelapor;
use Carbon\Carbon;

use Auth;
use Config;
use DB;
use Hash;
use Helper;
use JWTAuth;
use JWTFactory;
use Tymon\JWTAuth\Exceptions\JWTException;
use Socialite;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Validator;

class AuthController extends Controller
{
  /**
   * setup auth provider instance
   * for default we use default larave User Table
   * change table if you need custom auth
   *
   * @return  void
   */
  public function __construct()
  {
    Config::set('jwt.user', Pelapor::class);
    Config::set('auth.providers', [
      'users' => [
        'driver' => 'eloquent',
        'model' => Pelapor::class,
      ]
    ]);
  }

  // protected function CreateNewToken($token, $type){
  //   return response()->json([
  //     'access_token' => $token,
  //     'expires_in' => JWTAuth::factory()->getTTL() * env('JWT_EXPIRES_MINUTES', 60),
  //   ],200);
  // }

  /**
   * Check token if still valid or expired or not not found
   * @return json
   */
  public function CheckToken()
  {
    if (!JWTAuth::parseToken()->authenticate()) {
      return response()->json(['status' => 'ACCOUNT_NOT_FOUND'], 404);
    } else {
      return response()->json(
        ['status' => 'TOKEN_OK']
      );
    }
  }

  public function RegisterPelapor(Request $request)
  {
    $rules = [
      'email' => 'required|email|unique:pelapors',
      'password' => 'required|min:8',
    ];

    $messages = [
      '*.required' => 'Tidak boleh kosong',
      '*.unique' => 'Email sudah digunakan,harap gunakan alamat email yang lain',
      '*.min' => 'Password minimal 8 karakter',
    ];
    $validator = Validator::make($request->all(), $rules,$messages);

    if($validator->fails()) {
        return response()->json([
            'message' => $validator->messages()
        ],400);
    }
    
    // retrieve password
    $password = trim($request->password);
    // saving pelapor
    $pelapor = Pelapor::create([
      'email' => $request->email,
      'password' => Hash::make($password),
      'provider' => 'manual'
    ]);
    $token = JWTAuth::fromUser($pelapor);

    return response()->json([
      'pelapor' => [
        'uuid' => $pelapor->uuid,
        'email' => $pelapor->email,
        'provider' => $pelapor->provider,
      ],
      'token' => [
        'access_token' => $token,
        'expires_in' => JWTAuth::factory()->getTTL().' minutes',
      ]
    ]);
  }

  public function LoginPelapor(Request $request)
  {
    // Get credentials
    $credentials = $request->only('email', 'password');
    // Validation rules
    $rules = [
      'email' => 'required',
      'password' => 'required',
    ];
    // validation
    $validator = Validator::make($credentials, $rules);
    if ($validator->fails()) {
      return response()->json([
        'message' => $validator->messages(),
      ], 400);
    }
    // attempt login process
    try {
      // Check Email if exists
      if (!$token = JWTAuth::attempt($credentials)) {
        return response()->json([
          'message' => 'Email not registered',
        ], 400);
      }
    } catch (JWTException $e) {
      // Check if Email or Password match
      return response()->json([
        'message' => $e,
      ], 500);
    }

    // get last login for tracking purpose
    $loggedInPelapor = Auth::user();
    $pelapor = Pelapor::find($loggedInPelapor->id);
    $pelapor->last_login_at = Carbon::now()->toDateTimeString();
    $pelapor->last_login_ip = $request->getClientIp();
    $pelapor->save();

    // All good give 'em token
    return response()->json([
      'access_token' => $token,
      'expires_in' => JWTAuth::factory()->getTTL() * 60,
      ]);
  }

  public function Logout(Request $request)
  {
    // Get JWT Token from the request header key "Authorization"
    $token = $request->header('Authorization');
    // Invalidate the token
    try {
      JWTAuth::invalidate($token);
      return response()->json([
        'message' => "Logged out.",
      ], 200);
    } catch (JWTException $e) {
      // something went wrong whilst attempting to encode the token
      return response()->json([
        'message' => $e,
      ], 500);
    }
  }

  public function redirect($provider)
  {
    return Socialite::driver($provider)->stateless()->redirect();
  }

  public function getToken($provider, Request $request)
  {
    $access_token = $request->get('access_token');
    // $auth_pelapor = Socialite::driver($provider)->stateless()->user();
    $auth_pelapor = Socialite::driver($provider)->userFromToken($access_token);

    // split name to be first and last name
    $name = $auth_pelapor->user['name'];
    $parts = explode(" ", $name);
    if (count($parts) > 1) {
      $lastname = array_pop($parts);
      $firstname = implode(" ", $parts);
    } else {
      $firstname = $name;
      $lastname = " ";
    }
    // Check if a user exists with email response
    $pelapor = Pelapor::where('email', $auth_pelapor->email)->first();
    // if not exists create new account and return token
    if (!$pelapor) {
      $pelapor = Pelapor::create([
        'firstname' => $firstname,
        'lastname' => $lastname,
        'email'    => !empty($auth_pelapor->user['email']) ? $auth_pelapor->user['email'] : '',
        'provider' => $provider,
        'provider_id' => $auth_pelapor->user['id'],
        'avatar' => $auth_pelapor->avatar,
        'last_login_at' => Carbon::now()->toDateTimeString(),
        'last_login_ip' => $request->getClientIp(),
      ]);
      $app_token = JWTAuth::fromUser($pelapor);
      return response()->json([
        'success' => true,
        'data' => [
          'app_token' => $app_token,
        ]
      ], 200);
    }
    // if exists return token
    else {
      $app_token = JWTAuth::fromUser($pelapor);
      return response()->json([
        'success' => true,
        'data' => [
          'app_token' => $app_token,
        ]
      ], 200);
    }
  }

  /**
   * Get pelapor data
   * @param  Request $request [description]
   * @return [type]           [description]
   */
  public function pelapor(Request $request)
  {
    // Get JWT Token from the request header key "Authorization"
    $token = $request->header('Authorization');
    $payload = JWTAuth::getPayload($token)->toArray();
    $pelapor = Helper::pelapor();
    return response()->json([
      'success' => true,
      'data' => [
        'uuid' => $pelapor->uuid,
        'firstname' => $pelapor->firstname,
        'lastname' => $pelapor->lastname,
        'email' => $pelapor->email,
        'avatar' => $pelapor->avatar,
        'last_login_at' => $pelapor->last_login_at,
        'last_login_ip' => $pelapor->last_login_ip,
      ]
    ], 200);
  }

}
