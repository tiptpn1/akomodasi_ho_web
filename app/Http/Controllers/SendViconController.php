<?php

namespace App\Http\Controllers;

use App\Exports\AgendaExport;
use App\Http\Requests\StoreSendviconRequest;
use App\Http\Requests\StoreViconAdminRequest;
use App\Http\Requests\UpdateViconAdminRequest;
use App\Models\Bagian;
use App\Models\JenisRapat;
use App\Models\MasterLink;
use App\Models\Ruangan;
use App\Models\SendVicon;
use App\Models\Konsumsi;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class SendViconController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bagians = Bagian::orderBy('master_bagian_id', 'desc')->get();
        $userBagianId = Auth::user()->master_nama_bagian_id;
        $jenisRapat = JenisRapat::orderBy('id', 'desc')->get();
        $jenisRapatWithStatus = JenisRapat::where('status', 'Aktif')->get();
        $ruangans = Ruangan::orderBy('id', 'desc')->get();
        // $petugasRapat = User::where('petugas', 'Umum')->where('status', 'Aktif')->get();
        // $petugasTI = User::where('petugas', 'TI')->where('status', 'Aktif')->get();
        $masterlink = MasterLink::orderBy('id', 'desc')->get();
        $konsumsi = Konsumsi::orderBy('id', 'desc')->get();

        $view_data = [
            'bagians' => $bagians,
            'jenis_rapat' => $jenisRapat,
            'jenis_rapat_with_status' => $jenisRapatWithStatus,
            'ruangans' => $ruangans,
            // 'petugas_rapat' => $petugasRapat,
            // 'petugas_ti' => $petugasTI,
            'masterlink' => $masterlink,
            'konsumsi' => $konsumsi,
            'bagian_id'=> $userBagianId
        ];
        return view('admin.jadwal-vicon.index', $view_data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSendviconRequest $request)
    {
        $validated = $request->validated();

        $user = 'Bagian';


        $bagian = Bagian::find($validated['bagian']);
        if (!$bagian) {
            return redirect()->back()->with('bagian', 'Bagian tidak valid')->withInput($validated);
        }

        if ($bagian->kode_pin != $validated['passwordVerif']) {
            return redirect()->back()->with('passwordVerif', 'Kode tidak sesuai')->withInput($validated);
        }

        $setTanggal = explode(' - ', $validated['tanggal']);
        $tanggalAwal = Carbon::parse($setTanggal[0])->format('Y-m-d');
        $tanggalAkhir = Carbon::parse($setTanggal[1])->format('Y-m-d');

        $ruangan = null;
        $ruangan_lain = null;
        $id_ruangan = null;
        if ($validated['ruangan'] != 'lain') {
            $id_ruangan = $validated['ruangan'];
            $ruanganObj = Ruangan::find($validated['ruangan']);
            if ($ruanganObj) {
                $ruangan = $ruanganObj->nama;
            }
        } else {
            $ruangan_lain = $validated['ruangan2'] ?? '';
        }

        for ($i = $tanggalAwal; $i <= $tanggalAkhir; $i++) {
            $eventDate = Carbon::parse($i)->format('Y-m-d');

            $token = Str::random(8);

            $sk = $request->file('sk');
            if ($sk) {
                $name = $sk->getClientOriginalName();
                $namefile = time() . '_' . $name;
                $url = $sk->move(public_path() . '/uploads/sk', $namefile);
            }

            $sendvicon = new SendVicon();
            $sendvicon->user = $user;
            $sendvicon->privat = $validated['privat'];
            $sendvicon->bagian_id = $validated['bagian'];
            $sendvicon->acara = $validated['acara'];
            $sendvicon->dokumentasi = $validated['dokumentasi'];
            $sendvicon->tanggal = $eventDate;
            $sendvicon->waktu = $validated['waktu1'];
            $sendvicon->waktu2 = $validated['waktu2'];
            $sendvicon->peserta = $validated['peserta'];
            $sendvicon->jumlahpeserta = $validated['jumlahpeserta'];
            $sendvicon->id_ruangan = $id_ruangan;
            $sendvicon->ruangan = $ruangan;
            $sendvicon->ruangan_lain = $ruangan_lain;
            $sendvicon->vicon = $validated['vicon'];
            $sendvicon->jenis_link = $validated['jenis_link'];
            $sendvicon->personil = $validated['nopersonel'];
            $sendvicon->keterangan = $validated['keterangan'];
            $sendvicon->agenda_direksi = 'Tidak';
            $sendvicon->jenisrapat_id = 8;
            $sendvicon->persiapanrapat = '';
            $sendvicon->persiapanvicon = '';
            $sendvicon->is_reminded = 0;
            $sendvicon->token = $token;

            if ($sk) {
                $sendvicon->sk = 'uploads/sk/' . $namefile;
            }

            $sendvicon->save();

            // firebase must be send here

            $dataSession[] = $sendvicon->id;
            Session::put('id_sendvicon', $dataSession);

            $konsumsi = new Konsumsi();
            $konsumsi->id_sendvicon = $sendvicon->id;
            $konsumsi->m_pagi = $request->input('makan.pagi', 0);
            $konsumsi->m_siang = $request->input('makan.siang', 0);
            $konsumsi->m_malam = $request->input('makan.malam', 0);
            $konsumsi->s_pagi = $request->input('snack.pagi', 0);
            $konsumsi->s_siang = $request->input('snack.siang', 0);
            $konsumsi->s_sore = $request->input('snack.sore', 0);
            $konsumsi->save();

            $waktu1 = $validated['waktu1'];
            $waktu2 = $validated['waktu2'];

            $ruangan_tersedia = Ruangan::whereNotIn('id', function ($query) use ($eventDate, $waktu1, $waktu2) {
                $query->select('id_ruangan')
                    ->from('sendvicon')
                    ->where('tanggal', $eventDate)
                    ->where('waktu', '>=', $waktu1)
                    ->where('waktu2', '<=', $waktu2)
                    ->whereNotNull('id_ruangan');
            })->where('status', 'Aktif')
                ->where('id', '!=', 5)
                ->get();

            $array_ruangan = [];

            foreach ($ruangan_tersedia as $rt) {
                $array_ruangan[] = $rt->nama;
            }

            $daftar_ruangan = "<h5><b><span style='color:red'>Maaf, tidak ada rekomendasi tempat kosong.<span><b></h5>";
            if (count($array_ruangan) > 0) {
                $daftar_ruangan = "<h5>Daftar ruangan yang tersedia pada tanggal dan waktu tersebut :</h5><h5><b><span style='color:green'>" . implode(", ", $array_ruangan) . "<span></b></h5>";
            }

            $checkJadwal = SendVicon::cekctr($eventDate, $validated['ruangan'], $waktu1, $waktu2);
            $cekvicon_nama_waktu = SendVicon::cekvicon_nama_waktu($validated['acara'], $eventDate, $waktu1, $waktu2);
            if ($checkJadwal > 3) {
                Session::flash('gglindex_ruangan', '<h4><b>Ruang rapat tersebut sudah dipesan untuk rapat lain pada waktu bersamaan, tetap ingin melakukan pemesanan?</b></h4>' . $daftar_ruangan);
            } else if ($cekvicon_nama_waktu > 1) {
                Session::flash('gglindex_nama', 'Nama acara pada waktu dan tanggal tersebut telah ada pada Tabel Pemesanan, apakah Anda tetap ingin melakukan pemesanan dengan jadwal tersebut?');
            } else {
                Session::flash('success', 'Pemesanan berhasil dilakukan');
            }
        }
        return redirect('/#tabel_pemesanan');
    }

    public function storeAdmin(StoreViconAdminRequest $request)
    {
        $validated = $request->validated();

        try {
            $user = Auth::user()->master_user_nama;
            $setTanggal = explode(' - ', $validated['tanggal']);
            $tanggalAwal = Carbon::parse($setTanggal[0])->format('Y-m-d');
            $tanggalAkhir = Carbon::parse($setTanggal[1])->format('Y-m-d');

            $ruangan = null;
            $ruangan_lain = null;
            $id_ruangan = null;
            if ($validated['ruangan'] != 'lain') {
                $id_ruangan = $validated['ruangan'];
                $ruanganobj = Ruangan::find($validated['ruangan']);
                if ($ruanganobj) {
                    $ruangan = $ruanganobj->nama;
                }
            } else {
                $ruangan_lain = $validated['ruangan2'] ?? '';
            }

            $sk = $request->file('sk');
            if ($sk) {
                $name = $sk->getClientOriginalName();
                $namefile = time() . '_' . $name;
                $url = $sk->move(public_path() . '/uploads/sk', $namefile);
            }

            $dataSession = [];

            for ($i = $tanggalAwal; $i <= $tanggalAkhir; $i++) {
                $eventDate = Carbon::parse($i)->format('Y-m-d');

                $token = Str::random(8);

                $sendvicon = new SendVicon();
                $sendvicon->user = $user;
                $sendvicon->bagian_id = $validated['bagian'];
                $sendvicon->acara = $validated['acara'];
                $sendvicon->tanggal = $eventDate;
                //$sendvicon->agenda_direksi = $validated['agenda_direksi'];
                $sendvicon->jenisrapat_id = $validated['jenisrapat'];
                $sendvicon->waktu = $validated['waktu'];
                $sendvicon->waktu2 = $validated['waktu2'];
                $sendvicon->peserta = $validated['peserta'];
                $sendvicon->jumlahpeserta = $validated['jumlahpeserta'];
                $sendvicon->id_ruangan = $id_ruangan ?? null;
                $sendvicon->ruangan = $ruangan;
                $sendvicon->ruangan_lain = $ruangan_lain;
                //$sendvicon->privat = $validated['privat'];
                $sendvicon->vicon = $validated['vicon'];
                $sendvicon->jenis_link = $validated['jenis_link'];
                $sendvicon->personil = $validated['nopersonel'];
                $sendvicon->keterangan = $validated['keterangan'];
                //$sendvicon->link = $validated['link'];
                //$sendvicon->password = $validated['password'];
                $sendvicon->dokumentasi = null;
                $sendvicon->persiapanrapat = '';
                $sendvicon->persiapanvicon = '';
                $sendvicon->is_reminded = 0;
                $sendvicon->token = $token;

                if ($sk) {
                    $sendvicon->sk = 'uploads/sk/' . $namefile;
                }

                $sendvicon->save();

                $jam = $validated['waktu'] . " - " . $validated['waktu2'];
                // send Notification token topics/sendvicon must be here

                $dataSession[] = $sendvicon->id;

                $konsumsi = new Konsumsi();
                $konsumsi->id_sendvicon = $sendvicon->id;
                $konsumsi->m_pagi = $request->input('makan.pagi', 0);
                $konsumsi->m_siang = $request->input('makan.siang', 0);
                $konsumsi->m_malam = $request->input('makan.malam', 0);
                $konsumsi->s_pagi = $request->input('snack.pagi', 0);
                $konsumsi->s_siang = $request->input('snack.siang', 0);
                $konsumsi->s_sore = $request->input('snack.sore', 0);
                $konsumsi->save();

                $waktu1 = $validated['waktu'];
                $waktu2 = $validated['waktu2'];

                $ruangan_tersedia = Ruangan::whereNotIn('id', function ($query) use ($eventDate, $waktu1, $waktu2) {
                    $query->select('id_ruangan')
                        ->from('sendvicon')
                        ->where('tanggal', $eventDate)
                        ->where('waktu', '>=', $waktu1)
                        ->where('waktu2', '<=', $waktu2)
                        ->whereNotNull('id_ruangan');
                })->where('status', 'Aktif')
                    ->where('id', '!=', 5)
                    ->get();

                $array_ruangan = [];

                foreach ($ruangan_tersedia as $rt) {
                    $array_ruangan[] = $rt->nama;
                }

                $daftar_ruangan = "<h5><b><span style='color:red'>Maaf, tidak ada rekomendasi tempat kosong.<span><b></h5>";
                if (count($array_ruangan) > 0) {
                    $daftar_ruangan = "<h5>Daftar ruangan yang tersedia pada tanggal dan waktu tersebut :</h5><h5><b><span style='color:green'>" . implode(", ", $array_ruangan) . "<span></b></h5>";
                }

                $checkJadwal = SendVicon::cekctr($eventDate, $validated['ruangan'], $waktu1, $waktu2);
                $cekvicon_nama_waktu = SendVicon::cekvicon_nama_waktu($validated['acara'], $eventDate, $waktu1, $waktu2);
                if ($checkJadwal > 1) {
                    Session::flash('ggl_ruangan', '<h4><b>Ruang rapat tersebut sudah dipesan untuk rapat lain pada waktu bersamaan, tetap ingin melakukan pemesanan?</b></h4>' . $daftar_ruangan);
                } else if ($cekvicon_nama_waktu > 1) {
                    Session::flash('ggl_nama', 'Nama acara pada waktu dan tanggal tersebut telah ada pada Tabel Pemesanan, apakah Anda tetap ingin melakukan pemesanan dengan jadwal tersebut?');
                } else {
                    Session::flash('success', 'Pemesanan berhasil dilakukan');
                }
            }

            Session::put('id_sendvicon', $dataSession);

            $flashMessages = [
                'success' => Session::get('success'),
                'ggl_ruangan' => Session::get('ggl_ruangan'),
                'ggl_nama' => Session::get('ggl_nama'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data saved successfully',
                'data' => $dataSession,
                'flashMessages' => $flashMessages
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], $th->getCode() ? 400 : 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $sendvicons = SendVicon::with(['jenisrapat', 'ruangan', 'bagian', 'masterLink', 'konsumsi'])->find($id);

            if ($sendvicons) {
                return response()->json([
                    'success' => true,
                    'data' => $sendvicons
                ]);
            }

            throw new \Exception('Data not found', 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], $th->getCode() ?: 500);
        }
    }

    public function showGuest(string $id)
    {
        try {
            $sendvicons = SendVicon::with(['jenisrapat', 'ruangan', 'bagian', 'masterLink', 'konsumsi'])->find($id);

            if ($sendvicons) {
                return response()->json([
                    'success' => true,
                    'data' => $sendvicons
                ]);
            }

            throw new \Exception('Data not found', 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], $th->getCode() ?: 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateViconAdminRequest $request)
    {
        $validated = $request->validated();
        try {
            $id = $validated['id'];

            $tanggal = Carbon::createFromFormat('d/m/Y', $validated['tanggal'])->format('Y-m-d');

            $ruangan = null;
            $ruangan_lain = null;
            $id_ruangan = null;
            if ($validated['ruangan'] != 'lain' && !empty($validated['ruangan'])) {
                $id_ruangan = $validated['ruangan'];
                $ruanganobj = Ruangan::find($validated['ruangan']);
                if ($ruanganobj) {
                    $ruangan = $ruanganobj->nama;
                }
            } else {
                $ruangan_lain = $validated['ruangan2'] ?? '';
            }

            $sk = $request->file('sk');
            if ($sk) {
                $name = $sk->getClientOriginalName();
                $namefile = time() . '_' . $name;
                $url = $sk->move(public_path() . '/uploads/sk', $namefile);
            }

            $sendvicon = SendVicon::find($id);
            if ($sendvicon) {
                $sendvicon->bagian_id = $validated['bagian'];
                $sendvicon->acara = $validated['acara'];
                $sendvicon->jenisrapat_id = $validated['jenisrapat'];
                $sendvicon->tanggal = $tanggal;
                $sendvicon->waktu = $validated['waktu'];
                $sendvicon->waktu2 = $validated['waktu2'];
                $sendvicon->agenda_direksi = $validated['agenda_direksi'];
                $sendvicon->peserta = $validated['peserta'];
                $sendvicon->jumlahpeserta = $validated['jumlahpeserta'];
                $sendvicon->id_ruangan = $id_ruangan ?? null;
                $sendvicon->ruangan = $ruangan;
                $sendvicon->ruangan_lain = $ruangan_lain;
                $sendvicon->personil = $validated['nopersonel'];
                $sendvicon->privat = $validated['privat'];
                $sendvicon->vicon = $validated['vicon'];
                $sendvicon->jenis_link = $validated['jenis_link'];
                $sendvicon->status = $validated['status'];
                $sendvicon->link = $validated['link'];
                $sendvicon->keterangan = $validated['keterangan'];
                $sendvicon->password = $validated['password'];

                if ($sk) {
                    $sendvicon->sk = 'uploads/sk/' . $namefile;
                }

                $sendvicon->save();

                // Handle konsumsi, create or update
                $konsumsi = Konsumsi::firstOrCreate(['id_sendvicon' => $sendvicon->id], [
                    'm_pagi' => 0,
                    'm_siang' => 0,
                    'm_malam' => 0,
                    's_pagi' => 0,
                    's_siang' => 0,
                    's_sore' => 0,
                ]);

                // Update konsumsi dengan nilai dari request (checkboxes)
                $konsumsi->m_pagi = $request->input('makan.pagi', 0);
                $konsumsi->m_siang = $request->input('makan.siang', 0);
                $konsumsi->m_malam = $request->input('makan.malam', 0);
                $konsumsi->s_pagi = $request->input('snack.pagi', 0);
                $konsumsi->s_siang = $request->input('snack.siang', 0);
                $konsumsi->s_sore = $request->input('snack.sore', 0);

                $konsumsi->save();

                $jam = $validated['waktu'] . " - " . $validated['waktu2'];
                // send Notification token topics/sendvicon must be here

                return response()->json([
                    'success' => true,
                    'message' => 'Vicon updated successfully',
                ]);
            }

            throw new \Exception('Data not found', 404);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], $th->getCode() ? 400 : 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            SendVicon::destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Data deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], $th->getCode() ?: 500);
        }
    }

    public function data(Request $request)
    {
        // if ($request->ajax()) {
        $sendvicon = SendVicon::data($request);
        $start = $request->input('start', 0);
        $recordsFiltered = $sendvicon->count();
        $raw = $sendvicon->limit($request->input('length', 10))->offset($start)->get();

        $data = $raw->map(function ($vicon, $index) use ($start) {
            $tanggal = Carbon::parse($vicon->tanggal)->format('Y-m-d');
            $formatedTime = Carbon::parse($vicon->waktu)->format('H:i') . ' - ' . Carbon::parse($vicon->waktu2)->format('H:i');
            $ruangan = $vicon->ruangan ?: $vicon->ruangan_lain ?? null;

            $cek = '0,';
            $cek_acara = '0,';
            $cekvicon_nama_waktu = SendVicon::cekvicon_nama_waktu($vicon->acara, $vicon->tanggal, $vicon->waktu, $vicon->waktu2);
            $cekjadwal = SendVicon::cekctr($vicon->tanggal, $vicon->id_ruangan, $vicon->waktu, $vicon->waktu2);

            if ($cekjadwal > 0) {
                $cek = '1,';
            }

            if ($cekvicon_nama_waktu > 1) {
                $cek_acara = '1,';
            }
            $viconLink = "";
            if ($vicon->vicon == 'Ya') {
                $viconLink = $vicon->vicon . "\n" . $vicon->jenis_link;
            } else {
                $viconLink = $vicon->vicon;
            }

            return [
                'DT_RowIndex' => ($index + 1) + $start . '.',
                'id' => $vicon->id,
                'bagian' => $vicon->bagian->bagian,
                'acara' => $cek_acara . $vicon->acara,
                'tanggal' => $tanggal,
                'waktu' => $formatedTime,
                'ruangan' => $cek . $ruangan,
                'vicon' => $viconLink,
                'status' => $vicon->status,
                'keterangan' => $vicon->keterangan,
                'action' => '<button style="margin-right: 6px; margin-bottom: 3px;" class="btn btn-primary btn-sm" onclick="detail(' . $vicon->id . ')">Detail</button>',
            ];
        });

        return response()->json([
            'draw' => $request->input('draw', 1),
            'data' => $data,
            'recordsTotal' => SendVicon::count(),
            'recordsFiltered' => $recordsFiltered,
        ]);
        // }
    }

    public function refreshCaptcha()
    {
        return response()->json(captcha_img('math'));
    }

    public function cancel()
    {
        $ids = Session::get('id_sendvicon');
        SendVicon::whereIn('id', $ids)->delete();
        Session::forget('id_sendvicon');
        Session::flash('success', 'Pemesanan dibatalkan');
        return redirect('/#tabel_pemesanan');
    }

    public function ceknama()
    {
        $ids = Session::get('id_sendvicon');
        $data = SendVicon::with(['ruangan'])
            ->whereIn('id', $ids)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu')
            ->get();

        foreach ($data as $vicon) {
            $acara = $vicon->acara;
            $tanggal = $vicon->tanggal;
            $waktu1 = $vicon->waktu;
            $waktu2 = $vicon->waktu2;
        }

        $cekViconNama = SendVicon::cekvicon_nama_waktu($acara, $tanggal, $waktu1, $waktu2);
        if ($cekViconNama > 1) {
            Session::flash('gglindex_nama', 'Nama acara pada waktu dan tanggal tersebut telah ada pada Tabel Pemesanan, apakah Anda tetap ingin melakukan pemesanan dengan jadwal tersebut?');
        }

        return redirect('/#tabel_pemesanan');
    }

    public function getData(Request $request)
    {
        // return response()->json($request->all());
        $query = SendVicon::dataTables($request);
        $recordFilter = $query->count();
        $list = $query->limit(10)->offset($request->input('start', 1))->get();
        $data = [];
        $no = $request->input('start');

        $daftar_hari = array(
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        );

        foreach ($list as $item) {
            $no++;

            $gettgl = explode('-', $item->tanggal);
            $th = $gettgl[0];
            $d = $gettgl[2];
            $bln = $gettgl[1];
            $settgl = "$d-$bln-$th";

            $getwaktu = explode(':', $item->waktu);
            $jam = $getwaktu[0];
            $detik = $getwaktu[2];
            $menit = $getwaktu[1];
            $setwaktu = "$jam:$menit";

            $getwaktua = explode(':', $item->waktu2);
            $jam2 = $getwaktua[0];
            $detik = $getwaktua[2];
            $menit2 = $getwaktua[1];
            $setwaktu2 = "$jam2:$menit2";

            $getacara = $item->acara;
            $gettanggal = $item->tanggal;
            $getruangan = $item->id_ruangan;
            $getwaktu1 = $item->waktu;
            $getwaktu2 = $item->waktu2;
            $bagian = $item->bagian != null ? $item->bagian->bagian : '';

            $namahari = date('l', strtotime($gettanggal));

            $cek = '0,';
            $cek_acara = '0,';
            $cekvicon_nama_waktu = SendVicon::cekvicon_nama_waktu($getacara, $gettanggal, $getwaktu1, $getwaktu2);
            $cekjadwal = SendVicon::cek_vicon_ruangan_waktu($getruangan, $gettanggal, $getwaktu1, $getwaktu2);

            if ($cekjadwal > 1) {
                $cek = "1,";
            }
            if ($cekvicon_nama_waktu > 1) {
                $cek_acara = "1,";
            }

            $row = [];
            $row[] = $no . ".";
            $row[] = $cek_acara . $item->acara;
            $row[] = $daftar_hari[$namahari] . ", " . $settgl;
            $row[] = $setwaktu . " - " . $setwaktu2;
            if (is_null($item->id_ruangan) or empty($item->id_ruangan)) {
                $row[] = $cek . $item->ruangan_lain;
            } else {
                $row[] = $cek . $item->ruangan;
            }
            $row[] = $bagian;
            if ($item->vicon == 'Ya') {
                $row[] = $item->vicon . "\n" . $item->jenis_link;
            } else {
                $row[] = $item->vicon;
            }
            $row[] = $item->keterangan;
            $row[] = $item->status_approval == 1 ? 'Approved' : 'Waiting for Approve';

            if (!in_array(Auth::user()->master_hak_akses_id, [5, 6])) {
                $approval_btn = '';
                if ($item->status_approval == 0) {
                    if (in_array($item->ruangan, ['Ruangan Rapat Teh', 'Ruangan Rapat Karet', 'Ruangan Rapat Robusta']) || in_array($item->id_ruangan, [13, 14, 15])) {
                        if (auth()->user()->master_hak_akses_id == 4 || auth()->user()->master_hak_akses_id == 2) {
                            $approval_btn = '<button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" class="btn btn-success btn-approve" onclick="approve(' . $item->id . ', \'' . $item->acara . '\')"><div class="d-flex align-items-center justify-content-center"><i class="fas fa-check"></i></div></button>';
                        }
                    } else if (auth()->user()->master_hak_akses_id == 2) {
                        $approval_btn = '<button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" class="btn btn-success btn-approve" onclick="approve(' . $item->id . ', \'' . $item->acara . '\')"><div class="d-flex align-items-center justify-content-center"><i class="fas fa-check"></i></div></button>';
                    }
                }

                $detailButton = '<button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" onclick="detail(' . "'" . $item->id . "'" . ')" class="btn btn-primary btn-sm"><div class="d-flex align-items-center justify-content-center"><i class="fas fa-eye" aria-hidden="true"></i></div>';
                $absensiButton = '<center><button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" onclick="absensi(' . "'" . $item->id . "'" . ')" class="btn btn-outline-success btn-sm"><div class="d-flex align-items-center justify-content-center"><i class="fas fa-star"></i></div>';
               // $invitation = '<center><button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" onclick="invitation(' . "'" . $item->id . "'" . ')" class="btn btn-info btn-sm"><i class="nav-icon fas fa-file"></i>';
                $editButton = '<center><button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" onclick="update(' . "'" . $item->id . "'" . ')" class="btn btn-warning btn-sm"><i class="nav-icon fas fa-edit"></i>';
                $hapusButton = '<center><button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" class="btn btn-sm btn-danger" onclick="hapus(' . "'" . $item->id . "'" . '); return false"><i class="far fa-trash-alt"></i>';

                if (!is_null($item->token)) {
                    $actionButton = in_array(auth()->user()->master_hak_akses_id, [2, 4]) ? $detailButton . $absensiButton . $editButton . $hapusButton : ($item->bagian_id == auth()->user()->master_nama_bagian_id || auth()->user()->master_user_nama == 'op_dosg' ? ($item->status_approval == 0 ? $detailButton . $absensiButton . $editButton . $hapusButton : $detailButton . $absensiButton ) : '');
                    $row[] = '<center>' . $approval_btn . $actionButton;
                } else {
                    $actionButton = in_array(auth()->user()->master_hak_akses_id, [2, 4]) ? $detailButton . $editButton . $hapusButton : ($item->bagian_id == auth()->user()->master_nama_bagian_id || auth()->user()->master_user_nama == 'op_dosg' ? ($item->status_approval == 0 ? $detailButton . $editButton . $hapusButton : $detailButton) : '');
                    $row[] = '<center>' . $approval_btn . $actionButton;
                }
            } else {
                $row[] = '<center>
                        <button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" onclick="detail(' . "'" . $item->id . "'" . ')" class="btn btn-primary btn-sm"><div class="d-flex align-items-center justify-content-center"><i class="fas fa-eye" aria-hidden="true"></i></div>';
            }

            $data[] = $row;
        }
        // dd($list->count());
        return response()->json([
            'draw' => $request->input('draw'),
            'recordsTotal' => SendVicon::count(),
            'recordsFiltered' => $recordFilter,
            'data' => $data,
        ]);
    }

    public function approveSendvicond(Request $request)
    {
        if (!in_array(auth()->user()->master_hak_akses_id, [2, 4])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak bisa approve',
            ], 403);
        }

        try {
            $vicon = Sendvicon::where('id', $request->id)->first();

            if (!$vicon) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }
            $konsumsi = Konsumsi::where('id_sendvicon', $request->id)->first();

            if ($konsumsi && $konsumsi->status == 0) {
                $konsumsi->status = 1;
                $konsumsi->save();
            }

            $vicon->update(['status_approval' => 1]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diapprove',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function resetFilter()
    {
        if (Session::has('filter_tanggal_awal')) {
            Session::forget('filter_tanggal_awal');
        }
        if (Session::has('filter_tanggal_akhir')) {
            Session::forget('filter_tanggal_akhir');
        }
        if (Session::has('filter_jenisrapat')) {
            Session::forget('filter_jenisrapat');
        }
        if (Session::has('filter_acara')) {
            Session::forget('filter_acara');
        }
        if (Session::has('filter_agenda_direksi')) {
            Session::forget('filter_agenda_direksi');
        }
        if (Session::has('filter_vicon')) {
            Session::forget('filter_vicon');
        }
        if (Session::has('filter_db_bagian')) {
            Session::forget('filter_db_bagian');
        }
        return redirect()->route('admin.vicon.index');
    }

    public function cancelAdmin(Request $request)
    {
        // $ids = json_decode($request->input('dataSession'), true);
        $ids = Session::get('id_sendvicon');
        SendVicon::whereIn('id', $ids)->delete();
        Session::forget('id_sendvicon');
        Session::flash('success', 'Pemesanan dibatalkan');
        return redirect()->route('admin.vicon.index');
    }

    public function ceknamaAdmin(Request $request)
    {
        $ids = json_decode($request->input('dataSession'), true);
        if (!$ids) return redirect()->route('admin.vicon.index');
        $data = SendVicon::with(['ruangan'])
            ->whereIn('id', $ids)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu')
            ->get();

        foreach ($data as $vicon) {
            $acara = $vicon->acara;
            $tanggal = $vicon->tanggal;
            $waktu1 = $vicon->waktu;
            $waktu2 = $vicon->waktu2;
        }

        $cekViconNama = SendVicon::cekvicon_nama_waktu($acara, $tanggal, $waktu1, $waktu2);
        if ($cekViconNama > 1) {
            Session::flash('ggl_nama', 'Nama acara pada waktu dan tanggal tersebut telah ada pada Tabel Pemesanan, apakah Anda tetap ingin melakukan pemesanan dengan jadwal tersebut?');
        }

        return redirect()->route('admin.vicon.index');
    }

    public function exportExcel(Request $request)
    {
        $data = SendVicon::download($request);
        $startPeriode = $request->get('tanggal_awal');
        $endPeriode = $request->get('tanggal_akhir');
        $downloadTime = Carbon::now()->format('d-m-Y H-i-s');
        $fileName = 'Rekap Agenda ' . $downloadTime . '.xlsx';

        return Excel::download(new AgendaExport($data, $startPeriode, $endPeriode), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('tanggal_awal', Carbon::today()->toDateString());
        $endDate = $request->input('tanggal_akhir', Carbon::today()->toDateString());

        $downloadTime = Carbon::now()->format('Y-m-d H-i-s');

        $sendvicon = SendVicon::download($request);

        $view_data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'download_time' => $downloadTime,
            'sendvicon' => $sendvicon
        ];

        $pdf = Pdf::loadView('exports.pdf.export_by_date', $view_data)->setPaper('A4', 'landscape');
        return $pdf->stream('Rekap Agenda ' . $downloadTime . '.pdf');
    }
}
