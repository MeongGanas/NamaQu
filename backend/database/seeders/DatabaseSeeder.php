<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Teks awal halaman (aman dijalankan di production; tidak menimpa yang sudah diisi).
        $this->call(SiteSettingSeeder::class);

        // Pengurus & testimoni asli (dari Google Drive NamaQu).
        $this->call(NamaQuContentSeeder::class);

        // Data contoh + akun admin lokal, HANYA untuk development.
        if (app()->isLocal()) {
            $this->call(DemoSeeder::class);
        }
    }
}
