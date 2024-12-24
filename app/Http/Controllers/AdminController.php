<?php

namespace App\Http\Controllers;

use App\Models\admin;
use App\Models\User;
use App\Rules\RFCEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function register(Request $request) {
        try {
            $registrationData = $request->validate([
                'username' => ['bail','required', 'min:8', 'unique:users'],
                'email' => ['bail', 'required', 'email', 'unique:users', new RFCEmail()],
                'password' => ['bail', 'required', 'min:8'],
            ],[
                'username.unique' => 'Username already exists.',
                'email.unique' => 'Email already exists.',
            ]);
        } catch (ValidationException $e) {
            $priorityFields = ['email', 'username', 'password'];
            return $this->errorHandler->errorHierarchy($priorityFields, $e->errors());
        }

        $user = User::create($registrationData);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ]);
    }

    public function login(Request $request) {
        try {
            $logigData = $request->validate([
                'email' => 'required|email|exists:users',
                'password' => 'required|min:8'
            ]);
        } catch (ValidationException $e) {
            $priorityFields = ['email', 'password'];
            return $this->errorHandler->errorHierarchy($priorityFields, $e->errors());
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'Error' => "Invalid credentials",
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'message' => "Login successful",
            'token' => $token,
        ]);
    }

    public function logout(Request $request) {
        try {
            // Retrieve the authenticated user
            $user = $request->user();

            if ($user && $user->token()) {
                // Revoke the token used for the current request
                $user->token()->revoke();
            } else {
                return response()->json(['error' => 'User not authenticated or token not found'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
