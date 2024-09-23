<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Visus\Cuid2\Cuid2;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'contact' => ['required', 'exists:users,contact'],
            'password' => ['required'],
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        $id = new Cuid2();


        $user = User::where('contact', $request->contact)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['errors' => 'Invalid credentials'], 401);
        }
        
        // $token = $user->createToken('auth_token')->plainTextToken;         
        $token = Session::create([
                                    'id' => $id->toString(),
                                    'user_id' => $user->id,
                                    'payload' => Str::random(60),
                                    'last_activity' => strtotime(date("Y-m-d h:i:sa"))
                                ]);         

        return response()->json(['token' => $token->payload,'user'=> $user]);
    }

    public function logout(Request $request)
    {
        $session = Session::where('user_id', $request->user()->id)->first();
        $session->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

}