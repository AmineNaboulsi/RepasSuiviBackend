<?php
namespace App\Services;

use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;

    public function __construct(AuthRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $validatedData)
    {
        try {
            $user = $this->userRepository->createUser($validatedData);
            
            $token = JWTAuth::fromUser($user);
            
            $this->sendVerificationEmail($user, $token);
            
            return [
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    protected function sendVerificationEmail($user, $token)
    {
        $data = [
            "name" => $user->name,
            "email" => $user->email,
            "verification_link" => env('FRONTEND_URL') . '/verify-email?_token=' . $token
        ];

        Mail::send("emails.welcome", $data, function($message) use ($user) {
            $message->to($user->email, $user->name)->subject("Welcome to RepasSuivi");
        });
        
        return true;
    }
}
