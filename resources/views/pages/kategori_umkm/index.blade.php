@extends('layouts.master')

@section('title', 'Kategori UMKM')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Kategori UMKM</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                        <i class="fas fa-plus"></i> Tambah Kategori
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table id="kategori-table" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kategori</th>
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
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title">Tambah Kategori UMKM</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-kategori" class="form-horizontal">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="kategori-id">
                    <div class="form-group">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text"
                            class="form-control rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            id="nama_kategori" name="nama_kategori" placeholder="Masukkan nama kategori" required>
                        <div class="invalid-feedback" id="error-nama_kategori"></div>
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
                <p>Apakah Anda yakin ingin menghapus kategori ini?</p>
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
<script>
    $(function() {
        // Initialize DataTable
        var table = $('#kategori-table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "processing": true,
            "language": {
                "processing": "<i class='fas fa-spinner fa-spin fa-2x'></i>"
            },
            "ajax": {
                "url": "{{ route('master.kategori-umkm.data') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    "data": "nama_kategori"
                },
                {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
              <button type="button" class="btn btn-xs btn-info btn-edit" data-id="${row.id}"
                      data-toggle="tooltip" data-placement="top" title="Edit Kategori UMKM">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="${row.id}"
                      data-toggle="tooltip" data-placement="top" title="Hapus Kategori UMKM">
                <i class="fas fa-trash"></i>
              </button>
            `;
                    }
                }
            ],
            "drawCallback": function(settings) {
                // Initialize tooltips after each draw
                $('[data-toggle="tooltip"]').tooltip();
            }
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Show Add Modal
        $('#btn-add').click(function() {
            $('#form-kategori').trigger('reset');
            $('#kategori-id').val('');
            $('#modal-title').text('Tambah Kategori UMKM');
            $('#modal-form').modal('show');
            clearErrors();
        });

        // Show Edit Modal
        $('#kategori-table').on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $('#kategori-id').val(id);
            $('#modal-title').text('Edit Kategori UMKM');
            clearErrors();

            // Get data from server
            $.ajax({
                url: `{{ url('master/kategori-umkm') }}/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#nama_kategori').val(response.data.nama_kategori);
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
        $('#kategori-table').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            $('#delete-id').val(id);
            $('#modal-delete').modal('show');
        });

        // Handle Form Submit
        $('#form-kategori').submit(function(e) {
            e.preventDefault();
            var id = $('#kategori-id').val();
            var url = id ? `{{ url('master/kategori-umkm') }}/${id}` : "{{ route('master.kategori-umkm.store') }}";
            var method = id ? 'PUT' : 'POST';
            var formData = {
                nama_kategori: $('#nama_kategori').val()
            };

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
                            $('#error-nama_kategori').text(errors.message);
                            $('#nama_kategori').addClass('is-invalid');
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
                url: `{{ url('master/kategori-umkm') }}/${id}`,
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
    });
</script>
@endpush