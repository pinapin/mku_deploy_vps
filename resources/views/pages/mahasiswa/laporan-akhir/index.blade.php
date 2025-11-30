@extends('layouts.master')

@section('title', 'Laporan Akhir')

@push('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Perhatian!</strong> Setiap mahasiswa hanya diperbolehkan
                mengupload satu laporan akhir per tahun akademik. Laporan yang sudah divalidasi oleh dosen tidak dapat
                diubah atau dihapus.
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Laporan Akhir</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-upload-laporan">
                            <i class="fas fa-plus"></i> Upload Laporan Akhir
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table-laporan" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tahun Akademik</th>
                                <th>Kelas</th>
                                <th>Kelompok</th>
                                <th>Status Validasi</th>
                                <th>Tanggal Validasi</th>
                                <th>Validator</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title">Konfirmasi Hapus</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus laporan akhir ini?</p>
                    <input type="hidden" id="delete-id">
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btn-confirm-delete">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Upload Laporan -->
    <div class="modal fade" id="modal-upload-laporan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Laporan Akhir</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-upload-laporan" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="tahun_akademik_id">Tahun Akademik</label>
                            <select class="form-control select2" id="tahun_akademik_id" name="tahun_akademik_id" required>
                                <option value="">-- Pilih Tahun Akademik --</option>
                                @foreach ($suratPengantars->unique('tahun_akademik_id') as $suratPengantar)
                                    <option value="{{ $suratPengantar->tahun_akademik_id }}"
                                        {{ $tahunAkademikAktif && $tahunAkademikAktif->id == $suratPengantar->tahun_akademik_id ? 'selected' : '' }}>
                                        {{ $suratPengantar->tahunAkademik->tahun_ajaran }}
                                        {{ $suratPengantar->tahunAkademik->tipe_semester }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="surat_pengantar_id">Kelas</label>
                            <select class="form-control select2" id="surat_pengantar_id" name="surat_pengantar_id" required>
                                <option value="">-- Pilih Kelas --</option>
                                <!-- Options will be loaded dynamically -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="file_laporan">File Laporan (PDF)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file_laporan" name="file_laporan"
                                        accept=".pdf" required>
                                    <label class="custom-file-label" for="file_laporan">Pilih file</label>
                                </div>
                            </div>
                            <small class="text-muted">Maksimal ukuran file 10MB</small>
                        </div>
                        @if ($cekpks)
                            @if ($uploadPks)
                                <input type="hidden" name="file_pks" id="" value="{{ $cekpks->file_arsip_pks }}">
                            @endif
                        @else
                            <div class="form-group">
                                <label for="file_pks">File PKS Bertanda Tangan Basah (PDF)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file_pks" name="file_pks"
                                            accept=".pdf" required>
                                        <label class="custom-file-label" for="file_pks">Pilih file</label>
                                    </div>
                                </div>
                                <small class="text-muted">Maksimal ukuran file 10MB</small>
                            </div>
                        @endif
                        <!-- Template IA Info Box -->
                        <div class="alert alert-info bg-gradient-info" style="border-left: 4px solid #17a2b8;">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-info-circle fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="alert-heading mb-2">📄 Template Dokumen IA</h6>
                                    <p class="mb-2">Download template dokumen IA terlebih dahulu untuk memudahkan proses pembuatan IA!</p>
                                    <a style="text-decoration: none;" href="https://docs.google.com/document/d/12b8NzSINAvBFMLUDG9LIa0turrGqrba5/edit?usp=sharing&ouid=106181426321785289225&rtpof=true&sd=true"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-light">
                                        <i class="fas fa-download mr-1"></i> Download Template
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="file_ia">File IA (PDF)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file_ia" name="file_ia"
                                        accept=".pdf" required>
                                    <label class="custom-file-label" for="file_ia">Pilih file</label>
                                </div>
                            </div>
                            <small class="text-muted">Maksimal ukuran file 10MB</small>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Update Laporan -->
    <div class="modal fade" id="modal-update-laporan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Laporan Akhir</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-update-laporan" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="update_laporan_id" name="update_laporan_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="update_file_laporan">File Laporan Baru (PDF)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="update_file_laporan"
                                        name="file_laporan" accept=".pdf">
                                    <label class="custom-file-label" for="update_file_laporan">Pilih file</label>
                                </div>
                            </div>
                            <small class="text-muted">Maksimal ukuran file 10MB. Biarkan kosong jika tidak ingin mengubah
                                file laporan.</small>
                        </div>
                        {{-- <div class="form-group">
                            <label for="update_file_pks">File PKS Bertanda Tangan Basah Baru (PDF)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="update_file_pks" name="file_pks"
                                        accept=".pdf">
                                    <label class="custom-file-label" for="update_file_pks">Pilih file</label>
                                </div>
                            </div>
                            <small class="text-muted">Maksimal ukuran file 10MB. Biarkan kosong jika tidak ingin mengubah
                                file PKS.</small>
                        </div> --}}
                        <div class="form-group">
                            <label for="update_file_ia">File IA Baru (PDF)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="update_file_ia" name="file_ia"
                                        accept=".pdf">
                                    <label class="custom-file-label" for="update_file_ia">Pilih file</label>
                                </div>
                            </div>
                            <small class="text-muted">Maksimal ukuran file 10MB. Biarkan kosong jika tidak ingin mengubah
                                file IA.</small>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Laporan -->
    <div class="modal fade" id="modal-detail-laporan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Laporan Akhir</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
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
                            <th>Status Validasi</th>
                            <td id="detail-status-validasi"></td>
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
                            <td id="detail-catatan-validasi"></td>
                        </tr>
                        <tr>
                            <th>File Laporan</th>
                            <td>
                                <a id="detail-file-laporan" href="#" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-pdf"></i> Lihat File
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>File PKS</th>
                            <td>
                                <a id="detail-file-pks" href="#" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-pdf"></i> Lihat File
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>File IA</th>
                            <td>
                                <a id="detail-file-ia" href="#" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-pdf"></i> Lihat File
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- bs-custom-file-input -->
    <script src="{{ asset('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Initialize bs-custom-file-input
            bsCustomFileInput.init();

            // Initialize DataTable
            var table = $('#table-laporan').DataTable({
                processing: true,
                serverSide: false,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('mahasiswa.laporan-akhir.data') }}",
                    type: "GET",
                    dataSrc: function(json) {

                        if (json.hasLaporanAkhir) {
                            $('#btn-upload-laporan').hide();
                        }
                        return json.data;
                    }

                    // Cek apakah mahasiswa sudah memiliki laporan akhir

                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'tahun_akademik'
                    },
                    {
                        data: 'kelas'
                    },
                    {
                        data: 'kelompok'
                    },
                    {
                        data: 'is_validated',
                        render: function(data, type, row) {
                            if (data) {
                                return '<span class="badge badge-success"><i class="fas fa-check"></i> Tervalidasi</span>';
                            } else {
                                return '<span class="badge badge-warning"><i class="fas fa-clock"></i> Menunggu Validasi</span>';
                            }
                        }
                    },
                    {
                        data: 'validated_at',
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: 'validator',
                        render: function(data, type, row) {
                            return data ? data : '-';
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            var viewBtn = '<a href="' + '{{ asset('storage/') }}/' + row
                                .file_path +
                                '" target="_blank" class="btn btn-info btn-sm mr-1" data-toggle="tooltip" title="Lihat File"><i class="fas fa-eye"></i></a>';
                            var detailBtn =
                                '<button class="btn btn-primary btn-sm mr-1 btn-detail" data-id="' +
                                row.id +
                                '" data-toggle="tooltip" title="Detail"><i class="fas fa-info-circle"></i></button>';
                            var updateBtn = '';
                            var deleteBtn = '';

                            if (!row.is_validated) {
                                updateBtn =
                                    '<button class="btn btn-warning btn-sm mr-1 btn-update" data-id="' +
                                    row.id +
                                    '" data-toggle="tooltip" title="Update"><i class="fas fa-edit"></i></button>';
                                deleteBtn =
                                    '<button class="btn btn-danger btn-sm btn-delete" data-id="' +
                                    row.id +
                                    '" data-toggle="tooltip" title="Hapus"><i class="fas fa-trash"></i></button>';
                            }

                            return viewBtn + detailBtn + updateBtn + deleteBtn;
                        }
                    }
                ],
                 "drawCallback": function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Handle button upload laporan
            $('#btn-upload-laporan').on('click', function() {
                @if (!$cekpks)
                    $('#modal-upload-laporan').modal('show');
                    return true;
                @else
                    @if (!$uploadPks)
                        Swal.fire({
                            title: 'Perhatian!',
                            text: 'Anda belum Upload File PKS di Menu PKS',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    @else
                        $('#modal-upload-laporan').modal('show');
                    @endif
                @endif
            });

            // Load Surat Pengantar berdasarkan Tahun Akademik
            $('#tahun_akademik_id').on('change', function() {
                var tahunAkademikId = $(this).val();
                $('#surat_pengantar_id').empty().append('<option value="">-- Pilih Kelas --</option>');

                if (tahunAkademikId) {
                    $.ajax({
                        url: "{{ route('mahasiswa.get-surat-pengantar') }}",
                        type: "GET",
                        data: {
                            tahun_akademik_id: tahunAkademikId
                        },
                        success: function(response) {
                            if (response.success) {
                                // Tambahkan opsi surat pengantar
                                response.data.forEach(function(item) {
                                    $('#surat_pengantar_id').append(
                                        '<option value="' +
                                        item.id + '">Kelompok ' + item
                                        .kelompok +
                                        ' - Kelas ' + item.kelas + '</option>');
                                });
                            }
                            $('#surat_pengantar_id').trigger('change');
                        },
                        error: function(xhr) {
                            console.error('Error loading surat pengantar:', xhr);
                        }
                    });
                } else {
                    $('#surat_pengantar_id').trigger('change');
                }
            });

            // Trigger change event untuk load surat pengantar saat halaman dimuat
            $('#tahun_akademik_id').trigger('change');

            // Handle form upload laporan
            $('#form-upload-laporan').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('mahasiswa.laporan-akhir.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Mohon Tunggu',
                            text: 'Sedang mengupload laporan...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reset form dan tutup modal
                            $('#form-upload-laporan')[0].reset();
                            $('#modal-upload-laporan').modal('hide');

                            // Reload table
                            table.ajax.reload();

                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON.message ||
                                    'Anda tidak memiliki akses untuk menghapus data ini',
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON.message ||
                                    'Terjadi kesalahan saat menghapus data',
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';

                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });

                            Swal.fire({
                                title: 'Error!',
                                html: errorMessage,
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON.message ||
                                    'Terjadi kesalahan saat menyimpan data',
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    },
                    complete: function() {
                        // Reset file input labels
                        $('#update_file_laporan').next('.custom-file-label').html(
                            'Pilih file');
                        $('#update_file_pks').next('.custom-file-label').html('Pilih file');
                    }
                });
            });

            // Handle button detail
            $('#table-laporan').on('click', '.btn-detail', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ url('mahasiswa/laporan-akhir') }}" + '/' + id,
                    type: "GET",
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;

                            $('#detail-tahun-akademik').text(data.tahun_akademik
                                .tahun_ajaran +
                                ' - ' + data.tahun_akademik.tipe_semester);
                            $('#detail-kelas').text(data.kelas);
                            $('#detail-kelompok').text(data.kelompok);
                            $('#detail-status-validasi').html(data.is_validated ?
                                '<span class="badge badge-success"><i class="fas fa-check"></i> Tervalidasi</span>' :
                                '<span class="badge badge-warning"><i class="fas fa-clock"></i> Menunggu Validasi</span>'
                            );
                            $('#detail-tanggal-validasi').text(data.validated_at ? moment(
                                data
                                .validated_at).format('DD-MM-YYYY HH:mm') : '-');
                            $('#detail-validator').text(data.validator ? data.validator
                                .nama_dosen : '-');
                            $('#detail-catatan-validasi').text(data.catatan_validasi ? data
                                .catatan_validasi : '-');
                            $('#detail-file-laporan').attr('href',
                                '{{ asset('storage/') }}/' +
                                data.file_path);
                            $('#detail-file-pks').attr('href', '{{ asset('storage/') }}/' +
                                data.file_pks);
                            $('#detail-file-ia').attr('href', '{{ asset('storage/') }}/' +
                                data
                                .file_ia);

                            $('#modal-detail-laporan').modal('show');
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
                            text: xhr.responseJSON.message ||
                                'Terjadi kesalahan saat mengambil data',
                            icon: 'error',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    },
                    complete: function() {
                        // Reset button state
                        $('#btn-confirm-delete').html('Hapus');
                        $('#btn-confirm-delete').attr('disabled', false);
                    }
                });
            });

            // Handle button update
            $('#table-laporan').on('click', '.btn-update', function() {
                var id = $(this).data('id');
                $('#update_laporan_id').val(id);
                $('#modal-update-laporan').modal('show');
            });

            // Handle form update laporan
            $('#form-update-laporan').on('submit', function(e) {
                e.preventDefault();

                var id = $('#update_laporan_id').val();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ url('mahasiswa/laporan-akhir') }}" + '/' + id,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        formData.append('_method', 'PUT');

                        Swal.fire({
                            title: 'Mohon Tunggu',
                            text: 'Sedang mengupdate laporan...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            // Reset form dan tutup modal
                            $('#form-update-laporan')[0].reset();
                            $('#modal-update-laporan').modal('hide');

                            // Reload table
                            table.ajax.reload();

                            const Toast = Swal.mixin({
                                toast: true,
                                position: "top-end",
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: "success",
                                title: response.message
                            });
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
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';

                            $.each(errors, function(key, value) {
                                errorMessage += value[0] + '<br>';
                            });

                            Swal.fire({
                                title: 'Error!',
                                html: errorMessage,
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON.message ||
                                    'Terjadi kesalahan saat menyimpan data',
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    }
                });
            });

            // Handle button delete
            $('#table-laporan').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                $('#delete-id').val(id);
                $('#modal-delete').modal('show');
            });

            // Handle Delete Confirmation
            $('#btn-confirm-delete').click(function() {
                var id = $('#delete-id').val();

                // Show loading state
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
                $(this).attr('disabled', true);

                $.ajax({
                    url: "{{ url('mahasiswa/laporan-akhir') }}" + '/' + id,
                    type: "DELETE",
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#modal-delete').modal('hide');
                        table.ajax.reload();

                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON.message ||
                                    'Anda tidak memiliki akses untuk menghapus data ini',
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: xhr.responseJSON.message ||
                                    'Terjadi kesalahan saat menghapus data',
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
