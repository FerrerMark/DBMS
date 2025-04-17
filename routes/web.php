<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DatabaseController;

Route::get('/', function () {
    return view('index');
});

Route::post('/connect', [DatabaseController::class, 'connect'])->name('connect');
Route::post('/execute', [DatabaseController::class, 'execute'])->name('execute');