<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReferencesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/register', [AuthController::class, 'registration'])->name('api.registration');
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->name('api.resetPassword');
Route::get('/auth/check-password-token', [AuthController::class, 'checkPassToken'])->name('api.checkPassToken');
Route::post('/auth/change-password', [AuthController::class, 'changePassword'])->name('api.changePassword');

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::get('/references', [ReferencesController::class, 'getReferencesList'])->name('api.references');
    Route::get('/references/{id}', [ReferencesController::class, 'getReferenceById'])
        ->name('api.references.detail')
        ->where(['id' => '\d+']);

    Route::post('/references/create', [ReferencesController::class, 'createReference'])->name('api.references.create');
});
