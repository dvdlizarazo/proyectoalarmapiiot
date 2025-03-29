<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MotionSensorController;
use App\Http\Controllers\EntradaSalidaController;
use App\Http\Controllers\AuthController;

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


Route::get('motion-sensors', [MotionSensorController::class, 'index']);
Route::post('motion-sensors', [MotionSensorController::class, 'store']);
Route::get('motion-sensors/{id}', [MotionSensorController::class, 'show']);
Route::put('motion-sensors/{id}', [MotionSensorController::class, 'update']);
Route::delete('motion-sensors/{id}', [MotionSensorController::class, 'destroy']);


Route::post('/entrada-salida', [EntradaSalidaController::class, 'registrar']);


Route::post('login', [AuthController::class, 'login']);
