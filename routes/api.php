<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('ad/primary-key-name', [\App\Http\Controllers\AdController::class, 'getModelPrimaryKeyName']);
Route::post('ad/update/{id}', [\App\Http\Controllers\AdController::class, 'update']);
Route::get('ad/validation-rules', [\App\Http\Controllers\AdController::class, 'getValidationRules']);
Route::get('ads', [\App\Http\Controllers\AdController::class, 'get']);
