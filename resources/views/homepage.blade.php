<!DOCTYPE html>
<html lang="id">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ env('APP_NAME') }}</title>
  <meta name="description" content="{{ env('APP_NAME') }} aplikasi utang-piutang simpel dan gratis. Catat, kelola, dan pantau transaksi pribadi atau bisnis dengan mudah.">
  <meta name="keywords" content="aplikasi utang piutang, catat utang piutang, utang piutang pribadi, utang piutang bisnis, aplikasi gratis utang piutang, aplikasi sederhana untuk utang piutang, kelola utang piutang mudah, pencatatan utang piutang online, aplikasi keuangan pribadi gratis, aplikasi pencatatan keuangan kecil">
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  @vite([])
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loader"></div>
    </div>

    <!-- Header -->
    <header class="header navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand animate-slide-top" href="#">
                <h2 class="mb-0 text-primary">Lunasin</h2>
            </a>

            <button class="navbar-toggler animate-slide-top delay-100" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <nav class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item animate-slide-top delay-200">
                        <a class="nav-link active" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item animate-slide-top delay-300">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item animate-slide-top delay-400">
                        <a class="nav-link" href="#benefits">Manfaat</a>
                    </li>
                    <li class="nav-item animate-slide-top delay-500">
                        <a class="nav-link" href="#testimonials">Testimoni</a>
                    </li>
                    <li class="nav-item animate-slide-top delay-600">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                </ul>

                <div class="ms-lg-3 mt-3 mt-lg-0 d-flex animate-slide-top delay-700">
                    <a href="#" class="btn btn-outline-primary me-2">Masuk</a>
                    <a href="#" class="btn btn-primary">Daftar</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="display-4 fw-bold mb-4 animate-fade-left">Kelola Utang Piutang dengan Mudah dan Efisien</h1>
                    <p class="lead mb-4 animate-fade-left delay-200">Lunasin membantu Anda mencatat, memantau, dan mengelola semua transaksi utang
                        piutang dalam satu aplikasi sederhana. Nikmati pengelolaan keuangan yang lebih terorganisir
                        tanpa ribet.</p>
                    <div class="hero-buttons animate-fade-left delay-400">
                        <a href="#" class="btn btn-primary btn-lg">Mulai Sekarang</a>
                        <a href="#features" class="btn btn-outline-primary btn-lg">Pelajari Fitur</a>
                    </div>
                </div>
                <div class="col-lg-6 animate-fade-right delay-300">
                    <div class="text-center float-animation">
                        <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; box-shadow: 0 15px 35px rgba(67, 97, 238, 0.3);">
                            Dashboard Preview
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features" style="background-color: #f8f9fa;">
        <div class="container py-5">
            <div class="section-title animate-fade-up">
                <h2>Fitur Unggulan Lunasin</h2>
                <p class="lead">Dirancang khusus untuk memudahkan pengelolaan utang piutang Anda</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4 animate-scale-in delay-100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-journal-plus"></i>
                        </div>
                        <h4>Pencatatan Mudah</h4>
                        <p>Catat transaksi utang piutang dengan cepat melalui antarmuka yang intuitif dan user-friendly.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 animate-scale-in delay-200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-bell"></i>
                        </div>
                        <h4>Pengingat Otomatis</h4>
                        <p>Dapatkan notifikasi untuk jatuh tempo pembayaran sehingga Anda tidak pernah melewatkan
                            tanggal penting.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 animate-scale-in delay-300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h4>Laporan Keuangan</h4>
                        <p>Analisis kondisi keuangan Anda dengan laporan visual yang mudah dipahami.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 animate-scale-in delay-400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4>Multi-User</h4>
                        <p>Kelola utang piutang bersama tim atau keluarga dengan akses multi-user yang aman.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 animate-scale-in delay-500">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h4>Keamanan Data</h4>
                        <p>Data keuangan Anda terlindungi dengan enkripsi tingkat tinggi dan backup otomatis.</p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 animate-scale-in delay-600">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-cloud-arrow-down"></i>
                        </div>
                        <h4>Sync Multi-Device</h4>
                        <p>Akses data Anda kapan saja dari perangkat mana pun dengan sinkronisasi cloud yang handal.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section" id="benefits">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 animate-fade-right">
                    <div class="benefits-image">
                        <div style="width: 100%; height: 450px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center; color: #6c757d; font-size: 1.5rem;">
                            Benefits Illustration
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="benefits-content animate-fade-left delay-200">
                        <div class="section-title">
                            <h2>Mengapa Memilih Lunasin?</h2>
                            <p>Solusi tepat untuk mengelola keuangan Anda</p>
                        </div>

                        <div class="benefit-item animate-fade-up delay-300">
                            <div class="benefit-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="benefit-content">
                                <h5>Hemat Waktu</h5>
                                <p>Kelola semua catatan utang piutang dalam satu tempat tanpa perlu spreadsheet rumit. Proses pencatatan yang cepat dan efisien menghemat waktu berharga Anda.</p>
                            </div>
                        </div>

                        <div class="benefit-item animate-fade-up delay-400">
                            <div class="benefit-icon">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="benefit-content">
                                <h5>Hindari Kerugian</h5>
                                <p>Pengingat otomatis membantu Anda tidak melewatkan tanggal jatuh tempo pembayaran. Sistem notifikasi yang cerdas melindungi cash flow bisnis Anda.</p>
                            </div>
                        </div>

                        <div class="benefit-item animate-fade-up delay-500">
                            <div class="benefit-icon">
                                <i class="bi bi-bar-chart"></i>
                            </div>
                            <div class="benefit-content">
                                <h5>Keputusan Tepat</h5>
                                <p>Laporan keuangan detail dan dashboard analitik membantu Anda membuat keputusan finansial yang lebih baik berdasarkan data akurat.</p>
                            </div>
                        </div>

                        <div class="benefit-item animate-fade-up delay-600">
                            <div class="benefit-icon">
                                <i class="bi bi-heart"></i>
                            </div>
                            <div class="benefit-content">
                                <h5>Tenang dan Fokus</h5>
                                <p>Tidak perlu lagi mengingat semua utang piutang secara manual. Fokus pada pengembangan bisnis sementara Lunasin mengatur keuangan Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5 bg-light" id="testimonials">
        <div class="container py-5">
            <div class="section-title animate-fade-up">
                <h2>Apa Kata Pengguna Lunasin?</h2>
                <p class="lead">Testimoni dari mereka yang telah merasakan manfaat Lunasin</p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 animate-scale-in delay-200">
                    <div class="testimonial-card">
                        <p>"Sejak menggunakan Lunasin, saya tidak pernah lagi lupa dengan utang piutang. Aplikasinya
                            sangat mudah digunakan dan notifikasinya sangat membantu untuk mengingatkan jatuh tempo."</p>
                        <div class="testimonial-author">
                            <div class="avatar">BS</div>
                            <div>
                                <h6 class="mb-0">Budi Santoso</h6>
                                <small>Pemilik Bisnis UKM</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 animate-scale-in delay-300">
                    <div class="testimonial-card">
                        <p>"Sebagai ibu rumah tangga, Lunasin sangat membantu saya mengatur keuangan keluarga. Sekarang
                            tidak ada lagi utang yang terlewat atau terlupa. Interface-nya juga sangat user-friendly."</p>
                        <div class="testimonial-author">
                            <div class="avatar">SI</div>
                            <div>
                                <h6 class="mb-0">Sari Indah</h6>
                                <small>Ibu Rumah Tangga</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 animate-scale-in delay-400">
                    <div class="testimonial-card">
                        <p>"Aplikasi debt tracking terbaik yang pernah saya coba. Fiturnya lengkap, tampilannya clean,
                            dan yang paling penting aman untuk data keuangan saya. Highly recommended!"</p>
                        <div class="testimonial-author">
                            <div class="avatar">RP</div>
                            <div>
                                <h6 class="mb-0">Rizky Pratama</h6>
                                <small>Freelancer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-5" id="faq">
        <div class="container py-5">
            <div class="section-title animate-fade-up">
                <h2>Pertanyaan Umum</h2>
                <p class="lead">Temukan jawaban atas pertanyaan yang sering diajukan</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-item animate-fade-up delay-200">
                        <div class="faq-question">
                            <span>Apakah Lunasin benar-benar gratis?</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Ya, Lunasin dapat digunakan secara gratis dengan fitur-fitur dasar yang cukup untuk
                                mengelola utang piutang sehari-hari. Kami juga menyediakan paket premium dengan fitur
                                tambahan untuk kebutuhan yang lebih kompleks seperti laporan advanced dan integrasi dengan sistem akuntansi.</p>
                        </div>
                    </div>

                    <div class="faq-item animate-fade-up delay-300">
                        <div class="faq-question">
                            <span>Bagaimana keamanan data saya dijamin?</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Keamanan data adalah prioritas utama kami. Semua data dienkripsi menggunakan standar industri (AES-256) dan disimpan dengan protokol keamanan tinggi. Kami juga melakukan backup otomatis dan tidak akan pernah membagikan data Anda kepada pihak ketiga tanpa izin eksplisit.</p>
                        </div>
                    </div>

                    <div class="faq-item animate-fade-up delay-400">
                        <div class="faq-question">
                            <span>Apakah data saya bisa diakses dari banyak perangkat?</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Tentu saja! Lunasin menyinkronkan data Anda secara real-time di semua perangkat. Anda dapat mengakses dan mengelola data dari smartphone Android/iOS, tablet, atau komputer desktop kapan saja dan di mana saja dengan koneksi internet.</p>
                        </div>
                    </div>

                    <div class="faq-item animate-fade-up delay-500">
                        <div class="faq-question">
                            <span>Bagaimana cara membuat dan mengunduh laporan keuangan?</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Lunasin secara otomatis menghasilkan laporan keuangan berdasarkan data transaksi yang Anda input. Anda dapat melihat laporan harian, mingguan, bulanan, atau periode custom. Laporan dapat diunduh dalam format PDF atau Excel untuk keperluan dokumentasi atau presentasi.</p>
                        </div>
                    </div>

                    <div class="faq-item animate-fade-up delay-600">
                        <div class="faq-question">
                            <span>Apakah ada dukungan customer service?</span>
                            <i class="bi bi-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Ya, kami menyediakan dukungan customer service melalui berbagai channel: live chat di aplikasi, email support, dan knowledge base yang lengkap. Tim support kami siap membantu Anda dari Senin-Jumat pukul 09:00-18:00 WIB.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <div class="cta-content animate-fade-up">
                <h2 class="text-white mb-4">Siap Mengelola Utang Piutang dengan Lebih Baik?</h2>
                <p class="lead text-white mb-4">Bergabung dengan ribuan pengguna yang telah mempercayakan pengelolaan
                    keuangan mereka kepada Lunasin. Mulai gratis hari ini!</p>
                <div class="animate-scale-in delay-300">
                    <a href="#" class="btn btn-light btn-lg px-5 py-3">Daftar Gratis Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0 animate-fade-up delay-100">
                    <h3 class="text-white mb-4">Lunasin</h3>
                    <p>Aplikasi debt tracking terpercaya untuk membantu Anda mengelola utang piutang dengan mudah dan
                        efisien. Solusi keuangan modern untuk era digital.</p>
                    <div class="social-links mt-4">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4 mb-4 mb-md-0 footer-links animate-fade-up delay-200">
                    <h5>Tautan Cepat</h5>
                    <ul>
                        <li><a href="#home">Beranda</a></li>
                        <li><a href="#features">Fitur</a></li>
                        <li><a href="#benefits">Manfaat</a></li>
                        <li><a href="#testimonials">Testimoni</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-4 mb-4 mb-md-0 footer-links animate-fade-up delay-300">
                    <h5>Layanan</h5>
                    <ul>
                        <li><a href="#">Debt Tracking</a></li>
                        <li><a href="#">Laporan Keuangan</a></li>
                        <li><a href="#">Pengingat Otomatis</a></li>
                        <li><a href="#">Analisis Keuangan</a></li>
                        <li><a href="#">API Integration</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-4 footer-links animate-fade-up delay-400">
                    <h5>Kontak Kami</h5>
                    <ul>
                        <li><i class="bi bi-envelope me-2"></i> hello@lunasin.com</li>
                        <li><i class="bi bi-telephone me-2"></i> +62 21 1234 5678</li>
                        <li><i class="bi bi-geo-alt me-2"></i> Jakarta, Indonesia</li>
                        <li><i class="bi bi-clock me-2"></i> Senin - Jumat: 09:00 - 18:00 WIB</li>
                    </ul>
                </div>
            </div>

            <hr class="my-4 bg-light">

            <div class="text-center animate-fade-up delay-500">
                <p class="mb-0">&copy; 2024 Lunasin. All rights reserved. Made with ❤️ in Indonesia</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <a href="#" class="scroll-to-top" id="scrollToTop">
        <i class="bi bi-arrow-up-circle"></i>
    </a>

    <!-- Vendor JS Files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Main JS -->
    <script>
        // Page Loading Animation
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.getElementById('loadingOverlay').classList.add('fade-out');
                setTimeout(() => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                }, 500);
            }, 1000);
        });

        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document.querySelectorAll('[class*="animate-"]').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });

        // Navbar scroll effect with smooth transition
        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }

            lastScrollTop = scrollTop;
        });

        // Enhanced FAQ toggle with smooth animations
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentElement;
                const allItems = document.querySelectorAll('.faq-item');

                // Close other items
                allItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                    }
                });

                // Toggle current item
                item.classList.toggle('active');
            });
        });

        // Smooth scrolling for anchor links with offset
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    const navbarHeight = document.querySelector('.navbar').offsetHeight;
                    const targetPosition = targetElement.offsetTop - navbarHeight - 20;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Enhanced scroll to top button with smooth animations
        const scrollToTopBtn = document.getElementById('scrollToTop');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollToTopBtn.style.display = 'flex';
                setTimeout(() => {
                    scrollToTopBtn.style.opacity = '1';
                    scrollToTopBtn.style.transform = 'scale(1)';
                }, 10);
            } else {
                scrollToTopBtn.style.opacity = '0';
                scrollToTopBtn.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    if (window.scrollY <= 300) {
                        scrollToTopBtn.style.display = 'none';
                    }
                }, 300);
            }
        });

        scrollToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Add hover effects to cards
        document.querySelectorAll('.feature-card, .testimonial-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-15px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Parallax effect for hero section backgrounds
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroSection = document.querySelector('.hero-section');
            const rate = scrolled * -0.5;

            if (heroSection) {
                heroSection.style.transform = `translateY(${rate}px)`;
            }
        });

        // Add stagger animation to benefit items
        const benefitItems = document.querySelectorAll('.benefit-item');
        benefitItems.forEach((item, index) => {
            item.style.animationDelay = `${index * 0.1 + 0.3}s`;
        });

        // Enhanced button hover effects
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px) scale(1.02)';
            });

            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Add typing effect to hero title (optional)
        function typeWriter(element, text, speed = 50) {
            let i = 0;
            element.innerHTML = '';

            function type() {
                if (i < text.length) {
                    element.innerHTML += text.charAt(i);
                    i++;
                    setTimeout(type, speed);
                }
            }
            type();
        }

        // Initialize typing effect after page load
        setTimeout(() => {
            const heroTitle = document.querySelector('.hero-section h1');
            if (heroTitle) {
                const originalText = heroTitle.textContent;
                // Uncomment the line below to enable typing effect
                // typeWriter(heroTitle, originalText, 80);
            }
        }, 1500);
    </script>
</body>

</html>
