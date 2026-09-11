<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CourseController extends Controller
{
<<<<<<< HEAD
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
=======
    public function index()
    {
        $courses = Course::with('lecturer')->where('status', 'active')->get();
        return view('courses.index', compact('courses'));
>>>>>>> 4e0b1bfeb5804382e2efb504d81775042cb1a1fe
    }

    public function create()
    {
        $lecturers = User::where('role', 'dosen')->get();
        return view('courses.create', compact('lecturers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:courses,code',
            'name' => 'required',
            'sks' => 'required|numeric',
            'lecturer_id' => 'required|exists:users,id',
            'description' => 'nullable',
        ]);

        Course::create($validated);

        return redirect()->route('courses.index')->with('success', 'Mata kuliah berhasil ditambahkan');
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
<<<<<<< HEAD
        $course = collect($this->getCourses())->firstWhere('id', (int) $id);

=======
        $course = Course::with(['lecturer', 'materials', 'assignments'])->find($id);
>>>>>>> 4e0b1bfeb5804382e2efb504d81775042cb1a1fe
        if (!$course) {
            abort(404);
        }

        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $lecturers = User::where('role', 'dosen')->get();
        return view('courses.edit', compact('course', 'lecturers'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'code' => 'required|unique:courses,code,' . $course->id,
            'name' => 'required',
            'sks' => 'required|numeric',
            'lecturer_id' => 'required|exists:users,id',
            'description' => 'nullable',
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')->with('success', 'Mata kuliah berhasil diperbarui');
    }

    public function destroy($id)
    {
<<<<<<< HEAD
        $courses = $this->getCourses();

        // Hapus elemen array yang id-nya sesuai
        $courses = array_filter($courses, function ($course) use ($id) {
            return $course['id'] != (int) $id;
        });

        // Simpan sisa data kembali ke session
        session()->put('courses', array_values($courses));
=======
        $course = Course::findOrFail($id);
        $course->delete();
>>>>>>> 4e0b1bfeb5804382e2efb504d81775042cb1a1fe

        return redirect()->route('courses.index')->with('success', 'Mata kuliah berhasil dihapus');
    }
}