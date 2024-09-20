<?php

namespace App\Http\Middleware;

use App\Models\Session;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the token from the Authorization header
        $token = $request->header('Authorization');

        if (!$token || !preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            return response()->json(['message' => 'Token not provided.'], 401);
        }

        $token = $matches[1];  // Extract the token

        // Validate the token (you can add your own logic here, e.g., decoding JWT or checking DB)
        $user = $this->validateToken($token);

        if (!$user) {
            return response()->json(['message' => 'Invalid token.'], 401);
        }        


        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    /**
     * Token validation logic (you can replace this with JWT or your own validation logic)
     */
    protected function validateToken($token)
    {
        // Assuming you store tokens in the User model, you can retrieve the user by token
        $session = Session::where('payload', $token)->first();

        if(!$session) return false;
        
        return User::where('id', $session->user_id)->first();
    }
}
