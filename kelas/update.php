<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';

// 1. Ambil ID Kelas dari URL
$id = $_GET['id'];

// 2. Ambil Data Lama
$query = mysqli_query($koneksi, "SELECT * FROM kelas WHERE id_kelas='$id'");
$data = mysqli_fetch_array($query);

// 3. Proses Update
if (isset($_POST['update'])) {
    $mk    = $_POST['kode_mk'];
    $dosen = $_POST['nidn'];
    $nama  = $_POST['nama_kelas'];
    $kuota = $_POST['kuota'];
    $hari  = $_POST['hari'];
    $j_mulai = $_POST['jam_mulai'];
    $j_selesai = $_POST['jam_selesai'];

    $update = mysqli_query($koneksi, "UPDATE kelas SET 
        kode_mk='$mk', nidn='$dosen', nama_kelas='$nama', kuota='$kuota', 
        hari='$hari', jam_mulai='$j_mulai', jam_selesai='$j_selesai' 
        WHERE id_kelas='$id'");

    if ($update) {
        echo "<script>alert('Kelas berhasil diupdate!'); window.location='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kelas</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card col-md-8 mx-auto">
            <div class="card-header bg-warning">Edit Kelas Kuliah</div>
            <div class="card-body">
                <form method="POST">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Mata Kuliah</label>
                            <select name="kode_mk" class="form-select" required>
                                <?php
                                $q_mk = mysqli_query($koneksi, "SELECT * FROM matakuliah");
                                while($m = mysqli_fetch_array($q_mk)){
                                    // Cek apakah ini matkul yang dipilih sebelumnya?
                                    $sel = ($data['kode_mk'] == $m['kode_mk']) ? 'selected' : '';
                                    echo "<option value='$m[kode_mk]' $sel>$m[kode_mk] - $m[nama_mk]</option>";
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
                                    // Cek apakah ini dosen yang dipilih sebelumnya?
                                    $sel = ($data['nidn'] == $d['nidn']) ? 'selected' : '';
                                    echo "<option value='$d[nidn]' $sel>$d[nama_lengkap]</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="form-control" value="<?php echo $data['nama_kelas']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label>Hari</label>
                            <select name="hari" class="form-select">
                                <option <?php if($data['hari']=='Senin') echo 'selected'; ?>>Senin</option>
                                <option <?php if($data['hari']=='Selasa') echo 'selected'; ?>>Selasa</option>
                                <option <?php if($data['hari']=='Rabu') echo 'selected'; ?>>Rabu</option>
                                <option <?php if($data['hari']=='Kamis') echo 'selected'; ?>>Kamis</option>
                                <option <?php if($data['hari']=='Jumat') echo 'selected'; ?>>Jumat</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Kuota</label>
                            <input type="number" name="kuota" class="form-control" value="<?php echo $data['kuota']; ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label>Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="<?php echo $data['jam_mulai']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="<?php echo $data['jam_selesai']; ?>" required>
                        </div>
                    </div>

                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>