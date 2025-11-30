@extends('layouts.master')

@section('title', 'Sesi Ujian')

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Sesi Ujian - <strong
                                    class="text-primary">{{ $ujian->nama_ujian }}</strong></h3>
                            <div class="card-tools">
                                <a href="{{ route('master.ujian.index') }}" class="btn btn-default btn-sm"
                                    title="Kembali ke Data Ujian">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- JavaScript Filter Section -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-inline">
                                        <div class="form-group mr-2">
                                            <label for="filter_fakultas" class="mr-2">Fakultas:</label>
                                            <select name="filter_fakultas" id="filter_fakultas" class="form-control">
                                                <option value="">Semua Fakultas</option>
                                                @foreach ($fakultas ?? [] as $fakultas)
                                                    <option value="{{ $fakultas->id }}">{{ $fakultas->nama_fakultas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mr-2">
                                            <label for="filter_prodi" class="mr-2">Program Studi:</label>
                                            <select name="filter_prodi" id="filter_prodi" class="form-control" disabled>
                                                <option value="">Pilih Fakultas terlebih dahulu</option>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" id="resetFilters"
                                            title="Reset Filter">
                                            <i class="fas fa-redo"></i> Reset
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Mahasiswa</th>
                                            <th>Program Studi</th>
                                            <th>Ujian</th>
                                            <th>Waktu Mulai</th>
                                            <th>Waktu Selesai</th>
                                            <th>Durasi</th>
                                            <th>Status</th>
                                            <th>Skor</th>
                                            <th width="20%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($sesiUjians ?? [] as $index => $sesi)
                                            <tr class="sesi-row"
                                                data-fakultas="{{ $sesi->mahasiswa->programStudi->fakultas->id ?? '' }}"
                                                data-prodi="{{ $sesi->mahasiswa->programStudi->id ?? '' }}">
                                                <td>{{ isset($ujian) ? $index + 1 : $sesiUjians->firstItem() + $index }}
                                                </td>
                                                <td>
                                                    <strong>{{ $sesi->nim }}</strong><br>
                                                    <small
                                                        class="text-muted">{{ $sesi->mahasiswa->nama ?? 'Unknown' }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $sesi->mahasiswa->programStudi->nama_prodi ?? 'Unknown' }}</strong><br>
                                                    <small
                                                        class="text-muted">{{ $sesi->mahasiswa->programStudi->fakultas->nama_fakultas ?? 'Unknown' }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $sesi->ujian->nama_ujian }}</strong><br>
                                                    <small class="text-muted">{{ $sesi->ujian->durasi_menit }}
                                                        menit</small>
                                                </td>
                                                <td>{{ $sesi->waktu_mulai->format('d/m/Y H:i:s') }}</td>
                                                <td>
                                                    @if ($sesi->waktu_selesai)
                                                        {{ $sesi->waktu_selesai->format('d/m/Y H:i:s') }}
                                                    @else
                                                        <span class="text-muted">Belum selesai</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sesi->waktu_selesai)
                                                        @php
                                                            $duration = $sesi->waktu_mulai->diff($sesi->waktu_selesai);
                                                            $hours = $duration->h;
                                                            $minutes = $duration->i;
                                                            $seconds = $duration->s;

                                                            $durationText = '';
                                                            if ($hours > 0) {
                                                                $durationText .= $hours . 'jam ';
                                                            }
                                                            if ($minutes > 0) {
                                                                $durationText .= $minutes . 'menit ';
                                                            }
                                                            if ($seconds > 0 && $hours == 0) {
                                                                $durationText .= $seconds . 'detik';
                                                            }
                                                            echo trim($durationText);
                                                        @endphp
                                                    @else
                                                        @php
                                                            $duration = $sesi->waktu_mulai->diff(now());
                                                            $hours = $duration->h;
                                                            $minutes = $duration->i;

                                                            $durationText = '';
                                                            if ($hours > 0) {
                                                                $durationText .= $hours . 'jam ';
                                                            }
                                                            if ($minutes > 0) {
                                                                $durationText .= $minutes . 'menit ';
                                                            }
                                                            echo trim($durationText) . ' (berlangsung)';
                                                        @endphp
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sesi->status === 'berlangsung')
                                                        <span class="badge badge-warning">
                                                            <i class="fas fa-clock"></i> Berlangsung
                                                        </span>
                                                    @elseif($sesi->status === 'selesai')
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-check"></i> Selesai
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $sesi->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($sesi->skor_akhir !== null)
                                                        <span
                                                            class="badge {{ $sesi->skor_akhir >= 70 ? 'badge-success' : ($sesi->skor_akhir >= 60 ? 'badge-warning' : 'badge-danger') }}">
                                                            {{ $sesi->skor_akhir }}%
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button data-toggle="tooltip" data-placement="top"
                                                            title="Lihat Detail" type="button" class="btn btn-info btn-sm"
                                                            onclick="showJawabanDetail('{{ \App\Services\UrlEncryptionService::encryptId($sesi->id) }}')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if ($sesi->status === 'berlangsung')
                                                            <button data-toggle="tooltip" data-placement="top"
                                                                title="Force Finish" type="button"
                                                                class="btn btn-warning btn-sm"
                                                                onclick="forceFinish('{{ \App\Services\UrlEncryptionService::encryptId($sesi->id) }}')">
                                                                <i class="fas fa-stop"></i>
                                                            </button>
                                                            {{-- <button data-toggle="tooltip" data-placement="top" title="Extend Time" type="button" class="btn btn-primary btn-sm" onclick="extendTime('{{ \App\Services\UrlEncryptionService::encryptId($sesi->id) }}')">
                                                                <i class="fas fa-clock"></i>
                                                            </button> --}}
                                                        @endif
                                                        @if ($sesi->status === 'selesai')
                                                            <button data-toggle="tooltip" data-placement="top"
                                                                title="Review" type="button"
                                                                class="btn btn-secondary btn-sm"
                                                                onclick="reviewSession('{{ \App\Services\UrlEncryptionService::encryptId($sesi->id) }}')">
                                                                <i class="fas fa-search"></i>
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-danger btn-sm delete-sesi"
                                                            data-id="{{ \App\Services\UrlEncryptionService::encryptId($sesi->id) }}"
                                                            data-nama="{{ $sesi->nim }} - {{ $sesi->mahasiswa->nama ?? 'Unknown' }}"
                                                            data-toggle="tooltip" data-placement="top" title="Hapus Sesi">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">Tidak ada data sesi ujian</td>
                                            </tr>
                                        @endforelse
                                        <!-- Baris untuk pesan filter -->
                                        <tr id="no-filter-results" style="display: none;">
                                            <td colspan="10" class="text-center">
                                                <i class="fas fa-search"></i> Tidak ada data yang cocok dengan filter yang
                                                dipilih
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if (!isset($ujian) && method_exists($sesiUjians ?? [], 'links'))
                                <div class="d-flex justify-content-center">
                                    {{ $sesiUjians->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if (isset($ujian))
                <!-- Statistics Card -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Statistik Ujian</h5>
                                <div class="card-tools">
                                    <a href="{{ route('master.sesi_ujian.statistics.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}"
                                        class="btn btn-info btn-sm" title="Lihat Statistik Lengkap">
                                        <i class="fas fa-chart-bar"></i> Lihat Statistik
                                    </a>
                                    <a href="{{ route('master.sesi_ujian.exportResults.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}"
                                        class="btn btn-success btn-sm" title="Export Hasil">
                                        <i class="fas fa-file-excel"></i> Export
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Total Sesi</span>
                                                <span class="info-box-number">{{ $sesiUjians->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Selesai</span>
                                                <span
                                                    class="info-box-number">{{ $sesiUjians->where('status', 'selesai')->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Berlangsung</span>
                                                <span
                                                    class="info-box-number">{{ $sesiUjians->where('status', 'berlangsung')->count() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-primary"><i
                                                    class="fas fa-percentage"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Rata-rata Skor</span>
                                                <span class="info-box-number">
                                                    @php
                                                        $finishedSessions = $sesiUjians->where('status', 'selesai');
                                                        $avgScore =
                                                            $finishedSessions->count() > 0
                                                                ? round($finishedSessions->avg('skor_akhir'), 1)
                                                                : 0;
                                                    @endphp
                                                    {{ $avgScore }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Extend Time Modal -->
    <div class="modal fade" id="extendTimeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Perpanjang Waktu Ujian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="extendTimeForm">
                    @csrf
                    <input type="hidden" id="extend_sesi_id" name="sesi_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="additional_minutes">Tambah Waktu (menit) <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="additional_minutes" name="additional_minutes"
                                value="10" min="1" max="120" required>
                            <small class="form-text text-muted">Maksimal 120 menit</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-clock"></i> Perpanjang Waktu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Jawaban Detail Modal -->
    <div class="modal fade" id="jawabanDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Jawaban Mahasiswa</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="jawabanDetailContent">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SweetAlert for delete confirmation -->
    <form id="delete-sesi-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Professional Filtering System
        document.addEventListener('DOMContentLoaded', function() {
            const filterFakultas = document.getElementById('filter_fakultas');
            const filterProdi = document.getElementById('filter_prodi');
            const resetBtn = document.getElementById('resetFilters');
            const sesiRows = document.querySelectorAll('.sesi-row');
            const noResultsRow = document.getElementById('no-filter-results');

            // Program Studi data loaded from view
            const programStudiData = @json($programStudis ?? []);

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // SweetAlert confirmation for delete sesi
            document.querySelectorAll('.delete-sesi').forEach(function(button) {
                button.addEventListener('click', function() {
                    var sesiId = this.dataset.id;
                    var sesiNama = this.dataset.nama;
                    var form = document.getElementById('delete-sesi-form');
                    var url = '{{ route('master.sesi_ujian.delete.encrypted', ':id') }}'.replace(
                        ':id', sesiId);

                    form.setAttribute('action', url);

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        html: 'Anda akan menghapus sesi ujian:<br><strong>' + sesiNama +
                            '</strong><br><small>Tindakan ini tidak dapat dibatalkan!</small>',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal',
                        showLoaderOnConfirm: true,
                        preConfirm: function() {
                            form.submit();
                        },
                        allowOutsideClick: false
                    });
                });
            });

            // Handle Fakultas change
            filterFakultas?.addEventListener('change', function() {
                const selectedFakultasId = this.value;

                // Clear and reset Program Studi dropdown
                filterProdi.innerHTML = '';
                filterProdi.disabled = !selectedFakultasId;

                if (selectedFakultasId) {
                    // Add "All Program Studi" option for selected faculty
                    const allOption = document.createElement('option');
                    allOption.value = '';
                    allOption.textContent = 'Semua Program Studi';
                    filterProdi.appendChild(allOption);

                    // Add program studi for selected faculty
                    const filteredProdi = programStudiData.filter(prodi => prodi.fakultas_id ==
                        selectedFakultasId);
                    filteredProdi.forEach(prodi => {
                        const option = document.createElement('option');
                        option.value = prodi.id;
                        option.textContent = prodi.nama_prodi;
                        filterProdi.appendChild(option);
                    });
                } else {
                    filterProdi.innerHTML = '<option value="">Pilih Fakultas terlebih dahulu</option>';
                }

                // Apply filters
                applyFilters();
            });

            // Handle Program Studi change
            filterProdi?.addEventListener('change', applyFilters);

            // Handle Reset button
            resetBtn?.addEventListener('click', function() {
                filterFakultas.value = '';
                filterProdi.innerHTML = '<option value="">Pilih Fakultas terlebih dahulu</option>';
                filterProdi.disabled = true;
                applyFilters();
            });

            // Apply filters function
            function applyFilters() {
                const selectedFakultas = filterFakultas.value;
                const selectedProdi = filterProdi.value;
                let visibleCount = 0;

                sesiRows.forEach(row => {
                    const rowFakultas = row.dataset.fakultas;
                    const rowProdi = row.dataset.prodi;

                    let showRow = true;

                    // Filter by Fakultas
                    if (selectedFakultas && rowFakultas !== selectedFakultas) {
                        showRow = false;
                    }

                    // Filter by Program Studi
                    if (selectedProdi && rowProdi !== selectedProdi) {
                        showRow = false;
                    }

                    // Show/hide row
                    if (showRow) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show/hide "no results" message
                if (visibleCount === 0 && (selectedFakultas || selectedProdi)) {
                    noResultsRow.style.display = '';
                } else {
                    noResultsRow.style.display = 'none';
                }

                // Update row numbers
                updateRowNumbers();
            }

            // Update row numbers function
            function updateRowNumbers() {
                let counter = 1;
                sesiRows.forEach(row => {
                    if (row.style.display !== 'none') {
                        const numberCell = row.querySelector('td:first-child');
                        if (numberCell) {
                            numberCell.textContent = counter++;
                        }
                    }
                });
            }

            // Initial call to applyFilters if page loads with filters
            if (filterFakultas.value || filterProdi.value) {
                applyFilters();
            }
        });

        function forceFinish(encryptedId) {
            if (confirm('Apakah Anda yakin ingin menghentikan ujian ini secara paksa?')) {
                fetch(`{{ route('master.sesi_ujian.forceFinish.encrypted', ['encryptedId' => 'PLACEHOLDER']) }}`.replace(
                        'PLACEHOLDER', encryptedId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Gagal menghentikan ujian: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    });
            }
        }

        function extendTime(encryptedId) {
            document.getElementById('extend_sesi_id').value = encryptedId;
            $('#extendTimeModal').modal('show');
        }

        document.getElementById('extendTimeForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const encryptedId = document.getElementById('extend_sesi_id').value;
            const additionalMinutes = document.getElementById('additional_minutes').value;

            fetch(`{{ route('master.sesi_ujian.extendTime.encrypted', ['encryptedId' => 'PLACEHOLDER']) }}`
                    .replace('PLACEHOLDER', encryptedId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            additional_minutes: additionalMinutes
                        })
                    })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#extendTimeModal').modal('hide');
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Gagal memperpanjang waktu: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
        });

        function reviewSession(encryptedId) {
            window.open(`/master/jawaban_mahasiswa/review/${encryptedId}`, '_blank');
        }

        function showJawabanDetail(encryptedId) {
            $('#jawabanDetailModal').modal('show');

            // Show loading
            $('#jawabanDetailContent').html(
                '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data...</div>');

            // Load jawaban detail
            $.get(`/master/sesi_ujian/jawaban-detail/${encryptedId}`, function(data) {
                let html = generateJawabanDetailHtml(data);
                $('#jawabanDetailContent').html(html);
            }).fail(function() {
                $('#jawabanDetailContent').html(
                '<div class="alert alert-danger">Gagal memuat detail jawaban</div>');
            });
        }

        function generateJawabanDetailHtml(data) {
            let html = `
        <div class="row">
            <div class="col-md-12">
                <h6>Informasi Sesi Ujian</h6>
                <table class="table table-sm">
                    <tr><td>Mahasiswa</td><td><strong>${data.mahasiswa?.nim || '-'} - ${data.mahasiswa?.nama || 'Unknown'}</strong></td></tr>
                    <tr><td>Ujian</td><td><strong>${data.ujian?.nama_ujian || '-'}</strong></td></tr>
                    <tr><td>Waktu Mulai</td><td>${data.waktu_mulai || '-'}</td></tr>
                    <tr><td>Waktu Selesai</td><td>${data.waktu_selesai || 'Belum selesai'}</td></tr>
                    <tr><td>Skor Akhir</td><td><span class="badge badge-${data.skor_akhir >= 70 ? 'success' : (data.skor_akhir >= 60 ? 'warning' : 'danger')}">${data.skor_akhir || '-'}%</span></td></tr>
                </table>
                <hr>
                <h6>Jawaban Mahasiswa</h6>
    `;

            if (data.jawaban && data.jawaban.length > 0) {
                html += '<div class="table-responsive"><table class="table table-sm table-striped">';
                html += '<thead><tr><th>No</th><th>Soal</th><th>Jawaban</th><th>Benar/Salah</th></tr></thead><tbody>';

                data.jawaban.forEach(function(jawaban, index) {
                    const isCorrect = jawaban.is_benar || false;
                    html += `
                <tr>
                    <td>${jawaban.soal?.nomor_soal || '-'}</td>
                    <td>${jawaban.soal ? (jawaban.soal.teks_soal ? jawaban.soal.teks_soal.substring(0, 100) + '...' : '-') : '-'}</td>
                    <td>
                        ${jawaban.soal?.tipe === 'pilihan_ganda'
                            ? `${jawaban.pilihan_dipilih?.huruf_pilihan || '-'}. ${jawaban.pilihan_dipilih?.teks_pilihan || '-'}`
                            : (jawaban.jawaban_essay ? jawaban.jawaban_essay.substring(0, 80) + '...' : '-')
                        }
                    </td>
                    <td>
                        ${isCorrect
                            ? '<span class="badge badge-success"><i class="fas fa-check"></i> Benar</span>'
                            : (jawaban.soal?.tipe === 'pilihan_ganda'
                                ? '<span class="badge badge-danger"><i class="fas fa-times"></i> Salah</span>'
                                : '<span class="badge badge-secondary"><i class="fas fa-question"></i> Essay</span>')
                        }
                    </td>
                </tr>
            `;
                });

                html += '</tbody></table></div>';
            } else {
                html += '<p class="text-muted">Belum ada jawaban</p>';
            }

            html += '</div></div>';
            return html;
        }
    </script>
@endpush
