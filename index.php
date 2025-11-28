<?php
session_start();
include 'config/koneksi.php';

// Jika tombol login ditekan
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek username & password di database
    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    
    // Cek apakah data ditemukan?
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // 1. Simpan data dasar dulu
        $_SESSION['id_user']  = $data['id_user'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role']     = $data['role'];
        $_SESSION['status']   = "login";

        // 2. CEK NAMA ASLI (Fitur Baru)
        // Defaultnya pakai username dulu
        $_SESSION['nama_lengkap'] = $data['username'];

        // Kalau dia Mahasiswa, ambil nama asli dari tabel mahasiswa
        if ($data['role'] == 'mahasiswa') {
            $id = $data['id_user'];
            $q_mhs = mysqli_query($koneksi, "SELECT nama_lengkap FROM mahasiswa WHERE id_user='$id'");
            
            // Kalau datanya ada (sudah diinput admin), pakai nama aslinya
            if ($row = mysqli_fetch_assoc($q_mhs)) {
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
            }
        }
        // Kalau dia Dosen, ambil dari tabel dosen (Nanti kalau modul dosen dah jadi)
        else if ($data['role'] == 'dosen') {
             // Logic sama kayak mahasiswa, arahkan ke tabel dosen
        }

        // 3. Arahkan sesuai Role
        if ($data['role'] == 'admin') {
            header("Location: dashboard/index.php");
        } else if ($data['role'] == 'mahasiswa') {
            header("Location: dashboard/welcome_mhs.php");
        } else if ($data['role'] == 'dosen') {
            // INI BARU: Arahkan Dosen ke Dashboard Khusus
            header("Location: dashboard/welcome_dosen.php");
        }
        
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPRESMA</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .card-login {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

    <div class="card card-login bg-white">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-primary">🎓 SIPRESMA</h3>
            <p class="text-muted small">Silakan login untuk masuk ke sistem</p>
        </div>

        <?php if(isset($error)) { ?>
            <div class="alert alert-danger text-center py-2 mb-3">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username..." required autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password..." required>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" name="login" class="btn btn-primary fw-bold">MASUK</button>
            </div>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">&copy; 2025 Kelompok 4</small>
        </div>
    </div>

    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>