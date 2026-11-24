<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\LearningModule;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Konten dari Google Drive NamaQu (folder "Profil Komunitas NamaQu" & "Profil Founder dan Pengurus"):
 * "Komunitas NamaQu.pdf" (Grand Design) dan "5W + 1H PROFIL KOMUNITAS NAMAQU.pdf".
 *
 * Diterapkan SEKALI (ditandai lewat site_settings.content_version), supaya menjalankan seeder ulang
 * tidak menimpa perubahan yang dibuat pengurus di admin. Naikkan VERSION untuk menerapkan isi baru.
 */
class DriveContentSeeder extends Seeder
{
    public const VERSION = '2026-11-25-drive-profil';

    public function run(): void
    {
        $current = SiteSetting::allAsArray()['content_version'] ?? null;
        if ($current === self::VERSION) {
            $this->command?->info('Konten Drive sudah pernah diterapkan ('.self::VERSION.'), dilewati.');

            return;
        }

        $this->settings();
        $this->team();
        $this->modules();
        $this->activities();

        SiteSetting::put(['content_version' => self::VERSION]);
    }

    private function settings(): void
    {
        SiteSetting::put([
            'tagline' => 'Sebut Namamu, Gapai Cita-Citamu!',
            'hero_subtitle' => 'NamaQu adalah komunitas sosial yang mendampingi anak-anak usia 6–12 tahun di wilayah marginal Surabaya untuk belajar, berkreasi, dan berani bermimpi bersama kakak-kakak relawan.',
            'basecamp_address' => 'Keputih Tegal Timur Baru, Surabaya, Jawa Timur',
            'schedule_text' => 'Setiap akhir pekan (Sabtu atau Minggu)',
            'about' => implode('', [
                '<p><strong>“Namaku”</strong> adalah kata pertama yang diucap dengan penuh percaya diri oleh anak-anak ketika ditanya siapa mereka. NamaQu hadir agar mereka tak sekadar menyebut nama, tapi juga menyebut mimpinya dengan bangga: <em>“Namaku Andi, cita-citaku jadi dokter.”</em> <em>“Namaku Siti, aku ingin jadi insinyur.”</em></p>',
                '<p>NamaQu merupakan komunitas sosial yang bergerak di bidang pendidikan dan pemberdayaan anak-anak marginal di Surabaya. NamaQu hadir sebagai ruang belajar yang mengintegrasikan pendidikan akademik, pengenalan teknologi, pengembangan kreativitas, serta pendampingan karakter — ruang belajar yang aman, menyenangkan, dan inklusif.</p>',
                '<h3>Bagaimana NamaQu berawal</h3>',
                '<p>NamaQu berawal dari keresahan terhadap belum meratanya akses pendidikan bagi anak-anak di wilayah marginal. Gagasan ini menguat setelah para penggagas menjalankan program pengabdian masyarakat SOPHIA (Social Philanthropic Action) HMTB UNAIR pada 2024 di Keputih Tegal Timur Baru, dan menemukan anak-anak yang masih membutuhkan pendampingan belajar, termasuk kemampuan dasar membaca dan menulis.</p>',
                '<p>Salah satu pengalaman yang paling membekas adalah ketika seorang orang tua menitipkan harapan agar anaknya mendapat pendampingan belajar karena masih kesulitan membaca. Dari situ, pengabdian kampus itu dilanjutkan menjadi komunitas sosial yang berkelanjutan.</p>',
                '<h3>Cara kami belajar</h3>',
                '<p>Kegiatan rutin diawali dengan doa dan ice breaking atau senam bersama, lalu dilanjutkan dengan pembelajaran melalui modul, praktikum, permainan edukatif, dan kegiatan penutup. Perkembangan anak dievaluasi lewat hasil pengerjaan modul dan pengamatan relawan, lalu dirangkum dalam rapor tahunan untuk orang tua.</p>',
            ]),
            'vision' => 'Mewujudkan generasi Indonesia yang unggul dalam ilmu, teknologi, dan karakter melalui pendidikan yang menyeluruh, terjangkau, dan menyentuh hati.',
            'missions' => [
                'Menyediakan ruang belajar kreatif berbasis fun learning untuk anak-anak marginal di berbagai wilayah.',
                'Memperkenalkan teknologi dan sains dasar dengan cara menyenangkan dan membumi, sehingga bisa dipahami bahkan oleh anak di wilayah pinggiran sekalipun.',
                'Memberikan pendampingan psikososial berbasis nilai-nilai kemanusiaan, empati, dan pendekatan yang hangat.',
                'Mengembangkan titik binaan di berbagai wilayah Indonesia, khususnya daerah 3T, yang jarang dijangkau kegiatan sosial formal.',
                'Mendorong relawan muda untuk mengimplementasikan ilmu perkuliahannya secara nyata ke masyarakat sebagai bentuk pengabdian berkelanjutan.',
                'Menjadi komunitas sosial yang berkelanjutan, berdampak, dan kolaboratif, serta terbuka terhadap inovasi dan sinergi dari berbagai latar belakang.',
            ],
            'programs' => [
                [
                    'code' => 'QULEARN',
                    'name' => 'NamaQu Learning',
                    'description' => 'Penguatan kemampuan akademik dan dasar lewat calistung, matematika logika, dan bahasa Inggris dasar. Modul disusun mandiri oleh relawan dan disesuaikan dengan kemampuan anak dalam tiga kelompok belajar.',
                ],
                [
                    'code' => 'QRAFT',
                    'name' => 'NamaQu Craft',
                    'description' => 'Belajar lewat praktik (hands-on) untuk mengasah kreativitas, keterampilan, dan rasa ingin tahu: eksperimen rangkaian listrik seri & paralel, konduktor & isolator, hingga permainan coding dasar dengan laptop.',
                ],
                [
                    'code' => 'QIDS',
                    'name' => 'NamaQu Kids Day',
                    'description' => 'Program tahunan memperingati Hari Anak Nasional: kegiatan edukatif, pengenalan sains & teknologi, pentas kreativitas, serta melibatkan orang tua dalam perkembangan anak.',
                ],
            ],
            'impact' => [
                ['value' => '30–40', 'label' => 'anak didampingi'],
                ['value' => '20+', 'label' => 'kegiatan terlaksana'],
                ['value' => '5+', 'label' => 'kolaborasi'],
                ['value' => '6', 'label' => 'penggagas'],
            ],
            'milestones' => [
                ['date' => '2024', 'title' => 'Pengabdian SOPHIA HMTB UNAIR di Keputih Tegal Timur Baru menjadi awal gagasan NamaQu'],
                ['date' => '8 Agustus 2025', 'title' => 'Komunitas NamaQu resmi didirikan'],
                ['date' => '6 September 2025', 'title' => 'Kegiatan perdana bersama adik-adik'],
                ['date' => 'Ke depan', 'title' => 'PetualanganQu: membawa modul & kegiatan edukatif ke desa-desa terpencil'],
            ],
            'collab_partners' => [
                'Duta FST UNAIR',
                'Bidik Misi Mengajar ITS',
                'RUSA PWK ITS',
                'Social Project BEM SOSLING UNAIR',
            ],
        ]);
    }

    private function team(): void
    {
        // Menurut profil 5W+1H, keenam pengurus adalah penggagas/pendiri NamaQu.
        TeamMember::query()->update(['is_founder' => true]);

        $this->attachPhoto('Fira Novi Puspita Saputri', 'team/fira.jpg', __DIR__.'/assets/team/fira.jpg');
    }

    private function attachPhoto(string $name, string $path, string $source): void
    {
        $member = TeamMember::where('name', $name)->first();
        if (! $member || $member->photo) {
            return;
        }

        $disk = Storage::disk(config('filesystems.media'));
        if (! $disk->exists($path)) {
            $disk->put($path, file_get_contents($source));
        }
        $member->update(['photo' => $path]);
    }

    private function modules(): void
    {
        // Diturunkan dari deskripsi program QULEARN & QRAFT. Tingkat & kesulitan adalah perkiraan awal —
        // silakan disesuaikan pengurus di admin.
        $modules = [
            [
                'title' => 'Calistung: Membaca, Menulis & Berhitung',
                'level' => 'kecil', 'category' => 'calistung', 'difficulty' => 1,
                'target' => 'Usia 6–8 tahun, belum lancar membaca',
                'summary' => 'Bagian dari QULEARN: penguatan kemampuan dasar membaca, menulis, dan berhitung.',
                'objectives' => ['Mengenal huruf dan membaca suku kata', 'Menulis kata dan kalimat sederhana', 'Berhitung dasar dengan benda konkret'],
            ],
            [
                'title' => 'Matematika Logika',
                'level' => 'sedang', 'category' => 'logika_matematika', 'difficulty' => 2,
                'target' => 'Usia 8–10 tahun, sudah lancar berhitung dasar',
                'summary' => 'Bagian dari QULEARN: melatih penalaran, pemecahan masalah, dan berpikir logis lewat permainan.',
                'objectives' => ['Melatih penalaran dan pemecahan masalah', 'Mengenali pola dan urutan', 'Menyelesaikan soal cerita sederhana'],
            ],
            [
                'title' => 'Bahasa Inggris Dasar',
                'level' => 'sedang', 'category' => 'bahasa_inggris', 'difficulty' => 1,
                'target' => 'Usia 8–12 tahun',
                'summary' => 'Bagian dari QULEARN: pengenalan bahasa Inggris dasar yang menyenangkan.',
                'objectives' => ['Mengenal kosakata sehari-hari dalam bahasa Inggris', 'Berani memperkenalkan diri dalam bahasa Inggris'],
            ],
            [
                'title' => 'Konduktor & Isolator',
                'level' => 'sedang', 'category' => 'sains_teknologi', 'difficulty' => 2,
                'target' => 'Usia 8–12 tahun',
                'summary' => 'Bagian dari QRAFT: mengenal bahan yang bisa dan tidak bisa menghantarkan listrik lewat percobaan.',
                'objectives' => ['Membedakan konduktor dan isolator', 'Melakukan percobaan sederhana dengan aman'],
            ],
            [
                'title' => 'Rangkaian Listrik Seri & Paralel',
                'level' => 'besar', 'category' => 'sains_teknologi', 'difficulty' => 3,
                'target' => 'Usia 10–12 tahun',
                'summary' => 'Bagian dari QRAFT: eksperimen merangkai listrik seri dan paralel secara langsung.',
                'objectives' => ['Memahami perbedaan rangkaian seri dan paralel', 'Merangkai rangkaian sederhana bersama kakak relawan'],
            ],
            [
                'title' => 'Coding Dasar dengan Laptop',
                'level' => 'besar', 'category' => 'game_coding', 'difficulty' => 2,
                'target' => 'Usia 10–12 tahun, sudah lancar membaca',
                'summary' => 'Bagian dari QRAFT: mengenal logika pemrograman lewat permainan coding dasar menggunakan laptop.',
                'objectives' => ['Mengenal urutan perintah (algoritma sederhana)', 'Menyelesaikan tantangan permainan coding'],
            ],
        ];

        foreach ($modules as $i => $m) {
            LearningModule::firstOrCreate(['title' => $m['title']], $m + [
                'content' => '<p>'.$m['summary'].'</p><p>Kegiatan diawali doa dan ice breaking, dilanjutkan pembelajaran dengan modul, praktik, dan permainan edukatif, lalu ditutup bersama.</p>',
                'sort_order' => $i,
                'is_published' => true,
            ]);
        }
    }

    private function activities(): void
    {
        Activity::firstOrCreate(['title' => 'Kegiatan Perdana NamaQu'], [
            'type' => 'event',
            'held_on' => '2025-09-06',
            'summary' => 'Kegiatan pertama Komunitas NamaQu bersama adik-adik di Keputih Tegal Timur Baru, Surabaya.',
            'description' => '<p>Setelah resmi berdiri pada 8 Agustus 2025, NamaQu menggelar kegiatan perdananya pada 6 September 2025 di Keputih Tegal Timur Baru — wilayah yang dipilih setelah penelusuran beberapa kawasan marginal di Surabaya dan disambut positif oleh warga.</p>',
            'is_published' => true,
        ]);
    }
}
