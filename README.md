# NamaQu

Website komunitas sosial & pendidikan NamaQu (Keputih, Surabaya) — dari rekap meeting 20 Okt 2026.

| Folder | Isi |
|---|---|
| `backend/` | Laravel 13 + Filament 5: API publik & panel admin pengurus (jalan di STB Armbian) |
| `frontend/` | Next.js 16 (App Router, Cache Components): website publik (di-host di Vercel) |
| `deploy/` | Panduan & konfigurasi server: nginx, PHP-FPM, `.env` production, skrip deploy |
| `docker/` | Image untuk development lokal |

## Fitur (sesuai meeting)

1. **Open recruitment volunteer** — mingguan (via Instagram Story) & kegiatan besar (formulir di website, dengan kuota & batas waktu). Pendaftar masuk ke admin, bisa ditandai diterima/ditolak & langsung di-WhatsApp.
2. **Modul pembelajaran** — tingkat Kecil/Sedang/Besar × Calistung, Logika & Matematika, Game Coding; lengkap dengan target, kesulitan, tujuan belajar, dokumentasi.
3. **Dokumentasi kegiatan** — galeri foto, video YouTube/Instagram, dikelompokkan per jenis & bulan.
4. **Biodata pengurus** — founder & pengurus beserta peran dan tanggung jawab.
5. **Kesan & pesan volunteer** — dengan opsi menyamarkan nama sesuai persetujuan.
6. **Kontak & media sosial** — Instagram, LinkedIn, contact person WhatsApp.
7. **T&C kolaborasi** — persyaratan, prosedur, bentuk kerja sama, ketentuan logo banner & sertifikat, publikasi, CP kerja sama.

Semua isi diedit pengurus lewat panel admin; tidak perlu coding.

## Development lokal

```powershell
docker compose up -d --build
```

- Website: http://localhost:3010
- API: http://localhost:8010/api/site
- Admin: http://localhost:8010/admin — akun lokal `admin@namaqu.test` / `password` (hanya dibuat di env `local`)

Isi ulang data contoh:

```powershell
docker compose exec backend php artisan migrate:fresh --seed
```

Test backend (SQLite in-memory, tidak menyentuh DB dev):

```powershell
docker compose exec backend php artisan test
```

Simulasi server produksi (nginx + PHP-FPM di subpath `/namaqu`):

```powershell
docker compose --profile subpath up -d --build fpm nginx
```

## Deploy

Lihat [deploy/DEPLOY.md](deploy/DEPLOY.md).
