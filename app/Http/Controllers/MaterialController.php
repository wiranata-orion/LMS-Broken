<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public function store(Request $request, Course $course)
    {
        Gate::authorize('create', Material::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:file,link',
            'external_url' => 'required_if:type,link|nullable|url',
            'file' => 'required_if:type,file|nullable|file|mimes:pdf,docx,pptx,zip|max:10240', // Max 10MB (in KB)
        ]);

        $filePath = null;
        $originalName = null;
        $fileSize = null;
        $mimeType = null;

        if ($request->hasFile('file') && $validated['type'] === 'file') {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();

            // Store with secure random filename in private storage
            $hashedName = Str::random(40).'.'.$file->getClientOriginalExtension();
            $filePath = $file->storeAs('materials', $hashedName, 'local');
        }

        $course->materials()->create([
            'uploaded_by' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'file_path' => $filePath,
            'original_name' => $originalName,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'external_url' => $validated['type'] === 'link' ? $validated['external_url'] : null,
        ]);

        return redirect()->route('courses.show', $course)->with('success', 'Materi berhasil diunggah.');
    }

    public function download(Material $material)
    {
        Gate::authorize('download', $material);

        if ($material->type !== 'file' || ! $material->file_path || ! Storage::disk('local')->exists($material->file_path)) {
            abort(404, 'Berkas materi tidak ditemukan.');
        }

        return Storage::disk('local')->download($material->file_path, $material->original_name);
    }

    public function destroy(Material $material)
    {
        Gate::authorize('delete', $material);

        $course = $material->course;

        // Delete physical file from storage disk
        if ($material->file_path && Storage::disk('local')->exists($material->file_path)) {
            Storage::disk('local')->delete($material->file_path);
        }

        $material->delete();

        return redirect()->route('courses.show', $course)->with('success', 'Materi berhasil dihapus.');
    }
}
