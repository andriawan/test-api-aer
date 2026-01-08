<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/qr-callback', function () {
    $request = Request::capture()->all();
    \Log::info('QR Callback Data:', $request);
});
