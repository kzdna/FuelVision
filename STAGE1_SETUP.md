# Stage 1 — Authentication & Middleware (+ Migration & Seeder)

File-file pada paket ini hanya berisi bagian **Stage 1** dari 10 tahap yang
telah disepakati (Auth/Middleware → Migration/Seeder → Master Kendaraan
Operasional → Master Harga BBM Vendor → Scan QR → Input Pengisian BBM →
Dashboard → Monitoring → Riwayat Transaksi → AI Insight).

## Cara Memasang ke Project Laravel Anda

1. **Copy file-file berikut** ke lokasi yang sama pada project Laravel 12
   Anda (menimpa file bawaan Laravel yang masih default, seperti
   `routes/web.php` dan `database/seeders/DatabaseSeeder.php`):

   - `database/migrations/*.php` (6 file)
   - `database/seeders/RoleSeeder.php`, `UserSeeder.php`, `DatabaseSeeder.php`
   - `app/Models/*.php` (6 file)
   - `app/Support/RoleName.php`
   - `app/Http/Middleware/RoleMiddleware.php`
   - `app/Http/Requests/LoginRequest.php`
   - `app/Http/Controllers/Auth/AuthController.php`
   - `app/Services/AIInsightService.php`
   - `routes/web.php`
   - `resources/views/layouts/app.blade.php`
   - `resources/views/auth/login.blade.php`

2. **Daftarkan middleware `role`** pada `bootstrap/app.php` (Laravel 12
   tidak lagi memakai `app/Http/Kernel.php`). Tambahkan pada bagian
   `->withMiddleware(function (Middleware $middleware) { ... })`:

   ```php
   use App\Http\Middleware\RoleMiddleware;

   ->withMiddleware(function (Middleware $middleware) {
       $middleware->alias([
           'role' => RoleMiddleware::class,
       ]);
   })
   ```

3. **Tambahkan konfigurasi Gemini** pada `config/services.php` — isi
   persis seperti pada `config/services-gemini-snippet.php` yang saya
   sertakan (tempel sebagai salah satu entri array di dalam
   `config/services.php` yang sudah ada).

4. **Tambahkan baris berikut ke file `.env`** Anda:

   ```
   GEMINI_API_KEY=
   ```

5. **Jalankan migration dan seeder** (pastikan `.env` sudah mengarah ke
   database `db_monitoring_bbm` yang sudah Anda perbaiki Primary Key-nya):

   ```
   php artisan migrate
   php artisan db:seed
   ```

   Karena seluruh migration menggunakan `Schema::create()` (bukan mengubah
   tabel yang sudah ada), pastikan tabel-tabel pada `db_monitoring_bbm`
   **belum ada** saat migration pertama kali dijalankan (mis. database baru,
   atau `php artisan migrate:fresh` bila Anda ingin menimpa struktur yang
   sudah ada dengan hasil migration ini — struktur yang dihasilkan sudah
   saya pastikan identik dengan `db_monitoring_bbm.sql` versi final Anda).

6. **Uji login** dengan salah satu dari tiga akun seeder (password sama
   untuk ketiganya: `password`):

   | Email | Role | Redirect Setelah Login |
   |---|---|---|
   | admin@company.com | Admin Finance | `/dashboard` *(belum ada — Stage 6)* |
   | vendor@company.com | Vendor | `/scan` *(belum ada — Stage 4)* |
   | viewer@company.com | View Only | `/dashboard` *(belum ada — Stage 6)* |

   **Catatan penting**: karena route `dashboard.index` dan `scan.index`
   belum dibuat (menyusul di tahap berikutnya), login akan **berhasil**
   tetapi redirect-nya akan menampilkan error "Route not defined" untuk
   sementara. Ini normal dan akan hilang begitu Stage 4 dan Stage 6
   selesai. Jika Anda ingin menguji login secara terisolasi tanpa error
   ini dulu, beri tahu saya — saya bisa tambahkan halaman placeholder
   sementara.

## Satu Pertanyaan Terbuka Sebelum Lanjut ke Stage 2

Tabel `transaksi_pengisian_bbm` pada dump SQL Anda hanya memiliki **index
biasa** (`ADD KEY`) pada `user_id`, `kendaraan_operasional_id`,
`kendaraan_gs_id`, dan `master_harga_bbm_vendor_id` — **bukan** constraint
`FOREIGN KEY` yang sesungguhnya (tidak ada klausa
`REFERENCES ... ON DELETE/UPDATE`). Migration Stage 1 ini saya buat
**identik** dengan itu (index saja, tanpa FK constraint), sesuai instruksi
"jangan mengubah ... foreign key ... tanpa persetujuan saya".

Namun, SRS Bab 5.10 secara eksplisit merekomendasikan menerapkan Foreign
Key Constraint pada seluruh relasi. Relasi Eloquent (`belongsTo`/`hasMany`)
pada Model **tetap berfungsi normal** tanpa FK constraint di level
database — jadi ini tidak menghalangi pengembangan selanjutnya.

**Mohon konfirmasi**: apakah Anda ingin saya menambahkan FOREIGN KEY
constraint sungguhan pada keempat kolom tersebut (mis. `ON DELETE
RESTRICT`), mengikuti pola persetujuan seperti perbaikan Primary Key
sebelumnya — atau tetap mempertahankan index biasa saja seperti kondisi
dump saat ini?
