<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    try {
      $pelapor = JWTAuth::parseToken()->authenticate();
    } catch (Exception $e) {
      if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return response()->json(['success' => false, 'message' => 'TOKEN_IS_INVALID'], 401);
      } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return response()->json(['success' => false, 'message' => 'TOKEN_IS_EXPIRED'], 401);
      } else {
        return response()->json(['success' => false, 'message' => 'TOKEN_NOT_FOUND'], 401);
      }
    }
    return $next($request);
  }
}
