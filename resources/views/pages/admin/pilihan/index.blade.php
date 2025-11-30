@extends('layouts.master')

@section('title', 'Kelola Pilihan - ' . Str::limit($soal->teks_soal, 50))

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
                            <h3 class="card-title">Daftar Pilihan ({{ $pilihans->count() }} pilihan)</h3>
                            <div class="card-tools">
                                <a href="{{ route('master.pilihan.create', $soal->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Pilihan
                                </a>
                                <a href="{{ route('master.soal.index', $soal->id_ujian) }}" class="btn btn-default btn-sm">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Soal
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Informasi Soal</h6>
                                <p class="mb-1"><strong>Ujian:</strong> {{ $soal->ujian->nama_ujian }}</p>
                                <p class="mb-0"><strong>Pertanyaan:</strong> {{ $soal->teks_soal }}</p>
                            </div>

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="10%">Huruf</th>
                                        <th>Teks Pilihan</th>
                                        <th width="15%">Status</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pilihans as $index => $pilihan)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge badge-primary">{{ $pilihan->huruf_pilihan }}</span>
                                            </td>
                                            <td>{{ $pilihan->teks_pilihan }}</td>
                                            <td>
                                                @if($pilihan->is_benar)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check"></i> Jawaban Benar
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">Salah</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('master.pilihan.show', $pilihan->id) }}" class="btn btn-info btn-sm" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('master.pilihan.edit', $pilihan->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(!$pilihan->is_benar)
                                                        <button type="button" class="btn btn-success btn-sm" title="Set sebagai Benar" onclick="setCorrectAnswer({{ $pilihan->id }})">
                                                            <i class="fas fa-check-circle"></i>
                                                        </button>
                                                    @endif
                                                    <form action="{{ route('master.pilihan.destroy', $pilihan->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pilihan ini?');">
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
                                            <td colspan="5" class="text-center">Tidak ada data pilihan</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            @if($pilihans->count() < 2)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> <strong>Peringatan:</strong> Soal pilihan ganda sebaiknya memiliki minimal 2 pilihan jawaban.
                                </div>
                            @endif

                            @if(!$pilihans->contains('is_benar', true))
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle"></i> <strong>Penting:</strong> Belum ada pilihan yang ditandai sebagai jawaban benar.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Aksi Cepat</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#bulkCreateModal">
                                        <i class="fas fa-plus-square"></i> Bulk Create Pilihan
                                    </button>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('master.soal.edit', $soal->id) }}" class="btn btn-warning btn-block">
                                        <i class="fas fa-edit"></i> Edit Soal
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('master.soal.index', $soal->id_ujian) }}" class="btn btn-default btn-block">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Soal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Bulk Create Modal -->
<div class="modal fade" id="bulkCreateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Create Pilihan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('master.pilihan.bulkCreate', $soal->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Masukkan semua pilihan sekaligus. Pilih salah satu sebagai jawaban benar.
                    </div>

                    <div id="pilihanContainer">
                        <div class="form-group">
                            <label>Pilihan 1</label>
                            <input type="text" class="form-control" name="pilihans[0][text]" placeholder="Masukkan pilihan 1" required>
                        </div>
                        <div class="form-group">
                            <label>Pilihan 2</label>
                            <input type="text" class="form-control" name="pilihans[1][text]" placeholder="Masukkan pilihan 2" required>
                        </div>
                        <div class="form-group">
                            <label>Pilihan 3</label>
                            <input type="text" class="form-control" name="pilihans[2][text]" placeholder="Masukkan pilihan 3" required>
                        </div>
                        <div class="form-group">
                            <label>Pilihan 4</label>
                            <input type="text" class="form-control" name="pilihans[3][text]" placeholder="Masukkan pilihan 4" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Jawaban Benar <span class="text-danger">*</span></label>
                        <select class="form-control" name="correct_answer" required>
                            <option value="">Pilih jawaban benar</option>
                            <option value="0">Pilihan 1</option>
                            <option value="1">Pilihan 2</option>
                            <option value="2">Pilihan 3</option>
                            <option value="3">Pilihan 4</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function setCorrectAnswer(pilihanId) {
    if (confirm('Apakah Anda yakin ingin menetapkan pilihan ini sebagai jawaban benar?')) {
        fetch(`{{ route('master.pilihan.setCorrect', '') }}/${pilihanId}`, {
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
                alert('Gagal menetapkan jawaban benar: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan. Silakan coba lagi.');
        });
    }
}
</script>
@endpush