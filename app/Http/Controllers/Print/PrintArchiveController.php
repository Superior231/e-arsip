<?php

namespace App\Http\Controllers\Print;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use Illuminate\Http\Request;

class PrintArchiveController extends Controller
{
    public function show($archive_id)
    {
        $codeArray = explode('-', $archive_id);
        $archives = Archive::whereIn('archive_id', $codeArray)->with('division')->get();
        
        if ($archives->isEmpty()) {
            abort(404);
        }

        return view('pages.print.archive', [
            'title' => 'Putra Panggil Jaya',
            'archives' => $archives
        ]);
    }
}
