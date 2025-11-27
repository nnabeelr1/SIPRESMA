# Sistem Informasi Prestasi dan Manajemen Risiko Akademik (SIPRESMA)

Project ini dibuat untuk memenuhi tugas praktikum pengembangan aplikasi berbasis web.
Project ini terdiri dari 10 modul data dengan hubungan antar tabel sesuai ERD.

## 📌 Daftar Modul

| Modul | Folder | Penanggung Jawab |
|-------|--------|------------------|
| User/Pengguna | /user | Anggota 1 |
| Mahasiswa | /mahasiswa | Anggota 1 |
| Dosen | /dosen | Anggota 2 |
| Program Studi | /prodi | Anggota 2 |
| Mata Kuliah | /matakuliah | Anggota 3 |
| Kelas | /kelas | Anggota 3 |
| Semester | /semester | Anggota 4 |
| KRS | /krs | Anggota 4 |
| Nilai | /nilai | Anggota 5 |
| Prestasi Non-Akademik | /prestasi | Anggota 5 |
| Dashboard & Laporan | /dashboard | Anggota 6 |
| Peer Support Matchmaking | /peersupport | Anggota 6 |

> Semua anggota _hanya mengerjakan_ folder modul masing-masing.

---

## 💾 1. Cara Menjalankan Project (XAMPP)

1. Pastikan XAMPP terinstal
2. Start **Apache** dan **MySQL**
3. Clone project ini ke htdocs: C:\xampp\htdocs\sipresma
4. Akses project di browser: http://localhost/sipresma/

---

## 🗄 2. Import Database (Wajib dilakukan semua anggota)

1. Buka phpMyAdmin
2. Buat database: db_sipresma
3. Klik **Import**
4. Upload file **db_sipresma.sql** atau jalankan perintah SQL dari repository.

Jika berhasil akan muncul tabel: USER, MAHASISWA, DOSEN, PRODI, MATAKULIAH, KELAS, SEMESTER, KRS, NILAI, PRESTASI_NONAKADEMIK, PEER_SUPPORT

---

## 🌐 3. Struktur Folder Utama

1. index.php → halaman menu utama
2. config/koneksi.php → koneksi database
3. public/ → css & js (Bootstrap)
4. user/ → modul User/Pengguna
5. mahasiswa/ → modul Mahasiswa
6. dosen/ → modul Dosen
7. prodi/ → modul Program Studi
8. matakuliah/ → modul Mata Kuliah
9. kelas/ → modul Kelas
10. semester/ → modul Semester
11. krs/ → modul KRS
12. nilai/ → modul Nilai
13. prestasi/ → modul Prestasi Non-Akademik
14. dashboard/ → modul Dashboard & Laporan
15. peersupport/ → modul Peer Support Matchmaking

---

## 🧠 4. Aturan GitHub (WAJIB)

### 🔥 Dilarang keras:
❌ Commit ke branch `main`  
❌ Push tanpa melakukan `git pull`  
❌ Mengutak-atik folder modul milik orang lain  

### ✔ Wajib:
1. **Pull dulu sebelum bekerja**
2. **Kerja di branch masing-masing**
3. **Push ke branch masing-masing**
4. **Pull Request kalau mau merge ke `main`**

---

## 🔀 5. Daftar Branch Per Anggota

| Branch | Untuk |
|--------|--------|
| dev-user | Anggota 1 |
| dev-mahasiswa | Anggota 1 |
| dev-dosen | Anggota 2 |
| dev-prodi | Anggota 2 |
| dev-matakuliah | Anggota 3 |
| dev-kelas | Anggota 3 |
| dev-semester | Anggota 4 |
| dev-krs | Anggota 4 |
| dev-nilai | Anggota 5 |
| dev-prestasi | Anggota 5 |
| dev-dashboard | Anggota 6 |
| dev-peersupport | Anggota 6 |

---

## 🧩 6. Alur Kerja Git (Langkah Demi Langkah)

### 📌 Saat mau mulai coding
- git pull
- git checkout <nama-branchmu>

### 📌 Setelah selesai coding
- git add .
- git commit -m "progress hari ini"
- git push

### 📌 Setelah modul selesai dan siap digabung ke main
- Buat **Pull Request di GitHub**
- Minta anggota lain untuk review
- Setelah disetujui → merge ke `main`

---

## 🔧 7. Tips agar tidak konflik saat merge

- Jangan edit file milik modul orang lain
- Jangan rename folder
- Jangan ubah file `index.php` tanpa koordinasi tim
- Kalau menambah link baru → koordinasikan dulu
- Selalu pull sebelum mulai coding

---

## 🎯 Goal Akhir Project

### Fitur Utama:

**Manajemen Akademik**
- Admin Prodi mengelola data master (mahasiswa, dosen, prodi, matkul)
- Pembukaan semester dan pengelolaan kelas
- Mahasiswa melakukan KRS online
- Dosen input nilai mahasiswa
- Sistem otomatis menghitung IPS/IPK

**Early Warning System (EWS)**
- Deteksi mahasiswa berisiko akademik (IP rendah, SKS sedikit)
- Dashboard pemantauan untuk Dosen Wali
- Alert otomatis untuk mahasiswa bermasalah

**Identifikasi Mahasiswa Berprestasi**
- Database prestasi akademik dan non-akademik
- Rekomendasi calon Mawapres (Mahasiswa Berprestasi)
- Laporan analitik untuk Pimpinan Prodi

**Peer Support Matchmaking**
- Sistem otomatis mempertemukan mahasiswa berisiko dengan mentor sebaya
- Matching berdasarkan prodi, angkatan, dan kompetensi
- Monitoring progress mentoring informal
- Dukungan berbasis komunitas mahasiswa

**Dashboard & Laporan**
- Visualisasi data akademik dan prestasi
- Export laporan PDF/Excel
- Analytics untuk pengambilan keputusan

---

## 📊 Fitur Unggulan: Peer Support Matchmaking

Fitur inovatif yang membedakan SIPRESMA dari sistem akademik konvensional:

**Konsep:**
Mahasiswa berisiko akademik tinggi dipasangkan dengan teman sebaya berprestasi untuk mentoring informal (bukan formal seperti dosen wali). Dukungan emosional dan akademik dari sesama mahasiswa.

**Mekanisme:**
1. Sistem deteksi otomatis mahasiswa berisiko (IP < 2.5, SKS < 18)
2. Algoritma matching dengan mahasiswa berprestasi (IP > 3.5) di prodi yang sama
3. Notifikasi ke kedua pihak untuk memulai mentoring
4. Monitoring berkala progress akademik mentee
5. Feedback dan evaluasi program peer support

---

## 👨‍💻 Kontributor

- Anggota 1: [Nama] - User & Mahasiswa
- Anggota 2: [Nama] - Dosen & Prodi
- Anggota 3: [Nama] - Mata Kuliah & Kelas
- Anggota 4: [Nama] - Semester & KRS
- Anggota 5: [Nama] - Nilai & Prestasi
- Anggota 6: [Nama] - Dashboard & Peer Support

---

## 📝 Catatan Penting

- Semua modul harus terintegrasi melalui foreign key
- Gunakan Bootstrap untuk konsistensi UI/UX
- Implementasi validasi input di semua form
- Testing fitur secara menyeluruh sebelum merge ke main
- Dokumentasikan setiap perubahan major di commit message

---

Project ini akan terus dikembangkan hingga semua modul CRUD selesai dan siap untuk presentasi.

**SIPRESMA - Smart Academic Management for Better Student Success**