<?php

namespace App\Http\Controllers;

class UserController extends Controller{


    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = app('hash')->make($request->password);
            $user->save();

            return response()->json(['message' => 'User created successfully'], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        try {

            $credentials = $request->only('email', 'password');

            if (!$token = Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = Auth::user();

            return response()->json([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
                'user' => $user
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Could not create token: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function me()
    {
        try {
            if (!auth()->user()) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json(['user' => auth()->user()]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Authentication error: ' . $e->getMessage()
            ], 401);
        }
    }

    public function logout()
    {
        try {
            Auth::logout();

            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to logout: ' . $e->getMessage()
            ], 500);
        }
    }
}
