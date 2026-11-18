<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\WeeklyMaterial;
use App\Models\Student;
use App\Models\SessionReport;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Pengguna (1 Dosen, 1 Co. Asprak, 3 Asprak)
        $dosen = User::create([
            'name' => 'Dr. Ir. Budi Santoso, M.Kom.',
            'username' => 'dosen_pemdas',
            'nim_nip' => '198005122005011002',
            'role' => 'dosen',
            'password' => Hash::make('password123'),
        ]);

        $koor = User::create([
            'name' => 'Raka Pratama (Co. Asprak)',
            'username' => 'koor_pemdas',
            'nim_nip' => '235150700111001',
            'role' => 'koor_asprak',
            'password' => Hash::make('password123'),
        ]);

        $asprak1 = User::create([
            'name' => 'Muhammad Hikmal Al-Ghifary',
            'username' => 'hikmal_asprak',
            'nim_nip' => '255150700111030',
            'role' => 'asprak',
            'password' => Hash::make('password123'),
        ]);

        $asprak2 = User::create([
            'name' => 'Siti Aminah',
            'username' => 'siti_asprak',
            'nim_nip' => '245150700111015',
            'role' => 'asprak',
            'password' => Hash::make('password123'),
        ]);

        $asprak3 = User::create([
            'name' => 'Dimas Anggara',
            'username' => 'dimas_asprak',
            'nim_nip' => '245150700111022',
            'role' => 'asprak',
            'password' => Hash::make('password123'),
        ]);

        // 2. Buat Mata Kuliah: Pemrograman Dasar
        $pemdas = Course::create([
            'code' => 'COM60010',
            'name' => 'Pemrograman Dasar',
            'rps_file' => null,
        ]);

        // 3. Buat Jadwal Kelas (TI-A, TI-B, TI-C)
        $jadwalA = CourseSchedule::create([
            'course_id' => $pemdas->id,
            'dosen_id' => $dosen->id,
            'class_name' => 'TI-A',
            'day' => 'Senin',
            'start_time' => '08:00:00',
            'end_time' => '09:40:00',
            'room' => 'G1.1',
        ]);

        $jadwalB = CourseSchedule::create([
            'course_id' => $pemdas->id,
            'dosen_id' => $dosen->id,
            'class_name' => 'TI-B',
            'day' => 'Senin',
            'start_time' => '08:00:00',
            'end_time' => '09:40:00',
            'room' => 'G1.3',
        ]);

        $jadwalC = CourseSchedule::create([
            'course_id' => $pemdas->id,
            'dosen_id' => $dosen->id,
            'class_name' => 'TI-C',
            'day' => 'Rabu',
            'start_time' => '10:00:00',
            'end_time' => '11:40:00',
            'room' => 'G1.5',
        ]);

        // Plotting Asprak ke Jadwal
        $jadwalA->aspraks()->attach([$asprak1->id, $asprak2->id]);
        $jadwalB->aspraks()->attach([$asprak1->id, $asprak3->id]);
        $jadwalC->aspraks()->attach([$asprak2->id, $asprak3->id]);

        // 4. Buat Materi Pekanan (Pekan 1 - 4)
        $pekan1 = WeeklyMaterial::create([
            'course_id' => $pemdas->id,
            'week_number' => 1,
            'topic' => 'Flowchart',
            'material_file' => null,
        ]);

        $pekan2 = WeeklyMaterial::create([
            'course_id' => $pemdas->id,
            'week_number' => 2,
            'topic' => 'Seleksi Kondisi',
            'material_file' => null,
        ]);

        $pekan3 = WeeklyMaterial::create([
            'course_id' => $pemdas->id,
            'week_number' => 3,
            'topic' => 'Perulangan',
            'material_file' => null,
        ]);

        WeeklyMaterial::create([
            'course_id' => $pemdas->id,
            'week_number' => 4,
            'topic' => 'Live Coding',
            'material_file' => null,
        ]);

        // 5. Buat Data Mahasiswa Praktikan untuk TI-A, TI-B, dan TI-C
        $mahasiswaA = [];
        for ($i = 1; $i <= 5; $i++) {
            $mahasiswaA[] = Student::create([
                'nim' => '26515070011100' . $i,
                'name' => 'Praktikan TI-A ' . $i,
                'class_name' => 'TI-A',
            ]);
        }

        for ($i = 1; $i <= 5; $i++) {
            Student::create([
                'nim' => '26515070022200' . $i,
                'name' => 'Praktikan TI-B ' . $i,
                'class_name' => 'TI-B',
            ]);
            Student::create([
                'nim' => '26515070033300' . $i,
                'name' => 'Praktikan TI-C ' . $i,
                'class_name' => 'TI-C',
            ]);
        }

        // 6. Buat Data Simulasi Berita Acara pada Kelas TI-A
        // Pekan 1: Sudah Saved
        $rep1 = SessionReport::create([
            'schedule_id' => $jadwalA->id,
            'material_id' => $pekan1->id,
            'session_date' => '2026-11-02',
            'present_asprak' => 'Muhammad Hikmal Al-Ghifary (255150700111030), Siti Aminah (245150700111015)',
            'notes_for_dosen' => 'Seluruh praktikan berhasil menginstal IDE dengan lancar.',
            'issues' => 'Tidak ada kendala.',
            'is_read_by_dosen' => true,
            'status' => 'saved',
        ]);

        // Pekan 2: Sudah Saved
        $rep2 = SessionReport::create([
            'schedule_id' => $jadwalA->id,
            'material_id' => $pekan2->id,
            'session_date' => '2026-11-09',
            'present_asprak' => 'Muhammad Hikmal Al-Ghifary (255150700111030)',
            'notes_for_dosen' => 'Materi if-else dan switch-case tersampaikan seluruhnya.',
            'issues' => 'Proyektor sempat berkedip 5 menit di awal sesi.',
            'is_read_by_dosen' => false, // Belum dibaca dosen (memicu notif merah di dashboard dosen)
            'status' => 'saved',
        ]);

        // Pekan 3: Belum Dibuat / Terlewat (memicu notif merah di sidebar Asprak)
        $rep3 = SessionReport::create([
            'schedule_id' => $jadwalA->id,
            'material_id' => $pekan3->id,
            'session_date' => '2026-11-16',
            'present_asprak' => null,
            'notes_for_dosen' => null,
            'issues' => null,
            'is_read_by_dosen' => true,
            'status' => 'pending',
        ]);

        // Isi data kehadiran praktikan TI-A (Mahasiswa ke-5 dibuat Alfa di Pekan 1 & 2 agar persentase < 80%)
        foreach ([$rep1, $rep2, $rep3] as $idx => $report) {
            foreach ($mahasiswaA as $mIndex => $mhs) {
                $status = 'H';
                if ($mIndex === 4 && $idx < 2) {
                    $status = 'A'; // Hadir 0 dari 2 pertemuan saved (0% -> Merah < 80%)
                }
                Attendance::create([
                    'report_id' => $report->id,
                    'student_id' => $mhs->id,
                    'status' => $status,
                ]);
            }
        }
    }
}