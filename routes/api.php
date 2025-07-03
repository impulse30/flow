<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HabitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register',[AuthController::class, 'register']);
Route::post('login',[AuthController::class, 'login']);


//Route proteger
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('habits', HabitController::class);
    Route::post('habits/{id}/complete',[HabitController::class, 'markComplete']);
});
