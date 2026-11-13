# PerpusAPI — Perpustakaan RESTful API

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.3%20%7C%208.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP Version">
  <img src="https://img.shields.io/badge/Sanctum-Token%20Auth-red?style=for-the-badge" alt="Sanctum">
  <img src="https://img.shields.io/badge/OpenAPI-3.0-85EA2D?style=for-the-badge&logo=swagger&logoColor=black" alt="OpenAPI">
  <img src="https://img.shields.io/badge/PHPUnit-11.x-3776AB?style=for-the-badge&logo=php&logoColor=white" alt="PHPUnit">
  <img src="https://img.shields.io/badge/Tests-11%20Passed-brightgreen?style=for-the-badge" alt="Tests">
</p>

---

## 📖 Ringkasan Proyek

**PerpusAPI** adalah RESTful Web API modern untuk sistem manajemen perpustakaan yang dibangun menggunakan **Laravel 12** dan **PHP 8**. Proyek ini menerapkan standar pengembangan backend industri, meliputi autentikasi berbasis token dengan **Laravel Sanctum**, operasi **CRUD data buku** dengan paginasi dan pencarian, transformasi data menggunakan **Eloquent API Resources**, validasi ketat via **Form Requests**, serta dokumentasi interaktif **OpenAPI 3.0 / Swagger UI** menggunakan PHP 8 Native Attributes (`#[OA\...]`).

Dikembangkan oleh **Muhammad Lutfi Maulana** sebagai bagian dari portofolio backend development dan pemenuhan tugas praktikum Pemrograman Web Lanjut.

---

## ✨ Fitur Utama

- 🔐 **Autentikasi Token (Laravel Sanctum)**: Registrasi akun, otentikasi login, profil (`/api/me`), dan pencabutan token aktif (`/api/logout`).
- 🛡️ **Rate Limiting & Keamanan**: Pembatasan percobaan login (`throttle:6,1`) untuk perlindungan *brute-force*, serta hashing password otomatis menggunakan Eloquent Attribute Casting.
- 📚 **CRUD Manajemen Buku**:
  - `GET /api/books`: Pencarian dinamis (`title` & `author`) dan paginasi data fleksibel (1–100 data/halaman).
  - `POST /api/books`: Penambahan buku baru dengan validasi ISBN unik.
  - `GET /api/books/{book}`: Detail spesifik entitas buku.
  - `PUT/PATCH /api/books/{book}`: Pembaruan penuh (`PUT`) atau sebagian (`PATCH`) dengan penanganan pengecualian ISBN unik untuk rekaman yang sedang diedit.
  - `DELETE /api/books/{book}`: Penghapusan entitas buku dari basis data.
- 📑 **Dokumentasi Interaktif OpenAPI 3 (L5-Swagger)**:
  - Didokumentasikan menggunakan **PHP 8 Attributes** (`#[OA\Get]`, `#[OA\Post]`, `#[OA\Schema]`, dll) tanpa anotasi DocBlock lama.
  - Swagger UI interaktif langsung dengan dukungan otorisasi *Bearer Token*.
- ⚡ **Konsistensi Respons JSON**: Penanganan exception API terpusat pada `bootstrap/app.php` (`shouldRenderJsonWhen`) sehingga semua error API selalu mengembalikan format JSON terstandar, bahkan ketika klien tidak menyertakan header `Accept: application/json`.
- 🧪 **Pengujian Otomatis Komprehensif (PHPUnit)**: *Feature Tests* lengkap mencakup pengujian registrasi, otentikasi, validasi, otorisasi, pencarian, dan seluruh siklus hidup CRUD data buku menggunakan basis data *SQLite In-Memory*.

---

## 🛠️ Arsitektur & Teknologi

| Komponen | Teknologi | Keterangan |
| :--- | :--- | :--- |
| **Framework** | Laravel 12 | PHP Web Framework modern |
| **Bahasa Pemrograman** | PHP 8.3 / 8.4 | Menggunakan Typed Properties, Constructor Promotion, Attributes |
| **Autentikasi** | Laravel Sanctum | Stateful & Bearer token API |
| **Dokumentasi API** | L5-Swagger (OpenAPI 3) | `darkaonline/l5-swagger` berbasis PHP Attributes |
| **Database** | SQLite / MySQL | Default SQLite bawaan Laravel 12 |
| **Testing** | PHPUnit 11 | 11 Feature & Unit Tests (67 Assertions) |

---

## 📋 Daftar Endpoint API

| Method | Endpoint | Akses | Deskripsi |
| :--- | :--- | :---: | :--- |
| `POST` | `/api/register` | Publik | Registrasi akun baru & dapatkan Bearer Token |
| `POST` | `/api/login` | Publik | Otentikasi pengguna & terbitkan Bearer Token (`throttle:6,1`) |
| `GET` | `/api/me` | Sanctum | Mendapatkan profil akun pengguna yang sedang terautentikasi |
| `POST` | `/api/logout` | Sanctum | Menghapus token aktif yang digunakan |
| `GET` | `/api/books` | Sanctum | Menampilkan daftar buku (Paginasi: `per_page`, Pencarian: `search`) |
| `POST` | `/api/books` | Sanctum | Menambahkan data buku baru |
| `GET` | `/api/books/{book}` | Sanctum | Mengambil rincian informasi buku berdasarkan ID |
| `PUT/PATCH` | `/api/books/{book}` | Sanctum | Memperbarui seluruh data (`PUT`) atau sebagian data (`PATCH`) |
| `DELETE` | `/api/books/{book}` | Sanctum | Menghapus data buku berdasarkan ID |
| `GET` | `/api/documentation` | Publik | Antarmuka interaktif Swagger UI |
| `GET` | `/docs` | Publik | Spesifikasi mentah OpenAPI 3 (JSON) |

---

## 📁 Struktur Direktori

```text
perpus-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php                  # Info Global OpenAPI, Server, SecurityScheme
│   │   │   └── Api/
│   │   │       ├── AuthController.php          # Kontroler Auth (Register, Login, Me, Logout)
│   │   │       └── BookController.php          # Kontroler CRUD Buku
│   │   ├── Requests/
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php            # Validasi & Schema OpenAPI Login
│   │   │   │   └── RegisterRequest.php         # Validasi & Schema OpenAPI Register
│   │   │   ├── StoreBookRequest.php            # Validasi & Schema OpenAPI Tambah Buku
│   │   │   └── UpdateBookRequest.php           # Validasi & Schema OpenAPI Ubah Buku (PATCH/PUT)
│   │   └── Resources/
│   │       ├── BookResource.php                # Transformasi JSON & Schema Book/BookCollection
│   │       └── UserResource.php                # Transformasi JSON & Schema User
│   └── Models/
│       ├── Book.php                            # Model Buku dengan Casting & Fillable
│       └── User.php                            # Model User dengan Trait HasApiTokens
├── bootstrap/
│   └── app.php                                 # Konfigurasi Exception Handler (Always JSON)
├── config/
│   ├── l5-swagger.php                          # Konfigurasi L5-Swagger UI
│   └── sanctum.php                             # Konfigurasi Sanctum & Masa Berlaku Token
├── database/
│   ├── factories/
│   │   └── BookFactory.php                     # Factory data tiruan buku (Faker)
│   ├── migrations/
│   │   └── ..._create_books_table.php          # Skema migrasi tabel books
│   └── seeders/
│       └── DatabaseSeeder.php                  # Seeder akun demo dan 20 data buku
├── routes/
│   └── api.php                                 # Pendaftaran Rute API & Middleware Group
├── storage/
│   └── api-docs/
│       └── api-docs.json                       # Output spesifikasi OpenAPI 3.0
└── tests/
    └── Feature/
        ├── AuthApiTest.php                     # Test Otentikasi (Register, Login, Me, Logout)
        └── BookApiTest.php                     # Test CRUD Buku, Validasi, dan Paginasi
```

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### Prasyarat Sistem
- **PHP** >= 8.3 (dengan ekstensi: `pdo_sqlite` / `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`)
- **Composer** 2.x
- **Git**

### Langkah Instalasi

1. **Clone repositori dan masuk ke direktori proyek**:
   ```bash
   git clone <url-repositori-anda>
   cd perpus-api
   ```

2. **Pasang dependensi Composer**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**:
   Salin berkas template environment:
   ```bash
   cp .env.example .env
   ```
   Pastikan variabel konfigurasi OpenAPI Swagger aktif:
   ```env
   APP_URL=http://localhost:8000

   # Konfigurasi Swagger UI
   L5_SWAGGER_CONST_HOST=http://localhost:8000
   L5_SWAGGER_GENERATE_ALWAYS=true
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate:fresh --seed
   ```
   > *Catatan:* Perintah di atas akan membuat tabel basis data, menambahkan 1 akun administrator demo, dan mengenerate 20 data buku acak.

6. **Generate Dokumentasi OpenAPI Swagger**:
   ```bash
   php artisan l5-swagger:generate
   ```

7. **Jalankan Local Development Server**:
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan pada [http://localhost:8000](http://localhost:8000).

---

## 🧪 Pengujian Otomatis (Testing)

Proyek ini dilengkapi pengujian unit dan fungsional penuh menggunakan **PHPUnit** dengan isolasi basis data in-memory SQLite (`RefreshDatabase`):

```bash
php artisan test
```

### Hasil Eksekusi Test
```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\AuthApiTest
  ✓ user bisa register dan mendapat token
  ✓ user bisa login dengan kredensial benar
  ✓ login gagal jika password salah
  ✓ me dan logout menghapus token

   PASS  Tests\Feature\BookApiTest
  ✓ endpoint buku menolak akses tanpa login
  ✓ daftar buku berpaginasi dan bisa dicari
  ✓ tambah buku dan validasi
  ✓ lihat detail dan ubah buku
  ✓ hapus buku

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

  Tests:    11 passed (67 assertions)
  Duration: 1.54s
```

---

## 🖥️ Eksplorasi Dokumentasi Swagger UI

1. Buka browser dan arahkan ke:
   ```
   http://localhost:8000/api/documentation
   ```
2. **Kredensial Demo Login**:
   - **Email**: `admin@example.com`
   - **Password**: `password`
3. **Cara Autentikasi di Swagger UI**:
   - Buka tag **Auth** → `POST /api/login` → klik **Try it out** → masukkan kredensial → klik **Execute**.
   - Salin string token dari respons JSON (contoh: `1|Xz9aBcDeFgHiJkLmNoPq...`).
   - Klik tombol **Authorize** (ikon gembok di kanan atas).
   - Tempel token ke dalam kolom *Value* (langsung tanpa kata `Bearer`), lalu klik **Authorize** dan **Close**.
   - Sekarang Anda dapat mengeksekusi semua endpoint yang diproteksi token (tag **Books** dan `/api/me`).

---

## 👨‍💻 Pengembang

**Muhammad Lutfi Maulana**  
*Pemrograman Web Lanjut — Pengembangan REST API Laravel 12 & OpenAPI*

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).
