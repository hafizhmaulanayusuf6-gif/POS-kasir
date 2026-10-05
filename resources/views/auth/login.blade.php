<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - KasirKu</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            min-height: 100vh;
            background: #f5f7fa;
            color: #172b4d;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 30px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1000px;
            min-height: 600px;

            background: #ffffff;

            display: grid;
            grid-template-columns: 45% 55%;

            border-radius: 20px;

            box-shadow:
                0 20px 50px rgba(0, 0, 0, 0.08);

            overflow: hidden;
        }

        /* ========================================
           BAGIAN KIRI
        ======================================== */

        .login-left {
            background: #ffffff;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            text-align: center;

            padding: 50px;
        }

        .logo {
            width: 250px;
            max-width: 100%;

            object-fit: contain;

            margin-bottom: 30px;
        }

        .brand-title {
            font-size: 34px;
            font-weight: 700;

            color: #172b4d;

            margin-bottom: 12px;
        }

        .brand-description {
            font-size: 16px;
            line-height: 1.6;

            color: #6b7280;

            max-width: 280px;
        }

        /* ========================================
           BAGIAN KANAN
        ======================================== */

        .login-right {
            background: #f8fafc;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 50px;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .login-title {
            font-size: 32px;
            font-weight: 700;

            color: #172b4d;

            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 15px;
            color: #6b7280;

            margin-bottom: 32px;
        }

        /* ========================================
           INPUT
        ======================================== */

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;

            font-size: 14px;
            font-weight: 600;

            color: #374151;

            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;

            height: 48px;

            padding: 0 15px;

            border: 1px solid #d1d5db;
            border-radius: 8px;

            background: #ffffff;

            font-size: 15px;

            color: #172b4d;

            outline: none;

            transition: all 0.2s ease;
        }

        .form-input:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        /* ========================================
           ERROR
        ======================================== */

        .error-message {
            margin-top: 7px;

            font-size: 13px;

            color: #dc2626;
        }

        /* ========================================
           REMEMBER + FORGOT
        ======================================== */

        .form-options {
            display: flex;

            align-items: center;
            justify-content: space-between;

            margin-bottom: 25px;
        }

        .remember-wrapper {
            display: flex;
            align-items: center;

            gap: 8px;
        }

        .remember-checkbox {
            width: 16px;
            height: 16px;

            accent-color: #2563eb;

            cursor: pointer;
        }

        .remember-label {
            font-size: 14px;
            color: #6b7280;

            cursor: pointer;
        }

        .forgot-link {
            font-size: 14px;

            color: #2563eb;

            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* ========================================
           BUTTON
        ======================================== */

        .login-button {
            width: 100%;
            height: 48px;

            border: none;
            border-radius: 8px;

            background: #2563eb;
            color: white;

            font-size: 15px;
            font-weight: 600;

            cursor: pointer;

            transition: all 0.2s ease;
        }

        .login-button:hover {
            background: #1d4ed8;

            transform: translateY(-1px);

            box-shadow:
                0 6px 15px rgba(37, 99, 235, 0.25);
        }

        .login-button:active {
            transform: translateY(0);
        }

        /* ========================================
           FOOTER
        ======================================== */

        .login-footer {
            text-align: center;

            margin-top: 30px;

            font-size: 13px;

            color: #9ca3af;
        }

        /* ========================================
           RESPONSIVE
        ======================================== */

        @media (max-width: 800px) {

            body {
                padding: 20px;
            }

            .login-wrapper {
                max-width: 500px;

                grid-template-columns: 1fr;

                min-height: auto;
            }

            .login-left {
                padding: 40px 30px 30px;
            }

            .logo {
                width: 180px;

                margin-bottom: 20px;
            }

            .brand-title {
                font-size: 28px;
            }

            .brand-description {
                font-size: 14px;
            }

            .login-right {
                padding: 40px 30px;
            }
        }

        @media (max-width: 480px) {

            body {
                padding: 15px;
            }

            .login-wrapper {
                border-radius: 15px;
            }

            .login-left {
                padding: 30px 20px 25px;
            }

            .login-right {
                padding: 30px 20px;
            }

            .logo {
                width: 150px;
            }

            .brand-title {
                font-size: 25px;
            }

            .login-title {
                font-size: 27px;
            }

            .form-options {
                flex-direction: column;

                align-items: flex-start;

                gap: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        {{-- ==========================================
             BAGIAN KIRI
        =========================================== --}}

        <div class="login-left">

            {{-- GANTI NAMA FILE JIKA NAMA LOGO BERBEDA --}}
            <img src="{{ asset('asset/logo/logo.png') }}" alt="KasirKu" class="logo">
        </div>

        {{-- ==========================================
             BAGIAN KANAN
        =========================================== --}}

        <div class="login-right">

            <div class="login-form-wrapper">

                <h2 class="login-title">
                    Selamat Datang
                </h2>

                {{-- SESSION STATUS --}}

                @if (session('status'))
                <div
                    style="
                            margin-bottom: 20px;
                            padding: 12px 15px;
                            background: #ecfdf5;
                            color: #047857;
                            border: 1px solid #a7f3d0;
                            border-radius: 8px;
                            font-size: 14px;
                        ">
                    {{ session('status') }}
                </div>
                @endif


                {{-- FORM LOGIN BREEZE --}}

                <form method="POST" action="{{ route('login') }}">

                    @csrf


                    {{-- EMAIL --}}

                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label">
                            Email
                        </label>

                        <input
                            id="email"
                            class="form-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Masukkan email">

                        @error('email')
                        <div class="error-message">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    {{-- PASSWORD --}}

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label">
                            Password
                        </label>

                        <input
                            id="password"
                            class="form-input"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Masukkan password">

                        @error('password')
                        <div class="error-message">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>


                    {{-- REMEMBER + FORGOT PASSWORD --}}

                    <div class="form-options">

                        <div class="remember-wrapper">

                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                class="remember-checkbox">

                            <label
                                for="remember_me"
                                class="remember-label">
                                Ingat saya
                            </label>

                        </div>


                        @if (Route::has('password.request'))

                        <a
                            href="{{ route('password.request') }}"
                            class="forgot-link">
                            Lupa password?
                        </a>

                        @endif

                    </div>


                    {{-- LOGIN BUTTON --}}

                    <button
                        type="submit"
                        class="login-button">
                        Masuk
                    </button>

                </form>


                {{-- FOOTER --}}

                <div class="login-footer">
                    © {{ date('Y') }} KasirKu
                </div>

            </div>

        </div>

    </div>

</body>

</html>