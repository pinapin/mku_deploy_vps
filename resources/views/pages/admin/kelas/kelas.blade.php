@extends('layouts.master')

@section('title', 'Kelas Dosen P2K')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <style>
        /* Custom styles for dosen cards */
        .dosen-card {
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        #dosen-kelas-container > div {
            transition: all 0.3s ease;
        }
        
        .animated-in {
            transition: all 0.3s ease;
        }
        
        .dosen-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        /* Card animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-animated {
            animation: fadeInUp 0.5s ease forwards;
            opacity: 0; /* Start with opacity 0 */
        }
        
        /* Ensure cards are visible after animation completes */
        .card-animated.animation-done {
            opacity: 1;
            transform: translateY(0);
        }
        
        .dosen-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #007bff;
        }
        
        .dosen-card .card-title {
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .dosen-card .table th {
            background-color: #f8f9fa;
        }
        
        .dosen-card .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        .btn-action-group {
            white-space: nowrap;
        }
        
        .btn-action-group .btn {
            margin: 0 2px;
            transition: all 0.2s;
        }
        
        .btn-action-group .btn:hover {
            transform: scale(1.1);
        }
        
        /* Loading animation */
        .loading-container {
            padding: 40px 0;
        }
        
        .loading-spinner {
            animation: pulse 1.5s infinite ease-in-out;
        }
        
        @keyframes pulse {
            0% { opacity: 0.6; transform: scale(0.9); }
            50% { opacity: 1; transform: scale(1); }
            100% { opacity: 0.6; transform: scale(0.9); }
        }
        
        /* Button loading state */
        .btn-loading {
            position: relative;
            pointer-events: none;
            color: transparent !important;
        }
        
        .btn-loading:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 1em;
            height: 1em;
            margin-top: -0.5em;
            margin-left: -0.5em;
            border-radius: 50%;
            border: 2px solid currentColor;
            border-right-color: transparent;
            animation: button-loading-spinner 0.75s linear infinite;
        }
        
        @keyframes button-loading-spinner {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        /* Modal loading overlay */
        .overlay-loading {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                    <h3 class="card-title">Daftar Kelas Dosen P2K - {{ $tahunAkademik->tahun_ajaran }} {{ $tahunAkademik->tipe_semester }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('p2k.kelas.index') }}" class="btn btn-secondary btn-sm mr-2" data-toggle="tooltip" title="Kembali ke daftar tahun akademik">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-primary btn-sm" id="btn-add" data-toggle="tooltip" title="Tambah kelas baru">
                            <i class="fas fa-plus"></i> Tambah Kelas
                        </button>
                    </div>
                </div>
                
                <!-- Alert for success messages -->
                {{-- <div class="alert alert-success alert-dismissible fade show d-none" role="alert" id="success-alert">
                    <i class="fas fa-check-circle mr-1"></i> <span id="success-message"></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> --}}
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="info-box bg-light">
                            <div class="info-box-content">
                                <div class="row">
                                    <div class="col-md-6">
                                        <span class="info-box-text">Total Data</span>
                                        <span class="info-box-number">
                                            <span id="total-dosen-count">0</span> Dosen dengan <span id="total-kelas-count">0</span> Kelas
                                        </span>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <span class="info-box-text">Tahun Akademik</span>
                                        <span class="info-box-number">{{ $tahunAkademik->tahun_ajaran }} {{ $tahunAkademik->tipe_semester }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" id="search-input" placeholder="Cari dosen atau kelas...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="btn-search" data-toggle="tooltip" title="Cari dosen atau kelas">
                                    <i class="fas fa-search"></i>
                                </button>
                                <button class="btn btn-default d-none" type="button" id="btn-clear" data-toggle="tooltip" title="Hapus pencarian">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-2" id="search-info">
                            <small class="text-muted">Menampilkan <span id="dosen-count">0</span> dosen dengan total <span id="kelas-count">0</span> kelas</small>
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default" id="view-grid" data-toggle="tooltip" title="Tampilan grid (2 kolom)">
                                <i class="fas fa-th-large"></i> Grid
                            </button>
                            <button type="button" class="btn btn-default" id="view-list" data-toggle="tooltip" title="Tampilan list (1 kolom)">
                                <i class="fas fa-list"></i> List
                            </button>
                        </div>
                    </div>
                </div>
                <div id="dosen-kelas-container" class="row">
                    <!-- Initial loading state -->
                    <div class="col-12 text-center loading-container card-animated" style="animation-delay: 0.1s; opacity: 0;">
                        <div class="card card-body shadow-sm p-5">
                            <div class="loading-spinner text-primary mb-3">
                                <i class="fas fa-spinner fa-spin fa-3x"></i>
                            </div>
                            <h4 class="mt-3">Memuat Data</h4>
                            <p class="text-muted">Sedang mengambil data dosen dan kelas...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Form -->
<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Tambah Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-kelas">
                <div class="modal-body">
                    <input type="hidden" id="kelas-id" value="">
                    <div class="form-group">
                        <label for="kode_dosen">Dosen</label>
                        <select class="form-control select2" id="kode_dosen" name="kode_dosen" style="width: 100%;">
                            <option value="">Pilih Dosen</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->kode_dosen }}">{{ $dosen->nama_dosen }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="kode_dosen-error"></div>
                    </div>
                    <div class="form-group">
                        <label for="kelas">Kelas (Pisahkan dengan koma untuk multiple kelas)</label>
                        <input type="text" class="form-control" id="kelas" name="kelas" placeholder="Contoh: 01,02,03" oninput="updateKelasCount(this.value)">
                        <small class="form-text text-muted">Masukkan kode kelas dipisahkan dengan koma (,) untuk menambahkan beberapa kelas sekaligus. Format kelas harus 2 digit.</small>
                        <div class="mt-2" id="kelas-count-info">
                            <span class="badge badge-info" style="font-size: 14px; padding: 5px 10px;"><span id="kelas-count-form">0</span> kelas akan ditambahkan</span>
                        </div>
                        <div class="invalid-feedback" id="kelas-error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-toggle="tooltip" title="Tutup form tanpa menyimpan">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-save" data-toggle="tooltip" title="Simpan data kelas">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="btn-save-spinner"></span>
                        <span id="btn-save-text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="modal-delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h4 class="modal-title">Konfirmasi Hapus</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="delete-confirmation-message">Apakah Anda yakin ingin menghapus data ini?</p>
                <input type="hidden" id="delete-id">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal" data-toggle="tooltip" title="Batal menghapus">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete" data-toggle="tooltip" title="Konfirmasi penghapusan kelas">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="btn-delete-spinner"></span>
                    <span id="btn-delete-text">Hapus</span>
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
    $(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            dropdownParent: $('#modal-form')
        });
        
        // Track if there are unsaved changes on the page
        let hasUnsavedChanges = false;
        
        // Warn user before leaving page with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini?';
                return e.returnValue;
            }
        });
        
        // Track form changes
        let formChanged = false;
        
        $('#form-kelas').on('change', 'input, select', function() {
            formChanged = true;
            hasUnsavedChanges = true;
        });
        
        // Confirm before closing modal if form has changes
        $('#modal-form').on('hide.bs.modal', function(e) {
            if (formChanged) {
                const confirmed = confirm('Ada perubahan yang belum disimpan. Yakin ingin menutup form?');
                if (!confirmed) {
                    e.preventDefault();
                } else {
                    formChanged = false;
                }
            }
        });
        
        // Reset form changed flag after successful submit
        $('#form-kelas').on('submit', function() {
            formChanged = false;
        });
        
        // Mark initial loading container as animation-done after animation completes
        setTimeout(function() {
            $('.loading-container').addClass('animation-done');
        }, 500);
        
        // Get saved search from localStorage
        const savedSearch = localStorage.getItem('kelasDosenSearch');
        
        // Load and group data by dosen
        function loadDosenKelasData() {
            $('#dosen-kelas-container').html(`
                <div class="col-12 text-center loading-container card-animated" style="animation-delay: 0.1s; opacity: 0;">
                    <div class="card card-body shadow-sm p-5">
                        <div class="loading-spinner text-primary mb-3">
                            <i class="fas fa-spinner fa-spin fa-3x"></i>
                        </div>
                        <h4 class="mt-3">Memuat Data</h4>
                        <p class="text-muted">Sedang mengambil data dosen dan kelas...</p>
                    </div>
                </div>
            `);
            
            // Mark loading container as animation-done after animation completes
            setTimeout(function() {
                $('.loading-container').addClass('animation-done');
            }, 500);
            
            $.ajax({
                url: "{{ route('p2k.kelas.data.kelas', $tahunAkademik->id) }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    // Group data by dosen
                    const dosenMap = {};
                    
                    response.data.forEach(function(item) {
                        const dosenId = item.kode_dosen;
                        const dosenName = item.dosen ? item.dosen.nama_dosen : 'Tidak ada dosen';
                        
                        if (!dosenMap[dosenId]) {
                            dosenMap[dosenId] = {
                                nama_dosen: dosenName,
                                kelas: []
                            };
                        }
                        
                        dosenMap[dosenId].kelas.push({
                            id: item.id,
                            kelas: item.kelas
                        });
                    });
                    
                    // Clear container
                    $('#dosen-kelas-container').empty();
                    
                    // Check if we have data
                    if (Object.keys(dosenMap).length === 0) {
                        $('#dosen-kelas-container').html(`
                            <div class="col-12 text-center loading-container card-animated" style="animation-delay: 0.1s; opacity: 0;">
                                <div class="card card-body shadow-sm p-5">
                                    <i class="fas fa-info-circle fa-3x text-info mb-3"></i>
                                    <h4 class="mt-2">Belum ada data kelas</h4>
                                    <p class="text-muted">Silakan tambahkan kelas baru untuk tahun akademik ini.</p>
                                    <div class="mt-3">
                                        <button class="btn btn-primary btn-lg" id="btn-add-empty" data-toggle="tooltip" title="Tambah kelas baru untuk tahun akademik ini">
                                            <i class="fas fa-plus mr-1"></i> Tambah Kelas Baru
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `);
                        
                        // Add event listener for the empty state add button
                        $('#btn-add-empty').on('click', function() {
                            $('#btn-add').click();
                        });
                        
                        // Mark card as animation-done after animation completes
                        setTimeout(function() {
                            $('.loading-container').addClass('animation-done');
                        }, 500);
                        
                        // Update counts
                        updateCounts(0, 0);
                        
                        return;
                    }
                    
                    // Calculate total kelas count
                    let totalKelasCount = 0;
                    Object.keys(dosenMap).forEach(function(dosenId) {
                        totalKelasCount += dosenMap[dosenId].kelas.length;
                    });
                    
                    // Update counts
                    updateCounts(Object.keys(dosenMap).length, totalKelasCount);
                    
                    // Trigger search if there's a saved search term
                    if (savedSearch) {
                        $('#search-input').trigger('keyup');
                    }
                    
                    // Sort dosen by nama_dosen in ascending order
                    const sortedDosenIds = Object.keys(dosenMap).sort(function(a, b) {
                        return dosenMap[a].nama_dosen.localeCompare(dosenMap[b].nama_dosen);
                    });
                    
                    // Render each dosen card with animation
                    sortedDosenIds.forEach(function(dosenId, index) {
                        const dosen = dosenMap[dosenId];
                        const dosenCard = $(`
                            <div class="col-12 col-md-6 mb-4 card-animated" style="animation-delay: ${index * 0.1}s; opacity: 0;">
                                <div class="card card-primary card-outline dosen-card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-user-tie mr-2"></i>
                                            ${dosen.nama_dosen}
                                        </h3>
                                        <div class="card-tools">
                                            <span class="badge badge-primary">${dosen.kelas.length} Kelas</span>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th>Kelas</th>
                                                        <th width="20%" class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="kelas-list-${dosenId}">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        
                        $('#dosen-kelas-container').append(dosenCard);
                        
                        // Mark card as animation-done after animation completes
                        setTimeout(function() {
                            dosenCard.addClass('animation-done');
                        }, (index * 100) + 500); // Add delay based on index plus animation duration
                        
                        // Add kelas rows
                        const kelasList = $(`#kelas-list-${dosenId}`);
                        dosen.kelas.forEach(function(kelas, index) {
                            kelasList.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td><span class="badge badge-light p-2">${kelas.kelas}</span></td>
                                    <td class="text-center">
                                        <div class="btn-action-group">
                                            <button type="button" class="btn btn-xs btn-info btn-edit" data-id="${kelas.id}" data-toggle="tooltip" title="Edit kelas ${kelas.kelas}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-danger btn-delete" data-id="${kelas.id}" data-toggle="tooltip" title="Hapus kelas ${kelas.kelas}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `);
                        });
                    });
                },
                error: function(xhr) {
                    $('#dosen-kelas-container').html(`
                        <div class="col-12 text-center loading-container card-animated" style="animation-delay: 0.1s; opacity: 0;">
                            <div class="card card-body shadow-sm p-5 border-danger">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h4 class="mt-2 text-danger">Terjadi Kesalahan</h4>
                                <p class="text-muted">Tidak dapat memuat data kelas. Silakan coba lagi.</p>
                                <div class="mt-3">
                                    <button class="btn btn-danger btn-lg" id="btn-retry-load" data-toggle="tooltip" title="Muat ulang data kelas">
                                        <i class="fas fa-sync-alt mr-1"></i> Coba Lagi
                                    </button>
                                </div>
                            </div>
                        </div>
                    `);
                    
                    // Mark card as animation-done after animation completes
                    setTimeout(function() {
                        $('.loading-container').addClass('animation-done');
                    }, 500);
                    
                    // Add event listener for retry button
                    $('#btn-retry-load').on('click', function() {
                        loadDosenKelasData();
                    });
                    
                    console.error('Error loading data:', xhr);
                }
            });
        }
        
        // Initial load
        loadDosenKelasData();
        
        // Initialize tooltips after data is loaded
        $(document).on('mouseenter', '[data-toggle="tooltip"]', function() {
            $(this).tooltip({
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        });
        
        // Function to update dosen and kelas counts
        function updateCounts(dosenCount, kelasCount) {
            $('#dosen-count').text(dosenCount);
            $('#kelas-count').text(kelasCount);
            
            // Get total counts regardless of visibility
            const totalDosenCount = $('.dosen-card').length || dosenCount;
            const totalKelasCount = $('.dosen-card tbody tr').length || kelasCount;
            
            $('#total-dosen-count').text(totalDosenCount);
            $('#total-kelas-count').text(totalKelasCount);
            
            // Update visibility of search info
            if (dosenCount === 0 && kelasCount === 0) {
                $('#search-info').addClass('d-none');
            } else {
                $('#search-info').removeClass('d-none');
            }
        }
        
        // Function to show success message
        function showSuccessMessage(message) {
            $('#success-message').text(message);
            // $('#success-alert').removeClass('d-none').addClass('show');
            
            // Auto hide after 5 seconds
            // setTimeout(function() {
            //     $('#success-alert').alert('close');
            // }, 5000);
        }
        
        // Handle search functionality
        $('#search-input').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            // If search term is empty, show all cards
            if (searchTerm === '') {
                $('.dosen-card').parent().show();
                
                // Update counts with all visible items
                const visibleDosenCount = $('.dosen-card').length;
                let visibleKelasCount = 0;
                $('.dosen-card').each(function() {
                    visibleKelasCount += $(this).find('tbody tr').length;
                });
                
                updateCounts(visibleDosenCount, visibleKelasCount);
                return;
            }
            
            let visibleDosenCount = 0;
            let visibleKelasCount = 0;
            
            // Loop through each dosen card
            $('.dosen-card').each(function() {
                const dosenCard = $(this);
                const dosenName = dosenCard.find('.card-title').text().toLowerCase();
                let found = dosenName.includes(searchTerm);
                let foundKelasCount = 0;
                
                // Check kelas numbers
                if (!found) {
                    dosenCard.find('tbody tr').each(function() {
                        const kelasText = $(this).find('td:nth-child(2)').text().toLowerCase();
                        if (kelasText.includes(searchTerm)) {
                            found = true;
                            foundKelasCount++;
                        }
                    });
                } else {
                    // If dosen name matches, count all kelas
                    foundKelasCount = dosenCard.find('tbody tr').length;
                }
                
                // Show/hide based on search result
                dosenCard.parent()[found ? 'show' : 'hide']();
                
                // Update counts
                if (found) {
                    visibleDosenCount++;
                    visibleKelasCount += foundKelasCount;
                }
            });
            
            // Update the count display
            updateCounts(visibleDosenCount, visibleKelasCount);
            
            // Show no results message if needed
            if (visibleDosenCount === 0) {
                if ($('#no-results-message').length === 0) {
                    $('#dosen-kelas-container').append(`
                        <div id="no-results-message" class="col-12 text-center py-4">
                            <i class="fas fa-search fa-2x text-muted mb-3"></i>
                            <p>Tidak ada hasil yang ditemukan untuk pencarian "${searchTerm}"</p>
                            <button class="btn btn-outline-secondary mt-2" id="btn-clear-search" data-toggle="tooltip" title="Hapus pencarian dan tampilkan semua data">
                                <i class="fas fa-times mr-1"></i> Hapus Pencarian
                            </button>
                        </div>
                    `);
                    
                    // Add event listener for clear search button
                    $('#btn-clear-search').on('click', function() {
                        $('#search-input').val('');
                        $('#btn-clear').addClass('d-none');
                        localStorage.removeItem('kelasDosenSearch');
                        $('#search-input').trigger('keyup');
                    });
                }
            } else {
                $('#no-results-message').remove();
            }
        });
        
        // Handle search button click
        $('#btn-search').on('click', function() {
            const searchTerm = $('#search-input').val();
            if (searchTerm) {
                $('#btn-clear').removeClass('d-none');
                localStorage.setItem('kelasDosenSearch', searchTerm);
            }
            $('#search-input').trigger('keyup');
        });
        
        // Handle clear button click
        $('#btn-clear').on('click', function() {
            $('#search-input').val('');
            $(this).addClass('d-none');
            localStorage.removeItem('kelasDosenSearch');
            $('#search-input').trigger('keyup');
        });
        
        // Restore search from localStorage if exists
        if (savedSearch) {
            $('#search-input').val(savedSearch);
            $('#btn-clear').removeClass('d-none');
            // We'll trigger the search after data is loaded
        }
        
        // Handle view toggle (grid/list)
        $('#view-grid').on('click', function() {
            $(this).addClass('active').siblings().removeClass('active');
            $('.dosen-card').parent().removeClass('col-12').addClass('col-md-6');
            localStorage.setItem('kelasDosenView', 'grid');
        });
        
        $('#view-list').on('click', function() {
            $(this).addClass('active').siblings().removeClass('active');
            $('.dosen-card').parent().removeClass('col-md-6').addClass('col-12');
            localStorage.setItem('kelasDosenView', 'list');
        });
        
        // Set view based on saved preference or default to grid
        const savedView = localStorage.getItem('kelasDosenView') || 'grid';
        if (savedView === 'list') {
            $('#view-list').addClass('active');
            $('#view-grid').removeClass('active');
        } else {
            $('#view-grid').addClass('active');
            $('#view-list').removeClass('active');
        }
        
        // Apply the view layout after data is loaded
        $(document).ajaxComplete(function(event, xhr, settings) {
            if (settings.url.includes('{{ route('p2k.kelas.data.kelas', $tahunAkademik->id) }}')) {
                if (savedView === 'list') {
                    $('.dosen-card').parent().removeClass('col-md-6').addClass('col-12');
                } else {
                    $('.dosen-card').parent().removeClass('col-12').addClass('col-md-6');
                }
            }
        });

        // Show Add Modal
        $('#btn-add').click(function() {
            $('#form-kelas').trigger('reset');
            $('#kelas-id').val('');
            $('#modal-title').text('Tambah Kelas');
            $('#modal-form').modal('show');
            clearErrors();
            $('#kelas-count-info').hide();
            validateForm(); // Validate form to disable submit button initially
            
            // Ensure the input event is triggered when the modal is shown
            $('#modal-form').on('shown.bs.modal', function() {
                // Trigger input event if there's already a value
                if ($('#kelas').val().trim() !== '') {
                    updateKelasCount($('#kelas').val());
                }
            });
        });
        
        // Count kelas when typing and validate format
        $('#kelas').on('input', function() {
            // Call the global updateKelasCount function
            updateKelasCount($(this).val());
        });
        
        // Validate dosen selection
        $('#kode_dosen').on('change', function() {
            validateForm();
        });
        
        // Function to validate form and enable/disable submit button
        window.validateForm = function() {
            const kelasValid = $('#kelas').val().trim() !== '' && !$('#kelas').hasClass('is-invalid');
            const dosenValid = $('#kode_dosen').val() !== '';
            
            if (kelasValid && dosenValid) {
                $('#btn-save').attr('disabled', false);
            } else {
                $('#btn-save').attr('disabled', true);
            }
        }

        // Show Edit Modal
        $('#dosen-kelas-container').on('click', '.btn-edit', function() {
            var id = $(this).data('id');
            $('#kelas-id').val(id);
            $('#modal-title').text('Edit Kelas');
            clearErrors();

            // Get data from server
            $.ajax({
                url: `{{ url('p2k/kelas') }}/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#kode_dosen').val(response.data.kode_dosen).trigger('change');
                    $('#kelas').val(response.data.kelas);
                    $('#modal-form').modal('show');
                    // Call updateKelasCount directly
                    updateKelasCount($('#kelas').val());
                    validateForm(); // Validate form to enable submit button if form is valid
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON.message ||
                            'Terjadi kesalahan saat mengambil data',
                        icon: 'error',
                        position: 'top-end',
                        toast: true,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });

        // Show Delete Modal
        $('#dosen-kelas-container').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            var kelas = $(this).closest('tr').find('td:nth-child(2)').text().trim();
            var dosenNama = $(this).closest('.dosen-card').find('.card-title').text().trim();
            
            $('#delete-id').val(id);
            
            // Update confirmation message with details
             var confirmMessage = 'Apakah Anda yakin ingin menghapus kelas <strong>' + kelas + '</strong> dari dosen <strong>' + dosenNama + '</strong>?';
             $('#delete-confirmation-message').html(confirmMessage);
            
            $('#modal-delete').modal('show');
        });

        // Handle Form Submit
        $('#form-kelas').submit(function(e) {
            e.preventDefault();
            var id = $('#kelas-id').val();
            var url = id ? `{{ url('p2k/kelas') }}/${id}` : "{{ route('p2k.kelas.store') }}";
            var method = id ? 'PUT' : 'POST';
            var formData = {
                kode_dosen: $('#kode_dosen').val(),
                kelas: $('#kelas').val(),
                tahun_akademik_id: {{ $tahunAkademik->id }}
            };

            // Show loading state
            $('#btn-save').data('original-text', $('#btn-save-text').text());
            $('#btn-save-text').text('Menyimpan...');
            $('#btn-save').attr('disabled', true);
            $('#btn-save').addClass('btn-loading');
            $('#btn-save-spinner').removeClass('d-none');
            clearErrors();
            
            // Add loading overlay to modal
            $('#modal-form .modal-content').append('<div class="overlay-loading"><i class="fas fa-spinner fa-spin"></i></div>');
            $('.overlay-loading').css({
                'position': 'absolute',
                'top': '0',
                'left': '0',
                'width': '100%',
                'height': '100%',
                'background-color': 'rgba(255,255,255,0.7)',
                'display': 'flex',
                'justify-content': 'center',
                'align-items': 'center',
                'z-index': '1000',
                'font-size': '2rem',
                'color': '#007bff'
            });

            $.ajax({
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: method,
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // Reset button state
                    $('#btn-save-text').text($('#btn-save').data('original-text') || 'Simpan');
                    $('#btn-save').attr('disabled', false);
                    $('#btn-save').removeClass('btn-loading');
                    $('#btn-save-spinner').addClass('d-none');
                    
                    $('#modal-form').modal('hide');
                    loadDosenKelasData(); // Reload the grouped view
                    formChanged = false;
                    hasUnsavedChanges = false;

                    let successMessage = response.message;
                    if (response.warning) {
                        successMessage += '\n' + response.warning;
                        if (response.existing_classes) {
                            successMessage += ': ' + response.existing_classes.join(', ');
                        }
                    }

                    // Show success message
                    showSuccessMessage(successMessage);
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: "success",
                        title: successMessage
                    });
                },
                error: function(xhr) {
                    // Reset button state
                    $('#btn-save-text').text($('#btn-save').data('original-text') || 'Simpan');
                    $('#btn-save').attr('disabled', false);
                    $('#btn-save').removeClass('btn-loading');
                    $('#btn-save-spinner').addClass('d-none');
                    
                    if (xhr.status === 422) {
                        const response = xhr.responseJSON;


                        // Display each error on the form
                        if (response.errors) {
                            const errors = response.errors;
                            $.each(errors, function(field, messages) {
                                const inputField = $('#' + field);
                                const errorDisplay = $('#' + field + '-error');

                                inputField.addClass('is-invalid');
                                errorDisplay.text(messages[0]);
                            });
                        }


                        if (response.message && response.message.includes('Kombinasi')) {

                            // Tampilkan error pada field yang relevan
                            $('#kelas-error').text(response.message);
                            $('#kode_dosen').addClass('is-invalid');
                            $('#kelas').addClass('is-invalid');
                        }

                        if (response.existing_classes) {
                            let errorMsg = 'Beberapa kelas sudah ada: ' + response.existing_classes.join(', ');
                            $('#kelas-error').text(errorMsg);
                            $('#kelas').addClass('is-invalid');
                        }

                        if (response.invalid_classes) {
                            let errorMsg = 'Format kelas tidak valid: ' + response.invalid_classes.join(', ') + '. Gunakan format 2 digit (contoh: 01)';
                            $('#kelas-error').text(errorMsg);
                            $('#kelas').addClass('is-invalid');
                        }


                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON.message ||
                                'Terjadi kesalahan saat menyimpan data',
                            icon: 'error',
                            position: 'top-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                },
                complete: function() {
                    // Remove loading overlay
                    $('.overlay-loading').remove();
                }
            });
        });

        // Handle Delete Confirmation
        $('#btn-confirm-delete').click(function() {
            var id = $('#delete-id').val();

            // Show loading state
            $('#btn-confirm-delete').attr('disabled', true);
            $('#btn-delete-spinner').removeClass('d-none');
            $('#btn-delete-text').text('Menghapus...');
            
            // Add loading overlay to modal
            $('#modal-delete .modal-content').append('<div class="overlay-loading"><i class="fas fa-spinner fa-spin"></i></div>');
            $('.overlay-loading').css({
                'position': 'absolute',
                'top': '0',
                'left': '0',
                'width': '100%',
                'height': '100%',
                'background-color': 'rgba(255,255,255,0.7)',
                'display': 'flex',
                'justify-content': 'center',
                'align-items': 'center',
                'z-index': '1000',
                'font-size': '2rem',
                'color': '#dc3545'
            });

            $.ajax({
                url: `{{ url('p2k/kelas') }}/${id}`,
                type: 'DELETE',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    // Reset button state
                    $('#btn-confirm-delete').attr('disabled', false);
                    $('#btn-delete-spinner').addClass('d-none');
                    $('#btn-delete-text').text('Hapus');
                    
                    $('#modal-delete').modal('hide');
                    loadDosenKelasData(); // Reload the grouped view
                    hasUnsavedChanges = false;

                    // Show success message
                    showSuccessMessage('Kelas berhasil dihapus');
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: "success",
                        title: response.message || 'Kelas berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    // Reset button state
                    $('#btn-confirm-delete').attr('disabled', false);
                    $('#btn-delete-spinner').addClass('d-none');
                    $('#btn-delete-text').text('Hapus');
                    
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON.message ||
                            'Terjadi kesalahan saat menghapus data',
                        icon: 'error',
                        position: 'top-end',
                        toast: true,
                        showConfirmButton: false,
                        timer: 3000
                    });
                },
                complete: function() {
                    // Reset button state
                    $('#btn-confirm-delete').html($('#btn-confirm-delete').data('original-text') || 'Hapus');
                    $('#btn-confirm-delete').attr('disabled', false);
                    $('#btn-confirm-delete').removeClass('btn-loading');
                    
                    // Remove loading overlay
                    $('.overlay-loading').remove();
                }
            });
        });

        // Helper function to clear validation errors
        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
        }
    });
    
    // Function to update kelas count (called directly from HTML)
    function updateKelasCount(value) {
        const kelasInput = value.trim();
        
        if (kelasInput === '') {
            $('#kelas-count-info').hide();
            $('#kelas').removeClass('is-invalid');
            $('#kelas-error').text('');
            // Check if we're inside the jQuery document ready function
            if (typeof validateForm === 'function') {
                validateForm();
            } else {
                // If validateForm is not accessible, enable/disable button directly
                const dosenValid = $('#kode_dosen').val() !== '';
                if (dosenValid) {
                    $('#btn-save').attr('disabled', false);
                } else {
                    $('#btn-save').attr('disabled', true);
                }
            }
            return;
        }
        
        const kelasArray = kelasInput.split(',').filter(item => item.trim() !== '');
        const kelasCount = kelasArray.length;
        
        // Validate format (2 digits)
        let hasInvalidFormat = false;
        let invalidKelas = [];
        
        kelasArray.forEach(function(kelas) {
            const trimmedKelas = kelas.trim();
            if (!/^\d{2}$/.test(trimmedKelas)) {
                hasInvalidFormat = true;
                invalidKelas.push(trimmedKelas);
            }
        });
        
        if (hasInvalidFormat) {
            $('#kelas').addClass('is-invalid');
            $('#kelas-error').text('Format kelas tidak valid: ' + invalidKelas.join(', ') + '. Kelas harus 2 digit angka.');
        } else {
            $('#kelas').removeClass('is-invalid');
            $('#kelas-error').text('');
        }
        
        // Update kelas count and make sure it's visible
        $('#kelas-count-form').text(kelasCount);
        
        // Force display block and check if it's working
        $('#kelas-count-info').show();
        $('#kelas-count-info').css('display', 'block');
        
        
        // Check if we're inside the jQuery document ready function
        if (typeof validateForm === 'function') {
            validateForm();
        } else {
            // If validateForm is not accessible, enable/disable button directly
            const dosenValid = $('#kode_dosen').val() !== '';
            if (!hasInvalidFormat && dosenValid) {
                $('#btn-save').attr('disabled', false);
            } else {
                $('#btn-save').attr('disabled', true);
            }
        }
    }
</script>
@endpush