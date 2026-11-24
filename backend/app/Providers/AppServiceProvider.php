<?php

namespace App\Providers;

use App\Filesystem\CloudinaryAdapter;
use App\Models\Activity;
use Cloudinary\Cloudinary;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use App\Models\LearningModule;
use App\Models\Recruitment;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Di production semua trafik lewat Cloudflare (HTTPS), meski nginx lokal menerima HTTP.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Driver disk "cloudinary" (config/filesystems.php).
        Storage::extend('cloudinary', function ($app, array $config) {
            $adapter = new CloudinaryAdapter(new Cloudinary((string) $config['cloudinary_url']), $config['folder'] ?? 'namaqu');

            return new FilesystemAdapter(new Filesystem($adapter, $config), $adapter, $config);
        });

        // Setiap konten berubah di admin -> minta frontend (Vercel) memperbarui cache-nya.
        foreach ([Activity::class, LearningModule::class, Recruitment::class, SiteSetting::class, TeamMember::class, Testimonial::class] as $model) {
            $model::saved(fn () => $this->revalidateFrontend());
            $model::deleted(fn () => $this->revalidateFrontend());
        }
    }

    private function revalidateFrontend(): void
    {
        $url = config('services.frontend.revalidate_url');
        $secret = config('services.frontend.revalidate_secret');

        if (! $url || ! $secret) {
            return;
        }

        // dijalankan setelah respons terkirim & hanya sekali per request
        defer(function () use ($url, $secret) {
            try {
                Http::timeout(5)->withHeaders(['x-revalidate-secret' => $secret])->post($url);
            } catch (\Throwable $e) {
                Log::warning('Gagal revalidate frontend: '.$e->getMessage());
            }
        }, 'revalidate-frontend');
    }
}
