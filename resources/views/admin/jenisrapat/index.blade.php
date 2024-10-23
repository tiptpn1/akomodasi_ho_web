<x-layouts.app>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h3 class="mt-4">Manajemen Jenis Rapat</h3>
                @if (auth()->user()->role != 'Read Only')
                    <button type="button" data-toggle="modal" data-target="#modalTambahJenisRapat"
                        class="btn btn-success">Tambahkan</button><br>
                @endif
                <br>
                <div class="card mb-4">
                    <div class="card-body">
                        <x-partials.custom.datatable idTable="dataTables-example" :serverSide=false>
                            <x-slot name="tableHead">
                                <tr>
                                    <th>
                                        <center>No
                                    </th>
                                    <th>
                                        <center>Nama
                                    </th>
                                    <th>
                                        <center>Warna
                                    </th>
                                    <th>
                                        <center>Status
                                    </th>
                                    <th>
                                        <center>Keterangan
                                    </th>
                                    @if (auth()->user()->role != 'Read Only')
                                        <th>
                                            <center>Aksi
                                        </th>
                                    @endif
                                </tr>
                            </x-slot>

                            <x-slot name="tableBody">
                                @foreach ($view as $result)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $result->nama }}</td>
                                        <td style="text-align: center;">
                                            <div
                                                style="display: flex; flex-direction: row; gap: 10px; justify-content: center;">
                                                <div
                                                    style="width: 20px; height: 20px; background-color:{{ $result->kode_warna }}">
                                                </div>
                                                <div>{{ $result->kode_warna }}</div>
                                            </div>
                                        </td>
                                        <td style="text-align: center;">{{ $result->status }}</td>
                                        <td>{{ $result->keterangan }}</td>
                                        @if (auth()->user()->role != 'Read Only')
                                            <td>
                                                <center>
                                                    <button style="margin-right: 6px; margin-bottom: 3px;"
                                                        type="submit" data-toggle="modal"
                                                        data-target="#modalUpdateJenisRapat{{ $loop->iteration }}"
                                                        class="btn btn-warning btn-sm">edit
                                                        <center>
                                                            <!--   <form action="hapusjenisrapat" method="post">
                            <input type="hidden" name="id" value="{{ $result->id }}">
                            <button type="submit"class="btn btn-danger btn-sm" style="margin-right: 6px; margin-bottom: 3px;" onclick="return confirm('Are you sure?');">hapus</span>
                          </form> -->
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-partials.custom.datatable>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @foreach ($view as $result)
        <x-partials.forms.form-modal modalId="modalUpdateJenisRapat{{ $loop->iteration }}"
            modalTitle="Update Daftar Jenis Rapat" formId="formUpdateJenisRapat{{ $loop->iteration }}"
            formUrl="{{ route('admin.dashboard.master.jenis.update', ['id' => $result->id]) }}" formMethod="POST">
            <x-slot name="formBody">
                <div class="form-group">
                    <label class="col-sm-13 control-label"></label>
                    <div class="col-sm-12">
                        <b>Nama Jenis Rapat *</b>
                        <input type="text" class="form-control" name="nama" value="{{ $result->nama }}"
                            placeholder="Isikan Nama dari Jenis Rapat " required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-13 control-label"></label>
                    <div class="col-sm-12">
                        <b>Warna *</b>
                        <input type="color" class="form-control" name="kode_warna" value="{{ $result->kode_warna }}"
                            required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-13 control-label"></label>
                    <div class="col-sm-12">
                        <b>Status *</b>
                        <select name="status" class="form-control" value="{{ $result->status }}" required>
                            <option value='' disabled>Pilihan</option>
                            <option value='Aktif' @if ($result->status == 'Aktif') selected @endif>Aktif</option>
                            <option value='Non-Aktif' @if ($result->status == 'Non-Aktif') selected @endif>Non-Aktif
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-13 control-label"></label>
                    <div class="col-sm-12">
                        <b>Keterangan</b>
                        <input type="text" class="form-control" name="keterangan" value="{{ $result->keterangan }}"
                            placeholder="Isikan Keterangan Jenis Rapat">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary antosubmit">Simpan</button>
                </div>
            </x-slot>
        </x-partials.forms.form-modal>
    @endforeach

    <x-partials.forms.form-modal modalId="modalTambahJenisRapat" modalTitle="Update Daftar Jenis Rapat"
        formId="formTambahJenisRapat" formUrl="{{ route('admin.dashboard.master.jenis.save') }}" formMethod="POST">
        <x-slot name="formBody">
            <div class="form-group">
                <label class="col-sm-13 control-label"></label>
                <div class="col-sm-12">
                    <b>Nama Jenis Rapat *</b>
                    <input type="text" class="form-control" name="nama" placeholder="Isikan Nama dari Jenis Rapat"
                        required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-13 control-label"></label>
                <div class="col-sm-12">
                    <b>Warna *</b>
                    <input type="color" class="form-control" name="kode_warna" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-13 control-label"></label>
                <div class="col-sm-12">
                    <b>Status *</b>
                    <select name="status" class="form-control" required>
                        <option value='' disabled selected>Pilihan</option>
                        <option value='Aktif'>Aktif</option>
                        <option value='Non-Aktif'>Non-Aktif</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-13 control-label"></label>
                <div class="col-sm-12">
                    <b>Keterangan</b>
                    <input type="text" class="form-control" name="keterangan"
                        placeholder="Isikan Keterangan Jenis Rapat">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary antosubmit">Tambahkan</button>
            </div>
        </x-slot>
    </x-partials.forms.form-modal>

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                @if (Session::has('success'))
                    setTimeout(function() {
                        swal("{{ Session::get('success') }}");
                    }, 1000);
                @endif
            });
        </script>
    </x-slot>
</x-layouts.app>
