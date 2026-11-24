#!/usr/bin/env bash
# Update backend NamaQu di server. Jalankan:  bash /var/www/namaqu/deploy/deploy.sh
set -euo pipefail

APP=/var/www/namaqu/backend
cd "$APP"

echo "==> Ambil kode terbaru"
git -C /var/www/namaqu pull --ff-only

echo "==> Dependensi PHP (tanpa paket dev)"
composer install --no-dev --optimize-autoloader --no-interaction --no-progress

echo "==> Migrasi database"
php artisan migrate --force

echo "==> Cache konfigurasi, route, view, ikon & komponen Filament"
php artisan optimize
php artisan filament:optimize

echo "==> Izin folder"
sudo chown -R www-data:www-data storage bootstrap/cache

echo "==> Reload PHP-FPM (bersihkan opcache)"
sudo systemctl reload php8.5-fpm

echo "Selesai. Cek: https://api.meongganas.my.id/namaqu/up"
