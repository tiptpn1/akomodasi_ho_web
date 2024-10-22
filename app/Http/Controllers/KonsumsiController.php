<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SendVicon;
use App\Models\Konsumsi;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;


class KonsumsiController extends Controller
{
    public function index()
    {
        // $konsumsi = Konsumsi::with('sendVicon')->get();
        $konsumsi = Konsumsi::with(['sendVicon.bagian'])->get();
        return view('konsumsi.index', compact('konsumsi'));
    }

    public function create()
    {
        // 
    }

    // Menyimpan data konsumsi baru
    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $konsumsi = Konsumsi::find($id);

        $konsumsi->m_pagi = $request->input('makan.pagi', $konsumsi->m_pagi);
        $konsumsi->m_siang = $request->input('makan.siang', $konsumsi->m_siang);
        $konsumsi->m_malam = $request->input('makan.malam', $konsumsi->m_malam);

        $konsumsi->s_pagi = $request->input('snack.pagi', $konsumsi->s_pagi);
        $konsumsi->s_siang = $request->input('snack.siang', $konsumsi->s_siang);
        $konsumsi->s_sore = $request->input('snack.sore', $konsumsi->s_sore);

        // Update biaya
        $konsumsi->biaya_m_pagi = $request->input('biaya_m_pagi', $konsumsi->biaya_m_pagi);
        $konsumsi->biaya_m_siang = $request->input('biaya_m_siang', $konsumsi->biaya_m_siang);
        $konsumsi->biaya_m_malam = $request->input('biaya_m_malam', $konsumsi->biaya_m_malam);
        $konsumsi->biaya_s_pagi = $request->input('biaya_s_pagi', $konsumsi->biaya_s_pagi);
        $konsumsi->biaya_s_siang = $request->input('biaya_s_siang', $konsumsi->biaya_s_siang);
        $konsumsi->biaya_s_sore = $request->input('biaya_s_sore', $konsumsi->biaya_s_sore);

        $konsumsi->status = $request->input('status', $konsumsi->status);
        $konsumsi->keterangan = $request->input('keterangan', $konsumsi->keterangan);
        $konsumsi->biaya_lain = $request->input('biaya_lain', $konsumsi->biaya_lain);

        // Save the updated konsumsi
        $konsumsi->save();

        return redirect()->route('konsumsi.index')->with('success', 'Berhasil diupdate');
    }
    public function approve($id)
    {
        // Dapatkan data konsumsi berdasarkan ID
        $konsumsi = Konsumsi::find($id);

        // Pastikan data ditemukan
        if (!$konsumsi) {
            return redirect()->back()->with('error', 'Data konsumsi tidak ditemukan.');
        }

        // Dapatkan nama user yang sedang login
        $user = Auth::user()->master_user_nama;

        // Proses approval runtut sesuai role user
        switch ($user) {
            case 'asisten_ga':
                if ($konsumsi->status == 0) {
                    $konsumsi->status = 1; // Set status ke 1
                    $konsumsi->save();
                    return redirect()->back()->with('success', 'Pengajuan berhasil di-approve oleh Asisten GA.');
                }
                return redirect()->back()->with('error', 'Asisten GA hanya bisa approve jika status saat ini 0.');

            case 'kasubdiv_ga':
                if ($konsumsi->status == 1) {
                    $konsumsi->status = 2; // Set status ke 2
                    $konsumsi->save();
                    return redirect()->back()->with('success', 'Pengajuan berhasil di-approve oleh Kasubdiv GA.');
                }
                return redirect()->back()->with('error', 'Kasubdiv GA hanya bisa approve jika status saat ini 1.');

            case 'kadiv_ga':
                if ($konsumsi->status == 2) {
                    $konsumsi->status = 3; // Set status ke 3
                    $konsumsi->save();
                    return redirect()->back()->with('success', 'Pengajuan berhasil di-approve oleh Kadiv GA.');
                }
                return redirect()->back()->with('error', 'Kadiv GA hanya bisa approve jika status saat ini 2.');

            default:
                return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk melakukan approval.');
        }
    }

    // Menghapus data konsumsi
    public function destroy(Request $request, $id)
    {
        $konsumsi = Konsumsi::findOrFail($id);

        // Update status menjadi 4 (dibatalkan)
        $konsumsi->status = 4;
        $konsumsi->save();

        return redirect()->route('konsumsi.index')->with('success', 'Permintaan konsumsi dibatalkan.');
    }
}
