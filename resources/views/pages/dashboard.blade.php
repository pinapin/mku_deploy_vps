@extends('layouts.master')

@section('title', 'Dashboard')

@push('css')
<style>
    :root {
        --primary-color: #4e73df;
        --success-color: #1cc88a;
        --info-color: #36b9cc;
        --warning-color: #f6c23e;
        --danger-color: #e74a3b;
    }
    
    .card-dashboard {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s, box-shadow 0.3s;
        border: none;
        margin-bottom: 24px;
    }
    
    .card-dashboard:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .card-dashboard .card-header {
        background-color: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 15px 20px;
        font-weight: 600;
    }
    
    .card-dashboard .card-body {
        padding: 20px;
    }
    
    .stats-card {
        border-radius: 10px;
        padding: 20px;
        height: 110%;
        position: relative;
        overflow: hidden;
        color: white;
    }
    
    .stats-card .icon {
        position: absolute;
        right: 20px;
        top: 20px;
        font-size: 30px;
        opacity: 0.3;
    }
    
    .stats-card .stats-number {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .stats-card .stats-text {
        font-size: 14px;
        opacity: 0.8;
    }
    
    .stats-card .stats-link {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.1);
        text-align: center;
        padding: 5px;
        color: white;
        font-size: 12px;
        text-decoration: none;
        transition: background 0.3s;
    }
    
    .stats-card .stats-link:hover {
        background: rgba(0,0,0,0.2);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
    }
    
    .bg-gradient-success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
    }
    
    .bg-gradient-info {
        background: linear-gradient(45deg, #36b9cc, #258391);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 10px;
    }
    
    .table-dashboard {
        color: #333;
    }
    
    .table-dashboard th {
        font-weight: 600;
        background-color: rgba(0,0,0,0.02);
    }
    
    .table-dashboard td, .table-dashboard th {
        padding: 12px 15px;
        vertical-align: middle;
    }
    
    .table-dashboard tbody tr {
        transition: background 0.3s;
    }
    
    .table-dashboard tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }
    
    .badge-custom {
        padding: 5px 10px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #888;
    }
    
    .empty-state i {
        /* font-size: 50px;
        margin-bottom: 15px; */
        /* opacity: 0.3; */
    }

    .hover-scale {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .hover-scale:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .exam-action-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .exam-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
</style>
@endpush

@section('content')
@if(Session::get('role') === 'admin' || Session::get('role') === 'tamu')
    <!-- Admin Dashboard -->
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-primary">
                <div class="icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stats-number">{{ number_format($totalSuratPengantar) }}</div>
                <div class="stats-text">Surat Pengantar</div>
                <a href="{{ route('p2k.surat-pengantar.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-success">
                <div class="icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stats-number">{{ number_format($totalPKS) }}</div>
                <div class="stats-text">Perjanjian Kerja Sama</div>
                <a href="{{ route('p2k.pks.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-info">
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
                <div class="stats-number">{{ number_format($totalUMKM) }}</div>
                <div class="stats-text">UMKM Terdaftar</div>
                 <a href="{{ route('master.umkm.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-warning">
                <div class="icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <div class="stats-number">{{ number_format($totalSertifikatKwu) }}</div>
                <div class="stats-text">Data Sertifikat KWU</div>
                 <a href="{{ route('kwu.sertifikat-kwu.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- Surat Pengantar Chart -->
        <div class="col-xl-4 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-chart-line mr-1"></i>
                    Surat Pengantar (12 Bulan Terakhir)
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="suratPengantarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- PKS Chart -->
        <div class="col-xl-4 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-chart-line mr-1"></i>
                    Perjanjian Kerja Sama (12 Bulan Terakhir)
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="pksChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sertifikat KWU Chart -->
        <div class="col-xl-4 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Sertifikat KWU Per Tahun
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="sertifikatKwuChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional Charts for Sertifikat KWU -->
    <div class="row">
        <!-- Sertifikat KWU Per Fakultas -->
        <div class="col-xl-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Distribusi Sertifikat KWU Per Fakultas
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="sertifikatKwuPerFakultasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sertifikat KWU Per Program Studi -->
        <div class="col-xl-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Jumlah Sertifikat KWU Per Program Studi
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="sertifikatKwuPerProdiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="row">
        <!-- Surat Pengantar Table -->
        <div class="col-xl-6 mb-6">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-envelope mr-1"></i>
                    Surat Pengantar Terbaru
                </div>
                <div class="card-body">
                    @if(count($suratPengantarTerbaru) > 0)
                    <div class="table-responsive">
                        <table class="table table-dashboard">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>UMKM</th>
                                    <th>Mahasiswa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suratPengantarTerbaru as $surat)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($surat->tgl_surat)->format('d/m/Y') }}</td>
                                    <td>{{ $surat->umkm->nama_umkm }}</td>
                                    <td>{{ $surat->suratPengantarMahasiswas->count() }} orang</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-envelope"></i>
                        <p>Belum ada data surat pengantar</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- PKS Table -->
        <div class="col-xl-6 mb-6">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-file-contract mr-1"></i>
                    Perjanjian Kerja Sama Terbaru
                </div>
                <div class="card-body">
                    @if(count($pksTerbaru) > 0)
                    <div class="table-responsive">
                        <table class="table table-dashboard">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nomor PKS</th>
                                    <th>UMKM</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pksTerbaru as $pks)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pks->tgl_pks)->format('d/m/Y') }}</td>
                                    <td>{{ $pks->no_pks }}</td>
                                    <td>{{ $pks->umkm->nama_umkm ?? 'UMKM Tidak Ditemukan' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-file-contract"></i>
                        <p>Belum ada data perjanjian kerja sama</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sertifikat KWU Table -->
        {{-- <div class="col-xl-4 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-certificate mr-1"></i>
                    Sertifikat KWU Terbaru
                </div>
                <div class="card-body">
                    @if(count($sertifikatKwuTerbaru) > 0)
                    <div class="table-responsive">
                        <table class="table table-dashboard">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Prodi</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sertifikatKwuTerbaru as $sertifikat)
                                <tr>
                                    <td>{{ $sertifikat->nim }}</td>
                                    <td>{{ $sertifikat->nama }}</td>
                                    <td>{{ $sertifikat->programStudi->nama_prodi }}</td>
                                    <td>{{ number_format((floatval($sertifikat->nilai_teori) + floatval($sertifikat->nilai_praktek)) / 2, 1) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-certificate"></i>
                        <p>Belum ada data sertifikat KWU</p>
                    </div>
                    @endif
                </div>
            </div>
        </div> --}}
    </div>
@elseif(Session::get('role') === 'mahasiswa')
    <!-- Mahasiswa Dashboard -->
    <!-- Header Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="fas fa-info-circle fa-2x mr-3"></i>
                    <div>
                        <h6 class="mb-1">Tahun Akademik Aktif</h6>
                        <p class="mb-0">
                            <strong>{{ $tahunAkademikAktif ? $tahunAkademikAktif->tahun_ajaran . ' ' . $tahunAkademikAktif->tipe_semester : 'Tidak ada tahun akademik aktif' }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($hasAvailableExams || $activeExamSession)
    <!-- Modern Ujian Button Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="exam-action-card text-center p-4 rounded-lg shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="exam-icon mb-3">
                    <i class="fas fa-clipboard-list fa-3x text-white"></i>
                </div>
                <h4 class="text-white mb-2 font-weight-bold">Ujian Online Kewirausahaan</h4>
                <p class="text-white-50 mb-4">Tes pengetahuan dan pemahaman Anda tentang kewirausahaan</p>

                @if($activeExamSession)
                    <!-- Ada sesi ujian berlangsung -->
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('ujian.show', $activeExamSession->encrypted_id) }}" class="btn btn-warning btn-lg px-5 py-3 rounded-pill font-weight-bold hover-scale">
                            <i class="fas fa-clock mr-2"></i>Lanjutkan Ujian
                        </a>
                    </div>
                    <div class="mt-3">
                        <span class="badge badge-warning px-3 py-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Ujian sedang berlangsung: {{ $activeExamSession->ujian->nama_ujian ?? 'Ujian' }}
                        </span>
                    </div>
                @else
                    <!-- Tidak ada sesi ujian berlangsung -->
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('ujian.index') }}" class="btn btn-light btn-lg px-5 py-3 rounded-pill font-weight-bold hover-scale">
                            <i class="fas fa-play mr-2"></i>Mulai Ujian Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-primary">
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
                <div class="stats-number">{{ number_format($totalUMKM) }}</div>
                <div class="stats-text">Data UMKM</div>
                <a href="{{ route('mahasiswa.data-umkm.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-success">
                <div class="icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="stats-number">{{ number_format($totalSuratPengantar) }}</div>
                <div class="stats-text">Surat Pengantar</div>
                <a href="{{ route('mahasiswa.surat-pengantar.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-info">
                <div class="icon">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stats-number">{{ number_format($totalPKS) }}</div>
                <div class="stats-text">Perjanjian Kerja Sama</div>
                <a href="{{ route('mahasiswa.pks.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-warning">
                <div class="icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stats-number">{{ number_format($totalLaporanAkhir) }}</div>
                <div class="stats-text">Laporan Akhir</div>
                <a href="#" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
    </div>

  <!-- Single Data Detail Cards -->
    <div class="row">
        <!-- UMKM Detail Card -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-store fa-lg mr-3"></i>
                            <div>
                                <h5 class="mb-0">Data UMKM</h5>
                                <small>Informasi Usaha Mikro Kecil Menengah</small>
                            </div>
                        </div>
                        @if(count($umkms) > 0)
                            <span class="badge badge-light px-3 py-2">
                                <strong>1 Data</strong>
                            </span>
                        @else
                            <span class="badge badge-light px-3 py-2">
                                <strong>0 Data</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(count($umkms) > 0)
                        @foreach($umkms as $umkm)
                        <div class="single-data-card bg-light border-0 rounded p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tr class="table-header">
                                        <td colspan="2" class="p-3 bg-primary text-white rounded text-center">
                                            <h5 class="mb-0">
                                                <i class="fas fa-store mr-2"></i>{{ $umkm->nama_umkm }}
                                            </h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="35%" class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user mr-2 text-primary"></i>
                                                <span class="font-weight-semibold">Pemilik UMKM</span>
                                            </div>
                                        </td>
                                        <td class="p-3">{{ $umkm->nama_pemilik_umkm }}</td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-tag mr-2 text-primary"></i>
                                                <span class="font-weight-semibold">Kategori</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <span class="badge badge-primary px-3 py-2">{{ $umkm->kategoriUmkm->nama_kategori }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-phone mr-2 text-primary"></i>
                                                <span class="font-weight-semibold">Kontak</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <strong>{{ $umkm->no_hp_umkm }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state py-5 text-center">
                            <i class="fas fa-store fa-4x mb-4 text-muted"></i>
                            <h5 class="text-muted mb-3">Belum Ada Data UMKM</h5>
                            <p class="text-muted mb-4 px-4">Anda belum memasukkan data UMKM untuk tahun akademik ini</p>
                            <a href="{{ route('mahasiswa.data-umkm.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus mr-2"></i>Tambah Data UMKM
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Surat Pengantar Detail Card -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-success text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope fa-lg mr-3"></i>
                            <div>
                                <h5 class="mb-0">Surat Pengantar</h5>
                                <small>Surat Pengantar Kewirausahaan</small>
                            </div>
                        </div>
                        @if(count($suratPengantars) > 0)
                            <span class="badge badge-light px-3 py-2">
                                <strong>1 Data</strong>
                            </span>
                        @else
                            <span class="badge badge-light px-3 py-2">
                                <strong>0 Data</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(count($suratPengantars) > 0)
                        @foreach($suratPengantars as $surat)
                        <div class="single-data-card bg-light border-0 rounded p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tr class="table-header">
                                        <td colspan="2" class="p-3 bg-success text-white rounded text-center">
                                            <h5 class="mb-0">
                                                <i class="fas fa-file-alt mr-2"></i>{{ \Carbon\Carbon::parse($surat->tgl_surat)->format('d F Y') }}
                                            </h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="35%" class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-store mr-2 text-success"></i>
                                                <span class="font-weight-semibold">UMKM Tujuan</span>
                                            </div>
                                        </td>
                                        <td class="p-3">{{ $surat->umkm->nama_umkm }}</td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-users mr-2 text-success"></i>
                                                <span class="font-weight-semibold">Jumlah Mahasiswa</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <span class="badge badge-success px-3 py-2">{{ $surat->suratPengantarMahasiswas->count() }} Orang</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-tag mr-2 text-success"></i>
                                                <span class="font-weight-semibold">Kelas</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <strong>{{ $surat->kelas }}</strong>
                                        </td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-users mr-2 text-success"></i>
                                                <span class="font-weight-semibold">Kelompok</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <strong>{{ $surat->kelompok }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state py-5 text-center">
                            <i class="fas fa-envelope fa-4x mb-4 text-muted"></i>
                            <h5 class="text-muted mb-3">Belum Ada Surat Pengantar</h5>
                            <p class="text-muted mb-4 px-4">Anda belum membuat surat pengantar untuk tahun akademik ini</p>
                            <a href="{{ route('mahasiswa.surat-pengantar.index') }}" class="btn btn-success">
                                <i class="fas fa-plus mr-2"></i>Buat Surat Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Detail Cards -->
    <div class="row">
        <!-- PKS Detail Card -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-info text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-contract fa-lg mr-3"></i>
                            <div>
                                <h5 class="mb-0">Perjanjian Kerja Sama</h5>
                                <small>Dokumen Kerjasama dengan UMKM</small>
                            </div>
                        </div>
                        @if(count($pksList) > 0)
                            <span class="badge badge-light px-3 py-2">
                                <strong>1 Data</strong>
                            </span>
                        @else
                            <span class="badge badge-light px-3 py-2">
                                <strong>0 Data</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(count($pksList) > 0)
                        @foreach($pksList as $pks)
                        <div class="single-data-card bg-light border-0 rounded p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tr class="table-header">
                                        <td colspan="2" class="p-3 bg-info text-white rounded text-center">
                                            <h5 class="mb-0">
                                                <i class="fas fa-file-contract mr-2"></i>{{ $pks->no_pks }}
                                            </h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="35%" class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-store mr-2 text-info"></i>
                                                <span class="font-weight-semibold">UMKM Partner</span>
                                            </div>
                                        </td>
                                        <td class="p-3">{{ $pks->umkm->nama_umkm }}</td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-calendar mr-2 text-info"></i>
                                                <span class="font-weight-semibold">Tanggal PKS</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <strong>{{ \Carbon\Carbon::parse($pks->tgl_pks)->format('d F Y') }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-clock mr-2 text-info"></i>
                                                <span class="font-weight-semibold">Masa Berlaku</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <span class="badge badge-info px-3 py-2">{{ $pks->lama_perjanjian }} Tahun</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state py-5 text-center">
                            <i class="fas fa-file-contract fa-4x mb-4 text-muted"></i>
                            <h5 class="text-muted mb-3">Belum Ada PKS</h5>
                            <p class="text-muted mb-4 px-4">Anda belum membuat perjanjian kerja sama untuk tahun akademik ini</p>
                            <a href="{{ route('mahasiswa.pks.index') }}" class="btn btn-info">
                                <i class="fas fa-plus mr-2"></i>Tambah PKS Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Laporan Akhir Detail Card -->
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card card-dashboard h-100">
                <div class="card-header bg-warning text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-alt fa-lg mr-3"></i>
                            <div>
                                <h5 class="mb-0">Laporan Akhir</h5>
                                <small>Laporan Hasil Kewirausahaan</small>
                            </div>
                        </div>
                        @if(count($laporanAkhirList) > 0)
                            <span class="badge badge-light px-3 py-2">
                                <strong>1 Data</strong>
                            </span>
                        @else
                            <span class="badge badge-light px-3 py-2">
                                <strong>0 Data</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(count($laporanAkhirList) > 0)
                        @foreach($laporanAkhirList as $laporan)
                        <div class="single-data-card bg-light border-0 rounded p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tr class="table-header">
                                        <td colspan="2" class="p-3 bg-warning text-white rounded text-center">
                                            <h5 class="mb-0">
                                                <i class="fas fa-file-alt mr-2"></i>Laporan Kelompok {{ $laporan->kelompok }}
                                            </h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="35%" class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user mr-2 text-warning"></i>
                                                <span class="font-weight-semibold">Nomor Induk</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <strong>{{ $laporan->nim }}</strong>
                                        </td>
                                    </tr>
                                    <tr class="bg-white">
                                        <td class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-tag mr-2 text-warning"></i>
                                                <span class="font-weight-semibold">Kelas</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <strong>{{ $laporan->kelas }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-3 border-right">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-check-circle mr-2 text-warning"></i>
                                                <span class="font-weight-semibold">Status Validasi</span>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            @if($laporan->is_validated)
                                                <span class="badge badge-success px-3 py-2">Tervalidasi</span>
                                            @else
                                                <span class="badge badge-secondary px-3 py-2">Belum Validasi</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-state py-5 text-center">
                            <i class="fas fa-file-alt fa-4x mb-4 text-muted"></i>
                            <h5 class="text-muted mb-3">Belum Ada Laporan Akhir</h5>
                            <p class="text-muted mb-4 px-4">Anda belum mengunggah laporan akhir untuk tahun akademik ini</p>
                            <a href="{{ route('mahasiswa.laporan-akhir.index') }}" class="btn btn-warning">
                                <i class="fas fa-upload mr-2"></i>Upload Laporan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@elseif(Session::get('role') === 'dosen')
    <!-- Dosen Dashboard -->
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-primary">
                <div class="icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stats-number">{{ number_format($totalKelas) }}</div>
                <div class="stats-text">Kelas P2K</div>
                <a href="{{ route('dosen.p2k.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-success">
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number">{{ number_format($totalLaporanValidated) }}</div>
                <div class="stats-text">Laporan Tervalidasi</div>
                <a href="{{ route('dosen.p2k.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-warning">
                <div class="icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stats-number">{{ number_format($totalLaporanPending) }}</div>
                <div class="stats-text">Laporan Belum Divalidasi</div>
                <a href="{{ route('dosen.p2k.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card bg-gradient-info">
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">{{ number_format($totalMahasiswa) }}</div>
                <div class="stats-text">Total Mahasiswa</div>
                <a href="{{ route('dosen.p2k.index') }}" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ml-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- Kelas per Tahun Akademik Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Kelas per Tahun Akademik
                </div>
                <div class="card-body">
                    @if(count($kelasByTahunAkademik) > 0)
                    <div class="chart-container">
                        <canvas id="kelasByTahunAkademikChart"></canvas>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-chart-bar"></i>
                        <p>Belum ada data kelas</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Validasi Laporan per Kelas Chart -->
        <div class="col-xl-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Status Validasi Laporan per Kelas
                </div>
                <div class="card-body">
                    @if(count($validasiPerKelas) > 0)
                    <div class="chart-container">
                        <canvas id="validasiPerKelasChart"></canvas>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-chart-pie"></i>
                        <p>Belum ada data laporan</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tables -->
    <div class="row">
        <!-- Laporan Pending Table -->
        <div class="col-xl-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-file-alt mr-1"></i>
                    Laporan Belum Divalidasi
                </div>
                <div class="card-body">
                    @if(count($laporanPending) > 0)
                    <div class="table-responsive">
                        <table class="table table-dashboard">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Kelas</th>
                                    <th>Tanggal Upload</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($laporanPending as $laporan)
                                <tr>
                                    <td>{{ $laporan->nim }}</td>
                                    <td>{{ $laporan->mahasiswa ? $laporan->mahasiswa->nama_mahasiswa : $laporan->nim }}</td>
                                    <td>{{ $laporan->kelasDosenP2K->kelas }}</td>
                                    <td>{{ \Carbon\Carbon::parse($laporan->created_at)->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('dosen.p2k.detail-kelas', $laporan->kelas_dosen_p2k_id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-file-alt"></i>
                        <p>Tidak ada laporan yang menunggu validasi</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Kelas Terbaru Table -->
        <div class="col-xl-6 mb-4">
            <div class="card card-dashboard">
                <div class="card-header">
                    <i class="fas fa-chalkboard mr-1"></i>
                    Kelas Terbaru
                </div>
                <div class="card-body">
                    @if(count($kelasTerbaru) > 0)
                    <div class="table-responsive">
                        <table class="table table-dashboard">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Jumlah Mahasiswa</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelasTerbaru as $kelas)
                                <tr>
                                    <td>{{ $kelas->kelas }}</td>
                                    <td class="text-primary">
                                        {{ $kelas->jumlah_mahasiswa }} Mahasiswa
                                    </td>
                                    <td>
                                        @if($kelas->tahunAkademik->is_aktif)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('dosen.p2k.detail-kelas', $kelas->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-chalkboard"></i>
                        <p>Belum ada data kelas</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
$(function() {
    // Fungsi untuk mendapatkan nama bulan
    function getMonthName(monthNumber) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        return months[monthNumber - 1];
    }
    
    @if(Session::get('role') === 'admin' || Session::get('role') === 'tamu')
    // Chart Surat Pengantar
    if ($('#suratPengantarChart').length) {
        const suratPengantarData = @json($suratPengantarPerBulan);
        const suratPengantarLabels = [];
        const suratPengantarValues = [];
        
        // Siapkan data 12 bulan terakhir
        const today = new Date();
        for (let i = 11; i >= 0; i--) {
            const d = new Date(today);
            d.setMonth(d.getMonth() - i);
            const monthYear = getMonthName(d.getMonth() + 1) + ' ' + d.getFullYear();
            suratPengantarLabels.push(monthYear);
            
            // Cari data untuk bulan ini
            const found = suratPengantarData.find(item => 
                parseInt(item.bulan) === (d.getMonth() + 1) && 
                parseInt(item.tahun) === d.getFullYear()
            );
            
            suratPengantarValues.push(found ? found.total : 0);
        }
        
        const suratPengantarChart = new Chart($('#suratPengantarChart'), {
            type: 'line',
            data: {
                labels: suratPengantarLabels,
                datasets: [{
                    label: 'Jumlah Surat Pengantar',
                    data: suratPengantarValues,
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    // Chart Sertifikat KWU Per Tahun
    if ($('#sertifikatKwuChart').length) {
        const sertifikatKwuData = @json($sertifikatKwuPerTahun);
        const sertifikatKwuLabels = sertifikatKwuData.map(item => item.tahun.toString());
        const sertifikatKwuValues = sertifikatKwuData.map(item => item.total);
        
        const sertifikatKwuChart = new Chart($('#sertifikatKwuChart'), {
            type: 'bar',
            data: {
                labels: sertifikatKwuLabels,
                datasets: [{
                    label: 'Jumlah Sertifikat',
                    data: sertifikatKwuValues,
                    backgroundColor: 'rgba(246, 194, 62, 0.8)',
                    borderColor: 'rgba(246, 194, 62, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    
    // Chart Sertifikat KWU Per Fakultas
    if ($('#sertifikatKwuPerFakultasChart').length) {
        const sertifikatKwuPerFakultasData = @json($sertifikatKwuPerFakultas);
        const sertifikatKwuPerFakultasLabels = sertifikatKwuPerFakultasData.map(item => item.nama_fakultas);
        const sertifikatKwuPerFakultasValues = sertifikatKwuPerFakultasData.map(item => item.total);
        
        // Generate colors for each fakultas
        const backgroundColors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(199, 199, 199, 0.7)',
            'rgba(83, 102, 255, 0.7)',
            'rgba(40, 159, 64, 0.7)',
            'rgba(210, 199, 199, 0.7)'
        ];
        
        const sertifikatKwuPerFakultasChart = new Chart($('#sertifikatKwuPerFakultasChart'), {
            type: 'doughnut',
            data: {
                labels: sertifikatKwuPerFakultasLabels,
                datasets: [{
                    data: sertifikatKwuPerFakultasValues,
                    backgroundColor: backgroundColors.slice(0, sertifikatKwuPerFakultasLabels.length),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 15,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Chart Sertifikat KWU Per Program Studi
    if ($('#sertifikatKwuPerProdiChart').length) {
        const sertifikatKwuPerProdiData = @json($sertifikatKwuPerProdi);
        const sertifikatKwuPerProdiLabels = sertifikatKwuPerProdiData.map(item => item.nama_prodi);
        const sertifikatKwuPerProdiValues = sertifikatKwuPerProdiData.map(item => item.total);
        
        // Generate colors for each prodi
        const prodiColors = [
            'rgba(54, 162, 235, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(255, 99, 132, 0.8)',
            'rgba(255, 205, 86, 0.8)',
            'rgba(201, 203, 207, 0.8)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(153, 102, 255, 0.6)',
            'rgba(255, 159, 64, 0.6)',
            'rgba(255, 99, 132, 0.6)',
            'rgba(255, 205, 86, 0.6)',
            'rgba(201, 203, 207, 0.6)'
        ];
        
        const sertifikatKwuPerProdiChart = new Chart($('#sertifikatKwuPerProdiChart'), {
            type: 'bar',
            data: {
                labels: sertifikatKwuPerProdiLabels,
                datasets: [{
                    label: 'Jumlah Sertifikat',
                    data: sertifikatKwuPerProdiValues,
                    backgroundColor: prodiColors.slice(0, sertifikatKwuPerProdiLabels.length),
                    borderColor: prodiColors.slice(0, sertifikatKwuPerProdiLabels.length).map(color => color.replace('0.8', '1').replace('0.6', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',  // Horizontal bar chart
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} sertifikat (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Chart PKS
    if ($('#pksChart').length) {
        const pksData = @json($pksPerBulan);
        const pksLabels = [];
        const pksValues = [];
        
        // Siapkan data 12 bulan terakhir
        const today = new Date();
        for (let i = 11; i >= 0; i--) {
            const d = new Date(today);
            d.setMonth(d.getMonth() - i);
            const monthYear = getMonthName(d.getMonth() + 1) + ' ' + d.getFullYear();
            pksLabels.push(monthYear);
            
            // Cari data untuk bulan ini
            const found = pksData.find(item => 
                parseInt(item.bulan) === (d.getMonth() + 1) && 
                parseInt(item.tahun) === d.getFullYear()
            );
            
            pksValues.push(found ? found.total : 0);
        }
        
        const pksChart = new Chart($('#pksChart'), {
            type: 'line',
            data: {
                labels: pksLabels,
                datasets: [{
                    label: 'Jumlah PKS',
                    data: pksValues,
                    backgroundColor: 'rgba(28, 200, 138, 0.05)',
                    borderColor: 'rgba(28, 200, 138, 1)',
                    pointBackgroundColor: 'rgba(28, 200, 138, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(28, 200, 138, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    @endif
    
    @if(Session::get('role') === 'mahasiswa')
    // Chart UMKM per Kategori
    if ($('#umkmPerKategoriChart').length) {
        const umkmPerKategoriData = @json($umkmPerKategori);
        const umkmPerKategoriLabels = umkmPerKategoriData.map(item => item.nama_kategori);
        const umkmPerKategoriValues = umkmPerKategoriData.map(item => item.total);
        
        // Generate random colors
        const backgroundColors = umkmPerKategoriLabels.map(() => {
            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);
            return `rgba(${r}, ${g}, ${b}, 0.7)`;
        });
        
        const umkmPerKategoriChart = new Chart($('#umkmPerKategoriChart'), {
            type: 'pie',
            data: {
                labels: umkmPerKategoriLabels,
                datasets: [{
                    data: umkmPerKategoriValues,
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
    }
    
    // Chart Activity Summary
    if ($('#activitySummaryChart').length) {
        const activityData = [
            { label: 'Data UMKM', value: {{ $totalUMKM }} },
            { label: 'Surat Pengantar', value: {{ $totalSuratPengantar }} },
            { label: 'PKS', value: {{ $totalPKS }} }
        ];
        
        const activityLabels = activityData.map(item => item.label);
        const activityValues = activityData.map(item => item.value);
        
        const activitySummaryChart = new Chart($('#activitySummaryChart'), {
            type: 'bar',
            data: {
                labels: activityLabels,
                datasets: [{
                    label: 'Jumlah',
                    data: activityValues,
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.7)',
                        'rgba(28, 200, 138, 0.7)',
                        'rgba(54, 185, 204, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    @endif
    
    @if(Session::get('role') === 'dosen')
     // Chart Kelas per Tahun Akademik
     if ($('#kelasByTahunAkademikChart').length) {
         const kelasByTahunAkademikData = @json($kelasByTahunAkademik);
         const kelasByTahunAkademikLabels = kelasByTahunAkademikData.map(item => item.tahun_akademik);
         const kelasByTahunAkademikValues = kelasByTahunAkademikData.map(item => item.jumlah);
         
         const kelasByTahunAkademikChart = new Chart($('#kelasByTahunAkademikChart'), {
             type: 'bar',
             data: {
                 labels: kelasByTahunAkademikLabels,
                 datasets: [{
                     label: 'Jumlah Kelas',
                     data: kelasByTahunAkademikValues,
                     backgroundColor: 'rgba(0, 123, 255, 0.7)',
                     borderColor: 'rgba(0, 123, 255, 1)',
                     borderWidth: 1
                 }]
             },
             options: {
                 responsive: true,
                 maintainAspectRatio: false,
                 scales: {
                     y: {
                         beginAtZero: true,
                         ticks: {
                             precision: 0
                         }
                     }
                 },
                 plugins: {
                     legend: {
                         display: false
                     }
                 }
             }
         });
     }
     
     // Chart Validasi Laporan per Kelas
     if ($('#validasiPerKelasChart').length) {
         const validasiPerKelasData = @json($validasiPerKelas);
         const validasiPerKelasLabels = validasiPerKelasData.map(item => item.kelas);
         const validasiPerKelasValues = validasiPerKelasData.map(item => item.validated_count);
         
         // Generate colors for each kelas
         const kelasColors = [
             'rgba(40, 167, 69, 0.7)',  // success green
             'rgba(255, 193, 7, 0.7)',  // warning yellow
             'rgba(23, 162, 184, 0.7)',  // info blue
             'rgba(0, 123, 255, 0.7)',   // primary blue
             'rgba(111, 66, 193, 0.7)',  // purple
             'rgba(253, 126, 20, 0.7)',  // orange
             'rgba(32, 201, 151, 0.7)',  // teal
             'rgba(108, 117, 125, 0.7)'  // gray
         ];
         
         const validasiPerKelasChart = new Chart($('#validasiPerKelasChart'), {
             type: 'pie',
             data: {
                 labels: validasiPerKelasLabels,
                 datasets: [{
                     data: validasiPerKelasValues,
                     backgroundColor: kelasColors.slice(0, validasiPerKelasLabels.length),
                     borderWidth: 1
                 }]
             },
             options: {
                 responsive: true,
                 maintainAspectRatio: false,
                 plugins: {
                     legend: {
                         position: 'right',
                         labels: {
                             boxWidth: 15,
                             padding: 10
                         }
                     },
                     tooltip: {
                         callbacks: {
                             label: function(context) {
                                 const label = context.label || '';
                                 const value = context.raw || 0;
                                 const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                 const percentage = Math.round((value / total) * 100);
                                 return `${label}: ${value} laporan tervalidasi (${percentage}%)`;
                             }
                         }
                     }
                 }
             }
         });
     }
     @endif
});
</script>
@endpush