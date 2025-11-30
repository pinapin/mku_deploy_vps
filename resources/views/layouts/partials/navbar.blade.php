<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>

    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- User Profile Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link d-flex align-items-center" data-toggle="dropdown" href="#"
                style="padding: 0.5rem 1rem;">
                <div class="d-flex align-items-center">
                    <img src="{{ asset('assets/dist/img/user.png') }}" class="img-circle elevation-2 mr-2"
                        style="width: 2.5rem; height: 2.5rem; object-fit: cover;" alt="User Image">
                    <span
                        class="d-none d-md-inline text-dark font-weight-medium">{{ Session::get('nama') ?? 'Guest User' }}</span>
                    <i class="fas fa-chevron-down ml-2 text-muted" style="font-size: 0.75rem;"></i>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow-lg border-0"
                style="min-width: 200px; border-radius: 12px; margin-top: 0.5rem;">

                <!-- User Header -->
                <div class="dropdown-header bg-light border-bottom"
                    style="border-radius: 12px 12px 0 0; padding: 1rem;">
                    <div class="d-flex align-items-center text-left">
                        <img src="{{ asset('assets/dist/img/user.png') }}" class="img-circle mr-3"
                            style="width: 3rem; height: 3rem; object-fit: cover;" alt="User Image">
                        <div>
                            <h6 class="mb-0 font-weight-bold text-dark">{{ Session::get('nama') ?? 'Guest User' }}</h6>
                            <small class="text-muted">{{ Session::get('email') ?? 'guest@example.com' }}</small>
                        </div>
                    </div>
                </div>

                <!-- Menu Items -->
                <div class="p-2">
                    {{-- <a href="{{ route('profile.index') }}" class="dropdown-item d-flex align-items-center py-2 px-3 rounded"
                        style="transition: all 0.2s ease;">
                        <i class="fas fa-user-circle text-primary mr-3" style="width: 1.2rem;"></i>
                        <span class="font-weight-medium">Profil Saya</span>
                    </a> --}}

                    {{-- <div class="dropdown-divider my-2"></div> --}}

                    <a href="{{ route('logout') }}"
                        class="dropdown-item d-flex align-items-center py-2 px-3 rounded text-danger"
                        style="transition: all 0.2s ease;">
                        <i class="fas fa-sign-out-alt text-danger mr-3" style="width: 1.2rem;"></i>
                        <span class="font-weight-medium">Log Out</span>
                    </a>
                </div>
            </div>
        </li>

    </ul>
</nav>

<!-- Custom CSS for dropdown hover effects -->
<style>
    .dropdown-item:hover {
        background-color: #f8f9fa !important;
        transform: translateX(2px);
    }

    .dropdown-item.text-danger:hover {
        background-color: #fff5f5 !important;
    }

    .dropdown-menu {
        animation: fadeInUp 0.2s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .nav-link[data-toggle="dropdown"]:hover {
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 8px;
    }
</style>

<!-- /.navbar -->
