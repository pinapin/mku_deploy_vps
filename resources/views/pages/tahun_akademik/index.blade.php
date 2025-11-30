@extends('layouts.master')

@section('title', 'Tahun Akademik')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Tahun Akademik</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                        <i class="fas fa-plus"></i> Tambah Tahun Akademik
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="tahun-akademik-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Tipe Semester</th>
                            <th>Status Aktif</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal Form -->
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Tambah Tahun Akademik</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-tahun-akademik">
                <div class="modal-body">
                    <input type="hidden" id="tahun-akademik-id" value="">
                    <div class="form-group">
                        <label for="tahun_ajaran">Tahun Ajaran</label>
                        <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" placeholder="Contoh: 2023/2024">
                        <div class="invalid-feedback" id="tahun_ajaran-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="tipe_semester">Tipe Semester</label>
                        <select class="form-control" id="tipe_semester" name="tipe_semester">
                            <option value="">Pilih Tipe Semester</option>
                            <option value="Semester Ganjil">Semester Ganjil</option>
                            <option value="Semester Genap">Semester Genap</option>
                        </select>
                        <div class="invalid-feedback" id="tipe_semester-error"></div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_aktif" name="is_aktif">
                            <label class="custom-control-label" for="is_aktif">Aktifkan Tahun Akademik Ini</label>
                        </div>
                        <small class="form-text text-muted">Jika diaktifkan, tahun akademik lain akan dinonaktifkan secara otomatis.</small>
                        <div class="invalid-feedback" id="is_aktif-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
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
                <p>Apakah Anda yakin ingin menghapus data ini?</p>
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
@endsection

@push('scripts')
<script>
    $(function() {
        // Initialize DataTable
        var table = $('#tahun-akademik-table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "processing": true,
            "language": {
                "processing": "<i class='fas fa-spinner fa-spin fa-2x'></i>"
            },
            "ajax": {
                "url": "{{ route('master.tahun-akademik.data') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    "data": "tahun_ajaran"
                },
                {"data": "tipe_semester"},
                {
                    "data": "is_aktif",
                    "render": function(data, type, row) {
                        if (data == 1 || data === true) {
                            return '<span class="badge badge-success">Aktif</span>';
                        } else {
                            return '<span class="badge badge-secondary">Tidak Aktif</span>';
                        }
                    }
                },
                {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
              <button type="button" class="btn btn-xs btn-info btn-edit" data-id="${row.id}"
                      data-toggle="tooltip" data-placement="top" title="Edit Tahun Akademik">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="${row.id}"
                      data-toggle="tooltip" data-placement="top" title="Hapus Tahun Akademik">
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
            $('#form-tahun-akademik').trigger('reset');
            $('#tahun-akademik-id').val('');
            $('#modal-title').text('Tambah Tahun Akademik');
            $('#modal-form').modal('show');
            clearErrors();
        });

        // Show Edit Modal
        $('#tahun-akademik-table').on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $('#tahun-akademik-id').val(id);
            $('#modal-title').text('Edit Tahun Akademik');
            clearErrors();

            // Get data from server
            $.ajax({
                url: `{{ url('master/tahun-akademik') }}/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#tahun_ajaran').val(response.data.tahun_ajaran);
                    $('#tipe_semester').val(response.data.tipe_semester);
                    $('#is_aktif').prop('checked', response.data.is_aktif);
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
        $('#tahun-akademik-table').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            $('#delete-id').val(id);
            $('#modal-delete').modal('show');
        });

        // Handle Form Submit
        $('#form-tahun-akademik').submit(function(e) {
            e.preventDefault();
            var id = $('#tahun-akademik-id').val();
            var url = id ? `{{ url('master/tahun-akademik') }}/${id}` : "{{ route('master.tahun-akademik.store') }}";
            var method = id ? 'PUT' : 'POST';
            var formData = {
                tahun_ajaran: $('#tahun_ajaran').val(),
                tipe_semester: $('#tipe_semester').val(),
                is_aktif: $('#is_aktif').is(':checked')
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
                        const response = xhr.responseJSON;


                        // Display each error on the form
                        if (response.errors) {
                            const errors = response.errors;
                            $.each(errors, function(field, messages) {
                                const inputField = $('#' + field);
                                const errorDisplay = $('#' + field + '-error');

                                inputField.addClass('is-invalid');
                                errorDisplay.text(messages[0]);
                            });
                        }


                        if (response.message.includes('Kombinasi')) {

                            // Tampilkan error pada field yang relevan
                            $('#tipe_semester-error').text(response.message);
                            $('#tahun_ajaran').addClass('is-invalid');
                            $('#tipe_semester').addClass('is-invalid');
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
                url: `{{ url('master/tahun-akademik') }}/${id}`,
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