@extends('layouts.master')

@section('title', 'Data Dosen P2K')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Dosen P2K</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                                <i class="fas fa-plus"></i> Tambah Dosen
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="btn-get-from-api">
                                <i class="fas fa-sync"></i> Ambil Data dari Server
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="filter_tahun_akademik">Filter Tahun Akademik</label>
                                    <div class="input-group">
                                        <select class="form-control select2" id="filter_tahun_akademik" name="filter_tahun_akademik">
                                            <option value="">Semua Tahun Akademik</option>
                                            @foreach ($tahunAkademiks as $tahunAkademik)
                                                <option value="{{ $tahunAkademik->id }}" {{ $tahunAktif && $tahunAkademik->id == $tahunAktif->id ? 'selected' : '' }}>
                                                    {{ $tahunAkademik->tahun_ajaran }} {{ $tahunAkademik->tipe_semester }}
                                                    @if($tahunAkademik->is_aktif) (Aktif) @endif
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
                        
                            <table id="dosen-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kode Dosen</th>
                                        <th>Nama Dosen</th>
                                        <th>Tahun Akademik</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="form-dosen" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="method" value="POST">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-form-label">Tambah Dosen P2K</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="tahun_akademik_id">Tahun Akademik <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="tahun_akademik_id" name="tahun_akademik_id" required>
                                <option value="">Pilih Tahun Akademik</option>
                                @foreach ($tahunAkademiks as $tahunAkademik)
                                    <option value="{{ $tahunAkademik->id }}" {{ $tahunAktif && $tahunAkademik->id == $tahunAktif->id ? 'selected' : '' }}>
                                        {{ $tahunAkademik->tahun_ajaran }} {{ $tahunAkademik->tipe_semester }}
                                        @if($tahunAkademik->is_aktif) (Aktif) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="tahun_akademik_id_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="kode_dosen">Kode Dosen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kode_dosen" name="kode_dosen" required>
                            <div class="invalid-feedback" id="kode_dosen_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="nama_dosen">Nama Dosen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_dosen" name="nama_dosen" required>
                            <div class="invalid-feedback" id="nama_dosen_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal API Data -->
    <div class="modal fade" id="modal-api-data" tabindex="-1" role="dialog" aria-labelledby="modal-api-data-label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-api-data-label">Data Dosen dari Server</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                            <label for="api_tahun_akademik_id">Tahun Akademik <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="api_tahun_akademik_id" name="api_tahun_akademik_id"
                                required>
                                <option value="">Pilih Tahun Akademik</option>
                                @foreach ($tahunAkademiks as $tahunAkademik)
                                    <option value="{{ $tahunAkademik->id }}" {{ $tahunAktif && $tahunAkademik->id == $tahunAktif->id ? 'selected' : '' }}>
                                        {{ $tahunAkademik->tahun_ajaran }} {{ $tahunAkademik->tipe_semester }}
                                        @if($tahunAkademik->is_aktif) (Aktif) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="search_dosen" placeholder="Cari dosen...">
                    </div>
                    <div class="table-responsive">
                        <table id="api-dosen-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Unit</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="api-dosen-body">
                                <tr>
                                    <td colspan="5" class="text-center">Memuat data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4'
            });

            // Initialize DataTable
            let table = $('#dosen-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: "{{ route('p2k.dosen.data') }}",
                    data: function(d) {
                        d.tahun_akademik_id = $('#filter_tahun_akademik').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_dosen',
                        name: 'kode_dosen'
                    },
                    {
                        data: 'nama_dosen',
                        name: 'nama_dosen'
                    },
                    {
                        data: 'tahun_akademik',
                        name: 'tahun_akademik'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {
                    // Initialize tooltips for newly rendered content
                    $('[data-toggle="tooltip"]').tooltip();
                },
                initComplete: function(settings, json) {
                    // Initialize tooltips for initial content
                    $('[data-toggle="tooltip"]').tooltip();
                }
                // order: [
                //     [1, 'asc']
                // ]
            });

            // Filter by tahun akademik
            $('#filter_tahun_akademik').change(function() {
                table.ajax.reload();
            });
            
            // Reset filter
            $('#btn-reset-filter').click(function() {
                $('#filter_tahun_akademik').val('').trigger('change');
            });

            // Reset form
            function resetForm() {
                $('#form-dosen')[0].reset();
                $('#method').val('POST');
                $('#id').val('');
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                $('.select2').trigger('change');
            }

            // Show add modal
            $('#btn-add').click(function() {
                resetForm();
                $('#modal-form-label').text('Tambah Dosen P2K');
                $('#modal-form').modal('show');
            });

            // Show edit modal
            $(document).on('click', '.btn-edit', function() {
                resetForm();
                let id = $(this).data('id');
                $('#modal-form-label').text('Edit Dosen P2K');
                $('#method').val('PUT');

                $.ajax({
                    url: `{{ url('p2k/dosen') }}/${id}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#id').val(response.data.id);
                        $('#kode_dosen').val(response.data.kode_dosen);
                        $('#nama_dosen').val(response.data.nama_dosen);
                        $('#tahun_akademik_id').val(response.data.tahun_akademik_id).trigger(
                            'change');
                        $('#modal-form').modal('show');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal mengambil data',
                            icon: 'error'
                        });
                    }
                });
            });

            // Submit form
            $('#form-dosen').submit(function(e) {
                e.preventDefault();
                $('.form-control').removeClass('is-invalid');

                let formData = $(this).serialize();
                let method = $('#method').val();
                let id = $('#id').val();
                let url = method === 'POST' ? "{{ route('p2k.dosen.store') }}" :
                    `{{ url('p2k/dosen') }}/${id}`;

                $.ajax({
                    url: url,
                    type: method === 'POST' ? 'POST' : 'PUT',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        $('#modal-form').modal('hide');
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}_error`).text(value[0]);
                            });
                        } else {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: 'error',
                                title: xhr.responseJSON.message || 'Terjadi kesalahan'
                            });
                        }
                    }
                });
            });

            // Delete data
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('p2k/dosen') }}/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.onmouseenter = Swal.stopTimer;
                                        toast.onmouseleave = Swal.resumeTimer;
                                    }
                                });
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.onmouseenter = Swal.stopTimer;
                                        toast.onmouseleave = Swal.resumeTimer;
                                    }
                                });
                                Toast.fire({
                                    icon: 'error',
                                    title: xhr.responseJSON.message || 'Terjadi kesalahan'
                                });
                            }
                        });
                    }
                });
            });

            // Get data from API
            $('#btn-get-from-api').click(function() {
                $('#api-dosen-body').html(
                    '<tr><td colspan="5" class="text-center">Memuat data...</td></tr>');
                $('#modal-api-data').modal('show');

                $.ajax({
                    url: "{{ route('p2k.dosen.api') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            renderApiData(response.data);
                        } else {
                            $('#api-dosen-body').html(
                                '<tr><td colspan="5" class="text-center">Gagal memuat data</td></tr>'
                                );
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#api-dosen-body').html(
                            '<tr><td colspan="5" class="text-center">Gagal memuat data</td></tr>'
                            );
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: 'error',
                            title: 'Gagal mengambil data dari API'
                        });
                    }
                });
            });

            // Render API data
            function renderApiData(data) {
                if (!data || data.length === 0) {
                    $('#api-dosen-body').html('<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>');
                    return;
                }

                let html = '';
                $.each(data, function(index, item) {
                    html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.kode}</td>
                    <td>${item.nama}</td>
                    <td>${item.unit}</td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm btn-select-dosen" 
                            data-kode="${item.kode}" 
                            data-nama="${item.nama}">
                            <i class="fas fa-check"></i>
                        </button>
                    </td>
                </tr>
                `;
                });

                $('#api-dosen-body').html(html);
            }

            // Search API data
            $('#search_dosen').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                $('#api-dosen-body tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Select dosen from API
            $(document).on('click', '.btn-select-dosen', function() {
                let kode = $(this).data('kode');
                let nama = $(this).data('nama');
                let tahunAkademikId = $('#api_tahun_akademik_id').val();

                if (!tahunAkademikId) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: 'error',
                        title: 'Pilih tahun akademik terlebih dahulu'
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('p2k.dosen.store') }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_dosen: kode,
                        nama_dosen: nama,
                        tahun_akademik_id: tahunAkademikId
                    },
                    dataType: 'json',
                    success: function(response) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        });
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON.message || 'Terjadi kesalahan'
                        });
                    }
                });
            });
        });
    </script>
@endpush
