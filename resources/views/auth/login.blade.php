<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Login &mdash; {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ url('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/fontawesome/css/all.css') }}">

    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/components.css') }}">

    <style>
        /* Pastikan body atau html memiliki tinggi penuh untuk centering */
        html, body {
            height: 100%;
            margin: 0;
        }

        .login-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            align-items: center;
            justify-content: center;
            padding: 1.5rem; /* Tambahkan padding agar card tidak menempel di tepi layar */
            background-color: #f8f9fa; /* Opsional: background abu-abu untuk seluruh halaman */
        }
        .login-card {
            display: flex;
            width: 100%;
            max-width: 1000px; /* Lebar card */
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            border-radius: 0.75rem;
            overflow: hidden;
            background-color: white; /* Card background */
        }

        .login-image-section {
            flex-basis: 65%; /* Gambar ambil 65% lebar */
            max-width: 65%;
            /* === Overlay gelap + Gambar Background === */
            /* Ganti 'atmo3.png' dengan 'atmo1.jpg' jika Anda lebih suka gambar itu */
            background-image: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.65)), url('../assets/img/atmo/atmo2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.8); /* Shadow kuat untuk teks */
            /* min-height tidak lagi diperlukan karena flex akan menyesuaikan tinggi */
        }
         .login-image-section h1 {
            font-size: 2.8rem;
            font-weight: bold;
            margin-bottom: 1rem;
         }
         .login-image-section p {
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
         }
         #realtime-clock {
            font-size: 1rem;
            margin-top: 2rem;
         }

        .login-form-section {
            flex-basis: 35%; /* Form ambil 35% lebar */
            max-width: 35%;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative; /* Untuk posisi alert */
        }
         /* --- Styling Alert Error --- */
        .login-form-section .alert {
            position: absolute;
            top: 1rem;
            left: 1rem;
            right: 1rem;
            z-index: 10;
            width: calc(100% - 2rem);
            margin-bottom: 0;
            box-shadow: var(--shadow-sm); /* Tambah sedikit shadow */
        }
        .login-form-section h4 {
            font-weight: bold;
            color: #343a40;
            /* Beri margin atas jika ada alert */
            margin-top: 0; /* Margin normal */
            transition: margin-top 0.3s ease;
        }
        /* Jika ada alert sebelum H4, beri margin atas pada H4 */
        .login-form-section .alert + h4 {
            margin-top: 4.5rem; /* Sesuaikan jarak ini */
        }
        /* --------------------------- */

         .form-separator {
            display: flex;
            align-items: center;
            text-align: center;
            color: #adb5bd;
            margin: 1.5rem 0;
        }
        .form-separator::before,
        .form-separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        .form-separator:not(:empty)::before { margin-right: .5em; }
        .form-separator:not(:empty)::after { margin-left: .5em; }
        .btn-google {
             background-color: #fff;
             color: #495057;
             border: 1px solid #ced4da;
        }
        .btn-google:hover { background-color: #f8f9fa; }
        .btn-google i { margin-right: 8px; }

        /* Responsif */
        @media (max-width: 991.98px) {
            .login-card {
                flex-direction: column;
                max-width: 480px;
            }
            .login-image-section {
                flex-basis: auto;
                max-width: 100%;
                min-height: 280px;
                padding: 2rem;
            }
            .login-image-section h1 { font-size: 2.2rem; }
            .login-form-section {
                flex-basis: auto;
                max-width: 100%;
                padding: 2rem;
            }
            /* Atur ulang posisi alert di layar kecil jika perlu */
            .login-form-section .alert {
                position: static; /* Kembali ke alur normal */
                width: 100%;
                margin-bottom: 1rem; /* Beri jarak bawah */
            }
            /* Reset margin H4 di layar kecil */
            .login-form-section h4,
            .login-form-section .alert + h4 {
                margin-top: 0;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <section class="section login-container">
            <div class="login-card">
                {{-- Bagian Kiri (Gambar & Info) --}}
                <div class="login-image-section">
                    <div>
                        <h1>Halo, Selamat Datang!</h1>
                        <p class="mb-1">Aplikasi Inventaris Paroki Keluarga Kudus Atmodirono.</p>
                        <p>Silakan masuk untuk melanjutkan ke portal Anda.</p>
                    </div>
                    <div id="realtime-clock">
                        {{-- Jam akan dimuat oleh Javascript --}}
                    </div>
                </div>

                {{-- Bagian Kanan (Form Login) --}}
                <div class="login-form-section">
                     {{-- Alert Error akan muncul di sini --}}
                    @include('utilities.alert')
                    
                    <h4 class="text-center mb-4">Login</h4>

                    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                tabindex="1" placeholder="Masukkan Email Anda" value="{{ old('email') }}" required autofocus>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="control-label">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                                tabindex="2" placeholder="Masukkan Password Anda" required>
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                Login
                            </button>
                        </div>
                    </form>
                     <div class="mt-4 text-muted text-center" style="font-size: 0.8rem;">
                         Made by <a target="_blank" href="https://www.instagram.com/robertusfirenza?igsh=dG92cXJkdWRwdDFh">Robertus Firenza</a> in 2025
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="{{ url('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ url('assets/js/popper.min.js') }}"></script>
    <script src="{{ url('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ url('assets/js/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ url('assets/js/moment.min.js') }}"></script>
    <script src="{{ url('assets/js/stisla.js') }}"></script>
    <script src="{{ url('assets/js/scripts.js') }}"></script>
    <script src="{{ url('assets/js/custom.js') }}"></script>

    <script>
        function updateClock() {
             moment.locale('id');
             const now = moment();
            const dateTimeString = now.format('dddd, D MMMM YYYY HH:mm:ss') + ' WIB';
            const clockElement = document.getElementById('realtime-clock');
            if(clockElement) { clockElement.textContent = dateTimeString; }
        }
        setInterval(updateClock, 1000);
        updateClock(); // Panggil sekali saat load
    </script>
</body>
</html>