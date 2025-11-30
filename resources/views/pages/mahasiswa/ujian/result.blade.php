@extends('layouts.master')

@section('title', 'Hasil Ujian')

@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Hasil Ujian</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('ujian.index') }}">Ujian</a></li>
                        <li class="breadcrumb-item active">Hasil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Result Summary -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">{{ $sesiUjian->ujian->nama_ujian }} - Hasil</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-info"><i class="fas fa-clock"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Durasi</span>
                                            <span class="info-box-number">{{ $sesiUjian->ujian->durasi_menit }} menit</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-warning"><i class="fas fa-question-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Soal</span>
                                            <span class="info-box-number">{{ $sesiUjian->ujian->soal->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Jawaban Benar</span>
                                            <span
                                                class="info-box-number">{{ collect($results)->where('benar', true)->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-box">
                                        <span
                                            class="info-box-icon @if ($sesiUjian->skor_akhir >= 70) bg-success @else bg-danger @endif">
                                            <i class="fas fa-percentage"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Skor Akhir</span>
                                            <span class="info-box-number">{{ $sesiUjian->skor_akhir }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12">
                                    <p><strong>Waktu Mulai:</strong> {{ $sesiUjian->waktu_mulai->format('d M Y H:i:s') }}
                                    </p>
                                    <p><strong>Waktu Selesai:</strong>
                                        {{ $sesiUjian->waktu_selesai ? $sesiUjian->waktu_selesai->format('d M Y H:i:s') : '-' }}
                                    </p>
                                    <p><strong>Lama Pengerjaan:</strong>
                                        @if ($sesiUjian->waktu_selesai)
                                            {{ $sesiUjian->waktu_mulai->diffInMinutes($sesiUjian->waktu_selesai) }} menit
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Results -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detail Jawaban</h3>
                        </div>
                        <div class="card-body">
                            @foreach ($results as $index => $result)
                                <div
                                    class="card mb-3 @if (isset($result['benar']) && $result['benar']) border-success @else border-danger @endif">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            Soal {{ $result['soal']->nomor_soal ?? 'N/A' }}
                                        </h5>
                                        <span
                                            class="badge @if (isset($result['benar']) && $result['benar']) badge-success @else badge-danger @endif">
                                            @if (isset($result['benar']) && $result['benar'])
                                                <i class="fas fa-check"></i> Benar
                                            @else
                                                <i class="fas fa-times"></i> Salah
                                            @endif
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            <strong>Pertanyaan:</strong><br>{{ $result['soal']->teks_soal ?? 'Tidak ada pertanyaan' }}
                                        </p>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Jawaban Anda:</strong></p>
                                                @if (isset($result['jawaban']) && $result['jawaban'] && isset($result['jawaban']->pilihanDipilih))
                                                    <div
                                                        class="alert @if (isset($result['benar']) && $result['benar']) alert-success @else alert-danger @endif">
                                                        <strong>{{ $result['jawaban']->pilihanDipilih->huruf_pilihan ?? '' }}.</strong>
                                                        {{ $result['jawaban']->pilihanDipilih->teks_pilihan ?? '' }}
                                                    </div>
                                                @else
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle"></i> Tidak ada jawaban
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <p><strong>Jawaban Benar:</strong></p>
                                                @if (isset($result['soal']) && $result['soal'] && isset($result['soal']->pilihanBenar))
                                                    <div class="alert alert-success">
                                                        <strong>{{ $result['soal']->pilihanBenar->huruf_pilihan ?? '' }}.</strong>
                                                        {{ $result['soal']->pilihanBenar->teks_pilihan ?? '' }}
                                                    </div>
                                                @else
                                                    <div class="alert alert-info">
                                                        Tidak ada kunci jawaban
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row">
                <div class="col-12 text-center">
                    <a href="{{ route('ujian.index') }}" class="btn btn-primary">
                        <i class="fas fa-list"></i> Kembali ke Daftar Ujian
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary ml-2">
                        <i class="fas fa-home"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection
