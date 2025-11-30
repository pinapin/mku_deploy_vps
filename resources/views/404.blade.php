<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak</title>

    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('assets/dist/css/adminlte.min.css') }}">

    <style>
        body {
            font-family: 'Geist', sans-serif;
            height: 100vh;
            background: #f4f6f9;
            overflow: hidden;
        }

        .error-page {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .error-content {
            text-align: center;
            max-width: 500px;
            margin-left: 0!important;
        }

        .headline {
            font-size: 100px;
            font-weight: 700;
            color: #dc3545;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .error-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .back-button {
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="error-page">
        <div class="error-content">
            <i class="fas fa-ban error-icon"></i>
            <h3 class="headline">403</h3>
            <div class="error-content mt-4">
                <h3><i class="fas fa-exclamation-triangle text-danger"></i> Oops! Akses Ditolak</h3>
                <p class="mt-3">
                    Maaf, Anda tidak memiliki akses ke halaman ini.
                    Silakan kembali ke halaman sebelumnya atau hubungi administrator jika Anda yakin seharusnya memiliki
                    akses.
                </p>
                <div class="back-button">
                    <a href="{{ route('logout') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Halaman Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('assets/dist/js/adminlte.min.js') }}"></script>
</body>

</html>
