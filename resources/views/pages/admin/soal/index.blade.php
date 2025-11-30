@extends('layouts.master')

@section('title', 'Kelola Soal - ' . $ujian->nama_ujian)

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
                            <h3 class="card-title">Daftar Soal ({{ $soals->count() }} soal)</h3>
                            <div class="card-tools">
                                <a href="{{ route('master.soal.create.encrypted', App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Soal
                                </a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success btn-sm dropdown-toggle"
                                        data-toggle="dropdown">
                                        <i class="fas fa-file-excel"></i> Import/Export
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('master.soal.downloadTemplate.encrypted', App\Services\UrlEncryptionService::encryptId($ujian->id)) }}"
                                            class="dropdown-item">
                                            <i class="fas fa-download"></i> Download Template
                                        </a>
                                        <button type="button" class="dropdown-item" data-toggle="modal"
                                            data-target="#importModal">
                                            <i class="fas fa-upload"></i> Import Excel
                                        </button>
                                    </div>
                                </div>
                                <a href="{{ route('master.ujian.show.encrypted', App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-default btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped" id="soalsTable">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="8%">Nomor</th>
                                        <th>Soal</th>
                                        <th width="10%">Jenis</th>
                                        <th width="10%">Pilihan</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($soals as $soal)
                                        <tr data-id="{{ $soal->id }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>
                                                <span class="badge badge-primary">{{ $soal->nomor_soal }}</span>
                                            </td>
                                            <td>
                                                <div class="question-text">
                                                    {{ Str::limit($soal->teks_soal, 150) }}
                                                    @if (strlen($soal->teks_soal) > 150)
                                                        <a href="javascript:void(0)" class="text-primary"
                                                            onclick="showQuestionDetail('{{ \App\Services\UrlEncryptionService::encryptId($soal->id) }}')"> (lihat
                                                            selengkapnya)</a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $soal->tipe == 'pilihan_ganda' ? 'badge-info' : 'badge-warning' }}">
                                                    {{ $soal->tipe == 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($soal->tipe == 'pilihan_ganda')
                                                    <span class="badge badge-success">{{ $soal->pilihan->count() }}
                                                        pilihan</span>
                                                @else
                                                    <span class="badge badge-secondary">Essay</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info btn-sm" title="Lihat"
                                                        onclick="showQuestionDetail('{{ \App\Services\UrlEncryptionService::encryptId($soal->id) }}')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <a href="{{ route('master.soal.edit.encrypted', \App\Services\UrlEncryptionService::encryptId($soal->id)) }}"
                                                        class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    {{-- @if ($soal->tipe == 'pilihan_ganda')
                                                        <a href="{{ route('master.pilihan.index.encrypted', \App\Services\UrlEncryptionService::encryptId($soal->id)) }}"
                                                            class="btn btn-success btn-sm" title="Kelola Pilihan">
                                                            <i class="fas fa-list"></i>
                                                        </a>
                                                    @endif --}}
                                                    <form action="{{ route('master.soal.destroy.encrypted', \App\Services\UrlEncryptionService::encryptId($soal->id)) }}"
                                                        method="POST" style="display: inline;"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data soal</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Soal dari Excel</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('master.soal.import.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Silakan download template terlebih dahulu untuk format yang
                            benar.
                        </div>

                        <div class="form-group">
                            <label for="file">File Excel <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file" name="file"
                                accept=".xlsx,.xls,.csv" required>
                            <small class="form-text text-muted">Format file: .xlsx, .xls, .csv (Maks 10MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Question Detail Modal -->
    <div class="modal fade" id="questionDetailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Soal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="questionDetailContent">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function showQuestionDetail(encryptedId) {
            $.get(`{{ route('master.soal.show.encrypted', ':id') }}`.replace(':id', encryptedId), function(data) {
                let html = `
            <div class="row">
                <div class="col-md-12">
                    <h6>Soal #${data.nomor_soal}</h6>
                    <p>${data.teks_soal}</p>
                    <hr>
                    <p><strong>Jenis:</strong> ${data.tipe == 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay'}</p>
                    ${data.tipe == 'pilihan_ganda' ? generateOptionsHtml(data.pilihan) : ''}
                </div>
            </div>
        `;
                $('#questionDetailContent').html(html);
                $('#questionDetailModal').modal('show');
            }).fail(function() {
                $('#questionDetailContent').html('<p class="text-danger">Gagal memuat detail soal</p>');
                $('#questionDetailModal').modal('show');
            });
        }

        function generateOptionsHtml(pilihan) {
            let html = '<h6>Pilihan Jawaban:</h6>';
            if (pilihan && pilihan.length > 0) {
                html += '<ul>';
                pilihan.forEach(opt => {
                    html +=
                        `<li><strong>${opt.huruf_pilihan}.</strong> ${opt.teks_pilihan} ${opt.is_benar ? '<span class="badge badge-success">Benar</span>' : ''}</li>`;
                });
                html += '</ul>';
            } else {
                html += '<p class="text-muted">Belum ada pilihan jawaban</p>';
            }
            return html;
        }
    </script>
@endpush
