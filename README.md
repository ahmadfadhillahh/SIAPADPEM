# SIAPADPEM Website

Website profesional dan responsif berbasis **PHP + MySQL + HTML/CSS/JS** sesuai kebutuhan:
- Navbar: Beranda, Profil (Tupoksi + Struktur Organisasi), Layanan (SIMPERA), Publikasi (Kegiatan + Dokumen), Kontak.
- Login admin dan dashboard CRUD penuh.
- Grafik realisasi fisik & keuangan dengan filter OPD/Bulan/Triwulan.
- Struktur organisasi model slider horizontal (3-4 kartu terlihat, bisa digeser).
- Publikasi kegiatan tampil 6 item per halaman (3 atas, 3 bawah) + pagination.
- Publikasi dokumen dapat dilihat dan diunduh.

## Instalasi
1. Buat database dan import skema:
   ```bash
   mysql -u root -p < database.sql
   ```
2. Sesuaikan kredensial DB di `config.php`.
3. Jalankan server PHP:
   ```bash
   php -S 0.0.0.0:8000
   ```
4. Akses:
   - Frontend: `http://localhost:8000`
   - Admin: `http://localhost:8000/admin/login.php`

## Login Admin Default
- Username: `admin`
- Password: `admin123`

## Struktur Folder
- `index.php` frontend publik.
- `admin/` halaman dashboard + CRUD.
- `includes/` helper, data, koneksi.
- `assets/` CSS, JS, upload file.
- `database.sql` skema MySQL.
