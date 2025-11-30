@extends('layouts.master')

@section('title', 'Daftar Ujian')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clipboard-list mr-2"></i>
                                Daftar Ujian
                            </h3>
                        </div>
                        <div class="card-body">
                            @if ($ujians->count() > 0)
                                <div class="row">
                                    @foreach ($ujians as $ujian)
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div
                                                class="card card-outline
                                            @if ($ujian->status === 'completed') card-success
                                            @elseif($ujian->status === 'ongoing')
                                                card-warning
                                            @else
                                                card-primary @endif">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">{{ $ujian->nama_ujian }}</h5>
                                                    <span class="badge badge-{{ $ujian->statusClass }}">
                                                        {{ $ujian->statusText }}
                                                    </span>
                                                </div>
                                                <div class="card-body">
                                                    <div class="exam-info">
                                                        <div class="row mb-2">
                                                            <div class="col-6">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-clock"></i> Durasi
                                                                </small>
                                                                <div class="font-weight-bold">{{ $ujian->durasi_menit }}
                                                                    menit</div>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-question-circle"></i> Soal
                                                                </small>
                                                                <div class="font-weight-bold">{{ $ujian->total_soal }} soal
                                                                </div>
                                                            </div>
                                                        </div>

                                                        @if ($ujian->status !== 'available')
                                                            <div class="row mb-2">
                                                                <div class="col-12">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-check-circle"></i> Jawaban Terisi
                                                                    </small>
                                                                    <div class="font-weight-bold">
                                                                        {{ $ujian->answered_count }} /
                                                                        {{ $ujian->total_soal }} soal
                                                                        <span
                                                                            class="badge badge-{{ $ujian->answered_count == $ujian->total_soal ? '' : 'warning' }} ml-2">
                                                                            {{ $ujian->answered_count == $ujian->total_soal ? '' : round(($ujian->answered_count / $ujian->total_soal) * 100) . '%' }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($ujian->status === 'completed')
                                                            {{-- <div class="row mb-2">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-trophy"></i> Skor Akhir
                                                                </small>
                                                                <div class="font-weight-bold">
                                                                    {{ $ujian->score }} / 100
                                                                    <span class="badge badge-{{ $ujian->score >= 70 ? 'success' : ($ujian->score >= 50 ? 'warning' : 'danger') }} ml-2">
                                                                        {{ $ujian->score >= 70 ? 'Lulus' : ($ujian->score >= 50 ? 'Cukup' : 'Perlu Perbaikan') }}
                                                                    </span>
                                                                </div>
                                                                <small class="text-info">
                                                                    Benar: {{ $ujian->correct_count }} / {{ $ujian->total_soal }} soal
                                                                </small>
                                                            </div>
                                                        </div> --}}
                                                        @endif

                                                        @if ($ujian->deskripsi)
                                                            <p class="card-text text-muted small mb-2">
                                                                {{ Str::limit($ujian->deskripsi, 80) }}
                                                            </p>
                                                        @endif

                                                        @if ($ujian->sesiUjian)
                                                            <div class="mt-2">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-info-circle"></i>
                                                                    @if ($ujian->status === 'completed')
                                                                        Selesai pada:
                                                                        {{ $ujian->sesiUjian->waktu_selesai ? $ujian->sesiUjian->waktu_selesai->format('d M Y H:i') : '-' }}
                                                                    @elseif($ujian->status === 'ongoing')
                                                                        Dimulai:
                                                                        {{ $ujian->sesiUjian->waktu_mulai->format('d M Y H:i') }}
                                                                    @endif
                                                                </small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    @if ($ujian->status === 'available')
                                                        <!-- Belum pernah diikuti -->
                                                        <button type="button" class="btn btn-primary btn-block"
                                                            data-ujian-id="{{ $ujian->id }}"
                                                            onclick="showExamRules({{ $ujian->id }}, '{{ $ujian->nama_ujian }}', '{{ $ujian->durasi_menit }}')">
                                                            <i class="fas fa-play mr-2"></i> Mulai Ujian
                                                        </button>
                                                    @elseif($ujian->status === 'ongoing')
                                                        <!-- Sedang berlangsung -->
                                                        <a href="{{ route('ujian.show', $ujian->ongoingSession_encrypted) }}"
                                                            class="btn btn-warning btn-block">
                                                            <i class="fas fa-clock mr-2"></i> Lanjutkan Ujian
                                                        </a>
                                                    @else
                                                        {{-- <div class="d-flex gap-2">
                                                <a href="{{ route('ujian.result', $ujian->sesiUjian->id) }}"
                                                   class="btn btn-success btn-sm flex-fill">
                                                    <i class="fas fa-chart-line mr-1"></i> Lihat Hasil
                                                </a>
                                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                                    <i class="fas fa-check"></i> Selesai
                                                </button>
                                            </div> --}}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">Belum ada ujian tersedia</h4>
                                    <p class="text-muted">Hubungi administrator untuk informasi lebih lanjut</p>
                                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                        <i class="fas fa-home mr-2"></i> Kembali ke Dashboard
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Rules Modal -->
            <div class="modal fade" id="examRulesModal" tabindex="-1" role="dialog" aria-labelledby="examRulesModalLabel"
                aria-hidden="true" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="examRulesModalLabel">
                                <i class="fas fa-clipboard-check mr-2"></i>Tata Tertib Ujian
                            </h5>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info border-0">
                                <h6 class="alert-heading">
                                    <i class="fas fa-info-circle mr-2"></i>Informasi Ujian
                                </h6>
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Nama Ujian:</strong><br>
                                        <span id="modalExamName"></span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Durasi:</strong><br>
                                        <span id="modalExamDuration"></span> menit
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning border-0">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Peraturan Ujian
                                </h6>
                                <ol class="mb-0 small">
                                    <li>Ujian harus dikerjakan secara jujur tanpa bantuan pihak lain</li>
                                    <li>Dilarang membuka tab baru atau browser lain selama ujian</li>
                                    <li>Dilarang meng-copy-paste soal atau jawaban</li>
                                    <li>Dilarang keluar dari halaman ujian selesai waktu</li>
                                    <li>Sistem akan memantau aktivitas selama ujian</li>
                                    <li>Setiap pelanggaran akan dicatat oleh sistem</li>
                                </ol>
                            </div>

                            <div class="alert alert-danger border-0">
                                <h6 class="alert-heading">
                                    <i class="fas fa-ban mr-2"></i>Sistem 3 Pelanggaran
                                </h6>
                                <p class="mb-2 small"><strong>Ketentuan:</strong></p>
                                <ul class="mb-2 small">
                                    <li><strong>1x Pelanggaran:</strong> Peringatan pertama</li>
                                    <li><strong>2x Pelanggaran:</strong> Peringatan kedua (terakhir)</li>
                                    <li><strong>3x Pelanggaran:</strong> <span class="text-danger font-weight-bold">Ujian
                                            OTOMATIS disubmit</span></li>
                                </ul>
                                <hr class="my-2">
                                <p class="mb-2 small"><strong>Pelanggaran yang dihitung:</strong></p>
                                <ul class="mb-2 small">
                                    <li>Berpindah tab atau minimize browser</li>
                                    <li>Mencoba navigasi ke halaman lain</li>
                                    <li>Mencoba copy-paste soal atau jawaban</li>
                                    <li>Mencoba membuka developer tools</li>
                                </ul>
                                <hr class="my-2">
                                <p class="mb-2 small"><strong>KONSEKUENSI 3 PELANGGARAN:</strong></p>
                                <div class="bg-dark text-white p-2 rounded mb-0">
                                    <ul class="mb-0 small text-warning">
                                        <li>🚫 <strong>Ujian langsung berakhir</strong> dan jawaban otomatis disimpan</li>
                                    </ul>
                                </div>
                                {{-- <p class="mb-0 mt-2 small"><em>💡 <strong>Penting:</strong> Pastikan Anda fokus dan tidak melakukan pelanggaran agar ujian dapat diselesaikan dengan maksimal!</em></p> --}}
                            </div>

                            <div class="alert alert-success border-0">
                                <h6 class="alert-heading">
                                    <i class="fas fa-lightbulb mr-2"></i>Tips
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Pastikan koneksi internet stabil</li>
                                    <li>Kerjakan soal dengan teliti dan seksama</li>
                                    <li>Gunakan waktu dengan efisien</li>
                                    <li>Jawaban tersimpan otomatis saat memilih</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times mr-2"></i>Batal
                            </button>
                            <button type="button" class="btn btn-success" id="confirmStartExam">
                                <i class="fas fa-play mr-2"></i>Setuju & Mulai Ujian
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <script>
        function showExamRules(examId, examName, duration) {
            // Set modal content
            document.getElementById('modalExamName').textContent = examName;
            document.getElementById('modalExamDuration').textContent = duration;

            // Generate encrypted URL for start exam
            fetch('{{ route('ujian.generateEncryptedUrl') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        exam_id: examId,
                        action: 'start'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.encrypted_url) {
                        // Set up confirm button
                        const confirmBtn = document.getElementById('confirmStartExam');
                        confirmBtn.onclick = function() {
                            // Close modal
                            $('#examRulesModal').modal('hide');

                            // Redirect to encrypted start exam URL
                            window.location.href = data.encrypted_url;
                        };
                    } else {
                        // Fallback to direct encrypted URL generation
                        const confirmBtn = document.getElementById('confirmStartExam');
                        confirmBtn.onclick = function() {
                            $('#examRulesModal').modal('hide');
                            window.location.href = `/ujian/start/${generateBasicEncryptedId(examId)}`;
                        };
                    }
                })
                .catch(error => {
                    console.error('Error generating encrypted URL:', error);
                    // Fallback to direct encrypted URL generation
                    const confirmBtn = document.getElementById('confirmStartExam');
                    confirmBtn.onclick = function() {
                        $('#examRulesModal').modal('hide');
                        window.location.href = `/ujian/start/${generateBasicEncryptedId(examId)}`;
                    };
                });

            // Basic encryption fallback function
            function generateBasicEncryptedId(id) {
                return btoa('exam_' + id + '_' + Date.now()).replace(/[/+=]/g, '_');
            }

            // Show modal
            $('#examRulesModal').modal('show');
        }
    </script>
@endsection
