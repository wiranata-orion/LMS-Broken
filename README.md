# Temuan Masalah

## 1. Urutan migrasi salah

Masalah utama ada di urutan file migrasi:

- `database/migrations/2026_01_01_000001_create_courses_table.php`
- `database/migrations/2026_01_01_000002_create_users_table.php`

Pada `courses`, kolom `lecturer_id` dibuat dengan relasi ke tabel `users`:

```php
$table->foreignId('lecturer_id')->constrained('users')->cascadeOnDelete();
```

Tabel `users` seharusnya sudah ada sebelum migrasi `courses` dijalankan. Karena saat ini `create_courses_table` berada sebelum `create_users_table`, membuat migrasi gagal saat menjalankan foreign key, karena `users` belum dibuat.

### Solusi
Nama file migrasi harus diubah agar urutannya sesuai karena `courses` menggunakan` Foregin Key` dari `users`.

---

## 2. Unique composite pertama hilang di `course_user`

Pada `database/migrations/2026_01_01_000003_create_course_user_table.php`

Tabel `course_user` saat ini hanya membuat kolom `course_id` dan `user_id`.

```php
$table->foreignId('course_id')->constrained()->cascadeOnDelete();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
```

### Dampak
- Satu user dapat mendaftar ke satu course lebih dari satu kali.

### Solusi
Tambahkan unique composite:

```php
$table->unique(['course_id', 'user_id']);
```

---

## 3. Unique composite kedua hilang di `submissions`

Pada `database/migrations/2026_01_01_000006_create_submissions_table.php`

Tabel `submissions` saat ini dibuat tanpa constraint unik gabungan pada:

- `assignment_id`
- `user_id`

Padahal secara logika, satu user biasanya hanya boleh mengirim satu submission per assignment.

### Dampak
- Duplikasi submission untuk assignment yang sama oleh user yang sama.

### Solusi yang disarankan
Tambahkan:

```php
$table->unique(['assignment_id', 'user_id']);
```

---

## 4. `onDelete` yang keliru di `materials`

Pada `database/migrations/2026_01_01_000004_create_materials_table.php`

Baris yang bermasalah:

```php
$table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
```

Relasi ini menggunakan `restrictOnDelete()`, yang berarti `user` tidak dapat dihapus selama material tersebut masih mereferensikan `user` tersebut. Material mempunyai relasi `uploaded_by` kepada user melalui `Material`

### Solusi
- `cascadeOnDelete()` jika materi ikut terhapus saat user dihapus, atau
- `nullOnDelete()` jika data materi tetap ada tetapi `uploaded_by` dihapus menjadi `NULL`.

---

## 5. Model `User` memakai `$guarded = []`

Pada `app/Models/User.php`

Dalam model ini terdapat:

```php
protected $guarded = [];
```

Ini berbahaya karena semua atribut menjadi mass assignable secara terbuka. Artinya, request yang masuk bisa langsung mengisi field apa pun tanpa kontrol.

### Dampak
- Risiko mass assignment.
- Sulit untuk menjaga validasi dan keamanan data.

### Solusi
Seperti:
```php
protected $guarded = ['name', 'email', 'password', 'nim_nip', 'role'];
```

---

## 6. `down()` di migrasi `materials` kosong

Pada `database/migrations/2026_01_01_000004_create_materials_table.php`

Pada migrasi `materials`, method `down()` saat ini kosong:

```php
public function down(): void
{
}
```

- Rollback migrasi tidak bekerja dengan benar.
- Saat menjalankan `migrate:rollback`, tabel `materials` tidak akan dihapus.

### Solusi 
Tambahkan:
```php
Schema::dropIfExists('materials');
```

---

## 7. Controller memakai `$request->all()`

Pada `app/Http/Controllers/UserController.php`

Pada `store()` dan `update()` terdapat:

```php
User::create($request->all());
$user->update($request->all());
```

Penggunaan `$request->all()` tidak aman dan tidak disarankan karena:
- semua input dipaksa masuk ke model,
- tidak ada validasi.
- dapat memasukkan field yang seharusnya tidak diproses.

### Solusi
Gunakan `validated()` atau field tertentu saja.

Contoh:

```php
$data = $request->validated();
User::create($data);

$user->update($request->validated());
```

Jika validasi belum dibuat, minimal gunakan:

```php
User::create($request->only(['name', 'email', 'password', 'nim_nip', 'role']));
```