<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Responses\ServiceResponse;
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

// Route::middleware('auth')->group(function () {
//     Route::get('/appointments/load', [AppointmentController::class, 'load'])->name('appointments.load');
//     Route::get('/appointments/load-all', [AppointmentController::class, 'loadAll'])->name('appointments.load-all');
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('auth:api')->group(function () {
//     Route::apiResource('doctors', 'API\DoctorController');
// });

Route::get('/success', [(ServiceResponse::class), 'successResponse']);
Route::get('/error', [(ServiceResponse::class), 'errorResponse']);
Route::get('/array', [(ServiceResponse::class), 'arrayData']);
Route::get('/data', [(ServiceResponse::class), 'dataResponse']);
