# Catatan Perbaikan Branch W05

Dokumen ini merangkum tujuh masalah routing dan otorisasi, dampaknya, serta perubahan kode yang dilakukan.

## 1. Detail Course Di Luar Grup `auth`

**Akibat:** Pengunjung tanpa login dapat mencapai route detail course. Policy `view` mungkin menolak akses tertentu, tetapi autentikasi seharusnya menjadi batas awal route privat dan mencegah request anonim mencapai controller.

**Langkah perbaikan:** Pindahkan route detail ke dalam grup `auth`; otorisasi course tetap diperiksa oleh `CourseController::show()`.

**Sebelum:**

```php
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

Route::middleware('auth')->group(function () {
```

**Sesudah:**

```php
Route::middleware('auth')->group(function () {
  Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
```

## 2. IDOR pada Download Submission

**Akibat:** Pengguna yang mengetahui atau menebak ID submission orang lain dapat mengunduh berkas privat karena controller hanya memeriksa keberadaan file, bukan hak pengguna.

**Langkah perbaikan:** Jalankan policy `download` sebelum memeriksa atau mengirim file. Policy yang sudah ada membatasi unduhan kepada pemilik, dosen terkait, atau admin sesuai aturannya.

**Sebelum:**

```php
public function download(Submission $submission)
{
  if (! $submission->file_path || ! Storage::disk('local')->exists($submission->file_path)) {
```

**Sesudah:**

```php
public function download(Submission $submission)
{
  Gate::authorize('download', $submission);

  if (! $submission->file_path || ! Storage::disk('local')->exists($submission->file_path)) {
```

**Bukti request `curl`:** Gunakan cookie sesi mahasiswa yang bukan pemilik submission dan ganti ID contoh dengan ID submission milik mahasiswa lain. Sebelum perubahan controller mengirim file (`200`); sesudah perubahan policy menolak request (`403`).

```sh
# Sebelum perbaikan: respons berisi file, HTTP 200.
curl -i -H 'Cookie: laravel_session=<sesi-mahasiswa-lain>' \
  http://127.0.0.1:8000/submissions/42/download

# Sesudah perbaikan: akses ditolak, HTTP 403.
curl -i -H 'Cookie: laravel_session=<sesi-mahasiswa-lain>' \
  http://127.0.0.1:8000/submissions/42/download
```

## 3. IDOR pada Download Material

**Akibat:** Pengguna di luar course dapat mengunduh material privat dengan meminta URL yang berisi ID material, karena action download tidak memeriksa keanggotaan course.

**Langkah perbaikan:** Otorisasi dengan policy `download` sebelum akses disk. Policy material mengizinkan admin, dosen pengampu, dan mahasiswa yang terdaftar di course.

**Sebelum:**

```php
public function download(Material $material)
{
  if ($material->type !== 'file' || ! $material->file_path || ! Storage::disk('local')->exists($material->file_path)) {
```

**Sesudah:**

```php
public function download(Material $material)
{
  Gate::authorize('download', $material);

  if ($material->type !== 'file' || ! $material->file_path || ! Storage::disk('local')->exists($material->file_path)) {
```

## 4. Nested Route Tidak Menggunakan `scopeBindings`

**Akibat:** Laravel mencari assignment secara global, sehingga ID assignment dari course lain bisa dipasangkan dengan ID course pada URL. URL nested tidak lagi menjamin relasi parent-child yang dinyatakannya.

**Langkah perbaikan:** Aktifkan `scopeBindings()` dan terima kedua model pada action agar binding assignment dilakukan melalui relasi course. Otorisasi view menggunakan course yang sudah dibinding dari route.

**Sebelum:**

```php
Route::get('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

public function show(Assignment $assignment)
{
  Gate::authorize('view', $assignment->course);
```

**Sesudah:**

```php
Route::get('/courses/{course}/assignments/{assignment}', [AssignmentController::class, 'show'])
  ->scopeBindings()
  ->name('assignments.show');

public function show(Course $course, Assignment $assignment)
{
  Gate::authorize('view', $course);
```

## 5. Alias Middleware `role` Didaftarkan di Berkas yang Salah

**Akibat:** Aplikasi menggunakan konfigurasi middleware Laravel 12 di `bootstrap/app.php`; alias yang hanya ditambahkan ke `app/Http/Kernel.php` tidak terdaftar pada router aktif. Route dengan `role:dosen` atau `role:mahasiswa` berisiko gagal saat dijalankan.

**Langkah perbaikan:** Daftarkan alias melalui konfigurasi `withMiddleware()` di `bootstrap/app.php` dan hapus pendaftaran yang tidak digunakan dari Kernel.

**Sebelum:**

```php
// app/Http/Kernel.php
protected $middlewareAliases = [
  'role' => \App\Http\Middleware\RoleMiddleware::class,
];

// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
  //
})
```

**Sesudah:**

```php
// bootstrap/app.php
use App\Http\Middleware\RoleMiddleware;

->withMiddleware(function (Middleware $middleware) {
  $middleware->alias([
    'role' => RoleMiddleware::class,
  ]);
})
```

## 6. Nama Route Daftar Course Bentrok Antar Peran

**Akibat:** Kedua route memiliki nama `courses.index`. Pemanggilan `route('courses.index')` menjadi ambigu secara semantik dan dapat mengarah ke endpoint dengan middleware peran yang salah, menghasilkan akses ditolak atau tautan yang tidak sesuai.

**Langkah perbaikan:** Beri nama berbeda untuk route dosen dan mahasiswa, lalu sesuaikan tautan, redirect, dan test agar memilih route berdasarkan peran.

**Sebelum:**

```php
Route::get('/lecturer/courses', [CourseController::class, 'index'])->middleware('role:dosen')->name('courses.index');
Route::get('/student/courses', [CourseController::class, 'index'])->middleware('role:mahasiswa')->name('courses.index');
```

**Sesudah:**

```php
Route::get('/lecturer/courses', [CourseController::class, 'index'])->middleware('role:dosen')->name('lecturer.courses.index');
Route::get('/student/courses', [CourseController::class, 'index'])->middleware('role:mahasiswa')->name('student.courses.index');
```

## 7. Route Destruktif Menggunakan `GET`

**Akibat:** `GET` dirancang untuk membaca data dan dapat dipicu tanpa tindakan eksplisit, misalnya oleh prefetch browser atau tautan pihak ketiga. Menggunakannya untuk menghapus materi membuka peluang penghapusan tak disengaja atau CSRF.

**Langkah perbaikan:** Ubah endpoint menjadi `DELETE` dan panggil melalui form `POST` dengan token CSRF serta method spoofing Laravel.

**Sebelum:**

```php
Route::get('/materials/{material}/delete', [MaterialController::class, 'destroy'])->name('materials.destroy');

<a href="{{ route('materials.destroy', $material) }}">Hapus</a>
```

**Sesudah:**

```php
Route::delete('/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

<form action="{{ route('materials.destroy', $material) }}" method="POST">
  @csrf
  @method('DELETE')
  <button type="submit">Hapus</button>
</form>
```

## Verifikasi

- `php artisan route:list --except-vendor`: nama daftar course unik, route hapus materi memakai `DELETE`, dan route terkait terdaftar.
- `php artisan test`: 18 test lulus dengan 39 assertion. Test keamanan membuktikan pengguna lain menerima `403` untuk kedua download IDOR, pemilik/mahasiswa terdaftar tetap dapat mengunduh, serta nested binding dan route destruktif berperilaku benar.