<?php

use App\Http\Controllers\Api\LayerController;
use Illuminate\Support\Facades\Route;

Route::get('/layers', [LayerController::class, 'index']);
Route::get('/layers/{id}', [LayerController::class, 'show']);
