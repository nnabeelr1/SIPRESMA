<?php
session_start();
// 1. Security Check
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

include '../config/koneksi.php';

// 2. Ambil NIDN dari URL
$nidn = $_GET['nidn'];

// 3. Ambil data dosen + username dari tabel user
$query = mysqli_query($koneksi, "SELECT * FROM dosen 
                                 JOIN user ON dosen.id_user = user.id_user 
                                 WHERE dosen.nidn = '$nidn'");
$data = mysqli_fetch_array($query);

// 4. Proses Update
if (isset($_POST['update'])) {
    $nama     = $_POST['nama_lengkap'];
    $email    = $_POST['email'];
    $id_prodi = $_POST['id_prodi'];

    // Kita cuma update data profil, username/password biarkan tetap (safety)
    $update = mysqli_query($koneksi, "UPDATE dosen SET 
                                      nama_lengkap='$nama', 
                                      email='$email', 
                                      id_prodi='$id_prodi' 
                                      WHERE nidn='$nidn'");

    if ($update) {
        echo "<script>alert('Data Dosen Berhasil Diupdate!'); window.location='index.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Dosen</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card col-md-8 mx-auto">
            <div class="card-header bg-warning">Edit Data Dosen</div>
            <div class="card-body">
                <form method="POST">
                    
                    <div class="alert alert-light border">
                        <small class="text-muted d-block">Username Login:</small>
                        <strong><?php echo $data['username']; ?></strong>
                    </div>

                    <div class="mb-3">
                        <label>NIDN (Tidak bisa diubah)</label>
                        <input type="text" name="nidn" class="form-control" value="<?php echo $data['nidn']; ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $data['nama_lengkap']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Homebase Prodi</label>
                        <select name="id_prodi" class="form-select">
                            <?php
                            // Ambil semua prodi buat pilihan
                            $q_prodi = mysqli_query($koneksi, "SELECT * FROM prodi");
                            while($p = mysqli_fetch_array($q_prodi)){
                                // Cek: Apakah ini prodi dosen tersebut? Kalau iya, tambahkan 'selected'
                                $pilih = ($data['id_prodi'] == $p['id_prodi']) ? 'selected' : '';
                                echo "<option value='$p[id_prodi]' $pilih>$p[nama_prodi] ($p[jenjang])</option>";
                            }
                            ?>
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