<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Dokumentasi kegiatan dari Google Drive NamaQu (folder "Dokumentasi Kegiatan" → 2025, 2026, QIDS 2026).
 *
 * Foto terpilih (maks. 6 per kegiatan, sudah dikecilkan) SUDAH diunggah ke Cloudinary di
 * namaqu/activities/photos/<slug>-NN. Seeder ini hanya membuat data kegiatan + path fotonya;
 * album lengkap (termasuk video) ditautkan ke folder Drive masing-masing.
 * Tanggal diambil dari EXIF foto.
 *
 * Diterapkan sekali (site_settings.docs_content_version) supaya tidak menimpa suntingan admin.
 */
class DriveDocumentationSeeder extends Seeder
{
    public const VERSION = '2026-11-25-drive-dokumentasi';

    /** Sumber salinan foto untuk development lokal (disk "public"). */
    private const SOURCE = 'https://res.cloudinary.com/mwxtzmlg/image/upload/namaqu/';

    private const DRIVE = 'https://drive.google.com/drive/folders/';

    public function run(): void
    {
        if ((SiteSetting::allAsArray()['docs_content_version'] ?? null) === self::VERSION) {
            $this->command?->info('Dokumentasi Drive sudah pernah diterapkan ('.self::VERSION.'), dilewati.');

            return;
        }

        foreach ($this->activities() as $a) {
            $photos = [];
            for ($i = 1; $i <= $a['photos']; $i++) {
                $photos[] = sprintf('activities/photos/%s-%02d.jpg', $a['slug'], $i);
            }
            $this->ensureLocalCopies($photos);

            Activity::firstOrCreate(['slug' => $a['slug']], [
                'title' => $a['title'],
                'type' => $a['type'],
                'held_on' => $a['held_on'],
                'summary' => $a['summary'],
                'description' => $a['description'],
                'photos' => $photos,
                'videos' => [],
                'album_url' => self::DRIVE.$a['drive'],
                'is_published' => true,
            ]);
        }

        SiteSetting::put(['docs_content_version' => self::VERSION]);
    }

    /** Di produksi (Cloudinary) foto sudah ada. Di lokal, unduh salinan bila belum ada. */
    private function ensureLocalCopies(array $paths): void
    {
        $diskName = config('filesystems.media');
        if ($diskName === 'cloudinary' || app()->runningUnitTests()) {
            return;
        }

        $disk = Storage::disk($diskName);
        foreach ($paths as $path) {
            if ($disk->exists($path)) {
                continue;
            }
            try {
                $res = Http::timeout(30)->get(self::SOURCE.$path);
                if ($res->successful()) {
                    $disk->put($path, $res->body());
                }
            } catch (Throwable $e) {
                $this->command?->warn("Gagal mengunduh $path: ".$e->getMessage());
            }
        }
    }

    private function activities(): array
    {
        return [
            [
                'slug' => 'belajar-bersama-14-desember-2025',
                'title' => 'Belajar Bersama Akhir Tahun',
                'type' => 'weekly',
                'held_on' => '2025-12-14',
                'photos' => 6,
                'drive' => '1G4y2kW4-9Me6khmIS1CK14J4I_0tP-0N',
                'summary' => 'Adik-adik belajar dengan lembar kerja dan prakarya bersama kakak-kakak relawan di basecamp Keputih.',
                'description' => '<p>Kegiatan belajar rutin menutup tahun 2025. Adik-adik mengerjakan lembar kerja dan prakarya dalam kelompok, didampingi kakak-kakak relawan NamaQu, lalu ditutup dengan foto bersama.</p>',
            ],
            [
                'slug' => 'game-coding-codemonkey-januari-2026',
                'title' => 'Game Coding: Belajar Logika lewat CodeMonkey',
                'type' => 'weekly',
                'held_on' => '2026-01-25',
                'photos' => 6,
                'drive' => '1t1-31-vmYOqmJGijw6XdDv2M-K5xEd8U',
                'summary' => 'Adik-adik mencoba permainan coding CodeMonkey di laptop — menyusun urutan perintah untuk menyelesaikan tantangan.',
                'description' => '<p>Bagian dari program <strong>QRAFT</strong>: adik-adik berkelompok di depan laptop dan memainkan <em>CodeMonkey</em>, menyusun perintah seperti <em>move</em> dan <em>turn</em> untuk menuntun karakter menyelesaikan tantangan. Cara seru mengenal logika dan urutan (sequencing) dalam pemrograman.</p>',
            ],
            [
                'slug' => 'ramadan-ceria-namaqu-2026',
                'title' => 'Ramadan Ceria Bersama NamaQu',
                'type' => 'event',
                'held_on' => '2026-02-22',
                'photos' => 5,
                'drive' => '1dH8HvWtGHyRoKxKdJF73QPGgqWqsYThB',
                'summary' => 'Kebersamaan di bulan Ramadan: adik-adik menerima bingkisan bernama masing-masing dan berkumpul bersama relawan menjelang berbuka.',
                'description' => '<p>Di bulan Ramadan, NamaQu berkumpul bersama adik-adik menjelang waktu berbuka. Setiap anak menerima bingkisan dengan namanya masing-masing — sesuai semangat <em>“Sebut Namamu, Gapai Cita-Citamu”</em>.</p>',
            ],
            [
                'slug' => 'belajar-di-taman-april-2026',
                'title' => 'Belajar di Luar Kelas: Jalan-jalan ke Taman',
                'type' => 'weekly',
                'held_on' => '2026-04-26',
                'photos' => 4,
                'drive' => '12yZBOTFWidbNKpjGwkplGguC-p5ApIw0',
                'summary' => 'Kegiatan di luar basecamp: adik-adik bermain dan belajar di taman kota bersama kakak-kakak relawan.',
                'description' => '<p>Sesekali belajar tidak harus di dalam ruangan. Adik-adik diajak ke taman kota untuk bermain, mengamati lingkungan, dan menerima buku serta bingkisan dari kakak-kakak relawan.</p>',
            ],
            [
                'slug' => 'bermain-dan-belajar-mei-2026',
                'title' => 'Bermain & Belajar di Taman Bermain',
                'type' => 'weekly',
                'held_on' => '2026-05-17',
                'photos' => 4,
                'drive' => '1Ny3jCDO9UB_yLdPVXJ7qRnw-xSRTe8rx',
                'summary' => 'Adik-adik belajar sambil bermain di taman, ditemani kakak-kakak relawan.',
                'description' => '<p>Kegiatan pekan ini diadakan di taman bermain: adik-adik bermain bersama, mengerjakan aktivitas ringan, dan menikmati camilan bersama kakak-kakak relawan.</p>',
            ],
            [
                'slug' => 'kumpul-bersama-di-taman-juni-2026',
                'title' => 'Kumpul Bersama di Taman',
                'type' => 'event',
                'held_on' => '2026-06-07',
                'photos' => 4,
                'drive' => '1RMzg1nDgzXk-9WdI7s_WixQpYw5YUJeP',
                'summary' => 'Puluhan adik-adik dan relawan NamaQu berkumpul di taman untuk bermain dan berkegiatan bersama.',
                'description' => '<p>Kegiatan bersama berskala besar di taman: adik-adik dan kakak-kakak relawan bermain, berkegiatan, dan berfoto bersama.</p>',
            ],
            [
                'slug' => 'qids-2026-pekan-1',
                'title' => 'QIDS 2026 · NamaQu Kids Day (Pekan 1)',
                'type' => 'event',
                'held_on' => '2026-06-28',
                'photos' => 2,
                'drive' => '1sk6c_Yt49Btxy9McaB6BtkPGj1a3qq3y',
                'summary' => 'Pekan pertama rangkaian QIDS 2026, program tahunan NamaQu memperingati Hari Anak Nasional.',
                'description' => '<p><strong>QIDS (NamaQu Kids Day)</strong> adalah program tahunan NamaQu untuk memperingati Hari Anak Nasional lewat rangkaian kegiatan edukatif, kreativitas, dan pengenalan sains & teknologi. Pekan pertama QIDS 2026 diikuti adik-adik bersama para relawan.</p>',
            ],
            [
                'slug' => 'belajar-bersama-20-september-2026',
                'title' => 'Belajar Bersama di Basecamp',
                'type' => 'weekly',
                'held_on' => '2026-09-20',
                'photos' => 3,
                'drive' => '1R5r3h1vd15whXqwWQQEEScZ3AeKSDrZl',
                'summary' => 'Kegiatan belajar rutin bersama adik-adik di basecamp, ditutup dengan foto bersama tiap kelompok.',
                'description' => '<p>Kegiatan belajar rutin akhir pekan di basecamp. Adik-adik belajar dalam kelompok sesuai tingkat, lalu berfoto bersama kakak pendamping masing-masing.</p>',
            ],
            [
                'slug' => 'namaqu-di-rri-pro-2-surabaya',
                'title' => 'NamaQu di RRI Pro 2 Surabaya',
                'type' => 'event',
                'held_on' => '2026-09-20',
                'photos' => 2,
                'drive' => '1KHpYgbVjqJOPPsi_4YldX_LlRjXRT4vn',
                'summary' => 'Pengurus NamaQu berbagi cerita tentang komunitas dalam siaran talkshow di RRI Pro 2 Surabaya.',
                'description' => '<p>NamaQu mendapat kesempatan bercerita tentang perjalanan komunitas, program, dan ajakan menjadi relawan dalam siaran talkshow di RRI Pro 2 Surabaya.</p>',
            ],
        ];
    }
}
