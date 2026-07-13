<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DemoController extends Controller
{
    public function index(): View
    {
        return view('demo.index');
    }

    public function user(): View
    {
        return view('demo.user');
    }

    public function admin(): View
    {
        return view('demo.admin');
    }
}
