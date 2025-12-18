<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - UKM PAGARNUSA UNUSA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #004225;
            --primary-light: #0a5a35;
            --accent: #FFD700;
            --white: #ffffff;
            --bg-gray: #f8fafc;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --radius: 16px;
            --shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --font: 'Plus Jakarta Sans', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--font);
            margin: 0;
            background: var(--bg-gray);
            /* Background decoration matching previous dashboard style */
            background-image: 
                radial-gradient(900px 420px at 12% 0%, rgba(0, 66, 37, 0.08), transparent 60%),
                radial-gradient(700px 360px at 95% 10%, rgba(255, 215, 0, 0.12), transparent 60%);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* --- Header --- */
        .header-top {
            background: var(--white);
            padding: 15px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-bottom: 4px solid var(--primary);
        }
        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
            max-width: 1200px;
            width: 100%;
        }
        .header-content img { height: 45px; }
        .header-content span {
            font-weight: 900;
            font-size: 1.2rem;
            color: var(--primary);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* --- Login Container --- */
        .login-wrapper {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .login-card {
            background: var(--white);
            width: 100%;
            max-width: 950px;
            display: flex;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Left Side (Aesthetics) */
        .login-brand-side {
            flex: 1;
            background: linear-gradient(145deg, var(--primary), var(--primary-light));
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: var(--white);
            position: relative;
        }

        /* Decorative circles */
        .login-brand-side::before {
            content: "";
            position: absolute;
            top: -20%;
            left: -20%;
            width: 300px;
            height: 300px;
            background: rgba(255, 215, 0, 0.1);
            border-radius: 50%;
        }

        .pagarnusa-logo {
            width: 140px;
            height: auto;
            margin-bottom: 25px;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));
            z-index: 1;
        }

        .login-brand-side h2 {
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--accent);
            margin: 0 0 15px;
            letter-spacing: -0.5px;
            z-index: 1;
        }

        .login-brand-side p {
            font-size: 1.05rem;
            opacity: 0.9;
            line-height: 1.6;
            margin: 0;
            z-index: 1;
        }

        .welcome-tag {
            margin-top: 40px;
            padding: 12px 25px;
            background: rgba(255, 215, 0, 0.15);
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 999px;
            color: var(--accent);
            font-weight: 800;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            z-index: 1;
        }

        /* Right Side (Form) */
        .login-form-side {
            flex: 1.2;
            padding: 50px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
        }

        .login-form-side h3 {
            font-size: 1.75rem;
            color: var(--text-dark);
            font-weight: 800;
            margin: 0 0 10px;
            text-align: center;
        }

        .login-form-side .sub-h3 {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 35px;
            font-size: 0.95rem;
        }

        .form-group { margin-bottom: 22px; position: relative; }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-icon-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 15px;
            color: var(--text-muted);
        }

        .form-group input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: var(--font);
            font-size: 1rem;
            background: #fdfdfd;
            transition: all 0.2s ease;
            color: var(--text-dark);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-light);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(0, 66, 37, 0.08);
        }

        /* Buttons */
        .btn-masuk {
            background: var(--primary);
            color: var(--white);
            padding: 16px;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1rem;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(0, 66, 37, 0.3);
        }

        .btn-masuk:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 15px 20px -5px rgba(0, 66, 37, 0.4);
        }

        .btn-back-home {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: var(--primary);
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            margin-top: 25px;
            transition: color 0.2s;
        }

        .btn-back-home:hover { color: var(--primary-light); }

        /* Alerts */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid transparent;
        }
        .alert-success { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border-color: #fecaca; }

        /* Footer */
        .footer {
            padding: 25px;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            border-top: 1px solid rgba(0,0,0,0.05);
            background: var(--white);
        }

        @media (max-width: 850px) {
            .login-card { flex-direction: column; max-width: 450px; }
            .login-brand-side { padding: 40px 30px; }
            .login-form-side { padding: 40px 30px; }
            .header-top { padding: 15px 20px; }
        }
    </style>
</head>
<body>

    <div class="header-top">
        <div class="header-content">
            <img src="{{ asset('assets/images/PN UNUSA.png') }}" alt="Logo PN UNUSA">
            <span>UKM Pagar Nusa UNUSA</span>
        </div>
    </div>

    <div class="login-wrapper">
        <div class="login-card">
            
            <div class="login-brand-side">
                <img src="{{ asset('assets/images/PN UNUSA.png') }}" alt="Logo PN UNUSA" class="pagarnusa-logo">
                <h2>Selamat Datang</h2>
                <p>Gunakan akun SIP (Sistem Informasi Pagarnusa) untuk mengelola data anggota, absensi, dan kas UKM.</p>
                <div class="welcome-tag">Sistem Informasi Manajemen <br> UKM PAGAR NUSA UNUSA</div>
            </div>

            <div class="login-form-side">
                <h3>Login Akun</h3>
                <p class="sub-h3">Silakan masukkan kredensial Anda</p>

                {{-- NOTIFIKASI SUKSES --}}
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif
                
                {{-- NOTIFIKASI GAGAL --}}
                @if (session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        {{ session('error') }}
                    </div>
                @endif
                
                <form action="{{ route('login') }}" method="POST">
                    @csrf 
                    
                    <div class="form-group">
                        <label for="username">Email / Username</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username" id="username" placeholder="Email atau username" autocomplete="off" required>
                        </div>
                        @error('username') 
                            <div style="color: var(--danger); font-size: 0.75rem; margin-top: 5px; font-weight: 600;">
                                <i class="fas fa-info-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="••••••••" autocomplete="off" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-masuk">Masuk Sekarang</button>
                    
                </form>
                
                <a href="{{ url('/landing') }}" class="btn-back-home">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Beranda
                </a>
            </div>
            
        </div>
    </div>
    
    <div class="footer">
        <div>&copy; 2023 <strong>UKM PAGARNUSA UNUSA</strong>. All rights reserved.</div>
        <div style="margin-top: 5px;">Hubungi kami: <a href="mailto:info@pagarnusa.id" style="color: var(--primary); text-decoration: none; font-weight: 600;">info@pagarnusa.id</a></div>
    </div>

</body>
</html>
