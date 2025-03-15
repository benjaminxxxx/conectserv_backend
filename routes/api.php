<?php

use App\Http\Controllers\AuthSocialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ProfesionalController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('services', ServiceController::class);
Route::post('/verify/whatsapp', [WhatsAppController::class, 'sendWhatsappVerification']);
Route::post('/verify/check', [WhatsAppController::class, 'checkVerification']);
Route::post('/profesionales', [ProfesionalController::class, 'store']);

Route::get('/verificar-google/{google_id}', [AuthSocialController::class, 'verificarGoogle']);
Route::post('/login', [AuthSocialController::class, 'login']);


