<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

include '../config/koneksi.php';

// 1. Ambil Data Mahasiswa Lama
$nim = $_GET['nim'];
$query = mysqli_query($koneksi, "SELECT * FROM mahasiswa 
                                 JOIN user ON mahasiswa.id_user = user.id_user 
                                 WHERE mahasiswa.nim = '$nim'");
$data = mysqli_fetch_array($query);

// 2. Proses Simpan Perubahan
if (isset($_POST['update'])) {
    $nama     = $_POST['nama_lengkap'];
    $angkatan = $_POST['angkatan'];
    $status   = $_POST['status_akademik'];
    
    // INI DIA YANG PENTING: Update Prodi & Dosen Wali juga
    $id_prodi   = $_POST['id_prodi'];
    $dosen_wali = $_POST['dosen_wali'];

    $update = mysqli_query($koneksi, "UPDATE mahasiswa SET 
                            nama_lengkap = '$nama',
                            angkatan = '$angkatan',
                            status_akademik = '$status',
                            id_prodi = '$id_prodi',
                            dosen_wali = '$dosen_wali'
                            WHERE nim = '$nim'");
    
    if ($update) {
        echo "<script>alert('Data Berhasil Diupdate!'); window.location='index.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
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
        <div class="card col-md-8 mx-auto">
            <div class="card-header bg-warning">Edit Data Mahasiswa</div>
            <div class="card-body">
                <form method="POST">
                    
                    <div class="mb-3">
                        <label>Username (Login)</label>
                        <input type="text" class="form-control" value="<?php echo $data['username']; ?>" readonly disabled>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>NIM</label>
                            <input type="text" name="nim" class="form-control" value="<?php echo $data['nim']; ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Angkatan</label>
                            <input type="number" name="angkatan" class="form-control" value="<?php echo $data['angkatan']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $data['nama_lengkap']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Program Studi</label>
                        <select name="id_prodi" class="form-select">
                            <?php
                            $q_prodi = mysqli_query($koneksi, "SELECT * FROM prodi");
                            while ($p = mysqli_fetch_array($q_prodi)) {
                                // Cek: Apakah ini prodi si mahasiswa sekarang?
                                $selected = ($data['id_prodi'] == $p['id_prodi']) ? 'selected' : '';
                                echo "<option value='$p[id_prodi]' $selected>$p[nama_prodi] ($p[jenjang])</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Dosen Wali</label>
                        <select name="dosen_wali" class="form-select">
                            <?php
                            $q_dosen = mysqli_query($koneksi, "SELECT * FROM dosen");
                            while ($d = mysqli_fetch_array($q_dosen)) {
                                // Cek: Apakah ini dosen wali si mahasiswa sekarang?
                                $selected = ($data['dosen_wali'] == $d['nidn']) ? 'selected' : '';
                                echo "<option value='$d[nidn]' $selected>$d[nama_lengkap]</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Status Akademik</label>
                        <select name="status_akademik" class="form-select">
                            <option value="aktif" <?php echo ($data['status_akademik'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                            <option value="cuti" <?php echo ($data['status_akademik'] == 'cuti') ? 'selected' : ''; ?>>Cuti</option>
                            <option value="do" <?php echo ($data['status_akademik'] == 'do') ? 'selected' : ''; ?>>DO</option>
                            <option value="lulus" <?php echo ($data['status_akademik'] == 'lulus') ? 'selected' : ''; ?>>Lulus</option>
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