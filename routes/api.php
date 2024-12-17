<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Authentication
Route::post('/register',[AuthenticationController::class , 'register']);
Route::post('/login',[AuthenticationController::class , 'login']);
Route::post('/logout',[AuthenticationController::class , 'logout'])->middleware('auth:api');

//Email Verification
Route::get('/email/verify/{id}/{hash}', [AuthenticationController::class, 'verify'])->name('verification.verify');
