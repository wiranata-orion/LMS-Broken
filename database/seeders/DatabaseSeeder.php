<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin LMS',
            'email' => 'admin@kampuslms.test',
            'password' => 'password',
            'nim_nip' => '198001012005011001',
            'role' => 'admin',
        ]);

        $dosen1 = User::create([
            'name' => 'Dr. Aris Sugiharto',
            'email' => 'dosen@kampuslms.test',
            'password' => 'password',
            'nim_nip' => '198203152008121002',
            'role' => 'dosen',
        ]);

        $dosen2 = User::create([
            'name' => 'Budi Santoso, M.T.',
            'email' => 'budi@kampuslms.test',
            'password' => 'password',
            'nim_nip' => '198504202010121003',
            'role' => 'dosen',
        ]);

        $dosen3 = User::create([
            'name' => 'Siti Aminah, Ph.D.',
            'email' => 'siti@kampuslms.test',
            'password' => 'password',
            'nim_nip' => '198809102014042001',
            'role' => 'dosen',
        ]);

        $mahasiswa1 = User::create([
            'name' => 'Budi Mahasiswa',
            'email' => 'mahasiswa@kampuslms.test',
            'password' => 'password',
            'nim_nip' => '24060122120001',
            'role' => 'mahasiswa',
        ]);

        Course::create([
            'code' => 'SI2514024',
            'name' => 'Pemrograman Web',
            'sks' => 3,
            'lecturer_id' => $dosen1->id,
            'status' => 'active',
            'description' => 'Mata kuliah dasar pengembangan aplikasi web modern menggunakan Laravel 12.',
        ]);

        Course::create([
            'code' => 'SI2514025',
            'name' => 'Basis Data Lanjut',
            'sks' => 3,
            'lecturer_id' => $dosen2->id,
            'status' => 'active',
            'description' => 'Pembahasan indexing, transaksi, dan optimisasi query database relational.',
        ]);

        Course::create([
            'code' => 'SI2514026',
            'name' => 'Keamanan Informasi',
            'sks' => 2,
            'lecturer_id' => $dosen3->id,
            'status' => 'draft',
            'description' => 'Konsep dasar enkripsi, OWASP Top 10, dan pencegahan XSS/CSRF.',
        ]);
    }
}
