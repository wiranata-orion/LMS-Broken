<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamp('due_at');
            $table->unsignedSmallInteger('max_score')->default(100);
            $table->timestamps();

            $table->index(['course_id', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
