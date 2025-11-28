<?php
session_start();
// Security Check
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

if (isset($_POST['simpan'])) {
    include '../config/koneksi.php';
    $kode = $_POST['kode_prodi'];
    $nama = $_POST['nama_prodi'];
    $jenjang = $_POST['jenjang'];

    $simpan = mysqli_query($koneksi, "INSERT INTO prodi (kode_prodi, nama_prodi, jenjang) VALUES ('$kode', '$nama', '$jenjang')");

    if ($simpan) {
        echo "<script>alert('Berhasil disimpan!'); window.location='index.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Prodi</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card col-md-6 mx-auto">
            <div class="card-header bg-primary text-white">Tambah Program Studi</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Kode Prodi</label>
                        <input type="text" name="kode_prodi" class="form-control" required placeholder="Contoh: IF">
                    </div>
                    <div class="mb-3">
                        <label>Nama Prodi</label>
                        <input type="text" name="nama_prodi" class="form-control" required placeholder="Contoh: Informatika">
                    </div>
                    <div class="mb-3">
                        <label>Jenjang</label>
                        <select name="jenjang" class="form-select">
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                        </select>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>