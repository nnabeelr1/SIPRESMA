<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }

if (isset($_POST['simpan'])) {
    include '../config/koneksi.php';
    
    // Data Akun
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Data Dosen
    $nidn     = $_POST['nidn'];
    $nama     = $_POST['nama_lengkap'];
    $email    = $_POST['email'];
    $id_prodi = $_POST['id_prodi'];

    // 1. Insert User
    $insert_user = mysqli_query($koneksi, "INSERT INTO user (username, password, role) VALUES ('$username', '$password', 'dosen')");
    
    if ($insert_user) {
        $id_user = mysqli_insert_id($koneksi);
        
        // 2. Insert Dosen
        $insert_dosen = mysqli_query($koneksi, "INSERT INTO dosen (nidn, id_user, id_prodi, nama_lengkap, email) 
                                                VALUES ('$nidn', '$id_user', '$id_prodi', '$nama', '$email')");
        
        if ($insert_dosen) {
            echo "<script>alert('Dosen berhasil ditambahkan!'); window.location='index.php';</script>";
        } else {
            echo "Gagal Dosen: " . mysqli_error($koneksi);
        }
    } else {
        echo "Gagal User: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Dosen</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-success text-white">Tambah Data Dosen</div>
            <div class="card-body">
                <form method="POST">
                    <h5>1. Akun Login</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    
                    <hr>
                    <h5>2. Data Pribadi</h5>
                    <div class="mb-3">
                        <label>NIDN</label>
                        <input type="text" name="nidn" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Lengkap (+Gelar)</label>
                        <input type="text" name="nama_lengkap" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Homebase Prodi</label>
                        <select name="id_prodi" class="form-select">
                            <?php
                            include '../config/koneksi.php';
                            $prodi = mysqli_query($koneksi, "SELECT * FROM prodi");
                            while($p = mysqli_fetch_array($prodi)){
                                echo "<option value='$p[id_prodi]'>$p[nama_prodi] ($p[jenjang])</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">Simpan Data</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>