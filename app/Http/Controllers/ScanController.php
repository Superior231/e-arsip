<?php

namespace App\Http\Controllers;

use App\Models\History;

class ScanController extends Controller
{
    public function index()
    {
        $histories = History::latest()->get();

        return view('pages.scan', [
            'title' => 'Scan - Putra Panggil Jaya',
            'navTitle' => 'Scan',
            'active' => 'scan',
            'histories' => $histories
        ]);
    }
}
