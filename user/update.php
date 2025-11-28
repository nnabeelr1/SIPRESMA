<?php
session_start();
if ($_SESSION['role'] != 'admin') { header("Location: ../index.php"); exit(); }
include '../config/koneksi.php';

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id'"));

if (isset($_POST['update'])) {
    $user = $_POST['username'];
    $pass = $_POST['password']; // Password Baru
    $role = $_POST['role'];

    // Cek apakah admin mau ganti password atau tidak?
    if (!empty($pass)) {
        // Kalau password diisi, update passwordnya
        $query = "UPDATE user SET username='$user', password='$pass', role='$role' WHERE id_user='$id'";
    } else {
        // Kalau kosong, jangan update passwordnya
        $query = "UPDATE user SET username='$user', role='$role' WHERE id_user='$id'";
    }

    $update = mysqli_query($koneksi, $query);
    if ($update) {
        echo "<script>alert('Data user berhasil diupdate!'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card col-md-6 mx-auto">
            <div class="card-header bg-warning">Edit / Reset Password</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $data['username']; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="(Biarkan kosong jika tidak ingin mengganti password)">
                        <small class="text-danger">*Isi hanya jika ingin mereset password user ini.</small>
                    </div>

                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" class="form-select">
                            <option value="admin" <?php if($data['role']=='admin') echo 'selected'; ?>>Admin</option>
                            <option value="mahasiswa" <?php if($data['role']=='mahasiswa') echo 'selected'; ?>>Mahasiswa</option>
                            <option value="dosen" <?php if($data['role']=='dosen') echo 'selected'; ?>>Dosen</option>
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