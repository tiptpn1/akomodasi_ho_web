<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class AgendaKendaraan extends Model
{
    use HasFactory;

    protected $table = 'agendakendaraan';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'id_kendaraan',
        'id_user',
        'tanggal',
        'id_bagian',
        'tujuan',
        'keterangan',
        'persiapan',
    ];

    protected $guarded = [
        'id'
    ];

    var $column_search = array('agendakendaraan.tanggal', 'agendakendaraan.tujuan', 'agendakendaraan.keterangan', 'kendaraan.no_polisi', 'user.username', 'bagian.bagian'); //set column field database for datatable searchable

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function bagian()
    {
        return $this->belongsTo(Bagian::class, 'id_bagian', 'id');
    }

    private function _get_datatables_query()
    {
        $query  = self::join('kendaraan', 'kendaraan.id', '=', 'agendakendaraan.id_kendaraan', 'inner')
            ->join('user', 'user.id', '=', 'agendakendaraan.id_user', 'inner')
            ->join('bagian', 'bagian.id', '=', 'agendakendaraan.id_bagian', 'inner')
            ->selectRaw('agendakendaraan.*, kendaraan.no_polisi as join_no_polisi, user.username as join_pengemudi, bagian.bagian as join_bagian');

        // custom filter
        if ($_POST['tanggal_awal']) {
            $tgl_awal = date('Y-m-d', strtotime($_POST['tanggal_awal']));
            Session::put('ken_tanggal_awal', $tgl_awal);

            $query->where('tanggal', '>=', $tgl_awal);
        } else {
            Session::remove('ken_tanggal_awal');
        }

        if (Session::has('ken_tanggal_awal')) {
            $session_tgl_awal = Session::get('ken_tanggal_awal');
            $query->where('tanggal', '>=', $session_tgl_awal);
        }

        // -----------------------------------------------------------------------------------
        if ($_POST['tanggal_akhir']) {
            $tgl_akhir = date('Y-m-d', strtotime($_POST['tanggal_akhir']));
            Session::put('ken_tanggal_akhir', $tgl_akhir);

            $query->where('tanggal', '<=', $tgl_akhir);
        } else {
            Session::remove('ken_tanggal_akhir');
        }

        if (Session::has('ken_tanggal_akhir')) {
            $session_tgl_akhir = Session::get('ken_tanggal_akhir');
            $query->where('tanggal', '<=', $session_tgl_akhir);
        }

        // -----------------------------------------------------------------------------------
        if ($_POST['kendaraan']) {
            $kendaraan = $_POST['kendaraan'];
            Session::put('ken_kendaraan', $kendaraan);

            $query->where('id_kendaraan', $kendaraan);
        } else {
            Session::remove('ken_kendaraan');
        }

        if (Session::has('ken_kendaraan')) {
            $session_kendaraan = Session::get('ken_kendaraan');
            $query->where('id_kendaraan', $session_kendaraan);
        }

        // -----------------------------------------------------------------------------------
        if ($_POST['pengemudi']) {
            $pengemudi = $_POST['pengemudi'];
            Session::put('ken_pengemudi', $pengemudi);

            $query->where('id_user', $pengemudi);
        } else {
            Session::remove('ken_pengemudi');
        }

        if (Session::has('ken_pengemudi')) {
            $session_pengemudi = Session::get('ken_pengemudi');
            $query->where('id_user', $session_pengemudi);
        }

        // -----------------------------------------------------------------------------------
        if ($_POST['tujuan']) {
            $tujuan = $_POST['tujuan'];
            Session::put('ken_tujuan', $tujuan);

            $query->where('tujuan', $tujuan);
        } else {
            Session::remove('ken_tujuan');
        }

        if (Session::has('ken_tujuan')) {
            $session_tujuan = Session::get('ken_tujuan');
            $query->where('tujuan', $session_tujuan);
        }

        // -----------------------------------------------------------------------------------
        if ($_POST['bagian']) {
            $bagian = $_POST['bagian'];
            if (!in_array("", $bagian)) {
                $query->whereIn('id_bagian', $bagian);
            }
            Session::put('ken_db_bagian', $bagian);
        } else {
            Session::remove('ken_db_bagian');
        }

        if (Session::has('ken_db_bagian')) {
            $session_db_bagian = Session::get('ken_db_bagian');
            if (!in_array("", $bagian)) {
                $query->whereIn('id_bagian', $session_db_bagian);
            }
        }

        $query->orderBy('tanggal', 'desc');

        if ($_POST['search']['value']) {
            $query->where(function ($q) {
                $i = 0;
                foreach ($this->column_search as $item) {
                    if ($i == 0) {
                        $q->where($item, 'like', '%' . $_POST['search']['value']  . '%');
                    } else {
                        $q->orWhere($item, 'like', '%' . $_POST['search']['value'] . '%');
                    }

                    $i++;
                }

                return $q;
            });
        }

        return $query;
    }

    function get_datatables()
    {
        $query = $this->_get_datatables_query();
        return $query->paginate($_POST['length'] ?? 10, page: ceil(($_POST['start'] + 1) / $_POST['length']));
    }
    function count_filtered()
    {
        $query = $this->_get_datatables_query();
        $query->get();
        return $query->count();
    }
    function count_all()
    {
        return self::join('kendaraan', 'kendaraan.id', '=', 'agendakendaraan.id_kendaraan', 'inner')
                ->join('user', 'user.id', '=', 'agendakendaraan.id_user', 'inner')
                ->join('bagian', 'bagian.id', '=', 'agendakendaraan.id_bagian', 'inner')
                ->count();
    }
    // end datatables


    public function get_list_notifikasi_agendakendaraan($id_user, $kolom_petugas, $kolom_persiapan)
    {
        $query = DB::table('agendakendaraan')
            ->selectRaw('agendakendaraan.*, kendaraan.no_polisi as join_no_polisi, user.username as join_pengemudi, bagian.bagian as join_bagian')
            ->join('kendaraan', 'kendaraan.id', 'agendakendaraan.id_kendaraan')
            ->join('user', 'user.id', 'agendakendaraan.id_user')
            ->join('bagian', 'bagian.id', 'agendakendaraan.id_bagian')
            ->whereRaw("$kolom_petugas = '$id_user' AND ($kolom_persiapan != 'ready' OR $kolom_persiapan IS NULL) ORDER BY tanggal DESC")
            ->get();
        return $query;
    }


    // untuk helper notifikasi
    public function get_list_date_agendakendaraan($date)
    {
        $query = DB::table('agendakendaraan')
            ->selectRaw('agendakendaraan.*, kendaraan.no_polisi as join_no_polisi, user.username as join_pengemudi, bagian.bagian as join_bagian')
            ->join('kendaraan', 'kendaraan.id = agendakendaraan.id_kendaraan')
            ->join('user', 'user.id = agendakendaraan.id_user')
            ->join('bagian', 'bagian.id = agendakendaraan.id_bagian')
            ->whereRaw("tanggal = '$date'")
            ->get();
        return $query;
    }

    // untuk helper notifikasi
    public function get_list_agendakendaraan_where($where)
    {
        $query = self::join('kendaraan', 'kendaraan.id', 'agendakendaraan.id_kendaraan')
            ->join('user', 'user.id', 'agendakendaraan.id_user')
            ->join('bagian', 'bagian.id', 'agendakendaraan.id_bagian')
            ->selectRaw('agendakendaraan.*, kendaraan.no_polisi as join_no_polisi, user.username as join_pengemudi, bagian.bagian as join_bagian')
            ->whereRaw($where)
            ->get();
        return $query;
    }

    // sql query insert data
    public function input_data($data)
    {
        $insert_id = self::insertGetId($data);

        return  $insert_id;
    }


    // tampilkan seluruh data pada sebuah tabel
    public function tampilall()
    {
        $query = self::join('kendaraan', 'kendaraan.id', 'agendakendaraan.id_kendaraan')
            ->join('user', 'user.id', 'agendakendaraan.id_user')
            ->join('bagian', 'bagian.id', 'agendakendaraan.id_bagian')
            ->selectRaw('agendakendaraan.*, kendaraan.no_polisi as join_no_polisi, user.username as join_pengemudi, bagian.bagian as join_bagian')
            ->orderBy('tanggal', 'desc')
            ->get();
        return $query;
    }

    // tampilkan data pada sebuah tabel dengan kondisi 'where'
    public function tampil($where)
    {
        $query = self::join('kendaraan', 'kendaraan.id', 'agendakendaraan.id_kendaraan')
            ->join('user', 'user.id', 'agendakendaraan.id_user')
            ->join('bagian', 'bagian.id', 'agendakendaraan.id_bagian')
            ->selectRaw('agendakendaraan.*, kendaraan.no_polisi as join_no_polisi, user.username as join_pengemudi, bagian.bagian as join_bagian')
            ->whereRaw($where)
            ->orderBy('tanggal', 'desc')
            ->get();
        return $query;
    }

    // sql query delete data dari sebuah tabel dengan kondisi 'where'
    public function hapusdata($where)
    {
        self::whereRaw($where)->delete();
    }


    /*
    cek kesediaan kendaraan pada tanggal tertentu
    */
    public function cek_sedia_kendaraan($tanggal, $nopol)
    {
        // cek kendaraan pada hari yang sama
        $query1 = self::where('tanggal', $tanggal)->where('id_kendaraan', $nopol);

        // jumlah baris data yang cocok dengan query
        $cek = $query1->count();
        return $cek;
    }


    /*
    cek kesediaan pengemudi pada tanggal tertentu
    */
    public function cek_sedia_pengemudi($tanggal, $pengemudi)
    {
        // cek pengemudi pada hari yang sama
        $query = self::where('tanggal', $tanggal)->where('id_user', $pengemudi);

        // jumlah baris data yang cocok dengan query
        $cek = $query->count();
        return $cek;
    }


    // fungsi update data pada tabel dengan kondisi 'where'
    public function updatedata($where, $data, $table)
    {
        self::where($where)->update($data);
    }

    /*
    petugasti = status booked dengan waktu sampai belum hari ini
    selain petugasti = status booked dengan waktu sampai belum hari ini
    status expired apabila telah melalui hari ini
    */
    public function autoupdate()
    {
        date_default_timezone_set("Asia/Jakarta");
        $tglskrg = date('Y-m-d');
        $waktu = date('H:i:s');
        DB::query("UPDATE sendvicon SET status='Booked' WHERE status != 'Cancel' and petugasti='' or petugasti is null");
        DB::query("UPDATE sendvicon SET status='Confirm' WHERE status != 'Cancel' and petugasti!='' and petugasti is not null and tanggal>='$tglskrg'");
        DB::query("UPDATE sendvicon SET status='Expired' WHERE status != 'Cancel' and tanggal <= '$tglskrg' AND waktu <= '$waktu' AND status = 'Confirm'");
        DB::query("UPDATE sendvicon SET status='Expired' WHERE status != 'Cancel' and tanggal <= '$tglskrg' AND waktu <= '$waktu' AND status = 'Booked'");
    }
}
