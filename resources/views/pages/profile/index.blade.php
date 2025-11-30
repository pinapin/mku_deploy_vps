@extends('layouts.master')

@section('title', 'Profil Saya')

@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Profil Card -->
            <div class="col-md-4">
                <div class="card card-primary card-outline shadow-lg profile-card">
                    <div class="card-body box-profile">
                        <div class="profile-header">
                            <div class="profile-cover-bg"></div>
                            <div class="text-center position-relative mb-4">
                                <div class="profile-image-container">
                                    <img class="profile-user-img img-fluid rounded-circle" src="{{ asset('assets/dist/img/user2-160x160.jpg') }}" alt="User profile picture">
                                    <div class="profile-role-badge text-capitalize">{{ Auth::user()->role }}</div>
                                </div>
                            </div>
                        </div>

                        <h3 class="profile-username text-center mb-3">{{ Auth::user()->name }}</h3>
                       
                    </div>
                </div>
            </div>

            <!-- Edit Profile & Change Password Tabs -->
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header p-2 bg-white">
                        <ul class="nav nav-pills nav-modern">
                            <li class="nav-item"><a class="nav-link active" href="#edit-profile" data-toggle="tab"><i class="fas fa-user-edit mr-1"></i> Edit Profil</a></li>
                            <li class="nav-item"><a class="nav-link" href="#change-password" data-toggle="tab"><i class="fas fa-key mr-1"></i> Ubah Password</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <!-- Edit Profile Tab -->
                            <div class="active tab-pane" id="edit-profile">
                                <form id="profile-form">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name" class="form-label">Nama Lengkap</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" placeholder="Masukkan nama lengkap">
                                        </div>
                                        <div class="invalid-feedback" id="name-error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" placeholder="Masukkan alamat email">
                                        </div>
                                        <div class="invalid-feedback" id="email-error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="username" class="form-label">Username</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                            </div>
                                            <input type="text" class="form-control" id="username" value="{{ Auth::user()->username }}" disabled>
                                        </div>
                                        <small class="form-text text-muted"><i class="fas fa-info-circle mr-1"></i> Username tidak dapat diubah</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="role" class="form-label">Role</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                            </div>
                                            <input type="text" class="form-control text-capitalize" id="role" value="{{ Auth::user()->role }}" disabled>
                                        </div>
                                        <small class="form-text text-muted"><i class="fas fa-info-circle mr-1"></i> Role tidak dapat diubah</small>
                                    </div>
                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-primary btn-block" id="btn-update-profile">
                                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Change Password Tab -->
                            <div class="tab-pane" id="change-password">
                                <form id="password-form">
                                    @csrf
                                    <div class="form-group">
                                        <label for="current_password" class="form-label">Password Saat Ini</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                            </div>
                                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Masukkan password saat ini">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" id="current_password-error"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password" class="form-label">Password Baru</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                            </div>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password baru">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" id="password-error"></div>
                                        
                                    </div>
                                    <div class="form-group">
                                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                            </div>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password baru">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" id="password_confirmation-error"></div>
                                    </div>
                                    
                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-danger btn-block" id="btn-change-password">
                                            <i class="fas fa-key mr-1"></i> Ubah Password
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    /* Profile Card Styles */
    .profile-card {
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    }
    
    .profile-header {
        position: relative;
        margin-bottom: 1rem;
    }
    
    .profile-cover-bg {
        height: 80px;
        background: linear-gradient(135deg, #0061f2 0%, #6610f2 100%);
        border-radius: 8px 8px 0 0;
        margin: -1rem -1rem 0 -1rem;
    }
    
    .profile-image-container {
        position: relative;
        display: inline-block;
        margin-top: -40px;
        z-index: 1;
    }
    
    .profile-user-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }
    
    .profile-user-img:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }
    
    .profile-role-badge {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #007bff;
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transform: translate(10%, 50%);
    }
    
    .user-info-container {
        margin-top: 1.5rem;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.25rem;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
    }
    
    .user-info-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .user-info-item:last-child {
        border-bottom: none;
    }
    
    .info-icon {
        width: 40px;
        height: 40px;
        background: rgba(0, 123, 255, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        color: #007bff;
        flex-shrink: 0;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-label {
        color: #6c757d;
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .info-value {
        font-weight: 500;
        color: #343a40;
        font-size: 1rem;
    }
    
    .profile-username {
        font-weight: 600;
        color: #343a40;
        margin-top: 0.5rem;
    }
    
    /* Tab Styles */
    .nav-modern {
        border-bottom: none;
        gap: 0.5rem;
    }
    
    .nav-modern .nav-link {
        border-radius: 8px;
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s ease;
        color: #6c757d;
    }
    
    .nav-modern .nav-link:hover {
        background-color: #f8f9fa;
        color: #495057;
    }
    
    .nav-modern .nav-link.active {
        background-color: #007bff;
        color: white;
        box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);
    }
    
    /* Form Styles */
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
        border-color: #ced4da;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    }
    
    .btn-block {
        padding: 0.75rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);
    }
    
    .btn-danger {
        box-shadow: 0 2px 5px rgba(220, 53, 69, 0.3);
    }
    
    .btn-primary:hover, .btn-danger:hover {
        transform: translateY(-2px);
    }
    
    .toggle-password {
        cursor: pointer;
    }
    
    /* Card Styles */
    .card {
        border-radius: 10px;
        border: none;
        transition: all 0.3s ease;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(function() {
        // Toggle password visibility
        $('.toggle-password').click(function() {
            const target = $(this).data('target');
            const input = $(`#${target}`);
            const icon = $(this).find('i');
            
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Update Profile
        $('#profile-form').submit(function(e) {
            e.preventDefault();
            
            // Reset error messages
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            // Disable button and show loading state
            const btnUpdate = $('#btn-update-profile');
            const btnText = btnUpdate.html();
            btnUpdate.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
            btnUpdate.prop('disabled', true);
            
            $.ajax({
                url: "{{ route('profile.update') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Reload page to reflect changes
                        location.reload();
                    });
                },
                error: function(xhr) {
                    // Reset button state
                    btnUpdate.html(btnText);
                    btnUpdate.prop('disabled', false);
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON;
                        if (errors.message) {
                            // Show validation errors
                            if (errors.errors) {
                                $.each(errors.errors, function(key, value) {
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}-error`).text(value[0]);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: errors.message
                                });
                            }
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan. Silakan coba lagi.'
                        });
                    }
                }
            });
        });

        // Change Password
        $('#password-form').submit(function(e) {
            e.preventDefault();
            
            // Reset error messages
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            // Disable button and show loading state
            const btnChange = $('#btn-change-password');
            const btnText = btnChange.html();
            btnChange.html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
            btnChange.prop('disabled', true);
            
            $.ajax({
                url: "{{ route('profile.change-password') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    // Reset form
                    $('#password-form')[0].reset();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    
                    // Reset button state
                    btnChange.html(btnText);
                    btnChange.prop('disabled', false);
                },
                error: function(xhr) {
                    // Reset button state
                    btnChange.html(btnText);
                    btnChange.prop('disabled', false);
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON;
                        if (errors.message) {
                            // Show validation errors
                            if (errors.errors) {
                                $.each(errors.errors, function(key, value) {
                                    $(`#${key}`).addClass('is-invalid');
                                    $(`#${key}-error`).text(value[0]);
                                });
                            } else {
                                $(`#current_password`).addClass('is-invalid');
                                $(`#current_password-error`).show();
                                $(`#current_password-error`).text(errors.message);
                            }
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan. Silakan coba lagi.'
                        });
                    }
                }
            });
        });
    });
</script>
@endpush