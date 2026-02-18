<x-layouts.app>
    <x-slot name="slot">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h3 class="mt-4">Manajemen Pengguna</h3>
                    <a href="{{ route('admin.dashboard.master.pengguna.formCreate') }}" class="btn btn-success"
                        role="button">Tambahkan</a>
                    <br><br>
                    <div class="card mb-4">
                        <div class="card-body">
                            <x-partials.custom.datatable idTable="datatable-master-pengguna" :serverSide=false>
                                <x-slot name="tableHead">
                                    <tr>
                                        <th>
                                            <center>No
                                        </th>
                                        <th>
                                            <center>Username
                                        </th>
                                        <th>
                                            <center>Regional
                                        </th>
                                        <th>
                                            <center>Bagian
                                        </th>
                                        <th>
                                            <center>Hak Akses
                                        </th>
                                        {{-- <th>
                                            <center>Petugas
                                        </th>
                                        <th>
                                            <center>Role
                                        </th> --}}
                                        <th>
                                            <center>No. Handphone
                                        </th>
                                        {{-- <th>
                                            <center>NIK
                                        </th> --}}
                                        <th>
                                            <center>Status
                                        </th>
                                        <th>
                                            <center>Keterangan
                                        </th>
                                        <th style="min-width: 20%">
                                            <center>Aksi
                                        </th>
                                    </tr>
                                </x-slot>

                                <x-slot name="tableBody">
                                    @foreach ($view as $result)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $result->master_user_nama }}</td>
                                            <td style="text-align: center;">
                                               {{ $result->nama_regional }}
                                            </td>
                                            <td style="text-align: center;">
                                                @php
                                                    $bagianCocok = $bagian->firstWhere(
                                                        'master_bagian_id',
                                                        $result->master_nama_bagian_id,
                                                    );
                                                @endphp
                                                {{ $bagianCocok ? $bagianCocok->master_bagian_nama : 'Bagian tidak ditemukan' }}
                                            </td>
                                            <td style="text-align: center;">
                                                @php
                                                    $hakakses = $hak_akses->firstWhere(
                                                        'hak_akses_id',
                                                        $result->master_hak_akses_id,
                                                    );
                                                @endphp
                                                {{ $hakakses ? $hakakses->hak_akses_nama : 'Hak Akses tidak ditemukan' }}
                                            </td>
                                            {{-- <td>{{ $result->petugas }}</td>
                                            <td>{{ $result->role }}</td> --}}
                                            <td>{{ $result->master_user_no_hp }}</td>
                                            {{-- <td>{{ $result->nik }}</td> --}}
                                            <td style="text-align: center;">
                                                @if ($result->master_user_status == 1)
                                                    Aktif
                                                @else
                                                    Non-Aktif
                                                @endif
                                            </td>

                                            <td>{{ $result->master_user_keterangan }}</td>
                                            <td>
                                                <div class="row">
                                                    <a href="{{ route('admin.dashboard.master.pengguna.formResetPassword', ['id' => $result->master_user_id]) }}"
                                                        class="btn btn-success btn-sm"
                                                        style="margin-right: 6px; margin-bottom: 3px; color: white;">repass</a>
                                                    <a href="{{ route('admin.dashboard.master.pengguna.formUpdate', ['id' => $result->master_user_id]) }}"
                                                        class="btn btn-warning btn-sm"
                                                        style="margin-right: 6px; margin-bottom: 3px;">edit</a>
                                                    <form action="{{ route('admin.dashboard.master.pengguna.hapus') }}"
                                                        method="post">
                                                        @csrf
                                                        <input type="hidden" name="id"
                                                            value="{{ $result->master_user_id }}">
                                                        <button type="submit"class="btn btn-danger btn-sm"
                                                            style="margin-right: 6px; margin-bottom: 3px;"
                                                            onclick="return confirm('Are you sure?');">hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-partials.custom.datatable>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </x-slot>

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                @if (Session::has('success'))
                    setTimeout(function() {
                        swal("{{ Session::get('success') }}");
                    }, 1000);
                @endif

                @if (Session::has('error'))
                    setTimeout(function() {
                        swal({
                            title: "{{ Session::get('error') }}",
                            type: "info",
                            confirmButtonText: "Ok",
                            confirmButtonColor: "#ff0055",
                            reverseButtons: true,
                            focusConfirm: true
                        });
                    }, 1000);
                @endif
            });
        </script>
    </x-slot>
</x-layouts.app>
