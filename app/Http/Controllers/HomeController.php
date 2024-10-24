<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('guests.home');
    }
}
