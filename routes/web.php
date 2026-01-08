<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/qr-callback', function () {
    $request = Request::capture()->all();
    \Log::info('QR Callback Data:', $request);
});

Route::get('/checkout', function () {
    return view('checkout');
});

Route::post('/process-payment', [App\Http\Controllers\Payment::class, 'processPayment']);
Route::get('/payments', [App\Http\Controllers\Payment::class, 'getAllPayments']);
Route::get('/payments/view', [App\Http\Controllers\Payment::class, 'getAllPaymentsBlade']);
Route::get('/payments/view/{ref_id}', [App\Http\Controllers\Payment::class, 'viewPaymentByRefId'])
    ->name('payment.view.single');