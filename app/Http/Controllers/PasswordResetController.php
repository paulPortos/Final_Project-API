<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Custom_Libraries\NameFormater;
use App\Http\Controllers\Custom_Libraries\NumberGenerator;
use App\Models\ForgotPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use App\Mail\ForgotPasswordMail;
use Nette\Schema\ValidationException;
use Random\RandomException;

class PasswordResetController extends Controller
{
    protected NumberGenerator $numberGenerator;
    public function __construct() {
        $this->numberGenerator = new NumberGenerator();
    }

    /**
     * @throws RandomException
     */
    public function forgot(Request $request)
    {
        // Validate the incoming request to ensure email is present and valid
        try {
            $validatedData = $request->validate([
                'email' => 'required|email|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()]);
        }

        // Find the user by email
        $user = User::where('email', $validatedData['email'])->first();

        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'No record found, try a different email'], 404);
        }

        //Generate OTP
        $resetPasswordToken = $this->numberGenerator->generateNumber();

        // Check if the user already requested a password reset
        $userPassReset = ForgotPassword::where('email', $user->email)->first();

        //Create new
        if (!$userPassReset) {
            // Create a new reset token`
            ForgotPassword::create([
                'email' => $user->email,
                'token' => $resetPasswordToken,
            ]);
        } else {
            // Update the existing token
            $userPassReset->update([
                'token' => $resetPasswordToken,
            ]);
        }

        Mail::to($user->email)->send(new ForgotPasswordMail($resetPasswordToken));

        // Return the response with a success message and token (for testing purposes)
        return response()->json([
            'message' => 'Otp send to your email',
            'OTP' => $resetPasswordToken
        ], 200);
    }

    public function reset(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => 'required',
            'password' => 'required'
        ],
        [
            'email.exists' => 'This email does not exist',
        ]);

        // Find the user by email
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'No Record Found, Try another email']);
        }

        // Find the reset request by email
        $resetRequest = ForgotPassword::where('email', $user->email)->first();

        // Check if the reset request exists and if the token matches
        if (!$resetRequest || $resetRequest->token != $request->token) {
            return response()->json(['message' => 'Wrong OTP, Please try again']);
        }

        // Check if the new password is different from the old one
        if (Hash::check($validatedData['password'], $user->password)) {
            return response()->json(['message' => 'New password cannot be the same as the old password'], 400);
        }
        // Update the user's password
        $user->fill([
            'password' => Hash::make($validatedData['password']),
        ]);
        $user->save();

        // Delete used token from the database (Forgot_pass table)
        $resetRequest->delete();

        // Delete all existing tokens for the user (logout from other sessions)
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Password reset successful',
        ]);
    }

    public function checkOTP(Request $request) {
        $validatedData = $request->validate([
            'otp' => 'required|string'
        ]);

        $otp = ForgotPassword::where('token', $validatedData['otp'])->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'OTP matched'
            ]);
        }
    }

}
