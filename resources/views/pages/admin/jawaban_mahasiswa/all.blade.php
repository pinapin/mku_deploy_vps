@extends('layouts.master')

@section('title', 'Semua Jawaban Mahasiswa')

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Semua Jawaban Mahasiswa</h3>
                            <div class="card-tools">
                                <a href="{{ route('master.ujian.index') }}" class="btn btn-default btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Data Ujian
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Mahasiswa</th>
                                            <th>Ujian</th>
                                            <th>Soal</th>
                                            <th>Jawaban</th>
                                            <th>Tipe Jawaban</th>
                                            <th>Benar/Salah</th>
                                            <th width="10%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($jawabanMahasiswas as $index => $jawaban)
                                            <tr>
                                                <td>{{ $jawabanMahasiswas->firstItem() + $index }}</td>
                                                <td>
                                                    <strong>{{ $jawaban->sesiUjian->nim ?? '-' }}</strong><br>
                                                    <small class="text-muted">{{ $jawaban->sesiUjian->mahasiswa->nama ?? 'Unknown' }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $jawaban->sesiUjian->ujian->nama_ujian ?? '-' }}</strong><br>
                                                    <small class="text-muted">{{ $jawaban->sesiUjian->ujian->durasi_menit ?? '-' }} menit</small>
                                                </td>
                                                <td>
                                                    <strong>Soal {{ $jawaban->soal->nomor_soal ?? '-' }}</strong><br>
                                                    <small class="text-muted">{{ Str::limit($jawaban->soal->teks_soal ?? '-', 50) }}</small>
                                                </td>
                                                <td>
                                                    @if($jawaban->soal->tipe == 'pilihan_ganda')
                                                        <span class="badge badge-info">
                                                            {{ $jawaban->pilihanDipilih->huruf_pilihan ?? '-' }}.
                                                            {{ $jawaban->pilihanDipilih->teks_pilihan ?? '-' }}
                                                        </span>
                                                    @else
                                                        <small>{{ Str::limit($jawaban->jawaban_essay ?? '-', 100) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $jawaban->soal->tipe == 'pilihan_ganda' ? 'badge-primary' : 'badge-secondary' }}">
                                                        {{ $jawaban->soal->tipe == 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($jawaban->isBenar())
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> Benar
                                                        </span>
                                                    @elseif($jawaban->soal->tipe == 'pilihan_ganda')
                                                        <span class="badge badge-danger">
                                                            <i class="fas fa-times"></i> Salah
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">
                                                            <i class="fas fa-question"></i> Manual
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('master.jawaban_mahasiswa.show', $jawaban->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('master.jawaban_mahasiswa.edit', $jawaban->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada data jawaban mahasiswa</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if(method_exists($jawabanMahasiswas, 'links'))
                                <div class="d-flex justify-content-center">
                                    {{ $jawabanMahasiswas->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Statistik Jawaban</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Jawaban</span>
                                            <span class="info-box-number">{{ $jawabanMahasiswas->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Jawaban Benar</span>
                                            <span class="info-box-number">{{ $jawabanMahasiswas->filter(function($j) { return $j->isBenar(); })->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Jawaban Salah</span>
                                            <span class="info-box-number">{{ $jawabanMahasiswas->filter(function($j) { return $j->soal->tipe == 'pilihan_ganda' && !$j->isBenar(); })->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-secondary"><i class="fas fa-edit"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Essay</span>
                                            <span class="info-box-number">{{ $jawabanMahasiswas->filter(function($j) { return $j->soal->tipe == 'essay'; })->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection