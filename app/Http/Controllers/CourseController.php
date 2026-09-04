<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    private array $courses = [
        [
            'id' => 1,
            'code' => 'SI2514024',
            'name' => 'Pemrograman Web',
            'sks' => 3,
            'lecturer' => 'Dr. Aris Sugiharto',
            'status' => 'active',
            'description' => 'Mata kuliah dasar pengembangan aplikasi web modern menggunakan Laravel 12.'
        ],
        [
            'id' => 2,
            'code' => 'SI2514025',
            'name' => 'Basis Data Lanjut',
            'sks' => 3,
            'lecturer' => 'Budi Santoso, M.T.',
            'status' => 'active',
            'description' => 'Pembahasan indexing, transaksi, dan optimisasi query database relational.'
        ],
        [
            'id' => 3,
            'code' => 'SI2514026',
            'name' => 'Keamanan Informasi',
            'sks' => 2,
            'lecturer' => 'Siti Aminah, Ph.D.',
            'status' => 'draft',
            'description' => 'Konsep dasar enkripsi, OWASP Top 10, dan pencegahan XSS/CSRF.'
        ],
    ];

    /**
     * Ambil data mata kuliah dari session (atau default array jika session kosong)
     */
    private function getCourses(): array
    {
        return session()->get('courses', $this->courses);
    }

    public function index()
    {
        // Ambil semua courses dari session
        $courses = $this->getCourses();

        // Filter di controller: hanya tampilkan yang statusnya 'active'
        $activeCourses = array_filter($courses, function ($course) {
            return ($course['status'] ?? 'active') === 'active';
        });

        return view('courses.index', ['courses' => $activeCourses]);
    }

    public function create()
    {
        return view('courses.create');
    }

    public function store(Request $request)
    {
        $courses = $this->getCourses();

        // Buat data baru dari input form
        $newCourse = [
            'id' => count($courses) > 0 ? max(array_column($courses, 'id')) + 1 : 1,
            'code' => $request->code,
            'name' => $request->name,
            'sks' => (int) $request->sks,
            'lecturer' => $request->lecturer,
            'status' => 'active',
            'description' => $request->description ?? '-',
        ];

        // Masukkan data baru dan simpan ke session
        $courses[] = $newCourse;
        session()->put('courses', $courses);

        return redirect()->route('courses.index')->with('success', 'Mata kuliah berhasil ditambahkan');
    }

    public function show($id)
    {
        $course = collect($this->getCourses())->firstWhere('id', (int) $id);

        if (!$course) {
            abort(404);
        }

        return view('courses.show', compact('course'));
    }

    public function destroy($id)
    {
        $courses = $this->getCourses();

        // Hapus elemen array yang id-nya sesuai
        $courses = array_filter($courses, function ($course) use ($id) {
            return $course['id'] != (int) $id;
        });

        // Simpan sisa data kembali ke session
        session()->put('courses', array_values($courses));

        return redirect()->route('courses.index')->with('success', 'Mata kuliah berhasil dihapus');
    }
}