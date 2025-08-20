# 🧾 Lunasin

**Lunasin** adalah aplikasi web berbasis **Laravel + Inertia.js + Quasar** untuk **manajemen utang-piutang pribadi**.  
Aplikasi ini membantu pengguna mencatat, mengelola, dan menganalisis utang-piutang dengan mudah, cepat, dan rapi.

---

## ✨ Fitur Utama

-   **Multi Tenant** → setiap user memiliki workspace/lingkungan terpisah.
-   **Login via Email dan Google** (OAuth).
-   **Dashboard** → ringkasan utang, piutang, dan saldo bersih.
-   **Pihak** → daftar orang/entitas yang terkait dalam transaksi.
-   **Transaksi** → pencatatan utang, piutang, pembayaran, pelunasan.
-   **Kategori Transaksi** → klasifikasi transaksi sesuai kebutuhan (misal: pribadi, usaha, pinjaman keluarga).
-   **Laporan** → ringkasan utang-piutang berdasarkan pihak, kategori, atau periode.

> Catatan: Modul **User** hanya digunakan untuk manajemen internal sistem (autentikasi & multi-tenant), bukan di dalam tenant.

---

## 🛠️ Tech Stack

-   **Backend**: [Laravel](https://laravel.com/)
-   **Frontend**: [Inertia.js](https://inertiajs.com/) + [Quasar Framework](https://quasar.dev/)
-   **Database**: MySQL / PostgreSQL (opsional)
-   **Authentication**: Laravel Socialite (Google OAuth)

---

## 🚀 Instalasi & Setup

### 1. Clone Repository

```bash
git clone https://github.com/username/lunasin.git
cd lunasin
```

### 2. Install Dependency Backend

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Install Dependency Frontend

````bash
3. Install Dependency Frontend
```bash
npm install
````

### 4. Konfigurasi .env

```env
APP_NAME=Lunasin
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lunasin
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_CLIENT_ID=xxxx
GOOGLE_CLIENT_SECRET=xxxx
GOOGLE_REDIRECT=http://localhost:8000/auth/google/callback
```

### 5. Migrasi Database

```bash
php artisan migrate --seed
```

### 6. Jalankan Aplikasi

Backend:

```code
php artisan serve
```

Frontend (Quasar dev):

```bash
npm run dev
```

Akses aplikasi di http://localhost:8000.

📊 Roadmap

-   Export laporan ke PDF
-   Export laporan ke Excel (Pending review)
-   Notifikasi pelunasan (Pending review)
-   Integrasi WhatsApp reminder (Pending review)
-   Mobile-friendly PWA (Pending review)
-   Implementasi Hashids untuk encode dan decode id agar tidak terkespose (Pending review)

### 👨‍💻 Kontributor

-   Fahmi Fauzi Rahman https://github.com/ffrz
-   Noval Faturrahman https://github.com/NvlFR

### 📜 Lisensi

Project ini bersifat internal/private (belum open source).
Lisensi dapat disesuaikan jika dibuka untuk publik.
