<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Generated\HomeLoaderController;
use App\Http\Controllers\Generated\HomeActionController;
use App\Http\Controllers\Generated\TestLoaderController;
use App\Http\Controllers\Generated\TestActionController;

Route::get('/', [HomeLoaderController::class, 'index']);
Route::post('/', [HomeActionController::class, 'index']);
Route::delete('/', [HomeActionController::class, 'index']);
Route::put('/', [HomeActionController::class, 'index']);
Route::patch('/', [HomeActionController::class, 'index']);
Route::get('/test', [TestLoaderController::class, 'index']);
Route::post('/test', [TestActionController::class, 'index']);
Route::delete('/test', [TestActionController::class, 'index']);
Route::put('/test', [TestActionController::class, 'index']);
Route::patch('/test', [TestActionController::class, 'index']);
