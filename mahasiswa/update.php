<?php
session_start();
include '../config/koneksi.php';

// Cek Login
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$nim = $_GET['nim']; // Ambil NIM dari URL

// Ambil Data Lama
$q_data = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE nim='$nim'");
$data = mysqli_fetch_assoc($q_data);

// PROSES SIMPAN PERUBAHAN
if (isset($_POST['simpan_perubahan'])) {
    $nama         = $_POST['nama'];
    $angkatan     = $_POST['angkatan'];
    $id_prodi     = $_POST['id_prodi'];
    $dosen_wali   = $_POST['dosen_wali'];
    $ipk          = $_POST['ipk']; // Input IPK

    $update = mysqli_query($koneksi, "UPDATE mahasiswa SET 
                                      nama_lengkap='$nama', 
                                      id_prodi='$id_prodi', 
                                      angkatan='$angkatan', 
                                      dosen_wali='$dosen_wali',
                                      ipk_terakhir='$ipk' 
                                      WHERE nim='$nim'");
    
    if ($update) {
        echo "<script>alert('Data Mahasiswa Berhasil Diupdate!'); window.location='index.php';</script>";
    } else {
        echo "Gagal Update: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Mahasiswa</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Update Data Mahasiswa</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    
                    <div class="mb-3">
                        <label>NIM (Tidak bisa diubah)</label>
                        <input type="text" name="nim" class="form-control bg-light" value="<?php echo $data['nim']; ?>" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo $data['nama_lengkap']; ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Angkatan</label>
                            <input type="number" name="angkatan" class="form-control" value="<?php echo $data['angkatan']; ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>IPK Terakhir</label>
                            <input type="number" name="ipk" class="form-control" step="0.01" min="0" max="4.00" value="<?php echo $data['ipk_terakhir']; ?>" required>
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
                                    $selected = ($data['id_prodi'] == $row_p['id_prodi']) ? 'selected' : '';
                                    echo "<option value='".$row_p['id_prodi']."' $selected>".$row_p['nama_prodi']."</option>";
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
                                    $selected = ($data['dosen_wali'] == $row_d['nidn']) ? 'selected' : '';
                                    echo "<option value='".$row_d['nidn']."' $selected>".$row_d['nama_lengkap']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" name="simpan_perubahan" class="btn btn-warning">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>