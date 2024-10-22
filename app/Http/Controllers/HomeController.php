<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $bagians = Bagian::orderByDesc('master_bagian_id')
            ->get();

        $ruangans = Ruangan::orderByDesc('id')->get();

        $view_data = [
            'bagians' => $bagians,
            'ruangans' => $ruangans
        ];
        return view('guests.home', $view_data);
    }
}
