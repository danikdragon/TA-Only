<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/cars/available', [\App\Http\Controllers\Api\CarAvailabilityController::class, 'index']);

Route::get('/info/cars', [\App\Http\Controllers\Api\InfoController::class, 'getAllCars']);
Route::get('/info/users', [\App\Http\Controllers\Api\InfoController::class, 'getAllUsers']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated'], 401);
})->name('login');