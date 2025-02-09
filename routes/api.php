<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Generated\ContactPageController;
use App\Http\Controllers\Generated\HomePageController;

Route::get('/contact', [ContactPageController::class, 'loader']);
Route::post('/contact', [ContactPageController::class, 'action']);
Route::delete('/contact', [ContactPageController::class, 'action']);
Route::put('/contact', [ContactPageController::class, 'action']);
Route::patch('/contact', [ContactPageController::class, 'action']);
Route::get('/', [HomePageController::class, 'loader']);
Route::post('/', [HomePageController::class, 'action']);
Route::delete('/', [HomePageController::class, 'action']);
Route::put('/', [HomePageController::class, 'action']);
Route::patch('/', [HomePageController::class, 'action']);
