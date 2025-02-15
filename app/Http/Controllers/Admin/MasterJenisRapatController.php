<?php

namespace App\Http\Controllers\Admin;

use  App\Http\Controllers\Controller;
use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MasterJenisRapatController extends Controller
{
    var $m_data;
    public function __construct()
    {
        $this->m_data = new Data();
    }

    public function index()
    {
        $view = $this->m_data->tampilall('jenisrapat');
        $data = array(
            "title"     => "Pemesanan Layanan Video Conference",
            "halaman"   =>  "Dashboard",
            "linkhalaman"   =>  "",
            "view" => $view
        );

        return view('admin.jenisrapat.index', $data);
    }

    public function save(Request $request)
    {
        $nama = $request->nama;
        $warna = $request->kode_warna;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = array(
            'nama' => $nama,
            'kode_warna' => $warna,
            'status' => $status,
            'keterangan' => $keterangan ?? ''
        );

        $this->m_data->input_data($data, 'jenisrapat');

        Session::flash('success', "Berhasil disimpan");
        return redirect(route('admin.dashboard.master.jenis.index'));
    }

    public function update(Request $request, $id)
    {
        $nama = $request->nama;
        $warna = $request->kode_warna;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $where = 'id = ' . $id;
        $data = array(
            'nama' => $nama,
            'kode_warna' =>  $warna,
            'status' => $status,
            'keterangan' => $keterangan ?? ''
        );

        $this->m_data->updatedata($where, $data, 'jenisrapat');

        Session::flash('success', "Berhasil diupdate");

        return redirect(route('admin.dashboard.master.jenis.index'));
    }
}
