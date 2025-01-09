<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function inventory()
    {
        return view('dashboard.inventory');
    }

    public function account()
    {
        return view('dashboard.account');
    }
}
