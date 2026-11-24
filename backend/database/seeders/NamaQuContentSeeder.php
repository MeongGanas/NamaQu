<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Konten asli NamaQu dari dokumen "SEEDER KOLEKTIF" (Google Drive, Nov 2026).
 * Aman dijalankan berulang & di production: data yang sudah ada tidak ditimpa.
 */
class NamaQuContentSeeder extends Seeder
{
    public function run(): void
    {
        $team = [
            ['Nadia Tasya Kusumawati, S.T.', 'Nanas', 'Community Leader', true],
            ['Dinar Rheina Rassendria, S.T.', null, 'Vice Community Leader', true],
            ['Shania Chairunnisa Santoso, S.Kom.', null, 'Treasurer', false],
            ['Wulan Sari', null, 'Secretary', false],
            ['Rafi Amanu Surya Laksana', null, 'Media Creative', false],
            ['Fira Novi Puspita Saputri', null, 'Program Strategy', false],
        ];

        foreach ($team as $i => [$name, $nickname, $role, $founder]) {
            TeamMember::firstOrCreate(['name' => $name], [
                'nickname' => $nickname,
                'role' => $role,
                'is_founder' => $founder,
                'sort_order' => $i,
                'is_published' => true,
            ]);
        }

        $testimonials = [
            [
                'Muhammad Zidan Atsal', 'zidan.jpg',
                'Beberapa kali ikut program-program NamaQu seru banget, beda dari volunteering-volunteering biasanya, bener-bener bisa aplikasiin ilmu yang kita punya ke program-program di NamaQu, dan tiap pertemuan selalu ada inovasi-inovasi program yang aku bahkan ga pernah kepikiran sebelumnya. Pesannya semoga NamaQu bisa terus memberikan manfaat ke anak-anak di kemvil yaa dan membantu mereka jadi orang-orang hebat di masa depan! Sukses selalu, NamaQu!',
            ],
            [
                'Ryu Rafli Ananta', 'ryu.jpg',
                'Selamaa ikut kegiatan NamaQu pastii selaluu senang karna kegiatann yang dibuatt samaa temen-temen seru dan bermanfaat bangett buat adek-adeknyaa. Pesan akuu smogaa dari kitaa temen-temen NamaQu maupun eksternal bisaa selalu bantuu dan sharing ilmu ataupun pengalaman kita yang bermanfaat buat adek-adeknyaa.',
            ],
            [
                'Insani Kamil Hamdallah', 'insani.jpg',
                'Bersyukur pernah jadi bagian dari komunitas ini, punya metode pendekatan penuh kasih dan inovatif yang ga bikin anak-anak bosan. Lancar selalu dan panjang umur, NamaQu.',
            ],
        ];

        foreach ($testimonials as [$name, $photo, $message]) {
            $path = 'testimonials/'.$photo;
            if (! Storage::disk('public')->exists($path)) {
                Storage::disk('public')->put($path, file_get_contents(__DIR__.'/assets/testimonials/'.$photo));
            }

            Testimonial::firstOrCreate(['name' => $name], [
                'show_full_name' => true,
                'activity_name' => 'Volunteer NamaQu',
                'message' => $message,
                'photo' => $path,
                'is_published' => true,
            ]);
        }
    }
}
