# SIPRESMA 🎓🛡️
**Sistem Informasi Prestasi dan Manajemen Risiko Akademik**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://www.php.net/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)](https://getbootstrap.com/)

> Project praktikum pengembangan aplikasi berbasis web dengan fitur Early Warning System (EWS) dan Peer Support Matchmaking untuk deteksi dini mahasiswa berisiko.

---

## 📋 Daftar Isi

- [Tentang Project](#-tentang-project)
- [Fitur Utama](#-fitur-utama)
- [Daftar Modul](#-daftar-modul)
- [Teknologi](#-teknologi)
- [Instalasi](#-instalasi)
- [Struktur Folder](#-struktur-folder)
- [Alur Kerja Git](#-alur-kerja-git)
- [Aturan Kontribusi](#-aturan-kontribusi)
- [Tim Pengembang](#-tim-pengembang)

---

## 🎯 Tentang Project

SIPRESMA adalah sistem informasi akademik yang tidak hanya mencatat nilai mahasiswa, tetapi juga dilengkapi dengan:
- **Early Warning System (EWS)** untuk mendeteksi mahasiswa berisiko akademik
- **Peer Support Matchmaking** untuk menghubungkan mahasiswa dengan mentor sebaya
- **Manajemen Prestasi Non-Akademik** sebagai data pendukung evaluasi mahasiswa

Project ini dikembangkan dengan relasi antar tabel sesuai Entity Relationship Diagram (ERD) yang telah disepakati tim.

---

## ✨ Fitur Utama

- 🔐 **Autentikasi & Manajemen User** - Login multi-role (Admin, Dosen, Mahasiswa)
- 📚 **Data Master Lengkap** - Mahasiswa, Dosen, Program Studi
- 🎓 **Akademik** - Mata Kuliah, Kelas, Semester
- 📝 **Transaksi Akademik** - KRS dan Input Nilai
- ⚠️ **Early Warning System** - Alert otomatis untuk mahasiswa IPK < 2.00
- 🤝 **Peer Support Matchmaking** - Rekomendasi mentor berdasarkan performa
- 🏆 **Prestasi Non-Akademik** - Pencatatan lomba, organisasi, sertifikat
- 📊 **Dashboard & Laporan** - Visualisasi data dan cetak transkrip

---

## 📌 Daftar Modul

| Modul | Folder | Penanggung Jawab |
|-------|--------|------------------|
| Auth & User | `/user` | Anggota 1 |
| Data Master (Mhs, Dosen, Prodi) | `/master` | Anggota 1 |
| Akademik (Matkul, Kelas, Smt) | `/akademik` | Anggota 2 |
| Transaksi (KRS & Nilai) | `/transaksi` | Anggota 2 |
| EWS & Matchmaking | `/ews` | Anggota 3 |
| Prestasi Non-Akademik | `/prestasi` | Anggota 3 |
| Laporan & Transkrip | `/laporan` | Anggota 4 |
| Dashboard | `/dashboard` | Anggota 4 |

> ⚠️ **Penting:** Setiap anggota hanya mengerjakan folder modul masing-masing untuk menghindari konflik.

---

## 🛠 Teknologi

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** Bootstrap 5.3, HTML5, CSS3, JavaScript
- **Server:** XAMPP (Apache + MySQL)
- **Version Control:** Git & GitHub

---

## 💾 Instalasi

### 1️⃣ Persiapan Environment

```bash
# Pastikan XAMPP sudah terinstal
# Download di: https://www.apachefriends.org/
```

### 2️⃣ Clone Repository

```bash
# Clone ke folder htdocs XAMPP
cd C:\xampp\htdocs
git clone https://github.com/username/sipresma.git
cd sipresma
```

### 3️⃣ Setup Database

1. Start **Apache** dan **MySQL** di XAMPP Control Panel
2. Buka browser: `http://localhost/phpmyadmin`
3. Buat database baru dengan nama: `db_sipresma`
4. Import file `db_sipresma.sql` yang ada di root repository

**Tabel yang akan terbuat:**
- `USER`
- `MAHASISWA`
- `DOSEN`
- `PRODI`
- `MATAKULIAH`
- `KELAS`
- `KRS`
- `NILAI`
- `PRESTASI`
- `PEER_MENTORING`

### 4️⃣ Konfigurasi Koneksi

Edit file `config/koneksi.php` jika diperlukan:

```php
<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_sipresma";

$conn = mysqli_connect($host, $user, $pass, $db);
?>
```

### 5️⃣ Jalankan Aplikasi

Buka browser dan akses:
```
http://localhost/sipresma/
```

---

## 🌐 Struktur Folder

```
sipresma/
│
├── index.php                 # Landing page / Login
├── db_sipresma.sql           # File database
│
├── config/
│   └── koneksi.php           # Konfigurasi database
│
├── assets/
│   ├── css/                  # File CSS
│   ├── js/                   # JavaScript & Bootstrap
│   └── img/                  # Gambar/logo
│
├── user/                     # Modul Manajemen User
├── master/                   # Modul Data Master
├── akademik/                 # Modul Akademik
├── transaksi/                # Modul KRS & Nilai
├── ews/                      # Modul EWS & Matchmaking
├── prestasi/                 # Modul Prestasi
├── laporan/                  # Modul Laporan
└── dashboard/                # Dashboard utama
```

---

## 🔀 Alur Kerja Git

### 📌 Daftar Branch

| Branch | Fungsi | Developer |
|--------|--------|-----------|
| `main` | Branch utama (production-ready) | Semua |
| `dev-user` | Auth & Master Data | Anggota 1 |
| `dev-akademik` | Akademik & Transaksi | Anggota 2 |
| `dev-fitur` | EWS & Prestasi | Anggota 3 |
| `dev-ui` | Dashboard & Laporan | Anggota 4 |

### 🔄 Workflow Standar

#### Sebelum Mulai Coding

```bash
git pull origin main
git checkout dev-user  # sesuaikan dengan branch masing-masing
```

#### Setelah Selesai Coding

```bash
git add .
git commit -m "feat: tambah fitur input nilai mahasiswa"
git push origin dev-user  # sesuaikan dengan branch masing-masing
```

#### Merge ke Main Branch

1. Buka GitHub repository
2. Buat **Pull Request** dari branch kamu ke `main`
3. Minta **Code Review** dari anggota tim
4. Setelah approved → **Merge Pull Request**

---

## ⚠️ Aturan Kontribusi

### 🔥 DILARANG KERAS:

❌ Commit langsung ke branch `main`  
❌ Push tanpa melakukan `git pull` terlebih dahulu  
❌ Mengutak-atik folder modul milik orang lain  
❌ Mengubah file `koneksi.php` tanpa koordinasi tim  

### ✅ WAJIB:

✔️ Selalu `git pull` sebelum mulai coding  
✔️ Kerja di branch masing-masing  
✔️ Gunakan commit message yang jelas  
✔️ Pull Request untuk merge ke `main`  

### 💡 Tips Menghindari Konflik

- Jangan edit file di folder modul orang lain
- Jangan rename folder tanpa koordinasi
- Jangan ubah `index.php` tanpa diskusi tim
- Kabari di grup jika menambah library baru

---

## 🎯 Roadmap Project

- [x] Setup repository & database structure
- [x] Implementasi autentikasi user
- [ ] CRUD semua modul berjalan
- [ ] Relasi Foreign Key berfungsi sempurna
- [ ] Logika EWS aktif (Alert IPK < 2.00)
- [ ] Peer Support Matchmaking berjalan
- [ ] UI Bootstrap konsisten di semua halaman
- [ ] Testing & Bug Fixing
- [ ] Dokumentasi lengkap

---

## 👨‍💻 Tim Pengembang

| Nama | Role | Modul | GitHub |
|------|------|-------|--------|
| Anggota 1 | Frontend Lead | Auth & Master Data | [@username1](https://github.com/username1) |
| Anggota 2 | Backend Developer | Akademik & Transaksi | [@username2](https://github.com/username2) |
| Anggota 3 | Logic Developer | EWS & Prestasi | [@username3](https://github.com/username3) |
| Anggota 4 | UI/UX Developer | Dashboard & Laporan | [@username4](https://github.com/username4) |

---

## 📞 Kontak & Support

Jika ada pertanyaan atau menemukan bug:
- Buat **Issue** di GitHub
- Diskusi di grup WhatsApp tim
- Email: sipresma@university.ac.id

---

## 📄 Lisensi

Project ini dibuat untuk keperluan akademik dan pembelajaran.

---

<div align="center">

**Dibuat dengan ❤️ oleh Tim SIPRESMA**

⭐ Jangan lupa beri star jika project ini bermanfaat!

</div>