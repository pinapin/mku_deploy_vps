@extends('layouts.master')

@section('title', 'Data UMKM')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar UMKM</h3>
                @if(Session::get('role') == 'admin')
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                        <i class="fas fa-plus"></i> Tambah UMKM
                    </button>
                </div>
                @endif
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
                            <th>Alamat</th>
                            @if(Session::get('role') == 'admin')
                            <th width="8%">Aksi</th>
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
                            @foreach($kategoriUmkm as $kategori)
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
                        <textarea class="form-control" id="alamat_umkm" name="alamat_umkm"
                            placeholder="Masukkan alamat" rows="3" required></textarea>
                        <div class="invalid-feedback" id="error-alamat_umkm"></div>
                    </div>
                    <div class="form-group">
                        <label for="logo_umkm" class="form-label">Logo UMKM</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="logo_umkm" name="logo_umkm" accept="image/*">
                                <label class="custom-file-label" for="logo_umkm">Pilih file</label>
                            </div>
                        </div>
                        <small class="form-text text-danger">Format: JPG, JPEG, PNG, GIF. Maksimal ukuran: 2MB</small>
                        <div class="invalid-feedback" id="error-logo_umkm"></div>
                        <div class="mt-2" id="logo-preview-container" style="display: none;">
                            <img id="logo-preview" src="" alt="Logo Preview" style="max-height: 100px; max-width: 100%;">
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
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input@1.3.4/dist/bs-custom-file-input.min.js"></script>
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
                "url": "{{ route('master.umkm.data') }}",
                "type": "GET"
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
                    "data": "alamat_umkm"
                },
                @if(Session::get('role') == 'admin')
                {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
              <button type="button" class="btn btn-xs btn-info btn-edit" data-id="${row.id}"
                      data-toggle="tooltip" data-placement="top" title="Edit UMKM">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="${row.id}"
                      data-toggle="tooltip" data-placement="top" title="Hapus UMKM">
                <i class="fas fa-trash"></i>
              </button>
            `;
                    }
                }
                @endif
            ],
          "drawCallback": function(settings) {
              // Initialize tooltips after each draw
              $('[data-toggle="tooltip"]').tooltip();
          }
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

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
                url: `{{ url('master/umkm') }}/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const data = response.data;
                    $('#kategori_umkm_id').val(data.kategori_umkm_id);
                    $('#nama_umkm').val(data.nama_umkm);
                    $('#nama_pemilik_umkm').val(data.nama_pemilik_umkm);
                    $('#jabatan_umkm').val(data.jabatan_umkm);
                    $('#no_hp_umkm').val(data.no_hp_umkm);
                    $('#email_umkm').val(data.email_umkm);
                    $('#alamat_umkm').val(data.alamat_umkm);

                    // Show logo preview if exists
                    if (data.logo_umkm) {
                        $('#logo-preview').attr('src', `{{ asset('storage') }}/${data.logo_umkm}`);
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
                        text:
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
            var url = id ? `{{ url('master/umkm') }}/${id}` : "{{ route('master.umkm.store') }}";
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
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text:
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
                url: `{{ url('master/umkm') }}/${id}`,
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
                        text: 
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
    });
</script>
@endpush