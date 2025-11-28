<?php
session_start();
// 1. Security Check
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../config/koneksi.php';

// 2. Ambil Kode MK dari URL
$kode = $_GET['kode'];

// 3. Ambil data lama
$query = mysqli_query($koneksi, "SELECT * FROM matakuliah WHERE kode_mk = '$kode'");
$data = mysqli_fetch_array($query);

// 4. Proses Update
if (isset($_POST['update'])) {
    $nama  = $_POST['nama_mk'];
    $sks   = $_POST['sks'];
    $smt   = $_POST['semester_paket'];
    $prodi = $_POST['id_prodi'];

    // Update data (kecuali kode_mk)
    $update = mysqli_query($koneksi, "UPDATE matakuliah SET 
                                      nama_mk='$nama', 
                                      sks='$sks', 
                                      semester_paket='$smt', 
                                      id_prodi='$prodi' 
                                      WHERE kode_mk='$kode'");

    if ($update) {
        echo "<script>alert('Data Matkul Berhasil Diupdate!'); window.location='index.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mata Kuliah</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card col-md-8 mx-auto">
            <div class="card-header bg-warning">Edit Mata Kuliah</div>
            <div class="card-body">
                <form method="POST">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Kode MK (Terkunci)</label>
                            <input type="text" name="kode_mk" class="form-control" value="<?php echo $data['kode_mk']; ?>" readonly>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label>Nama Mata Kuliah</label>
                            <input type="text" name="nama_mk" class="form-control" value="<?php echo $data['nama_mk']; ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>SKS</label>
                            <input type="number" name="sks" class="form-control" min="1" max="6" value="<?php echo $data['sks']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Semester Paket</label>
                            <input type="number" name="semester_paket" class="form-control" min="1" max="8" value="<?php echo $data['semester_paket']; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Prodi Pemilik</label>
                            <select name="id_prodi" class="form-select">
                                <?php
                                // Ambil semua prodi
                                $q_prodi = mysqli_query($koneksi, "SELECT * FROM prodi");
                                while($p = mysqli_fetch_array($q_prodi)){
                                    // Cek biar otomatis terpilih prodi lamanya
                                    $pilih = ($data['id_prodi'] == $p['id_prodi']) ? 'selected' : '';
                                    echo "<option value='$p[id_prodi]' $pilih>$p[nama_prodi]</option>";
                                }
                                ?>
                            </select>
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