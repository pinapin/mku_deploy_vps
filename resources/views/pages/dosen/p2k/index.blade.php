@extends('layouts.master')

@section('title', 'Kelas P2K')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4 border-left-primary">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-gradient-light">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-calendar-alt mr-2"></i>Daftar Tahun Akademik</h6>
                </div>
                <div class="card-body">
                    @if(count($tahunAkademiks) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Tahun Akademik</th>
                                        <th width="15%" class="text-center">Status</th>
                                        <th width="15%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tahunAkademiks as $index => $tahunAkademik)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $tahunAkademik->tahun_ajaran }} - {{ $tahunAkademik->tipe_semester }}</td>
                                            <td class="text-center">
                                                @if($tahunAkademik->is_aktif)
                                                    <span class="badge badge-success p-2"><i class="fas fa-check mr-1"></i>Aktif</span>
                                                @else
                                                    <span class="badge badge-secondary p-2"><i class="fas fa-times mr-1"></i>Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-primary btn-sm btn-kelas" data-tahun-akademik-id="{{ $tahunAkademik->id }}">
                                                    <i class="fas fa-list mr-1"></i> Lihat Kelas
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> Belum ada data tahun akademik yang tersedia.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Daftar Kelas -->
<div class="modal fade" id="kelasModal" tabindex="-1" role="dialog" aria-labelledby="kelasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="kelasModalLabel"><i class="fas fa-chalkboard mr-2"></i>Daftar Kelas</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="kelasInfo" class="alert alert-info mb-3 d-none">
                    <i class="fas fa-info-circle mr-1"></i> <span id="kelasInfoText"></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="kelasTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Kelas</th>
                                <th>Jumlah Mahasiswa</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="kelasTableBody">
                            <!-- Data kelas akan ditampilkan di sini -->
                        </tbody>
                    </table>
                </div>
                <div id="kelasEmpty" class="alert alert-info d-none">
                    <i class="fas fa-info-circle mr-1"></i> Belum ada data kelas yang tersedia.
                </div>
                <div id="kelasLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2 text-primary">Memuat data kelas...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times mr-1"></i>Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTable dengan konfigurasi
        $('#dataTable').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data yang ditemukan",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data yang tersedia",
                infoFiltered: "(difilter dari _MAX_ total data)",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                },
            },
            responsive: true
        });
        
        // Ketika tombol Kelas diklik
        $('.btn-kelas').on('click', function() {
            const tahunAkademikId = $(this).data('tahun-akademik-id');
            const tahunAkademik = $(this).closest('tr').find('td:nth-child(2)').text();
            
            // Set informasi tahun akademik
            $('#kelasInfoText').text(`Daftar kelas untuk Tahun Akademik: ${tahunAkademik}`);
            $('#kelasInfo').removeClass('d-none');
            
            // Reset dan tampilkan loading
            $('#kelasTableBody').empty();
            $('#kelasEmpty').addClass('d-none');
            $('#kelasLoading').removeClass('d-none');
            
            // Buka modal
            $('#kelasModal').modal('show');
            
            // Ambil data kelas berdasarkan tahun akademik
            $.ajax({
                url: '{{ route("dosen.p2k.get-kelas") }}',
                type: 'GET',
                data: {
                    tahun_akademik_id: tahunAkademikId
                },
                success: function(response) {
                    $('#kelasLoading').addClass('d-none');
                    
                    if (response.success && response.data.length > 0) {
                        let html = '';
                        
                        response.data.forEach(function(kelas, index) {
                            html += `
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>${kelas.kelas}</td>
                                    <td>${kelas.jumlah_mahasiswa}</td>
                                    <td class="text-center">
                                        <a href="{{ url('dosen/p2k/kelas') }}/${kelas.id}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye mr-1"></i> Detail Kelas
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#kelasTableBody').html(html);
                        
                        // Inisialisasi tooltip untuk tombol di dalam tabel
                        $('[data-toggle="tooltip"]').tooltip();
                    } else {
                        $('#kelasEmpty').removeClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    $('#kelasLoading').addClass('d-none');
                    $('#kelasEmpty').removeClass('d-none').html('<i class="fas fa-exclamation-circle mr-1"></i> Terjadi kesalahan saat mengambil data kelas.');
                    console.error(error);
                }
            });
        });
        
        // Inisialisasi tooltip
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush