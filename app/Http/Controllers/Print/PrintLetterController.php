<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\Request;

class PrintLetterController extends Controller
{
    public function show($no_letter)
    {
        $codeArray = explode('-', $no_letter);
        $letters = Letter::whereIn('no_letter', $codeArray)->get();

        if ($letters->isEmpty()) {
            abort(404);
        }

        return view('pages.print.letter', [
            'title' => 'Putra Panggil Jaya',
            'letters' => $letters
        ]);
    }
}
