<?php

use App\Http\Controllers\AdminAuthenticationController;
use App\Http\Controllers\UserAuthenticationController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Authentication
Route::post('/register',[UserAuthenticationController::class , 'register']);
Route::post('/login',[UserAuthenticationController::class , 'login']);
Route::post('/logout',[UserAuthenticationController::class , 'logout'])->middleware('auth:api');

//Email Verification
Route::get('/email/verify/{id}/{hash}', [UserAuthenticationController::class, 'verify'])->name('verification.verify');

//Forgot Password
Route::post('/forgot',[PasswordResetController::class,'forgot']);
Route::post('/reset',[PasswordResetController::class,'reset']);
Route::post('/check-otp',[PasswordResetController::class,'checkOTP']);

//Change Password
//Route::put('/ChangePass', [ChangePassword::class, 'ChangePass'])->middleware('auth:api');

Route::get('/reset-password/{token}', )->middleware('guest')->name('password.reset');

Route::post('/admin/register',[AdminAuthenticationController::class , 'register']);
Route::post('/admin/login',[AdminAuthenticationController::class , 'login']);
Route::post('/admin/logout',[AdminAuthenticationController::class , 'logout'])->middleware('auth:admin');
