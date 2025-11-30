@extends('layouts.master')

@section('title', 'Edit Ujian')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Ujian</h3>
                            <div class="card-tools">
                                <a href="{{ route('master.ujian.index') }}" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="Kembali ke Daftar Ujian">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <form action="" method="POST" id="editUjianForm">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            <div class="card-body">
                                @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_ujian">Nama Ujian <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('nama_ujian') is-invalid @enderror"
                                                id="nama_ujian" name="nama_ujian" value="{{ old('nama_ujian', $ujian->nama_ujian) }}"
                                                placeholder="Masukkan nama ujian" required>
                                            @error('nama_ujian')
                                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="durasi_menit">Durasi (menit) <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('durasi_menit') is-invalid @enderror"
                                                id="durasi_menit" name="durasi_menit" value="{{ old('durasi_menit', $ujian->durasi_menit) }}"
                                                placeholder="Masukkan durasi dalam menit" min="1" required>
                                            @error('durasi_menit')
                                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="deskripsi">Deskripsi</label>
                                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                                id="deskripsi" name="deskripsi" rows="3"
                                                placeholder="Masukkan deskripsi ujian (opsional)">{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
                                            @error('deskripsi')
                                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $ujian->is_active) ? 'checked' : '' }}>
                                                <label for="is_active" class="custom-control-label">Aktif</label>
                                                <small class="form-text text-muted">Centang jika ujian ini aktif dan dapat diikuti oleh mahasiswa</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Update Data Ujian">
                                    <i class="fas fa-save"></i> Update
                                </button>
                                <a href="{{ route('master.ujian.index') }}" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="Batalkan dan Kembali">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Hidden input for encrypted ID -->
    <input type="hidden" id="encryptedUjianId" value="{{ \App\Services\UrlEncryptionService::encryptId($ujian->id) }}">
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const encryptedId = document.getElementById('encryptedUjianId').value;
    const form = document.getElementById('editUjianForm');

    if (encryptedId && form) {
        form.action = '/master/ujian/update/' + encryptedId;
    }
});
</script>
@endpush