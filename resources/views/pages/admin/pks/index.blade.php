@extends('layouts.master')

@section('title', 'Data PKS')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
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

        /* Style for clickable UMKM column */
        .btn-detail-umkm {
            text-decoration: none;
            transition: all 0.2s ease-in-out;
        }

        .btn-detail-umkm:hover {
            text-decoration: none;
            color: #0056b3 !important;
            transform: scale(1.02);
        }

        .btn-detail-umkm u {
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar PKS</h3>
                    {{-- <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add">
                            <i class="fas fa-plus"></i> Tambah PKS
                        </button>
                    </div> --}}
                    <div class="card-tools">
                        @if (Session::get('role') == 'admin')
                            <button type="button" class="btn btn-primary btn-sm" id="btn-add-arsip-pks">
                                <i class="fas fa-plus"></i> Tambah Arsip PKS dan UMKM
                            </button>
                        @endif
                        <button type="button" class="btn btn-info btn-sm ml-1" id="btn-arsip">
                            <i class="fas fa-archive"></i> Arsip UMKM
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
                                <th>Input By</th>
                                <th>File Arsip</th>
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
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Tambah PKS</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-pks" class="form-horizontal" enctype="multipart/form-data">
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
                            <input type="hidden" name="nama_umkm" id="nama_umkm" readonly>
                        </div>
                        <div class="form-group">
                            <label for="no_pks" class="form-label">Nomor PKS</label>
                            <input type="text" class="form-control" id="no_pks" name="no_pks"
                                placeholder="Masukkan nomor PKS" required>
                            <small class="text-danger">*Nomor PKS tidak dapat diubah</small>
                            <div class="invalid-feedback" id="error-no_pks"></div>
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
                        <div class="form-group">
                            <label for="file_arsip_pks" class="form-label">File Arsip PKS</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file_arsip_pks"
                                        name="file_arsip_pks" accept=".pdf">
                                    <label class="custom-file-label" for="file_arsip_pks">Pilih file</label>
                                </div>
                            </div>
                            <small class="text-muted">Format: PDF. Maks: 10MB</small>
                            <div class="invalid-feedback" id="error-file_arsip_pks"></div>
                            <div id="current-file-info" class="mt-2" style="display: none;">
                                <p>File saat ini: <a id="current-file-link" href="#" target="_blank">Lihat file</a>
                                </p>
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

    <!-- Modal Form Tambah Arsip PKS dan UMKM -->
    <div class="modal fade" id="modal-form-arsip-pks">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Arsip PKS dan UMKM</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-arsip-pks" class="form-horizontal" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Data UMKM</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="kategori_umkm_id" class="form-label">Kategori UMKM</label>
                                    <select class="form-control select2" id="kategori_umkm_id" name="kategori_umkm_id"
                                        required>
                                        <option value="">-- Pilih Kategori UMKM --</option>
                                        @foreach ($kategori_umkms as $kategori)
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
                                    <input type="text" class="form-control" id="nama_pemilik_umkm"
                                        name="nama_pemilik_umkm" placeholder="Masukkan nama pemilik" required>
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
                                    <input type="text" class="form-control" id="no_hp_umkm" name="no_hp_umkm"
                                        placeholder="Masukkan nomor HP" required>
                                    <div class="invalid-feedback" id="error-no_hp_umkm"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email_umkm" class="form-label">Email</label>
                                    <input type="text" class="form-control" id="email_umkm" name="email_umkm"
                                        placeholder="Masukkan email" required>
                                    <div class="invalid-feedback" id="error-email_umkm"></div>
                                </div>
                                <div class="form-group">
                                    <label for="alamat_umkm" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat_umkm" name="alamat_umkm" rows="3" placeholder="Masukkan alamat"
                                        required></textarea>
                                    <div class="invalid-feedback" id="error-alamat_umkm"></div>
                                </div>
                                <div class="form-group">
                                    <label for="logo_umkm" class="form-label">Logo UMKM</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="logo_umkm"
                                                name="logo_umkm" accept="image/*">
                                            <label class="custom-file-label" for="logo_umkm">Pilih file</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Format: JPG, PNG, JPEG. Maks: 2MB</small>
                                    <div class="invalid-feedback" id="error-logo"></div>
                                    <div class="mt-2" id="logo-preview-container" style="display: none;">
                                        <img id="logo-preview" src="" alt="Logo Preview"
                                            style="max-height: 100px; max-width: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">Data PKS</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="tgl_pks_arsip" class="form-label">Tanggal PKS</label>
                                    <input type="date" class="form-control" id="tgl_pks_arsip" name="tgl_pks"
                                        required>
                                    <div class="invalid-feedback" id="error-tgl_pks"></div>
                                </div>

                                <div class="form-group">
                                    <label for="no_pks_arsip" class="form-label">Nomor PKS</label>
                                    <input type="text" class="form-control" id="no_pks_arsip" name="no_pks"
                                        placeholder="Masukkan nomor PKS" required>
                                    <small class="text-red">*Format: 001/UPT
                                        MKU-Ketramp.UMK/PKS/C.06.01/{{ $bulan_romawi }}/{{ $tahun_ini }}</small>
                                    <div class="invalid-feedback" id="error-no_pks"></div>
                                </div>
                                <div class="form-group">
                                    <label for="no_pks_umkm_arsip" class="form-label">Nomor PKS UMKM</label>
                                    <input type="text" class="form-control" id="no_pks_umkm_arsip" name="no_pks_umkm"
                                        placeholder="Masukkan nomor PKS dari UMKM">
                                    <small class="text-red">*Jika belum memiliki nomor, silahkan diisi contoh:
                                        01/NamaUmkm/{{ $bulan_romawi }}/{{ $tahun_ini }}</small>
                                    <div class="invalid-feedback" id="error-no_pks_umkm"></div>
                                </div>
                                <div class="form-group">
                                    <label for="lama_perjanjian_arsip" class="form-label">Lama Perjanjian (tahun)</label>
                                    <input type="number" class="form-control" id="lama_perjanjian_arsip"
                                        name="lama_perjanjian" min="1" required>
                                    <div class="invalid-feedback" id="error-lama_perjanjian"></div>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="pic_pks_arsip" class="form-label">Nama PIC UMKM</label>
                                    <input type="text" class="form-control" id="pic_pks_arsip" name="pic_pks"
                                        placeholder="Masukkan nama PIC" required>
                                    <div class="invalid-feedback" id="error-pic_pks"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email_pks_arsip" class="form-label">Email PIC UMKM</label>
                                    <input type="email" class="form-control" id="email_pks_arsip" name="email_pks"
                                        placeholder="Masukkan email" required>
                                    <div class="invalid-feedback" id="error-email_pks"></div>
                                </div>
                                <div class="form-group">
                                    <label for="alamat_pks_arsip" class="form-label">Alamat PKS</label>
                                    <textarea class="form-control" id="alamat_pks_arsip" name="alamat_pks" rows="3" placeholder="Masukkan alamat"
                                        required></textarea>
                                    <div class="invalid-feedback" id="error-alamat_pks"></div>
                                </div> --}}
                                <div class="form-group">
                                    <label for="file_arsip_pks" class="form-label">File Arsip PKS</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="file_arsip_pks"
                                                name="file_arsip_pks" accept=".pdf">
                                            <label class="custom-file-label" for="file_arsip_pks">Pilih file</label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Format: PDF. Maks: 10MB</small>
                                    <div class="invalid-feedback" id="error-file_arsip_pks"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-save-arsip">Simpan</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <!-- Modal Detail UMKM -->
    <div class="modal fade" id="modal-detail-umkm">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title">Detail UMKM</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 id="detail-nama-umkm" class="text-primary"></h5>
                            <p class="text-muted mb-3"><strong>Kategori:</strong> <span id="detail-kategori"></span></p>
                        </div>
                        <div class="col-md-4 text-center">
                            <img id="detail-logo" src="" alt="Logo UMKM"
                                style="max-height: 100px; max-width: 100%; display: none;">
                            <div id="no-logo-placeholder" style="display: none;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted small">Tidak ada logo</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%"><strong>Nama Pemilik:</strong></td>
                                    <td id="detail-nama-pemilik"></td>
                                </tr>
                                <tr>
                                    <td><strong>Jabatan:</strong></td>
                                    <td id="detail-jabatan"></td>
                                </tr>
                                <tr>
                                    <td><strong>No. HP:</strong></td>
                                    <td id="detail-no-hp"></td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td id="detail-email"></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%"><strong>Input By:</strong></td>
                                    <td id="detail-input-by"></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong>Alamat:</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2" id="detail-alamat"></td>
                                </tr>
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
                            <a class="nav-link active" id="pks-tab" data-toggle="tab" href="#pks" role="tab"
                                aria-controls="pks" aria-selected="true">
                                <i class="fas fa-file-contract"></i> UMKM dengan PKS Berlaku
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="tanpa-pks-tab" data-toggle="tab" href="#tanpa-pks" role="tab"
                                aria-controls="tanpa-pks" aria-selected="false">
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
                                    <li>Daftar UMKM yang memiliki PKS masih berlaku.</li>
                                    <li>File PKS dapat diunduh untuk melihat dokumen kerjasama.</li>
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

@push('scripts')
    <!-- Moment JS -->
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
    {{-- <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <!-- Select2 -->
    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Moment -->
    <script src="{{ asset('assets/plugins/moment/moment.min.js') }}" --}}
    <script src="{{ asset('assets/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(function() {
            // Initialize bs-custom-file-input
            bsCustomFileInput.init();

            // Initialize Select2 - Removed global initialization that was causing issues

            // Reinitialize Select2 for each modal separately to fix dropdown parent issue
            $('#modal-form .select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || "-- Pilih --";
                },
                dropdownParent: $('#modal-form')
            });

            $('#modal-form-arsip-pks .select2').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || "-- Pilih --";
                },
                dropdownParent: $('#modal-form-arsip-pks')
            });

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
                ajax: "{{ route('p2k.pks.getData') }}",
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
                        render: function(data, type, row) {
                            if (data && row.umkm_id) {
                                return `<a href="javascript:void(0)" class="btn-detail-umkm text-primary"
                                        data-umkm-id="${row.umkm_id}"
                                        data-toggle="tooltip" data-placement="top" title="Klik untuk lihat detail">
                                        ${data}
                                       </a>`;
                            }
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
                        data: 'created_by_name',
                        name: 'created_by_name',
                        render: function(data, type, row) {
                            if (data === 'Admin') {
                                return '<span style="color: blue;">' + data + '</span>';
                            }
                            return data;
                        }
                    },
                    {
                        data: 'file_arsip_pks',
                        name: 'file_arsip_pks',
                        render: function(data, type, row) {
                            if (data) {
                                return `<a href="{{ asset('storage') }}/${data.replace('public/', '')}" class="btn btn-xs btn-primary" target="_blank">
                                    <i class="fas fa-file-pdf"></i> Lihat
                                </a>`;
                            }
                            return '-';
                        }
                    },
                    @if(Session::get('role') == 'admin')
                    {
                        "data": null,
                        "orderable": false,
                        "className": "text-center",
                        "render": function(data, type, row) {
                            let printUrl = "{{ route('p2k.pks.cetak', ['id' => ':id']) }}"
                                .replace(':id', row.encrypted_id);
                            if (row.created_by_name != 'Admin') {
                                return `
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-xs btn-info btn-edit" data-id="${row.id}"
                                                data-toggle="tooltip" data-placement="top" title="Edit PKS">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="${row.id}"
                                                data-toggle="tooltip" data-placement="top" title="Hapus PKS">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <a href="${printUrl}" class="btn btn-xs btn-success btn-print" target="_blank"
                                           data-toggle="tooltip" data-placement="top" title="Cetak PKS">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                `;
                            } else {
                                return `
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-xs btn-info btn-edit" data-id="${row.id}"
                                                data-toggle="tooltip" data-placement="top" title="Edit PKS">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="${row.id}"
                                                data-toggle="tooltip" data-placement="top" title="Hapus PKS">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    }
                    @endif
                ],
                // order: [
                //     [1, 'desc']
                // ]
                "drawCallback": function(settings) {
                    // Initialize tooltips after each draw
                    $('[data-toggle="tooltip"]').tooltip();
                }
            });

            // Initialize tooltips
            $('[data-toggle="tooltip"]').tooltip();

            // Clear validation errors
            function clearErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');
            }
            // Function to convert month names to Indonesian
            function convertMonthToIndonesian(date) {
                const monthsIndo = {
                    'January': 'Januari',
                    'February': 'Februari',
                    'March': 'Maret',
                    'April': 'April',
                    'May': 'Mei',
                    'June': 'Juni',
                    'July': 'Juli',
                    'August': 'Agustus',
                    'September': 'September',
                    'October': 'Oktober',
                    'November': 'November',
                    'December': 'Desember'
                };

                let formattedDate = moment(date).format('DD MMMM YYYY');

                // Replace English month names with Indonesian ones
                Object.keys(monthsIndo).forEach(month => {
                    formattedDate = formattedDate.replace(month, monthsIndo[month]);
                });

                return formattedDate;
            }

            // Show Add Modal
            $('#btn-add').click(function() {
                $('#form-pks').trigger('reset');
                $('#pks-id').val('');
                $('#modal-title').text('Tambah PKS');
                clearErrors();
                $('#tgl_pks').val(moment().format('YYYY-MM-DD'));

                // Default: field no_pks readonly
                $('#no_pks').prop('readonly', true);
                $('.text-danger').show(); // Tampilkan pesan peringatan

                // Reset and reinitialize Select2 before showing modal
                $('#modal-form .select2').val(null).trigger('change');

                // Show modal after initializing Select2
                $('#modal-form').modal('show');
            });

            // Show Add Arsip PKS dan UMKM Modal
            $('#btn-add-arsip-pks').click(function() {
                $('#form-arsip-pks').trigger('reset');
                clearErrors();
                $('#tgl_pks_arsip').val(moment().format('YYYY-MM-DD'));
                $('#lama_perjanjian_arsip').val(1);
                $('#logo-preview-container').hide();
                $('.custom-file-label').text('Pilih file');

                // Reset and reinitialize Select2 before showing modal
                $('#modal-form-arsip-pks .select2').val(null).trigger('change');

                // Show modal after initializing Select2
                $('#modal-form-arsip-pks').modal('show');
            });

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

            // Show Edit Modal
            $(document).on('click', '.btn-edit', function() {
                var id = $(this).data('id');
                $('#pks-id').val(id);
                $('#modal-title').text('Edit PKS');
                clearErrors();
                $('#current-file-info').hide(); // Hide file info initially

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
                    url: "{{ route('p2k.pks.show', ['id' => ':id']) }}".replace(':id', id),
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var data = response.data;
                        $('#tgl_pks').val(moment(data.tgl_pks).format('YYYY-MM-DD'));
                        $('#no_pks').val(data.no_pks);
                        $('#no_pks_umkm').val(data.no_pks_umkm);
                        $('#lama_perjanjian').val(data.lama_perjanjian);
                        $('#pic_pks').val(data.pic_pks);
                        $('#email_pks').val(data.email_pks);
                        $('#alamat_pks').val(data.alamat_pks);
                        $('#nama_umkm').val(data.umkm.nama_umkm);

                        // Set readonly attribute for no_pks based on created_by_name
                        if (data.created_by == null) {
                            $('#no_pks').prop('readonly', false);
                            $('.text-danger').hide(); // Hide warning message
                        } else {
                            $('#no_pks').prop('readonly', true);
                            $('.text-danger').show(); // Show warning message
                        }

                        // Display file information if available
                        if (data.file_arsip_pks) {
                            $('#current-file-info').show();
                            $('#current-file-link').attr('href', '{{ asset('storage') }}/' +
                                data.file_arsip_pks.replace('public/', ''));
                        }

                        // Initialize Select2 with value before showing modal
                        $('#umkm_id').val(data.umkm_id).trigger('change');

                        // Show modal after initializing Select2
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

            // Show Detail UMKM Modal
            $(document).on('click', '.btn-detail-umkm', function() {
                var umkmId = $(this).data('umkm-id');

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

                // Get UMKM data
                $.ajax({
                    url: "{{ route('p2k.pks.umkm.show', ['id' => ':id']) }}".replace(':id',
                        umkmId),
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        // Clear previous data
                        $('#detail-nama-umkm').text('');
                        $('#detail-kategori').text('');
                        $('#detail-nama-pemilik').text('');
                        $('#detail-jabatan').text('');
                        $('#detail-no-hp').text('');
                        $('#detail-email').text('');
                        $('#detail-input-by').text('');
                        $('#detail-alamat').text('');
                        $('#detail-logo').hide();
                        $('#no-logo-placeholder').hide();
                    },
                    success: function(response) {
                        var data = response.data;

                        // Fill modal with UMKM data
                        $('#detail-nama-umkm').text(data.nama_umkm || '-');
                        $('#detail-kategori').text(data.kategori_umkm?.nama_kategori || '-');
                        $('#detail-nama-pemilik').text(data.nama_pemilik_umkm || '-');
                        $('#detail-jabatan').text(data.jabatan_umkm || '-');
                        $('#detail-no-hp').text(data.no_hp_umkm || '-');
                        $('#detail-email').text(data.email_umkm || '-');
                        $('#detail-input-by').text(data.mahasiswa?.nama || data.input_by ||
                        '-');
                        $('#detail-alamat').text(data.alamat_umkm || '-');

                        // Handle logo
                        if (data.logo_umkm) {
                            $('#detail-logo').attr('src',
                                `{{ asset('storage') }}/${data.logo_umkm}`);
                            $('#detail-logo').show();
                            $('#no-logo-placeholder').hide();
                        } else {
                            $('#detail-logo').hide();
                            $('#no-logo-placeholder').show();
                        }

                        // Show modal
                        $('#modal-detail-umkm').modal('show');
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: "error",
                            title: xhr.responseJSON?.message ||
                                "Terjadi kesalahan saat mengambil data UMKM"
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
                    url: "{{ route('p2k.pks.destroy', ['id' => ':id']) }}".replace(':id',
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

            // Submit Form
            $('#form-pks').submit(function(e) {
                e.preventDefault();
                var id = $('#pks-id').val();
                var url = id ? "{{ route('p2k.pks.update', ['id' => ':id']) }}".replace(':id', id) :
                    "{{ route('p2k.pks.store') }}";
                var method = id ? 'PUT' : 'POST';

                // Use FormData to handle file uploads
                var formData = new FormData(this);

                // If method is PUT, add _method because FormData doesn't support PUT directly
                if (method === 'PUT') {
                    formData.append('_method', 'PUT');
                }

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
                    type: 'POST', // Always use POST with FormData
                    data: formData,
                    dataType: 'json',
                    contentType: false, // Required for FormData
                    processData: false, // Required for FormData,
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

            // Submit Form Arsip PKS dan UMKM
            $('#form-arsip-pks').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                // Show loading state
                $('#btn-save-arsip').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
                $('#btn-save-arsip').attr('disabled', true);
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
                    url: "{{ route('p2k.pks.storePksUmkm') }}",
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#modal-form-arsip-pks').modal('hide');
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
                                    "Terjadi kesalahan saat menyimpan data"
                            });
                        }
                    },
                    complete: function() {
                        // Reset button state
                        $('#btn-save-arsip').html('Simpan');
                        $('#btn-save-arsip').attr('disabled', false);
                    }
                });
            });

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
                            "url": "{{ route('p2k.pks.arsip') }}",
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
                                    if (data) {
                                        return `<a href="{{ asset('storage') }}/${data.replace('public/', '')}" class="btn btn-xs btn-primary" target="_blank"
                                           data-toggle="tooltip" data-placement="top" title="Unduh File PKS">
                                            <i class="fas fa-file-pdf"></i> Unduh File PKS
                                        </a>`;
                                    }
                                    return '-';
                                }
                            }
                        ],
                        "drawCallback": function(settings) {
                            // Initialize tooltips after each draw
                            $('[data-toggle="tooltip"]').tooltip();
                        }
                    });
                } else {
                    $('#arsip-table').DataTable().ajax.reload();
                    // Initialize tooltips after reload
                    $('[data-toggle="tooltip"]').tooltip();
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
                            "url": "{{ route('p2k.pks.tanpa-pks') }}",
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
                                        return '<span class="badge badge-secondary">' +
                                            data + '</span>';
                                    }
                                }
                            }
                        ],
                        "drawCallback": function(settings) {
                            // Initialize tooltips after each draw
                            $('[data-toggle="tooltip"]').tooltip();
                        }
                    });
                } else {
                    $('#tanpa-pks-table').DataTable().ajax.reload();
                    // Initialize tooltips after reload
                    $('[data-toggle="tooltip"]').tooltip();
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
