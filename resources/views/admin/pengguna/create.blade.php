<x-layouts.app>
    <x-slot name="slot">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h3 class="mt-4">Tambah Pengguna</h3>
                    <div class="float-right">
                        <a type="button" href="{{ route('admin.dashboard.master.pengguna.index') }}"
                            class="btn btn-outline-info">Kembali</a>
                    </div>
                    <br><br>
                    <div class="card mb-4">
                        <div class="card-body container-fluid">
                            <form id="antoform" class="form-horizontal calender" role="form"
                                action="{{ route('admin.dashboard.master.pengguna.save') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <b>Username * (incasesensitive & no-spasi)</b>
                                        <input type="text"
                                            class="form-control @error('master_user_nama') is-invalid @enderror"
                                            name="master_user_nama" placeholder="Isikan Username Pengguna"
                                            value="{{ old('master_user_nama') }}" required>
                                        @error('master_user_nama')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <b>Pilih Bagian *</b>
                                        <select name="master_nama_bagian_id"
                                            class="form-control @error('master_nama_bagian_id') is-invalid @enderror"
                                            required>
                                            <option value="" disabled selected>Pilih Bagian</option>
                                            @foreach ($bagian as $b)
                                                <option value="{{ $b->master_bagian_id }}"
                                                    {{ old('master_nama_bagian_id') == $b->master_bagian_id ? 'selected' : '' }}>
                                                     {{ $b->nama_regional }} - {{ $b->master_bagian_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('master_nama_bagian_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- <div class="form-group col-md-6">
                                        <b>Role *</b>
                                        <select name="role"
                                            class="form-control @error('role') form-control is-invalid @enderror"
                                            required>
                                            <!-- atribut selected untuk mempertahankan inputan saat validation error terjadi -->
                                            <option value="" disabled {{ old('role') == '' ? 'selected' : null }}>Pilih Role
                                                Pengguna</option>
                                            <option value="Super Admin"
                                                {{ old('role') == 'Super Admin' ? 'selected' : null }}>Super
                                                Admin</option>
                                            <option value="Admin Umum"
                                                {{ old('role') == 'Admin Umum' ? 'selected' : null }}>Admin Umum
                                            </option>
                                            <option value="Admin Sekper"
                                                {{ old('role') == 'Admin Sekper' ? 'selected' : null }}>Admin
                                                Sekper</option>
                                            <option value="Read Only"
                                                {{ old('role') == 'Read Only' ? 'selected' : null }}>Read Only
                                            </option>
                                        </select>
                                        @error('role')
                                            {{ $message }}
                                        @enderror
                                    </div> --}}
                                </div>
                                <div class="row">
                                    {{-- <div class="form-group col-md-6">
                                        <b>Petugas *</b>
                                        <select name="petugas"
                                            class="form-control @error('petugas') form-control is-invalid @enderror"
                                            required>
                                            <option value='' disabled
                                                {{ old('petugas') == '' ? 'selected' : null }}>Pilihan</option>
                                            <!-- atribut selected untuk mempertahankan inputan saat validation error terjadi -->
                                            <option value='TI' {{ old('petugas') == 'TI' ? 'selected' : null }}>TI
                                            </option>
                                            <option value='Umum' {{ old('petugas') == 'Umum' ? 'selected' : null }}>
                                                Umum</option>
                                            <option value='Driver'
                                                {{ old('petugas') == 'Driver' ? 'selected' : null }}>Driver
                                            </option>
                                            <option value='Lain-lain'
                                                {{ old('petugas') == 'Lain-lain' ? 'selected' : null }}>Lain-lain
                                            </option>
                                        </select>
                                        @error('petugas')
                                            {{ $message }}
                                        @enderror
                                    </div> --}}
                                    <div class="form-group col-md-6">
                                        <b>No. Handphone *</b>
                                        <input type="number"
                                            class="form-control @error('master_user_no_hp') form-control is-invalid @enderror"
                                            name="master_user_no_hp" placeholder="Isikan No. Handphone"
                                            value="{{ old('master_user_no_hp') }}" required>
                                        @error('master_user_no_hp')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <b>Pilih Hak Akses *</b>
                                        <select name="master_hak_akses_id"
                                            class="form-control @error('master_hak_akses_id') is-invalid @enderror"
                                            required>
                                            <option value="" disabled selected>Pilih Hak Akses</option>
                                            @foreach ($hak_akses as $h)
                                                <option value="{{ $h->hak_akses_id }}"
                                                    {{ old('master_hak_akses_id') == $h->hak_akses_id ? 'selected' : '' }}>
                                                    {{ $h->hak_akses_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('master_hak_akses_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <b>Status *</b>
                                        <select name="master_user_status"
                                            class="form-control @error('master_user_status') is-invalid @enderror"
                                            required>
                                            <option value=""
                                                {{ old('master_user_status') == '' ? 'selected' : '' }} disabled>Pilih
                                                Status Pengguna</option>
                                            <option value="1"
                                                {{ old('master_user_status') == '1' ? 'selected' : '' }}>Aktif</option>
                                            <option value="0"
                                                {{ old('master_user_status') == '0' ? 'selected' : '' }}>Non-Aktif
                                            </option>
                                        </select>
                                        @error('master_user_status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    {{-- <div class="form-group col-md-6">
                                        <b>NIK</b>
                                        <input type="number" class="form-control" name="nik"
                                            placeholder="Isikan NIK" value="{{ old('nik') }}">
                                    </div> --}}
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <b>Password *</b>
                                        <input type="password"
                                            class="form-control @error('master_user_password') form-control is-invalid' @enderror"
                                            name="master_user_password" placeholder="Isikan password"
                                            value="{{ old('master_user_password') }}" required>
                                        @error('master_user_password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    <div class="form-group col-md-6">
                                        <b>Konfirmasi Password *</b>
                                        <input type="password"
                                            class="form-control @error('konpassword') form-control is-invalid @enderror"
                                            name="konpassword" placeholder="Isikan konfirmasi password"
                                            value="{{ old('konpassword') }}" required>
                                        @error('konpassword')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <b>Keterangan</b>
                                        <input type="text" class="form-control" name="master_user_keterangan"
                                            placeholder="Isikan keterangan"
                                            value="{{ old('master_user_keterangan') }}">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary antosubmit">Tambahkan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </div>
        </main>
        </div>
    </x-slot>
</x-layouts.app>
