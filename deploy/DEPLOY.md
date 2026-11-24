# Deploy NamaQu

```
Pengunjung ──► Vercel (Next.js, frontend)  ──cache──►  halaman tetap tampil walau STB offline
                     │  fetch data (jarang, di-cache per jam + revalidate saat admin menyimpan)
                     ▼
Cloudflare ──tunnel──► STB Armbian ─ nginx :8090 ─► /namaqu → Laravel (PHP-FPM) ─► MariaDB
                                                    /app-lain → (nanti)
```

- API publik  : `https://api.meongganas.my.id/namaqu/api/...`
- Panel admin : `https://api.meongganas.my.id/namaqu/admin`
- Upload foto : disimpan di HDD `/mnt/hdd/namaqu/uploads`

---

## 0. Simpan kode ke GitHub

Vercel butuh repo GitHub, dan server akan `git pull` dari sana.

```bash
cd D:\coding\namaqu
git init && git add . && git commit -m "NamaQu: backend Laravel + frontend Next.js"
# buat repo (private boleh) di GitHub lalu:
git remote add origin git@github.com:<user>/namaqu.git && git push -u origin main
```

## 1. Persiapan server (sekali saja)

### 1a. Swap zram (RAM 1,7 GB tanpa swap rawan OOM saat `composer install`)

```bash
sudo apt install -y zram-tools
echo -e "ALGO=zstd\nPERCENT=60" | sudo tee /etc/default/zramswap
sudo systemctl restart zramswap && free -h     # baris Swap harus > 0
```

### 1b. Ekstensi PHP yang dibutuhkan Laravel + Filament

```bash
sudo apt install -y php8.5-mysql php8.5-intl php8.5-zip php8.5-gd php8.5-mbstring \
                    php8.5-xml php8.5-bcmath php8.5-curl
php -m | grep -E 'intl|pdo_mysql|zip|gd|mbstring|bcmath'
```

### 1c. Tuning PHP-FPM hemat RAM

Ikuti [`php-fpm-pool.conf`](php-fpm-pool.conf) (mode `ondemand`, max 4 proses, opcache 64 MB), lalu
`sudo systemctl restart php8.5-fpm`.

### 1d. Database

```bash
sudo mariadb -e "CREATE DATABASE namaqu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  CREATE USER 'namaqu'@'localhost' IDENTIFIED BY '<password-kuat>';
  GRANT ALL ON namaqu.* TO 'namaqu'@'localhost'; FLUSH PRIVILEGES;"
```

## 2. Pasang backend

```bash
sudo mkdir -p /var/www && sudo chown $USER /var/www
git clone git@github.com:<user>/namaqu.git /var/www/namaqu
cd /var/www/namaqu/backend

composer install --no-dev --optimize-autoloader
cp ../deploy/backend.env.production.example .env      # lalu edit: DB_PASSWORD, FRONTEND_*
php artisan key:generate
php artisan migrate --force --seed                    # di production hanya mengisi teks awal halaman
php artisan make:filament-user                        # akun admin untuk pengurus
```

### Upload foto ke HDD (bukan ke eMMC yang sisa 1,8 GB)

```bash
sudo mkdir -p /mnt/hdd/namaqu/uploads
sudo -u www-data touch /mnt/hdd/namaqu/uploads/.test && echo "HDD bisa ditulis www-data"
rm -rf storage/app/public && ln -s /mnt/hdd/namaqu/uploads storage/app/public
php artisan storage:link
```

> HDD-nya NTFS (`fuseblk`). Kalau tes tulis di atas gagal, tambahkan `uid=33,gid=33,umask=002`
> (33 = www-data) pada opsi mount `/mnt/hdd` di `/etc/fstab`, lalu `sudo mount -o remount /mnt/hdd`.
> **Database jangan ditaruh di NTFS** — biarkan MariaDB di `/var/lib/mysql`.

```bash
bash /var/www/namaqu/deploy/deploy.sh      # optimize, filament:optimize, izin folder, reload fpm
```

## 3. nginx (satu domain untuk semua backend)

```bash
sudo mkdir -p /var/www/api-root
sudo ln -s /var/www/namaqu/backend/public /var/www/api-root/namaqu
sudo cp /var/www/namaqu/deploy/nginx/api.meongganas.my.id.conf /etc/nginx/sites-available/
sudo ln -s /etc/nginx/sites-available/api.meongganas.my.id.conf /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
curl -s localhost:8090/namaqu/api/site | head -c 200        # harus JSON
```

App backend lain nanti cukup: buat symlink baru di `/var/www/api-root/<nama>` dan salin blok
`location ^~ /namaqu/` dengan nama baru.

> Konfigurasi ini sudah diuji di Docker (`docker compose --profile subpath up -d fpm nginx`
> → `http://localhost:8090/namaqu/admin`): API, aset Filament, Livewire, cookie, dan login berjalan.

## 4. Cloudflare Tunnel

`cloudflared` sudah jalan sebagai service. Tambahkan hostname publik:

- **Kalau tunnel dikelola dari dashboard** (Zero Trust → Networks → Tunnels → tunnel kamu → *Public Hostname* → Add):
  subdomain `api`, domain `meongganas.my.id`, service `HTTP` → `localhost:8090`.
- **Kalau pakai `config.yml`**, tambahkan sebelum aturan `http_status:404`:
  ```yaml
  - hostname: api.meongganas.my.id
    service: http://localhost:8090
  ```
  lalu `sudo systemctl restart cloudflared`.

Cek dari luar: `https://api.meongganas.my.id/namaqu/up` → 200.

## 5. Frontend di Vercel

1. Vercel → *Add New Project* → import repo → **Root Directory: `frontend`**.
2. Environment Variables (lihat [`frontend/.env.example`](../frontend/.env.example)):
   - `API_URL` dan `NEXT_PUBLIC_API_URL` = `https://api.meongganas.my.id/namaqu/api`
   - `NEXT_PUBLIC_SITE_URL` = domain Vercel kamu
   - `REVALIDATE_SECRET` = string acak panjang (`openssl rand -hex 32`)
3. Deploy. **Saat build, API di STB harus online** — Next.js mengambil data untuk pre-render.
4. Di server, isi `.env` backend:
   `FRONTEND_REVALIDATE_URL=https://<domain-vercel>/api/revalidate` dan
   `FRONTEND_REVALIDATE_SECRET=<sama dengan REVALIDATE_SECRET>`, lalu `php artisan optimize`.

Sekarang setiap pengurus menyimpan sesuatu di admin, website di Vercel langsung diperbarui.
Tanpa itu pun cache tetap diperbarui otomatis tiap ±1 jam.

## 6. Perawatan

- **Update kode**: `git push` di laptop → di server `bash /var/www/namaqu/deploy/deploy.sh`.
  Frontend ter-deploy otomatis oleh Vercel.
- **Backup DB harian ke HDD** (`crontab -e`):
  ```
  0 3 * * * mysqldump -unamaqu -p'<password>' namaqu | gzip > /mnt/hdd/namaqu/backup-$(date +\%a).sql.gz
  ```
  (menyimpan 7 file bergilir: Senin–Minggu.)
- **Jangan jalankan `php artisan test` di server** tanpa perlu — test sudah dikunci agar menolak
  berjalan selain di SQLite in-memory, tapi tetap tidak ada gunanya di production.
