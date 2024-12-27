<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Custom_Libraries\ErrorHandlerAuth;
use App\Http\Controllers\Custom_Libraries\NameFormater;
use App\Models\User;
use App\Rules\NameWithoutNumbers;
use App\Rules\RFCEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    protected ErrorHandlerAuth $errorHandler;
    protected NameFormater $nameFormater;

    // Initialize ErrorHandlerAuth in the constructor
    public function __construct()
    {
        $this->errorHandler = new ErrorHandlerAuth();
        $this->nameFormater = new NameFormater();
    }

    public function register(Request $request) {
        try {
            $registrationData = $request->validate([
                'first_name' => ['required', 'string', 'max:255', new NameWithoutNumbers()],
                'last_name' => ['required', 'string', 'max:255', new NameWithoutNumbers()],
                'username' => ['bail','required', 'min:8', 'unique:users'],
                'email' => ['bail', 'required', 'email', 'unique:users', new RFCEmail()],
                'age' => ['bail', 'required', 'integer', 'min:18', 'max:70'],
                'password' => ['bail', 'required', 'min:8'],
            ],[
                'age.required' => 'Please enter age',
                'age.min' => 'Must be at least 18 years old',
                'username.unique' => 'Username already exists.',
                'email.unique' => 'Email already exists.',
            ]);
        } catch (ValidationException $e) {
            $priorityFields = ['email', 'username', 'password, first_name', 'last_name', 'age'];
            return $this->errorHandler->errorHierarchy($priorityFields, $e->errors());
        }
        $registrationData['first_name'] = $this->nameFormater->capitalizeName($registrationData['first_name']);
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
                'email' => 'required|email|exists:users,email',
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
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'username' => $user->username,
            'email' => $user->email,
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


    public function verify($id, $hash) {
        try {
            $user = User::findOrFail($id);

            if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
                return response()->json(['message' => 'Invalid verification link.'], 403);
            }

            if ($user->hasVerifiedEmail()) {
                return view('emails/email-exist');
            }

            $user->markEmailAsVerified();

            return view('emails/email_verified');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occured: ' . $e->getMessage()
            ],422);
        }
    }
}
