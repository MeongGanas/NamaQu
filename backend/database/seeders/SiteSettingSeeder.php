<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/** Isi awal dari rekap meeting 20 Okt 2026. Pengurus bisa mengubahnya di admin. */
class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'tagline' => 'Sebut Namamu, Gapai Cita-Citamu!',
            'hero_title' => 'Belajar bareng, tumbuh bareng.',
            'hero_subtitle' => 'NamaQu adalah komunitas sosial & pendidikan di Keputih, Surabaya, tempat anak-anak SD belajar bersama kakak-kakak volunteer setiap hari Minggu.',
            'about' => '<p>NamaQu merupakan komunitas sosial dan pendidikan yang berfokus pada kegiatan belajar bersama anak-anak SD. Kegiatan dilaksanakan di basecamp Keputih dengan melibatkan volunteer yang direkrut secara terbuka sesuai kebutuhan kegiatan.</p><p>Komunitas ini berawal dari ide Nadia Tasya (Nanas) dan Reyna, berbekal pengalaman mengelola event dan project kampus.</p>',
            'basecamp_address' => 'Keputih, Sukolilo, Surabaya',
            'maps_url' => null,
            'schedule_text' => 'Setiap hari Minggu',
            'instagram' => 'namaqu.id',
            'tiktok' => 'namaqu.id',
            'linkedin' => 'https://www.linkedin.com/company/komunitas-namaqu/',
            'email' => null,
            'contacts' => [
                ['name' => 'Kak Ulen', 'purpose' => 'Narahubung', 'whatsapp' => '6285171539202'],
            ],
            'collab_intro' => 'NamaQu terbuka untuk berkolaborasi dengan instansi, organisasi, komunitas, maupun pihak lain yang ingin ikut mendukung pendidikan anak-anak SD di Keputih. Berikut ketentuan kerja sama (partnership) dengan Komunitas NamaQu.',
            // Dari "TNC NamaQu.pdf" — Terms and Condition Partnership.
            'collab_terms' => [
                ['title' => 'Bentuk Kerja Sama', 'optional' => false, 'note' => null,
                    'content' => '<p>Bentuk kerja sama dari kolaborator adalah melakukan kegiatan yang akan diadakan di <strong>basecamp NamaQu</strong>.</p>'],
                ['title' => 'Penggunaan Logo', 'optional' => false, 'note' => 'Butuh logo? Tinggal reach out ke tim kita ya 😉',
                    'content' => '<p>Kolaborator diharapkan <strong>mencantumkan logo NamaQu</strong> pada seluruh media publikasi kegiatan, seperti poster, banner, dan konten media sosial.</p>'],
                ['title' => 'Komitmen Jadwal & Lokasi', 'optional' => false, 'note' => null,
                    'content' => '<p>Kolaborator diharapkan menjaga <strong>komitmen terkait tempat, tanggal, dan waktu</strong> pelaksanaan yang telah disepakati bersama.</p>'],
                ['title' => 'Perubahan dan Pembatalan Kegiatan', 'optional' => false, 'note' => 'Perubahan dan pembatalan maksimal dilakukan 7 hari sebelum hari pelaksanaan.',
                    'content' => '<p>Diharapkan tidak melakukan perubahan dan pembatalan kegiatan secara sepihak dalam waktu mendadak.</p>'],
                ['title' => 'Kontribusi Operasional', 'optional' => false, 'note' => null,
                    'content' => '<p>Kolaborator bersedia <strong>memberikan donasi (tanpa nominal yang ditentukan)</strong>, yang akan digunakan untuk kebutuhan seperti kebersihan dan perlengkapan belajar mengajar.</p>'],
                ['title' => 'Pemberian Cenderamata', 'optional' => true, 'note' => null,
                    'content' => '<p>Apabila kolaborator ingin memberikan cenderamata seperti sertifikat, plakat, atau bentuk penghargaan lainnya, dimohon mencantumkan nama komunitas <strong>“Komunitas NamaQu”</strong>.</p>'],
                ['title' => 'Menjaga Nama Baik Komunitas', 'optional' => false, 'note' => null,
                    'content' => '<p>Kolaborator diharapkan turut <strong>menjaga nama baik Komunitas NamaQu</strong> serta tidak melakukan tindakan yang dapat merugikan atau mencoreng reputasi bersama, baik selama maupun setelah kegiatan berlangsung.</p>'],
                ['title' => 'Force Majeure', 'optional' => false, 'note' => null,
                    'content' => '<p>Dalam kondisi di luar kendali (seperti cuaca ekstrem, bencana alam, atau situasi darurat lainnya), jadwal kegiatan dapat disesuaikan kembali berdasarkan kesepakatan bersama melalui komunikasi yang baik antara kedua belah pihak.</p>'],
                ['title' => 'Dress Code & Attitude', 'optional' => false, 'note' => null,
                    'content' => '<p>Demi menciptakan lingkungan yang nyaman dan positif, khususnya bagi anak-anak, kolaborator diharapkan <strong>mengenakan pakaian yang sopan serta menjaga sikap yang ramah</strong> dan profesional selama kegiatan berlangsung. Kolaborator juga <strong>dilarang merokok</strong> maupun <strong>menggunakan vape</strong> di lingkungan belajar adik-adik.</p>'],
            ],
            'collab_contact' => ['name' => 'Kak Ulen', 'whatsapp' => '6285171539202'],
        ];

        $existing = SiteSetting::pluck('key')->all();
        SiteSetting::put(array_diff_key($defaults, array_flip($existing)));
    }
}
