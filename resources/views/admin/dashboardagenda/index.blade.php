<x-layouts.app>
    <x-slot name="styles">
        <style>
            .color-legend {
                display: flex;
                flex-direction: row;
                justify-content: center;
                gap: 20px;
            }

            @media (max-width: 494px) {
                .color-legend {
                    flex-direction: column !important;
                }
            }

            .color-legend>div {
                display: flex;
                flex-direction: row;
                gap: 5px;
            }

            .square {
                border: 1px solid black;
                width: 20px;
                height: 20px;
            }

            .green {
                background-color: rgb(8, 158, 8);
            }

            .yellow {
                background-color: rgb(241, 197, 37);
            }

            .blue {
                background-color: rgb(21, 223, 210);
            }

            .grey {
                background-color: rgb(177, 177, 177);
            }

            th,
            td {
                font-size: 12px;
            }

            table {
                width: 100% !important;
            }

            .table-overflow-x {
                width: 100% !important;
                overflow-x: scroll !important;
            }

            .table-overflow-x::-webkit-scrollbar {
                height: 0;
                background: transparent;
            }
        </style>
    </x-slot>

    <x-slot name="slot">
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <h3 class="mt-4">PT Perkebunan Nusantara I <br /> <em>Head Office</em></h3>
                    <br>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row mb-2">
                                <b>Ruangan Rapat:</b>
                                <div class="ml-2">
                                    <select class="form-control" name="ruangan" id="ruanganRapat"
                                        style="height: 100% !important;">
                                        <option value="" disabled>--Pilih Lantai--</option>
                                        @foreach ($list_lantai as $lantai)
                                            <option value="{{ $lantai->lantai }}"
                                                @if ($loop->iteration == 1) selected @endif>Lantai
                                                {{ $lantai->lantai }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <b>Tanggal:</b>
                                <div class="ml-2">
                                    <input type="text" id="tanggal" name="tanggal" class="form-control"
                                        placeholder="Pilih Tanggal" style="height: 100% !important;">
                                </div>
                            </div>
                            <div id="agendaContent"></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </x-slot>

    <x-slot name="scripts">
        <script>
            var date;
            var lantai;
            var get_data = '';

            function setDateNow() {
                today = new Date();
                year = today.getFullYear();
                month = today.getMonth() + 1;
                date = today.getDate();

                $('#tanggal').val(`${month}/${date}/${year}`).trigger('change');
            }

            function fetchData() {
                if (get_data != '') {
                    get_data.abort();
                }

                $('#agendaContent').html(`
                        <div style="display: flex; flex-direction: row; justify-content: center;">
                            <em><i class="fas fa-spin fa-spinner"></i> Process</em>
                        </div>
                    `);

                get_data = $.ajax({
                    url: "{{ route('admin.agenda.content') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        lt: lantai,
                        date: date,
                    },
                    success: function(response) {
                        $('#agendaContent').html(response);
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status == 419) {
                            location.reload();
                        } else if (status != 'abort') {
                            $('#agendaContent').empty();
                            swal({
                                title: 'Error!',
                                text: 'Gagal Memuat!',
                                type: 'error',
                            }, function (confirm) {
                                fetchData();
                            })
                        }
                    }
                });
            }

            $(document).ready(function() {
                $('#tanggal').datepicker({});

                $('#ruanganRapat').on('change', function() {
                    lantai = $(this).val();

                    fetchData();
                });

                $('#tanggal').on('change', function() {
                    date = $(this).val();

                    fetchData();
                });

                setDateNow();

                $('#ruanganRapat').trigger('change');
            });
        </script>
    </x-slot>
</x-layouts.app>
