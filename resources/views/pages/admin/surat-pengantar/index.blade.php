@extends('layouts.master')

@section('title', 'Surat Pengantar')

@push('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Surat Pengantar</h3>
                    {{-- <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                            <i class="fas fa-plus"></i> Buat Surat Pengantar
                        </button>
                    </div> --}}
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="surat-pengantar-table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal Surat</th>
                                <th>Kelas</th>
                                <th>Kelompok</th>
                                <th>UMKM</th>
                                <th>Jumlah Mahasiswa</th>
                                <th>Input By</th>
                                @if(Session::get('role') == 'admin')
                                <th width="15%">Aksi</th>
                                @endif
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Buat Surat Pengantar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-surat-pengantar" class="form-horizontal">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="surat-pengantar-id">
                        <div class="form-group">
                            <label for="umkm_id" class="form-label">UMKM</label>
                            <div class="input-group">
                                <select class="form-control select2" id="umkm_id" name="umkm_id" required>
                                    <option value="">-- Pilih UMKM --</option>
                                    @foreach ($umkms as $umkm)
                                        <option value="{{ $umkm->id }}">{{ $umkm->nama_umkm }}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="button" title="Tambah UMKM" data-toggle="tooltip" data-placement="top"
                                        class="btn btn-primary" id="btn-add-umkm">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="invalid-feedback" id="error-umkm_id"></div>
                        </div>
                        <div class="form-group">
                            <label for="kelas" class="form-label">Kelas</label>
                            <input type="text" class="form-control" id="kelas" name="kelas" maxlength="2" required
                                placeholder="Contoh: 01, 02, 03">
                            <div class="invalid-feedback" id="error-kelas"></div>
                        </div>
                        <div class="form-group">
                            <label for="tgl_surat" class="form-label">Tanggal Surat</label>
                            <input type="date" class="form-control" id="tgl_surat" name="tgl_surat" required>
                            <div class="invalid-feedback" id="error-tgl_surat"></div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-light">
                                <h3 class="card-title">Daftar Mahasiswa</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-success btn-sm" id="btn-add-mahasiswa">
                                        <i class="fas fa-plus"></i> Tambah Mahasiswa
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered" id="mahasiswa-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="20%">NIM</th>
                                            <th width="40%">Nama Mahasiswa</th>
                                            <th width="30%">Program Studi</th>
                                            <th width="5%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Mahasiswa rows will be added here -->
                                    </tbody>
                                </table>
                            </div>
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
                    <p>Apakah Anda yakin ingin menghapus surat pengantar ini?</p>
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

    <!-- Modal Tambah UMKM -->
    <div class="modal fade" id="modal-add-umkm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah UMKM Baru</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-add-umkm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="kategori_umkm_id">Kategori UMKM</label>
                            <select class="form-control" id="kategori_umkm_id" name="kategori_umkm_id">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriUmkms as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-kategori_umkm_id"></div>
                        </div>
                        <div class="form-group">
                            <label for="nama_umkm">Nama UMKM</label>
                            <input type="text" class="form-control" id="nama_umkm" name="nama_umkm"
                                placeholder="Masukkan nama UMKM">
                            <div class="invalid-feedback" id="error-nama_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="nama_pemilik_umkm">Nama Pemilik</label>
                            <input type="text" class="form-control" id="nama_pemilik_umkm" name="nama_pemilik_umkm"
                                placeholder="Masukkan nama pemilik UMKM">
                            <div class="invalid-feedback" id="error-nama_pemilik_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="jabatan_umkm">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan_umkm" name="jabatan_umkm"
                                placeholder="Masukkan jabatan">
                            <div class="invalid-feedback" id="error-jabatan_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="no_hp_umkm">No. HP</label>
                            <input type="text" class="form-control" id="no_hp_umkm" name="no_hp_umkm"
                                placeholder="Masukkan nomor HP">
                            <div class="invalid-feedback" id="error-no_hp_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="email_umkm">Email</label>
                            <input type="text" class="form-control" id="email_umkm" name="email_umkm"
                                placeholder="Masukkan email">
                            <div class="invalid-feedback" id="error-email_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="alamat_umkm">Alamat</label>
                            <textarea class="form-control" id="alamat_umkm" name="alamat_umkm" rows="3"
                                placeholder="Masukkan alamat lengkap"></textarea>
                            <div class="invalid-feedback" id="error-alamat_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="logo_umkm" class="form-label">Logo UMKM</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="logo_umkm" name="logo_umkm"
                                        accept="image/*">
                                    <label class="custom-file-label" for="logo_umkm">Pilih file</label>
                                </div>
                            </div>
                            <div class="invalid-feedback" id="error-logo_umkm"></div>
                            <div class="mt-2" id="logo-preview-container" style="display: none;">
                                <img id="logo-preview" src="" alt="Logo Preview"
                                    style="max-height: 100px; max-width: 100%;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-umkm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Daftar Mahasiswa -->
    <div class="modal fade" id="modal-mahasiswa-list">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Daftar Mahasiswa</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="mahasiswa-list-table">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">NIM</th>
                                    <th width="40%">Nama Mahasiswa</th>
                                    <th width="35%">Program Studi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be filled by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
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
    <script src="{{ asset('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

    <script>
        $(function() {
            // Initialize DataTable
            var table = $('#surat-pengantar-table').DataTable({
                "responsive": true,
                "autoWidth": false,
                "processing": true,
                "language": {
                    "processing": "<i class='fas fa-spinner fa-spin fa-2x'></i>"
                },
                "ajax": {
                    "url": "{{ route('p2k.surat-pengantar.data') }}",
                    "type": "GET"
                },
                "columns": [{
                        "data": null,
                        "render": function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        "data": "tgl_surat",
                        "render": function(data) {
                            return moment(data).format('DD MMMM YYYY');
                        }
                    },
                    {
                        "data": "kelas"
                    },
                    {
                        "data": "kelompok"
                    },
                    {
                        "data": "umkm.nama_umkm"
                    },
                    {
                        "data": "surat_pengantar_mahasiswas",
                        "render": function(data, type, row) {
                            const count = data ? data.length : 0;
                            if (count > 0) {
                                return `<span class="cursor-pointer text-primary" 
                                            data-toggle="tooltip" 
                                            data-placement="top" 
                                            title="Lihat" 
                                            onclick="showMahasiswaModal('${row.encrypted_id}')">
                                        ${count} Mahasiswa
                                    </span>`;
                            }
                            return '<span class="badge bg-secondary">0</span>';
                        }
                    },
                    {
                        "data": "input_by"
                    },
                    @if(Session::get('role') == 'admin')
                    {
                        "data": null,
                        "orderable": false,
                        "className": "text-center",
                        "render": function(data, type, row) {
                            return `
                            <div class="btn-group">
                                <button type="button" class="btn btn-xs btn-info btn-edit" data-id="${row.id}"
                                        data-toggle="tooltip" data-placement="top" title="Edit Surat Pengantar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="${row.id}"
                                        data-toggle="tooltip" data-placement="top" title="Hapus Surat Pengantar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <a href="{{ url('p2k/surat-pengantar') }}/${row.encrypted_id}/cetak" class="btn btn-xs btn-success" target="_blank"
                                   data-toggle="tooltip" data-placement="top" title="Cetak Surat Pengantar">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        `;
                        }
                    }
                    @endif
                ],
                drawCallback: function(settings) {
                    // Reinitialize tooltips after each draw
                    // Destroy existing tooltips to prevent duplicates
                    $('[data-toggle="tooltip"]').tooltip('dispose');
                    // Initialize new tooltips with Bootstrap
                    $('[data-toggle="tooltip"]').tooltip({
                        boundary: 'window',
                        trigger: 'hover focus',
                        template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
                    });
                }
            });

            // Set today's date as default
            $('#tgl_surat').val(moment().format('YYYY-MM-DD'));

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: "-- Pilih UMKM --",
                dropdownParent: $('#modal-form')
            });

            // Add Mahasiswa Row
            $('#btn-add-mahasiswa').click(function() {
                addMahasiswaRow();
            });

            // Remove Mahasiswa Row
            $(document).on('click', '.btn-remove-mahasiswa', function() {
                $(this).closest('tr').remove();
                updateMahasiswaRowNumbers();
            });

            // Show Add Modal
            $('#btn-add').click(function() {
                $('#form-surat-pengantar').trigger('reset');
                $('#surat-pengantar-id').val('');
                $('#modal-title').text('Buat Surat Pengantar');
                $('#mahasiswa-table tbody').empty();
                addMahasiswaRow(); // Add at least one row
                $('#modal-form').modal('show');
                clearErrors();
                $('.select2').val('').trigger('change');
            });

            // Show Edit Modal
            $('#surat-pengantar-table').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $('#surat-pengantar-id').val(id);
                $('#modal-title').text('Edit Surat Pengantar');
                clearErrors();

                // Get data from server
                $.ajax({
                    url: `{{ url('p2k/surat-pengantar') }}/${id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        const data = response.data;
                        $('#umkm_id').val(data.umkm_id).trigger('change');
                        $('#kelas').val(data.kelas);
                        $('#tgl_surat').val(moment(data.tgl_surat).format('YYYY-MM-DD'));

                        // Clear and populate mahasiswa table
                        $('#mahasiswa-table tbody').empty();
                        if (data.surat_pengantar_mahasiswas.length > 0) {
                            data.surat_pengantar_mahasiswas.forEach(function(mhs, index) {
                                addMahasiswaRow(mhs.nim, mhs.nama_mahasiswa, mhs
                                    .prodi_id);
                            });
                        } else {
                            addMahasiswaRow(); // Add empty row if no data
                        }

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
            $('#surat-pengantar-table').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                $('#delete-id').val(id);
                $('#modal-delete').modal('show');
            });

            // Handle Form Submit
            $('#form-surat-pengantar').submit(function(e) {
                e.preventDefault();
                var id = $('#surat-pengantar-id').val();
                var url = id ? `{{ url('p2k/surat-pengantar') }}/${id}` :
                    "{{ route('p2k.surat-pengantar.store') }}";
                var method = id ? 'PUT' : 'POST';

                // Collect mahasiswa data
                var mahasiswas = [];
                $('#mahasiswa-table tbody tr').each(function() {
                    var nim = $(this).find('.mahasiswa-nim').val();
                    var nama = $(this).find('.mahasiswa-nama').val();
                    var prodiId = $(this).find('.mahasiswa-prodi').val();

                    if (nim && nama && prodiId) {
                        mahasiswas.push({
                            nim: nim,
                            nama_mahasiswa: nama,
                            prodi_id: prodiId
                        });
                    }
                });

                // Prepare form data
                var formData = {
                    umkm_id: $('#umkm_id').val(),
                    kelas: $('#kelas').val(),
                    tgl_surat: $('#tgl_surat').val(),
                    mahasiswas: mahasiswas,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };

                // Show loading state
                $('#btn-save').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                $('#btn-save').attr('disabled', true);
                clearErrors();

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
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
                                if (errors.tipe === 'umkm') {
                                    $('#error-umkm_id').text(errors.message);
                                    $('#error-umkm_id').show();
                                    $('#umkm_id').addClass('is-invalid');
                                    $('#kelas').addClass('is-invalid');
                                    return;
                                }
                                // Handle validation errors
                                if (errors.message.includes('umkm_id')) {
                                    $('#error-umkm_id').text(errors.message);
                                    $('#umkm_id').addClass('is-invalid');
                                } else if (errors.message.includes('kelas')) {
                                    $('#error-kelas').text(errors.message);
                                    $('#kelas').addClass('is-invalid');
                                } else if (errors.message.includes('tgl_surat')) {
                                    $('#error-tgl_surat').text(errors.message);
                                    $('#tgl_surat').addClass('is-invalid');
                                } else if (errors.message.includes('mahasiswas')) {
                                    // General error for mahasiswa data
                                    Swal.fire({
                                        title: 'Error!',
                                        text: errors.message,
                                        icon: 'error',
                                        position: 'top-end',
                                        toast: true,
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
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

            // Handle Delete Confirmation
            $('#btn-confirm-delete').click(function() {
                var id = $('#delete-id').val();

                // Show loading state
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
                $(this).attr('disabled', true);

                $.ajax({
                    url: `{{ url('p2k/surat-pengantar') }}/${id}`,
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

            // Helper function to add mahasiswa row
            function addMahasiswaRow(nim = '', nama = '', prodiId = '') {
                var rowCount = $('#mahasiswa-table tbody tr').length;
                var rowNumber = rowCount + 1;

                var row = `
                <tr>
                    <td>${rowNumber}</td>
                    <td>
                        <input type="text" class="form-control mahasiswa-nim" value="${nim}" placeholder="NIM" required>
                    </td>
                    <td>
                        <input type="text" class="form-control mahasiswa-nama" value="${nama}" placeholder="Nama Mahasiswa" required>
                    </td>
                    <td>
                        <select class="form-control mahasiswa-prodi" required>
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach ($programStudis as $prodi)
                            <option value="{{ $prodi->id }}" ${prodiId == {{ $prodi->id }} ? 'selected' : ''}>{{ $prodi->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-mahasiswa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

                $('#mahasiswa-table tbody').append(row);
            }

            // Helper function to update row numbers
            function updateMahasiswaRowNumbers() {
                $('#mahasiswa-table tbody tr').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
            }

            // Helper function to clear validation errors
            function clearErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }

            // Modal Tambah UMKM
            $('#btn-add-umkm').click(function() {
                $('#form-add-umkm').trigger('reset');
                clearErrors();
                $('#modal-add-umkm').modal('show');
            });

            // Initialize bs-custom-file-input
            bsCustomFileInput.init();

            // Function to show mahasiswa modal
            window.showMahasiswaModal = function(id) {
                // Show loading state
                $('#mahasiswa-list-table tbody').html(
                    '<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>'
                    );

                // Show modal
                $('#modal-mahasiswa-list').modal('show');

                // Fetch mahasiswa data
                $.ajax({
                    url: `{{ url('p2k/surat-pengantar') }}/${id}/mahasiswa`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        let html = '';

                        if (response.status === 'success' && response.data.length > 0) {
                            response.data.forEach(function(mahasiswa, index) {
                                html += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${mahasiswa.nim}</td>
                                        <td>${mahasiswa.nama_mahasiswa}</td>
                                        <td>${mahasiswa.nama_prodi}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            html =
                                '<tr><td colspan="4" class="text-center">Tidak ada data mahasiswa</td></tr>';
                        }

                        $('#mahasiswa-list-table tbody').html(html);
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat memuat data';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        $('#mahasiswa-list-table tbody').html(
                            `<tr><td colspan="4" class="text-center text-danger">${errorMessage}</td></tr>`
                            );
                    }
                });
            };

            // Logo preview
            $('#logo_umkm').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#logo-preview').attr('src', e.target.result);
                        $('#logo-preview-container').show();
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#logo-preview-container').hide();
                }
            });

            // Handle Form Submit UMKM
            $('#form-add-umkm').submit(function(e) {
                e.preventDefault();

                // Show loading state
                $('#btn-save-umkm').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                $('#btn-save-umkm').attr('disabled', true);
                clearErrors();

                // Create FormData object for file upload
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('master.umkm.store') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#modal-add-umkm').modal('hide');

                        // Add new option to select
                        const newOption = new Option(response.data.nama_umkm, response.data.id,
                            true, true);
                        $('#umkm_id').append(newOption).trigger('change');

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
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON;
                            if (errors.message) {
                                // Handle validation errors
                                if (errors.message.includes('nama umkm')) {
                                    $('#error-nama_umkm').text(errors.message);
                                    $('#nama_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('nama pemilik umkm')) {
                                    $('#error-nama_pemilik_umkm').text(errors.message);
                                    $('#nama_pemilik_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('jabatan umkm')) {
                                    $('#error-jabatan_umkm').text(errors.message);
                                    $('#jabatan_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('no hp umkm')) {
                                    $('#error-no_hp_umkm').text(errors.message);
                                    $('#no_hp_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('email umkm')) {
                                    $('#error-email_umkm').text(errors.message);
                                    $('#email_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('alamat umkm')) {
                                    $('#error-alamat_umkm').text(errors.message);
                                    $('#alamat_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('kategori umkm id')) {
                                    $('#error-kategori_umkm_id').text(errors.message);
                                    $('#kategori_umkm_id').addClass('is-invalid');
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
                                text: xhr.responseJSON?.message ||
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
                        $('#btn-save-umkm').html('Simpan');
                        $('#btn-save-umkm').attr('disabled', false);
                    }
                });
            });
        });

        // Initialize tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
