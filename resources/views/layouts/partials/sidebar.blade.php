<!-- Main Sidebar Container -->
<style>
    /* .elevation-3 {
        background-color: white;
        border-radius: 8px;
    } */

    /* Modern Brand Logo Styling */
    .brand-link {
        padding: 1.5rem 1rem !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        transition: all 0.3s ease;
    }

    .brand-link:hover {
        background-color: rgba(255, 255, 255, 0.05);
        transform: translateY(-1px);
    }

    .brand-link img {
        transition: all 0.3s ease;
        filter: brightness(1.2);
    }

    /* Modern User Panel Styling */
    .user-panel {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        border-radius: 12px;
        margin: 1rem 0.75rem;
        padding: 1.25rem 1rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .user-panel:hover {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0.08) 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .user-panel .image {
        margin-right: 0.75rem;
    }

    .user-panel .image img {
        width: 3rem !important;
        height: 3rem !important;
        border: 3px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        object-fit: cover;
    }

    .user-panel:hover .image img {
        border-color: rgba(255, 255, 255, 0.4);
        transform: scale(1.05);
    }

    .user-panel .info {
        flex: 1;
        min-width: 0;
    }

    .user-panel .info a {
        color: #fff !important;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-panel .info a:hover {
        color: #fff !important;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
    }

    /* Add user role indicator */
    .user-role {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.7);
        margin-top: 0.25rem;
        font-weight: 400;
        text-transform: capitalize;
    }

    /* Hide Modul Penggunaan button when sidebar is collapsed */
    .sidebar-mini.sidebar-collapse .sidebar-expanded-only {
        display: none !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .user-panel {
            margin: 0.75rem 0.5rem;
            padding: 1rem 0.75rem;
        }

        .user-panel .image img {
            width: 2.5rem !important;
            height: 2.5rem !important;
        }

        .user-panel .info a {
            font-size: 0.85rem;
        }
    }
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4 d-flex flex-column" style="height: 100vh;">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <img src="{{ asset('assets/image/logo-silaku.png') }}" class=""
            style="width: 100%; height: auto; object-fit: contain;" alt="MKU Logo">
    </a>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column flex-grow-1">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel d-flex align-items-center">
            <div class="image">
                <img src="{{ asset('assets/dist/img/user.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ Session::get('nama') ?? 'Guest User' }}</a>
                <div class="user-role">{{ Session::get('role') ?? 'Guest' }}</div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->

                @if (Session::get('role') === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('master.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('master.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-database"></i>
                            <p>
                                Master Data
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('master.kategori-umkm.index') }}"
                                    class="nav-link {{ request()->routeIs('master.kategori-umkm.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kategori UMKM</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('master.umkm.index') }}"
                                    class="nav-link {{ request()->routeIs('master.umkm.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data UMKM</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('master.tahun-akademik.index') }}"
                                    class="nav-link {{ request()->routeIs('master.tahun-akademik.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tahun Akademik</p>
                                </a>
                            </li>
                            <li
                                class="nav-item {{ request()->routeIs('master.ujian.*') || request()->routeIs('master.sesi_ujian.*') || request()->routeIs('master.soal.*') || request()->routeIs('master.pilihan.*') || request()->routeIs('master.jawaban_mahasiswa.*') ? 'menu-open' : '' }}">
                                <a href="#"
                                    class="nav-link {{ request()->routeIs('master.ujian.*') || request()->routeIs('master.sesi_ujian.*') || request()->routeIs('master.soal.*') || request()->routeIs('master.pilihan.*') || request()->routeIs('master.jawaban_mahasiswa.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>
                                        Ujian
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('master.ujian.index') }}"
                                            class="nav-link {{ request()->routeIs('master.ujian.index') ? 'active' : '' }}">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Data Ujian</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('master.sesi_ujian.index') }}"
                                            class="nav-link {{ request()->routeIs('master.sesi_ujian.*') ? 'active' : '' }}">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Sesi Ujian</p>
                                        </a>
                                    </li>
                                    {{-- <li class="nav-item">
                                        <a href="{{ route('admin.jawaban_mahasiswa.index') }}"
                                            class="nav-link {{ request()->routeIs('admin.jawaban_mahasiswa.*') ? 'active' : '' }}">
                                            <i class="far fa-dot-circle nav-icon"></i>
                                            <p>Jawaban Mahasiswa</p>
                                        </a>
                                    </li> --}}
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item {{ request()->routeIs('kwu.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('kwu.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-certificate"></i>
                            <p>
                                KWU
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('kwu.sertifikat-kwu.index') }}"
                                    class="nav-link {{ request()->routeIs('kwu.sertifikat-kwu.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Sertifikat KWU</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item {{ request()->routeIs('p2k.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('p2k.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>
                                P2K
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('p2k.mahasiswa.index') }}"
                                    class="nav-link {{ request()->routeIs('p2k.mahasiswa.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Mahasiswa</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('p2k.dosen.index') }}"
                                    class="nav-link {{ request()->routeIs('p2k.dosen.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Dosen</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('p2k.kelas.index') }}"
                                    class="nav-link {{ request()->routeIs('p2k.kelas.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kelas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('p2k.surat-pengantar.index') }}"
                                    class="nav-link {{ request()->routeIs('p2k.surat-pengantar.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Surat Pengantar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('p2k.pks.index') }}"
                                    class="nav-link {{ request()->routeIs('p2k.pks.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>PKS</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('p2k.ia.index') }}"
                                    class="nav-link {{ request()->routeIs('p2k.ia.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>IA</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('p2k.laporan.index') }}"
                                    class="nav-link {{ request()->routeIs('p2k.laporan.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Laporan P2K</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item {{ request()->routeIs('settings.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Settings
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('settings.surat-pengantar.index') }}"
                                    class="nav-link {{ request()->routeIs('settings.surat-pengantar.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Setting Surat Pengantar</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @elseif(Session::get('role') === 'dosen')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <!-- Menu khusus dosen di sini -->

                    <li class="nav-item {{ request()->routeIs('dosen.p2k.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('dosen.p2k.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>
                                P2K
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('dosen.p2k.index') }}"
                                    class="nav-link {{ request()->routeIs('dosen.p2k.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Kelas P2K</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @elseif(Session::get('role') === 'mahasiswa')
                    <!-- Menu khusus mahasiswa di sini -->
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('mahasiswa.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>
                                P2K
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('mahasiswa.data-umkm.index') }}"
                                    class="nav-link {{ request()->routeIs('mahasiswa.data-umkm.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data UMKM</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mahasiswa.surat-pengantar.index') }}"
                                    class="nav-link {{ request()->routeIs('mahasiswa.surat-pengantar.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Surat Pengantar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mahasiswa.pks.index') }}"
                                    class="nav-link {{ request()->routeIs('mahasiswa.pks.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>PKS</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('mahasiswa.laporan-akhir.index') }}"
                                    class="nav-link {{ request()->routeIs('mahasiswa.laporan-akhir.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Laporan Akhir</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @elseif(Session::get('role') === 'tamu')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}"
                            class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item {{ request()->routeIs('p2k.*') || request()->routeIs('master.umkm.*') ? 'menu-open' : '' }}">
                        <a href="#" class="nav-link {{ request()->routeIs('p2k.*') || request()->routeIs('master.umkm.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-envelope"></i>
                            <p>
                                P2K
                                <i class="fas fa-angle-left right"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('master.umkm.index') }}"
                                    class="nav-link {{ request()->routeIs('master.umkm.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Data UMKM</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('p2k.surat-pengantar.index') }}"
                                    class="nav-link {{ request()->routeIs('p2k.surat-pengantar.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Surat Pengantar</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('p2k.pks.index') }}"
                                    class="nav-link {{ request()->routeIs('p2k.pks.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>PKS</p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif
                <!-- Add more menu items as needed -->
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>

    <!-- Modul Penggunaan button positioned at the very bottom -->
    @if (Session::get('role') != 'admin' && Session::get('role') != 'tamu')
        <div class="mt-auto pb-3 px-3 sidebar-expanded-only">
            @if (Session::get('role') === 'mahasiswa')
                <a href="{{ asset('assets/modul/Buku Petunjuk Penggunaan Aplikasi SILAKU - Mahasiswa v2.pdf') }}"
                    target="_blank" class="btn btn-primary btn-block">
                @elseif(Session::get('role') === 'dosen')
                    <a href="{{ asset('assets/modul/Buku Petunjuk Penggunaan Aplikasi SILAKU - Dosen.pdf') }}"
                        target="_blank" class="btn btn-primary btn-block">
            @endif
            <i class="fas fa-book mr-2"></i>
            <span id="btn-save-text">Modul Penggunaan</span>
            </a>
        </div>
    @endif
</aside>
<!-- /.sidebar -->

</aside>
