@extends('layouts.master')

@section('title', 'Data UMKM')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Perhatian!</strong> Setiap mahasiswa hanya diperbolehkan
                mendaftarkan
                satu UMKM. Jika Anda sudah mendaftarkan UMKM, tombol "Tambah UMKM" tidak akan tersedia.
            </div>

            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> <strong>Perhatian!</strong> Jika anda ingin menggunakan UMKM yang sudah
                ada, silahkan klik tombol "Arsip UMKM" dan tidak perlu mendaftarkan UMKM lagi. Silahkan melanjutkan ke menu
                <a class="text-bold text-blue text-decoration-none"
                    href="{{ route('mahasiswa.surat-pengantar.index') }}">Surat
                    Pengantar</a>
            </div>
            {{-- <div class="alert alert-warning" id="alert-has-umkm" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian!</strong> Anda sudah memiliki UMKM. Anda hanya diperbolehkan membuat satu UMKM.
            </div> --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar UMKM Saya</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                            <i class="fas fa-plus"></i> Tambah UMKM
                        </button>
                        <button type="button" class="btn btn-info btn-sm ml-1" id="btn-arsip">
                            <i class="fas fa-archive"></i> Arsip UMKM
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="umkm-table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Logo</th>
                                <th>Nama UMKM</th>
                                <th>Kategori</th>
                                <th>Pemilik</th>
                                <th>No. HP</th>
                                <th>Email</th>
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
                    <h4 class="modal-title" id="modal-title">Tambah UMKM</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-umkm" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="umkm-id">
                        <div class="form-group">
                            <label for="kategori_umkm_id" class="form-label">Kategori UMKM</label>
                            <select class="form-control" id="kategori_umkm_id" name="kategori_umkm_id" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriUmkm as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-kategori_umkm_id"></div>
                        </div>
                        <div class="form-group">
                            <label for="nama_umkm" class="form-label">Nama UMKM</label>
                            <input type="text" class="form-control" id="nama_umkm" name="nama_umkm"
                                placeholder="Masukkan nama UMKM" required>
                            <div class="invalid-feedback" id="error-nama_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="nama_pemilik_umkm" class="form-label">Nama Pemilik</label>
                            <input type="text" class="form-control" id="nama_pemilik_umkm" name="nama_pemilik_umkm"
                                placeholder="Masukkan nama pemilik" required>
                            <div class="invalid-feedback" id="error-nama_pemilik_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="jabatan_umkm" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan_umkm" name="jabatan_umkm"
                                placeholder="Masukkan jabatan" required>
                            <div class="invalid-feedback" id="error-jabatan_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="no_hp_umkm" class="form-label">No. HP</label>
                            <input type="number" class="form-control" id="no_hp_umkm" name="no_hp_umkm"
                                placeholder="Masukkan nomor HP" required>
                            <div class="invalid-feedback" id="error-no_hp_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="email_umkm" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email_umkm" name="email_umkm"
                                placeholder="Masukkan email" required>
                            <div class="invalid-feedback" id="error-email_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="alamat_umkm" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat_umkm" name="alamat_umkm" placeholder="Masukkan alamat" rows="3"
                                required></textarea>
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
                            <small class="form-text text-danger">Format: JPG, JPEG, PNG, GIF. Maksimal ukuran: 2MB</small>
                            <div class="invalid-feedback" id="error-logo_umkm"></div>
                            <div class="mt-2" id="logo-preview-container" style="display: none;">
                                <img id="logo-preview" src="" alt="Logo Preview"
                                    style="max-height: 100px; max-width: 100%;">
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
                    <p>Apakah Anda yakin ingin menghapus UMKM ini?</p>
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

    <!-- Modal Arsip UMKM -->
    <div class="modal fade" id="modal-arsip">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h4 class="modal-title">Daftar UMKM</h4>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="umkmTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="pks-tab" data-toggle="tab" href="#pks" role="tab" aria-controls="pks" aria-selected="true">
                                <i class="fas fa-file-contract"></i> UMKM dengan PKS Berlaku
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="tanpa-pks-tab" data-toggle="tab" href="#tanpa-pks" role="tab" aria-controls="tanpa-pks" aria-selected="false">
                                <i class="fas fa-exclamation-triangle"></i> UMKM Tanpa PKS Berlaku
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="umkmTabsContent">
                        <!-- Tab 1: UMKM dengan PKS Berlaku -->
                        <div class="tab-pane fade show active" id="pks" role="tabpanel" aria-labelledby="pks-tab">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian!</strong>
                                <ul class="mb-0">
                                    <li>UMKM yang sudah digunakan 2 kali, tidak bisa digunakan lagi.</li>
                                    <li>Warna hijau pada tabel menunjukkan UMKM yang anda ambil.</li>
                                </ul>
                            </div>
                            <table id="arsip-table" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama UMKM</th>
                                        <th>Kategori UMKM</th>
                                        <th>Nomor PKS</th>
                                        <th>Nomor PKS UMKM</th>
                                        <th>Tanggal PKS</th>
                                        <th>Tanggal Berakhir</th>
                                        <th>Jumlah Digunakan</th>
                                        <th>File PKS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be filled by DataTable -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Tab 2: UMKM Tanpa PKS Berlaku -->
                        <div class="tab-pane fade" id="tanpa-pks" role="tabpanel" aria-labelledby="tanpa-pks-tab">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> <strong>Informasi!</strong>
                                <ul class="mb-0">
                                    <li>Daftar UMKM yang tidak memiliki PKS atau PKS sudah kedaluwarsa.</li>
                                </ul>
                            </div>
                            <table id="tanpa-pks-table" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama UMKM</th>
                                        <th>Kategori UMKM</th>
                                        <th>Nama Pemilik</th>
                                        <th>No. HP</th>
                                        <th>Email</th>
                                        <th>Status PKS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be filled by DataTable -->
                                </tbody>
                            </table>
                        </div>
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

@push('css')
<style>
    /* Modern styles for tabs */
    .nav-tabs {
        border-bottom: none;
        margin-bottom: 25px;
        background: linear-gradient(to right, #f8f9fa, #ffffff);
        border-radius: 10px;
        padding: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-radius: 8px;
        color: #6c757d;
        font-weight: 500;
        padding: 12px 24px;
        margin-right: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        background-color: transparent;
    }
    
    .nav-tabs .nav-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 100%;
        background: linear-gradient(45deg, #007bff, #0056b3);
        transition: width 0.3s ease;
        z-index: -1;
        border-radius: 8px;
    }
    
    .nav-tabs .nav-link:hover {
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgb(202, 166, 6);
    }
    
    .nav-tabs .nav-link:hover::before {
        width: 100%;
    }
    
    .nav-tabs .nav-link.active {
        color: #ffffff;
        background: linear-gradient(45deg, #007bff, #0056b3);
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.25);
        transform: translateY(-2px);
    }
    
    .nav-tabs .nav-link i {
        margin-right: 8px;
    }
    
    .tab-content {
        padding: 25px 15px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
    }
    
    .alert-info {
        border-left: 4px solid #17a2b8;
    }
    
    .alert-warning {
        border-left: 4px solid #ffc107;
    }
    
    .badge-danger {
        background-color: #dc3545;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .table-responsive {
        border-radius: 0.25rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #007bff !important;
        border-color: #007bff !important;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #e9ecef !important;
        border-color: #dee2e6 !important;
    }
</style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input@1.3.4/dist/bs-custom-file-input.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    <script>
        $(function() {
            // Initialize DataTable
            var table = $('#umkm-table').DataTable({
                "responsive": true,
                "autoWidth": false,
                "processing": true,
                "language": {
                    "processing": "<i class='fas fa-spinner fa-spin fa-2x'></i>"
                },
                "ajax": {
                    "url": "{{ route('mahasiswa.data-umkm.data') }}",
                    "type": "GET",
                    "dataSrc": function(json) {
                        // Simpan status has_surat_pengantar ke variabel global
                        window.hasSuratPengantar = json.has_surat_pengantar || false;

                        // Cek apakah user sudah memiliki UMKM
                        if (json.has_umkm) {
                            $('#btn-add').hide();
                            $('#alert-has-umkm').show();
                        } else {
                            $('#btn-add').show();
                            $('#alert-has-umkm').hide();
                        }
                        return json.data;
                    }
                },
                "columns": [{
                        "data": null,
                        "render": function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        "data": "logo_umkm",
                        "render": function(data, type, row) {
                            if (data) {
                                return `<img src="{{ asset('storage') }}/${data}" alt="Logo" class="img-thumbnail" style="max-height: 50px;">`;
                            }
                            return `<span class="badge badge-secondary">No Logo</span>`;
                        }
                    },
                    {
                        "data": "nama_umkm"
                    },
                    {
                        "data": "kategori_umkm.nama_kategori"
                    },
                    {
                        "data": "nama_pemilik_umkm"
                    },
                    {
                        "data": "no_hp_umkm"
                    },
                    {
                        "data": "email_umkm"
                    },
                    {
                        "data": null,
                        "orderable": false,
                        "className": "text-center",
                        "render": function(data, type, row) {
                            const nim = {{ Session::get('kode') }};
                            if (row.input_by == nim) {
                                let deleteButton = '';

                                if (window.hasSuratPengantar) {
                                    // Tombol disabled dengan tooltip
                                    deleteButton = `
                                        <button data-toggle="tooltip" data-placement="top" title="Tidak dapat dihapus karena sudah memiliki surat pengantar" type="button" class="btn btn-xs btn-secondary" disabled >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        `;
                                } else {
                                    // Tombol aktif
                                    deleteButton = `
                                        <button data-toggle="tooltip" data-placement="top" title="Hapus UMKM" type="button" class="btn btn-xs btn-danger btn-delete" data-id="${row.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        `;
                                }

                                return `
                                    <button data-toggle="tooltip" data-placement="top" title="Edit UMKM" type="button" class="btn btn-xs btn-info btn-edit" data-id="${row.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    ${deleteButton}
                                `;
                            } else {
                                return `<span class="badge badge-info">Hanya Lihat</span>`;
                            }
                        }
                    }
                ],
                "drawCallback": function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Initialize bs-custom-file-input
            bsCustomFileInput.init();

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

            // Show Add Modal
            $('#btn-add').click(function() {
                $('#form-umkm').trigger('reset');
                $('#umkm-id').val('');
                $('#modal-title').text('Tambah UMKM');
                $('#logo-preview-container').hide();
                $('.custom-file-label').text('Pilih file');
                $('#modal-form').modal('show');
                clearErrors();
            });

            // Show Edit Modal
            $('#umkm-table').on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $('#umkm-id').val(id);
                $('#modal-title').text('Edit UMKM');
                clearErrors();

                // Get data from server
                $.ajax({
                    url: `{{ url('mahasiswa/data-umkm') }}/${id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        const data = response.data;
                        $('#kategori_umkm_id').val(data.kategori_umkm_id);
                        $('#nama_umkm').val(data.nama_umkm);
                        $('#nama_pemilik_umkm').val(data.nama_pemilik_umkm);
                        $('#jabatan_umkm').val(data.jabatan_umkm);
                        $('#email_umkm').val(data.email_umkm);
                        $('#no_hp_umkm').val(data.no_hp_umkm);
                        $('#alamat_umkm').val(data.alamat_umkm);

                        // Show logo preview if exists
                        if (data.logo_umkm) {
                            $('#logo-preview').attr('src',
                                `{{ asset('storage') }}/${data.logo_umkm}`);
                            $('#logo-preview-container').show();
                            $('.custom-file-label').text('Ubah logo');
                        } else {
                            $('#logo-preview-container').hide();
                            $('.custom-file-label').text('Pilih file');
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
            $('#umkm-table').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                $('#delete-id').val(id);
                $('#modal-delete').modal('show');
            });

            // Handle Form Submit
            $('#form-umkm').submit(function(e) {
                e.preventDefault();
                var id = $('#umkm-id').val();
                var url = id ? `{{ url('mahasiswa/data-umkm') }}/${id}` :
                    "{{ route('mahasiswa.data-umkm.store') }}";
                var method = id ? 'POST' : 'POST'; // Always POST for FormData

                // Create FormData object for file upload
                var formData = new FormData(this);

                // Add _method field for PUT requests
                if (id) {
                    formData.append('_method', 'PUT');
                }

                // Show loading state
                $('#btn-save').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                $('#btn-save').attr('disabled', true);
                clearErrors();

                $.ajax({
                    url: url,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST', // Always POST for FormData
                    data: formData,
                    dataType: 'json',
                    contentType: false, // Required for FormData
                    processData: false, // Required for FormData
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
                                if (errors.message.includes('nama_umkm')) {
                                    $('#error-nama_umkm').text(errors.message);
                                    $('#nama_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('kategori_umkm_id')) {
                                    $('#error-kategori_umkm_id').text(errors.message);
                                    $('#kategori_umkm_id').addClass('is-invalid');
                                } else if (errors.message.includes('nama_pemilik_umkm')) {
                                    $('#error-nama_pemilik_umkm').text(errors.message);
                                    $('#nama_pemilik_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('jabatan_umkm')) {
                                    $('#error-jabatan_umkm').text(errors.message);
                                    $('#jabatan_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('no_hp_umkm')) {
                                    $('#error-no_hp_umkm').text(errors.message);
                                    $('#no_hp_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('email_umkm')) {
                                    $('#error-email_umkm').text(errors.message);
                                    $('#email_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('alamat_umkm')) {
                                    $('#error-alamat_umkm').text(errors.message);
                                    $('#alamat_umkm').addClass('is-invalid');
                                } else if (errors.message.includes('logo_umkm')) {
                                    $('#error-logo_umkm').text(errors.message);
                                    $('#logo_umkm').addClass('is-invalid');
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
                        } else if (xhr.status === 403) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Anda tidak memiliki akses untuk melakukan tindakan ini',
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menyimpan data',
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
                    url: `{{ url('mahasiswa/data-umkm') }}/${id}`,
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

            // Show Arsip UMKM Modal
            $('#btn-arsip').click(function() {
                $('#modal-arsip').modal('show');

                // Initialize DataTable for Arsip UMKM (PKS Berlaku) if not already initialized
                if (!$.fn.DataTable.isDataTable('#arsip-table')) {
                    $('#arsip-table').DataTable({
                        "responsive": true,
                        "autoWidth": false,
                        "processing": true,
                        "serverSide": true,
                        "language": {
                            "processing": "<i class='fas fa-spinner fa-spin fa-2x'></i>"
                        },
                        "ajax": {
                            "url": "{{ route('mahasiswa.data-umkm.arsip') }}",
                            "type": "GET"
                        },
                        "rowCallback": function(row, data) {
                            if (data.is_used_by_current_user > 0) {
                                $(row).css('background-color', '#e6ffe6');
                            }
                        },
                        "columns": [{
                                "data": "id",
                                "name": "id",
                                "orderable": false,
                                "searchable": false,
                                "render": function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                "data": "nama_umkm",
                                "name": "nama_umkm"
                            },
                            {
                                "data": "nama_kategori",
                                "name": "nama_kategori"
                            },
                            {
                                "data": "no_pks",
                                "name": "no_pks"
                            },
                            {
                                "data": "no_pks_umkm",
                                "name": "no_pks_umkm"
                            },
                            {
                                "data": "tgl_pks",
                                "name": "tgl_pks",
                                "render": function(data) {
                                    return moment(data).format('DD MMMM YYYY');
                                }
                            },
                            {
                                "data": "tanggal_berakhir",
                                "name": "tanggal_berakhir",
                                "render": function(data) {
                                    return moment(data).format('DD MMMM YYYY');
                                }
                            },
                            {
                                "data": "jumlah_umkm_digunakan",
                                "name": "jumlah_umkm_digunakan",
                                "render": function(data) {
                                    return data + ' kali';
                                }
                            },
                            {
                                "data": "file_arsip_pks",
                                "name": "file_arsip_pks",
                                "orderable": false,
                                "render": function(data, type, row) {
                                    // Tampilkan tombol unduh jika jumlah_umkm_digunakan < 2 ATAU jika user saat ini pernah menggunakan UMKM ini
                                    if (row.jumlah_umkm_digunakan < 2 || row
                                        .is_used_by_current_user > 0) {
                                        if (data) {
                                            return `<a href="{{ asset('storage') }}/${data.replace('public/', '')}" class="btn btn-xs btn-primary" target="_blank">
                            <i class="fas fa-file-pdf"></i> Unduh File PKS
                        </a>`;
                                        }
                                    }
                                    return '-';
                                }
                            }
                        ]
                    });
                } else {
                    $('#arsip-table').DataTable().ajax.reload();
                }

                // Initialize DataTable for UMKM Tanpa PKS Berlaku if not already initialized
                if (!$.fn.DataTable.isDataTable('#tanpa-pks-table')) {
                    $('#tanpa-pks-table').DataTable({
                        "responsive": true,
                        "autoWidth": false,
                        "processing": true,
                        "serverSide": true,
                        "language": {
                            "processing": "<i class='fas fa-spinner fa-spin fa-2x'></i>"
                        },
                        "ajax": {
                            "url": "{{ route('mahasiswa.data-umkm.tanpa-pks') }}",
                            "type": "GET"
                        },
                        "columns": [{
                                "data": "id",
                                "name": "id",
                                "orderable": false,
                                "searchable": false,
                                "render": function(data, type, row, meta) {
                                    return meta.row + meta.settings._iDisplayStart + 1;
                                }
                            },
                            {
                                "data": "nama_umkm",
                                "name": "nama_umkm"
                            },
                            {
                                "data": "nama_kategori",
                                "name": "nama_kategori"
                            },
                            {
                                "data": "nama_pemilik_umkm",
                                "name": "nama_pemilik_umkm"
                            },
                            {
                                "data": "no_hp_umkm",
                                "name": "no_hp_umkm"
                            },
                            {
                                "data": "email_umkm",
                                "name": "email_umkm"
                            },
                            {
                                "data": "status_pks",
                                "name": "status_pks",
                                "render": function(data) {
                                    if (data === 'Tidak Ada PKS') {
                                        return '<span class="badge badge-danger">Tidak Ada PKS</span>';
                                    } else if (data === 'PKS Kedaluwarsa') {
                                        return '<span class="badge badge-warning">PKS Kedaluwarsa</span>';
                                    } else {
                                        return '<span class="badge badge-secondary">' + data + '</span>';
                                    }
                                }
                            }
                        ]
                    });
                } else {
                    $('#tanpa-pks-table').DataTable().ajax.reload();
                }
            });

            // Handle tab switching to reload data when tab is shown
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr("href");
                if (target === '#pks') {
                    // Reload PKS data when tab is shown
                    if ($.fn.DataTable.isDataTable('#arsip-table')) {
                        $('#arsip-table').DataTable().ajax.reload();
                    }
                } else if (target === '#tanpa-pks') {
                    // Reload Tanpa PKS data when tab is shown
                    if ($.fn.DataTable.isDataTable('#tanpa-pks-table')) {
                        $('#tanpa-pks-table').DataTable().ajax.reload();
                    }
                }
            });
        });
    </script>
@endpush
