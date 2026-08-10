# Vault - Helmet Storage Management System

Vault adalah sistem manajemen penitipan helm berbasis web full-stack yang dirancang untuk mengotomatisasi operasional lokasi penitipan helm, seperti pusat perbelanjaan, stasiun, dan gedung perkantoran. Sistem ini mengelola alur penitipan dan pengambilan helm, alokasi loker otomatis, perhitungan tarif berbasis durasi jam, pencatatan pembayaran, pencetakan struk termal, serta pelaporan keuangan harian dan bulanan.

---

## Daftar Isi

1. [Teknologi dan Arsitektur](#teknologi-dan-arsitektur)
2. [Fitur Utama](#fitur-utama)
3. [Struktur Database (ERD)](#struktur-database-erd)
4. [Prasyarat Sistem](#prasyarat-sistem)
5. [Instalasi dan Konfigurasi](#instalasi-dan-konfigurasi)
7. [Pengujian Automasi](#pengujian-automasi)
8. [Struktur Direktori Proyek](#struktur-direktori-proyek)

---

## Teknologi dan Arsitektur

Aplikasi dibangun menggunakan arsitektur Model-View-Controller (MVC) dengan komponen berikut:

- **Backend Framework**: Laravel 12 (PHP 8.2+)
- **Database Engine**: MySQL 8.0+
- **Frontend Stack**: Laravel Blade, Bootstrap 5.3, Custom CSS (Tremor.so Design System)
- **Visualisasi Data**: Chart.js 4.4
- **Autentikasi & Keamanan**: Laravel Breeze, Role-Based Access Control (RBAC) Middleware, Isolasi Transaksi Database (Atomic Locks).

---

## Fitur Utama

### 1. Manajemen Operasional Penitipan dan Pengambilan Helm
- **Pencatatan Penitipan**: Input data pelanggan dan rincian helm (merk, warna, ciri khusus).
- **Alokasi Loker Otomatis**: Sistem memilihkan loker berstatus `tersedia` secara otomatis dan aman dari kondisi *race condition*.
- **Generasi Kode Transaksi**: Pembuatan kode unik transaksi otomatis dengan format `VLT-YYYYMMDD-XXX`.
- **Perhitungan Tarif Dinamis**: Biaya dihitung berdasarkan pembulatan ke atas durasi jam (`ceil(durasi_jam) * tarif_per_jam_aktif`).
- **Penyelesaian Transaksi & Pembayaran**: Pencatatan metode bayar (`tunai` atau `ewallet`) dan pengosongan loker kembali menjadi `tersedia`.
- **Cetak Struk Termal**: Tampilan struk bukti titip dan bukti bayar yang siap dicetak untuk printer termal ukuran 80mm.

### 2. Monitoring Status Loker
- Tampilan grid status seluruh unit loker secara real-time.
- Indikator visual ketersediaan loker (`tersedia` / `terisi`).
- Informasi detail transaksi aktif pada loker yang sedang terisi.
- Filter berdasarkan status ketersediaan dan zona lokasi loker.

### 3. Pengelolaan Data Pelanggan
- Pencatatan data master pelanggan (nama, nomor kontak WhatsApp, alamat/catatan).
- Riwayat frekuensi penitipan per pelanggan.

### 4. Administrasi dan Otorisasi Hak Akses (Admin Only)
- **Kelola Petugas**: Registrasi, pembaruan data, reset password, dan pemblokiran/penghapusan akun pengguna (`admin` dan `petugas`).
- **Kelola Tarif**: Pembuatan skema harga per jam dan sakelar aktivasi tarif utama.
- **Laporan Laba/Rugi**: Pelaporan rekapitulasi transaksi dan pendapatan harian serta bulanan, lengkap dengan rincian pemisahan metode pembayaran tunai dan e-wallet.

---

## Struktur Database (ERD)

Sistem menggunakan 7 tabel utama dalam basis data `vault`:

1. **`users`**: Menyimpan akun pengguna sistem (`id`, `name`, `email`, `password`, `role` ENUM['admin', 'petugas']).
2. **`pelanggan`**: Menyimpan identitas pelanggan (`id`, `nama`, `no_hp`, `alamat`).
3. **`loker`**: Menyimpan data unit loker (`id`, `nomor_loker`, `status` ENUM['tersedia', 'terisi'], `lokasi`).
4. **`helm`**: Menyimpan atribut helm yang dititipkan (`id`, `pelanggan_id`, `merk`, `warna`, `deskripsi`).
5. **`transaksi`**: Record utama penitipan (`id`, `kode_transaksi`, `pelanggan_id`, `helm_id`, `loker_id`, `user_id`, `tgl_titip`, `tgl_ambil`, `status` ENUM['titip', 'ambil', 'batal'], `tarif`).
6. **`pembayaran`**: Financial ledger transaksi (`id`, `transaksi_id`, `jumlah`, `metode_bayar` ENUM['tunai', 'ewallet'], `tgl_bayar`, `status` ENUM['lunas', 'belum']).
7. **`tarif`**: Tabel referensi harga per jam (`id`, `nama`, `harga_per_jam`, `is_active`).

---

## Prasyarat Sistem

Sebelum melakukan instalasi, pastikan lingkungan eksekusi memiliki dependensi berikut:

- **PHP**: v8.2.0 atau versi yang lebih baru (ext-pdo, ext-mbstring, ext-openssl)
- **Composer**: v2.5.0 atau lebih baru
- **Node.js**: v18.0.0 atau lebih baru
- **NPM**: v9.0.0 atau lebih baru
- **Database Server**: MySQL v8.0+ atau MariaDB v10.4+

---

## Instalasi dan Konfigurasi

Jalankan langkah-langkah berikut untuk memasang aplikasi di lingkungan lokal:

1. **Kloning Repository**
   ```bash
   git clone https://github.com/wibisanabama/vault.git
   cd vault
   ```

2. **Instalasi Dependensi PHP dan Node.js**
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Pastikan pengaturan koneksi database pada `.env` telah disesuaikan:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=vault
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Migrasi Database dan Seeding Data Awal**
   Perintah ini akan membuat database `vault` (jika belum ada), menjalankan tabel skema, dan mengisi data awal (user default, 20 unit loker, serta tarif default):
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Compiling Frontend Assets**
   ```bash
   npm run build
   ```

7. **Menjalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di: `http://127.0.0.1:8000`

---

## Pengujian Automasi

Proyek ini dilengkapi dengan suite pengujian otomatis menggunakan PHPUnit. Untuk menjalankan seluruh unit test dan feature test:

```bash
php artisan test
```

Cakupan pengujian mencakup:
- Otorisasi halaman login dan pembatasan akses berbasis peran.
- Alur lengkap penitipan helm dan pengujian alokasi loker otomatis.
- Kalkulasi akurat durasi jam dan pembuatan record pembayaran saat pengambilan.
- Pembatasan hak akses laporan dan tarif khusus Administrator.

---

## Struktur Direktori Proyek

```text
vault/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Logic pengendali (Dashboard, Transaksi, Loker, dsb.)
│   │   └── Middleware/        # CheckRole middleware untuk RBAC
│   └── Models/                # Eloquent ORM Models (User, Transaksi, Loker, dsb.)
├── database/
│   ├── migrations/            # Migration skema tabel ERD
│   └── seeders/               # Data seeder awal (Users, Lockers, Tarif)
├── public/
│   ├── css/
│   │   └── tremor-vault.css   # System desain CSS Tremor.so style
│   └── index.php
├── resources/
│   └── views/                 # Blade Template UI (Layouts, Transaksi, Laporan, dsb.)
├── routes/
│   └── web.php                # Definisi route web aplikasi
└── tests/
    └── Feature/
        └── VaultSystemTest.php # Test suite pengujian otomatis
```
