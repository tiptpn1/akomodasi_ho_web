<?php

namespace App\Http\Controllers;

use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class MasterKendaraanController extends Controller
{
    var $m_data;
    public function __construct()
    {
        $this->m_data = new Data();
    }

    public function index()
    {
        $view = $this->m_data->tampilall('kendaraan');
        $data = array(
            "title"     => "ARHAN PTPN I",
            "halaman"   =>  "Dashboard",
            "linkhalaman"   =>  "",
            "view" => $view
        );

        return view('admin.kendaraan.index', $data);
    }

    public function save(Request $request)
    {
        $no_polisi = $request->no_polisi;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = array(
            'no_polisi' => $no_polisi,
            'status' => $status,
            'keterangan' => $keterangan ?? ''
        );

        $this->m_data->input_data($data, 'kendaraan');

        Session::flash('success', "Berhasil disimpan");
        return redirect(route('admin.dashboard.master.kendaraan.index'));
    }

    public function update(Request $request, $id)
    {
        $id = $request->id;
        $no_polisi = $request->no_polisi;
        $status = $request->status;
        $keterangan = $request->keterangan;
        $where = 'id = ' . $id;

        $data = array(
            'no_polisi' => $no_polisi,
            'status' => $status,
            'keterangan' => $keterangan ?? '',
        );

        $tes = $this->m_data->updatedata($where, $data, 'kendaraan');

        Session::flash('success', "Berhasil diupdate");
        return redirect(route('admin.dashboard.master.kendaraan.index'));
    }

    public function delete(Request $request, $id)
    {
        if ($this->m_data->cek_id('agendakendaraan', 'id_kendaraan', $id) > 0) {
            Session::flash('error', "Kendaraan yang dipilih terdapat pada Agenda Kendaraan, untuk menghapusnya dibutuhkan menghapus data pada Agenda Kendaraan, atau non-aktif pada status untuk membuat kendaraan tidak tampil pada pilihan kendaraan di Agenda Kendaraan");
        } else {
            // $kendaraan = $request->kendaraan;
            $where = array('id' => $id);
            $this->m_data->hapusdata($where, 'kendaraan');
            Session::flash('success', "Berhasil dihapus");
        }

        return redirect(route('admin.dashboard.master.kendaraan.index'));
    }
}
