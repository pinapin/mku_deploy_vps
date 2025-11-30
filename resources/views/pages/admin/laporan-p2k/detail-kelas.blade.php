@extends('layouts.master')

@section('title', 'Detail Kelas P2K')

@section('content')
<div id="page-top"></div>
<style>
    .hover-card:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
    }
    .hover-shadow:hover {
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        transition: box-shadow 0.3s ease;
    }
    .icon-circle {
        height: 2.5rem;
        width: 2.5rem;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #4e73df;
        color: white;
        margin-right: 0.75rem;
    }
    .btn-hover:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease;
    }
    .fa-rotate-90 {
        transform: rotate(90deg);
        transition: transform 0.3s ease;
    }
    .btn-filter.active {
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        font-weight: bold;
    }
    .page-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease;
    }
    .loader-content {
        text-align: center;
    }
    .loader-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #4e73df;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 15px auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .scroll-to-top {
        position: fixed;
        right: 1rem;
        bottom: 1rem;
        display: none;
        width: 2.75rem;
        height: 2.75rem;
        text-align: center;
        color: #fff;
        background: rgba(78, 115, 223, 0.5);
        line-height: 46px;
        border-radius: 50%;
        transition: all 0.3s ease;
        z-index: 1000;
    }
    .scroll-to-top:hover {
        background: #4e73df;
    }
    .scroll-to-top i {
        font-weight: 800;
    }
</style>
<!-- Page Loader -->
<div class="page-loader" id="pageLoader">
    <div class="loader-content">
        <div class="loader-spinner"></div>
        <h5 class="text-primary">Memuat Data...</h5>
    </div>
</div>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            {{-- <div class="icon-circle bg-primary text-white mr-3">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h1 class="h3 mb-0 text-gray-800">Detail Kelas P2K</h1> --}}
        </div>
        <div>
            <button class="d-none d-sm-inline-block btn btn-primary shadow-sm hover-card btn-hover mr-2 btn-filter active" data-filter="all">
                <i class="fas fa-list fa-sm mr-1"></i> Semua Kelompok
            </button>
            <button class="d-none d-sm-inline-block btn btn-success shadow-sm hover-card btn-hover mr-2 btn-filter" data-filter="border-left-success">
                <i class="fas fa-check-circle fa-sm mr-1"></i> Laporan Tervalidasi
            </button>
            <button class="d-none d-sm-inline-block btn btn-warning shadow-sm hover-card btn-hover mr-2 btn-filter" data-filter="border-left-warning">
                <i class="fas fa-exclamation-circle fa-sm mr-1"></i> Laporan Belum Divalidasi
            </button>
            <button class="d-none d-sm-inline-block btn btn-info shadow-sm hover-card btn-hover mr-2 btn-filter" data-filter="border-left-info">
                <i class="fas fa-file-alt fa-sm mr-1"></i> Belum Ada Laporan
            </button>
            <a href="{{ route('dosen.p2k.index') }}" class="d-none d-sm-inline-block btn btn-outline-primary shadow-sm hover-card btn-hover">
                <i class="fas fa-arrow-left fa-sm mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Informasi Kelas -->
    <div class="card shadow-sm mb-4 border-left-info hover-shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i>Informasi Kelas</h6>
            {{-- <span class="badge badge-info badge-pill">{{ $kelas->tahunAkademik->is_aktif ? 'Aktif' : 'Tidak Aktif' }}</span> --}}
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-3 mb-md-0 hover-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary text-white mr-3">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Tahun Akademik</h6>
                                    <p class="mb-0">{{ $kelas->tahunAkademik->tahun_ajaran }} {{ $kelas->tahunAkademik->tipe_semester }} <span class="badge badge-{{ $kelas->tahunAkademik->is_aktif ? 'success' : 'secondary' }} ml-2">{{ $kelas->tahunAkademik->is_aktif ? 'Aktif' : 'Tidak Aktif' }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-success text-white mr-3">
                                    <i class="fas fa-chalkboard"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Kelas</h6>
                                    <p class="mb-0">{{ $kelas->kelas }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm hover-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-info text-white mr-3">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Dosen</h6>
                                    <p class="mb-0">{{ $kelas->dosen->nama_dosen }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Kelompok -->
    <div class="row" id="kelompokSection">
        <div class="col-12">
            <div class="card shadow-sm mb-4 border-left-primary hover-shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users mr-2"></i>Daftar Kelompok</h6>
                    {{-- <span class="badge badge-primary badge-pill">{{ count($kelompokMahasiswa) }} Kelompok</span> --}}
                </div>
                <div class="card-body">
                    @if(count($kelompokMahasiswa) > 0)
                        <div class="accordion" id="accordionKelompok">
                            @foreach($kelompokMahasiswa as $kelompok)
                                @php
                                    $hasLaporanAkhir = collect($kelompok['mahasiswas'])->contains(function ($mahasiswa) {
                                        return isset($mahasiswa['laporan_akhir']) && $mahasiswa['laporan_akhir'];
                                    });
                                    
                                    $isValidated = collect($kelompok['mahasiswas'])->contains(function ($mahasiswa) {
                                        return isset($mahasiswa['laporan_akhir']) && $mahasiswa['laporan_akhir'] && $mahasiswa['laporan_akhir']['is_validated'];
                                    });
                                    
                                    $borderClass = $isValidated ? 'border-left-success' : ($hasLaporanAkhir ? 'border-left-warning' : 'border-left-info');
                                @endphp
                                <div class="card mb-3 shadow-sm {{ $borderClass }} rounded kelompok-card">
                                    <div class="card-header py-3 bg-white" id="heading{{ $kelompok['no'] }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left text-primary p-0" type="button" data-toggle="collapse" data-target="#collapse{{ $kelompok['no'] }}" aria-expanded="true" aria-controls="collapse{{ $kelompok['no'] }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-circle bg-primary text-white mr-3">
                                                            <i class="fas fa-angle-right"></i>
                                                        </div>
                                                        <div>
                                                            <strong class="h5 mb-0">Kelompok {{ $kelompok['kelompok'] }}</strong>
                                                            <div class="small text-gray-600"><i class="fas fa-store-alt mr-1"></i>{{ $kelompok['umkm'] }}</div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span class="badge badge-pill {{ $kelompok['surat_pengantar'] ? 'badge-success' : 'badge-secondary' }} mr-1" data-toggle="tooltip" title="Surat Pengantar">
                                                            <i class="fas {{ $kelompok['surat_pengantar'] ? 'fa-check' : 'fa-times' }}"></i> SP
                                                        </span>
                                                        
                                                        <span class="badge badge-pill {{ isset($kelompok['pks']['file_path']) ? 'badge-success' : 'badge-secondary' }} mr-1" data-toggle="tooltip" title="PKS">
                                                            <i class="fas {{ isset($kelompok['pks']['file_path']) ? 'fa-check' : 'fa-times' }}"></i> PKS
                                                        </span>

                                                          <span class="badge badge-pill {{ isset($kelompok['ia']['file_path']) ? 'badge-success' : 'badge-secondary' }} mr-1" data-toggle="tooltip" title="IA">
                                                            <i class="fas {{ isset($kelompok['ia']['file_path']) ? 'fa-check' : 'fa-times' }}"></i> IA
                                                        </span>
                                                        
                                                        <span class="badge badge-pill {{ $hasLaporanAkhir ? 'badge-success' : 'badge-secondary' }} mr-1" data-toggle="tooltip" title="Laporan Akhir">
                                                            <i class="fas {{ $hasLaporanAkhir ? 'fa-check' : 'fa-times' }}"></i> LA
                                                        </span>
                                                        
                                                        <span class="badge badge-pill {{ $isValidated ? 'badge-success' : ($hasLaporanAkhir ? 'badge-warning' : 'badge-secondary') }}" data-toggle="tooltip" title="{{ $isValidated ? 'Tervalidasi' : ($hasLaporanAkhir ? 'Menunggu Validasi' : 'Belum Ada Laporan') }}">
                                                            <i class="fas {{ $isValidated ? 'fa-check' : ($hasLaporanAkhir ? 'fa-exclamation' : 'fa-times') }}"></i> Validasi
                                                        </span>
                                                    </div>
                                                </div>
                                            </button>
                                        </h2>
                                    </div>

                                    <div id="collapse{{ $kelompok['no'] }}" class="collapse" aria-labelledby="heading{{ $kelompok['no'] }}" data-parent="#accordionKelompok">
                                        <div class="card-body">
                                            <!-- Status Dokumen Kelompok -->
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <div class="card border-left-info shadow-sm rounded hover-shadow">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                <h5 class="text-primary font-weight-bold"><i class="fas fa-file-alt mr-2"></i>Status Dokumen</h5>
                                                                <span class="badge badge-info badge-pill">Kelompok {{ $kelompok['kelompok'] }}</span>
                                                            </div>
                                                            <div class="row">
                                                                <!-- Surat Pengantar -->
                                                                <div class="col-md-3">
                                                                    <div class="card h-100 border-0 shadow-sm hover-card">
                                                                        <div class="card-body">
                                                                            <div class="d-flex align-items-center mb-3">
                                                                                <div class="icon-circle {{ $kelompok['surat_pengantar'] ? 'bg-success' : 'bg-secondary' }} text-white mr-3">
                                                                                    <i class="fas {{ $kelompok['surat_pengantar'] ? 'fa-check' : 'fa-times' }}"></i>
                                                                                </div>
                                                                                <h6 class="font-weight-bold mb-0">Surat Pengantar</h6>
                                                                            </div>
                                                                            <div class="text-center mt-3">
                                                                                        @if($kelompok['surat_pengantar'])
                                                                                            {{-- <div class="mb-2">
                                                                                                <span class="badge badge-success p-2">Tersedia</span>
                                                                                            </div> --}}
                                                                                            <div class="d-flex flex-column align-items-center w-100">
                                                                                                <a href="{{ route('p2k.surat-pengantar.cetak', ['id' => encrypt($kelompok['surat_pengantar']['id'])]) }}" class="btn btn-sm btn-primary btn-hover w-100" target="_blank" data-toggle="tooltip" title="Lihat File">
                                                                                                    <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen
                                                                                                </a>
                                                                                            </div>
                                                                                        @else
                                                                                            <div class="mb-2">
                                                                                                <span class="badge badge-secondary p-2">Belum Tersedia</span>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- PKS -->
                                                                <div class="col-md-3">
                                                                    <div class="card h-100 border-0 shadow-sm hover-card">
                                                                        <div class="card-body">
                                                                            <div class="d-flex align-items-center mb-3">
                                                                                <div class="icon-circle {{ isset($kelompok['pks']['file_path']) ? 'bg-success' : 'bg-secondary' }} text-white mr-3">
                                                                                    <i class="fas {{ isset($kelompok['pks']['file_path']) ? 'fa-check' : 'fa-times' }}"></i>
                                                                                </div>
                                                                                <h6 class="font-weight-bold mb-0">PKS</h6>
                                                                            </div>
                                                                            <div class="text-center mt-3">
                                                                                @if(isset($kelompok['pks']['file_path']))
                                                                                    {{-- <div class="mb-2">
                                                                                        <span class="badge badge-success p-2">Tersedia</span>
                                                                                    </div> --}}
                                                                                    <div class="d-flex flex-column align-items-center w-100">
                                                                                        {{-- @if($kelompok['pks']['file_path']) --}}
                                                                                            <a href="{{ asset('storage/' . $kelompok['pks']['file_path']) }}" class="btn btn-sm btn-primary btn-hover w-100" target="_blank" data-toggle="tooltip" title="Lihat File PKS">
                                                                                                <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen
                                                                                            </a>
                                                                                        {{-- @else
                                                                                            <a href="{{ route('dosen.pks.cetak', ['id' => encrypt($kelompok['pks']['id'])]) }}" class="btn btn-sm btn-primary btn-hover w-100" target="_blank" data-toggle="tooltip" title="Lihat File">
                                                                                                <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen
                                                                                            </a>
                                                                                        @endif --}}
                                                                                    </div>
                                                                                @else
                                                                                    <div class="mb-2">
                                                                                        <span class="badge badge-secondary p-2">Belum Tersedia</span>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                 <!-- IA -->
                                                                <div class="col-md-3">
                                                                    <div class="card h-100 border-0 shadow-sm hover-card">
                                                                        <div class="card-body">
                                                                            <div class="d-flex align-items-center mb-3">
                                                                                <div class="icon-circle {{ isset($kelompok['ia']['file_path']) ? 'bg-success' : 'bg-secondary' }} text-white mr-3">
                                                                                    <i class="fas {{ isset($kelompok['ia']['file_path']) ? 'fa-check' : 'fa-times' }}"></i>
                                                                                </div>
                                                                                <h6 class="font-weight-bold mb-0">IA</h6>
                                                                            </div>
                                                                            <div class="text-center mt-3">
                                                                                @if(isset($kelompok['ia']['file_path']))
                                                                                    {{-- <div class="mb-2">
                                                                                        <span class="badge badge-success p-2">Tersedia</span>
                                                                                    </div> --}}
                                                                                    <div class="d-flex flex-column align-items-center w-100">
                                                                                        {{-- @if($kelompok['pks']['file_path']) --}}
                                                                                            <a href="{{ asset('storage/' . $kelompok['ia']['file_path']) }}" class="btn btn-sm btn-primary btn-hover w-100" target="_blank" data-toggle="tooltip" title="Lihat File IA">
                                                                                                <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen IA
                                                                                            </a>
                                                                                       
                                                                                        {{-- @endif --}}
                                                                                    </div>
                                                                                @else
                                                                                    <div class="mb-2">
                                                                                        <span class="badge badge-secondary p-2">Belum Tersedia</span>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                
                                                                <!-- Laporan Akhir -->
                                                                <div class="col-md-3">
                                                                    <div class="card h-100 border-0 shadow-sm hover-card">
                                                                        <div class="card-body">
                                                                            @php
                                                                                $hasLaporanAkhir = collect($kelompok['mahasiswas'])->contains(function ($mahasiswa) {
                                                                                    return isset($mahasiswa['laporan_akhir']) && $mahasiswa['laporan_akhir'];
                                                                                });
                                                                                
                                                                                $isValidated = collect($kelompok['mahasiswas'])->contains(function ($mahasiswa) {
                                                                                    return isset($mahasiswa['laporan_akhir']) && $mahasiswa['laporan_akhir'] && $mahasiswa['laporan_akhir']['is_validated'];
                                                                                });
                                                                                
                                                                                $laporanToValidate = collect($kelompok['mahasiswas'])
                                                                                    ->filter(function ($mahasiswa) {
                                                                                        return isset($mahasiswa['laporan_akhir']) && $mahasiswa['laporan_akhir'] && !$mahasiswa['laporan_akhir']['is_validated'];
                                                                                    })
                                                                                    ->first();
                                                                                    
                                                                                $statusColor = $isValidated ? 'bg-success' : ($hasLaporanAkhir ? 'bg-warning' : 'bg-secondary');
                                                                                $statusIcon = $isValidated ? 'fa-check' : ($hasLaporanAkhir ? 'fa-exclamation' : 'fa-times');
                                                                            @endphp
                                                                            
                                                                            <div class="d-flex align-items-center mb-3">
                                                                                <div class="icon-circle {{ $statusColor }} text-white mr-3">
                                                                                    <i class="fas {{ $statusIcon }}"></i>
                                                                                </div>
                                                                                <h6 class="font-weight-bold mb-0">Laporan Akhir</h6>
                                                                            </div>
                                                                            <div class="text-center mt-3">
                                                                                @if($hasLaporanAkhir)
                                                                                    {{-- <div class="mb-2">
                                                                                        <span class="badge badge-success p-2">Tersedia</span>
                                                                                    </div> --}}
                                                                                    <div class="d-flex flex-column align-items-center w-100">
                                                                                        @foreach($kelompok['mahasiswas'] as $mahasiswa)
                                                                                            @if(isset($mahasiswa['laporan_akhir']) && $mahasiswa['laporan_akhir'])
                                                                                                <a href="{{ asset('storage/' . $mahasiswa['laporan_akhir']['file_path']) }}" class="btn btn-sm btn-primary btn-hover w-100 mb-2" target="_blank" data-toggle="tooltip" title="Lihat File">
                                                                                                    <i class="fas fa-file-pdf mr-1"></i> Lihat Dokumen
                                                                                                </a>
                                                                                                @break
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </div>
                                                                                    
                                                                                    <div class="mt-2">
                                                                                        @if($isValidated)
                                                                                            <div class="d-flex flex-column align-items-center">
                                                                                                <button class="btn btn-sm btn-success btn-hover w-100 mb-2" disabled>
                                                                                                    <i class="fas fa-check mr-1"></i> Tervalidasi
                                                                                                </button>
                                                                                                @foreach($kelompok['mahasiswas'] as $mahasiswa)
                                                                                                    @if(isset($mahasiswa['laporan_akhir']) && $mahasiswa['laporan_akhir'] && $mahasiswa['laporan_akhir']['is_validated'])
                                                                                                        <button class="btn btn-sm btn-info btn-hover w-100 show-validation-note" data-toggle="tooltip" title="Lihat Catatan" data-note="{{ $mahasiswa['laporan_akhir']['catatan_validasi'] }}">
                                                                                                            <i class="fas fa-comment-dots mr-1"></i> Lihat Catatan
                                                                                                        </button>
                                                                                                        <small class="text-muted mt-2">Divalidasi pada: {{ $mahasiswa['laporan_akhir']['validated_at']->format('d M Y H:i') }}</small>
                                                                                                        @break
                                                                                                    @endif
                                                                                                @endforeach
                                                                                            </div>
                                                                                        @elseif($laporanToValidate)
                                                                                            <div class="d-flex flex-column align-items-center w-100">
                                                                                                <button class="btn btn-sm btn-warning btn-hover w-100" data-toggle="tooltip" title="Menunggu Validasi">
                                                                                                    <i class="fas fa-check-circle mr-1"></i> Menunggu Validasi Dosen
                                                                                                </button>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                @else
                                                                                    <div class="mb-2">
                                                                                        <span class="badge badge-secondary p-2">Belum Tersedia</span>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Daftar Mahasiswa -->
                                            <div class="card shadow-sm border-left-primary">
                                                <div class="card-header bg-white py-3">
                                                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-graduate mr-2"></i>Daftar Mahasiswa</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-striped">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th width="5%" class="text-center">No</th>
                                                                    <th width="15%">NIM</th>
                                                                    <th>Nama Mahasiswa</th>
                                                                    <th>Program Studi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($kelompok['mahasiswas'] as $index => $mahasiswa)
                                                                    <tr>
                                                                        <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                                                        <td>{{ $mahasiswa['nim'] }}</td>
                                                                        <td>{{ $mahasiswa['nama'] }}</td>
                                                                        <td>{{ $mahasiswa['program_studi'] }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            Belum ada data kelompok mahasiswa yang tersedia.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Validasi Laporan -->
<div class="modal fade" id="validasiModal" tabindex="-1" role="dialog" aria-labelledby="validasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="validasiModalLabel"><i class="fas fa-check-circle mr-2"></i>Validasi Laporan Akhir</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="validasiForm">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label for="catatan_validasi"><i class="fas fa-comment-alt mr-1"></i> Catatan Validasi (Opsional)</label>
                        <textarea class="form-control" id="catatan_validasi" name="catatan_validasi" rows="3" placeholder="Masukkan catatan validasi jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i> Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Validasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Catatan Validasi -->
<div class="modal fade" id="catatanModal" tabindex="-1" role="dialog" aria-labelledby="catatanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="catatanModalLabel"><i class="fas fa-comment-dots mr-2"></i>Catatan Validasi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="card border-left-info shadow-sm">
                    <div class="card-body">
                        <p id="catatanText" class="mb-0"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i> Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top" id="scrollToTop">
    <i class="fas fa-angle-up"></i>
</a>
@endsection

@push('scripts')
<script>
    // Sembunyikan loader saat halaman selesai dimuat
    $(window).on('load', function() {
        setTimeout(function() {
            $('#pageLoader').fadeOut(500);
        }, 500);
    });
    
    // Kontrol tombol scroll-to-top
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('#scrollToTop').fadeIn();
        } else {
            $('#scrollToTop').fadeOut();
        }
    });
    
    // Scroll ke atas saat tombol diklik
    $('#scrollToTop').click(function() {
        $('html, body').animate({scrollTop : 0}, 800);
        return false;
    });
    
    $(document).ready(function() {
        // Inisialisasi tooltip
        $('[data-toggle="tooltip"]').tooltip();
        
        // Tambahkan efek hover pada kartu
        $('.hover-card').hover(
            function() { $(this).addClass('shadow-sm'); },
            function() { $(this).removeClass('shadow-sm'); }
        );
        
        // Animasi untuk accordion
        $('.collapse').on('show.bs.collapse', function() {
            $(this).closest('.card').addClass('shadow');
            $(this).prev('.card-header').find('button .icon-circle i.fas').addClass('fa-rotate-90');
        }).on('hide.bs.collapse', function() {
            $(this).closest('.card').removeClass('shadow');
            $(this).prev('.card-header').find('button .icon-circle i.fas').removeClass('fa-rotate-90');
        });
        
        // Filter laporan berdasarkan status validasi
        $('.btn-filter').on('click', function() {
            const filterType = $(this).data('filter');
            const allCards = $('.kelompok-card');
            const kelompokSection = $('#kelompokSection');
            
            // Reset semua filter terlebih dahulu
            $('.btn-filter').removeClass('active');
            $(this).addClass('active');
            
            // Scroll ke bagian kelompok dengan animasi
            $('html, body').animate({
                scrollTop: kelompokSection.offset().top - 20
            }, 500);
            
            if (filterType === 'all') {
                allCards.fadeIn(300);
                return;
            }
            
            allCards.each(function() {
                const card = $(this);
                const hasClass = card.hasClass(filterType);
                if (hasClass) {
                    card.fadeIn(300);
                } else {
                    card.fadeOut(300);
                }
            });
        });
        
        // Ketika tombol validasi diklik
        $('.validate-laporan').on('click', function() {
            const laporanId = $(this).data('id');
            $('#validasiForm').data('laporan-id', laporanId);
            $('#validasiModal').modal('show');
        });
        
        // Ketika form validasi disubmit
        $('#validasiForm').on('submit', function(e) {
            e.preventDefault();
            
            const laporanId = $(this).data('laporan-id');
            const catatan = $('#catatan_validasi').val();
            const submitBtn = $(this).find('button[type="submit"]');
            const btnText = submitBtn.html();
            
            // Tampilkan loading
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...');
            submitBtn.prop('disabled', true);
            
            $.ajax({
                url: '{{ url("dosen/p2k/laporan") }}/' + laporanId + '/validate',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    catatan_validasi: catatan
                },
                success: function(response) {
                    if (response.success) {
                        // Tampilkan pesan sukses
                        Swal.fire({
                            title: '<i class="fas fa-check-circle text-success mr-2"></i> Berhasil!',
                            html: response.message,
                            icon: 'success',
                            confirmButtonText: '<i class="fas fa-check mr-1"></i> OK',
                            confirmButtonColor: '#4e73df'
                        }).then((result) => {
                            // Reload halaman
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: '<i class="fas fa-exclamation-circle text-warning mr-2"></i> Gagal!',
                            html: response.message,
                            icon: 'error',
                            confirmButtonText: '<i class="fas fa-check mr-1"></i> OK',
                            confirmButtonColor: '#4e73df'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire({
                        title: '<i class="fas fa-exclamation-triangle text-danger mr-2"></i> Error!',
                        html: 'Terjadi kesalahan saat memvalidasi laporan.<br><small class="text-muted">Detail: ' + error + '</small>',
                        icon: 'error',
                        confirmButtonText: '<i class="fas fa-check mr-1"></i> OK',
                        confirmButtonColor: '#4e73df'
                    });
                },
                complete: function() {
                    $('#validasiModal').modal('hide');
                    $('#catatan_validasi').val('');
                    submitBtn.html(btnText);
                    submitBtn.prop('disabled', false);
                }
            });
        });
        
        // Ketika tombol lihat catatan diklik
        $('.show-validation-note').on('click', function() {
            const note = $(this).data('note');
            $('#catatanText').html(note ? note : '<em class="text-muted">Tidak ada catatan validasi.</em>');
            $('#catatanModal').modal('show');
        });
    });
</script>
@endpush