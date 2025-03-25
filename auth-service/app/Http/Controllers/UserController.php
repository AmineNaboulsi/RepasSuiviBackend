<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class UserController extends Controller{

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
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

            return response()->json(['message' => 'User created successfully'], 201);

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

            $token = \Tymon\JWTAuth\Facades\JWTAuth::fromUser($user);

            $data = array(
                "name" => $user->name,
                "email" => $user->email,
                "verification_link" => env('FRONTEND_URL')  . '/verify-email?_token=' . $token
            );

            Mail::send("emails.welcome", $data, function($message) use ($user) {
                $message->to($user->email, $user->name)->subject("Welcome to RepasSuivi");
            });

            return response()->json(['message' => 'Verification link sent successfully'], 200);

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

            if ($user->email_verified_at !== null) {
                return redirect(env('FRONTEND_URL') . '/already-verified');
            }

            $user->email_verified_at = Carbon::now();
            $user->save();

            return redirect('/verification-success');
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return redirect('/verification-error?error=expired');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return redirect('/verification-error?error=invalid');
        } catch (\Exception $e) {
            return redirect('/verification-error?error=general');
        }
    }
    public function verificationSuccess(Request $request)  {
        $message = 'Your email has been verified successfully.';
        return view('verification.verification-success',compact('message'));
    }
    public function verificationError(Request $request)  {
        $error = $request->input('error');
        $message ="";
        if ($error === 'expired') {
            $message = 'The verification link has expired.';
        } else if ($error === 'invalid') {
            $message = 'The verification link is invalid.';
        } else {
            $message = 'An error occurred while verifying your email.';
        }
        return view('verification.verification-success',compact('message'));
    }
}
