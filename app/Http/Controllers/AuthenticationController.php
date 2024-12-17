<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Authentication\ErrorHandlerAuth;
use App\Models\User;
use App\Rules\RFCEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    protected ErrorHandlerAuth $errorHandler;

    // Initialize ErrorHandlerAuth in the constructor
    public function __construct()
    {
        $this->errorHandler = new ErrorHandlerAuth();
    }

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

        $user->sendEmailVerificationNotification();

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

        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'message' => "Email is not yet verified"
            ],422);
        }

        $token = $user->createToken('authToken')->accessToken;

        return response()->json([
            'message' => "Login successful",
            'token' => $token,
        ]);
    }

    public function logout(Request $request) {

    }
}
