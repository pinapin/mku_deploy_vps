@extends('layouts.master')

@section('title', 'Data Sertifikat KWU')

@push('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        /* Custom dropdown styling */
        .animated--fade-in {
            animation-name: fadeIn;
            animation-duration: 200ms;
            animation-timing-function: opacity cubic-bezier(0, 1, 0.4, 1);
        }
        
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dropdown-item:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .dropdown-item .fas {
            transition: transform 0.2s ease;
        }
        
        .dropdown-item:hover .fas {
            transform: translateX(3px);
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Sertifikat KWU</h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle shadow-sm" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-file-excel mr-1"></i> Import/Export
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" role="menu" style="border-radius: 0.5rem;">
                            <a href="{{ route('kwu.sertifikat-kwu.import-template') }}" class="dropdown-item d-flex align-items-center py-2">
                                <i class="fas fa-download text-success mr-2"></i> 
                                <span>Download Template</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item d-flex align-items-center py-2" id="btn-import">
                                <i class="fas fa-upload text-primary mr-2"></i> 
                                <span>Import Data</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item d-flex align-items-center py-2" id="btn-validate">
                                <i class="fas fa-check-circle text-info mr-2"></i> 
                                <span>Validasi Data</span>
                            </a>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm ml-2 shadow-sm" id="btn-add">
                        <i class="fas fa-plus mr-1"></i> Tambah Sertifikat
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <!-- Filter Section -->
                <div class="card card-outline card-primary mb-4 collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Data</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-tahun">Tahun</label>
                                    <select class="form-control" id="filter-tahun">
                                        <option value="">Semua Tahun</option>
                                        @foreach($tahunList as $tahun)
                                            <option value="{{ $tahun }}">{{ $tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-semester">Semester</label>
                                    <select class="form-control" id="filter-semester">
                                        <option value="">Semua Semester</option>
                                        @foreach($semesterList as $semester)
                                            <option value="{{ $semester }}">{{ $semester }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-fakultas">Fakultas</label>
                                    <select class="form-control" id="filter-fakultas">
                                        <option value="">Semua Fakultas</option>
                                        @foreach($fakultas as $fak)
                                            <option value="{{ $fak->id }}">{{ $fak->nama_fakultas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filter-prodi">Program Studi</label>
                                    <select class="form-control select2" id="filter-prodi" style="width: 100%;">
                                        <option value="">Semua Program Studi</option>
                                        @foreach ($programStudis as $prodi)
                                            <option value="{{ $prodi->id }}"
                                                data-fakultas="{{ $prodi->fakultas_id }}">
                                                {{ $prodi->nama_prodi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-right">
                                <button type="button" id="btn-reset-filter" class="btn btn-default">
                                    <i class="fas fa-undo"></i> Reset Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Filter Section -->
                
                <table id="sertifikat-kwu-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>No Sertifikat</th>
                            <th>Tanggal Sertifikat</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Program Studi</th>
                            <th>Fakultas</th>
                            <th>Semester</th>
                            <th>Tahun</th>
                            <th>Keterangan</th>
                            <th width="15%">Aksi</th>
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

<!-- Modal Form -->
<div class="modal fade" id="modal-form">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Tambah Sertifikat KWU</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-sertifikat" class="form-horizontal">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="sertifikat-id">
                    <div class="form-group">
                        <label for="no_sertifikat" class="form-label">No Sertifikat</label>
                        <input type="text" class="form-control" id="no_sertifikat" name="no_sertifikat"
                            placeholder="Masukkan nomor sertifikat" maxlength="20" required>
                        <div class="invalid-feedback" id="error-no_sertifikat"></div>
                    </div>
                    <div class="form-group">
                        <label for="tgl_sertifikat" class="form-label">Tanggal Sertifikat</label>
                        <input type="date" class="form-control" id="tgl_sertifikat" name="tgl_sertifikat"
                            placeholder="Masukkan tanggal sertifikat" required>
                        <div class="invalid-feedback" id="error-tgl_sertifikat"></div>
                    </div>
                    <div class="form-group">
                        <label for="nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="nim" name="nim"
                            placeholder="Masukkan NIM" maxlength="9" required>
                        <div class="invalid-feedback" id="error-nim"></div>
                    </div>
                    <div class="form-group">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama"
                            placeholder="Masukkan nama" required>
                        <div class="invalid-feedback" id="error-nama"></div>
                    </div>
                    <div class="form-group">
                        <label for="prodi_id" class="form-label">Program Studi</label>
                        <select class="form-control select2" id="prodi_id" name="prodi_id" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($programStudis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="error-prodi_id"></div>
                    </div>

                    <div class="form-group">
                        <label for="semester" class="form-label">Semester</label>
                        <select class="form-control" id="semester" name="semester" required>
                            <option value="">-- Pilih Semester --</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                        <div class="invalid-feedback" id="error-semester"></div>
                    </div>
                    <div class="form-group">
                        <label for="tahun" class="form-label">Tahun</label>
                        <input type="text" class="form-control" id="tahun" name="tahun"
                            placeholder="Masukkan tahun (contoh: 2023/2024)" required>
                        <div class="invalid-feedback" id="error-tahun"></div>
                    </div>
                    <div class="form-group">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan"
                            placeholder="Masukkan keterangan (opsional)" rows="3"></textarea>
                        <div class="invalid-feedback" id="error-keterangan"></div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Import Modal -->
<div class="modal fade" id="modal-import">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Import Data Sertifikat KWU</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-import" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="file" class="form-label">File Excel</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx,.xls" required>
                                <label class="custom-file-label" for="file">Pilih file</label>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="error-file"></div>
                        <small class="text-muted">Download template terlebih dahulu untuk format yang benar.</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btn-import-submit">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
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
                <p>Apakah Anda yakin ingin menghapus sertifikat ini?</p>
                <input type="hidden" id="delete-id">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete">Hapus</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Validate Data Modal -->
<div class="modal fade" id="modal-validate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Validasi Data Sertifikat KWU</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-validate" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="validate-file" class="form-label">File Excel</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="validate-file" name="file" accept=".xlsx,.xls" required>
                                <label class="custom-file-label" for="validate-file">Pilih file</label>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="error-validate-file"></div>
                        <small class="text-muted">Upload file Excel yang berisi NIM dan Nama untuk divalidasi.</small>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-validate-submit">Validasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Validation Result Modal -->
<div class="modal fade" id="modal-validation-result">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Hasil Validasi Data</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-file-excel"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Data</span>
                                <span class="info-box-number" id="total-data">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Data Cocok</span>
                                <span class="info-box-number" id="matched-data">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Data Tidak Cocok</span>
                                <span class="info-box-number" id="unmatched-data">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-success btn-lg" id="btn-export-validation">
                        <i class="fas fa-file-excel mr-1"></i> Unduh Hasil Validasi
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input@1.3.4/dist/bs-custom-file-input.min.js"></script>
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(function() {
        // Initialize Select2 for modal form
        $('.select2').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#modal-form')
        });
        
        // Initialize Select2 for filters
        $('#filter-tahun, #filter-semester, #filter-fakultas').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // Initialize Select2 for prodi filter with templateResult
            $('#filter-prodi').select2({
                theme: 'bootstrap4',
                width: '100%',
                templateResult: function (data) {
                    // Jika opsi disabled, jangan tampilkan
                    if (data.element && $(data.element).prop('disabled')) {
                        return null;
                    }
                    return data.text;
                }
            });

        // Initialize bs-custom-file-input
        bsCustomFileInput.init();

        // Initialize DataTable with ServerSide Processing
        var table = $('#sertifikat-kwu-table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "language": {
                "processing": "<i class='fas fa-spinner fa-spin fa-2x'></i>",
                // "searchPlaceholder": "Cari data...",
                // "lengthMenu": "Tampilkan _MENU_ data per halaman",
                // "zeroRecords": "Data tidak ditemukan",
                // "info": "Menampilkan halaman _PAGE_-__ dari _MAX_ total data",
                // "infoEmpty": "Tidak ada data yang tersedia",
                // "infoFiltered": "(difilter dari _MAX_ total data)",
                // "paginate": {
                //     "first": "Pertama",
                //     "last": "Terakhir",
                //     "next": "Selanjutnya",
                //     "previous": "Sebelumnya"
                // }
            },
            "ajax": {
                "url": "{{ route('kwu.sertifikat-kwu.data') }}",
                "type": "GET",
                "data": function(d) {
                    d.tahun = $('#filter-tahun').val();
                    d.semester = $('#filter-semester').val();
                    d.fakultas_id = $('#filter-fakultas').val();
                    d.prodi_id = $('#filter-prodi').val();
                }
            },
            "columns": [{
                    "data": "DT_RowIndex",
                    "name": "DT_RowIndex",
                    "orderable": false,
                    "searchable": false
                },
                {
                    "data": "no_sertifikat",
                    "name": "no_sertifikat"
                },
                {
                    "data": "tgl_sertifikat",
                    "name": "tgl_sertifikat"
                },
                {
                    "data": "nim",
                    "name": "nim"
                },
                {
                    "data": "nama",
                    "name": "nama"
                },
                {
                    "data": "nama_prodi",
                    "name": "nama_prodi",
                    "defaultContent": "<span class='badge badge-warning'>Tidak Ada</span>"
                },
                {
                    "data": "nama_fakultas",
                    "name": "nama_fakultas",
                    "defaultContent": "<span class='badge badge-warning'>Tidak Ada</span>"
                },
                {
                    "data": "semester",
                    "name": "semester"
                },
                {
                    "data": "tahun",
                    "name": "tahun"
                },
                {
                    "data": "keterangan",
                    "name": "keterangan",
                    "defaultContent": "-"
                },
                {
                    "data": "action",
                    "name": "action",
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center"
                }
            ],
            // "order": [[1, 'asc']],
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            "drawCallback": function(settings) {
                // Initialize tooltips after each draw
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Show Add Modal
        $('#btn-add').click(function() {
            $('#form-sertifikat').trigger('reset');
            $('#sertifikat-id').val('');
            $('#modal-title').text('Tambah Sertifikat KWU');
            $('#modal-form').modal('show');
            clearErrors();
        });

        // Show Import Modal
        $('#btn-import').click(function() {
            $('#form-import').trigger('reset');
            $('.custom-file-label').text('Pilih file');
            $('#modal-import').modal('show');
            clearErrors();
        });

        // Show Edit Modal
        $('#sertifikat-kwu-table').on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $('#sertifikat-id').val(id);
            $('#modal-title').text('Edit Sertifikat KWU');
            clearErrors();

            // Get data from server
            $.ajax({
                url: `{{ url('kwu/sertifikat-kwu') }}/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const data = response.data;
                    $('#no_sertifikat').val(data.no_sertifikat);
                    $('#tgl_sertifikat').val(data.tgl_sertifikat);
                    $('#nim').val(data.nim);
                    $('#nama').val(data.nama);
                    $('#prodi_id').val(data.prodi_id).trigger('change');
                    $('#semester').val(data.semester);
                    $('#tahun').val(data.tahun);
                    $('#keterangan').val(data.keterangan);

                    $('#modal-form').modal('show');
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
                }
            });
        });

        // Show Delete Modal
        $('#sertifikat-kwu-table').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            $('#delete-id').val(id);
            $('#modal-delete').modal('show');
        });

        // Handle Form Submit
        $('#form-sertifikat').submit(function(e) {
            e.preventDefault();
            var id = $('#sertifikat-id').val();
            var url = id ? `{{ url('kwu/sertifikat-kwu') }}/${id}` : "{{ route('kwu.sertifikat-kwu.store') }}";
            var method = id ? 'PUT' : 'POST';

            // Show loading state
            $('#btn-save').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            $('#btn-save').attr('disabled', true);
            clearErrors();

            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: method,
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    $('#modal-form').modal('hide');
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
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON;
                        if (errors.message) {
                            // Handle single error message
                            if (errors.message.includes('no_sertifikat')) {
                                $('#error-no_sertifikat').text(errors.message);
                                $('#no_sertifikat').addClass('is-invalid');
                            } else if (errors.message.includes('tgl_sertifikat')) {
                                $('#error-tgl_sertifikat').text(errors.message);
                                $('#tgl_sertifikat').addClass('is-invalid');
                            } else if (errors.message.includes('nim')) {
                                $('#error-nim').text(errors.message);
                                $('#nim').addClass('is-invalid');
                            } else if (errors.message.includes('nama')) {
                                $('#error-nama').text(errors.message);
                                $('#nama').addClass('is-invalid');
                            } else if (errors.message.includes('prodi_id')) {
                                $('#error-prodi_id').text(errors.message);
                                $('#prodi_id').addClass('is-invalid');
                            } else if (errors.message.includes('semester')) {
                                $('#error-semester').text(errors.message);
                                $('#semester').addClass('is-invalid');
                            } else if (errors.message.includes('tahun')) {
                                $('#error-tahun').text(errors.message);
                                $('#tahun').addClass('is-invalid');
                            } else if (errors.message.includes('keterangan')) {
                                $('#error-keterangan').text(errors.message);
                                $('#keterangan').addClass('is-invalid');
                            } else {
                                // General error
                                Swal.fire({
                                    title: 'Error!',
                                    text: errors.message,
                                    icon: 'error',
                                    position: 'top-end',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        }
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
                    // Reset button state
                    $('#btn-save').html('Simpan');
                    $('#btn-save').attr('disabled', false);
                }
            });
        });

        // Handle Import Form Submit
        $('#form-import').submit(function(e) {
            e.preventDefault();
            
            // Show loading state
            $('#btn-import-submit').html('<i class="fas fa-spinner fa-spin"></i> Mengimpor...');
            $('#btn-import-submit').attr('disabled', true);
            clearErrors();

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('kwu.sertifikat-kwu.import') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#modal-import').modal('hide');
                    table.ajax.reload();

                    if (response.status === 'warning') {
                        // Show warning with errors
                        Swal.fire({
                            title: 'Perhatian!',
                            text: response.message,
                            icon: 'warning',
                            confirmButtonText: 'Lihat Detail',
                            showCancelButton: true,
                            cancelButtonText: 'Tutup'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show error details
                                let errorList = '<ul>';
                                response.errors.forEach(function(error) {
                                    errorList += `<li>${error}</li>`;
                                });
                                errorList += '</ul>';
                                
                                Swal.fire({
                                    title: 'Detail Error',
                                    html: errorList,
                                    icon: 'info'
                                });
                            }
                        });
                    } else {
                        // Show success message
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
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON;
                        if (errors.message) {
                            if (errors.message.includes('file')) {
                                $('#error-file').text(errors.message);
                                $('#file').addClass('is-invalid');
                            } else {
                                // General error
                                Swal.fire({
                                    title: 'Error!',
                                    text: errors.message,
                                    icon: 'error',
                                    position: 'top-end',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        }
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message ||
                                'Terjadi kesalahan saat mengimpor data',
                            icon: 'error',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                },
                complete: function() {
                    // Reset button state
                    $('#btn-import-submit').html('Import');
                    $('#btn-import-submit').attr('disabled', false);
                }
            });
        });

        // Handle Delete Confirmation
        $('#btn-confirm-delete').click(function() {
            var id = $('#delete-id').val();

            // Show loading state
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
            $(this).attr('disabled', true);

            $.ajax({
                url: `{{ url('kwu/sertifikat-kwu') }}/${id}`,
                type: 'DELETE',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modal-delete').modal('hide');
                    table.ajax.reload();

                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        position: 'top-end',
                        toast: true,
                        showConfirmButton: false,
                        timer: 3000
                    });
                },
                error: function(xhr) {
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
                },
                complete: function() {
                    // Reset button state
                    $('#btn-confirm-delete').html('Hapus');
                    $('#btn-confirm-delete').attr('disabled', false);
                }
            });
        });

        // Helper function to clear validation errors
        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }
        
        // Event handler untuk filter
        $('#filter-tahun, #filter-semester, #filter-fakultas, #filter-prodi').change(function() {
            table.ajax.reload();
        });
        
        // Event handler untuk reset filter
        $('#btn-reset-filter').click(function() {
            // Reset semua filter
            $('#filter-tahun, #filter-semester, #filter-fakultas, #filter-prodi').val('').trigger('change');
            
            // Tampilkan semua opsi program studi
            $('#filter-prodi option').show();
            
            // Reload table
            table.ajax.reload();
        });
        
         // Filter prodi berdasarkan fakultas
            $('#filter-fakultas').on('change', function() {
                var fakultasId = $(this).val();
                var $prodiSelect = $('#filter-prodi');

                // Reset program studi filter
                $prodiSelect.val('');

                // Jika fakultas dipilih, filter program studi
                if (fakultasId) {
                    // Nonaktifkan dan sembunyikan semua opsi terlebih dahulu
                    $prodiSelect.find('option').each(function() {
                        var $option = $(this);
                        var optionFakultasId = $option.data('fakultas');
                        
                        if (!optionFakultasId || optionFakultasId == fakultasId) {
                            $option.prop('disabled', false).show();
                        } else {
                            $option.prop('disabled', true).hide();
                        }
                    });
                } else {
                    // Tampilkan semua program studi jika tidak ada fakultas yang dipilih
                    $prodiSelect.find('option').prop('disabled', false).show();
                }

                // Trigger change untuk refresh Select2
                $prodiSelect.trigger('change.select2');
            });

        
        // Show Validate Modal
        $('#btn-validate').click(function() {
            $('#form-validate').trigger('reset');
            $('.custom-file-label').text('Pilih file');
            $('#modal-validate').modal('show');
            clearErrors();
        });
        
        // Handle Validate Form Submit
        $('#form-validate').submit(function(e) {
            e.preventDefault();
            
            // Show loading state
            $('#btn-validate-submit').html('<i class="fas fa-spinner fa-spin"></i> Memvalidasi...');
            $('#btn-validate-submit').attr('disabled', true);
            clearErrors();

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('kwu.sertifikat-kwu.validate-data') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#modal-validate').modal('hide');
                    
                    // Update validation result modal with data
                    $('#total-data').text(response.data.total_data);
                    $('#matched-data').text(response.data.matched_count);
                    $('#unmatched-data').text(response.data.unmatched_count);
                    
                    // Show validation result modal
                    $('#modal-validation-result').modal('show');
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON;
                        if (errors.message) {
                            if (errors.message.includes('file')) {
                                $('#error-validate-file').text(errors.message);
                                $('#validate-file').addClass('is-invalid');
                            } else {
                                // General error
                                Swal.fire({
                                    title: 'Error!',
                                    text: errors.message,
                                    icon: 'error',
                                    position: 'top-end',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        }
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message ||
                                'Terjadi kesalahan saat memvalidasi data',
                            icon: 'error',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                },
                complete: function() {
                    // Reset button state
                    $('#btn-validate-submit').html('Validasi');
                    $('#btn-validate-submit').attr('disabled', false);
                }
            });
        });
        
        // Handle Export Validation Results
        $('#btn-export-validation').click(function() {
            // Show loading state
            var originalText = $(this).html();
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Mengunduh...');
            $(this).attr('disabled', true);
            
            // Create a form to submit the data
            var form = $('<form>', {
                'method': 'post',
                'action': '{{ route("kwu.sertifikat-kwu.export-validation-results") }}',
                'target': '_blank'
            });
            
            // Add CSRF token
            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': $('meta[name="csrf-token"]').attr('content')
            }));
            
            // Append form to body, submit it, and remove it
            $('body').append(form);
            form.submit();
            form.remove();
            
            // Reset button state after a short delay
            setTimeout(function() {
                $('#btn-export-validation').html(originalText);
                $('#btn-export-validation').attr('disabled', false);
            }, 1000);
        });
    });
</script>
@endpush