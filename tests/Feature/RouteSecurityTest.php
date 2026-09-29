<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RouteSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_details_require_authentication(): void
    {
        $course = Course::factory()->create();

        $this->get(route('courses.show', $course))
            ->assertRedirect(route('login'));
    }

    public function test_submission_download_is_limited_to_authorized_users(): void
    {
        Storage::fake('local');
        $owner = User::factory()->create(['role' => 'mahasiswa']);
        $attacker = User::factory()->create(['role' => 'mahasiswa']);
        $assignment = Assignment::factory()->create();
        $path = 'submissions/private.pdf';
        Storage::disk('local')->put($path, 'private submission');
        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => $owner->id,
            'file_path' => $path,
            'original_name' => 'submission.pdf',
            'file_size' => 18,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->actingAs($attacker)
            ->get(route('submissions.download', $submission))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('submissions.download', $submission))
            ->assertOk();
    }

    public function test_material_download_is_limited_to_course_members(): void
    {
        Storage::fake('local');
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);
        $outsider = User::factory()->create(['role' => 'mahasiswa']);
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $course->students()->attach($student);
        $path = 'materials/private.pdf';
        Storage::disk('local')->put($path, 'course material');
        $material = Material::create([
            'course_id' => $course->id,
            'uploaded_by' => $lecturer->id,
            'title' => 'Private material',
            'type' => 'file',
            'file_path' => $path,
            'original_name' => 'material.pdf',
        ]);

        $this->actingAs($outsider)
            ->get(route('materials.download', $material))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('materials.download', $material))
            ->assertOk();
    }

    public function test_nested_assignment_route_is_scoped_to_its_course(): void
    {
        $course = Course::factory()->create();
        $assignment = Assignment::factory()->create();

        $this->actingAs($course->lecturer)
            ->get(route('assignments.show', [
                'course' => $course,
                'assignment' => $assignment,
            ]))
            ->assertNotFound();
    }

    public function test_role_middleware_is_registered_and_course_route_names_are_unique(): void
    {
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $student = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($lecturer)->get(route('lecturer.courses.index'))->assertOk();
        $this->actingAs($student)->get(route('lecturer.courses.index'))->assertForbidden();
        $this->assertNotSame(route('lecturer.courses.index'), route('student.courses.index'));
    }

    public function test_material_deletion_uses_delete_method(): void
    {
        $lecturer = User::factory()->create(['role' => 'dosen']);
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $material = Material::create([
            'course_id' => $course->id,
            'uploaded_by' => $lecturer->id,
            'title' => 'Removable material',
            'type' => 'link',
            'external_url' => 'https://example.test/material',
        ]);

        $this->actingAs($lecturer)
            ->delete(route('materials.destroy', $material))
            ->assertRedirect(route('courses.show', $course));

        $this->get('/materials/'.$material->id.'/delete')->assertNotFound();
    }
}
