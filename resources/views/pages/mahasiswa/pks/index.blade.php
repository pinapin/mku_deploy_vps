@extends('layouts.master')

@section('title', 'Data PKS')

@push('css')
    <!-- DataTables -->
    {{-- <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}"> --}}
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <style>
        /* Animasi untuk tombol Upload PKS */
        .btn-upload {
            position: relative;
            overflow: hidden;
            background: linear-gradient(45deg, #17a2b8, #20c997);
            border: none;
            color: white;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
            transition: all 0.3s ease;
            animation: pulse-upload 2s infinite, glow-upload 3s ease-in-out infinite alternate, button-bounce 2s infinite ease-in-out;
            transform: translateZ(0);
        }

        .btn-upload:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-upload:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.6);
            background: linear-gradient(45deg, #20c997, #17a2b8);
        }

        .btn-upload:hover:before {
            left: 100%;
        }

        .btn-upload:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-upload i {
            animation: bounce-icon 1s infinite ease-in-out;
            display: inline-block;
            transform-origin: center bottom;
        }

        /* Pastikan icon tetap bergerak saat hover */
        .btn-upload:hover i {
            animation: bounce-icon 1s infinite ease-in-out;
        }


        @keyframes button-bounce {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-1px) scale(1.02);
            }
        }

        @keyframes pulse-upload {
            0% {
                box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
            }

            50% {
                box-shadow: 0 4px 25px rgba(23, 162, 184, 0.8);
            }

            100% {
                box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
            }
        }

        @keyframes bounce-icon {
            0% {
                transform: translateY(0);
            }

            25% {
                transform: translateY(-4px);
            }

            50% {
                transform: translateY(0);
            }

            75% {
                transform: translateY(-2px);
            }

            100% {
                transform: translateY(0);
            }
        }


        @keyframes glow-upload {
            from {
                box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4), 0 0 10px rgba(23, 162, 184, 0.2);
            }

            to {
                box-shadow: 0 4px 25px rgba(23, 162, 184, 0.8), 0 0 20px rgba(23, 162, 184, 0.4);
            }
        }

        /* Efek ripple saat klik */
        .btn-upload {
            position: relative;
            overflow: hidden;
        }

        .btn-upload:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .btn-upload:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 1;
            }

            20% {
                transform: scale(25, 25);
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: scale(40, 40);
            }
        }

        /* Efek shake untuk menarik perhatian pertama kali */
        .btn-upload.first-attention {
            animation: shake 0.5s ease-in-out, pulse-upload 2s infinite, glow-upload 3s ease-in-out infinite alternate;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-2px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(2px);
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> <strong>Perhatian!</strong> Jika anda menggunakan PKS UMKM yang sudah ada,
                maka tidak perlu membuat PKS lagi. Silahkan lanjut ke menu <a
                    class="text-bold text-blue text-decoration-none"
                    href="{{ route('mahasiswa.laporan-akhir.index') }}">Laporan
                    Akhir</a>
            </div>

            {{-- <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian!</strong>
                <ul class="mb-0">
                    <li>Jika anda menggunakan PKS UMKM yang sudah ada, maka tidak perlu membuat PKS lagi.</li>
                    <li>Warna hijau pada tabel menunjukkan UMKM yang anda ambil.</li>
                </ul>
            </div> --}}

            @if ($cekPKSExist)
                <div id="alert-pks-exist" class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <strong>Perhatian!</strong> Silahkan cetak draft PKS
                    dibawah
                    yang telah dibuat. Lengkapi dengan Tanda Tangan dan Stempel kedua pihak, kemudian draft PKS di scan
                    dan
                    di Upload pada tombol dibawah!</a>
                </div>
            @endif

            <div id="alert-upload-pks" class="alert alert-warning" style="display: none;">
                <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian!</strong> Segera Upload File PKS Yang Sudah
                Bertanda Tangan
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar PKS</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                            <i class="fas fa-plus"></i> Tambah PKS
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="pks-table" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal PKS</th>
                                <th>Nomor PKS</th>
                                <th>Nomor PKS UMKM</th>
                                <th>UMKM</th>
                                <th>Lama Perjanjian</th>
                                <th>PIC</th>
                                <th>Email</th>
                                <th>File Arsip PKS</th>
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
                    <h4 class="modal-title" id="modal-title">Tambah PKS</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-pks" class="form-horizontal">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="pks-id">
                        <div class="form-group">
                            <label for="tgl_pks" class="form-label">Tanggal PKS</label>
                            <input type="date" class="form-control" id="tgl_pks" name="tgl_pks" required>
                            <div class="invalid-feedback" id="error-tgl_pks"></div>
                        </div>

                        <div class="form-group">
                            <label for="umkm_id" class="form-label">UMKM</label>
                            <div class="input-group">
                                <select class="form-control select2" id="umkm_id" name="umkm_id" required>
                                    <option value="">-- Pilih UMKM --</option>
                                    @foreach ($umkms as $umkm)
                                        <option value="{{ $umkm->id }}">{{ $umkm->nama_umkm }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="invalid-feedback" id="error-umkm_id"></div>
                        </div>
                        <div class="form-group">
                            <label for="no_pks_umkm" class="form-label">Nomor PKS UMKM</label>
                            <input type="text" class="form-control" id="no_pks_umkm" name="no_pks_umkm"
                                placeholder="Masukkan nomor PKS dari UMKM">
                            <small class="text-red">*Jika belum memiliki nomor, silahkan diisi contoh:
                                01/NamaUmkm/{{ $bulan_romawi }}/{{ $tahun_ini }}</small>
                            <div class="invalid-feedback" id="error-no_pks_umkm"></div>
                        </div>
                        <div class="form-group">
                            <label for="lama_perjanjian" class="form-label">Lama Perjanjian (tahun)</label>
                            <input type="number" class="form-control" id="lama_perjanjian" name="lama_perjanjian"
                                min="1" required>
                            <div class="invalid-feedback" id="error-lama_perjanjian"></div>
                        </div>
                        <div class="form-group">
                            <label for="pic_pks" class="form-label">Nama PIC UMKM</label>
                            <input type="text" class="form-control" id="pic_pks" name="pic_pks"
                                placeholder="Masukkan nama PIC" required>
                            <div class="invalid-feedback" id="error-pic_pks"></div>
                        </div>
                        <div class="form-group">
                            <label for="email_pks" class="form-label">Email PIC UMKM</label>
                            <input type="email" class="form-control" id="email_pks" name="email_pks"
                                placeholder="Masukkan email" required>
                            <div class="invalid-feedback" id="error-email_pks"></div>
                        </div>
                        <div class="form-group">
                            <label for="alamat_pks" class="form-label">Alamat PKS</label>
                            <textarea class="form-control" id="alamat_pks" name="alamat_pks" rows="3" placeholder="Masukkan alamat"
                                required></textarea>
                            <div class="invalid-feedback" id="error-alamat_pks"></div>
                        </div>
                        @if (!$cekPKSExist)
                        <div id="current-file-info"></div>
                        <div id="file-arsip-pks-section" class="form-group">
                            <label for="edit_file_arsip_pks" class="form-label">File Arsip PKS</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="edit_file_arsip_pks"
                                        name="file_arsip_pks" accept=".pdf">
                                    <label class="custom-file-label" for="edit_file_arsip_pks">Pilih file...</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah file. Format file: PDF
                                (Maks. 5MB)</small>
                            <div class="invalid-feedback" id="error-edit_file_arsip_pks"></div>
                        </div>
                        @endif
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
                    <p>Apakah Anda yakin ingin menghapus PKS ini?</p>
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

    <!-- File Upload Modal -->
    <div class="modal fade" id="modal-upload">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title">Upload File PKS</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-upload" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="upload-pks-id">
                        <div class="form-group">
                            <label for="file_arsip_pks" class="form-label">Pilih File PKS Yang Sudah Bertanda
                                tangan</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file_arsip_pks"
                                        name="file_arsip_pks" accept=".pdf" required>
                                    <label class="custom-file-label" for="file_arsip_pks">Pilih file...</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">Format file: PDF (Maks. 5MB)</small>
                            <div class="invalid-feedback" id="error-file_arsip_pks"></div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info" id="btn-upload">Upload</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/dist/js/bulanIndonesia.js') }}"></script>

    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: "-- Pilih UMKM --",
                dropdownParent: $('#modal-form')
            });

            // Tambahkan efek perhatian khusus untuk tombol Upload PKS
            setTimeout(function() {
                $('.btn-upload.first-attention').each(function(index) {
                    const button = $(this);
                    setTimeout(() => {
                        // Hapus class first-attention setelah animasi shake selesai
                        setTimeout(() => {
                            button.removeClass('first-attention');
                        }, 500);
                    }, index * 200); // Delay untuk setiap tombol
                });
            }, 1000); // Delay 1 detik setelah halaman dimuat

            // Set today's date as default
            $('#tgl_pks').val(moment().format('YYYY-MM-DD'));

            // Initialize DataTable
            var table = $('#pks-table').DataTable({
                "responsive": true,
                "autoWidth": false,
                "processing": true,
                language: {
                    processing: "<i class='fas fa-spinner fa-spin fa-2x'></i>"
                },
                ajax: {
                    url: "{{ route('mahasiswa.pks.getData') }}",
                    dataSrc: function(json) {
                        // Cek apakah mahasiswa sudah memiliki PKS aktif
                        if (json.has_active_pks) {
                            // Sembunyikan tombol Tambah PKS
                            $('#btn-add').hide();
                        } else {
                            // Tampilkan tombol Tambah PKS
                            $('#btn-add').show();
                        }

                        // Cek apakah ada PKS yang belum diupload file-nya
                        let hasUnuploadedFile = false;
                        json.data.forEach(function(item) {
                            if (!item.file_arsip_pks) {
                                hasUnuploadedFile = true;
                            }
                        });

                        // Tampilkan atau sembunyikan alert upload
                        if (hasUnuploadedFile) {
                            $('#alert-pks-exist').show();
                            $('#alert-upload-pks').show();
                        } else {
                            $('#alert-pks-exist').hide();
                            $('#alert-upload-pks').hide();
                        }

                        return json.data;
                    }
                },
                columns: [{
                        "data": null,
                        "render": function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'tgl_pks',
                        name: 'tgl_pks',
                        render: function(data) {
                            return convertMonthToIndonesian(data);
                        }
                    },
                    {
                        data: 'no_pks',
                        name: 'no_pks'
                    },
                    {
                        data: 'no_pks_umkm',
                        name: 'no_pks_umkm',
                        render: function(data) {
                            return data ? data :
                                '<span class="text-muted text-red">Belum diisi</span>';
                        }
                    },
                    {
                        data: 'nama_umkm',
                        name: 'nama_umkm',
                        render: function(data) {
                            return data ??
                                '<span class="text-muted text-red">UMKM dihapus</span>';
                        }
                    },
                    {
                        data: 'lama_perjanjian',
                        name: 'lama_perjanjian',
                        render: function(data) {
                            return data + ' tahun';
                        }
                    },
                    {
                        data: 'pic_pks',
                        name: 'pic_pks'
                    },
                    {
                        data: 'email_pks',
                        name: 'email_pks'
                    },
                    {
                        "data": null,
                        "orderable": false,
                        "className": "text-center",
                        "render": function(data, type, row) {
                            if (row.file_arsip_pks) {
                                let fileUrl = "{{ asset('storage/:file') }}"
                                    .replace(':file', row.file_arsip_pks);
                                return `<a href="${fileUrl}" class="btn btn-xs btn-success" target="_blank">
                                    <i class="fas fa-file"></i> Lihat File
                                </a>`;
                            } else {
                                return `<button type="button" class="btn btn-xs btn-info btn-upload first-attention" data-id="${row.id}">
                                    <i class="fas fa-upload"></i> Upload PKS
                                </button>`;
                            }
                        }
                    },
                    {
                        "data": null,
                        "orderable": false,
                        "className": "text-center",
                        "render": function(data, type, row) {
                            let printUrl = "{{ route('mahasiswa.pks.cetak', ['id' => ':id']) }}"
                                .replace(':id', row.encrypted_id);
                            let deleteButton = row.file_arsip_pks ? '' : `<button data-toggle="tooltip" title="Hapus PKS" type="button" class="btn btn-xs btn-danger btn-delete" data-id="${row.id}">
                                    <i class="fas fa-trash"></i>
                                </button>`;

                            return `
                            <div class="btn-group">
                                <button data-toggle="tooltip" title="Edit PKS" type="button" class="btn btn-xs btn-info btn-edit" data-id="${row.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${deleteButton}
                                <a data-toggle="tooltip" title="Cetak PKS" href="${printUrl}" class="btn btn-xs btn-success btn-print" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        `;
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                drawCallback: function(settings) {
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Clear validation errors
            function clearErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }


            // Show Add Modal
            $('#btn-add').click(function() {
                $('#form-pks').trigger('reset');
                $('#pks-id').val('');
                $('#modal-title').text('Tambah PKS');

                // Sembunyikan form File Arsip PKS saat tambah PKS
                $('#file-arsip-pks-section').hide();
                $('#current-file-info').html('');

                $('#modal-form').modal('show');
                clearErrors();
                $('#tgl_pks').val(moment().format('YYYY-MM-DD'));
                $('.select2').val('').trigger('change');
            });

            // Show Edit Modal
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $('#pks-id').val(id);
                $('#modal-title').text('Edit PKS');
                clearErrors();

                // Show loading state
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

                // Get data
                $.ajax({
                    url: "{{ route('mahasiswa.pks.show', ['id' => ':id']) }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var data = response.data;
                        $('#tgl_pks').val(moment(data.tgl_pks).format('YYYY-MM-DD'));
                        $('#no_pks_umkm').val(data.no_pks_umkm);
                        $('#umkm_id').val(data.umkm_id).trigger('change');
                        $('#lama_perjanjian').val(data.lama_perjanjian);
                        $('#pic_pks').val(data.pic_pks);
                        $('#email_pks').val(data.email_pks);
                        $('#alamat_pks').val(data.alamat_pks);

                        // Reset file input
                        $('#edit_file_arsip_pks').val('');
                        $('.custom-file-label').text('Pilih file...');

                        // Tampilkan form File Arsip PKS saat edit PKS
                        $('#file-arsip-pks-section').show();

                        // Show current file info if exists
                        if (data.file_arsip_pks) {
                            $('#current-file-info').html(
                                `<small class="text-info">File saat ini: ${data.file_arsip_pks}</small>`
                            );
                        } else {
                            $('#current-file-info').html('');
                        }

                        $('#modal-form').modal('show');
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: "error",
                            title: xhr.responseJSON?.message ||
                                "Terjadi kesalahan saat mengambil data"
                        });
                    }
                });
            });

            // Show Delete Modal
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                $('#delete-id').val(id);
                $('#modal-delete').modal('show');
            });

            // Confirm Delete
            $('#btn-confirm-delete').click(function() {
                var id = $('#delete-id').val();

                // Show loading state
                $(this).html('<i class="fas fa-spinner fa-spin"></i> Menghapus...');
                $(this).attr('disabled', true);

                // Delete data
                $.ajax({
                    url: "{{ route('mahasiswa.pks.destroy', ['id' => ':id']) }}".replace(':id',
                        id),
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#modal-delete').modal('hide');

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

                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: "error",
                            title: xhr.responseJSON?.message ||
                                "Terjadi kesalahan saat menghapus data"
                        });
                    },
                    complete: function() {
                        // Reset button state
                        $('#btn-confirm-delete').html('Hapus');
                        $('#btn-confirm-delete').attr('disabled', false);
                    }
                });
            });

            // Show Upload Modal
            $(document).on('click', '.btn-upload', function() {
                var id = $(this).data('id');
                $('#upload-pks-id').val(id);
                $('#form-upload').trigger('reset');
                $('.custom-file-label').text('Pilih file...');
                $('#modal-upload').modal('show');
                clearErrors();
            });

            // Handle file input change
            $('input[type="file"]').change(function(e) {
                var fileName = e.target.files[0].name;
                $(this).next('.custom-file-label').html(fileName);
            });

            // Submit Upload Form
            $('#form-upload').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var id = $('#upload-pks-id').val();
                var url = "{{ route('mahasiswa.pks.uploadFile', ['id' => ':id']) }}".replace(':id', id);

                // Show loading state
                $('#btn-upload').html('<i class="fas fa-spinner fa-spin"></i> Mengupload...');
                $('#btn-upload').attr('disabled', true);
                clearErrors();

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
                    icon: "info",
                    title: "Mengupload file..."
                });

                // Upload file
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#modal-upload').modal('hide');
                        table.ajax.reload();

                        Toast.fire({
                            icon: "success",
                            title: response.message
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON;
                            if (errors.errors) {
                                // Handle validation errors
                                $.each(errors.errors, function(key, value) {
                                    $('#' + key).addClass('is-invalid');
                                    $('#error-' + key).text(value[0]);
                                });
                            } else if (errors.message) {
                                // General error
                                Toast.fire({
                                    icon: "error",
                                    title: errors.message
                                });
                            }
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: xhr.responseJSON?.message ||
                                    "Terjadi kesalahan saat mengupload file"
                            });
                        }
                    },
                    complete: function() {
                        // Reset button state
                        $('#btn-upload').html('Upload');
                        $('#btn-upload').attr('disabled', false);
                    }
                });
            });

            // Submit Form
            $('#form-pks').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var id = $('#pks-id').val();
                var url = id ? "{{ route('mahasiswa.pks.update', ['id' => ':id']) }}".replace(':id', id) :
                    "{{ route('mahasiswa.pks.store') }}";
                // UBAH INI: Selalu gunakan POST
                var method = 'POST';

                // Show loading state
                $('#btn-save').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                $('#btn-save').attr('disabled', true);
                clearErrors();

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
                    icon: "info",
                    title: "Menyimpan data..."
                });

                // Submit data
                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // $('#modal-form').modal('hide');
                        // table.ajax.reload();

                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);

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
                            if (errors.errors) {
                                // Handle validation errors
                                $.each(errors.errors, function(key, value) {
                                    $('#' + key).addClass('is-invalid');
                                    $('#error-' + key).text(value[0]);
                                });
                            } else if (errors.message) {
                                // General error
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
                                    icon: "error",
                                    title: errors.message
                                });
                            }
                        } else {
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
                                icon: "error",
                                title: xhr.responseJSON?.message ||
                                    "Terjadi kesalahan saat menyimpan data"
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
        });
    </script>
@endpush
