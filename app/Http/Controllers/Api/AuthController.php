<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\User;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login (Request $request){
      $credentials = $request->only(['student_number', 'password']);
      if (!$token = auth()->attempt($credentials)){
        return response()->json([
          'success' => false,
          'message' => 'invalid credentials'
        ]);
      }

      return response()->json([
        'success' => true,
        'token' => $token,
        'user' => Auth::user()
      ]);
    }

    public function register (Request $request){
      $encrypted_pass = Hash::make($request->password);
      $user = new User;
      try {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $encrypted_pass;
        $user->student_number = $request->student_number;
        $user->save();
        return $this->login($request);
      }
      catch (Exception $e){
        return response()->json([
          'success' => false,
          'message' => ''. $e
        ]);
      }
    }

    public function logout(Request $request){
      try {
        JWTAuth::invalidate(JWTAuth::parseToken($request->token));
        return response()->json([
          'success' => true,
          'message' => 'logout success'
        ]);
      }
      catch (Exception $e) {
        return response()->json([
          'success' => false,
          'message' => ''.$e
        ]);
      }
    }

}
