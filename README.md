# 🎓 SNBTKU - Platform E-Learning Persiapan SNBT

Platform e-learning gratis untuk membantu siswa Indonesia mempersiapkan diri menghadapi **Seleksi Nasional Berbasis Tes (SNBT)**.

## 📋 Daftar Isi

- [Overview](#-overview)
- [Tech Stack](#-tech-stack)
- [Fitur Utama](#-fitur-utama)
- [Instalasi](#-instalasi)
- [Struktur Database](#-struktur-database)
- [URL Testing](#-url-testing)
- [Kredensial Login](#-kredensial-login)
- [Panduan Penggunaan](#-panduan-penggunaan)
- [Struktur Proyek](#-struktur-proyek)
- [API Endpoints](#-api-endpoints)
- [Komponen Livewire](#-komponen-livewire)
- [Troubleshooting](#-troubleshooting)

## 🎯 Overview

SNBTKU adalah platform e-learning yang dirancang khusus untuk membantu siswa SMA/sederajat mempersiapkan diri menghadapi SNBT. Platform ini menyediakan:

- **Try Out Online** dengan timer real-time
- **Bank Soal** lengkap dengan pembahasan
- **Analisis Hasil** yang detail
- **Artikel Edukasi** tips dan strategi
- **Admin Panel** untuk manajemen konten

### Misi Utama
Menyediakan platform persiapan SNBT yang **100% gratis** dan mudah diakses untuk seluruh siswa Indonesia.

## 🛠 Tech Stack

| Komponen | Teknologi | Versi |
|----------|-----------|-------|
| **Backend Framework** | Laravel | 11.x |
| **Frontend Interaktif** | Livewire | 3.x |
| **Database** | MySQL/SQLite | - |
| **Autentikasi** | Laravel Breeze | - |
| **Styling** | Tailwind CSS | 3.x |
| **Build Tool** | Vite | - |

### Mengapa Tech Stack Ini?

- **Laravel 11**: Framework PHP modern dengan performa tinggi
- **Livewire 3**: Interaktivitas tanpa JavaScript kompleks (lebih ringan dari React/Vue)
- **Laravel Breeze**: Autentikasi siap pakai yang ringan
- **Tailwind CSS**: Styling yang efisien dan responsif
- **SQLite**: Database ringan untuk development (mudah deploy ke MySQL)

## ✨ Fitur Utama

### 👨‍🎓 Fitur untuk Siswa

1. **Try Out Online**
   - Timer countdown real-time
   - Navigasi soal dengan indikator status
   - Auto-save jawaban
   - Konfirmasi sebelum submit

2. **Analisis Hasil**
   - Skor dan persentase
   - Review jawaban benar/salah
   - Pembahasan lengkap setiap soal
   - Statistik waktu pengerjaan

3. **Bank Artikel**
   - Tips dan strategi SNBT
   - Materi pembelajaran
   - Estimasi waktu baca

### 👨‍💼 Fitur untuk Admin

1. **Dashboard**
   - Statistik pengguna dan try out
   - Overview konten

2. **Manajemen Konten**
   - CRUD Subjects (Mata Pelajaran)
   - CRUD Topics (Sub-Materi)
   - CRUD Questions (Bank Soal)
   - CRUD Tryout Packages
   - CRUD Posts (Artikel)

3. **Manajemen Try Out**
   - Buat paket soal
   - Atur durasi dan status
   - Kelola urutan soal

## 🚀 Instalasi

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL (opsional, menggunakan SQLite untuk development)

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd snbtku
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Build Assets**
   ```bash
   npm run build
   ```

6. **Start Server**
   ```bash
   php artisan serve
   ```

## 🗄 Struktur Database

### Tabel Utama

| Tabel | Deskripsi | Relasi |
|-------|-----------|--------|
| `users` | Data pengguna (admin/siswa) | - |
| `subjects` | Mata pelajaran (TPS, Literasi) | hasMany topics |
| `topics` | Sub-materi per subject | belongsTo subject, hasMany questions |
| `questions` | Bank soal dengan pembahasan | belongsTo topic |
| `tryout_packages` | Paket try out | belongsToMany questions |
| `user_tryouts` | Sesi pengerjaan siswa | belongsTo user, package |
| `user_tryout_answers` | Jawaban per soal | belongsTo user_tryout, question |
| `posts` | Artikel dan konten edukasi | belongsTo user |

### Entity Relationship Diagram

```
Users (1) -----> (N) UserTryouts (N) -----> (1) TryoutPackages
                      |                           |
                      |                           |
                      v                           v
                 UserTryoutAnswers (N) -----> (1) Questions
                                                   |
                                                   v
                                              (1) Topics
                                                   |
                                                   v
                                              (1) Subjects
```

## 🌐 URL Testing

### 🏠 Frontend (User)

| URL | Deskripsi | Status |
|-----|-----------|--------|
| [http://localhost:8000](http://localhost:8000) | Homepage | ✅ |
| [http://localhost:8000/login](http://localhost:8000/login) | Login | ✅ |
| [http://localhost:8000/register](http://localhost:8000/register) | Register | ✅ |
| [http://localhost:8000/tryouts](http://localhost:8000/tryouts) | Daftar Try Out | ✅ |
| [http://localhost:8000/posts](http://localhost:8000/posts) | Daftar Artikel | ✅ |
| [http://localhost:8000/tryout/conduct/{id}](http://localhost:8000/tryout/conduct/1) | Pengerjaan Try Out | ✅ |
| [http://localhost:8000/tryout/result/{id}](http://localhost:8000/tryout/result/1) | Hasil Try Out | ✅ |

### 🔧 Backend (Admin)

| URL | Deskripsi | Status |
|-----|-----------|--------|
| [http://localhost:8000/admin](http://localhost:8000/admin) | Dashboard Admin | ✅ |
| [http://localhost:8000/admin/subjects](http://localhost:8000/admin/subjects) | Kelola Subjects | ✅ |
| [http://localhost:8000/admin/topics](http://localhost:8000/admin/topics) | Kelola Topics | ✅ |
| [http://localhost:8000/admin/questions](http://localhost:8000/admin/questions) | Kelola Questions | ✅ |
| [http://localhost:8000/admin/tryout-packages](http://localhost:8000/admin/tryout-packages) | Kelola Try Out | ✅ |
| [http://localhost:8000/admin/posts](http://localhost:8000/admin/posts) | Kelola Artikel | ✅ |

## 🔑 Kredensial Login

### Admin
- **Email**: `admin@snbtku.com`
- **Password**: `password`
- **Role**: Admin
- **Akses**: Full admin panel

### Siswa (User)
- **Email**: `budi@student.com`
- **Password**: `password`
- **Role**: User

- **Email**: `sari@student.com`
- **Password**: `password`
- **Role**: User

## 📖 Panduan Penggunaan

### Untuk Siswa

1. **Registrasi/Login**
   - Kunjungi [http://localhost:8000/register](http://localhost:8000/register)
   - Atau login dengan akun dummy di atas

2. **Mengerjakan Try Out**
   - Kunjungi [http://localhost:8000/tryouts](http://localhost:8000/tryouts)
   - Klik "Mulai Try Out" pada paket yang tersedia
   - Kerjakan soal dengan timer yang berjalan
   - Gunakan navigasi nomor soal untuk berpindah
   - Klik "Selesai" untuk submit

3. **Melihat Hasil**
   - Setelah submit, otomatis redirect ke halaman hasil
   - Lihat skor dan analisis jawaban
   - Baca pembahasan setiap soal

4. **Membaca Artikel**
   - Kunjungi [http://localhost:8000/posts](http://localhost:8000/posts)
   - Pilih artikel yang ingin dibaca

### Untuk Admin

1. **Login Admin**
   - Login dengan kredensial admin
   - Akses [http://localhost:8000/admin](http://localhost:8000/admin)

2. **Kelola Bank Soal**
   - Buat Subjects dan Topics terlebih dahulu
   - Tambah Questions dengan pembahasan
   - Atur tingkat kesulitan dan kategori

3. **Buat Try Out**
   - Buat TryoutPackage baru
   - Pilih soal-soal dari bank soal
   - Atur durasi dan status publikasi

4. **Kelola Konten**
   - Tulis artikel tips dan strategi
   - Atur status draft/published

## 📁 Struktur Proyek

```
snbtku/
├── app/
│   ├── Http/Controllers/     # Controllers untuk routing
│   ├── Livewire/            # Komponen Livewire
│   ├── Models/              # Eloquent Models
│   └── Middleware/          # Custom Middleware
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── views/
│   │   ├── admin/          # Views admin panel
│   │   ├── livewire/       # Livewire component views
│   │   ├── posts/          # Views artikel
│   │   └── tryouts/        # Views try out
│   └── css/                # Styling files
├── routes/
│   ├── web.php             # Web routes
│   └── auth.php            # Authentication routes
└── public/                 # Public assets
```

## 🔌 API Endpoints

### Authentication Routes
```php
POST   /login              # Login
POST   /register           # Register
POST   /logout             # Logout
GET    /forgot-password    # Forgot password form
POST   /forgot-password    # Send reset link
GET    /reset-password     # Reset password form
POST   /reset-password     # Update password
```

### User Routes
```php
GET    /                   # Homepage
GET    /tryouts            # Daftar try out
GET    /posts              # Daftar artikel
GET    /posts/{slug}       # Detail artikel
GET    /tryout/conduct/{id} # Pengerjaan try out
GET    /tryout/result/{id}  # Hasil try out
```

### Admin Routes (Protected)
```php
GET    /admin                    # Dashboard
GET    /admin/subjects          # CRUD Subjects
GET    /admin/topics            # CRUD Topics
GET    /admin/questions         # CRUD Questions
GET    /admin/tryout-packages   # CRUD Try Out Packages
GET    /admin/posts             # CRUD Posts
```

## ⚡ Komponen Livewire

### 1. ShowTryoutPackageList
**File**: `app/Livewire/ShowTryoutPackageList.php`

**Fungsi**: Menampilkan daftar paket try out dengan status pengerjaan user

**Methods**:
- `startTryout($packageId)`: Memulai atau melanjutkan try out
- `render()`: Render view dengan data packages

### 2. ConductTryout
**File**: `app/Livewire/ConductTryout.php`

**Fungsi**: Mengelola proses pengerjaan try out

**Properties**:
- `$userTryout`: Data sesi try out
- `$questions`: Koleksi soal
- `$currentQuestionIndex`: Index soal aktif
- `$userAnswers`: Array jawaban user
- `$timeRemaining`: Waktu tersisa

**Methods**:
- `mount($userTryoutId)`: Inisialisasi komponen
- `goToQuestion($index)`: Navigasi ke soal tertentu
- `selectAnswer($answer)`: Simpan jawaban user
- `submitTryout()`: Submit dan hitung skor

### 3. ShowTryoutResult
**File**: `app/Livewire/ShowTryoutResult.php`

**Fungsi**: Menampilkan hasil try out dengan analisis

**Properties**:
- `$userTryout`: Data hasil try out
- `$questions`: Soal dengan jawaban user
- `$showExplanations`: Toggle pembahasan

**Methods**:
- `mount($userTryoutId)`: Load data hasil
- `toggleExplanations()`: Show/hide pembahasan

## 🐛 Troubleshooting

### Common Issues

1. **Server tidak bisa diakses**
   ```bash
   # Pastikan server berjalan
   php artisan serve
   
   # Cek port yang digunakan
   netstat -an | findstr :8000
   ```

2. **Database error**
   ```bash
   # Reset database
   php artisan migrate:fresh --seed
   
   # Cek koneksi database di .env
   DB_CONNECTION=sqlite
   ```

3. **Assets tidak load**
   ```bash
   # Build ulang assets
   npm run build
   
   # Atau untuk development
   npm run dev
   ```

4. **Permission error**
   ```bash
   # Set permission untuk storage dan cache
   chmod -R 775 storage bootstrap/cache
   ```

### Performance Tips

1. **Optimasi Database**
   - Gunakan eager loading untuk relasi
   - Index kolom yang sering di-query
   - Pagination untuk data besar

2. **Optimasi Frontend**
   - Lazy loading untuk komponen Livewire
   - Minimize DOM updates
   - Gunakan wire:key untuk list items

3. **Caching**
   ```bash
   # Cache routes dan config
   php artisan route:cache
   php artisan config:cache
   php artisan view:cache
   ```

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` di `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate production key: `php artisan key:generate`
- [ ] Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- [ ] Cache configuration: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`
- [ ] Build production assets: `npm run build`
- [ ] Set proper file permissions
- [ ] Configure web server (Apache/Nginx)
- [ ] Setup SSL certificate
- [ ] Configure database backup

### Environment Variables

```env
APP_NAME="SNBTKU"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=snbtku
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

## 📞 Support

Jika mengalami masalah atau memiliki pertanyaan:

1. Cek bagian [Troubleshooting](#-troubleshooting)
2. Review log error di `storage/logs/laravel.log`
3. Pastikan semua dependencies terinstall dengan benar
4. Cek konfigurasi environment di `.env`

---

**Dibuat dengan ❤️ untuk pendidikan Indonesia**

*Platform SNBTKU - Membantu siswa Indonesia meraih impian perguruan tinggi negeri*