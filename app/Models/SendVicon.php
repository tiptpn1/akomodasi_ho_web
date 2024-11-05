<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SendVicon extends Model
{
    use HasFactory;

    protected $table = 'sendvicon';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'acara',
        'dokumentasi',
        'bagian_id',
        'agenda_direksi',
        'jenisrapat_id',
        'tanggal',
        'waktu',
        'waktu2',
        'ruangan',
        'ruangan_lain',
        'id_ruangan',
        'privat',
        'vicon',
        'personil',
        'peserta',
        'jumlahpeserta',
        'sk',
        'status',
        'link',
        'password',
        'keterangan',
        'persiapanrapat',
        'persiapanvicon',
        'durasi',
        'jenis_link',
        'user',
        'created',
        'status_absensi',
        'is_reminded',
        'status_approval',
    ];

    protected $guarded = [
        'id'
    ];

    public function bagian()
    {
        return $this->belongsTo(Bagian::class, 'bagian_id', 'master_bagian_id');
    }

    public function jenisrapat()
    {
        return $this->belongsTo(JenisRapat::class, 'jenisrapat_id', 'id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan', 'id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'sendvicon_id', 'id');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'sendvicon_id', 'id');
    }

    public function masterLink()
    {
        return $this->belongsTo(MasterLink::class, 'link', 'namalink');
    }
    public function konsumsi()
    {
        return $this->hasOne(Konsumsi::class, 'id_sendvicon', 'id');
    }

    public static function countDate($date)
    {
        $dateCount = self::with(['ruangan', 'masterLink'])
            ->where('tanggal', $date)
            ->orderBy('waktu')
            ->count();

        return $dateCount;
    }

    public static function cekctr($tanggal, $ruangan, $waktu1, $waktu2)
    {
        $cekfin = 0;

        // Cek jika ruangan tidak null dan bukan 'Tidak Membutuhkan Ruangan'
        if (!is_null($ruangan) and $ruangan != 5) {
            // Query 1: cek waktu akhir pengajuan vicon yang kemungkinan sama
            $cek1 = self::where('id_ruangan', $ruangan)
                ->where('tanggal', $tanggal)
                ->where('waktu', '<=', $waktu2)
                ->where('waktu2', '>=', $waktu2)
                ->count();

            // Query 2: cek waktu awal pengajuan vicon yang kemungkinan sama
            $cek2 = self::where('id_ruangan', $ruangan)
                ->where('tanggal', $tanggal)
                ->where('waktu', '<=', $waktu1)
                ->where('waktu2', '>=', $waktu1)
                ->count();

            // Query 3: cek waktu awal pengajuan vicon yang lebih awal dari waktu awal yang ada
            $cek3 = self::where('id_ruangan', $ruangan)
                ->where('tanggal', $tanggal)
                ->where('waktu', '>=', $waktu1)
                ->where('waktu2', '>=', $waktu1)
                ->count();

            // Jika jumlah baris = 1 maka dianggap tidak bermasalah (diubah jadi 0)
            $cek1 = $cek1 == 1 ? 0 : $cek1;
            $cek2 = $cek2 == 1 ? 0 : $cek2;
            $cek3 = $cek3 == 1 ? 0 : $cek3;

            // Jumlahkan total hasil cek
            $cekfin = $cek1 + $cek2 + $cek3;
        }

        return $cekfin;
    }

    // mirip dengan function cekctr namun menambahkan kondisi vicon sudah di approve
    public static function cekctr_approve($tanggal, $ruangan, $waktu1, $waktu2)
    {
        $cekfin = 0;

        // Cek jika ruangan tidak null dan bukan 'Tidak Membutuhkan Ruangan'
        if (!is_null($ruangan) and $ruangan != 99) {
            $cekfin =self::where('id_ruangan', $ruangan)
                    ->where('tanggal', $tanggal)
                    ->where(function ($q) use ($waktu1, $waktu2) {
                        return $q->where(function ($q2) use ($waktu1, $waktu2) {
                            return $q2->where('waktu', '<=', $waktu1)
                            ->where('waktu2', '>=', $waktu2);
                        })
                        ->orWhere(function ($q2) use ($waktu1, $waktu2) {
                            return  $q2->where('waktu', '>=', $waktu1)
                                ->where('waktu', '<=', $waktu2);
                        })
                        ->orWhere(function ($q2) use ($waktu1, $waktu2) {
                            return  $q2->where('waktu2', '>=', $waktu1)
                                ->where('waktu2', '<=', $waktu2);
                        });
                    })
                    ->where('status_approval', 1)
                    ->count();
        }

        return $cekfin;
    }

    public static function cekvicon_nama_waktu($acara, $tanggal, $waktu1, $waktu2)
    {
        $acara_v = str_replace("'", "", $acara);
        return self::where('acara', '=', DB::raw("BINARY '$acara_v'"))
            ->where('tanggal', $tanggal)
            ->where('waktu', '<=', $waktu2)
            ->where('waktu2', '>=', $waktu1)
            ->count();
    }

    //  Cek apakah ada pengajuan vicon yang sama pada tanggal, ruangan, dan waktu yang sama
    public static function cekvicon_nama_waktu_ruangan($acara, $tanggal, $waktu1, $waktu2, $ruangan)
    {
        $acara_v = str_replace("'", "", $acara);
        return self::where('acara', '=', DB::raw("BINARY '$acara_v'"))
            ->where('id_ruangan', $ruangan)
            ->where('tanggal', $tanggal)
            ->where('waktu', '<=', $waktu2)
            ->where('waktu2', '>=', $waktu1)
            ->count();
    }

    public static function cek_vicon_ruangan_waktu($ruangan, $tanggal, $waktu1, $waktu2)
    {
        $rows = 0;
        if (!is_null($ruangan) and $ruangan != 5) {
            $query = self::where('id_ruangan', '=', $ruangan)
                ->where('tanggal', '=', $tanggal)
                ->where('waktu', '<=', $waktu2)
                ->where('waktu2', '>=', $waktu1)
                ->count();
            $rows = $query;
        }

        return $rows;
    }

    public static function dataTables(Request $request)
    {
        $query = SendVicon::with(['ruangan', 'bagian', 'jenisrapat'])->orderBy('id', 'desc');

        // Filter berdasarkan tanggal awal
        if ($request->has('tanggal_awal') && $request->get('tanggal_awal')) {
            $tgl_awal = date('Y-m-d', strtotime($request->input('tanggal_awal')));
            $query->where('tanggal', '>=', $tgl_awal);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->has('tanggal_akhir') && $request->get('tanggal_akhir')) {
            $tgl_akhir = date('Y-m-d', strtotime($request->input('tanggal_akhir')));
            $query->where('tanggal', '<=', $tgl_akhir);
        }

        // Filter berdasarkan jenis rapat
        if ($request->has('jenisrapat') && $request->get('jenisrapat')) {
            $jenisrapat = $request->input('jenisrapat');
            $query->where('jenisrapat_id', $jenisrapat);
        }

        // Filter berdasarkan acara
        if ($request->has('acara') && $request->get('acara')) {
            $acara = str_replace("'", "", $request->input('acara'));
            $query->where('acara', 'LIKE', '%' . $acara . '%');
        }

        // Filter berdasarkan agenda direksi
        if ($request->has('agenda_direksi') && $request->get('agenda_direksi')) {
            $agenda_direksi = $request->input('agenda_direksi');
            $query->where('agenda_direksi', $agenda_direksi);
        }

        // Filter berdasarkan vicon
        if ($request->has('vicon') && $request->get('vicon')) {
            $vicon = $request->input('vicon');
            $query->where('vicon', $vicon);
        }

        // Filter berdasarkan bagian
        if ($request->has('bagian') && $request->get('bagian')) {
            $bagian = $request->input('bagian');
            if (!in_array("", $bagian)) {
                $query->whereIn('bagian_id', $bagian);
            }
        }

        // Filter berdasarkan status approval
        if ($request->input('status_approval') != '') {
            $status_approval = $request->input('status_approval');
            $query->where('status_approval', $status_approval);
        }

        // Searching
        if ($request->input('search.value')) {
            $search = $request->input('search.value');
            $approve_status = preg_match('/\b' . $request->input('search.value') . '\b/i', 'Waiting for Approve') ? 0 : (preg_match('/\b' . $request->input('search.value') . '\b/i', 'Approved') ? 1 : '');
            $query->where(function ($query) use ($search) {
                $query->orWhere('acara', 'LIKE', "%{$search}%")
                    ->orWhere('tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('waktu', 'LIKE', "%{$search}%")
                    ->orWhere('ruangan_lain', 'LIKE', "%{$search}%")
                    ->orWhere('vicon', 'LIKE', "%{$search}%")
                    ->orWhereHas('bagian', function ($query) use ($search) {
                        $query->where('master_bagian_nama', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('ruangan', function ($query) use ($search) {
                        $query->where('nama', 'LIKE', "%{$search}%");
                    });
            });

            if ($approve_status != '') {
                $query->orWhere('status_approval', $approve_status);
            }
        }

        $data = $query;

        return $data;
    }

    public static function download(Request $request)
    {
        $query = self::with(['ruangan', 'bagian', 'jenisrapat'])->orderBy('tanggal', 'desc')
            ->orderBy('waktu');

        // Filter berdasarkan bagian
        if ($request->has('bagian') && $request->get('bagian')) {
            $bagian = $request->input('bagian');
            if (!in_array("", $bagian)) {
                $query->whereIn('bagian_id', $bagian);
            }
        }

        // Filter berdasarkan acara
        if ($request->has('acara') && $request->get('acara')) {
            $acara = str_replace("'", "", $request->input('acara'));
            $query->where('acara', 'LIKE', '%' . $acara . '%');
        }

        // Filter berdasarkan agenda direksi
        if ($request->has('agenda_direksi') && $request->get('agenda_direksi')) {
            $agenda_direksi = $request->input('agenda_direksi');
            $query->where('agenda_direksi', $agenda_direksi);
        }

        // Filter berdasarkan vicon
        if ($request->has('vicon') && $request->get('vicon')) {
            $vicon = $request->input('vicon');
            $query->where('vicon', $vicon);
        }

        // Filter berdasarkan tanggal awal
        if ($request->has('tanggal_awal') && $request->get('tanggal_awal')) {
            $tgl_awal = date('Y-m-d', strtotime($request->input('tanggal_awal')));
            $query->where('tanggal', '>=', $tgl_awal);
        }

        // Filter berdasarkan tanggal akhir
        if ($request->has('tanggal_akhir') && $request->get('tanggal_akhir')) {
            $tgl_akhir = date('Y-m-d', strtotime($request->input('tanggal_akhir')));
            $query->where('tanggal', '<=', $tgl_akhir);
        }

        // Filter berdasarkan jenis rapat
        if ($request->has('jenisrapat') && $request->get('jenisrapat')) {
            $jenisrapat = $request->input('jenisrapat');
            $query->where('jenisrapat_id', $jenisrapat);
        }

        $data = $query->get();

        return $data;
    }

    public static function data(Request $request)
    {
        $query = self::with(['bagian', 'jenisrapat'])
            ->where('privat', 'Tidak')
            ->latest('tanggal');

        // Searching
        if ($request->input('search.value')) {
            $search = $request->input('search.value');
            $query->where(function ($query) use ($search) {
                $query->orWhere('acara', 'LIKE', "%{$search}%")
                    ->orWhereHas('bagian', function ($query) use ($search) {
                        $query->where('bagian', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('tanggal', 'LIKE', "%{$search}%")
                    ->orWhere('waktu', 'LIKE', "%{$search}%")
                    ->orWhere('ruangan', 'LIKE', "%{$search}%")
                    ->orWhere('vicon', 'LIKE', "%{$search}%")
                    ->orWhere('keterangan', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    public static function getSendviconAndRuanganById($id)
    {
        return self::with(['ruangan', 'masterLink', 'bagian', 'jenisrapat'])
            ->where('id', '=', $id)
            ->orderByDesc('tanggal')
            ->orderBy('waktu')
            ->get();
    }

    public static function getListNotifikasiSendvicon($user, $kolom_petugas, $kolom_persiapan)
    {
        // Mengambil semua sendvicon
        $sendvicons = self::all();

        // Array untuk menyimpan ID sendvicon yang sesuai dengan kriteria
        $id_sendvicon = [];

        foreach ($sendvicons as $sendvicon) {
            // Mengubah string petugas dan persiapan menjadi array
            $petugas = explode(", ", $sendvicon->$kolom_petugas);
            $persiapan = explode(", ", $sendvicon->$kolom_persiapan);

            // Cek apakah user termasuk dalam petugas dan tidak ada di persiapan
            if (in_array($user, $petugas) && !array_intersect($petugas, $persiapan)) {
                $id_sendvicon[] = $sendvicon->id;
            }
        }

        // Mengambil detail sendvicon berdasarkan ID yang ditemukan
        $notifikasi = Sendvicon::with(['ruangan', 'masterLink', 'bagian', 'jenisrapat'])
            ->whereIn('id', $id_sendvicon)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('waktu')
            ->get();

        return $notifikasi;
    }
}
