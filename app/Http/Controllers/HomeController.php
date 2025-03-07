<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $histories = History::latest()->get();

        return view('pages.index', [
            'title' => 'Dashboard Putra Panggil Jaya',
            'navTitle' => 'Dashboard',
            'active' => 'dashboard',
            'histories' => $histories
        ]);
    }

    public function dashboardUser()
    {
        return view('pages.dashboard-user', [
            'title' => 'Putra Panggil Jaya',
            'navTitle' => 'Dashboard',
            'active' => 'dashboard',
        ]);
    }
}
