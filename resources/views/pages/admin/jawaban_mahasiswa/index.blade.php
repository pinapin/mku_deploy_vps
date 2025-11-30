@extends('layouts.master')

@section('title', 'Jawaban Mahasiswa')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Jawaban Mahasiswa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item"><a href="#">Ujian</a></li>
                        <li class="breadcrumb-item active">Jawaban Mahasiswa</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

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

            @if(isset($sesiUjian))
                <!-- Session Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Informasi Sesi Ujian</h5>
                        <div class="card-tools">
                            <a href="{{ route('master.jawaban_mahasiswa.index') }}" class="btn btn-default btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Umum
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Mahasiswa:</strong><br>
                                {{ $sesiUjian->nim }} - {{ $sesiUjian->mahasiswa->nama ?? 'Unknown' }}
                            </div>
                            <div class="col-md-3">
                                <strong>Ujian:</strong><br>
                                {{ $sesiUjian->ujian->nama_ujian }}
                            </div>
                            <div class="col-md-3">
                                <strong>Waktu:</strong><br>
                                {{ $sesiUjian->waktu_mulai->format('d/m/Y H:i') }}
                                @if($sesiUjian->waktu_selesai)
                                    - {{ $sesiUjian->waktu_selesai->format('H:i') }}
                                @endif
                            </div>
                            <div class="col-md-3">
                                <strong>Status & Skor:</strong><br>
                                <span class="badge {{ $sesiUjian->status === 'selesai' ? 'badge-success' : 'badge-warning' }}">
                                    {{ $sesiUjian->status }}
                                </span>
                                @if($sesiUjian->skor_akhir)
                                    <span class="badge {{ $sesiUjian->skor_akhir >= 70 ? 'badge-success' : ($sesiUjian->skor_akhir >= 60 ? 'badge-warning' : 'badge-danger') }}">
                                        {{ $sesiUjian->skor_akhir }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                {{ isset($sesiUjian) ? 'Jawaban Sesi Ini' : 'Daftar Jawaban Mahasiswa' }}
                                ({{ $jawabanMahasiswas->count() }} jawaban)
                            </h3>
                            @if(isset($sesiUjian))
                                <div class="card-tools">
                                    <a href="{{ route('master.jawaban_mahasiswa.review', $sesiUjian->id) }}" class="btn btn-info btn-sm" target="_blank">
                                        <i class="fas fa-search"></i> Review Mode
                                    </a>
                                    <a href="{{ route('master.sesi_ujian.show', $sesiUjian->id) }}" class="btn btn-default btn-sm">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Sesi
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        @if(!isset($sesiUjian))
                                            <tr>
                                                <th width="5%">No</th>
                                                <th>Mahasiswa</th>
                                                <th>Ujian</th>
                                                <th>Soal</th>
                                                <th>Jawaban</th>
                                                <th>Status</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        @else
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="8%">Nomor Soal</th>
                                                <th>Soal</th>
                                                <th width="15%">Jawaban Dipilih</th>
                                                <th width="10%">Status</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        @endif
                                    </thead>
                                    <tbody>
                                        @forelse($jawabanMahasiswas as $index => $jawaban)
                                            @if(!isset($sesiUjian))
                                                <!-- General View -->
                                                <tr>
                                                    <td>{{ isset($jawabanMahasiswas->firstItem()) ? ($jawabanMahasiswas->firstItem() + $index) : ($index + 1) }}</td>
                                                    <td>
                                                        <strong>{{ $jawaban->sesiUjian->nim }}</strong><br>
                                                        <small>{{ $jawaban->sesiUjian->mahasiswa->nama ?? 'Unknown' }}</small>
                                                    </td>
                                                    <td>{{ $jawaban->sesiUjian->ujian->nama_ujian }}</td>
                                                    <td>
                                                        No. {{ $jawaban->soal->nomor_soal }}<br>
                                                        <small>{{ Str::limit($jawaban->soal->teks_soal, 50) }}</small>
                                                    </td>
                                                    <td>
                                                        @if($jawaban->pilihanDipilih)
                                                            {{ $jawaban->pilihanDipilih->huruf_pilihan }}. {{ $jawaban->pilihanDipilih->teks_pilihan }}
                                                        @else
                                                            <span class="text-muted">Belum dijawab</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($jawaban->isBenar())
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-check"></i> Benar
                                                            </span>
                                                        @else
                                                            <span class="badge badge-danger">
                                                                <i class="fas fa-times"></i> Salah
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('master.jawaban_mahasiswa.show', $jawaban->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if($jawaban->sesiUjian->status === 'berlangsung')
                                                                <a href="{{ route('master.jawaban_mahasiswa.edit', $jawaban->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            @endif
                                                            <form action="{{ route('master.jawaban_mahasiswa.destroy', $jawaban->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jawaban ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <!-- Session Detail View -->
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <span class="badge badge-primary">{{ $jawaban->soal->nomor_soal }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="question-text">
                                                            {{ $jawaban->soal->teks_soal }}
                                                        </div>
                                                        @if($jawaban->soal->tipe === 'pilihan_ganda')
                                                            <br>
                                                            <small class="text-muted">
                                                                <strong>Jawaban Benar:</strong>
                                                                @php
                                                                    $correctOption = $jawaban->soal->pilihan->where('is_benar', true)->first();
                                                                    if ($correctOption) {
                                                                        echo $correctOption->huruf_pilihan . '. ' . $correctOption->teks_pilihan;
                                                                    }
                                                                @endphp
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($jawaban->pilihanDipilih)
                                                            <div class="jawaban-dipilih">
                                                                <span class="badge {{ $jawaban->isBenar() ? 'badge-success' : 'badge-danger' }}">
                                                                    {{ $jawaban->pilihanDipilih->huruf_pilihan }}. {{ $jawaban->pilihanDipilih->teks_pilihan }}
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="badge badge-secondary">
                                                                <i class="fas fa-minus"></i> Belum dijawab
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($jawaban->pilihanDipilih)
                                                            @if($jawaban->isBenar())
                                                                <span class="badge badge-success">
                                                                    <i class="fas fa-check"></i> Benar
                                                                </span>
                                                            @else
                                                                <span class="badge badge-danger">
                                                                    <i class="fas fa-times"></i> Salah
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="badge badge-secondary">Kosong</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('master.jawaban_mahasiswa.show', $jawaban->id) }}" class="btn btn-info btn-sm" title="Lihat Detail">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            @if($jawaban->sesiUjian->status === 'berlangsung')
                                                                <a href="{{ route('master.jawaban_mahasiswa.edit', $jawaban->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="{{ isset($sesiUjian) ? 6 : 7 }}" class="text-center">
                                                    Tidak ada data jawaban
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if(!isset($sesiUjian) && method_exists($jawabanMahasiswas, 'links'))
                                <div class="d-flex justify-content-center">
                                    {{ $jawabanMahasiswas->links() }}
                                </div>
                            @endif

                            @if(isset($sesiUjian))
                                <div class="mt-3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="alert alert-info">
                                                <h6><i class="fas fa-chart-pie"></i> Ringkasan Jawaban</h6>
                                                @php
                                                    $totalQuestions = $sesiUjian->ujian->soal->count();
                                                    $answeredQuestions = $jawabanMahasiswas->count();
                                                    $correctAnswers = $jawabanMahasiswas->filter(function($j) { return $j->isBenar(); })->count();
                                                    $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
                                                @endphp
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <strong>Total Soal:</strong> {{ $totalQuestions }}
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Dijawab:</strong> {{ $answeredQuestions }} ({{ round(($answeredQuestions / $totalQuestions) * 100) }}%)
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Benar:</strong> {{ $correctAnswers }}
                                                    </div>
                                                    <div class="col-md-3">
                                                        <strong>Skor:</strong> <span class="badge {{ $score >= 70 ? 'badge-success' : ($score >= 60 ? 'badge-warning' : 'badge-danger') }}">{{ $score }}%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.question-text {
    max-width: 300px;
    word-wrap: break-word;
}

.jawaban-dipilih {
    max-width: 200px;
    word-wrap: break-word;
}
</style>
@endsection