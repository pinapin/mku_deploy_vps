@extends('layouts.master')

@section('title', 'Statistik Ujian')

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if(isset($ujian))
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Ujian: {{ $ujian->nama_ujian }}</h3>
                                <div class="card-tools">
                                    <a href="{{ route('master.sesi_ujian.index_by_ujian.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Sesi Ujian
                                    </a>
                                    <a href="{{ route('master.ujian.show.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-default btn-sm">
                                        <i class="fas fa-eye"></i> Detail Ujian
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="info-box bg-info">
                                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Durasi Ujian</span>
                                                <span class="info-box-number">{{ $ujian->durasi_menit }} menit</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-primary">
                                            <span class="info-box-icon"><i class="fas fa-question-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Jumlah Soal</span>
                                                <span class="info-box-number">{{ $ujian->soal->count() }} soal</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-success">
                                            <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Status Ujian</span>
                                                <span class="info-box-number">{{ $ujian->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box bg-warning">
                                            <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Dibuat</span>
                                                <span class="info-box-number">{{ $ujian->created_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistik Peserta -->
                                <h5 class="mb-3">Statistik Peserta</h5>
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Peserta</span>
                                                <span class="info-box-number">{{ $totalSessions }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Sudah Selesai</span>
                                                <span class="info-box-number">{{ $finishedSessions }}</span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success" style="width: {{ $totalSessions > 0 ? ($finishedSessions / $totalSessions) * 100 : 0 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Sedang Berlangsung</span>
                                                <span class="info-box-number">{{ $ongoingSessions }}</span>
                                                <div class="progress">
                                                    <div class="progress-bar bg-warning" style="width: {{ $totalSessions > 0 ? ($ongoingSessions / $totalSessions) * 100 : 0 }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-percentage"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Tingkat Penyelesaian</span>
                                                <span class="info-box-number">{{ $totalSessions > 0 ? round(($finishedSessions / $totalSessions) * 100, 1) : 0 }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistik Nilai -->
                                <h5 class="mb-3">Statistik Nilai</h5>
                                <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-primary"><i class="fas fa-chart-line"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rata-rata Nilai</span>
                                                <span class="info-box-number">{{ $averageScore }}</span>
                                                <small class="text-muted">dari {{ $finishedSessions }} peserta</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-trophy"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Nilai Tertinggi</span>
                                                <span class="info-box-number">{{ $maxScore }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-danger"><i class="fas fa-arrow-down"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Nilai Terendah</span>
                                                <span class="info-box-number">{{ $minScore }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-award"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Lulus KKM (70)</span>
                                                <span class="info-box-number">
                                                    @php
                                                        $lulusCount = 0;
                                                        if ($finishedSessions > 0) {
                                                            $sesiUjians = App\Models\SesiUjian::where('id_ujian', $ujian->id)
                                                                ->where('status', 'selesai')
                                                                ->where('skor_akhir', '>=', 70)
                                                                ->count();
                                                            $lulusCount = $sesiUjians;
                                                        }
                                                    @endphp
                                                    {{ $lulusCount }}
                                                </span>
                                                <small class="text-muted">({{ $finishedSessions > 0 ? round(($lulusCount / $finishedSessions) * 100, 1) : 0 }}%)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Distribusi Nilai -->
                                @if($finishedSessions > 0)
                                    <h5 class="mb-3">Distribusi Nilai</h5>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    @php
                                                        $sesiUjians = App\Models\SesiUjian::where('id_ujian', $ujian->id)
                                                            ->where('status', 'selesai')
                                                            ->get();

                                                        $gradeA = $sesiUjians->where('skor_akhir', '>=', 85)->count();
                                                        $gradeB = $sesiUjians->whereBetween('skor_akhir', [70, 84])->count();
                                                        $gradeC = $sesiUjians->whereBetween('skor_akhir', [60, 69])->count();
                                                        $gradeD = $sesiUjians->where('skor_akhir', '<', 60)->count();
                                                    @endphp

                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="text-center">
                                                                <h4 class="text-success">{{ $gradeA }}</h4>
                                                                <p class="mb-0">Nilai A (85-100)</p>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-success" style="width: {{ ($gradeA / $finishedSessions) * 100 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-center">
                                                                <h4 class="text-primary">{{ $gradeB }}</h4>
                                                                <p class="mb-0">Nilai B (70-84)</p>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-primary" style="width: {{ ($gradeB / $finishedSessions) * 100 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-center">
                                                                <h4 class="text-warning">{{ $gradeC }}</h4>
                                                                <p class="mb-0">Nilai C (60-69)</p>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-warning" style="width: {{ ($gradeC / $finishedSessions) * 100 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-center">
                                                                <h4 class="text-danger">{{ $gradeD }}</h4>
                                                                <p class="mb-0">Nilai D (&lt;60)</p>
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-danger" style="width: {{ ($gradeD / $finishedSessions) * 100 }}%"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Download Results -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="text-center">
                                            <a href="{{ route('master.sesi_ujian.exportResults.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-success">
                                                <i class="fas fa-file-excel"></i> Download Hasil Ujian (CSV)
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Statistik Tidak Tersedia</h3>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <h5><i class="icon fas fa-exclamation-triangle"></i> Peringatan!</h5>
                                    Silakan pilih ujian terlebih dahulu untuk melihat statistik.
                                    <hr>
                                    <a href="{{ route('master.ujian.index') }}" class="btn btn-primary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Ujian
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection