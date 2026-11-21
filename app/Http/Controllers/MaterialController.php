<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\WeeklyMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    // 1. Halaman RPS & Bahan Ajar untuk Asisten Praktikum
    public function asprakIndex(Request $request)
    {
        $user = Auth::user();
        // Ambil daftar mata kuliah unik yang diajar asprak
        $courses = $user->schedulesAsAsprak()
            ->with('course')
            ->get()
            ->pluck('course')
            ->unique('id')
            ->values();

        $selectedCourseId = $request->query('course_id');
        $selectedCourse = null;
        $materials = collect();
        $classNames = '-';

        if ($selectedCourseId) {
            $selectedCourse = Course::find($selectedCourseId);
            $materials = WeeklyMaterial::where('course_id', $selectedCourseId)
                ->orderBy('week_number')
                ->get();

            // Ambil kelas yang diajar asprak pada mata kuliah ini
            $classes = $user->schedulesAsAsprak()
                ->where('course_id', $selectedCourseId)
                ->pluck('class_name')
                ->unique()
                ->toArray();

            $classNames = !empty($classes) ? implode(', ', $classes) : '-';
        }

        return view('asprak.rps', compact(
            'courses',
            'selectedCourseId',
            'selectedCourse',
            'materials',
            'classNames'
        ));
    }

    // 2. Halaman RPS & Bahan Ajar untuk Koordinator Asprak
    public function koorIndex(Request $request)
    {
        $courses = Course::all();
        $selectedCourseId = $request->query('course_id');
        $selectedCourse = null;
        $materials = collect();

        if ($selectedCourseId) {
            $selectedCourse = Course::find($selectedCourseId);
            $materials = WeeklyMaterial::where('course_id', $selectedCourseId)
                ->orderBy('week_number')
                ->get();
        }

        return view('koor.rps', compact(
            'courses',
            'selectedCourseId',
            'selectedCourse',
            'materials'
        ));
    }

    // Upload / Update File RPS oleh Co. Asprak
    public function uploadRps(Request $request, Course $course)
    {
        $request->validate([
            'rps_file' => 'required|mimes:pdf|max:10240', // Maks 10MB PDF
        ]);

        if ($course->rps_file && Storage::disk('public')->exists($course->rps_file)) {
            Storage::disk('public')->delete($course->rps_file);
        }

        $path = $request->file('rps_file')->store('rps', 'public');
        $course->update(['rps_file' => $path]);

        return redirect()->route('koor.rps', ['course_id' => $course->id])
            ->with('success', 'File RPS berhasil diunggah.');
    }

    // Tambah Materi Pekanan Baru oleh Co. Asprak
    public function storeMaterial(Request $request)
    {
        $validated = $request->validate([
            'course_id'     => 'required|exists:courses,id',
            'week_number'   => 'required|integer|min:1',
            'topic'         => 'required|string|max:255',
            'material_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('material_file')) {
            $path = $request->file('material_file')->store('materials', 'public');
        }

        WeeklyMaterial::create([
            'course_id'     => $validated['course_id'],
            'week_number'   => $validated['week_number'],
            'topic'         => $validated['topic'],
            'material_file' => $path,
        ]);

        return redirect()->route('koor.rps', ['course_id' => $validated['course_id']])
            ->with('success', 'Materi pekan baru berhasil ditambahkan.');
    }

    // Edit Materi & Ganti File PDF oleh Co. Asprak
    public function updateMaterial(Request $request, WeeklyMaterial $material)
    {
        $validated = $request->validate([
            'week_number'   => 'required|integer|min:1',
            'topic'         => 'required|string|max:255',
            'material_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $path = $material->material_file;
        if ($request->hasFile('material_file')) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $path = $request->file('material_file')->store('materials', 'public');
        }

        $material->update([
            'week_number'   => $validated['week_number'],
            'topic'         => $validated['topic'],
            'material_file' => $path,
        ]);

        return redirect()->route('koor.rps', ['course_id' => $material->course_id])
            ->with('success', 'Materi Pekan ' . $material->week_number . ' berhasil diperbarui.');
    }

    // Hapus Materi Pekanan oleh Co. Asprak
    public function destroyMaterial(WeeklyMaterial $material)
    {
        $courseId = $material->course_id;

        if ($material->material_file && Storage::disk('public')->exists($material->material_file)) {
            Storage::disk('public')->delete($material->material_file);
        }

        $material->delete();

        return redirect()->route('koor.rps', ['course_id' => $courseId])
            ->with('success', 'Materi berhasil dihapus.');
    }
}