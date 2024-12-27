<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Authentication
Route::post('/register',[AuthenticationController::class , 'register']);
Route::post('/login',[AuthenticationController::class , 'login']);
Route::post('/logout',[AuthenticationController::class , 'logout'])->middleware('auth:api');

//Email Verification
Route::get('/email/verify/{id}/{hash}', [AuthenticationController::class, 'verify'])->name('verification.verify');

//Forgot Password
Route::post('/forgot',[PasswordResetController::class,'forgot']);
Route::post('/reset',[PasswordResetController::class,'reset']);
Route::post('/check-otp',[PasswordResetController::class,'checkOTP']);

//Change Password
//Route::put('/ChangePass', [ChangePassword::class, 'ChangePass'])->middleware('auth:api');

Route::get('/reset-password/{token}', )->middleware('guest')->name('password.reset');
