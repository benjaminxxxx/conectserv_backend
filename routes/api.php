<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WhatsAppController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('services', ServiceController::class);
Route::post('/verify/{phoneNumber}', [WhatsAppController::class, 'sendVerification']);
Route::post('/verify/check', [WhatsAppController::class, 'checkVerification']);
