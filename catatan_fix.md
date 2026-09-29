# Catatan Perbaikan Branch W05

## Perubahan

1. Memindahkan halaman detail mata kuliah ke grup `auth`, sehingga pengunjung tanpa sesi tidak dapat membuka detail.
2. Memeriksa policy `download` untuk submission dan material sebelum membaca atau mengirim file. Pemilik submission, dosen pengampu, admin, dan mahasiswa terdaftar tetap mengikuti aturan policy yang ada.
3. Mengaktifkan `scopeBindings()` pada route detail assignment bersarang agar assignment harus berasal dari course pada URL.
4. Mendaftarkan alias middleware `role` melalui `bootstrap/app.php`, konfigurasi middleware Laravel 12, bukan `app/Http/Kernel.php`.
5. Memberi nama berbeda pada route daftar course dosen dan mahasiswa serta memperbarui tautan dan redirect internal.
6. Mengganti penghapusan materi dari `GET` ke `DELETE` dan mengirimkannya melalui form ber-CSRF.

## Bukti IDOR Submission

Gunakan sesi yang sudah login sebagai mahasiswa lain (bukan pemilik submission), lalu ganti nilai contoh dengan ID submission yang ada. Kedua perintah mengirim request yang sama; sebelum perbaikan endpoint mengirimkan berkas tanpa policy, sesudah perbaikan policy menolak akses dengan `403`.

```sh
# Sebelum perbaikan: request dengan sesi mahasiswa lain dapat mengunduh file (200).
curl -i -H 'Cookie: laravel_session=<authenticated-session>' \
  http://127.0.0.1:8000/submissions/42/download

# Sesudah perbaikan: request yang sama ditolak (403).
curl -i -H 'Cookie: laravel_session=<authenticated-session>' \
  http://127.0.0.1:8000/submissions/42/download
```

Tes regresi yang relevan ada di `tests/Feature/RouteSecurityTest.php`; tes membuktikan pengguna lain menerima `403` dan pemilik submission tetap dapat mengunduh.

## Verifikasi

- `php artisan route:list --except-vendor`: nama daftar course unik, route hapus materi memakai `DELETE`, seluruh route terkait terdaftar.
- `php artisan test`: 18 test lulus dengan 39 assertion, termasuk enam test keamanan route.