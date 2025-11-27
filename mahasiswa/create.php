<?php
// Cek apakah form sudah disubmit
if (isset($_POST['simpan'])) {
    include '../config/koneksi.php';
    
    // Ambil data dari form
    $username     = $_POST['username'];
    $password     = $_POST['password']; // Password (nanti bisa di-encrypt pake md5/password_hash)
    $nim          = $_POST['nim'];
    $nama         = $_POST['nama_lengkap'];
    $angkatan     = $_POST['angkatan'];
    $id_prodi     = $_POST['id_prodi'];
    $dosen_wali   = $_POST['dosen_wali'];

    // 1. Insert ke Tabel USER dulu (Bikin akun login)
    // Role otomatis di-set jadi 'mahasiswa'
    $insert_user = mysqli_query($koneksi, "INSERT INTO user (username, password, role) VALUES ('$username', '$password', 'mahasiswa')");
    
    if ($insert_user) {
        // Kalau user berhasil dibuat, ambil ID-nya
        $id_user_baru = mysqli_insert_id($koneksi);

        // 2. Insert ke Tabel MAHASISWA (Pake ID User tadi)
        $insert_mhs = mysqli_query($koneksi, "INSERT INTO mahasiswa (nim, id_user, id_prodi, dosen_wali, nama_lengkap, angkatan) 
                                              VALUES ('$nim', '$id_user_baru', '$id_prodi', '$dosen_wali', '$nama', '$angkatan')");
        
        if ($insert_mhs) {
            echo "<script>alert('Data Berhasil Disimpan!'); window.location='index.php';</script>";
        } else {
            echo "<div class='alert alert-danger'>Gagal simpan mahasiswa: " . mysqli_error($koneksi) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Gagal simpan user: " . mysqli_error($koneksi) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mahasiswa</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4>Tambah Data Mahasiswa</h4>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    
                    <h5 class="mb-3">1. Informasi Akun Login</h5>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <hr>

                    <h5 class="mb-3">2. Data Mahasiswa</h5>
                    <div class="mb-3">
                        <label>NIM</label>
                        <input type="text" name="nim" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Angkatan</label>
                            <input type="number" name="angkatan" class="form-control" value="2024" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Program Studi</label>
                            <select name="id_prodi" class="form-select">
                                <option value="1">1 - Informatika</option>
                                </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Dosen Wali</label>
                        <select name="dosen_wali" class="form-select">
                            <option value="00112233">Budi Santoso</option>
                        </select>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">Simpan Data</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>