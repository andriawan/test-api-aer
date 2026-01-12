<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::post('/qr-callback', [App\Http\Controllers\Payment::class, 'processCallbackQr']);

Route::get('/checkout', function () {
    return Inertia::render('Checkout');
});

Route::post('/process-payment', [App\Http\Controllers\Payment::class, 'processPayment']);
Route::get('/payments', [App\Http\Controllers\Payment::class, 'getAllPayments']);
Route::get('/payments/view', [App\Http\Controllers\Payment::class, 'getAllPaymentsBlade'])
    ->name('payments.index');
Route::get('/payments/view/{ref_id}', [App\Http\Controllers\Payment::class, 'viewPaymentByRefId'])
    ->name('payment.view.single');