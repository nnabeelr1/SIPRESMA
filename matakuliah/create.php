<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

if (isset($_POST['simpan'])) {
    include '../config/koneksi.php';
    $kode  = $_POST['kode_mk'];
    $nama  = $_POST['nama_mk'];
    $sks   = $_POST['sks'];
    $smt   = $_POST['semester_paket'];
    $prodi = $_POST['id_prodi'];

    $simpan = mysqli_query($koneksi, "INSERT INTO matakuliah (kode_mk, id_prodi, nama_mk, sks, semester_paket) 
                                      VALUES ('$kode', '$prodi', '$nama', '$sks', '$smt')");

    if ($simpan) {
        echo "<script>alert('Matkul berhasil disimpan!'); window.location='index.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mata Kuliah</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card col-md-8 mx-auto">
            <div class="card-header bg-primary text-white">Tambah Mata Kuliah</div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Kode MK</label>
                            <input type="text" name="kode_mk" class="form-control" placeholder="Cth: IF101" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label>Nama Mata Kuliah</label>
                            <input type="text" name="nama_mk" class="form-control" placeholder="Cth: Algoritma Pemrograman" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>SKS (1-6)</label>
                            <input type="number" name="sks" class="form-control" min="1" max="6" value="3" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Semester Paket</label>
                            <input type="number" name="semester_paket" class="form-control" min="1" max="8" value="1" required>
                            <small class="text-muted">Biasanya diambil di semester brp?</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Prodi Pemilik</label>
                            <select name="id_prodi" class="form-select">
                                <?php
                                include '../config/koneksi.php';
                                $q = mysqli_query($koneksi, "SELECT * FROM prodi");
                                while($p = mysqli_fetch_array($q)){
                                    echo "<option value='$p[id_prodi]'>$p[nama_prodi]</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">Simpan Data</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>