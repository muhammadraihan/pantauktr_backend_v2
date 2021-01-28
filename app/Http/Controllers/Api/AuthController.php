<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Carbon\Carbon;

use Auth;
use Config;
use DB;
use Exception;
use Hash;
use Helper;
use JWTAuth;
use JWTFactory;
use Socialite;
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
        Config::set('jwt.user', User::class);
        Config::set('auth.providers', [
            'users' => [
                'driver' => 'eloquent',
                'model' => User::class,
            ]]);
    }

    public function Login(Request $request)
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
                'success' => false,
                'message' => $validator->messages(),
            ]);
        }
        // attempt login process
        try {
            // Check Email if exists
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email not registered',
                ], 401);
            }
        } catch (JWTException $e) {
            // Check if Email or Password match
            return response()->json([
                'success' => false,
                'message' => 'Login failed, please try again',
            ], 500);
        }

        // get last login for tracking purpose
        $loggedInUser = Auth::user();
        $user = User::find($loggedInUser->id);
        $user->last_login_at = Carbon::now()->toDateTimeString();
        $user->last_login_ip = $request->getClientIp();
        $user->save();

        // All good give 'em token
        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
            ]]);
    }

    public function Logout(Request $request)
    {
        // Get JWT Token from the request header key "Authorization"
        $token = $request->header('Authorization');
        // Invalidate the token
        try {
            JWTAuth::invalidate($token);
            return response()->json([
                'success' => true,
                'message' => "Logged out.",
            ], 200);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json([
                'success' => false,
                'message' => 'Failed to logout, please try again.',
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
    if(count($parts) > 1) {
      $lastname = array_pop($parts);
      $firstname = implode(" ", $parts);
    }
    else{
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
        'email'    => !empty($auth_pelapor->user['email'])? $auth_pelapor->user['email'] : '' ,
        'provider' => $provider,
        'provider_id' => $auth_pelapor->user['id'],
        'avatar' => $auth_pelapor->avatar,
        'last_login_at' => Carbon::now()->toDateTimeString(),
        'last_login_ip' => $request->getClientIp(),
      ]);
      $app_token = JWTAuth::fromUser($pelapor);
      return response()->json([
        'success' => true,
        'data'=> [
          'app_token' => $app_token,
        ]
      ],200);
    }
    // if exists return token
    else {
      $app_token = JWTAuth::fromUser($pelapor);
      return response()->json([
        'success' => true,
        'data'=> [
          'app_token' => $app_token,
        ]
      ],200);
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
        'data'=> [
          'uuid' => $pelapor->uuid,
          'firstname' => $pelapor->firstname,
          'lastname' => $pelapor->lastname,
          'email' => $pelapor->email,
          'avatar' => $pelapor->avatar,
          'last_login_at' => $pelapor->last_login_at,
          'last_login_ip' => $pelapor->last_login_ip,
          ]
      ],200);
  }

  /**
   * Check token if still valid or expired or not not found
   * @return json
   */
  public function checkToken()
  {
    if (!JWTAuth::parseToken()->authenticate()) {
      return response()->json(['status' => 'ACCOUNT_NOT_FOUND'], 404);
    }
    else{
      return response()->json(
        ['status' => 'TOKEN_OK']
      );
    }
  }
}
