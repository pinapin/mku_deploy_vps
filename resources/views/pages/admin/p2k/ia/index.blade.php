@extends('layouts.master')

@section('title', 'Data IA')

@push('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data IA</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="filter_tahun_akademik">Filter Tahun Akademik</label>
                                <div class="input-group">
                                    <select class="form-control select2" id="filter_tahun_akademik"
                                        name="filter_tahun_akademik">
                                        <option value="">Semua Tahun Akademik</option>
                                        @foreach ($tahunAkademiks as $tahunAkademik)
                                            <option value="{{ $tahunAkademik->id }}"
                                                {{ $tahunAktif && $tahunAkademik->id == $tahunAktif->id ? 'selected' : '' }}>
                                                {{ $tahunAkademik->tahun_ajaran }} {{ $tahunAkademik->tipe_semester }}
                                                @if ($tahunAkademik->is_aktif)
                                                    (Aktif)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" id="btn-reset-filter">
                                            <i class="fas fa-sync-alt"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table id="ia-table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th class="tahun-akademik-column" style="display: none;">Tahun Akademik</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Kelas</th>
                                <th>File IA</th>
                                {{-- <th width="15%">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be filled by DataTable -->
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Modal Detail IA -->
    <div class="modal fade" id="modal-detail-ia">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail IA</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">NIM</th>
                            <td id="detail-nim"></td>
                        </tr>
                        <tr>
                            <th>Nama Mahasiswa</th>
                            <td id="detail-nama"></td>
                        </tr>
                        <tr>
                            <th>Tahun Akademik</th>
                            <td id="detail-tahun-akademik"></td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td id="detail-kelas"></td>
                        </tr>
                        <tr>
                            <th>Kelompok</th>
                            <td id="detail-kelompok"></td>
                        </tr>
                        <tr>
                            <th>Tanggal Upload</th>
                            <td id="detail-tanggal"></td>
                        </tr>
                        <tr>
                            <th>File Laporan Akhir</th>
                            <td id="detail-file-laporan"></td>
                        </tr>
                        <tr>
                            <th>File PKS</th>
                            <td id="detail-file-pks"></td>
                        </tr>
                        <tr>
                            <th>File IA</th>
                            <td id="detail-file-ia"></td>
                        </tr>
                        <tr>
                            <th>Status Validasi</th>
                            <td id="detail-status"></td>
                        </tr>
                        <tr>
                            <th>Tanggal Validasi</th>
                            <td id="detail-tanggal-validasi"></td>
                        </tr>
                        <tr>
                            <th>Validator</th>
                            <td id="detail-validator"></td>
                        </tr>
                        <tr>
                            <th>Catatan Validasi</th>
                            <td id="detail-catatan"></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Moment.js -->
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>

    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Initialize DataTable
            var table = $('#ia-table').DataTable({
                processing: true,
                serverSide: false,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('p2k.ia.data') }}",
                    type: "GET",
                    data: function(d) {
                        d.tahun_akademik_id = $('#filter_tahun_akademik').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal'
                    },
                    {
                        data: 'tahun_akademik',
                        className: 'tahun-akademik-column',
                        visible: false
                    },
                    {
                        data: 'nim'
                    },
                    {
                        data: 'nama_mahasiswa'
                    },
                    {
                        data: 'kelas'
                    },
                    {
                        data: 'file_ia',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    // {
                    //     data: 'aksi',
                    //     orderable: false,
                    //     searchable: false,
                    //     className: 'text-center'
                    // }
                ],
                drawCallback: function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Filter by tahun akademik
            $('#filter_tahun_akademik').change(function() {
                var selectedValue = $(this).val();

                // Show/hide tahun akademik column based on filter selection
                var tahunAkademikColumnIndex = 2; // Index of tahun akademik column (0-based)

                if (selectedValue === '') {
                    // Show tahun akademik column when "Semua Tahun Akademik" is selected
                    table.column(tahunAkademikColumnIndex).visible(true);
                    $('.tahun-akademik-column').show();
                } else {
                    // Hide tahun akademik column when specific tahun akademik is selected
                    table.column(tahunAkademikColumnIndex).visible(false);
                    $('.tahun-akademik-column').hide();
                }

                table.ajax.reload();
            });

            // Reset filter
            $('#btn-reset-filter').click(function() {
                $('#filter_tahun_akademik').val('').trigger('change');
            });

            // Initial check for tahun akademik column visibility
            $('#filter_tahun_akademik').trigger('change');

            // Handle view detail
            $('#ia-table').on('click', '.btn-detail', function() {
                var id = $(this).data('id');

                // Taruh di atas fungsi AJAX
                var storageUrl = "{{ asset('storage') }}";

                $.ajax({
                    url: "{{ url('p2k/ia') }}/" + id,
                    type: "GET",
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;

                            $('#detail-nim').text(data.nim || '-');
                            $('#detail-nama').text(data.mahasiswa ? data.mahasiswa
                                .nama : '-');
                            $('#detail-tahun-akademik').text(data.tahun_akademik ?
                                data.tahun_akademik.tahun_ajaran + ' ' + data.tahun_akademik
                                .tipe_semester : '-');
                            $('#detail-kelas').text(data.kelas || '-');
                            $('#detail-kelompok').text(data.kelompok ? 'Kelompok ' + data
                                .kelompok : '-');
                            $('#detail-tanggal').text(data.created_at ? moment(data.created_at)
                                .format('DD MMMM YYYY') : '-');

                            // File Laporan Akhir
                            if (data.file_path) {
                                $('#detail-file-laporan').html(
                                    '<a href="' + storageUrl + '/' + data.file_path +
                                    '" target="_blank" class="btn btn-sm btn-info">' +
                                    '<i class="fas fa-file-pdf"></i> Lihat</a>'
                                );
                            } else {
                                $('#detail-file-laporan').text('-');
                            }

                            // File PKS
                            if (data.file_pks) {
                                $('#detail-file-pks').html(
                                    '<a href="' + storageUrl + '/' + data.file_pks +
                                    '" target="_blank" class="btn btn-sm btn-info">' +
                                    '<i class="fas fa-file-pdf"></i> Lihat</a>'
                                );
                            } else {
                                $('#detail-file-pks').text('-');
                            }

                            // File IA
                            if (data.file_ia) {
                                $('#detail-file-ia').html(
                                    '<a href="' + storageUrl + '/' + data.file_ia +
                                    '" target="_blank" class="btn btn-sm btn-info">' +
                                    '<i class="fas fa-file-pdf"></i> Lihat</a>'
                                );
                            } else {
                                $('#detail-file-ia').text('-');
                            }

                            // Status Validasi
                            if (data.is_validated) {
                                $('#detail-status').html(
                                    '<span class="badge badge-success">' +
                                    '<i class="fas fa-check"></i> Tervalidasi</span>'
                                );
                            } else {
                                $('#detail-status').html(
                                    '<span class="badge badge-warning">' +
                                    '<i class="fas fa-clock"></i> Menunggu Validasi</span>'
                                );
                            }

                            $('#detail-tanggal-validasi').text(data.validated_at ?
                                moment(data.validated_at).format('DD MMMM YYYY HH:mm') : '-'
                                );
                            $('#detail-validator').text(data.validator ? data.validator
                                .nama_dosen : '-');
                            $('#detail-catatan').text(data.catatan_validasi || '-');

                            $('#modal-detail-ia').modal('show');
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON ? xhr.responseJSON.message :
                                'Terjadi kesalahan saat mengambil data',
                            icon: 'error',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            });

            // Handle view file IA directly
            $('#ia-table').on('click', '.btn-view-ia', function() {
                // This will open the IA file directly in new tab
                return true;
            });
        });
    </script>
@endpush
