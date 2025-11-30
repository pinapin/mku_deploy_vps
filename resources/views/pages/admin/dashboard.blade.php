@extends('layouts.master')

@section('title', 'Dashboard')

@push('css')
<style>
    .card-dashboard {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .card-dashboard:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .small-box .icon i {
        font-size: 50px;
        position: absolute;
        right: 15px;
        top: 15px;
        opacity: 0.3;
        transition: all 0.3s linear;
    }
    .small-box:hover .icon i {
        font-size: 55px;
        opacity: 0.5;
    }
    .latest-data-table th, .latest-data-table td {
        white-space: nowrap;
    }
    .bg-gradient-primary {
        background: linear-gradient(45deg, #4e73df, #224abe);
    }
    .bg-gradient-success {
        background: linear-gradient(45deg, #1cc88a, #13855c);
    }
    .bg-gradient-info {
        background: linear-gradient(45deg, #36b9cc, #258391);
    }
    .bg-gradient-warning {
        background: linear-gradient(45deg, #f6c23e, #dda20a);
    }
    .bg-gradient-danger {
        background: linear-gradient(45deg, #e74a3b, #be2617);
    }
    .bg-gradient-secondary {
        background: linear-gradient(45deg, #858796, #60616f);
    }
    .bg-gradient-light {
        background: linear-gradient(45deg, #f8f9fc, #c6d2e9);
    }
    .bg-gradient-dark {
        background: linear-gradient(45deg, #5a5c69, #373840);
    }
</style>
@endpush

@section('content')
<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="small-box bg-gradient-info card-dashboard">
            <div class="inner">
                <h3>{{ number_format($totalSertifikat) }}</h3>
                <p>Total Sertifikat KWU</p>
            </div>
            <div class="icon">
                <i class="fas fa-certificate"></i>
            </div>
            <a href="{{ route('kwu.sertifikat-kwu.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="small-box bg-gradient-success card-dashboard">
            <div class="inner">
                <h3>{{ number_format($sertifikatPerFakultas->count()) }}</h3>
                <p>Fakultas</p>
            </div>
            <div class="icon">
                <i class="fas fa-university"></i>
            </div>
            <a href="{{ route('kwu.sertifikat-kwu.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="small-box bg-gradient-warning card-dashboard">
            <div class="inner">
                <h3>{{ number_format($sertifikatPerProdi->count()) }}</h3>
                <p>Program Studi</p>
            </div>
            <div class="icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <a href="{{ route('kwu.sertifikat-kwu.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="small-box bg-gradient-danger card-dashboard">
            <div class="inner">
                <h3>{{ number_format($sertifikatPerTahun->count()) }}</h3>
                <p>Tahun Akademik</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <a href="{{ route('kwu.sertifikat-kwu.index') }}" class="small-box-footer">
                Lihat Detail <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <div class="col-md-8">
        <!-- Grafik Sertifikat per Tahun -->
        <div class="card card-primary card-outline card-dashboard">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar mr-1"></i>
                    Jumlah Sertifikat KWU per Tahun
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="sertifikatPerTahunChart"></canvas>
                </div>
            </div>
        </div>
        <!-- /.card -->

        <!-- Grafik Sertifikat per Fakultas -->
        <div class="card card-success card-outline card-dashboard">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Distribusi Sertifikat KWU per Fakultas
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="sertifikatPerFakultasChart"></canvas>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->

    <div class="col-md-4">
        <!-- Grafik Sertifikat per Semester -->
        <div class="card card-info card-outline card-dashboard">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    Distribusi per Semester
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="sertifikatPerSemesterChart"></canvas>
                </div>
            </div>
        </div>
        <!-- /.card -->

        <!-- Rata-rata Nilai -->
        <div class="card card-warning card-outline card-dashboard">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-star mr-1"></i>
                    Rata-rata Nilai
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="rataRataNilaiChart"></canvas>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->

<!-- Program Studi dengan Sertifikat Terbanyak -->
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline card-dashboard">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-1"></i>
                    Program Studi dengan Sertifikat Terbanyak
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="sertifikatPerProdiChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- /.col -->

    <div class="col-md-6">
        <div class="card card-success card-outline card-dashboard">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-1"></i>
                    Sertifikat KWU Terbaru
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover latest-data-table">
                        <thead>
                            <tr>
                                <th>No Sertifikat</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sertifikatTerbaru as $sertifikat)
                            <tr>
                                <td>{{ $sertifikat->no_sertifikat }}</td>
                                <td>{{ $sertifikat->nim }}</td>
                                <td>{{ $sertifikat->nama }}</td>
                                <td>{{ $sertifikat->programStudi->nama_prodi }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('kwu.sertifikat-kwu.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-search mr-1"></i> Lihat Semua Data
                </a>
            </div>
        </div>
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
@endsection

@push('scripts')
<script>
    $(function() {
        // Data untuk grafik
        const sertifikatPerTahunData = @json($sertifikatPerTahun);
        const sertifikatPerFakultasData = @json($sertifikatPerFakultas);
        const sertifikatPerProdiData = @json($sertifikatPerProdi);
        const sertifikatPerSemesterData = @json($sertifikatPerSemester);
        const rataRataNilaiData = @json($rataRataNilai);

        // Warna untuk grafik
        const backgroundColors = [
            'rgba(78, 115, 223, 0.8)',
            'rgba(28, 200, 138, 0.8)',
            'rgba(54, 185, 204, 0.8)',
            'rgba(246, 194, 62, 0.8)',
            'rgba(231, 74, 59, 0.8)',
            'rgba(133, 135, 150, 0.8)',
            'rgba(90, 92, 105, 0.8)',
            'rgba(32, 168, 216, 0.8)',
            'rgba(111, 66, 193, 0.8)',
            'rgba(249, 177, 21, 0.8)'
        ];

        // Grafik Sertifikat per Tahun
        const sertifikatPerTahunCtx = document.getElementById('sertifikatPerTahunChart').getContext('2d');
        new Chart(sertifikatPerTahunCtx, {
            type: 'bar',
            data: {
                labels: sertifikatPerTahunData.map(item => item.tahun),
                datasets: [{
                    label: 'Jumlah Sertifikat',
                    data: sertifikatPerTahunData.map(item => item.total),
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Jumlah: ${context.raw} sertifikat`;
                            }
                        }
                    }
                }
            }
        });

        // Grafik Sertifikat per Fakultas
        const sertifikatPerFakultasCtx = document.getElementById('sertifikatPerFakultasChart').getContext('2d');
        new Chart(sertifikatPerFakultasCtx, {
            type: 'pie',
            data: {
                labels: sertifikatPerFakultasData.map(item => item.nama_fakultas),
                datasets: [{
                    data: sertifikatPerFakultasData.map(item => item.total),
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Grafik Sertifikat per Program Studi
        const sertifikatPerProdiCtx = document.getElementById('sertifikatPerProdiChart').getContext('2d');
        new Chart(sertifikatPerProdiCtx, {
            type: 'horizontalBar',
            data: {
                labels: sertifikatPerProdiData.map(item => item.nama_prodi),
                datasets: [{
                    label: 'Jumlah Sertifikat',
                    data: sertifikatPerProdiData.map(item => item.total),
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Grafik Sertifikat per Semester
        const sertifikatPerSemesterCtx = document.getElementById('sertifikatPerSemesterChart').getContext('2d');
        new Chart(sertifikatPerSemesterCtx, {
            type: 'doughnut',
            data: {
                labels: sertifikatPerSemesterData.map(item => `Semester ${item.semester}`),
                datasets: [{
                    data: sertifikatPerSemesterData.map(item => item.total),
                    backgroundColor: backgroundColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Grafik Rata-rata Nilai
        const rataRataNilaiCtx = document.getElementById('rataRataNilaiChart').getContext('2d');
        new Chart(rataRataNilaiCtx, {
            type: 'bar',
            data: {
                labels: ['Nilai Teori', 'Nilai Praktek'],
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: [
                        parseFloat(rataRataNilaiData.rata_nilai_teori).toFixed(2),
                        parseFloat(rataRataNilaiData.rata_nilai_praktek).toFixed(2)
                    ],
                    backgroundColor: [
                        'rgba(54, 185, 204, 0.8)',
                        'rgba(246, 194, 62, 0.8)'
                    ],
                    borderColor: [
                        'rgba(54, 185, 204, 1)',
                        'rgba(246, 194, 62, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush