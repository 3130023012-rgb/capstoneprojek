<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UKM PENCAK SILAT PAGARNUSA UNUSA</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> 

    <style>
        :root {
            --primary: #004225;
            --primary-light: #0a5a35;
            --accent: #FFD700;
            --white: #fff;
            --bg-light: #f6faf7;
            --text-dark: #0f172a;
            --muted: #64748b;
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            --font: 'Plus Jakarta Sans', sans-serif;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body { 
            font-family: var(--font); 
            margin: 0; 
            background-color: var(--white); 
            color: var(--text-dark); 
            line-height: 1.6;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* --- Navbar --- */
        .navbar { 
            background-color: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #eee; 
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .navbar-content { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; }
        .logo { font-size: 1.1em; font-weight: 900; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .logo img { height: 40px; }
        
        .nav-links { display: flex; align-items: center; gap: 25px; }
        .nav-links a { text-decoration: none; color: var(--text-dark); font-size: 0.95em; font-weight: 700; transition: 0.2s; }
        .nav-links a:hover { color: var(--primary); }
        
        .btn-login {
            background-color: var(--primary);
            color: var(--white) !important;
            padding: 10px 22px;
            border-radius: 999px;
            box-shadow: 0 4px 12px rgba(0, 66, 37, 0.2);
        }
        .btn-login:hover { transform: translateY(-2px); filter: brightness(1.2); }

        /* --- Hero Section --- */
        .hero-section {
            background-color: var(--primary); 
            color: var(--white);
            padding: 100px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url('{{ asset("assets/images/background.jpg") }}'); 
            background-size: cover;
            background-position: center;
            opacity: 0.1;
        }
        .hero-content { position: relative; z-index: 10; display: flex; flex-direction: column; align-items: center; }
        .hero-logo-img { width: 140px; height: 140px; margin-bottom: 25px; filter: drop-shadow(0 0 20px rgba(0,0,0,0.3)); }
        
        .hero-text h1 { font-size: clamp(2rem, 5vw, 3.5rem); margin: 0; font-weight: 900; line-height: 1.1; }
        .hero-text p { font-size: 1.2rem; opacity: 0.9; max-width: 800px; margin: 25px auto 40px; font-weight: 500; }
        
        .hero-buttons { display: flex; gap: 15px; justify-content: center; }
        .hero-buttons a {
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 800;
            transition: 0.3s;
        }
        .btn-primary-hero { background-color: var(--accent); color: var(--primary); box-shadow: 0 8px 20px rgba(255, 215, 0, 0.3); }
        .btn-secondary-hero { background-color: transparent; color: var(--white); border: 2px solid var(--white); }
        .hero-buttons a:hover { transform: translateY(-3px); filter: brightness(1.05); }

        /* --- Section Titles --- */
        section { padding: 90px 0; }
        .section-title { text-align: center; margin-bottom: 60px; }
        .section-title h2 { font-size: 2.5rem; font-weight: 900; color: var(--primary); margin: 0; position: relative; display: inline-block; }
        .section-title h2::after { content: ''; display: block; width: 60%; height: 5px; background: var(--accent); margin: 10px auto; border-radius: 999px; }

        /* --- About --- */
        .about-text { max-width: 900px; margin: 0 auto 50px; text-align: center; font-size: 1.1rem; font-weight: 500; color: var(--muted); }
        .vision-mission-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; }
        .vm-card { 
            padding: 35px; border-radius: 20px; 
            background: var(--bg-light); 
            border: 1px solid rgba(0,66,37,0.05);
            transition: 0.3s;
        }
        .vm-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
        .vm-card h3 { font-size: 1.5rem; color: var(--primary); margin-top: 0; font-weight: 800; display: flex; align-items: center; gap: 10px; }
        .vm-card h3 i { color: var(--accent); }

        /* --- Schedule --- */
        .schedule-section { background-color: var(--bg-light); }
        .schedule-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; }
        .schedule-card { 
            background: var(--white); border-radius: 20px; padding: 25px; 
            box-shadow: var(--shadow-md); border-top: 6px solid var(--primary);
        }
        .schedule-card h4 { font-size: 1.5rem; margin: 0 0 15px; font-weight: 800; color: var(--primary); }
        .schedule-item { display: flex; padding: 12px 0; border-bottom: 1px dashed #eee; align-items: center; gap: 10px; }
        .schedule-item:last-child { border: none; }
        .schedule-item i { color: var(--primary-light); width: 20px; }
        .schedule-item strong { width: 70px; color: var(--muted); }

        /* --- Gallery --- */
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .photo-item { 
            height: 280px; border-radius: 15px; overflow: hidden; 
            position: relative; cursor: pointer; box-shadow: var(--shadow-md);
        }
        .photo-item img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .photo-item:hover img { transform: scale(1.1); }
        .photo-caption { 
            position: absolute; bottom: 0; inset: auto 0 0 0; 
            padding: 20px; background: linear-gradient(transparent, rgba(0,0,0,0.8)); color: white; 
        }
        .photo-caption h4 { margin: 0; font-size: 1.1rem; }

        /* --- Contact & Footer --- */
        .contact-section { background-color: var(--primary); color: var(--white); padding: 70px 0; }
        .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
        .contact-info h2 { font-size: 2.5rem; font-weight: 900; color: var(--accent); margin-bottom: 20px; }
        .contact-links { display: flex; flex-direction: column; gap: 15px; }
        .contact-links a { color: var(--white); text-decoration: none; display: flex; align-items: center; gap: 12px; font-size: 1.1rem; transition: 0.2s; }
        .contact-links a:hover { color: var(--accent); padding-left: 5px; }

        .footer { padding: 30px 0; background-color: #002b18; color: rgba(255,255,255,0.6); font-size: 0.9em; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); }

        /* --- Modal Lightbox --- */
        #lightbox-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 2000; padding: 40px; }
        #lightbox-img { max-width: 100%; max-height: 100%; margin: auto; display: block; border-radius: 10px; box-shadow: 0 0 30px rgba(0,0,0,0.5); }
        .close-btn { position: absolute; top: 20px; right: 30px; color: white; font-size: 40px; cursor: pointer; }

        @media (max-width: 768px) {
            .nav-links:not(.btn-login) { display: none; }
            .contact-grid, .vision-mission-grid { grid-template-columns: 1fr; gap: 40px; }
            .hero-text h1 { font-size: 2.2rem; }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container navbar-content">
            <div class="logo">
                <img src="{{ asset('assets/images/PN UNUSA.png') }}" alt="Logo">
                UKM PAGARNUSA UNUSA
            </div>
            <div class="nav-links">
                <a href="#beranda">Beranda</a>
                <a href="#tentang">Tentang</a>
                <a href="#jadwal">Jadwal</a>
                <a href="#galeri">Galeri</a>
                <a href="#kontak">Kontak</a>
                <a href="{{ route('login') }}" class="btn-login">Login</a>
            </div>
        </div>
    </nav>

    <header class="hero-section" id="beranda">
        <div class="container hero-content">
            <img src="{{ asset('assets/images/PN UNUSA.png') }}" alt="Logo" class="hero-logo-img"> 
            <div class="hero-text">
                <h1>Selamat Datang Di UKM Pencak Silat<br>PAGARNUSA UNUSA</h1>
                <p>{{ $settings['hero_description'] ?? 'Mencetak generasi yang unggul dalam prestasi, tangguh dalam bela diri, dan berkarakter Islami dalam bingkai Nahdlatul Ulama.' }}</p>
                <div class="hero-buttons">
                    <a href="#galeri" class="btn-primary-hero">Lihat Galeri</a>
                    <a href="#kontak" class="btn-secondary-hero">Hubungi Kami</a>
                </div>
            </div>
        </div>
    </header>

    <section id="tentang">
        <div class="container">
            <div class="section-title">
                <h2>Tentang Kami</h2>
            </div>
            <div class="about-text">
                <p>Unit Kegiatan Mahasiswa (UKM) Pencak Silat Pagar Nusa UNUSA adalah wadah pelestarian seni bela diri tradisional di lingkungan Universitas Nahdlatul Ulama Surabaya. Diresmikan pada 09 April 2019, kami berfokus pada pembinaan fisik, mental, dan spiritual untuk menciptakan mahasiswa yang cerdas dan berakhlak mulia.</p>
            </div>
            
            <div class="vision-mission-grid">
                <div class="vm-card">
                    <h3><i class="fas fa-eye"></i> Visi Kami</h3>
                    <p>{{ $settings['visi'] ?? 'Menjadi UKM pencak silat terbaik di lingkungan UNUSA yang melahirkan atlet berakhlak mulia.' }}</p>
                </div>
                <div class="vm-card">
                    <h3><i class="fas fa-bullseye"></i> Misi Kami</h3>
                    <p>{!! nl2br(e($settings['misi'] ?? "Mengadakan latihan rutin.\nMengembangkan potensi seni bela diri.\nMenanamkan nilai-nilai ke-NU-an.")) !!}</p>
                </div>
            </div>
        </div>
    </section>

    <section style="background-color: var(--bg-light)">
        <div class="container">
            <div class="section-title">
                <h2>Sejarah Kami</h2>
            </div>
            <div style="background: var(--white); padding: 40px; border-radius: 25px; box-shadow: var(--shadow-md); text-align: center;">
                <p style="font-size: 1.1rem; color: var(--muted);">{{ $settings['sejarah'] ?? 'PAGARNUSA UNUSA didirikan untuk mewadahi minat mahasiswa terhadap pencak silat sebagai sarana dakwah dan pembinaan karakter.' }}</p>
            </div>
        </div>
    </section>

    <section class="schedule-section" id="jadwal">
        <div class="container">
            <div class="section-title">
                <h2>Jadwal Latihan</h2>
            </div>
            
            @if(isset($schedules) && $schedules->isNotEmpty())
                <div class="schedule-grid">
                    @foreach($schedules as $schedule)
                        <div class="schedule-card">
                            @if ($schedule->proof_photo)
                                <div class="photo-item" style="height: 180px; margin-bottom: 20px;" 
                                     data-src="{{ asset($schedule->proof_photo) }}" onclick="openLightbox(this)">
                                    <img src="{{ asset($schedule->proof_photo) }}" alt="Bukti">
                                </div>
                            @endif
                            <h4>{{ $schedule->name }}</h4> 
                            <div class="schedule-item"><i class="fas fa-calendar-day"></i> <strong>Hari:</strong> {{ $schedule->day_of_week }}</div>
                            <div class="schedule-item"><i class="fas fa-clock"></i> <strong>Waktu:</strong> {{ date('H:i', strtotime($schedule->time)) }} WIB</div>
                            <div class="schedule-item"><i class="fas fa-map-marker-alt"></i> <strong>Lokasi:</strong> {{ $schedule->location }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="text-align: center; color: var(--muted);">Belum ada jadwal latihan tersedia.</p>
            @endif
        </div>
    </section>

    <section id="galeri">
        <div class="container">
            <div class="section-title">
                <h2>Galeri Kegiatan</h2>
            </div>
            <div class="gallery-grid">
                @forelse($photos ?? [] as $photo)
                    <div class="photo-item" data-src="{{ asset($photo->file_name) }}" onclick="openLightbox(this)">
                        <img src="{{ asset($photo->file_name) }}" alt="{{ $photo->title }}">
                        <div class="photo-caption">
                            <h4>{{ $photo->title }}</h4>
                        </div>
                    </div>
                @empty
                    <p style="grid-column: 1/-1; text-align: center; color: var(--muted);">Belum ada dokumentasi galeri.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="contact-section" id="kontak">
        <div class="container contact-grid">
            <div class="contact-info">
                <h2>Kontak Kami</h2>
                <p>Ingin bergabung atau memiliki pertanyaan terkait UKM Pagar Nusa UNUSA? Hubungi kami melalui kanal di samping.</p>
            </div>
            <div class="contact-links">
                <a href="mailto:{{ $settings['kontak_email'] ?? '#' }}"><i class="fas fa-envelope"></i> {{ $settings['kontak_email'] ?? 'pagarnusa.unusa@gmail.com' }}</a>
                <a href="tel:{{ $settings['kontak_telepon'] ?? '#' }}"><i class="fas fa-phone"></i> {{ $settings['kontak_telepon'] ?? '+62 8xx xxxx xxxx' }}</a>
                <a href="#"><i class="fab fa-instagram"></i> @pagarnusa_unusa</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} UKM PAGARNUSA UNUSA. All rights reserved.</p>
        </div>
    </footer>

    <div id="lightbox-modal" onclick="this.style.display='none'">
        <span class="close-btn">&times;</span>
        <img id="lightbox-img" src="" alt="Zoom">
    </div>
    
    <script>
        function openLightbox(element) {
            const modal = document.getElementById('lightbox-modal');
            const modalImg = document.getElementById('lightbox-img');
            modal.style.display = 'flex';
            modalImg.src = element.getAttribute('data-src');
        }
    </script>
</body>
</html>