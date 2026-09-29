<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AssignmentController extends Controller
{
    public function store(Request $request, Course $course)
    {
        Gate::authorize('create', Course::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_at' => 'required|date',
            'max_score' => 'required|integer|min:1|max:100',
        ]);

        $course->assignments()->create($validated);

        return redirect()->route('courses.show', $course)->with('success', 'Tugas berhasil dibuat.');
    }

    public function show(Course $course, Assignment $assignment)
    {
        Gate::authorize('view', $course);

        $assignment->load(['course', 'submissions.student', 'submissions.grade']);

        return view('assignments.show', compact('assignment'));
    }

    public function destroy(Assignment $assignment)
    {
        Gate::authorize('update', $assignment->course);

        $course = $assignment->course;
        $assignment->delete();

        return redirect()->route('courses.show', $course)->with('success', 'Tugas berhasil dihapus.');
    }
}
