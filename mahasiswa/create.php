<?php
session_start();
include '../config/koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// PROSES SIMPAN DATA
if (isset($_POST['simpan'])) {
    $username     = $_POST['username'];
    $password     = $_POST['password']; 
    $nim          = $_POST['nim'];
    $nama         = $_POST['nama'];
    $angkatan     = $_POST['angkatan'];
    $id_prodi     = $_POST['id_prodi'];
    $dosen_wali   = $_POST['dosen_wali'];
    $ipk          = $_POST['ipk']; // <-- TAMBAHAN IPK

    // 1. Cek Username Kembar
    $cek_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Username sudah terpakai!'); window.history.back();</script>";
        exit();
    }

    // 2. Cek NIM Kembar
    $cek_nim = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE nim='$nim'");
    if (mysqli_num_rows($cek_nim) > 0) {
        echo "<script>alert('NIM sudah terdaftar!'); window.history.back();</script>";
        exit();
    }

    // 3. Insert ke Tabel USER dulu
    $insert_user = mysqli_query($koneksi, "INSERT INTO user (username, password, role) VALUES ('$username', '$password', 'mahasiswa')");
    
    if ($insert_user) {
        $id_user_baru = mysqli_insert_id($koneksi);

        // 4. Insert ke Tabel MAHASISWA (Sekarang ada IPK)
        $query_mhs = "INSERT INTO mahasiswa (nim, id_user, nama_lengkap, id_prodi, angkatan, dosen_wali, ipk_terakhir) 
                      VALUES ('$nim', '$id_user_baru', '$nama', '$id_prodi', '$angkatan', '$dosen_wali', '$ipk')";
        
        $insert_mhs = mysqli_query($koneksi, $query_mhs);
        
        if ($insert_mhs) {
            echo "<script>alert('Data Mahasiswa Berhasil Ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Gagal Insert Mahasiswa: " . mysqli_error($koneksi);
        }
    } else {
        echo "Gagal Insert User: " . mysqli_error($koneksi);
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
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Data Mahasiswa</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    
                    <h6 class="text-primary mb-3">1. Informasi Akun Login</h6>
                    <div class="mb-3">
                        <label>Username (Disarankan pakai NIM)</label>
                        <input type="text" name="username" class="form-control" required placeholder="Contoh: 24001">
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="text" name="password" class="form-control" required value="123">
                    </div>

                    <hr>

                    <h6 class="text-primary mb-3">2. Data Mahasiswa</h6>
                    <div class="mb-3">
                        <label>NIM</label>
                        <input type="text" name="nim" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Angkatan</label>
                            <input type="number" name="angkatan" class="form-control" value="<?php echo date('Y'); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label>IPK Terakhir</label>
                            <input type="number" name="ipk" class="form-control" step="0.01" min="0" max="4.00" value="0.00" required>
                            <small class="text-muted">Gunakan titik (.) untuk desimal. Contoh: 3.50</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Program Studi</label>
                            <select name="id_prodi" class="form-select" required>
                                <option value="">-- Pilih Prodi --</option>
                                <?php
                                $q_prodi = mysqli_query($koneksi, "SELECT * FROM prodi ORDER BY nama_prodi ASC");
                                while ($row_p = mysqli_fetch_assoc($q_prodi)) {
                                    echo "<option value='".$row_p['id_prodi']."'>".$row_p['nama_prodi']." (".$row_p['jenjang'].")</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Dosen Wali</label>
                            <select name="dosen_wali" class="form-select" required>
                                <option value="">-- Pilih Dosen Wali --</option>
                                <?php
                                $q_dosen = mysqli_query($koneksi, "SELECT * FROM dosen ORDER BY nama_lengkap ASC");
                                while ($row_d = mysqli_fetch_assoc($q_dosen)) {
                                    echo "<option value='".$row_d['nidn']."'>".$row_d['nama_lengkap']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">Simpan Data</button>
                    <a href="index.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>