<?php

namespace App\Http\Controllers;

use App\Models\Data;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class MasterPenggunaController extends Controller
{
    var $m_data;

    public function __construct()
    {
        $this->m_data = new Data();
    }

    public function index()
    {
        $view = $this->m_data->tampilalluser('master_user');
        $bagian = $this->m_data->getActiveBagian();
        $hak_akses = $this->m_data->getActiveHakAkses();
        $data = array(
            "title"     => "ARHAN PTPN I",
            "halaman"   => "Dashboard",
            "linkhalaman" => "",
            "view" => $view,
            "bagian" => $bagian,
            "hak_akses" => $hak_akses
        );

        return view('admin.pengguna.index', $data);
    }

    public function formCreatePengguna()
    {
        $bagian = $this->m_data->getActiveBagian();
        $hak_akses = $this->m_data->getActiveHakAkses();
        return view('admin.pengguna.create', compact('bagian', 'hak_akses'));
    }

    public function createPengguna(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'master_user_nama' => 'required|unique:master_user,master_user_nama|alpha_dash|lowercase',
            'master_nama_bagian_id' => 'required',
            'master_hak_akses_id' => 'required',
            'master_user_no_hp' => 'required',
            'master_user_status' => 'required',
            'master_user_password' => 'required',
            'konpassword' => 'required|same:master_user_password'
        ], [
            'master_user_nama.required' => 'Username masih kosong, wajib diisi',
            'master_user_nama.unique' => 'Username sudah ada, silakan gunakan Username lain',
            'master_user_nama.alpha_dash' => 'Username hanya menerima a-z, 0-9, -, _ dan tanpa spasi',
            'master_user_nama.lowercase' => 'Username hanya menerima huruf kecil',
            'master_nama_bagian_id.required' => 'Bagian masih kosong, wajib dipilih',
            'master_hak_akses_id.required' => 'Hak Akses masih kosong, wajib dipilih',
            'master_user_no_hp.required' => 'No. Handphone masih kosong, wajib diisi',
            'master_user_status.required' => 'Status masih kosong, wajib diisi',
            'master_user_password.required' => 'Password masih kosong, wajib diisi',
            'konpassword.required' => 'Konfirmasi Password masih kosong, wajib diisi',
            'konpassword.same' => 'Konfirmasi Password tidak sama dengan Password',
        ]);

        if ($validatedData->fails()) {
            return redirect(route('admin.dashboard.master.pengguna.formCreate'))->withErrors($validatedData)->withInput();
        }

        $master_user_nama = $request->master_user_nama;
        $master_nama_bagian_id = $request->master_nama_bagian_id;
        $master_hak_akses_id = $request->master_hak_akses_id;
        $master_user_no_hp = $request->master_user_no_hp;
        $master_user_status = $request->master_user_status;
        $master_user_password = $request->master_user_password;
        $master_user_keterangan = $request->master_user_keterangan;

        // Hash the password using bcrypt
        $hashedPassword = Hash::make($master_user_password);

        $data = array(
            'master_user_nama' => $master_user_nama,
            'master_nama_bagian_id' => $master_nama_bagian_id,
            'master_hak_akses_id' => $master_hak_akses_id,
            'master_user_no_hp' => $master_user_no_hp,
            'master_user_status' => $master_user_status,
            'master_user_password' => $hashedPassword, // Use hashed password here
            'master_user_keterangan' => $master_user_keterangan ?? '',
        );

        $this->m_data->input_data($data, 'master_user');
        Session::flash('success', "Berhasil disimpan");
        return redirect(route('admin.dashboard.master.pengguna.index'));
    }

    public function formEditPengguna($id)
    {
        $data['master_user'] = User::where('master_user_id',  $id)->first();
        $data['bagian'] = $this->m_data->getActiveBagian();
        $data['hak_akses'] = $this->m_data->getActiveHakAkses();

        return view('admin.pengguna.edit', $data);
    }

    public function updatePengguna(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'master_user_nama' => 'required|unique:master_user,master_user_nama,' . $id . ',master_user_id|alpha_dash|lowercase',
            'master_user_no_hp' => 'required',
            'master_nama_bagian_id' => 'required',
            'master_hak_akses_id' => 'required',
            'master_user_status' => 'required',
        ], [
            'master_user_nama.required' => 'Username masih kosong, wajib diisi',
            'master_user_nama.unique' => 'Username sudah ada, silakan gunakan Username lain',
            'master_user_nama.alpha_dash' => 'Username hanya menerima a-z, 0-9, _ dan tanpa spasi',
            'master_user_nama.lowercase' => 'Username hanya menerima huruf kecil',
            'master_nama_bagian_id.required' => 'Bagian masih kosong, wajib dipilih',
            'master_hak_akses_id.required' => 'Hak Akses masih kosong, wajib dipilih',
            'master_user_no_hp.required' => 'No. Handphone masih kosong, wajib diisi',
            'master_user_status.required' => 'Status masih kosong, wajib diisi',
        ]);

        if ($validatedData->fails()) {
            return redirect(route('admin.dashboard.master.pengguna.formUpdate', ['master_user_id' => $id]))->withErrors($validatedData)->withInput();
        }

        $master_user_nama = $request->master_user_nama;
        $master_nama_bagian_id = $request->master_nama_bagian_id;
        $master_hak_akses_id = $request->master_hak_akses_id;
        $master_user_no_hp = $request->master_user_no_hp;
        $master_user_status = $request->master_user_status;
        $master_user_keterangan = $request->master_user_keterangan;

        $data = array(
            'master_user_nama' => $master_user_nama,
            'master_nama_bagian_id' => $master_nama_bagian_id,
            'master_hak_akses_id' => $master_hak_akses_id,
            'master_user_no_hp' => $master_user_no_hp,
            'master_user_status' => $master_user_status,
            'master_user_keterangan' => $master_user_keterangan ?? ''
        );

        $this->m_data->updatedata('master_user_id = ' . $id, $data, 'master_user');
        Session::flash('success', "Berhasil diupdate");
        return redirect(route('admin.dashboard.master.pengguna.index'));
    }

    public function hapuspengguna(Request $request)
    {
        $id = $request->id;

        if ($this->m_data->cek_id('agendakendaraan', 'id_user', $id) > 0) {
            Session::flash('error', "Pengguna yang dipilih terdapat pada Agenda Kendaraan, untuk menghapusnya dibutuhkan menghapus data pada Agenda Kendaraan, atau non-aktif pada status untuk membuat pengguna tidak tampil pada pilihan pengemudi Agenda Kendaraan");
        } else {
            $where = array('master_user_id' => $id);
            $this->m_data->hapusdata($where, 'master_user');
            Session::flash('success', "Berhasil dihapus");
        }
        return redirect(route('admin.dashboard.master.pengguna.index'));
    }

    public function halaman_resetpassword($id)
    {
        $data['master_user'] = User::where('master_user_id', $id)->first();

        return view('admin.pengguna.resetpassword', $data);
    }

    public function resetpassword(Request $request, $id)
    {
        $validatedData = Validator::make($request->all(), [
            'master_user_password' => 'required',
            'konpassword' => 'required|same:master_user_password',
        ], [
            'master_user_password.required' => 'Password masih kosong, wajib diisi',
            'konpassword.required' => 'Konfirmasi Password  masih kosong, wajib diisi',
            'konpassword.same' => 'Konfirmasi Password  tidak sama dengan Password',
        ]);

        if ($validatedData->fails()) {
            return redirect(route('admin.dashboard.master.pengguna.formResetPassword', ['master_user_id' => $id]))->withErrors($validatedData)->withInput();
        }

        // Retrieve new password from the request
        $master_user_password = $request->master_user_password;

        // Hash the new password
        $hashedPassword = Hash::make($master_user_password);

        // Update the user with the new hashed password
        User::where('master_user_id', $id)->update(['master_user_password' => $hashedPassword]);

        Session::flash('success', "Berhasil diupdate");
        return redirect(route('admin.dashboard.master.pengguna.index'));
    }
}
