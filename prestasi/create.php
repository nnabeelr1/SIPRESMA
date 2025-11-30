<?php
session_start();
include '../config/koneksi.php';

// Cek Login & Role (Hanya Mahasiswa)
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../index.php");
    exit();
}

// Ambil NIM Mahasiswa yang Login
$username = $_SESSION['username'];
$mhs = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nim FROM mahasiswa JOIN user ON mahasiswa.id_user = user.id_user WHERE user.username='$username'"));
$nim_saya = $mhs['nim'];

// Logic Simpan Data
if(isset($_POST['simpan'])) {
    $kegiatan = $_POST['kegiatan'];
    $juara    = $_POST['juara'];
    $tingkat  = $_POST['tingkat'];
    $tahun    = $_POST['tahun'];
    $ket      = $_POST['keterangan'];

    // Insert ke tabel 'prestasi'
    $insert = mysqli_query($koneksi, "INSERT INTO prestasi (nim, nama_kegiatan, jenis_juara, tingkat, tahun, keterangan) 
                                      VALUES ('$nim_saya', '$kegiatan', '$juara', '$tingkat', '$tahun', '$ket')");
    
    if($insert) {
        echo "<script>alert('Prestasi berhasil ditambahkan!'); window.location='index.php';</script>";
    } else {
        echo "Gagal: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Prestasi</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card col-md-6 mx-auto shadow-sm">
            <div class="card-header bg-primary text-white">Tambah Prestasi Baru</div>
            <div class="card-body">
                <form method="POST">
                    
                    <div class="mb-3">
                        <label>Nama Kegiatan / Lomba</label>
                        <input type="text" name="kegiatan" class="form-control" required placeholder="Cth: Lomba Web Design Nasional">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Jenis Juara</label>
                            <select name="juara" class="form-select">
                                <option>Juara 1</option>
                                <option>Juara 2</option>
                                <option>Juara 3</option>
                                <option>Juara Harapan</option>
                                <option>Finalis</option>
                                <option>Peserta</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tingkat</label>
                            <select name="tingkat" class="form-select">
                                <option>Jurusan</option>
                                <option>Universitas</option>
                                <option>Regional</option>
                                <option>Nasional</option>
                                <option>Internasional</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Tahun</label>
                        <input type="number" name="tahun" class="form-control" value="<?php echo date('Y'); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Link Sertifikat / Keterangan</label>
                        <textarea name="keterangan" class="form-control" placeholder="Tempel link GDrive sertifikat di sini..."></textarea>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success w-100">Simpan Prestasi</button>
                    <a href="index.php" class="btn btn-secondary w-100 mt-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>