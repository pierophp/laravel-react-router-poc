<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Generated\HomePageController;
use App\Http\Controllers\Generated\TestPageController;

Route::get('/', [HomePageController::class, 'loader']);
Route::post('/', [HomePageController::class, 'action']);
Route::delete('/', [HomePageController::class, 'action']);
Route::put('/', [HomePageController::class, 'action']);
Route::patch('/', [HomePageController::class, 'action']);
Route::get('/test', [TestPageController::class, 'loader']);
Route::post('/test', [TestPageController::class, 'action']);
Route::delete('/test', [TestPageController::class, 'action']);
Route::put('/test', [TestPageController::class, 'action']);
Route::patch('/test', [TestPageController::class, 'action']);
