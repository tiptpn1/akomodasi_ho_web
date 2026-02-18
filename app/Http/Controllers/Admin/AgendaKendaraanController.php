<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgendaKendaraan;
use App\Models\Bagian;
use App\Models\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AgendaKendaraanController extends Controller
{
    var $model, $m_data;

    public function __construct()
    {
        $this->model = new AgendaKendaraan();
        $this->m_data = new Data();
    }
    function getToken($id, $message, $title, $acara, $tanggal, $jam)
    {

        $fb = Firebase::initialize("https://akomodasiptpnxii-default-rtdb.firebaseio.com", "ySHg8LRopxfDnxnkR7WzGIeewwPJ7XPJNHHRMXtU");

        $user = $this->m_data->tampilall('user');
        $username = "";
        foreach ($user as $pengemudi) {
            if ($pengemudi->id == $id) {
                $username = $pengemudi->username;
            }
        }

        //retrieve a node
        $token = $fb->get('/Tokens/' . $username);
        // print_r($username);
        // die();
        if (!empty($token) and !is_null($token)) {
            $this->sendNotification($token['token'], $message, $title, $acara, $tanggal, $jam);
        }
    }

    function sendNotification($token, $message, $title, $acara, $tanggal, $jam)
    {
        $url = "https://fcm.googleapis.com/fcm/send";

        // print_r($token);
        // die();

        $fields = array(
            "to" => $token,
            "data" => array(
                "Message" => $message,
                "Title" => $title,
                "Acara" => $acara,
                "Tanggal" => $tanggal,
                "Jam" => $jam
            )
        );

        $headers = array(
            'Authorization: key=AAAAbe02zb4:APA91bFFmQgO6KdLwaHdEpiFJOqCGRHEyXWtb1mF5wTWqHF6Og-CGjOhjDuberORScrJXZUgnQuPC6IutgNheffG-hSH6aPMvXsg1JGf2HsXQ7NjxyG_3rXa4B40bH8BIigjQcOA0uuX',
            'Content-Type:application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_exec($ch);
        curl_close($ch);
    }

    function get_ajax()
    {
        $list = $this->model->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $item) {
            $no++;
            $gettgl = explode('-', $item->tanggal);
            $th = $gettgl[0];
            $d = $gettgl[2];
            $bln = $gettgl[1];
            $settgl = "$bln/$d/$th";
            $settgl_ = "$d-$bln-$th";

            $gettanggal = $item->tanggal;
            $no_polisi = $item->id_kendaraan;
            $pengemudi = $item->id_user;

            $cek1 = '0,';
            $cek2 = '0,';
            $cekkendaraan = $this->model->cek_sedia_kendaraan($gettanggal, $no_polisi);
            $cekpengemudi = $this->model->cek_sedia_pengemudi($gettanggal, $pengemudi);

            if ($cekkendaraan > 1) {
                $cek1 = "1,";
            }
            if ($cekpengemudi > 1) {
                $cek2 = "1,";
            }

            $row = array();
            $row[] = $no . ".";
            $row[] = $cek1 . $item->join_no_polisi;
            $row[] = $cek2 . $item->join_pengemudi;
            $row[] = $settgl_;
            $row[] = $item->join_bagian;
            $row[] = $item->tujuan;
            $row[] = $item->keterangan;

            if (auth()->user()->role != 'Read Only') {
                $row[] = '<center>
                        <button style="margin-right: 6px; margin-bottom: 3px;" class="btn btn-warning btn-sm btn-edit" data-id="' . $item->id . '" data-nopol="' . $no_polisi . '" data-driver="' . $pengemudi . '" data-tanggal="' . $settgl_ . '" data-bagian="' . $item->id_bagian . '" data-tujuan="'. $item->tujuan .'" data-keterangan="' . $item->keterangan . '">edit
                        <center>
                        <button style="margin-right: 6px; margin-bottom: 3px;" class="btn btn-sm btn-danger" onclick="hapus(' . "'" . $item->id . "'" . ');">hapus';
            }


            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->model->count_all(),
            "recordsFiltered" => $list->total(),
            "data" => $data,
            'starts' => $_POST['start'],
            'lengths' => $_POST['length'],
            'pageCalculate' => ceil($_POST['start'] / $_POST['length']),
        );

        return response()->json($output);
    }

    public function index()
    {
        // $this->model->autoupdate();
        // $view = $model->tampilall('agendakendaraan');

        $data = array(
            "title"     => "Pemesanan Layanan Video Conference",
            "halaman"   =>  "Dashboard",
            "linkhalaman"   =>  "",
            // "view" => $view,
            "model" => new Data(),
            "bagian" => new Bagian(),
        );

        return view('admin.agendakendaraan.index', $data);
    }

    public function reset_filter()
    {
        if (isset($_SESSION['ken_tanggal_awal'])) {
            Session::remove('ken_tanggal_awal');
        }
        if (isset($_SESSION['ken_tanggal_akhir'])) {
            Session::remove('ken_tanggal_akhir');
        }
        if (isset($_SESSION['ken_kendaraan'])) {
            Session::remove('ken_kendaraan');
        }
        if (isset($_SESSION['ken_pengemudi'])) {
            Session::remove('ken_pengemudi');
        }
        if (isset($_SESSION['ken_tujuan'])) {
            Session::remove('ken_tujuan');
        }
        if (isset($_SESSION['ken_db_bagian'])) {
            Session::remove('ken_db_bagian');
        }

        return redirect(route('admin.dashboard.kendaraan.show'));
    }

    public function addagendakendaraan(Request $request)
    {
        $no_polisi = $request->no_polisi;
        $pengemudi = $request->pengemudi;
        $join_pengemudi = $request->join_pengemudi;
        $tanggal = $request->tanggal;
        // pisahkan tanggal awal - tanggal akhir
        $settgl = explode(' ', $tanggal);
        $tgl1 = $settgl[0];
        $tgl2 = $settgl[2];
        // konfersi waktu ke timestamp -> tgl awal - tgl akhir
        $startTime = strtotime($tgl1);
        $endTime = strtotime($tgl2);

        $bagian = $request->bagian;
        $tujuan = $request->tujuan;
        $keterangan = $request->keterangan;

        $data_session = array();

        // looping interval timestamp per satu hari
        for ($i = $startTime; $i <= $endTime; $i = $i + 86400) {
            $eventdate = date('Y-m-d', $i);
            $data = array(
                'id_kendaraan' => $no_polisi,
                'id_user' => $pengemudi,
                'tanggal' => $eventdate,
                'id_bagian' => $bagian,
                'tujuan' => $tujuan,
                'persiapan' => '',
                'keterangan' => $keterangan
            );
            $id_agendakendaraan = $this->model->input_data($data);
            // $this->sendNotification("/topics/sendvicon", "tambah agenda", "Notifikasi Agenda Kendaraan Baru", $tujuan, $eventdate, $keterangan);
            // if (!empty($pengemudi)) {
            //     $this->getToken($pengemudi, "petugas driver", "Notifikasi Petugas Driver", $tujuan, $eventdate, $keterangan);
            // }

            $data_session[] = $id_agendakendaraan;
            Session::put(
                array('id_agendakendaraan' => $data_session)
            );

            $dipakai = $this->model->cek_sedia_kendaraan($eventdate, $no_polisi);
            $pengemudi_tersedia = $this->model->cek_sedia_pengemudi($eventdate, $pengemudi);

            if ($dipakai > 1) {
                Session::flash('gglkendaraan', "Kendaraan tidak tersedia pada tanggal " . date("d-m-Y", strtotime($eventdate)) . ", apakah Anda tetap ingin melakukan pemesanan dengan jadwal tersebut?");
            } else if ($pengemudi_tersedia > 1) {
                Session::flash('gglkendaraan', "Pengemudi tidak tersedia pada tanggal " . date("d-m-Y", strtotime($eventdate)) . ", apakah Anda tetap ingin melakukan pemesanan dengan jadwal tersebut?");
            } else {
                Session::flash('success', "Berhasil disimpan");
            }
        }

        return redirect(route('admin.dashboard.kendaraan.show'));
    }

    // saat pilihan cancel dipilih pada flashdata
    public function cancelkendaraan()
    {
        $id = Session::get('id_agendakendaraan');
        $this->model->whereIn('id', $id)->delete();
        return redirect(route('admin.dashboard.kendaraan.show'));
    }


    public function updateagendakendaraan(Request $request)
    {
        $id = $request->id;
        $no_polisi = $request->no_polisi;
        $pengemudi = $request->pengemudi;
        $update = $request->update;

        $tanggal = $request->tanggal;
        $settgl = explode('-', $tanggal);
        $bln = $settgl[1];
        $tgl = $settgl[0];
        $th = $settgl[2];
        $tglakhir = "$th-$bln-$tgl";

        $eventdate = date('Y-m-d', strtotime($tanggal));

        $bagian = $request->bagian;
        $tujuan = $request->tujuan;
        $keterangan = $request->keterangan;
        $where = array('id' => $id);
        $data = array(
            'id_kendaraan' => $no_polisi,
            'id_user' => $pengemudi,
            'tanggal' => $tglakhir,
            'id_bagian' => $bagian,
            'tujuan' => $tujuan,
            'keterangan' => $keterangan
        );

        $tes = $this->model->updatedata($where, $data, 'agendakendaraan');
        // if (!empty($pengemudi)) {
        //     $this->getToken($pengemudi, "petugas driver", "Notifikasi Petugas Driver", $tujuan, $eventdate, $keterangan);
        // }
        Session::flash('success', "Agenda terupdate");
        return redirect(route('admin.dashboard.kendaraan.show'));
    }

    public function commitquery()
    {
        $this->db->trans_commit();
    }

    public function hapusagendakendaraan($id)
    {
        // $agendakendaraan=$_POST['agendakendaraan');
        $where = 'id = ' . $id;
        $this->model->hapusdata($where, 'agendakendaraan');
        Session::flash('success', "Berhasil dihapus");
        return redirect(route('admin.dashboard.kendaraan.show'));
    }

    // fungsi export to pdf
    public function export_agendakendaraan()
    {
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

        $get_bagian = $_POST['bagian'];
        $get_tujuan = $_POST['tujuan'];
        $get_tanggal_awal = date('Y-m-d', strtotime($_POST['tanggal_awal']));
        $get_tanggal_akhir = date('Y-m-d', strtotime($_POST['tanggal_akhir']));
        $get_pengemudi = $_POST['pengemudi'];
        $get_kendaraan = $_POST['kendaraan'];

        $where_bagian = "";
        $where_tujuan = "";
        $where_tgl_awal = "";
        $where_tgl_akhir = "";
        $where_pengemudi = "";
        $where_kendaraan = "";

        if (!empty($get_bagian) and !in_array("", $get_bagian)) {
            $where_bagian = "AND id_bagian IN ('" . implode("','", $get_bagian) . "')";
        }
        if (!empty($get_tujuan)) {
            $where_tujuan = "AND tujuan LIKE '%$get_tujuan%'";
        }
        if (!empty($get_pengemudi)) {
            $where_pengemudi = "AND id_user = '$get_pengemudi'";
        }
        if (!empty($get_kendaraan)) {
            $where_kendaraan = "AND id_kendaraan = '$get_kendaraan'";
        }
        if (!empty($_POST['tanggal_awal'])) {
            $where_tgl_awal = "AND tanggal >= '$get_tanggal_awal'";
        }
        if (!empty($_POST['tanggal_akhir'])) {
            $where_tgl_akhir = "AND tanggal <= '$get_tanggal_akhir'";
        }

        // diberikan awalan 1 untuk menerima AND pada awal query SQL
        $where = "1 " . $where_bagian . $where_tujuan . $where_pengemudi . $where_kendaraan . $where_tgl_awal . $where_tgl_akhir;

        if ($where != '1 ') {
            $agendakendaraan = $this->model->tampil($where);
        } else {
            $agendakendaraan = $this->model->tampilall();
        }

        // echo $where;
        // die();

        // tanggal_awal = "d-m-Y"
        $tgl_awal = explode('-', $get_tanggal_awal);
        $tgl_akhir = explode('-', $get_tanggal_akhir);

        $tanggalawal = $tgl_awal[2] . ' ' . $bulan[(int)$tgl_awal[1]] . ' ' . $tgl_awal[0];
        $tanggalakhir = $tgl_akhir[2] . ' ' . $bulan[(int)$tgl_akhir[1]] . ' ' . $tgl_akhir[0];

        $tgl_today = explode('-', date('Y-m-d'));
        $tanggal = $tgl_today[2] . ' ' . $bulan[(int)$tgl_today[1]] . ' ' . $tgl_today[0];

        $pdf = new \TCPDF();
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // judul header
        $pdf->AddPage('L', 'A4');

        // awal dan akhir periode
        $pdf->SetFont('Helvetica', '', 18);
        $pdf->Cell(280, 5, "Agenda Kendaraan", 0, 1, 'C');
        $pdf->Ln();

        $pdf->SetFont('Helvetica', '', 12);
        if (!empty($_POST['tanggal_awal']) and !empty($_POST['tanggal_akhir'])) {
            $pdf->Cell(50, 5, "Periode", 0);
            $pdf->Cell(230, 0, ": " . $tanggalawal . " - " . $tanggalakhir, 0);
            $pdf->Ln();
        } else if (!empty($_POST['tanggal_awal'])) {
            $pdf->Cell(50, 5, "Periode", 0);
            $pdf->Cell(230, 0, ": Setelah " . $tanggalawal, 0);
            $pdf->Ln();
        } else if (!empty($_POST['tanggal_akhir'])) {
            $pdf->Cell(50, 5, "Periode", 0);
            $pdf->Cell(230, 0, ": Sebelum " . $tanggalakhir, 0);
            $pdf->Ln();
        }
        if (!empty($get_bagian) and !in_array("", $get_bagian)) {

            $value_bagian = array();
            $bagian = $this->m_data->tampilall('bagian');
            foreach ($bagian as $value) {
                if (in_array($value->id, $get_bagian)) {
                    $value_bagian[] = $value->bagian;
                }
            }

            $pdf->Cell(50, 5, "PIC / Bagian", 0);
            $pdf->Cell(230, 0, ": " . implode(", ", $value_bagian), 0);
            $pdf->Ln();
        }
        if (!empty($get_tujuan)) {
            $pdf->Cell(50, 5, "Tujuan", 0);
            $pdf->Cell(230, 0, ": " . $get_tujuan, 0);
            $pdf->Ln();
        }
        if (!empty($get_pengemudi)) {

            $pengemudi = '';
            $user = $this->m_data->tampilall('user');
            foreach ($user as $value) {
                if ($value->id == $get_pengemudi) {
                    $pengemudi = $value->username;
                }
            }

            $pdf->Cell(50, 5, "Pengemudi", 0);
            $pdf->Cell(230, 0, ": " . $pengemudi, 0);
            $pdf->Ln();
        }
        if (!empty($get_kendaraan)) {

            $kendaraan = '';
            $nopol = $this->m_data->tampilall('kendaraan');
            foreach ($nopol as $value) {
                if ($value->id == $get_kendaraan) {
                    $kendaraan = $value->no_polisi;
                }
            }

            $pdf->Cell(50, 5, "Kendaraan", 0);
            $pdf->Cell(230, 0, ": " . $kendaraan, 0);
            $pdf->Ln();
        }

        date_default_timezone_set("Asia/Jakarta");
        $time_now = date('H:i:s');

        $pdf->Ln();
        $pdf->Cell(50, 5, "Tanggal Jam Download", 0);
        $pdf->Cell(230, 0, ": " . $tanggal . ' ' . $time_now, 0);
        $pdf->Ln();
        $pdf->Ln();

        $pdf->SetFont('Helvetica', '', 10);
        // make a table
        $html = "
            <table>
                <tr>
                    <th style=\"width:4%;\">No</th>
                    <th>Hari, Tanggal</th>
                    <th>No. Polisi</th>
                    <th>Pengemudi</th>
                    <th>PIC/Bagian</th>
                    <th>Tujuan</th>
                    <th>Keterangan</th>
                </tr>
            ";

        $daftar_hari = array(
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        );

        foreach ($agendakendaraan as $k => $agendakendaraan) {
            $tgl = explode('-', $agendakendaraan->tanggal);
            $tanggal = $tgl[2] . ' ' . $bulan[(int)$tgl[1]] . ' ' . $tgl[0];

            $namahari = date('l', strtotime($agendakendaraan->tanggal));

            $html .= "
                <tr>
                    <td style=\"width:4%; text-align:center;\">" . ++$k . "</td>
                    <td>" . $daftar_hari[$namahari] . ", " . $tanggal . "</td>
                    <td style=\"text-align:center;\">" . $agendakendaraan->join_no_polisi . "</td>
                    <td style=\"text-align:center;\">" . $agendakendaraan->join_pengemudi . "</td>
                    <td style=\"text-align:center;\">" . $agendakendaraan->join_bagian . "</td>
                    <td>" . $agendakendaraan->tujuan . "</td>
                    <td>" . $agendakendaraan->keterangan . "</td>
                </tr>

            ";
        }

        $html .= "
            </table>
            <style>
            table {
                border-collapse:collapse;
            }
            th,td {
                border:1px solid #888;
            }
            table tr th {
                background-color:#888;
                color:#fff;
                font-weight:bold;
                text-align: center;
            }
            }
            </style>
        ";

        // menulis html
        $pdf->WriteHTMLCell(310, 0, 9, '', $html, 0);

        date_default_timezone_set("Asia/Jakarta");
        $date_time = date('d-m-Y H:i:s');
        if (ob_get_length()) ob_end_clean();
        $pdf->Output('Agenda Kendaraan ' . $date_time . '.pdf');
    }

    // fungsi export to excel
    public function export_agendakendaraan_excel()
    {
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

        $get_bagian = $_POST['bagian'];
        $get_tujuan = $_POST['tujuan'];
        $get_tanggal_awal = date('Y-m-d', strtotime($_POST['tanggal_awal']));
        $get_tanggal_akhir = date('Y-m-d', strtotime($_POST['tanggal_akhir']));
        $get_pengemudi = $_POST['pengemudi'];
        $get_kendaraan = $_POST['kendaraan'];

        $where_bagian = "";
        $where_tujuan = "";
        $where_tgl_awal = "";
        $where_tgl_akhir = "";
        $where_pengemudi = "";
        $where_kendaraan = "";

        if (!empty($get_bagian) and !in_array("", $get_bagian)) {
            $where_bagian = "AND id_bagian IN ('" . implode("','", $get_bagian) . "')";
        }
        if (!empty($get_tujuan)) {
            $where_tujuan = "AND tujuan LIKE '%$get_tujuan%'";
        }
        if (!empty($get_pengemudi)) {
            $where_pengemudi = "AND id_user = '$get_pengemudi'";
        }
        if (!empty($get_kendaraan)) {
            $where_kendaraan = "AND id_kendaraan = '$get_kendaraan'";
        }
        if (!empty($_POST['tanggal_awal'])) {
            $where_tgl_awal = "AND tanggal >= '$get_tanggal_awal'";
        }
        if (!empty($_POST['tanggal_akhir'])) {
            $where_tgl_akhir = "AND tanggal <= '$get_tanggal_akhir'";
        }

        // diberikan awalan 1 untuk menerima AND pada awal query SQL
        $where = "1 " . $where_bagian . $where_tujuan . $where_pengemudi . $where_kendaraan . $where_tgl_awal . $where_tgl_akhir;

        if ($where != '1 ') {
            $agendakendaraan = $this->model->tampil($where);
        } else {
            $agendakendaraan = $this->model->tampilall();
        }

        // echo $this->db->last_query();;
        // die();

        // tanggal_awal = "d-m-Y"
        $tgl_awal = explode('-', $get_tanggal_awal);
        $tgl_akhir = explode('-', $get_tanggal_akhir);

        $tanggalawal = $tgl_awal[2] . ' ' . $bulan[(int)$tgl_awal[1]] . ' ' . $tgl_awal[0];
        $tanggalakhir = $tgl_akhir[2] . ' ' . $bulan[(int)$tgl_akhir[1]] . ' ' . $tgl_akhir[0];

        $tgl_today = explode('-', date('Y-m-d'));
        $tanggal = $tgl_today[2] . ' ' . $bulan[(int)$tgl_today[1]] . ' ' . $tgl_today[0];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getDefaultColumnDimension()->setWidth(15);
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(25);

        $sheet->setCellValue('A1', 'Agenda Kendaraan');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1:G1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); //Set horizontal center

        $row = 2;
        if (!empty($_POST['tanggal_awal']) and !empty($_POST['tanggal_akhir'])) {
            $row++;
            $sheet->setCellValue('A' . $row, 'Periode');
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('C' . $row, ": " . $tanggalawal . " - " . $tanggalakhir);
        } else if (!empty($_POST['tanggal_awal'])) {
            $row++;
            $sheet->setCellValue('A' . $row, 'Periode');
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('C' . $row, ": Setelah " . $tanggalawal);
        } else if (!empty($_POST['tanggal_akhir'])) {
            $row++;
            $sheet->setCellValue('A' . $row, 'Periode');
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('C' . $row, ": Sebelum " . $tanggalakhir);
        }
        if (!empty($get_bagian) and !in_array("", $get_bagian)) {

            $value_bagian = array();
            $bagian = $this->m_data->tampilall('bagian');
            foreach ($bagian as $value) {
                if (in_array($value->id, $get_bagian)) {
                    $value_bagian[] = $value->bagian;
                }
            }

            $row++;
            $sheet->setCellValue('A' . $row, 'PIC / Bagian');
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('C' . $row, ": " . implode(", ", $value_bagian));
        }
        if (!empty($get_tujuan)) {
            $row++;
            $sheet->setCellValue('A' . $row, 'Tujuan');
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('C' . $row, ": " . $get_tujuan);
        }
        if (!empty($get_pengemudi)) {

            $pengemudi = '';
            $user = $this->m_data->tampilall('user');
            foreach ($user as $value) {
                if ($value->id == $get_pengemudi) {
                    $pengemudi = $value->username;
                }
            }

            $row++;
            $sheet->setCellValue('A' . $row, 'Pengemudi');
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('C' . $row, ": " . $pengemudi);
        }
        if (!empty($get_kendaraan)) {

            $kendaraan = '';
            $nopol = $this->m_data->tampilall('kendaraan');
            foreach ($nopol as $value) {
                if ($value->id == $get_kendaraan) {
                    $kendaraan = $value->no_polisi;
                }
            }

            $row++;
            $sheet->setCellValue('A' . $row, 'Kendaraan');
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->setCellValue('C' . $row, ": " . $kendaraan);
        }

        date_default_timezone_set("Asia/Jakarta");
        $time_now = date('H:i:s');

        $row += 2;
        $sheet->setCellValue('A' . $row, 'Tanggal Jam Download');
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('C' . $row, ": " . $tanggal . ' ' . $time_now);

        $row += 2;
        $sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A' . $row, 'No');
        $sheet->setCellValue('B' . $row, 'Hari, Tanggal');
        $sheet->setCellValue('C' . $row, 'No. Polisi');
        $sheet->setCellValue('D' . $row, 'Pengemudi');
        $sheet->setCellValue('E' . $row, 'PIC/Bagian');
        $sheet->setCellValue('F' . $row, 'Tujuan');
        $sheet->setCellValue('G' . $row, 'Keterangan');

        $daftar_hari = array(
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        );

        $row_mulai = $row;
        foreach ($agendakendaraan as $k => $agendakendaraan) {
            $tgl = explode('-', $agendakendaraan->tanggal);
            $tanggal = $tgl[2] . ' ' . $bulan[(int)$tgl[1]] . ' ' . $tgl[0];

            $namahari = date('l', strtotime($agendakendaraan->tanggal));

            $row++;
            $sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()
                ->setWrapText(true); //Set wrap
            $sheet->setCellValue('A' . $row, ++$k);
            $sheet->setCellValue('B' . $row, $daftar_hari[$namahari] . ", " . $tanggal);
            $sheet->setCellValue('C' . $row, $agendakendaraan->join_no_polisi);
            $sheet->setCellValue('D' . $row, $agendakendaraan->join_pengemudi);
            $sheet->setCellValue('E' . $row, $agendakendaraan->join_bagian);
            $sheet->setCellValue('F' . $row, $agendakendaraan->tujuan);
            $sheet->setCellValue('G' . $row, $agendakendaraan->keterangan);
        }

        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A' . $row_mulai . ':G' . $row)->applyFromArray($border);

        date_default_timezone_set("Asia/Jakarta");
        $date_time = date('d-m-Y H:i:s');

        $writer = new Xlsx($spreadsheet);
        $spreadsheet->setActiveSheetIndex(0);
        $filename = 'Agenda Kendaraan ' . $date_time;

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }
}
