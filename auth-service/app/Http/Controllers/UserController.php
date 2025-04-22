<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class UserController extends Controller{

    public function me(Request $request) {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['message' => 'Token not provided'], 401);
            }
    
            $payload = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->getPayload();
            $userId = $payload->get('sub'); 
    
            $user = \App\Models\User::find($userId);

            if ($user) {
                return response()->json($user, 200);
            }
            return response()->json([
                'message' => "user not found"
            ], 404);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            $validator= Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
       
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 422);
            }
            $credentials = $request->only('email', 'password');
    
            if (! $token = auth()->attempt($credentials)) {
                return response()->json(['error' => 'Account not found'], 401);
            }
            
            return response()->json([
                'token' => $token,
                'user' => auth()->user(),
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
    

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
            $user->role = 'user';
            $user->password = app('hash')->make($request->password);
            $user->save();

            $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

            $data = array(
                "name" => $user->name,
                "email" => $user->email,
                "verification_link" => env('FRONTEND_URL')  . '/verify-email?_token=' . $token
            );

            Mail::send("emails.welcome", $data, function($message) use ($user) {
                $message->to($user->email, $user->name)->subject("Welcome to RepasSuivi");
            });

            return response()->json(
                [
                    'message' => 'User created successfully' ,
                    'token' => $token ,
                    'user' => $user,
                ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function SendVerificationLink()
    {
        try {
            $email = request('email');
            if ($email === null) {
                return response()->json(['message' => 'Email is required'], 422);
            }
            $user = User::where('email', $email)->first();
            if ($user === null) {
                return response()->json(['message' => 'User not found'], 404);
            }
            if ($user->email_verified_at !== null) {
                return response()->json(
                    [
                        'message' => 'Email already verified' ,
                        'isVerified' => true
                    ]
                    , 422);
            }
            $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

            $data = array(
                "name" => $user->name,
                "email" => $user->email,
                "verification_link" => env('FRONTEND_URL')  . '/verify-email?_token=' . $token
            );

            Mail::send("emails.welcome", $data, function($message) use ($user) {
                $message->to($user->email, $user->name)->subject("Welcome to RepasSuivi");
            });

            return response()->json(
                [
                    'message' => 'A new verification email has been sent.',
                    'isVerified' => false
                ], 200
            );

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }

    }

    public function verifyEmail(Request $request)
    {
        try {
            $token = $request->input('_token');
            $payload = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->getPayload();
            $userId = $payload->get('sub');
            $user = \App\Models\User::find($userId);
            
            return "lol";
            if ($user->email_verified_at !== null) {
                $this->alreadyVerified();
            }
            
            $user->email_verified_at = Carbon::now();
            $user->save();

             return $this->verificationSuccess();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->verificationError('expired');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->verificationError('invalid');
        } catch (\Exception $e) {
            return $this->verificationError('general');
        }
    }

    public function verificationSuccess()  {
        $message = 'Your email has been verified successfully.';
        return response()->json(
            [
                'message' =>$message
            ], 200
        );
    }
    // $table->string('image')->nullable();
    // $table->date('birthay')->nullable();
    // $table->decimal('height', 8, 2)->nullable();
    // $table->string('role');
    // $table->string('email')->unique();
    // $table->string('password');
    public function fillUserinfo(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'nullable|string',
                'height' => 'nullable|numeric',
                'birthday' => 'nullable|date',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 422);
            }
            
            $user = User::find($request->userId);
            
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            
            if ($request->has('image')) {
                $user->image = $request->image;
            }
            
            if ($request->has('height')) {
                $user->height = $request->height;
            }
            
            if ($request->has('birthday')) {
                $user->birthday = $request->birthday;
            }
            
            $user->save();
            
            return response()->json([
                'message' => 'User information updated successfully',
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function alreadyVerified()  {
        $message = 'Your email is already verified .';
        return response()->json(
            [
                'message' =>$message
            ], 200
        );
    }

    public function verificationError($error)  {
        $message ="";
        if ($error === 'expired') {
            $message = 'The verification link has expired.';
        } else if ($error === 'invalid') {
            $message = 'The verification link is invalid.';
        } else {
            $message = 'An error occurred while verifying your email.';
        }
        return response()->json(
            [
                'message' => $message
            ], 200
        );
    }
}
