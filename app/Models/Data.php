<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class Data extends Model
{
    private function _get_datatables_query($request)
    {
        // custom filter
        if ($request->has('tanggal_awal')) {
            $tgl_awal = date('Y-m-d', strtotime($request->input('tanggal_awal')));
            Session::put('filter_tanggal_awal', $tgl_awal);

            $this->db->where('tanggal >=', $tgl_awal);
        }
        if (Session::has('filter_tanggal_awal')) {
            $session_tgl_awal = Session::get('filter_tanggal_awal');
            $this->db->where('tanggal >=', $session_tgl_awal);
        }
        // ------------------------------------------------------------------------------
        if ($request->has('tanggal_akhir')) {
            $tgl_akhir = date('Y-m-d', strtotime($request->input('tanggal_akhir')));
            Session::put('filter_tanggal_akhir', $tgl_akhir);

            $this->db->where('tanggal <=', $tgl_akhir);
        }
        if (Session::has('filter_tanggal_akhir')) {
            $session_tgl_akhir = Session::get('filter_tanggal_akhir');
            $this->db->where('tanggal <=', $session_tgl_akhir);
        }
        // ------------------------------------------------------------------------------
        if ($request->has('jenisrapat')) {
            $jenisrapat = $request->input('jenisrapat');
            Session::put('filter_jenisrapat', $jenisrapat);

            $this->db->where('jenisrapat', $jenisrapat);
        }
        if (Session::has('filter_jenisrapat')) {
            $session_jenisrapat = Session::get('filter_jenisrapat');
            $this->db->where('jenisrapat', $session_jenisrapat);
        }
        // ------------------------------------------------------------------------------
        if ($request->has('acara')) {
            $acara = $request->input('acara');
            $acara_v = str_replace("'", "", $acara);
            Session::put('filter_acara', $acara);

            $this->db->where("acara LIKE '%$acara_v%'");
        }
        if (Session::has('filter_acara')) {
            $session_acara = Session::get('filter_acara');
            $session_acara_v = str_replace("'", "", $session_acara);
            $this->db->where("acara LIKE '%$session_acara_v%'");
        }
        // ------------------------------------------------------------------------------
        if ($request->has('agenda_direksi')) {
            $agenda_direksi = $request->input('agenda_direksi');
            Session::put('filter_agenda_direksi', $agenda_direksi);

            $this->db->where('agenda_direksi', $agenda_direksi);
        }
        if (Session::has('filter_agenda_direksi')) {
            $session_agenda_direksi = Session::get('filter_agenda_direksi');
            $this->db->where('agenda_direksi', $session_agenda_direksi);
        }
        // ------------------------------------------------------------------------------
        if ($request->has('vicon')) {
            $vicon = $request->input('vicon');
            Session::put('filter_vicon', $vicon);

            $this->db->where('vicon', $vicon);
        }
        if (Session::has('filter_vicon')) {
            $session_vicon = Session::get('filter_vicon');
            $this->db->where('vicon', $session_vicon);
        }
        // ------------------------------------------------------------------------------
        if ($request->has('bagian')) {
            $bagian = $request->input('bagian');
            $db_bagian = implode("','", $bagian);
            if (!in_array("", $bagian)) {
                $this->db->where("bagian IN ('$db_bagian')");
            }
            Session::put('filter_db_bagian', $db_bagian);
        }
        if (Session::has('filter_db_bagian')) {
            $session_db_bagian = Session::get('filter_db_bagian');
            $this->db->where("bagian IN ('$session_db_bagian')");
        }

        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->orderBy('tanggal', 'desc');
        $this->db->orderBy('waktu');

        $i = 0;
        foreach ($this->column_search as $item) { // loop column
            if (@$_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
    }
    function get_datatables(Request $request)
    {
        $this->_get_datatables_query($request);
        $this->db->limit(10, @$_POST['start']);
        $query = $this->db->get();
        return $query;
    }
    function count_filtered(Request $request)
    {
        $this->_get_datatables_query($request);
        $query = $this->db->get();
        return $query->count();
    }
    function count_all()
    {
        $this->db->from('sendvicon');
        return $this->db->count_all_results();
    }


    private function _get_datatables_query_dashboard()
    {
        // custom filter
        $date_now = date('Y-m-d');
        $this->db->where("tanggal = '$date_now'");

        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');

        $this->db->orderBy('tanggal', 'desc');
        $this->db->orderBy('waktu');

        $i = 0;
        foreach ($this->column_search as $item) { // loop column
            if (@$_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
    }
    function get_datatables_dashboard()
    {
        $this->_get_datatables_query_dashboard();
        $this->db->limit(10, @$_POST['start']);
        $query = $this->db->get();
        return $query;
    }
    function count_filtered_dashboard()
    {
        $this->_get_datatables_query_dashboard();
        $query = $this->db->get();
        return $query->count();
    }
    function count_all_dashboard()
    {
        $this->db->from('sendvicon');
        return $this->db->count_all_results();
    }
    // end datatables agenda vicon

    function get_to_notif($today, $time_notif)
    {
        return $this->db->select('*')
            ->from('sendvicon')
            ->where('tanggal', $today)
            ->where('waktu', $time_notif)
            ->where('is_reminded !=', 1)
            ->get()->result();
    }

    public function mark_reminded($id)
    {
        return $this->db->where('id', $id)->update(
            'sendvicon',
            array('is_reminded' => 1)
        );
    }

    // cek apakah ada id di table
    public function cek_id($table, $field, $id)
    {
        $query = DB::table($table)
            ->where($field, $id)
            ->count();

        return $query;
    }

    // untuk helper notifikasi agenda vicon petugas
    public function get_list_notifikasi_sendvicon($user, $kolom_petugas, $kolom_persiapan)
    {
        $this->db->select('*');
        $this->db->from('sendvicon');
        $query_cek = $this->db->get();

        $id_sendvicon = array();
        foreach ($query_cek->result() as $result) {
            $petugas = explode(", ", $result->$kolom_petugas);
            $persiapan = explode(", ", $result->$kolom_persiapan);

            if (!empty($petugas)) {
                foreach ($petugas as $k => $value) {
                    if ($petugas[$k] == $user and !in_array($petugas[$k], $persiapan)) {
                        $id_sendvicon[] = $result->id;
                    }
                }
            }
        }
        // print_r($id_sendvicon).'//';
        // die();

        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan, masterlink.link as join_link');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->join('masterlink', 'masterlink.namalink = sendvicon.link', 'left');
        $this->db->where("sendvicon.id IN ('" . implode("','", $id_sendvicon) . "') ORDER BY 'tanggal' DESC, 'waktu'");
        $query = $this->db->get();
        return $query;
    }

    // untuk helper notifikasi vicon today
    public function get_list_date_sendvicon($date)
    {
        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan, masterlink.link as join_link');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->join('masterlink', 'masterlink.namalink = sendvicon.link', 'left');
        $this->db->where("sendvicon.tanggal = '$date' ORDER BY 'waktu'");
        $query = $this->db->get();
        return $query;
    }

    // untuk helper notifikasi vicon today
    public function get_list_notif_sendvicon($date)
    {
        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan, masterlink.link as join_link');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->join('masterlink', 'masterlink.namalink = sendvicon.link', 'left');
        $this->db->where("sendvicon.tanggal = '$date' AND sendvicon.status != 'Expired' AND sendvicon.status != 'Cancel' ORDER BY 'waktu'");
        $query = $this->db->get();
        return $query;
    }

    // untuk dashboard satu kartu 7 hari (1 minggu)
    // public function get_dashboard_list($limit, $start){
    //     $query = $this->db->get_where('dashboard', "tanggal_awal != '' order by tanggal_akhir DESC", $limit, $start);
    //     return $query;
    // }

    // untuk dashboard 7 hari terkahir
    // public function get_distinct_between($table, $field_distinct, $start_between, $end_between){
    //     $this->db->select('*');
    //     $this->db->group_by($field_distinct);
    //     $this->db->orderBy('tanggal', 'DESC');
    //     $query = $this->db->get_where($table, "tanggal BETWEEN '$start_between' AND '$end_between'");

    //     return $query;
    // }

    public function get_distinct($table, $field_distinct1)
    {
        $this->db->select('tanggal');
        $this->db->group_by($field_distinct1);
        $query = $this->db->get($table);
        return $query;
    }

    public function get_distinct_dashboard($field_distinct1, $limit, $start)
    {
        $this->db->select('tanggal');
        $this->db->group_by($field_distinct1);
        $this->db->orderBy('tanggal', 'DESC');
        $query = $this->db->get('sendvicon', $limit, $start);
        return $query;
    }

    public function get_user_data($id = null)
    {
        $this->db->from("master_user");
        if ($id != null) {
            $this->db->where('master_user_id', $id);
        }
        return $this->db->get();
    }

    // sql query insert data
    public function input_data($data, $table)
    {
        $insert_id = DB::table($table)->insertGetId($data);

        return  $insert_id;
    }

    // get data sendvicon dengan kondisi 'where'
    public function get_absensi_where($where)
    {
        $this->db->select('absensi.*, sendvicon.acara as join_rapat');
        $this->db->from('absensi');
        $this->db->join('sendvicon', 'sendvicon.id = absensi.id_rapat', 'left');
        $this->db->where($where);
        $this->db->orderBy('absensi.created', 'desc');

        $query = $this->db->get();
        return $query;
    }

    // get data sendvicon dengan kondisi 'where'
    public function get_sendvicon_where($where)
    {
        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->where($where);
        $this->db->orderBy('sendvicon.tanggal', 'desc');
        $this->db->orderBy('waktu');

        $query = $this->db->get();
        return $query;
    }

    // get data sendvicon dengan id
    public function get_sendvicon_by_id($id)
    {
        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan, masterlink.link as join_link');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->join('masterlink', 'sendvicon.link = masterlink.namalink', 'left');
        $this->db->where("sendvicon.id = '$id'");
        $query = $this->db->get();
        return $query->row();
    }

    // get data sendvicon dengan 'limit'
    public function get_sendvicon_limit($limit)
    {
        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->orderBy('sendvicon.tanggal', 'desc');
        $this->db->orderBy('waktu');
        $this->db->limit($limit);

        $query = $this->db->get();
        return $query;
    }

    // get seluruh data sendvicon
    public function get_sendvicon_all()
    {
        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->orderBy('sendvicon.tanggal', 'desc');
        $this->db->orderBy('waktu');

        $query = $this->db->get();
        return $query;
    }

    // get data sendvicon dengan kondisi 'where'
    public function get_exportvicon_where($where)
    {
        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->where($where);
        $this->db->orderBy('sendvicon.tanggal');
        $this->db->orderBy('waktu');

        $query = $this->db->get();
        return $query;
    }

    // get seluruh data sendvicon
    public function get_exportvicon_all()
    {
        $this->db->select('sendvicon.*, ruangan.nama as join_ruangan');
        $this->db->from('sendvicon');
        $this->db->join('ruangan', 'ruangan.id = sendvicon.id_ruangan', 'left');
        $this->db->orderBy('sendvicon.tanggal');
        $this->db->orderBy('waktu');

        $query = $this->db->get();
        return $query;
    }

    // tampilkan seluruh data pada sebuah tabel
    public function tampilall($table)
    {
        $query = DB::table($table)
            ->select('*')
            ->orderBy('id', 'DESC')
            ->get();
        return $query;
    }
    // tampilkan seluruh data pada sebuah tabel master_user
    public function tampilalluser($table)
    {
        $query = DB::table($table)
            ->select('*')
            ->orderBy('master_user_id', 'DESC')
            ->get();
        return $query;
    }

    public function getActiveBagian()
    {
        $query = DB::table('master_bagian')
            ->select("*")
            ->where('is_active', '1')
            ->orderBy('master_bagian_id', 'DESC')
            ->get();
        return $query;
    }
    public function getActiveHakAkses()
    {
        $query = DB::table('master_hak_akses')
            ->select("*")
            ->where('status', '1')
            ->orderBy('hak_akses_id', 'DESC')
            ->get();
        return $query;
    }

    // tampilkan data pada sebuah tabel dengan kondisi 'where'
    public function tampil($table, $where)
    {
        $query = DB::table($table)
            ->select('*')
            ->whereRaw($where)
            ->get();
        return $query;
    }

    // sql query delete data dari sebuah tabel dengan kondisi 'where'
    public function hapusdata($where, $table)
    {
        DB::table($table)
            ->where($where)
            ->delete();
    }

    public function last_record($table)
    {
        $last = DB::table($table)
            ->orderBy('id', "desc")
            ->first();

        return $last;
    }

    // dapatkan link dari namalink
    public function get_link_by_name($name)
    {
        $query = DB::table("masterlink")
            ->select("*")
            ->where("namalink = '$name'")
            ->first();
        return $query;
    }

    /*
    cek jumlah data pada tabel dengan kondisi
    tanggal, ruangan, waktu mulai, waktu akhir dan bagian
    yang mengajukan vicon pada bagian yang berbeda
    */
    public function cek($tanggal, $ruangan, $waktu1, $waktu2, $bagian)
    {
        $query = DB::query("SELECT * FROM sendvicon WHERE bagian!=? AND ruangan=? AND tanggal=? AND waktu>=? AND waktu2<=?", [$bagian, $ruangan, $tanggal, $waktu1, $waktu2]);
        return $query->count();
    }

    /*
    cek ruangan, tanggal, awal dan akhir waktu untuk pengajuan vicon
    pada bagian yang sama dan berbeda
    */
    public function cekctr2($tanggal, $ruangan, $waktu1, $waktu2, $bagian)
    {
        // cek waktu akhir vicon dengan kemungkinan telah terboking
        $query1 = DB::query("SELECT * FROM sendvicon WHERE
			bagian!='$bagian' AND ruangan='$ruangan' AND tanggal='$tanggal' AND
            '$waktu2'>=waktu AND '$waktu2'<=waktu2");
        // cek waktu awal vicon dengan kemungkinan telah terboking
        $query2 = DB::query("SELECT * FROM sendvicon WHERE
			bagian!='$bagian' AND ruangan='$ruangan' AND tanggal='$tanggal' AND
            '$waktu1'<=waktu2 AND '$waktu1'>=waktu");
        // sama dengan query2
        $query3 = DB::query("SELECT * FROM sendvicon WHERE
			bagian!='$bagian' AND ruangan='$ruangan' AND tanggal='$tanggal' AND
            '$waktu1'<=waktu2 AND '$waktu1'>=waktu");
        // cek waktu akhir vicon dengan kemungkinan telah terboking untuk bagian yang sama
        $query4 = DB::query("SELECT * FROM sendvicon WHERE
            bagian='$bagian' AND ruangan='$ruangan' AND tanggal='$tanggal' AND
            '$waktu2'>=waktu AND '$waktu2'<=waktu2 ");
        // cek waktu awal vicon dengan kemungkinan telah terboking untuk bagian yang sama
        $query5 = DB::query("SELECT * FROM sendvicon WHERE
            bagian='$bagian' AND ruangan='$ruangan' AND tanggal='$tanggal' AND
            '$waktu1'<=waktu2 AND '$waktu1'>=waktu");
        // sama dengan query5
        $query6 = DB::query("SELECT * FROM sendvicon WHERE
            bagian='$bagian' AND ruangan='$ruangan' AND tanggal='$tanggal' AND
            '$waktu1'<=waktu2 AND '$waktu1'>=waktu");

        // jumlah baris data yang cocok dengan query
        $cek1 = $query1->count();
        $cek2 = $query2->count();
        $cek3 = $query3->count();
        $cek4 = $query4->count();
        $cek5 = $query5->count();
        $cek6 = $query6->count();

        /*
        kondisi awal dan akhir waktu pengajuan vicon yang memiliki kemungkinan sama
        untuk bagian yang sama = 2 baris data dijadikan 0 (dianggap tidak bermasalah)
        */
        if ($cek4 == 2) {
            $cek4 = 0;
        }
        if ($cek5 == 2) {
            $cek5 = 0;
        }
        if ($cek6 == 2) {
            $cek6 = 0;
        }
        $cekfin = $cek1 + $cek2 + $cek3 + $cek4 + $cek5 + $cek6;
        return $cekfin;
    }

    /*
    cek ruangan, tanggal, awal dan akhir waktu untuk pengajuan vicon
    */
    public function cekctr($tanggal, $ruangan, $waktu1, $waktu2, $bagian)
    {
        $cekfin = 0;

        // cek apabila ada post ruangan dan ruangan yang dipilih buka 'Tidak Membutuhkan Ruangan'
        if (!is_null($ruangan) and $ruangan != 5) {
            // cek waktu akhir pengajuan vicon yang kemungkinan sama
            $query1 = DB::query("SELECT * FROM sendvicon WHERE
                id_ruangan='$ruangan' AND tanggal='$tanggal' AND
                '$waktu2'>=waktu AND '$waktu2'<=waktu2");
            // cek waktu awal pengajuan vicon yang kemungkinan sama
            $query2 = DB::query("SELECT * FROM sendvicon WHERE
                id_ruangan='$ruangan' AND tanggal='$tanggal' AND
                '$waktu1'<=waktu2 AND '$waktu1'>=waktu");
            // cek waktu awal pengajuan vicon yang lebih awal dari waktu awal yang ada
            // dan lebih awal dari waktu akhir yang ada
            $query3 = DB::query("SELECT * FROM sendvicon WHERE
                id_ruangan='$ruangan' AND tanggal='$tanggal' AND
                '$waktu1'<=waktu2 AND '$waktu1'<=waktu");

            // jumlah baris data yang cocok dengan query
            $cek1 = $query1->count();
            $cek2 = $query2->count();
            $cek3 = $query2->count();

            // klau jumlah baris = 1 maka ubah menjadi 0 (tidak bermasalah)
            // jika tidak maka sesuai dengan jumlah baris data
            $cek1 = $cek1 == 1 ? 0 : $query1->count();
            $cek2 = $cek2 == 1 ? 0 : $query2->count();
            $cek3 = $cek3 == 1 ? 0 : $query2->count();

            $cekfin = $cek1 + $cek2 + $cek3;
        }

        return $cekfin;
    }

    public function cek_vicon_ruangan_waktu($ruangan, $tanggal, $waktu1, $waktu2)
    {
        $rows = 0;
        if (!is_null($ruangan) and $ruangan != 5) {
            $query = DB::query("SELECT * FROM sendvicon WHERE id_ruangan = $ruangan AND tanggal='$tanggal' AND waktu<='$waktu2' AND waktu2>='$waktu1'");
            $rows = $query->count();
        }

        return $rows;
    }

    // fungsi cek nama acara vicon sama pada waktu tertentu
    public function cekvicon_nama_waktu($acara, $tanggal, $waktu1, $waktu2)
    {
        $acara_v = str_replace("'", "", $acara);
        $query = DB::query("SELECT * FROM sendvicon WHERE acara LIKE BINARY '$acara_v' AND tanggal='$tanggal' AND waktu<='$waktu2' AND waktu2>='$waktu1'");
        return $query->count();
    }

    // cek ruangan yang tersedia pada tanggal dan waktu
    public function cekvicon_ruangan_waktu($tanggal, $waktu1, $waktu2)
    {
        $query = DB::query("SELECT * FROM ruangan WHERE id NOT IN (SELECT id_ruangan FROM sendvicon WHERE tanggal = '$tanggal' AND waktu>='$waktu1' AND waktu2<='$waktu2' AND id_ruangan IS NOT NULL) AND status = 'Aktif' AND id != '5'");
        return $query;
    }

    public function updatestatus_absensi($id)
    {
        DB::query("UPDATE sendvicon SET status_absensi='Closed' WHERE id = '$id'");
    }

    public function nullstatus_absensi($id)
    {
        DB::query("UPDATE sendvicon SET status_absensi = NULL WHERE id = '$id'");
    }

    public function open_absensi($id)
    {
        DB::query("UPDATE sendvicon SET status_absensi='Open' WHERE id = '$id'");
    }

    public function cek_duplicate_absensi($id_rapat, $nama, $jabatan, $instansi)
    {
        $query = DB::query("SELECT * FROM absensi WHERE id_rapat = '$id_rapat' AND nama = '$nama' AND jabatan ='$jabatan' AND instansi = '$instansi'");
        return $query->count();
    }

    // fungsi update data pada tabel dengan kondisi 'where'
    public function updatedata($where, $data, $table)
    {
        DB::table($table)
            ->whereRaw($where)
            ->update($data);
    }

    // sql query untuk validasi bahwa data terdapat di tabel user
    public function check_login($dataemail, $datapassword)
    {
        $query = DB::query("SELECT * FROM master_user where master_user_nama = '" . $dataemail . "' and master_user_password = '" . $datapassword . "'");
        return $query->count();
    }

    // sql query untuk mendapat data dari tabel dengan kondisi 'where'
    public function find($tabel, $data)
    {
        $query = DB::table($tabel)->select("*")->where($data)->get();
        return $query;
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

        DB::query("UPDATE sendvicon SET status='Booked' WHERE status != 'Cancel' and (petugasti='' or petugasti is null)");
        DB::query("UPDATE sendvicon SET status='Confirm' WHERE status != 'Cancel' and petugasti!='' and petugasti is not null and tanggal>='$tglskrg'");
        DB::query("UPDATE sendvicon SET status='Expired' WHERE status != 'Cancel' and tanggal <= '$tglskrg' AND waktu <= '$waktu' AND status = 'Confirm'");
        DB::query("UPDATE sendvicon SET status='Expired' WHERE status != 'Cancel' and tanggal <= '$tglskrg' AND waktu <= '$waktu' AND status = 'Booked'");
    }


    public function autoinsert()
    {
        date_default_timezone_set("Asia/Jakarta");

        // SELECT MAX(tanggal_akhir) FROM dashboard
        $this->db->select_max('tanggal_akhir');
        $query = $this->db->get('dashboard');
        $bulan = array(
            1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );

        foreach ($query as $ta) {

            // get range date for next week
            $next_week_start = strtotime('1 days', strtotime($ta->tanggal_akhir));
            $next_week_end = strtotime('7 days', strtotime($ta->tanggal_akhir));

            $tanggal_awal_in = date('Y-m-d', $next_week_start);
            $tanggal_akhir_in = date('Y-m-d', $next_week_end);

            // set data to insert
            $today = strtotime(date('Y-m-d'));
            // untuk mendapatkan minggu dari bulan tertentu
            $firstOfMonth = date("Y-m-01", $next_week_end);
            $week = intval(date("W", $next_week_end)) - intval(date("W", strtotime($firstOfMonth)));
            $month = date("F", $next_week_end);
            // dapatkan bulan dalam bahasa indonesia
            $get_month = explode('-', $tanggal_akhir_in);
            $month = $bulan[(int)$get_month[1]];

            $year = date("Y", $next_week_end);

            $data = array(
                'tanggal_awal' => $tanggal_awal_in,
                'tanggal_akhir' => $tanggal_akhir_in,
                'week' => $week,
                'month' => $month,
                'year' => $year
            );

            if ($today > $next_week_end) {
                $this->db->insert('dashboard', $data);
            }
        }
    }
}
