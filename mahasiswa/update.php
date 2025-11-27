<?php
include '../config/koneksi.php';

// 1. Ambil Data Lama untuk ditampilkan di form
$nim = $_GET['nim'];
$query = mysqli_query($koneksi, "SELECT * FROM mahasiswa 
                                 JOIN user ON mahasiswa.id_user = user.id_user 
                                 WHERE mahasiswa.nim = '$nim'");
$data = mysqli_fetch_array($query);

// 2. Proses jika tombol Update ditekan
if (isset($_POST['update'])) {
    $nama       = $_POST['nama_lengkap'];
    $angkatan   = $_POST['angkatan'];
    $dosen_wali = $_POST['dosen_wali'];
    $status     = $_POST['status_akademik'];
    
    // Update tabel MAHASISWA
    $update = mysqli_query($koneksi, "UPDATE mahasiswa SET 
                            nama_lengkap = '$nama',
                            angkatan = '$angkatan',
                            dosen_wali = '$dosen_wali',
                            status_akademik = '$status'
                            WHERE nim = '$nim'");
    
    if ($update) {
        echo "<script>alert('Data Berhasil Diupdate!'); window.location='index.php';</script>";
    } else {
        echo "Gagal update: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4>Edit Data Mahasiswa</h4>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" class="form-control" value="<?php echo $data['username']; ?>" readonly disabled>
                        <small class="text-muted">Username tidak bisa diubah di sini.</small>
                    </div>

                    <div class="mb-3">
                        <label>NIM</label>
                        <input type="text" name="nim" class="form-control" value="<?php echo $data['nim']; ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $data['nama_lengkap']; ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Angkatan</label>
                            <input type="number" name="angkatan" class="form-control" value="<?php echo $data['angkatan']; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Status Akademik</label>
                            <select name="status_akademik" class="form-select">
                                <option value="aktif" <?php echo ($data['status_akademik'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                <option value="cuti" <?php echo ($data['status_akademik'] == 'cuti') ? 'selected' : ''; ?>>Cuti</option>
                                <option value="do" <?php echo ($data['status_akademik'] == 'do') ? 'selected' : ''; ?>>DO</option>
                                <option value="lulus" <?php echo ($data['status_akademik'] == 'lulus') ? 'selected' : ''; ?>>Lulus</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Dosen Wali (NIDN)</label>
                        <select name="dosen_wali" class="form-select">
                            <option value="00112233" selected>Budi Santoso</option>
                            </select>
                    </div>

                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>