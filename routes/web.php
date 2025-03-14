<?php

use App\Http\Controllers\Archive\ArchiveController;
use App\Http\Controllers\Archive\LetterController;
use App\Http\Controllers\Archive\MemoController;
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
use App\Http\Controllers\ScanController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes(['register' => false]);




Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::resource('division', DivisionController::class);
    Route::resource('category', CategoryController::class);
    Route::put('/archive/delete/{id}', [ArchiveController::class, 'delete_archive'])->name('archive.delete');
    Route::resource('staff', StaffController::class);
});


Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::resource('archive', ArchiveController::class);
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/history-detail/{id}', [HistoryController::class, 'detail'])->name('history.detail');


    Route::get('/letter/create/{archive_id}', [LetterController::class, 'create'])->name('letter.create');
    Route::post('/letter', [LetterController::class, 'store'])->name('letter.store');
    Route::get('/letter/{no_letter}', [LetterController::class, 'show'])->name('letter.show');
    Route::get('/letter/{archive_id}/edit', [LetterController::class, 'edit'])->name('letter.edit');
    Route::put('/letter/{archive_id}', [LetterController::class, 'update'])->name('letter.update');
    Route::put('/letter/delete/{id}', [LetterController::class, 'delete_letter'])->name('letter.delete');
    Route::get('/letter/reply/{no_letter}', [LetterController::class, 'letter_reply'])->name('letter.reply');
    Route::post('/letter/approve', [LetterController::class, 'approve_letter'])->name('letter.approve');
    Route::get('/letter/inventory/{no_letter}', [ArchiveController::class, 'inventory_detail'])->name('inventory.detail');
    
    Route::get('/letter', [LetterController::class, 'letter_index'])->name('letter.index');
    Route::get('/surat-masuk', [LetterController::class, 'letterIn_index'])->name('letterIn.index');
    Route::get('/surat-keluar', [LetterController::class, 'letterOut_index'])->name('letterOut.index');
    Route::get('/surat-pending', [LetterController::class, 'letter_pending_index'])->name('letter.pending');
    Route::get('/faktur', [ArchiveController::class, 'faktur_index'])->name('faktur.index');

    Route::get('/memo', [MemoController::class, 'index'])->name('memo.index');
    Route::get('/memo/create/{archive_id}', [MemoController::class, 'create'])->name('memo.create');
    Route::post('/memo', [MemoController::class, 'store'])->name('memo.store');
    Route::get('/memo/{no_letter}', [MemoController::class, 'show'])->name('memo.show');
    Route::get('/memo/{archive_id}/edit', [MemoController::class, 'edit'])->name('memo.edit');
    Route::put('/memo/{archive_id}', [MemoController::class, 'update'])->name('memo.update');
    Route::put('/memo/delete/{id}', [MemoController::class, 'delete_memo'])->name('memo.delete');

    Route::post('/document', [DocumentController::class, 'store'])->name('document.store');
    Route::put('/document/{id}', [DocumentController::class, 'update'])->name('document.update');
    Route::put('/document/delete/{id}', [DocumentController::class, 'delete_document'])->name('document.delete');

    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::resource('profile', ProfileController::class);
    Route::delete('/profile/{id}/delete-avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.delete.avatar');

    Route::get('/print/letter/{no_letter}', [PrintLetterController::class, 'show'])->name('print.letter');
});
