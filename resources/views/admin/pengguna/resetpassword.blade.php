<x-layouts.app>
    <x-slot name="slot">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h3 class="mt-4">Reset Password Pengguna</h3>
                    <div class="float-right">
                        <a type="button" href="{{ route('admin.dashboard.master.pengguna.index') }}"
                            class="btn btn-outline-info">Kembali</a>
                    </div>
                    <br><br>
                    <div class="card mb-4">
                        <div class="card-body offset-1 col-10">
                            <h5>Reset password untuk username : <b>{{ $master_user->master_user_nama }}</b></h5>
                            <form id="antoform" class="form-horizontal calender" role="form"
                                action="{{ route('admin.dashboard.master.pengguna.resetPassword', ['id' => $master_user->master_user_id]) }}"
                                method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ $master_user->master_user_id }}">
                                <div class="form-group">
                                    <label class="col-sm-13 control-label"></label>
                                    <div class="col-sm-12">
                                        <b>Masukkan Password *</b>
                                        <input type="master_user_password"
                                            class="form-control @error('master_user_password') form-control is-invalid @enderror"
                                            name="master_user_password" placeholder="Isikan Password Baru" required>
                                        @error('master_user_password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-13 control-label"></label>
                                    <div class="col-sm-12">
                                        <b>Konfirmasi Password *</b>
                                        <input type="master_user_password"
                                            class="form-control @error('konpassword') form-control is-invalid @enderror"
                                            name="konpassword" placeholder="Isikan Konfirmasi Password" required>
                                        @error('konpassword')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary antosubmit">Reset</button>
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
