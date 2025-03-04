<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\Letter;

class PrintBarcodeLetterController extends Controller
{
    public function show($no_letter)
    {
        $codeArray = explode('-', $no_letter);
        $letters = Letter::whereIn('no_letter', $codeArray)->get();

        if ($letters->isEmpty()) {
            abort(404);
        }

        return view('pages.print.barcode-letter', [
            'title' => 'Putra Panggil Jaya',
            'letters' => $letters
        ]);
    }
}
