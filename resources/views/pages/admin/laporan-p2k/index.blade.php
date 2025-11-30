@extends('layouts.master')

@section('title', 'Laporan P2K')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Tahun Akademik</h3>
            </div>
            <div class="card-body">
                <table id="tahun-akademik-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Tahun Akademik</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Jumlah Kelas</th>
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

@endsection

@push('scripts')
<script>
    $(function() {
        // Initialize DataTable
        var table = $('#tahun-akademik-table').DataTable({
            "responsive": true,
            "autoWidth": false,
            "processing": true,
            "language": {
                "processing": "<i class='fas fa-spinner fa-spin fa-2x'></i>"
            },
            "ajax": {
                "url": "{{ route('p2k.laporan.data') }}",
                "type": "GET"
            },
            "columns": [{
                    "data": null,
                    "render": function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {"data": "tahun_ajaran"},
                {"data": "tipe_semester"},
                {
                    "data": "is_aktif",
                    "render": function(data, type, row) {
                        return data == 1 ?
                            '<span class="badge badge-success">Aktif</span>' :
                            '<span class="badge badge-secondary">Tidak Aktif</span>';
                    }
                },
                {"data": "jumlah_kelas"},
                {
                    "data": null,
                    "orderable": false,
                    "className": "text-center",
                    "render": function(data, type, row) {
                        return `
                            <a href="{{ url('p2k/laporan') }}/${row.id_encrypted}/show" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="top" title="Lihat Dosen">
                                <i class="fas fa-eye"></i> Lihat Dosen
                            </a>
                        `;
                    }
                }
            ],
            "drawCallback": function(settings) {
                // Initialize tooltips for newly rendered content
                $('[data-toggle="tooltip"]').tooltip();
            },
            "initComplete": function(settings, json) {
                // Initialize tooltips for initial content
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    });
</script>
@endpush