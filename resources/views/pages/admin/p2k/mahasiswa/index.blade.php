@extends('layouts.master')

@section('title', 'Data Mahasiswa')

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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Mahasiswa</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                                <i class="fas fa-plus"></i> Tambah Mahasiswa
                            </button>
                            <button type="button" class="btn btn-success btn-sm" id="btn-import">
                                <i class="fas fa-file-import"></i> Import Data
                            </button>
                            <a href="{{ route('p2k.mahasiswa.import-template') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-file-download"></i> Download Template
                            </a>
                        </div>
                    </div>
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="filter-tahun-akademik">Tahun Akademik</label>
                                            <select class="form-control select2" id="filter-tahun-akademik"
                                                style="width: 100%;">
                                                <option value="">Semua Tahun Akademik</option>
                                                @foreach ($tahunAkademiks as $tahunAkademik)
                                                    <option value="{{ $tahunAkademik->id }}"
                                                        {{ $tahunAkademikAktif && $tahunAkademik->id == $tahunAkademikAktif->id ? 'selected' : '' }}>
                                                        {{ $tahunAkademik->tahun_ajaran }}
                                                        {{ $tahunAkademik->tipe_semester }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="filter-fakultas">Fakultas</label>
                                            <select class="form-control select2" id="filter-fakultas" style="width: 100%;">
                                                <option value="">Semua Fakultas</option>
                                                @foreach ($fakultas as $fak)
                                                    <option value="{{ $fak->id }}">{{ $fak->nama_fakultas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
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


                        <div class="table-responsive">
                            <table id="mahasiswa-table" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>NIM</th>
                                        <th>Nama</th>
                                        <th>Program Studi</th>
                                        <th>Fakultas</th>
                                        <th>Tahun Akademik</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Mahasiswa -->
    <div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-form-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-form-label">Tambah Mahasiswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-mahasiswa">
                    <div class="modal-body">
                        <input type="hidden" id="form-method" value="post">
                        <input type="hidden" id="mahasiswa-nim" name="nim">
                        <div class="form-group">
                            <label for="nim">NIM</label>
                            <input type="text" class="form-control" id="nim" name="nim" maxlength="9"
                                required>
                            <small class="text-muted">Maksimal 9 karakter</small>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="fakultas_id">Fakultas</label>
                            <select class="form-control select2" id="fakultas_id" name="fakultas_id"
                                style="width: 100%;" required>
                                <option value="">Pilih Fakultas</option>
                                @foreach ($fakultas as $fak)
                                    <option value="{{ $fak->id }}">{{ $fak->nama_fakultas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="prodi_id">Program Studi</label>
                            <select class="form-control select2" id="prodi_id" name="prodi_id" style="width: 100%;"
                                required>
                                <option value="">Pilih Program Studi</option>
                                @foreach ($programStudis as $prodi)
                                    <option value="{{ $prodi->id }}" data-fakultas="{{ $prodi->fakultas_id }}">
                                        {{ $prodi->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tahun_akademik_id">Tahun Akademik</label>
                            <select class="form-control select2" id="tahun_akademik_id" name="tahun_akademik_id"
                                style="width: 100%;" required>
                                <option value="">Pilih Tahun Akademik</option>
                                @foreach ($tahunAkademiks as $tahunAkademik)
                                    <option value="{{ $tahunAkademik->id }}"
                                        {{ $tahunAkademikAktif && $tahunAkademik->id == $tahunAkademikAktif->id ? 'selected' : '' }}>
                                        {{ $tahunAkademik->tahun_ajaran }} {{ $tahunAkademik->tipe_semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="modal-import-label"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-import-label">Import Data Mahasiswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-import" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="file">File Excel</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file"
                                    accept=".xlsx, .xls" required>
                                <label class="custom-file-label" for="file">Pilih file</label>
                            </div>
                            <small class="text-muted">Format file: .xlsx, .xls</small>
                        </div>
                        <div class="form-group">
                            <label for="import_tahun_akademik_id">Tahun Akademik</label>
                            <select class="form-control" id="import_tahun_akademik_id" name="tahun_akademik_id" required>
                                <option value="">Pilih Tahun Akademik</option>
                                @foreach ($tahunAkademiks as $tahunAkademik)
                                    <option value="{{ $tahunAkademik->id }}"
                                        {{ $tahunAkademikAktif && $tahunAkademik->id == $tahunAkademikAktif->id ? 'selected' : '' }}>
                                        {{ $tahunAkademik->tahun_ajaran }} {{ $tahunAkademik->tipe_semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Pastikan format file sesuai dengan template yang disediakan.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        {{-- <button type="button" class="btn btn-warning" id="btn-validate">Validasi Data</button> --}}
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Validasi -->
    <div class="modal fade" id="modal-validate" tabindex="-1" role="dialog" aria-labelledby="modal-validate-label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-validate-label">Hasil Validasi Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
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
                                    <span class="info-box-number" id="matched-count">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Data Tidak Cocok</span>
                                    <span class="info-box-number" id="unmatched-count">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-danger">
                                <div class="card-header">
                                    <h3 class="card-title">Data Tidak Cocok</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="unmatched-table">
                                            <thead>
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th>NIM</th>
                                                    <th>Nama</th>
                                                </tr>
                                            </thead>
                                            <tbody id="unmatched-data">
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3" id="export-unmatched-container" style="display: none;">
                                        <button type="button" class="btn btn-warning" id="btn-export-unmatched">
                                            <i class="fas fa-file-export"></i> Export Data Tidak Cocok
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            // Initialize custom file input
            bsCustomFileInput.init();

            // Initialize Select2 for modal form
            $('.select2').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#modal-form')
            });
            
            // Reinitialize Select2 for fakultas and tahun akademik
            $('#fakultas_id, #tahun_akademik_id').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#modal-form')
            });
            
            // Reinitialize Select2 for prodi with templateResult
            $('#prodi_id').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#modal-form'),
                templateResult: function (data) {
                    // Jika opsi disabled, jangan tampilkan
                    if (data.element && $(data.element).prop('disabled')) {
                        return null;
                    }
                    return data.text;
                }
            });

            // Initialize Select2 for filters
            $('#filter-tahun-akademik, #filter-fakultas').select2({
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

            // Form fakultas-prodi relation
            $('#fakultas_id').on('change', function() {
                var fakultasId = $(this).val();
                var $prodiSelect = $('#prodi_id');

                // Reset program studi
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

            // DataTable
            var table = $('#mahasiswa-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('p2k.mahasiswa.data') }}",
                    data: function(d) {
                        d.tahun_akademik_id = $('#filter-tahun-akademik').val();
                        d.fakultas_id = $('#filter-fakultas').val();
                        d.prodi_id = $('#filter-prodi').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nim',
                        name: 'nim'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'nama_prodi',
                        name: 'nama_prodi'
                    },
                    {
                        data: 'nama_fakultas',
                        name: 'nama_fakultas'
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
                // order: [
                //     [1, 'asc']
                // ],
                "drawCallback": function(settings) {
                    // Initialize tooltips after each draw
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();



            // Event handler untuk filter
            $('#filter-tahun-akademik, #filter-fakultas, #filter-prodi').change(function() {
                table.draw();
            });



            // Reset filter button
            $('#btn-reset-filter').on('click', function() {
                // Reset semua filter
                $('#filter-tahun-akademik').val('{{ $tahunAkademikAktif ? $tahunAkademikAktif->id : '' }}')
                    .trigger('change');
                $('#filter-fakultas').val('').trigger('change');
                $('#filter-prodi').val('').trigger('change');

                // Tampilkan semua opsi program studi
                $('#filter-prodi option').prop('disabled', false).show();
                $('#filter-prodi').trigger('change.select2');

                // Reload table
                table.draw();
            });

            // Add button
            $('#btn-add').on('click', function() {
                $('#form-mahasiswa').trigger('reset');
                $('#form-method').val('post');
                $('#modal-form-label').text('Tambah Mahasiswa');
                $('#modal-form').modal('show');
                
                // Reinitialize Select2 for fakultas and tahun akademik
                $('#fakultas_id, #tahun_akademik_id').select2({
                    theme: 'bootstrap4',
                    dropdownParent: $('#modal-form')
                });
                
                // Reinitialize Select2 for prodi with templateResult
                $('#prodi_id').select2({
                    theme: 'bootstrap4',
                    dropdownParent: $('#modal-form'),
                    templateResult: function (data) {
                        // Jika opsi disabled, jangan tampilkan
                        if (data.element && $(data.element).prop('disabled')) {
                            return null;
                        }
                        return data.text;
                    }
                });
            });

            // Edit button
            $(document).on('click', '.btn-edit', function() {
                var nim = $(this).data('id');
                $.ajax({
                    url: "{{ url('p2k/mahasiswa') }}/" + nim,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        $('#form-mahasiswa').trigger('reset');
                        $('#form-method').val('put');
                        $('#mahasiswa-nim').val(nim);
                        $('#nim').val(response.data.nim);
                        $('#nama').val(response.data.nama);
                        
                        // Dapatkan fakultas_id dari data-fakultas pada option prodi yang dipilih
                        var prodiId = response.data.prodi_id;
                        var fakultasId = $('#prodi_id option[value="' + prodiId + '"]').data('fakultas');
                        
                        // Set nilai fakultas terlebih dahulu dan trigger change untuk memfilter prodi
                        $('#fakultas_id').val(fakultasId).trigger('change');
                        
                        // Kemudian set nilai prodi setelah prodi difilter berdasarkan fakultas
                        setTimeout(function() {
                            $('#prodi_id').val(prodiId).trigger('change.select2');
                        }, 100);
                        $('#tahun_akademik_id').val(response.data.tahun_akademik_id);
                        $('#modal-form-label').text('Edit Mahasiswa');
                        $('#modal-form').modal('show');
                        
                        // Reinitialize Select2 for fakultas and tahun akademik
                        $('#fakultas_id, #tahun_akademik_id').select2({
                            theme: 'bootstrap4',
                            dropdownParent: $('#modal-form')
                        });
                        
                        // Reinitialize Select2 for prodi with templateResult
                        $('#prodi_id').select2({
                            theme: 'bootstrap4',
                            dropdownParent: $('#modal-form'),
                            templateResult: function (data) {
                                // Jika opsi disabled, jangan tampilkan
                                if (data.element && $(data.element).prop('disabled')) {
                                    return null;
                                }
                                return data.text;
                            }
                        });
                        
                        // Trigger perubahan untuk memperbarui tampilan Select2
                        $('#fakultas_id').trigger('change');
                        $('#prodi_id').val(prodiId).trigger('change.select2');
                        $('#tahun_akademik_id').trigger('change.select2');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            });

            // Delete button
            $(document).on('click', '.btn-delete', function() {
                var nim = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data mahasiswa akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ url('p2k/mahasiswa') }}/" + nim,
                            type: 'DELETE',
                            dataType: 'json',
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Berhasil!',
                                    response.message,
                                    'success'
                                );
                                table.draw();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: xhr.responseJSON.message,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });

            // Form submit
            $('#form-mahasiswa').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var method = $('#form-method').val();
                var url = "{{ route('p2k.mahasiswa.store') }}";
                var nim = $('#mahasiswa-nim').val();

                if (method === 'put') {
                    url = "{{ url('p2k/mahasiswa') }}/" + nim;
                }

                $.ajax({
                    url: url,
                    type: method === 'post' ? 'POST' : 'PUT',
                    dataType: 'json',
                    data: formData + "&_token={{ csrf_token() }}",
                    success: function(response) {
                        $('#modal-form').modal('hide');
                        Swal.fire({
                            title: 'Berhasil!',
                            text: response.message,
                            icon: 'success'
                        });
                        table.draw();
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            });

            // Import button
            $('#btn-import').on('click', function() {
                $('#form-import').trigger('reset');
                $('.custom-file-label').text('Pilih file');
                $('#modal-import').modal('show');
            });

            // Validate button
            $('#btn-validate').on('click', function() {
                var formData = new FormData($('#form-import')[0]);
                formData.append('_token', '{{ csrf_token() }}');

                if (!formData.get('file').name) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Pilih file terlebih dahulu!',
                        icon: 'error'
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('p2k.mahasiswa.validate-data') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Memvalidasi data...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        $('#total-data').text(response.data.total_data);
                        $('#matched-count').text(response.data.matched_count);
                        $('#unmatched-count').text(response.data.unmatched_count);

                        // Tampilkan data yang tidak cocok
                        var unmatchedData = response.data.unmatched_data;
                        var html = '';
                        if (unmatchedData.length > 0) {
                            $.each(unmatchedData, function(index, item) {
                                html += '<tr>';
                                html += '<td>' + (index + 1) + '</td>';
                                html += '<td>' + item.nim + '</td>';
                                html += '<td>' + item.nama + '</td>';
                                html += '</tr>';
                            });
                            $('#export-unmatched-container').show();
                        } else {
                            html =
                                '<tr><td colspan="3" class="text-center">Tidak ada data yang tidak cocok</td></tr>';
                            $('#export-unmatched-container').hide();
                        }
                        $('#unmatched-data').html(html);

                        // Simpan data untuk export
                        $('#btn-export-unmatched').data('unmatched', JSON.stringify(
                            unmatchedData));

                        $('#modal-validate').modal('show');
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            });

            // Export unmatched data
            $('#btn-export-unmatched').on('click', function() {
                var unmatchedData = $(this).data('unmatched');

                // Buat form untuk submit
                var form = $('<form>', {
                    'method': 'post',
                    'action': "{{ route('p2k.mahasiswa.export-unmatched-data') }}",
                    'target': '_blank'
                });

                form.append($('<input>', {
                    'name': '_token',
                    'value': '{{ csrf_token() }}',
                    'type': 'hidden'
                }));

                form.append($('<input>', {
                    'name': 'unmatched_data',
                    'value': unmatchedData,
                    'type': 'hidden'
                }));

                $('body').append(form);
                form.submit();
                form.remove();
            });

            // Import form submit
            $('#form-import').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: "{{ route('p2k.mahasiswa.import') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Mengimpor data...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response) {
                        Swal.close();
                        $('#modal-import').modal('hide');

                        if (response.status === 'warning') {
                            var errorHtml = '<ul>';
                            $.each(response.errors, function(index, error) {
                                errorHtml += '<li>Baris ' + error.row + ': ' + error
                                    .message + '</li>';
                            });
                            errorHtml += '</ul>';

                            Swal.fire({
                                title: 'Peringatan!',
                                html: response.message + '<br><br>' + errorHtml,
                                icon: 'warning'
                            });
                        } else {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success'
                            });
                        }

                        table.draw();
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message,
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
@endpush
