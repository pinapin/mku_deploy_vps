@extends('layouts.master')

@section('title', 'Laporan P2K - Daftar Kelas')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Kelas Dosen P2K - {{ $tahunAkademik->tahun_ajaran }} {{ $tahunAkademik->semester }}</h3>
                <div class="card-tools">
                    <a href="{{ route('p2k.laporan.index') }}" class="btn btn-secondary btn-sm mr-2">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table id="kelas-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Dosen</th>
                            <th>Jumlah Kelas</th>
                            <th>Jumlah Mahasiswa</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Daftar Kelas -->
<div class="modal fade" id="modal-kelas">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-kelas-title">Daftar Kelas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table id="modal-kelas-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kelas</th>
                            <th>Jumlah Mahasiswa</th>
                            <th>Laporan Tervalidasi</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(function() {
        // Initialize DataTable
        var table = $('#kelas-table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "processing": true,
            "language": {
                "processing": "<i class='fas fa-spinner fa-spin fa-2x'></i>"
            },
            "ajax": {
                "url": "{{ route('p2k.laporan.data.kelas', $tahunAkademik->id) }}",
                "type": "GET"
            },
            "columns": [{
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {"data": "dosen_name"},
                {"data": "jumlah_kelas"},
                {"data": "jumlah_mahasiswa"},
                {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <button type="button" class="btn btn-xs btn-info btn-lihat-kelas" data-dosen="${row.dosen_name}" data-dosen-id="${row.kode_dosen}">
                                <i class="fas fa-list"></i> Lihat Kelas
                            </button>
                        `;
                    }
                }
            ]
        });
        
        // Handle Lihat Kelas button click
        $('#kelas-table').on('click', '.btn-lihat-kelas', function() {
            var dosenName = $(this).data('dosen');
            var dosenId = $(this).data('dosen-id');
            
            // Set modal title
            $('#modal-kelas-title').text('Daftar Kelas - ' + dosenName);
            
            // Clear kelas table
            var kelasTableBody = $('#modal-kelas-table tbody');
            kelasTableBody.empty();
            kelasTableBody.append('<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat data kelas...</td></tr>');
            
            // Show modal
            $('#modal-kelas').modal('show');
            
            // Fetch kelas data from server based on dosen ID
            $.ajax({
                url: "{{ route('p2k.laporan.data.kelas', $tahunAkademik->id) }}?dosen_id=" + dosenId,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    kelasTableBody.empty();
                    
                    if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                        response.data.forEach(function(item, index) {
                            // Check if we have the necessary data
                            if (!item.kelas && !item.jumlah_mahasiswa && !item.laporan_tervalidasi) {
                                console.error('Missing required data in item:', item);
                                return;
                            }
                            
                            const percentage = item.jumlah_kelompok > 0 ? 
                                Math.round((item.laporan_tervalidasi / item.jumlah_kelompok) * 100) : 0;
                            
                            let badgeClass = 'badge-danger';
                            if (percentage >= 75) badgeClass = 'badge-success';
                            else if (percentage >= 50) badgeClass = 'badge-warning';
                            else if (percentage >= 25) badgeClass = 'badge-info';
                            
                            var row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.kelas}</td>
                                    <td>${item.jumlah_mahasiswa}</td>
                                    <td><span class="badge ${badgeClass}">${item.laporan_tervalidasi} / ${item.jumlah_kelompok} (${percentage}%)</span></td>
                                    <td class="text-center">
                                        <a href="{{ url('/p2k/laporan/kelas') }}/${item.id_encrypted}" class="btn btn-xs btn-primary">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            `;
                            kelasTableBody.append(row);
                        });
                    } else {
                        kelasTableBody.append('<tr><td colspan="5" class="text-center">Tidak ada data kelas</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching kelas data:', error);
                    kelasTableBody.empty();
                    kelasTableBody.append('<tr><td colspan="5" class="text-center">Error: Gagal memuat data kelas</td></tr>');
                }
            });
        });
    });
</script>
@endpush