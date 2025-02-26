<?php

namespace App\Http\Controllers;

use App\Models\History;

class PengaturanController extends Controller
{
    public function index()
    {
        $histories = History::latest()->get();
        
        return view('pages.pengaturan.index', [
            'title' => 'Pengaturan - Putra Panggil Jaya',
            'navTitle' => 'Pengaturan',
            'active' => 'pengaturan',
            'histories' => $histories
        ]);
    }
}
