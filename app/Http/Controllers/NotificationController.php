<?php

namespace App\Http\Controllers;

use App\Models\AgendaKendaraan;
use App\Models\Data;
use App\Models\SendVicon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    var $m_agendakendaraan, $m_data;

    public function __construct()
    {
        $this->m_agendakendaraan = new AgendaKendaraan();
        $this->m_data = new Data();
    }

    public function listNotifikasiDriver($id)
    {
        $data['view'] = $this->m_agendakendaraan->get_list_agendakendaraan_where("agendakendaraan.id = '$id'");
        $query_update = array(
            'persiapan' => 'ready'
        );

        $this->m_data->updatedata("id = '$id'", $query_update, 'agendakendaraan');

        return view('admin.agendakendaraan.detail', $data);
    }

    public function listNotifikasiDriverRead($id)
    {
        $data['view'] = $this->m_agendakendaraan->get_list_agendakendaraan_where("agendakendaraan.id = '$id'");

        return view('admin.agendakendaraan.detail', $data);
    }

    public function listNotifikasiVicon($id)
    {
        $list = SendVicon::getSendviconAndRuanganById($id);
        $petugas = Auth::user()->petugas;
        $username = Auth::user()->username;

        $query_update = [];
        if ($petugas == 'Umum') {
            foreach ($list as $key => $value) {
                $persiapanRapat = $value->persiapanrapat;
            }

            if (!empty($persiapanRapat)) {
                $username = $persiapanRapat . ', ' . $username;
            }

            $query_update = ['persiapanrapat' => $username];
        } else if ($petugas == 'TI') {
            foreach ($list as $key => $value) {
                $persiapanvicon = $value->persiapanvicon;
            }

            if (!empty($persiapanvicon)) {
                $username = $persiapanvicon . ', ' . $username;
            }

            $query_update = ['persiapanvicon' => $username];
        }

        SendVicon::where('id', $id)->update($query_update);
        // dd($list);
        return view('admin.jadwal-vicon.notification', ['list' => $list]);
    }

    public function listNotifikasiViconRead($id)
    {
        $list = SendVicon::getSendviconAndRuanganById($id);

        return view('admin.jadwal-vicon.notification', ['list' => $list]);
    }
}
