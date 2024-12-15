<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
//Test
Route::get('/php-version', function () {
    return phpversion();
});
