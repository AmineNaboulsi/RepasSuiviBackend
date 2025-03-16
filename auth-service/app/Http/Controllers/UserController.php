<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller{

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = app('hash')->make($request->password);
            $user->save();

            $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

            // Setup email data
            $data = array(
                "name" => $user->name,
                "email" => $user->email,
                "verification_link" => env('APP_URL') . '/verify-email/' . $token
            );

            Mail::send("emails.welcome", $data, function($message) use ($user) {
                $message->to($user->email, $user->name)->subject("Welcome to RepasSuivi");
            });

            return response()->json(['message' => 'User created successfully'], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
}
