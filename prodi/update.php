<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

include '../config/koneksi.php';
$id = $_GET['id'];
$data = mysqli_fetch_array(mysqli_query($koneksi, "SELECT * FROM prodi WHERE id_prodi='$id'"));

if (isset($_POST['update'])) {
    $kode = $_POST['kode_prodi'];
    $nama = $_POST['nama_prodi'];
    $jenjang = $_POST['jenjang'];

    $update = mysqli_query($koneksi, "UPDATE prodi SET kode_prodi='$kode', nama_prodi='$nama', jenjang='$jenjang' WHERE id_prodi='$id'");

    if ($update) {
        echo "<script>alert('Berhasil diupdate!'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Prodi</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card col-md-6 mx-auto">
            <div class="card-header bg-warning">Edit Program Studi</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Kode Prodi</label>
                        <input type="text" name="kode_prodi" class="form-control" value="<?php echo $data['kode_prodi']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Prodi</label>
                        <input type="text" name="nama_prodi" class="form-control" value="<?php echo $data['nama_prodi']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Jenjang</label>
                        <select name="jenjang" class="form-select">
                            <option value="D3" <?php if($data['jenjang']=='D3') echo 'selected'; ?>>D3</option>
                            <option value="S1" <?php if($data['jenjang']=='S1') echo 'selected'; ?>>S1</option>
                            <option value="S2" <?php if($data['jenjang']=='S2') echo 'selected'; ?>>S2</option>
                        </select>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>