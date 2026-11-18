<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('session_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('course_schedules')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('weekly_materials')->onDelete('cascade');
            $table->date('session_date');
            $table->text('present_asprak')->nullable();
            $table->text('notes_for_dosen')->nullable();
            $table->text('issues')->nullable();
            $table->boolean('is_read_by_dosen')->default(true);
            $table->enum('status', ['pending', 'saved'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_reports');
    }
};
