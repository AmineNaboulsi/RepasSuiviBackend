<?php

namespace App\Http\Controllers;

class UserController extends Controller{
    

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = collect($this->users)->firstWhere('id', $id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email'
        ]);
        
        // In a real app, you would save to the database
        $newUser = [
            'id' => count($this->users) + 1,
            'name' => $request->input('name'),
            'email' => $request->input('email')
        ];
        
        return response()->json($newUser, 201);
    }

    public function update(Request $request, $id)
    {
        $user = collect($this->users)->firstWhere('id', $id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        // In a real app, you would update the database
        $updatedUser = [
            'id' => $id,
            'name' => $request->input('name', $user['name']),
            'email' => $request->input('email', $user['email'])
        ];
        
        return response()->json($updatedUser);
    }

    public function destroy($id)
    {
        $user = collect($this->users)->firstWhere('id', $id);
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        // In a real app, you would delete from the database
        
        return response()->json(['message' => 'User deleted']);
    }
}