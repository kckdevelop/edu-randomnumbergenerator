# 🚀 Panduan Deployment Edu-Judol (Laravel 12)

Dokumen ini menjelaskan langkah-langkah konfigurasi dan deployment aplikasi Edu-Judol ke berbagai layanan cloud hosting (Render, Railway, VPS, dll.).

---

## 🛠️ Persiapan Environment Variables (Environment Settings)

Saat melakukan deploy di platform cloud (Render, Railway, Vercel, dll.), tambahkan **Environment Variables** berikut pada Dashboard platform Anda:

| Key | Example Value | Keterangan |
|---|---|---|
| `APP_ENV` | `production` | Wajib `production` |
| `APP_DEBUG` | `false` | Keamanan di server produksi |
| `APP_KEY` | `base64:xxx...` | Dihasilkan via `php artisan key:generate --show` |
| `APP_URL` | `https://nama-app.onrender.com` | URL domain publik aplikasi |
| `DB_CONNECTION` | `sqlite` atau `pgsql` / `mysql` | Tipe database yang digunakan |
| `DATABASE_URL` | `postgres://user:pass@host:5432/db` | (Opsional) Jika memakai PostgreSQL/MySQL dari cloud provider |
| `LOG_CHANNEL` | `stderr` | Mengarahkan log Laravel ke dashboard platform |

---

## ⚙️ Skrip Deployment Otomatis (`build.sh`)

Aplikasi ini sudah dilengkapi dengan skrip deployment otomatis [`build.sh`](file:///d:/laravel12/contoh-judol/build.sh) yang menjalankan:
1. `composer install --no-dev --optimize-autoloader`
2. `npm install && npm run build`
3. Memastikan file `database/database.sqlite` otomatis dibuat jika menggunakan SQLite.
4. Menjalankan migrasi database dengan flag `--force` (`php artisan migrate --force`).
5. Menjalankan seeder akun awal secara otomatis (`php artisan db:seed --force`).
6. Mengoptimalkan cache Laravel (`config:cache`, `route:cache`, `view:cache`).

---

## 🌐 1. Deploy di Render.com

1. Push repository ke GitHub / GitLab.
2. Buka dashboard **Render.com** -> Pilih **New Web Service** -> Hubungkan repo GitHub ini.
3. Gunakan pengaturan berikut:
   - **Environment**: `PHP`
   - **Build Command**: `./build.sh`
   - **Start Command**: `php artisan serve --host 0.0.0.0 --port $PORT`
4. Masukkan **Environment Variables** (`APP_ENV`, `APP_KEY`, `APP_URL`, `DB_CONNECTION=sqlite`, dll.).
5. Klik **Create Web Service**.

> 💡 *Catatan:* Alternatifnya, Render akan membaca file [`render.yaml`](file:///d:/laravel12/contoh-judol/render.yaml) secara otomatis (Render Blueprint).

---

## 🚆 2. Deploy di Railway.app

1. Buat project baru di **Railway.app** -> **Deploy from GitHub repo**.
2. Di bagian **Variables**, tambahkan environment variables di atas.
3. Di bagian **Settings** -> **Build Command**: set ke `bash build.sh`.
4. **Start Command**: set ke `php artisan serve --host 0.0.0.0 --port $PORT`.

---

## 🖥️ 3. Deploy di VPS / Server Linux (Ubuntu/Debian)

Jika Anda melakukan deploy manual di VPS:
```bash
git clone <repo-url>
cd contoh-judol
cp .env.example .env
# Edit .env sesuaikan DB dan APP_URL
bash build.sh
```

---

## 🔑 Data Akun Bawaan (Default Seeded Accounts)

Setelah migrasi & seeding berhasil saat deploy, akun berikut siap digunakan:
- **Admin**: `guru@sekolah.id` | Password: `password`
- **Siswa**: `siswa1@sekolah.id` s/d `siswa15@sekolah.id` | Password: `password`
