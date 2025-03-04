<?php

use App\Http\Controllers\Archive\ArchiveController;
use App\Http\Controllers\Archive\LetterController;
use App\Http\Controllers\CategoryController;
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
    Route::resource('archive', ArchiveController::class);
    Route::get('/faktur', [ArchiveController::class, 'faktur_index'])->name('faktur.index');
    Route::get('/administrasi', [ArchiveController::class, 'administrasi_index'])->name('administrasi.index');
    Route::get('/laporan', [ArchiveController::class, 'laporan_index'])->name('laporan.index');
    Route::get('/letter/create/{archive_id}', [LetterController::class, 'create'])->name('letter.create');
    Route::post('/letter', [LetterController::class, 'store'])->name('letter.store');
    Route::get('/letter/{no_letter}', [LetterController::class, 'show'])->name('letter.show');
    Route::get('/letter/{archive_id}/edit', [LetterController::class, 'edit'])->name('letter.edit');
    Route::put('/letter/{archive_id}', [LetterController::class, 'update'])->name('letter.update');
    Route::delete('/letter/{id}', [LetterController::class, 'destroy'])->name('letter.destroy');
    Route::get('/letter/inventory/{no_letter}', [ArchiveController::class, 'inventory_detail'])->name('inventory.detail');
    Route::resource('profile', ProfileController::class);
    Route::resource('staff', StaffController::class);
});
