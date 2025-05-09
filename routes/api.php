<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthSocialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\SolicitudController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('services', ServiceController::class);
Route::post('/verify/whatsapp', [WhatsAppController::class, 'sendWhatsappVerification']);
Route::post('/verify/check', [WhatsAppController::class, 'checkVerification']);
Route::post('/profesionales', [ProfesionalController::class, 'store']);

Route::get('/verificar-google/{google_id}', [AuthSocialController::class, 'verificarGoogle']);
Route::post('/login', [AuthSocialController::class, 'login']);

Route::get('/profesionales/{id}/verificar-imagenes', [ProfesionalController::class, 'verificarImagenes']);
//Route::post('/profesional/subir-documentos', [ProfesionalController::class, 'uploadDocs']);
Route::get('/profesional/{id}/obtener-documentos', [ProfesionalController::class, 'getUploadedDocs']);
Route::post('/profesional/delete-document', [ProfesionalController::class, 'deleteDocument']);


Route::post('/solicitudes', [SolicitudController::class, 'store']);


Route::prefix('admin')->middleware('jwt.auth')->group(function () {
    Route::get('/dashboard/stats', [AdminDashboardController::class, 'getDashboardStats']);
    Route::get('profesionales/listar/{page}/{profesion?}', [\App\Http\Controllers\Admin\ProfesionalController::class, 'listar']);
    Route::get('profesionales/aprobar/{uuid}', [\App\Http\Controllers\Admin\ProfesionalController::class, 'aprobar']);
    Route::get('profesionales/eliminar/{uuid}', [\App\Http\Controllers\Admin\ProfesionalController::class, 'eliminar']);

    Route::post('/profesional/subir-documentos', [ProfesionalController::class, 'subirDocumentoProfesional']);
    Route::post('/profesional/estado-documento', [ProfesionalController::class, 'obtenerEstadoDocumento']);
});


