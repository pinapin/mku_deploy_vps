@extends('layouts.master')

@section('title', 'Detail Soal #' . $soal->nomor_soal)

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Detail Soal #{{ $soal->nomor_soal }}</h3>
                            <div class="card-tools">
                                <a href="{{ route('master.soal.index.encrypted', \App\Services\UrlEncryptionService::encryptId($soal->id_ujian)) }}" class="btn btn-default btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <a href="{{ route('master.soal.edit.encrypted', \App\Services\UrlEncryptionService::encryptId($soal->id)) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label><strong>Nomor Soal</strong></label>
                                        <p>{{ $soal->nomor_soal }}</p>
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Pertanyaan</strong></label>
                                        <div class="card card-light">
                                            <div class="card-body">
                                                {!! nl2br(e($soal->teks_soal)) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label><strong>Jenis Soal</strong></label>
                                        <p>
                                            <span class="badge {{ $soal->tipe == 'pilihan_ganda' ? 'badge-info' : 'badge-warning' }}">
                                                {{ $soal->tipe == 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' }}
                                            </span>
                                        </p>
                                    </div>

                                    @if ($soal->tipe == 'essay' && $soal->kunci_jawaban)
                                        <div class="form-group">
                                            <label><strong>Kunci Jawaban</strong></label>
                                            <div class="card card-success">
                                                <div class="card-body">
                                                    {!! nl2br(e($soal->kunci_jawaban)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    @if ($soal->tipe == 'pilihan_ganda')
                                        <div class="form-group">
                                            <label><strong>Pilihan Jawaban</strong></label>
                                            @if ($soal->pilihan->count() > 0)
                                                @foreach ($soal->pilihan->sortBy('huruf_pilihan') as $pilihan)
                                                    <div class="card {{ $pilihan->is_benar ? 'bg-success' : 'bg-light' }} mb-2">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge {{ $pilihan->is_benar ? 'badge-success' : 'badge-secondary' }} mr-2">
                                                                    {{ $pilihan->huruf_pilihan }}
                                                                </span>
                                                                <span class="flex-grow-1">{{ $pilihan->teks_pilihan }}</span>
                                                                @if ($pilihan->is_benar)
                                                                    <i class="fas fa-check text-success"></i>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">Belum ada pilihan jawaban</p>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <a href="{{ route('master.pilihan.index', $soal->id) }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-list"></i> Kelola Pilihan
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection