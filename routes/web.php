<?php

use App\Http\Controllers\Archive\ArchiveController;
use App\Http\Controllers\Archive\LetterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\Print\PrintArchiveController;
use App\Http\Controllers\Print\PrintBarcodeArchiveController;
use App\Http\Controllers\Print\PrintBarcodeLetterController;
use App\Http\Controllers\Print\PrintLetterController;
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
    Route::get('/archive-pending', [ArchiveController::class, 'pending_index'])->name('archive.pending');
    Route::put('/archive/delete/{id}', [ArchiveController::class, 'delete_archive'])->name('archive.delete');

    Route::get('/letter/create/{archive_id}', [LetterController::class, 'create'])->name('letter.create');
    Route::post('/letter', [LetterController::class, 'store'])->name('letter.store');
    Route::get('/letter/{no_letter}', [LetterController::class, 'show'])->name('letter.show');
    Route::get('/letter/{archive_id}/edit', [LetterController::class, 'edit'])->name('letter.edit');
    Route::put('/letter/{archive_id}', [LetterController::class, 'update'])->name('letter.update');
    Route::put('/letter/delete/{id}', [LetterController::class, 'delete_letter'])->name('letter.delete');
    Route::get('/letter/inventory/{no_letter}', [ArchiveController::class, 'inventory_detail'])->name('inventory.detail');

    Route::get('/faktur', [ArchiveController::class, 'faktur_index'])->name('faktur.index');
    Route::get('/administrasi', [ArchiveController::class, 'administrasi_index'])->name('administrasi.index');
    Route::get('/laporan', [ArchiveController::class, 'laporan_index'])->name('laporan.index');
    Route::resource('profile', ProfileController::class);
    Route::resource('staff', StaffController::class);

    Route::post('/document', [DocumentController::class, 'store'])->name('document.store');
    Route::put('/document/{id}', [DocumentController::class, 'update'])->name('document.update');
    Route::put('/document/delete/{id}', [DocumentController::class, 'delete_document'])->name('document.delete');


    Route::get('/print/archive/{archive_id}', [PrintArchiveController::class, 'show'])->name('print.archive');
    Route::get('/print/letter/{no_letter}', [PrintLetterController::class, 'show'])->name('print.letter');
    Route::get('/print/barcode/archive/{archive_id}', [PrintBarcodeArchiveController::class, 'show'])->name('print.barcode.archive');
    Route::get('/print/barcode/letter/{no_letter}', [PrintBarcodeLetterController::class, 'show'])->name('print.barcode.letter');
    Route::get('/letter/content/{no_letter}', [LetterController::class, 'letter_content'])->name('letter.content');
});
