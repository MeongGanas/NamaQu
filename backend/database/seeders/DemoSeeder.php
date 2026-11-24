<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\LearningModule;
use App\Models\Recruitment;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Data contoh untuk development lokal.
 * Akun admin lokal: admin@namaqu.test / password (JANGAN dipakai di server).
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@namaqu.test'], ['name' => 'Admin NamaQu', 'password' => 'password']);

        Recruitment::firstOrCreate(['slug' => 'volunteer-minggu-ini'], [
            'type' => 'weekly',
            'title' => 'Volunteer Minggu Ini',
            'summary' => 'Temani adik-adik belajar calistung & logika di basecamp Keputih.',
            'requirements' => ['Follow & like postingan Instagram @namaqu.id', 'Balas Story open recruitment', 'Hadir tepat waktu pukul 07.45'],
            'activity_date' => now()->next('Sunday'),
            'activity_time' => '08.00 - 11.00 WIB',
            'location' => 'Basecamp NamaQu, Keputih, Surabaya',
            'registration_closes_at' => now()->next('Saturday'),
            'registration_method' => 'instagram',
            'external_url' => 'https://www.instagram.com/namaqu.id/',
            'quota' => 10,
            'is_published' => true,
        ]);

        Recruitment::firstOrCreate(['slug' => 'volunteer-hari-anak'], [
            'type' => 'event',
            'title' => 'Volunteer Perayaan Hari Anak',
            'summary' => 'Bantu kami menyiapkan lomba, panggung, dan games untuk 40+ anak.',
            'description' => '<p>Perayaan Hari Anak bersama adik-adik NamaQu. Volunteer akan dibagi ke divisi acara, perlengkapan, konsumsi, dan dokumentasi.</p>',
            'requirements' => ['Mahasiswa/umum usia 17+', 'Bisa hadir technical meeting H-3', 'Bersedia mengikuti seluruh rangkaian acara'],
            'activity_date' => now()->addWeeks(3),
            'activity_time' => '07.00 - 13.00 WIB',
            'location' => 'Basecamp NamaQu, Keputih, Surabaya',
            'registration_opens_at' => now()->subDays(2),
            'registration_closes_at' => now()->addWeeks(2),
            'registration_method' => 'form',
            'quota' => 25,
            'is_published' => true,
        ]);

        $modules = [
            ['Mengenal Huruf & Suku Kata', 'kecil', 'calistung', 1, 'Usia 6-7 tahun, belum lancar membaca', ['Mengenal huruf vokal & konsonan', 'Membaca suku kata KV']],
            ['Penjumlahan dengan Benda', 'kecil', 'calistung', 1, 'Kelas 1 SD', ['Menjumlahkan bilangan 1-20 dengan benda konkret']],
            ['Pola & Urutan', 'sedang', 'logika_matematika', 2, 'Kelas 2-3 SD', ['Menemukan pola gambar & bilangan', 'Melanjutkan barisan sederhana']],
            ['Perkalian Lewat Permainan', 'sedang', 'logika_matematika', 2, 'Kelas 3 SD', ['Memahami perkalian sebagai penjumlahan berulang']],
            ['Teka-teki Logika', 'besar', 'logika_matematika', 3, 'Kelas 4-6 SD', ['Bernalar dengan petunjuk', 'Menyelesaikan soal cerita']],
            ['Coding Pertama dengan Blok', 'besar', 'game_coding', 2, 'Kelas 4-6 SD, sudah lancar membaca', ['Mengenal urutan perintah (sequence)', 'Mengenal perulangan (loop)']],
        ];
        foreach ($modules as $i => [$title, $level, $cat, $diff, $target, $obj]) {
            LearningModule::firstOrCreate(['title' => $title], [
                'level' => $level, 'category' => $cat, 'difficulty' => $diff, 'target' => $target,
                'objectives' => $obj, 'sort_order' => $i,
                'summary' => 'Materi '.strtolower(LearningModule::CATEGORIES[$cat]).' untuk '.strtolower(LearningModule::LEVELS[$level]).'.',
                'content' => '<p>Kegiatan dibuka dengan ice breaking, lalu adik-adik belajar dalam kelompok kecil didampingi volunteer.</p>',
                'external_url' => $cat === 'game_coding' ? 'https://code.org' : null,
                'is_published' => true,
            ]);
        }

        foreach ([
            ['Belajar Minggu: Calistung Ceria', 'weekly', 7],
            ['Belajar Minggu: Pola & Bilangan', 'weekly', 14],
            ['Belajar Minggu: Coding Tanpa Komputer', 'weekly', 21],
            ['Perayaan Hari Anak 2026', 'event', 120],
        ] as [$title, $type, $daysAgo]) {
            Activity::firstOrCreate(['title' => $title], [
                'type' => $type,
                'held_on' => now()->subDays($daysAgo),
                'summary' => 'Keseruan adik-adik NamaQu belajar bareng kakak volunteer.',
                'description' => '<p>Hari ini adik-adik belajar dalam tiga kelompok sesuai tingkat: kecil, sedang, dan besar.</p>',
                'is_published' => true,
            ]);
        }

        // Testimoni dengan nama disamarkan (untuk menguji fitur persetujuan nama).
        Testimonial::firstOrCreate(['name' => 'Rina Amelia'], [
            'show_full_name' => false, 'institution' => 'ITS', 'activity_name' => 'Volunteer Mingguan',
            'message' => 'Contoh testimoni (data demo lokal): senang lihat adik-adik semangat belajar!',
            'is_published' => true,
        ]);
    }
}
