<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes(['register' => false]);


Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');
});
