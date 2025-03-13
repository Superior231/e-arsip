<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Support\Str;

class HistoryController extends Controller
{
    public function index()
    {
        $histories = History::latest()->get();
        $history_mutate = $histories->filter(function($history) {
            return Str::contains($history->method, 'mutate');
        });
        $history_category = $histories->where('type', 'category');
        
        return view('pages.history.index', [
            'title' => 'History - Putra Panggil Jaya',
            'navTitle' => 'History',
            'active' => 'history',
            'histories' => $histories,
            'history_mutate' => $history_mutate,
            'history_category' => $history_category,
        ]);
    }

    public function detail($id)
    {
        $histories = History::latest()->get();
        $history = $histories->find($id);
        
        return view('pages.history.detail', [
            'title' => 'Detail - Putra Panggil Jaya',
            'navTitle' => 'Detail History',
            'active' => 'history',
            'histories' => $histories,
            'history' => $history,
        ]);
    }
}
