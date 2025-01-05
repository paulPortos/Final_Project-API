<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Custom_Libraries\ErrorHandlerAuth;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Nette\Schema\ValidationException;

class AdminAuthenticationController extends Controller
{
    protected ErrorHandlerAuth $errorHandler;
    public function __construct() {
        $this->errorHandler = new ErrorHandlerAuth();
    }
    public function register(Request $request) {
        try {
            $registrationData = $request->validate([
                'username' => ['required', 'max:255'],
                'password' => ['required', 'min:8'],
            ],
                [
                    'username.required' => 'Username is required',
                    'password.min' => 'Password should be at least 8 characters'
                ]
            );
        } catch (ValidationException $e) {
            $priorityFields = ['username', 'password'];
            return $this->errorHandler->errorHierarchy($priorityFields, $e->errors());
        }

        $admin = Admin::create($registrationData);

        return response()->json(['message' => 'Admin registration successful'], 201);
    }

    public function login(Request $request) {
        try {
            $loginData = $request->validate([
                'username' => ['required', 'max:255'],
                'password' => ['required', 'min:8'],
            ],
                [
                    'username.required' => 'Username is required',
                    'password.required' => 'Password is required',
                    'password.min' => 'Password should be at least 8 characters'
                ]);
        } catch (ValidationException $e) {
            $priorityFields = ['username', 'password'];
            return $this->errorHandler->errorHierarchy($priorityFields, $e->errors());
        }

        // Attempt to find the admin by username
        $admin = Admin::where('username', $loginData['username'])->first();

        if ($admin && Hash::check($loginData['password'], $admin->password)) {
            // Generate a token for the authenticated admin
            $token = $admin->createToken('AdminAccessToken')->accessToken;

            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);

    }
    public function logout(Request $request) {
        $user = $request->user();
        $user->token()->revoke();
    }
}
