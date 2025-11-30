@extends('layouts.master')

@section('title', 'Setting Surat Pengantar')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Setting Surat Pengantar</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                            <i class="fas fa-plus"></i> Tambah Setting
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div id="no-data-message" class="text-center py-4" style="display: none;">
                        <i class="fas fa-cog fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada setting surat pengantar</h5>
                        <p class="text-muted">Klik tombol "Tambah Setting" untuk menambahkan setting surat pengantar</p>
                        <button type="button" class="btn btn-primary" id="btn-add-no-data">
                            <i class="fas fa-plus"></i> Tambah Setting
                        </button>
                    </div>

                    <div id="data-container" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tahun Akademik</th>
                                        <th>Nomor Surat</th>
                                        <th>QR Surat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    <!-- Data akan diisi oleh JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Tambah Setting Surat Pengantar</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-setting" class="form-horizontal">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="setting-id">
                        <div class="form-group">
                            <label for="tahun_akademik_id" class="form-label">Tahun Akademik</label>
                            <select class="form-control" id="tahun_akademik_id" name="tahun_akademik_id">
                                <option value="">Pilih Tahun Akademik</option>
                                @foreach($tahunAkademiks as $tahunAkademik)
                                    <option value="{{ $tahunAkademik->id }}">{{ $tahunAkademik->tahun_ajaran }} - {{ $tahunAkademik->tipe_semester }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="error-tahun_akademik_id"></div>
                        </div>
                        <div class="form-group">
                            <label for="no_surat" class="form-label">Nomor Surat</label>
                            <input type="text"
                                class="form-control rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                id="no_surat" name="no_surat" placeholder="Masukkan nomor surat">
                            <div class="invalid-feedback" id="error-no_surat"></div>
                        </div>
                        <div class="form-group">
                            <label for="qr_surat_image" class="form-label">QR Surat (Gambar)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="qr_surat_image" name="qr_surat_image"
                                    accept="image/*">
                                <label class="custom-file-label" for="qr_surat_image">Pilih file gambar...</label>
                            </div>
                            <small class="form-text text-muted">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
                            <small class="form-text text-info" id="file-info" style="display: none;"></small>
                            <div class="invalid-feedback" id="error-qr_surat_image"></div>
                            <div id="preview-container" style="margin-top:10px; display:none;">
                                <label>Preview Gambar:</label><br>
                                <img id="preview-qr-image" src="" alt="Preview QR Surat"
                                    style="max-width:180px; max-height:180px; border:1px solid #eee;">
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
                    <p>Apakah Anda yakin ingin menghapus setting surat pengantar ini?</p>
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
@endsection

@push('css')
    <style>
        .custom-file.is-invalid .custom-file-input {
            border-color: #dc3545;
        }

        .custom-file.is-invalid .custom-file-label {
            border-color: #dc3545;
        }

        .custom-file.is-invalid .custom-file-input:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(function() {
            var currentSetting = null;

            // Load data on page load
            loadData();

            // Show Add Modal
            $('#btn-add, #btn-add-no-data').click(function() {
                $('#form-setting').trigger('reset');
                $('#setting-id').val('');
                $('#modal-title').text('Tambah Setting Surat Pengantar');
                $('.custom-file-label').text('Pilih file gambar...');
                $('#modal-form').modal('show');
                clearErrors();
                // Hide preview on add
                $('#preview-qr-image').attr('src', '').hide();
                $('#preview-container').hide();
            });

            // Show Edit Modal - Delegated event for dynamically created buttons
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                
                // Fetch setting data by ID
                $.ajax({
                    url: `{{ url('settings/surat-pengantar') }}/${id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.data) {
                            var setting = response.data;
                            
                            $('#setting-id').val(setting.id);
                            $('#tahun_akademik_id').val(setting.tahun_akademik_id);
                            $('#no_surat').val(setting.no_surat);
                            $('.custom-file-label').text('Pilih file gambar...');
                            $('#modal-title').text('Edit Setting Surat Pengantar');
                            $('#modal-form').modal('show');
                            clearErrors();

                            // Show preview if image exists
                            if (setting.qr_surat_image) {
                                $('#preview-qr-image').attr('src', '{{ asset('storage/') }}/' + setting.qr_surat_image).show();
                                $('#preview-container').show();
                            } else {
                                $('#preview-qr-image').attr('src', '').hide();
                                $('#preview-container').hide();
                            }
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: (xhr.responseJSON && xhr.responseJSON.message) || 'Terjadi kesalahan saat mengambil data',
                            icon: 'error',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            });

            // Show Delete Modal - Delegated event for dynamically created buttons
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                $('#delete-id').val(id);
                $('#modal-delete').modal('show');
            });

            // Handle Form Submit
            $('#form-setting').submit(function(e) {
                e.preventDefault();

                // Clear previous errors
                clearErrors();

                // Client-side validation
                var hasError = false;
                
                // Validate tahun_akademik_id
                if (!$('#tahun_akademik_id').val()) {
                    $('#error-tahun_akademik_id').text('Tahun akademik wajib dipilih');
                    $('#tahun_akademik_id').addClass('is-invalid');
                    hasError = true;
                }

                // Validate no_surat
                if (!$('#no_surat').val().trim()) {
                    $('#error-no_surat').text('Nomor surat wajib diisi');
                    $('#no_surat').addClass('is-invalid');
                    hasError = true;
                }

                // Validate file is required (only for add, not for edit)
                var fileInput = $('#qr_surat_image')[0];
                var isEdit = $('#setting-id').val() !== '';

                if (fileInput.files.length === 0) {
                    // Only require file if it's add mode
                    if (!isEdit) {
                        $('#error-qr_surat_image').text('Gambar QR surat wajib dipilih');
                        $('#qr_surat_image').closest('.custom-file').addClass('is-invalid');
                        hasError = true;
                    }
                } else {
                    var file = fileInput.files[0];
                    var maxSize = 2 * 1024 * 1024; // 2MB in bytes

                    if (file.size > maxSize) {
                        $('#error-qr_surat_image').text('Ukuran file tidak boleh lebih dari 2MB');
                        $('#qr_surat_image').closest('.custom-file').addClass('is-invalid');
                        hasError = true;
                    }

                    // Validate file type
                    var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        $('#error-qr_surat_image').text('Format file harus JPG, PNG, atau GIF');
                        $('#qr_surat_image').closest('.custom-file').addClass('is-invalid');
                        hasError = true;
                    }
                }

                if (hasError) {
                    return false;
                }

                var id = $('#setting-id').val();
                var url = id ? `{{ url('settings/surat-pengantar') }}/${id}` :
                    "{{ route('settings.surat-pengantar.store') }}";
                var method = 'POST'; // Always use POST for file uploads
                var formData = new FormData();
                formData.append('tahun_akademik_id', $('#tahun_akademik_id').val());
                formData.append('no_surat', $('#no_surat').val());
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                // Add _method field for PUT request
                if (id) {
                    formData.append('_method', 'PUT');
                }

                if (fileInput.files.length > 0) {
                    formData.append('qr_surat_image', fileInput.files[0]);
                }

                // Show loading state
                $('#btn-save').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                $('#btn-save').attr('disabled', true);
                clearErrors();

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        $('#modal-form').modal('hide');
                        loadData();

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
                            // Clear previous errors
                            clearErrors();
                            
                            // Handle validation errors if they exist
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                
                                if (errors.tahun_akademik_id) {
                                    $('#error-tahun_akademik_id').text(errors.tahun_akademik_id[0]);
                                    $('#tahun_akademik_id').addClass('is-invalid');
                                }
                                if (errors.no_surat) {
                                    $('#error-no_surat').text(errors.no_surat[0]);
                                    $('#no_surat').addClass('is-invalid');
                                }
                                if (errors.qr_surat_image) {
                                    $('#error-qr_surat_image').text(errors.qr_surat_image[0]);
                                    $('#qr_surat_image').closest('.custom-file').addClass(
                                        'is-invalid');
                                }
                            }

                            // Show error message in toast
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: xhr.responseJSON.message,
                                    icon: 'error',
                                    position: 'top-end',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 5000
                                });
                            }
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: (xhr.responseJSON && xhr.responseJSON.message) ||
                                    'Terjadi kesalahan saat menyimpan data',
                                icon: 'error',
                                position: 'top-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 5000
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
                    url: `{{ url('settings/surat-pengantar') }}/${id}`,
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#modal-delete').modal('hide');
                        loadData();

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
                            text: (xhr.responseJSON && xhr.responseJSON.message) ||
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

            // Function to load data
            function loadData() {
                $.ajax({
                    url: "{{ route('settings.surat-pengantar.data') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.data && response.data.length > 0) {
                            displayData(response.data);
                        } else {
                            displayNoData();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: (xhr.responseJSON && xhr.responseJSON.message) || 
                                'Terjadi kesalahan saat mengambil data',
                            icon: 'error',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            }

            // Function to display data
            function displayData(data) {
                $('#no-data-message').hide();
                $('#data-container').show();
                $('#btn-add').show(); // Selalu tampilkan tombol tambah

                // Kosongkan tabel terlebih dahulu
                $('#table-body').empty();
                
                // Isi tabel dengan data
                $.each(data, function(index, item) {
                    // Pastikan item tidak undefined dan memiliki properti yang dibutuhkan
                    if (!item) return;
                    
                    // Siapkan data dengan pengecekan null/undefined
                    var tahunAkademikText = '-';
                    if (item.tahun_akademik) {
                        tahunAkademikText = item.tahun_akademik.tahun_ajaran + ' - ' + item.tahun_akademik.tipe_semester;
                    }
                    
                    var noSurat = item.no_surat || '-';
                    var qrImage = item.qr_surat_image ? 
                        `<img src="{{ asset('storage/') }}/${item.qr_surat_image}" alt="QR Surat" class="img-thumbnail" style="max-width:100px;">` : 
                        '-';
                    
                    var row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${tahunAkademikText}</td>
                            <td>${noSurat}</td>
                            <td>${qrImage}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info btn-edit" data-id="${item.id}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="${item.id}">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#table-body').append(row);
                });
            }

            // Function to display no data message
            function displayNoData() {
                $('#no-data-message').show();
                $('#data-container').hide();
                $('#btn-add').show();
            }

            // Custom file input handler
            $('#qr_surat_image').change(function() {
                var fileName = $(this).val().split('\\').pop();
                $('.custom-file-label').text(fileName || 'Pilih file gambar...');

                // Clear previous error and info
                $('#error-qr_surat_image').text('');
                $('#file-info').hide();
                $(this).closest('.custom-file').removeClass('is-invalid');

                // Real-time validation
                if (this.files.length > 0) {
                    var file = this.files[0];
                    var maxSize = 2 * 1024 * 1024; // 2MB in bytes

                    // Show file info
                    var fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    $('#file-info').text('Ukuran file: ' + fileSizeMB + ' MB').show();

                    // Preview image
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview-qr-image').attr('src', e.target.result).show();
                        $('#preview-container').show();
                    };
                    reader.readAsDataURL(file);

                    if (file.size > maxSize) {
                        $('#error-qr_surat_image').text('Ukuran file tidak boleh lebih dari 2MB');
                        $(this).closest('.custom-file').addClass('is-invalid');

                        // Show toast notification
                        Swal.fire({
                            title: 'Error!',
                            text: 'Ukuran file tidak boleh lebih dari 2MB',
                            icon: 'error',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 5000
                        });

                        // Clear the file input
                        $(this).val('');
                        $('.custom-file-label').text('Pilih file gambar...');
                        $('#file-info').hide();
                        $('#preview-qr-image').attr('src', '').hide();
                        $('#preview-container').hide();
                        return;
                    }

                    // Validate file type
                    var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                    if (!allowedTypes.includes(file.type)) {
                        $('#error-qr_surat_image').text('Format file harus JPG, PNG, atau GIF');
                        $(this).closest('.custom-file').addClass('is-invalid');

                        // Show toast notification
                        Swal.fire({
                            title: 'Error!',
                            text: 'Format file harus JPG, PNG, atau GIF',
                            icon: 'error',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 5000
                        });

                        // Clear the file input
                        $(this).val('');
                        $('.custom-file-label').text('Pilih file gambar...');
                        $('#file-info').hide();
                        $('#preview-qr-image').attr('src', '').hide();
                        $('#preview-container').hide();
                        return;
                    }
                } else {
                    $('#file-info').hide();
                    $('#preview-qr-image').attr('src', '').hide();
                    $('#preview-container').hide();
                }
            });

            // Helper function to clear validation errors
            function clearErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('.custom-file.is-invalid').removeClass('is-invalid');
                $('#file-info').hide();
            }
        });
    </script>
@endpush
