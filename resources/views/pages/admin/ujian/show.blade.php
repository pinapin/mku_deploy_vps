@extends('layouts.master')

@section('title', 'Detail Ujian')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Ujian</h3>
                            <div class="card-tools">
                                <a href="{{ route('master.ujian.edit.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-warning btn-sm" title="Edit Data Ujian">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('master.soal.index.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-success btn-sm" title="Kelola Soal Ujian">
                                    <i class="fas fa-question-circle"></i> Kelola Soal
                                </a>
                                <a href="{{ route('master.ujian.index') }}" class="btn btn-default btn-sm" title="Kembali ke Daftar Ujian">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Nama Ujian</strong></td>
                                            <td>{{ $ujian->nama_ujian }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Durasi</strong></td>
                                            <td>{{ $ujian->durasi_menit }} menit</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jumlah Soal</strong></td>
                                            <td>{{ $ujian->soal->count() }} soal</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Status</strong></td>
                                            <td>
                                                @if($ujian->is_active)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dibuat</strong></td>
                                            <td>{{ $ujian->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Diubah</strong></td>
                                            <td>{{ $ujian->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($ujian->deskripsi)
                                <hr>
                                <h5>Deskripsi</h5>
                                <p>{{ $ujian->deskripsi }}</p>
                            @endif

                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Aksi Cepat</h5>
                                    <div class="btn-group">
                                        <a href="{{ route('master.soal.index.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-success" title="Kelola Soal Ujian">
                                            <i class="fas fa-question-circle"></i> Kelola Soal
                                        </a>
                                        <a href="{{ route('master.sesi_ujian.index_by_ujian.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-info" title="Lihat Semua Sesi Ujian">
                                            <i class="fas fa-clock"></i> Lihat Sesi Ujian
                                        </a>
                                        @if($ujian->soal->count() > 0)
                                            <a href="{{ route('master.ujian.preview.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-primary" target="_blank" title="Preview Tampilan Ujian">
                                                <i class="fas fa-eye"></i> Preview Ujian
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($ujian->soal->count() > 0)
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Daftar Soal ({{ $ujian->soal->count() }} soal)</h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>Pertanyaan</th>
                                            <th width="10%">Jenis</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ujian->soal->sortBy('nomor_soal') as $index => $soal)
                                            <tr>
                                                <td>{{ $soal->nomor_soal }}</td>
                                                <td>
                                                    {{ Str::limit($soal->teks_soal, 100) }}
                                                    @if(strlen($soal->teks_soal) > 100)
                                                        <a href="javascript:void(0)" class="text-primary" onclick="showQuestionDetail('{{ App\Services\UrlEncryptionService::encryptId($soal->id) }}')"> (lihat selengkapnya)</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $soal->tipe == 'pilihan_ganda' ? 'badge-info' : 'badge-warning' }}">
                                                        {{ $soal->tipe == 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button data-toggle="tooltip" data-placement="top" title="Lihat" type="button" class="btn btn-info btn-sm" onclick="showQuestionDetail('{{ App\Services\UrlEncryptionService::encryptId($soal->id) }}')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <a data-toggle="tooltip" data-placement="top" title="Edit" href="{{ route('master.soal.edit.encrypted', \App\Services\UrlEncryptionService::encryptId($soal->id)) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        {{-- @if($soal->tipe == 'pilihan_ganda')
                                                            <a href="{{ route('master.pilihan.index', $soal->id) }}" class="btn btn-success btn-sm" title="Pilihan">
                                                                <i class="fas fa-list"></i>
                                                            </a>
                                                        @endif --}}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

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
        function showQuestionDetail(soalId) {
            $.get(`{{ route('master.soal.show.encrypted', ':id') }}`.replace(':id', soalId), function(data) {
                let html = `
            <div class="row">
                <div class="col-md-12">
                    <h6>Soal #${data.nomor_soal}</h6>
                    <p>${data.teks_soal}</p>
                    <hr>
                    <p><strong>Jenis:</strong> ${data.tipe == 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay'}</p>
                    ${data.tipe == 'pilihan_ganda' ? generateOptionsHtml(data.pilihan) : ''}
                    ${data.kunci_jawaban ? `<p><strong>Kunci Jawaban:</strong> ${data.kunci_jawaban}</p>` : ''}
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