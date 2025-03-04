<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\Archive;

class PrintBarcodeArchiveController extends Controller
{
    public function show($archive_id)
    {
        $codeArray = explode('-', $archive_id);
        $archives = Archive::whereIn('archive_id', $codeArray)->get();
        
        if ($archives->isEmpty()) {
            abort(404);
        }

        return view('pages.print.barcode-archive', [
            'title' => 'Putra Panggil Jaya',
            'archives' => $archives
        ]);
    }
}
