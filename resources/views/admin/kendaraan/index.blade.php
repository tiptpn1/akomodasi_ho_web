<x-layouts.app>
    <x-slot name="slot">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h3 class="mt-4">Manajemen Kendaraan</h3>
                    @if (auth()->user()->role != 'Read Only')
                        <button type="button" data-toggle="modal" data-target="#tambahMasterKendaraan"
                            class="btn btn-success">Tambahkan</button><br>
                    @endif
                    <br>
                    <div class="card mb-4">
                        <div class="card-body">
                            <x-partials.custom.datatable idTable="dataTables-master-kendaraan" :serverSide=false>
                                <x-slot name="tableHead">
                                    <tr>
                                        <th>
                                            <center>No
                                        </th>
                                        <th>
                                            <center>No. Polisi Kendaraan
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
                                            <td>{{ $result->no_polisi }}</td>
                                            <td style="text-align: center;">{{ $result->status }}</td>
                                            <td>{{ $result->keterangan }}</td>
                                            @if (auth()->user()->role != 'Read Only')
                                                <td>
                                                    <center>
                                                        <button style="margin-right: 6px; margin-bottom: 3px;"
                                                            type="submit" data-toggle="modal"
                                                            data-target="#updateMasterKendaraan{{ $loop->iteration }}"
                                                            class="btn btn-warning btn-sm">edit
                                                            <center>
                                                                <!-- <form action="hapuskendaraan" method="post">
                                        <input type="hidden" name="id" value="$result->id">
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
            <x-partials.forms.form-modal modalId="updateMasterKendaraan{{ $loop->iteration }}"
                modalTitle="Update Daftar Kendaraan"
                formUrl="{{ route('admin.dashboard.master.kendaraan.update', ['id' => $result->id]) }}"
                formId="formUpdateKendaraan{{ $loop->iteration }}" formMethod="POST">
                <x-slot name="formBody">
                    <div class="form-group">
                        <label class="col-sm-13 control-label"></label>
                        <div class="col-sm-12">
                            <b>No. Polisi *</b>
                            <input type="text" class="form-control" name="no_polisi" value="{{ $result->no_polisi }}"
                                placeholder="Isikan No. Polisi Kendaraan " required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-13 control-label"></label>
                        <div class="col-sm-12">
                            <b>Status *</b>
                            <select name="status" class="form-control" value="$result->status" required>
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
                                placeholder="Isikan Keterangan">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary antosubmit">Simpan</button>
                    </div>
                </x-slot>
            </x-partials.forms.form-modal>
        @endforeach

        <x-partials.forms.form-modal modalId="tambahMasterKendaraan" modalTitle="Tambah Kendaraan"
            formUrl="{{ route('admin.dashboard.master.kendaraan.save') }}" formId="formTambahKendaraan"
            formMethod="POST">
            <x-slot name="formBody">
                <div class="form-group">
                    <label class="col-sm-13 control-label"></label>
                    <div class="col-sm-12">
                        <b>No.Polisi Kendaraan *</b>
                        <input type="text" class="form-control" name="no_polisi" placeholder="Isikan Kendaraan"
                            required>
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
                        <input type="text" class="form-control" name="keterangan" placeholder="Isikan Keterangan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default antoclose" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary antosubmit">Tambahkan</button>
                </div>
            </x-slot>
        </x-partials.forms.form-modal>
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
