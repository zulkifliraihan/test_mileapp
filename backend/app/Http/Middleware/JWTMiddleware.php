<?php

namespace App\Http\Middleware;

use App\Traits\ReturnResponser;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    use ReturnResponser;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * 
     */
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('authorization');

        if (!$authorization) {
            return $this->errorAuthentication(["Token is required!"]);
        }

        try {
            $token = str_replace('Bearer ', '', $authorization);
            $user = JWTAuth::parseToken()->authenticate($token);

        } catch (TokenExpiredException $e) {
            return $this->errorAuthentication(["Token has expired!"]);

        } catch (TokenInvalidException $e) {
            return $this->errorAuthentication(["Token is invalid!"]);

        }

        if (!auth()->user()) {
            return $this->errorAuthentication(["Token has expired!"]);
        }
        
        if ($user->id !== auth()->user()->id) {
            return $this->errorAuthentication(["Token is invalid!"]);
        }

        session(['user_id' => $user->id]);


        return $next($request);
    }
}