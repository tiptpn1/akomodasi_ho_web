<x-layouts.app>
    <x-slot name="styles">
        <style type="text/css">
            .select2-selection__choice__remove {
                color: white !important;
            }

            .hidden-section {
                display: none;
            }
        </style>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
        <!-- CSS Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- JavaScript Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </x-slot>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Permintaan Kartu Lift & Parkir</h3>
                @if (!in_array(Auth::user()->master_hak_akses_id, [5, 6]))
                <button id="btnTambah" type="button" data-toggle="modal" data-target="#tambah" class="btn btn-primary">Tambah Data</button>
                <button id="btnExport" type="button" data-toggle="modal" data-target="#exportModal" class="btn btn-warning">Export Data</button>
                @endif
                @if(session('success'))
                <div class="alert alert-success alert-sm alert-dismissible fade show" role="alert" style="max-width: 400px; margin:">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @elseif(session('error'))
                <div class="alert alert-danger alert-sm alert-dismissible fade show" role="alert" style="max-width: 400px;">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <!-- Modal Tambah -->
                <div id="tambah" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Tambah Permintaan Kartu Lift & Parkir</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskecil" class="form-group container-fluid">
                                    <form id="form_tambah" class="form-horizontal calender" role="form" enctype="multipart/form-data" method="POST" action="/kartu/store">
                                        @csrf
                                        <div class="row">
                                            
                                            <div class="form-group col-md-6">
                                                <b>Nama Pengaju <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_pengaju" id='ajukan' required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>NIK Karyawan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nik" id='nik' required>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Divisi <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="divisi" value="{{ $divisi }}" required readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Permintaan Kartu <span class="text-danger">*</span></b>
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                        <input 
                                                            type="checkbox" 
                                                            class="form-check-input" 
                                                            name="ck_lift" 
                                                            id="ck_lift"
                                                            value="1">
                                                        <label class="form-check-label" for="ck_lift">Lift</label>
                                                    </div><br>
                                                    <div class="form-check form-check-inline">
                                                        <input 
                                                            type="checkbox" 
                                                            class="form-check-input" 
                                                            name="ck_parkir"  value="1"
                                                            id="ck_parkir" 
                                                            onchange="toggleKendaraan()"> <!-- Trigger untuk toggle -->
                                                        <label class="form-check-label" for="ck_parkir">Parkir</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Bagian Kendaraan -->
                                        <div id="data-kendaraan" style="display: none;"> <!-- Hidden by default -->
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="jenis_kendaraan">Jenis Kendaraan 1</label>
                                                    <select class="form-control" name="jenis_kendaraan1">
                                                        <option value="" disabled selected>Pilih Kendaraan</option>
                                                        <option value='Mobil'>Mobil</option>
                                                        <option value='Motor'>Motor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <b>Nopol Kendaraan 1 <span class="text-danger">*</span></b>
                                                    <input type="text" class="form-control" name="nopol1" id="nopol1">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <b>Upload STNK Kendaraan 1 <span class="text-danger">*</span></b>
                                                    <input type="file" class="form-control" name="stnk1" id="stnk1">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="jenis_kendaraan">Jenis Kendaraan 2</label>
                                                    <select class="form-control" name="jenis_kendaraan2">
                                                        <option value="" disabled selected>Pilih Kendaraan</option>
                                                        <option value='Mobil'>Mobil</option>
                                                        <option value='Motor'>Motor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <b>Nopol Kendaraan 2 <span class="text-danger">*</span></b>
                                                    <input type="text" class="form-control" name="nopol2" id="nopol2">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <b>Upload STNK Kendaraan 2 <span class="text-danger">*</span></b>
                                                    <input type="file" class="form-control" name="stnk2" id="stnk2">
                                                </div>
                                            </div>
                                        </div>

                                        

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Upload KTP<span class="text-danger">*</span></b>
                                                <input type="file" class="form-control" name="ktp" id="ktp" required >
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Upload Memo Persetujuan<span class="text-danger">*</span></b>
                                                <input type="file" class="form-control" name="memo" id="memo" required >
                                            </div>
                                        </div>
                                        <!-- Modal Footer (buttons) -->
                                        <div class="modal-footer d-flex justify-content-end">
                                            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary antosubmit">Tambahkan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div id="edit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel">Edit Data</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body">
                                <div id="kaskeciledit" class="form-group container-fluid">
                                    <form id="form_edit" class="form-horizontal calender" role="form" enctype="multipart/form-data" method="POST" action="/kartu/edit/{{ id }}">
                                        @csrf
                                        <div class="row">
                                            <input type="hidden" name="id">
                                            <div class="form-group col-md-6">
                                                <b>Nama Pengaju <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nama_pengaju1" id='ajukan1' required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>NIK Karyawan <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="nik1" id='nik1' required>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Divisi <span class="text-danger">*</span></b>
                                                <input type="text" class="form-control" name="divisi1" value="{{ $divisi }}" required readonly>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Permintaan Kartu <span class="text-danger">*</span></b>
                                                <div>
                                                    <div class="form-check form-check-inline">
                                                        <input 
                                                            type="checkbox" 
                                                            class="form-check-input" 
                                                            name="ck_lift1" 
                                                            id="ck_lift1"
                                                            value="1">
                                                        <label class="form-check-label" for="ck_lift1">Lift</label>
                                                    </div><br>
                                                    <div class="form-check form-check-inline">
                                                        <input 
                                                            type="checkbox" 
                                                            class="form-check-input" 
                                                            name="ck_parkir1"  
                                                            value="1"
                                                            id="ck_parkir1" 
                                                            onchange="toggleKendaraan1()"> <!-- Panggil toggleKendaraan1 -->
                                                        <label class="form-check-label" for="ck_parkir1">Parkir</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Bagian Kendaraan -->
                                        <div id="data-kendaraan1" style="display: none;"> <!-- Hidden by default -->
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="jenis_kendaraan">Jenis Kendaraan 1</label>
                                                    <select class="form-control" name="jenis_kendaraan3">
                                                        <option value="" disabled selected>Pilih Kendaraan</option>
                                                        <option value='Mobil'>Mobil</option>
                                                        <option value='Motor'>Motor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <b>Nopol Kendaraan 1 <span class="text-danger">*</span></b>
                                                    <input type="text" class="form-control" name="nopol3" id="nopol3">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <b>Upload STNK Kendaraan 1 <span class="text-danger">*</span></b>
                                                    <span id="stnk1-file-name" class="text-muted ml-2"></span>
                                                    <input type="file" class="form-control" name="stnk3" id="stnk3">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <label for="jenis_kendaraan">Jenis Kendaraan 2</label>
                                                    <select class="form-control" name="jenis_kendaraan4">
                                                        <option value="" disabled selected>Pilih Kendaraan</option>
                                                        <option value='Mobil'>Mobil</option>
                                                        <option value='Motor'>Motor</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-6">
                                                    <b>Nopol Kendaraan 2 <span class="text-danger">*</span></b>
                                                    <input type="text" class="form-control" name="nopol4" id="nopol4">
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <b>Upload STNK Kendaraan 2 <span class="text-danger">*</span></b>
                                                    <span id="stnk2-file-name" class="text-muted ml-2"></span>
                                                    <input type="file" class="form-control" name="stnk4" id="stnk4">
                                                </div>
                                            </div>
                                        </div>

                                        

                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <b>Upload KTP<span class="text-danger">*</span></b>
                                                <span id="ktp-file-name" class="text-muted ml-2"></span>
                                                <input type="file" class="form-control" name="ktp1"  >
                                                {{-- <input type="file" class="form-control" name="bukti_bayar"> --}}
                                            </div>
                                            <div class="form-group col-md-6">
                                                <b>Upload Memo Persetujuan<span class="text-danger">*</span></b>
                                                <span id="memo-file-name" class="text-muted ml-2"></span>
                                                <input type="file" class="form-control" name="memo1" >
                                            </div>
                                        </div>
                                        <!-- Modal Footer (buttons) -->
                                        <div class="modal-footer d-flex justify-content-end">
                                            <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary antosubmit">Update Data</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Export -->
                <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exportModalLabel">Export Makan Siang</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="exportForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl_pengajuan_awal">Tanggal Awal</label>
                                                <input type="date" class="form-control" id="tgl_awal" name="tgl_awal">
                                            </div>
                                            <div class="form-group">
                                                <label for="nama_group">Divisi</label>
                                                <!-- <input type="text" class="form-control" id="nama_group" name="nama_group"> -->
                                                <select class="form-control" name="id_divisi">
                                                @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))
                                                    <option value="" disabled selected>Pilih Divisi</option>
                                                    @foreach ($get_divisi as $data_divisi)
                                                    <option value='{{ $data_divisi->master_bagian_nama }}'>{{ $data_divisi->master_bagian_nama }}</option>
                                                    @endforeach
                                                    <option value='all'>Seluruh Divisi</option>
                                                    @else
                                                    <option value="{{ $divisi }}">{{ $divisi }}</option>
                                                @endif
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tgl_pengajuan_akhir">Tanggal Akhir</label>
                                                <input type="date" class="form-control" id="tgl_akhir" name="tgl_akhir">
                                            </div>
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <!-- <input type="text" class="form-control" id="nomor_gl" name="nomor_gl"> -->
                                                <select class="form-control" name="status">
                                                    <option value="" disabled selected>Pilih Status</option>
                                                    <option value='2'>Approved</option>
                                                    <option value='3'>Rejected</option>
                                                    <option value='0'>Canceled</option>
                                                    <option value='1'>Pengajuan Divisi</option>
                                                    <option value='all'>Seluruh Status</option>
                                                    
                                                </select>
                                            </div>
                                           
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary" id="exportBtn">Export to Excel</button>
                                    <button type="reset" class="btn btn-secondary mr-2" id="resetBtn">Reset Filter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-primary mt-3">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTables-kaskecil">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Aksi</th>
                                        <th>Nama Pengaju</th>
                                        <th>NIK Karyawan</th>
                                        <th>Divisi</th>
                                        <th>Kartu Lift</th>
                                        <th>Kartu Parkir</th>
                                        <th>Kendaraan</th>
                                        <th>Nopol Kendaraan</th>
                                        <th>Status Pengajuan Kartu Lift</th>
                                        <th>Status Pengajuan Kartu Parkir</th>
                                        <th>No. Kartu Lift</th>
                                        <th>No. Kartu Parkir</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kartu    as $index => $item)
                                    <tr align="center">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @php
                                                    $today = \Carbon\Carbon::today();
                                                    $requestDate = \Carbon\Carbon::parse($item->tgl_permintaan);
                                                    $isEnabled = $item->status_lift === "Pengajuan" || $item->status_parkir === "Pengajuan"; // Disabled jika tgl_permintaan <= hari ini atau status = 0
                                                    // $isDisabled = $requestDate->lte($today) || $item->status == 0 || $item->status == 2; // Disabled jika tgl_permintaan <= hari ini atau status = 0 (Canceled) atau status = 2 (Approved)
      
                                                    // $isPending = $item->status == 1; // Hanya aktif jika status = 1
                                                    if($item->status_lift === "Pengajuan" || $item->status_parkir === "Pengajuan")
                                                    {
                                                        $hasil='enabled';
                                                    }
                                                    else {
                                                        $hasil='disabled';
                                                    }
                                                @endphp
                                                <!-- Edit Button -->
                                                {{-- {{ $isEnabled }} --}}
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-info" 
                                                    data-toggle="modal" 
                                                    data-target="#edit" 
                                                    data-id="{{ $item->id ?? '-' }}" 
                                                    id="btnEdit" 
                                                    {{ $hasil; }}
                                                    
                                                >
                                                    <i class="fa fa-pencil" style="color: white;"></i>
                                                </button>
                                                
                                                <!-- Delete Button -->
                                                <form action="{{ route('kartu.destroy', $item->id ?? '-') }}" method="POST" onsubmit="return confirm('Apakah yakin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" {{ $hasil; }}>
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                
                                                <!-- Approve and Reject Buttons -->
                                                @if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga']))
                                                <!-- Approve Button -->
                                                <form action="{{ route('kartu.approve', $item->id ?? '-') }}" method="POST" onsubmit="return confirm('Apakah yakin menyetujui data ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" @if (!$isPending) disabled @endif>
                                                        <i class="fa fa-check"></i> <!-- Icon centang -->
                                                    </button>
                                                </form>

                                                <!-- Reject Button -->
                                                <form action="{{ route('kartu.reject', $item->id ?? '-') }}" method="POST" onsubmit="return confirm('Apakah yakin menolak data ini?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" @if (!$isPending) disabled @endif>
                                                        <i class="fa fa-times"></i> <!-- Icon silang -->
                                                    </button>
                                                </form>
                                                @endif

                                            </div>
                                        </td>
                                        <td>{{ $item->nama_pengaju ?? '-' }}</td>
                                        <td align="center">{{ $item->nik_karyawan ?? '-' }}</td>
                                        <td align="center">{{ $item->divisi ?? '-' }}</td>
                                        <td align="center">
                                            {!! $item->lift == 1 ? '<span class="text-success">✔</span>' : '-' !!}
                                        </td>
                                        <td align="center">
                                            {!! $item->parkir == 1 ? '<span class="text-success">✔</span>' : '-' !!}
                                        </td>
                                        <td align="center">{{ $item->k1 ?? '-' }}<br>{{ $item->k2 ?? '-' }}</td>
                                        <td align="center">{{ $item->nopol1 ?? '-' }}<br>{{ $item->nopol2 ?? '-' }}</td>
                                        <td align="center">{{ $item->status_lift ?? '-' }}</td>
                                        <td align="center">{{ $item->status_parkir ?? '-' }}</td>
                                        <td align="center">{{ $item->no_lift ?? '-' }}</td>
                                        <td align="center">{{ $item->no_parkir ?? '-' }}</td>
                                        {{-- <td align="center">
                                            @if ($item->kadiv == 1)
                                                <i class="fa fa-check text-success" title="Ya"></i> <!-- Icon centang -->
                                            @else
                                                <i class="fa fa-times text-danger" title="Tidak"></i> <!-- Icon silang -->
                                            @endif
                                        </td> --}}
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                
                
            </div>
        </main>
    </div>

    <x-slot name="scripts">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script src="https://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#dataTables-kaskecil').DataTable({
                    responsive: true
                });
            });
        </script>
        <script>
            $(document).ready(function () {
                // Set tanggal besok pada input secara langsung
                $('#tgl_pengajuan').val(moment().add(1, 'days').format('YYYY-MM-DD'));
        
                // Inisialisasi datepicker setelah nilai input di-set
                $('#tgl_pengajuan').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate: moment().add(1, 'days'), // Tanggal default adalah besok
                    minDate: moment().add(1, 'days'), // Blokir tanggal hari ini dan sebelumnya
                    locale: {
                        format: 'YYYY-MM-DD' // Format tanggal
                    }
                });
        
                // Mengatur agar input hanya bisa dipilih (tidak bisa diketik)
                $('#tgl_pengajuan').prop('readonly', true);  // Menonaktifkan input manual
            });
        </script>
        <script>
            $(document).ready(function () {
                // Set tanggal besok pada input secara langsung
                $('#tgl_pengajuan1').val(moment().add(1, 'days').format('YYYY-MM-DD'));
        
                // Inisialisasi datepicker setelah nilai input di-set
                $('#tgl_pengajuan1').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate: moment().add(1, 'days'), // Tanggal default adalah besok
                    minDate: moment().add(1, 'days'), // Blokir tanggal hari ini dan sebelumnya
                    locale: {
                        format: 'YYYY-MM-DD' // Format tanggal
                    }
                });
        
                // Mengatur agar input hanya bisa dipilih (tidak bisa diketik)
                $('#tgl_pengajuan1').prop('readonly', true);  // Menonaktifkan input manual
            });
        </script>
        <script>
            var today = new Date();
            var tomorrow = new Date(today);
            tomorrow.setDate(today.getDate() + 1); // Menambahkan 1 hari
            var dateString = tomorrow.toISOString().split('T')[0]; // Format: YYYY-MM-DD

            document.getElementById("btnTambah").addEventListener("click", function() {
                resetForm();
                document.getElementById("tgl_pengajuan").value = dateString; // Isi dengan tanggal esok
            });

        </script>
    <script>
        function toggleKendaraan() {
        const checkboxParkir = document.getElementById('ck_parkir');
        const dataKendaraan = document.getElementById('data-kendaraan');

        if (checkboxParkir.checked) {
            dataKendaraan.style.display = 'block'; // Tampilkan jika Parkir dicentang
        } else {
            dataKendaraan.style.display = 'none'; // Sembunyikan jika tidak
        }
    }

    </script>
    <script>
       function toggleKendaraan1() {
        const checkbox = document.getElementById('ck_parkir1');
        const dataKendaraan = document.getElementById('data-kendaraan1');
        
        if (checkbox.checked) {
            // Tampilkan form kendaraan
            dataKendaraan.style.display = 'block';
        } else {
            // Sembunyikan form kendaraan dan kosongkan isinya
            dataKendaraan.style.display = 'none';
            
            // Kosongkan semua input di dalam form kendaraan
            const inputs = dataKendaraan.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.type === 'file') {
                    // Reset input file
                    input.value = '';
                    const fileLabel = input.nextElementSibling;
                    if (fileLabel) {
                        fileLabel.textContent = ''; // Reset file label
                    }
                } else {
                    // Reset input text, select, etc.
                    input.value = '';
                }
            });
        }
    }



    </script>


        <script>
            function resetForm() {
                document.getElementById("form_tambah").reset(); // Reset semua field form
            }

            function resetFormEdit() {
                document.getElementById("form_edit").reset(); // Reset semua field form
            }
        </script>
        <script>
            $(document).on('click', '.btn-info', function() {
            resetFormEdit(); // Reset form sebelum mengisi
            const id = $(this).data('id'); // Ambil ID dari tombol
            
            $.ajax({
                url: `/kartu/edit/${id}`, // Endpoint untuk mendapatkan data
                method: 'GET',
                success: function(data) {
                    // Isi modal dengan data dari server
                    $('input[name="id"]').val(data.id);
                    $('input[name="nama_pengaju1"]').val(data.nama_pengaju);
                    $('input[name="nik1"]').val(data.nik_karyawan);
                    $('input[name="divisi1"]').val(data.divisi);

                    // Atur checkbox Lift
                    if (data.lift == 1) {
                        $('#ck_lift1').prop('checked', true);
                    } else {
                        $('#ck_lift1').prop('checked', false);
                    }

                    // Atur checkbox Parkir dan tampilkan/hidden kendaraan
                    if (data.parkir == 1) {
                        $('#ck_parkir1').prop('checked', true);
                        $('#data-kendaraan1').show();
                    } else {
                        $('#ck_parkir1').prop('checked', false);
                        $('#data-kendaraan1').hide();
                    }


                    // Isi data kendaraan
                    $('select[name="jenis_kendaraan3"]').val(data.k1);
                    $('input[name="nopol3"]').val(data.nopol1);
                    $('select[name="jenis_kendaraan4"]').val(data.k2);
                    $('input[name="nopol4"]').val(data.nopol2);

                    if (data.ktp_file) {
                        const fileName = data.ktp_file.split('.').pop(); // Ambil bagian terakhir dari path
                        $('#stnk1-file-name').text(`(STNK.${fileName})`);
                    } else {
                        $('#stnk1-file-name').text('-'); // Kosongkan jika tidak ada file
                    }
                    if (data.memo_file) {
                        const fileName2 = data.memo_file.split('.').pop(); // Ambil bagian terakhir dari path
                        $('#stnk2-file-name').text(`(STNK.${fileName2})`);
                    } else {
                        $('#stnk2-file-name').text('-'); // Kosongkan jika tidak ada file
                    }

                    // Tampilkan nama file KTP jika ada
                    if (data.ktp_file) {
                        const fileName = data.ktp_file.split('.').pop(); // Ambil bagian terakhir dari path
                        $('#ktp-file-name').text(`(KTP.${fileName})`);
                    } else {
                        $('#ktp-file-name').text('-'); // Kosongkan jika tidak ada file
                    }
                    if (data.memo_file) {
                        const fileName2 = data.memo_file.split('.').pop(); // Ambil bagian terakhir dari path
                        $('#memo-file-name').text(`(Memo.${fileName2})`);
                    } else {
                        $('#memo-file-name').text('-'); // Kosongkan jika tidak ada file
                    }

                    // Tampilkan nama file Memo jika ada
                    if (data.memo_file) {
                        $('#memo1').text(`File saat ini: ${data.memo_file}`);
                    } else {
                        $('#memo1').text('Belum ada file yang diunggah.');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        </script>
        <script>
            $('#form_edit').on('submit', function(e) {
                e.preventDefault();

                const id = $('input[name="id"]').val(); // Ambil ID dari form
                const formData = $(this).serialize(); // Ambil semua data dari form

                $.ajax({
                    url: `/kartu/update/${id}`, // Endpoint untuk update
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        if (response.redirect_url) {
                            // Redirect ke URL dari server
                            window.location.href = response.redirect_url;
                        } else {
                            console.error('Redirect URL tidak ditemukan dalam respons.');
                        }

                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseText);
                        alert('Terjadi kesalahan saat mengirim data.');
                    }
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                const groupIdsToShow = [2, 3, 5, 13, 17, 18];

                // Pastikan elemen hidden-section tersembunyi saat halaman dimuat
                $('.hidden-section').hide();

                // Pengecekan awal jika dalam mode edit
                const selectedGroupId = parseInt($('select[name="id_group"]').val());
                if (groupIdsToShow.includes(selectedGroupId)) {
                    $('.hidden-section').show(); // Tampilkan elemen
                    $('.hidden-section input, .hidden-section select').attr('required', true); // Tambahkan validasi required
                }
                // Event listener untuk dropdown id_group
                $('select[name="id_group"]').on('change', function() {
                    const selectedGroupId = parseInt($(this).val());
                    if (groupIdsToShow.includes(selectedGroupId)) {
                        $('.hidden-section').show(); // Tampilkan elemen
                        //$('.hidden-section input, .hidden-section select').attr('required', true); // Tambahkan validasi required
                    } else {
                        $('.hidden-section').hide(); // Sembunyikan elemen
                        $('.hidden-section input, .hidden-section select').removeAttr('required'); // Hapus validasi required
                    }
                });
            });
        </script>
        <script>
            $('#exportBtn').on('click', function() {
                var formData = $('#exportForm').serialize(); // Get the form data

                // Trigger the Excel export request with the selected filters
                window.location.href = "{{ route('makansiang.export') }}?" + formData;

                // Use JavaScript to simulate a click event on the element with data-dismiss="modal"
                $('[data-dismiss="modal"]').click();
            });
            // });
        </script>
    </x-slot>
</x-layouts.app>