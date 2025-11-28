<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';

if (isset($_POST['simpan'])) {
    $mk    = $_POST['kode_mk'];
    $dosen = $_POST['nidn'];
    $nama  = $_POST['nama_kelas'];
    $kuota = $_POST['kuota'];
    $hari  = $_POST['hari'];
    $j_mulai = $_POST['jam_mulai'];
    $j_selesai = $_POST['jam_selesai'];

    $simpan = mysqli_query($koneksi, "INSERT INTO kelas (kode_mk, nidn, nama_kelas, kuota, hari, jam_mulai, jam_selesai) 
                                      VALUES ('$mk', '$dosen', '$nama', '$kuota', '$hari', '$j_mulai', '$j_selesai')");

    if ($simpan) {
        header("Location: index.php");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buka Kelas Baru</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card col-md-8 mx-auto">
            <div class="card-header bg-primary text-white">Buka Kelas Baru</div>
            <div class="card-body">
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Mata Kuliah</label>
                            <select name="kode_mk" class="form-select" required>
                                <?php
                                $q_mk = mysqli_query($koneksi, "SELECT * FROM matakuliah");
                                while($m = mysqli_fetch_array($q_mk)){
                                    echo "<option value='$m[kode_mk]'>$m[kode_mk] - $m[nama_mk]</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Dosen Pengajar</label>
                            <select name="nidn" class="form-select" required>
                                <?php
                                $q_d = mysqli_query($koneksi, "SELECT * FROM dosen");
                                while($d = mysqli_fetch_array($q_d)){
                                    echo "<option value='$d[nidn]'>$d[nama_lengkap]</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Nama Kelas</label>
                            <select name="nama_kelas" class="form-select">
                                <option>A</option><option>B</option><option>C</option>
                                <option>Pagi</option><option>Malam</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Hari</label>
                            <select name="hari" class="form-select">
                                <option>Senin</option><option>Selasa</option><option>Rabu</option>
                                <option>Kamis</option><option>Jumat</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Kuota</label>
                            <input type="number" name="kuota" class="form-control" value="40">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">Simpan Kelas</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>