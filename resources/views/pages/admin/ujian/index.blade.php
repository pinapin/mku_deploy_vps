@extends('layouts.master')

@section('title', 'Data Ujian')

@section('content')

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Daftar Ujian</h3>
                            <div class="card-tools">
                                <a href="{{ route('master.ujian.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Ujian
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
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

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Nama Ujian</th>
                                        <th>Durasi</th>
                                        <th>Jumlah Soal</th>
                                        <th>Status</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ujians as $index => $ujian)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $ujian->nama_ujian }}</strong>
                                                @if($ujian->deskripsi)
                                                    <br><small class="text-muted">{{ $ujian->deskripsi }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $ujian->durasi_menit }} menit</td>
                                            <td>
                                                <span class="badge badge-info">{{ $ujian->soal_count }} soal</span>
                                            </td>
                                            <td>
                                                @if($ujian->is_active)
                                                    <span class="badge badge-success">Aktif</span>
                                                @else
                                                    <span class="badge badge-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('master.ujian.show.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('master.soal.index.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="top" title="Kelola Soal">
                                                        <i class="fas fa-question-circle"></i>
                                                    </a>
                                                    <a href="{{ route('master.ujian.edit.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top" title="Edit Ujian">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('master.ujian.preview.encrypted', \App\Services\UrlEncryptionService::encryptId($ujian->id)) }}" target="_blank" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Preview Ujian">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                    <form action="{{ route('master.ujian.toggleStatus', $ujian->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn {{ $ujian->is_active ? 'btn-secondary' : 'btn-primary' }} btn-sm" data-toggle="tooltip" data-placement="top" title="{{ $ujian->is_active ? 'Nonaktifkan Ujian' : 'Aktifkan Ujian' }}">
                                                            <i class="fas {{ $ujian->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-danger btn-sm delete-ujian" data-id="{{ $ujian->id }}" data-name="{{ $ujian->nama_ujian }}" data-toggle="tooltip" data-placement="top" title="Hapus Ujian">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data ujian</td>
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

    <!-- SweetAlert for delete confirmation -->
    <form id="delete-form" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // SweetAlert confirmation for delete
    document.querySelectorAll('.delete-ujian').forEach(function(button) {
        button.addEventListener('click', function() {
            var ujianId = this.dataset.id;
            var ujianName = this.dataset.name;
            var form = document.getElementById('delete-form');
            var url = '{{ route("master.ujian.destroy", ":id") }}'.replace(':id', ujianId);

            form.setAttribute('action', url);

            Swal.fire({
                title: 'Apakah Anda yakin?',
                html: 'Anda akan menghapus ujian:<br><strong>' + ujianName + '</strong><br><small>Tindakan ini tidak dapat dibatalkan!</small>',
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
});
</script>
@endpush