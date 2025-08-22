<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lunasin - Aplikasi Manajemen Keuangan Modern</title>
    <meta name="description" content="Lunasin adalah aplikasi web manajemen keuangan untuk individu, freelancer, dan usaha kecil. Kelola keuangan dengan mudah, cepat, dan intuitif.">
    <meta name="keywords" content="manajemen keuangan, aplikasi keuangan, budgeting, freelancer, usaha kecil, pencatatan transaksi, dashboard keuangan, laporan keuangan">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.0/cdn.js" defer></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            overflow-x: hidden;
        }

        /* Custom Properties */
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #10b981;
            --accent-color: #f59e0b;
            --text-light: #6b7280;
            --bg-light: #f9fafb;
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --shadow-soft: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-slideInLeft {
            animation: slideInLeft 0.8s ease-out forwards;
        }

        .animate-slideInRight {
            animation: slideInRight 0.8s ease-out forwards;
        }

        .animate-bounce {
            animation: bounce 2s infinite;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-light);
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .cta-button {
            background: var(--gradient-1);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px -10px rgba(102, 102, 241, 0.3);
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-color);
            cursor: pointer;
        }

        .mobile-menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            z-index: 999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        .mobile-menu a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 1.2rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .mobile-menu a:hover {
            color: var(--primary-color);
        }

        .mobile-close-btn {
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary-color);
            cursor: pointer;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='7' cy='7' r='7'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
        }

        .hero-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-content p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: white;
            color: var(--primary-color);
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: var(--shadow-soft);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            padding: 1rem 2rem;
            border: 2px solid white;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: white;
            color: var(--primary-color);
        }

        .hero-image {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-mockup {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0, 0, 0, 0.2));
            animation: bounce 6s ease-in-out infinite;
        }

        /* Features Section */
        .features {
            padding: 5rem 2rem;
            background: var(--bg-light);
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -10px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient-1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1a1a1a;
        }

        .feature-card p {
            color: var(--text-light);
            line-height: 1.6;
        }

        /* Target Audience */
        .audience {
            padding: 5rem 2rem;
            background: white;
        }

        .audience-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .audience-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .audience-card {
            background: var(--bg-light);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .audience-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-1);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .audience-card:hover::before {
            transform: translateX(0);
        }

        .audience-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-soft);
        }

        .audience-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        /* Use Cases */
        .use-cases {
            padding: 5rem 2rem;
            background: var(--bg-light);
        }

        .use-cases-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .use-case {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
        }

        .use-case:hover {
            transform: translateX(10px);
        }

        .use-case-number {
            width: 40px;
            height: 40px;
            background: var(--gradient-1);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            float: left;
            margin-right: 1rem;
            margin-bottom: 1rem;
        }

        /* CTA Section */
        .cta-section {
            background: var(--gradient-1);
            padding: 5rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='20' cy='20' r='2'/%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .cta-content {
            position: relative;
            z-index: 1;
        }

        .cta-section h2 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .cta-section p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        .footer {
            background: #1a1a1a;
            color: white;
            padding: 3rem 2rem 1rem;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .hero-container {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2rem;
                padding: 1rem;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .section-title h2 {
                font-size: 2rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .audience-grid {
                grid-template-columns: 1fr;
            }

            .hero-buttons {
                justify-content: center;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .cta-section h2 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .feature-card, .audience-card, .use-case {
                padding: 1.5rem;
            }

            .nav-container {
                padding: 1rem;
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Intersection Observer Classes */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header" x-data="{ mobileMenuOpen: false }">
        <nav class="nav-container">
            <div class="logo">Lunasin</div>

            <ul class="nav-menu">
                <li><a href="#home" class="nav-link">Beranda</a></li>
                <li><a href="#fitur" class="nav-link">Fitur</a></li>
                <li><a href="#target" class="nav-link">Target</a></li>
                <li><a href="#kasus" class="nav-link">Kasus Penggunaan</a></li>
            </ul>

            <a href="#daftar" class="cta-button">Mulai Gratis</a>

            <button class="mobile-menu-btn" @click="mobileMenuOpen = true">
                <i class="fas fa-bars"></i>
            </button>
        </nav>

        <!-- Mobile Menu -->
        <div class="mobile-menu" :class="{ 'active': mobileMenuOpen }" x-show="mobileMenuOpen" x-transition>
            <button class="mobile-close-btn" @click="mobileMenuOpen = false">
                <i class="fas fa-times"></i>
            </button>
            <a href="#home" @click="mobileMenuOpen = false">Beranda</a>
            <a href="#fitur" @click="mobileMenuOpen = false">Fitur</a>
            <a href="#target" @click="mobileMenuOpen = false">Target</a>
            <a href="#kasus" @click="mobileMenuOpen = false">Kasus Penggunaan</a>
            <a href="#daftar" class="btn-primary" @click="mobileMenuOpen = false" style="margin-top: 1rem;">
                <i class="fas fa-rocket"></i>
                Mulai Gratis
            </a>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-container">
            <div class="hero-content animate-slideInLeft">
                <h1>Kelola Keuangan dengan Mudah & Intuitif</h1>
                <p>Lunasin adalah aplikasi web manajemen keuangan modern yang membantu individu, freelancer, dan usaha kecil untuk mencatat, mengelola, dan menganalisis aktivitas keuangan dengan cara yang cepat dan efisien.</p>
                <div class="hero-buttons">
                    <a href="#daftar" class="btn-primary">
                        <i class="fas fa-rocket"></i>
                        Mulai Sekarang
                    </a>
                    <a href="#fitur" class="btn-secondary">Lihat Fitur</a>
                </div>
            </div>
            <div class="hero-image animate-slideInRight">
                <div class="hero-mockup">
                    <i class="fas fa-mobile-alt" style="font-size: 300px; color: rgba(255,255,255,0.9);"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features fade-in" id="fitur">
        <div class="features-container">
            <div class="section-title">
                <h2>Fitur Lengkap untuk Manajemen Keuangan</h2>
                <p>Dilengkapi dengan semua tools yang Anda butuhkan untuk mengelola keuangan dengan efektif</p>
            </div>

            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <h3>Dashboard Interaktif</h3>
                    <p>Lihat ringkasan keuangan real-time dengan grafik tren dan visualisasi data yang mudah dipahami</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h3>Pencatatan Transaksi</h3>
                    <p>Catat pemasukan dan pengeluaran dengan formulir intuitif, lengkap dengan kategori dan bukti digital</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3>Kategori Fleksibel</h3>
                    <p>Buat dan kelola kategori transaksi sesuai kebutuhan untuk organisasi data yang lebih baik</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h3>Lampiran Bukti</h3>
                    <p>Upload foto struk, invoice, dan bukti transaksi untuk dokumentasi yang lengkap</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Laporan & Analisis</h3>
                    <p>Dapatkan insight mendalam dengan laporan periodik dan filter pencarian lanjutan</p>
                </div>

                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <h3>Ekspor Data</h3>
                    <p>Export laporan ke format PDF atau Excel untuk analisis lebih lanjut atau keperluan arsip</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Target Audience -->
    <section class="audience fade-in" id="target">
        <div class="audience-container">
            <div class="section-title">
                <h2>Dibuat untuk Semua Kalangan</h2>
                <p>Lunasin dirancang khusus untuk memenuhi kebutuhan berbagai profesi dan gaya hidup</p>
            </div>

            <div class="audience-grid">
                <div class="audience-card fade-in">
                    <div class="audience-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3>Individu</h3>
                    <p>Untuk siapa saja yang ingin melacak pemasukan dan pengeluaran pribadi untuk tujuan budgeting dan perencanaan keuangan yang lebih baik.</p>
                </div>

                <div class="audience-card fade-in">
                    <div class="audience-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h3>Freelancer</h3>
                    <p>Profesional yang perlu memisahkan dan melacak pendapatan proyek serta pengeluaran bisnis dengan sistem yang terorganisir.</p>
                </div>

                <div class="audience-card fade-in">
                    <div class="audience-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3>Usaha Kecil</h3>
                    <p>Pemilik usaha yang membutuhkan alat sederhana untuk pembukuan dasar tanpa kompleksitas software akuntansi skala besar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Use Cases -->
    <section class="use-cases fade-in" id="kasus">
        <div class="use-cases-container">
            <div class="section-title">
                <h2>Contoh Penggunaan Sehari-hari</h2>
                <p>Lihat bagaimana Lunasin membantu dalam aktivitas keuangan harian Anda</p>
            </div>

            <div class="use-case fade-in">
                <div class="use-case-number">1</div>
                <h3>Mencatat Pengeluaran Harian</h3>
                <p>Beli kopi seharga Rp25.000? Buka Lunasin, pilih "Pengeluaran", isi nominal dan kategori "Makanan & Minuman", tambahkan foto struk, dan transaksi langsung tersimpan di dashboard Anda.</p>
            </div>

            <div class="use-case fade-in">
                <div class="use-case-number">2</div>
                <h3>Review Keuangan Bulanan</h3>
                <p>Di akhir bulan, masuk ke menu "Laporan", set filter tanggal untuk bulan yang diinginkan, dan lihat total pemasukan dari semua klien serta breakdown pengeluaran per kategori. Export ke PDF untuk arsip.</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section" id="daftar">
        <div class="cta-content">
            <h2 class="animate-fadeInUp">Siap Mengontrol Keuangan Anda?</h2>
            <p class="animate-fadeInUp">Bergabunglah dengan ribuan pengguna yang sudah merasakan kemudahan mengelola keuangan dengan Lunasin</p>
            <a href="#" class="btn-primary animate-fadeInUp">
                <i class="fas fa-rocket"></i>
                Daftar Gratis Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="#home">Beranda</a>
                <a href="#fitur">Fitur</a>
                <a href="#target">Target Pengguna</a>
                <a href="#">Bantuan</a>
                <a href="#">Kontak</a>
                <a href="#">Kebijakan Privasi</a>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Lunasin. Semua hak dilindungi. Aplikasi Manajemen Keuangan Modern.</p>
            </div>
        </div>
    </footer>

    <script>
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe all fade-in elements
        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
                header.style.boxShadow = '0 2px 20px rgba(0, 0, 0, 0.1)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
                header.style.boxShadow = 'none';
            }
        });

        // Add loading animation
        window.addEventListener('load', () => {
            document.body.style.opacity = '1';
            document.body.style.transform = 'translateY(0)';
        });
    </script>
</body>
</html>
