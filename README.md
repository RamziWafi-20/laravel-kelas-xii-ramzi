# Bahan materi kelas XII
Langkah-langkah yang harus dilakukan:

1. Silahkan kalian fork untuk memasukan kedalam repository kalian masing-masing
2. Setelah di fork silahkan kalian lakukan cloning dari repo hasil fork
```bash
git clone nama-repo-masing-masing
cd dataweb
```
3. Membuat file .env
```bash
cp .env.example .env
nano .env
```
4. Atur koneksi database (pastikan databasenya sudah di buat terlebih dahulu)
```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_name
DB_USERNAME=db_user
DB_PASSWORD=db_pass
```
5. Install dependncies
```bash
composer install
```
6. Generate APP Key
```bash
php artisan key:generate
```
7. Migration & Seeder
```bash
php artisan migrate --seed
```
Jika database sudah dimigrasi tetapi belum berisi film, jalankan seeder:
```bash
php artisan db:seed
```
Gambar poster film sudah tersedia di `public/template/images` dan akan ditampilkan langsung oleh aplikasi.

Login dan pendaftaran tersedia melalui tombol Login. Pendaftaran membuat akun dengan role `user`. Setelah login, admin dan user diarahkan ke halaman utama; admin dapat membuka dashboard melalui tombol Dashboard.

Dashboard admin tersedia di `/admin` dan dilindungi middleware role admin. Setelah login, admin tetap masuk ke halaman utama dan dapat membuka dashboard melalui tombol Dashboard di header. Menu admin mencakup pengelolaan film, komentar, user, cast, dan actor/peran. Untuk membuat akun admin awal:
```bash
php artisan db:seed --class=AdminSeeder
```
Kredensial awal: `admin@example.com` / `12345678`. Ganti password setelah login pertama.
