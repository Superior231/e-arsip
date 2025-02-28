<?php

use App\Http\Controllers\Archive\ArchiveController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Category\SubCategoryController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes(['register' => false]);


Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::delete('/profile/{id}/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete.avatar');
    Route::resource('division', DivisionController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('subcategory', SubCategoryController::class);
    Route::resource('archive', ArchiveController::class);
    Route::resource('profile', ProfileController::class);
    Route::resource('staff', StaffController::class);
});
