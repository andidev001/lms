<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuruController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // For now, only check if logged in. We can add specific role middleware later
        // e.g., $this->middleware(['auth', 'role:guru']);
        $this->middleware('auth');
    }

    /**
     * Show the guru dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('guru.dashboard');
    }
}
