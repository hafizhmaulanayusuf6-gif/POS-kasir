<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar - KasirKu</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 30px;
        }

        /* =========================
           CONTAINER UTAMA
        ========================= */

        .register-container {
            width: 900px;
            max-width: 100%;

            min-height: 560px;

            background: #ffffff;

            border-radius: 16px;

            overflow: hidden;

            display: flex;

            box-shadow:
                0 15px 40px rgba(15, 23, 42, 0.08);
        }


        /* =========================
           BAGIAN LOGO
        ========================= */

        .register-logo {
            width: 50%;

            background: #ffffff;

            display: flex;
            justify-content: center;
            align-items: center;

            padding: 50px;
        }

        .register-logo img {
            width: 270px;
            max-width: 100%;
            height: auto;

            object-fit: contain;
        }


        /* =========================
           BAGIAN FORM
        ========================= */

        .register-form {
            width: 50%;

            background: #f8fafc;

            padding: 55px 65px;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }


        /* =========================
           JUDUL
        ========================= */

        .register-form h1 {
            font-size: 30px;

            color: #17365d;

            margin-bottom: 8px;

            font-weight: 700;
        }

        .register-description {
            font-size: 14px;

            color: #64748b;

            margin-bottom: 28px;
        }


        /* =========================
           LABEL
        ========================= */

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;

            font-size: 14px;

            font-weight: 600;

            color: #1e293b;

            margin-bottom: 7px;
        }


        /* =========================
           INPUT
        ========================= */

        .form-input {
            width: 100%;

            height: 44px;

            border: 1px solid #d1d5db;

            border-radius: 7px;

            padding: 0 13px;

            font-size: 14px;

            color: #1e293b;

            background: #ffffff;

            outline: none;

            transition: 0.2s;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        .form-input:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.10);
        }


        /* =========================
           ERROR
        ========================= */

        .error-message {
            color: #dc2626;

            font-size: 12px;

            margin-top: 6px;
        }


        /* =========================
           TOMBOL
        ========================= */

        .register-button {
            width: 100%;

            height: 44px;

            border: none;

            border-radius: 7px;

            background: #2563eb;

            color: #ffffff;

            font-size: 14px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;

            margin-top: 5px;
        }

        .register-button:hover {
            background: #1d4ed8;
        }

        .register-button:active {
            transform: scale(0.99);
        }


        /* =========================
           LOGIN LINK
        ========================= */

        .login-link {
            text-align: center;

            margin-top: 20px;

            font-size: 13px;

            color: #64748b;
        }

        .login-link a {
            color: #2563eb;

            text-decoration: none;

            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }


        /* =========================
           FOOTER
        ========================= */

        .register-footer {
            text-align: center;

            margin-top: 25px;

            font-size: 12px;

            color: #94a3b8;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 768px) {

            body {
                padding: 20px;
            }

            .register-container {
                flex-direction: column;

                width: 100%;

                min-height: auto;
            }

            .register-logo {
                width: 100%;

                padding: 35px 30px;
            }

            .register-logo img {
                width: 190px;
            }

            .register-form {
                width: 100%;

                padding: 35px 30px;
            }

            .register-form h1 {
                font-size: 26px;
            }
        }
    </style>
</head>

<body>

    <div class="register-container">

        <!-- =========================
             LOGO
        ========================== -->

        <div class="register-logo">

            <img
                src="{{ asset('asset/logo/logo.png') }}"
                alt="KasirKu">

        </div>


        <!-- =========================
             FORM REGISTER
        ========================== -->

        <div class="register-form">

            <h1>Buat Akun</h1>

            <p class="register-description">
                Daftar untuk mulai menggunakan KasirKu.
            </p>


            <!-- FORM BREEZE -->

            <form method="POST" action="{{ route('register') }}">

                @csrf


                <!-- NAME -->

                <div class="form-group">

                    <label for="name">
                        Nama
                    </label>

                    <input
                        id="name"
                        class="form-input"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama"
                        required
                        autofocus
                        autocomplete="name">

                    @error('name')
                        <div class="error-message">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        id="email"
                        class="form-input"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email"
                        required
                        autocomplete="username">

                    @error('email')
                        <div class="error-message">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        id="password"
                        class="form-input"
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        autocomplete="new-password">

                    @error('password')
                        <div class="error-message">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- CONFIRM PASSWORD -->

                <div class="form-group">

                    <label for="password_confirmation">
                        Konfirmasi Password
                    </label>

                    <input
                        id="password_confirmation"
                        class="form-input"
                        type="password"
                        name="password_confirmation"
                        placeholder="Ulangi password"
                        required
                        autocomplete="new-password">

                    @error('password_confirmation')
                        <div class="error-message">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <!-- BUTTON -->

                <button
                    type="submit"
                    class="register-button">

                    Daftar

                </button>

            </form>


            <!-- LOGIN -->

            <div class="login-link">

                Sudah punya akun?

                <a href="{{ route('login') }}">
                    Masuk
                </a>

            </div>


            <!-- FOOTER -->

            <div class="register-footer">

                © {{ date('Y') }} KasirKu

            </div>

        </div>

    </div>

</body>

</html>