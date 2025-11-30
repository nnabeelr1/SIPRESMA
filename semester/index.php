<?php
session_start();
// Security: Cuma Admin yang boleh masuk
if (!isset($_SESSION['status']) || $_SESSION['role'] != 'admin') { 
    header("Location: ../index.php"); 
    exit(); 
}
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Semester (KRS)</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h3 class="mb-4">Pengaturan Semester (KRS)</h3>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">Buat Semester Baru</div>
                    <div class="card-body">
                        <form action="create.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nama Semester</label>
                                <input type="text" name="nama" class="form-control" placeholder="Cth: 2024 Ganjil" required>
                            </div>
                            <button type="submit" name="simpan" class="btn btn-success w-100">Simpan</button>
                        </form>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="../dashboard/index.php" class="btn btn-secondary w-100">Kembali ke Dashboard</a>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white">Daftar Semester</div>
                    <div class="card-body">
                        <div class="alert alert-warning small">
                            <strong>Info:</strong> Hanya satu semester yang boleh <strong>AKTIF</strong> dalam satu waktu.
                            <br>Mengaktifkan semester baru otomatis akan menutup akses KRS semester lama.
                        </div>

                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Semester</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q = mysqli_query($koneksi, "SELECT * FROM semester ORDER BY nama_semester DESC");
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($q)) {
                                    // PEMBERSIHAN DATA (Agar logika kebal terhadap spasi/huruf besar)
                                    // trim = buang spasi, strtolower = jadiin huruf kecil
                                    $status_bersih = trim(strtolower($row['status']));
                                    
                                    // Tentukan warna baris
                                    $bg_row = ($status_bersih == 'aktif') ? 'table-success' : '';
                                ?>
                                    <tr class="<?php echo $bg_row; ?>">
                                        <td><?php echo $no++; ?></td>
                                        <td>
                                            <strong><?php echo $row['nama_semester']; ?></strong>
                                        </td>
                                        <td>
                                            <?php if($status_bersih == 'aktif'){ ?>
                                                <span class="badge bg-success">✅ AKTIF</span>
                                            <?php } else { ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php 
                                            // LOGIKA UTAMA: Jika statusnya BUKAN 'aktif', maka munculkan tombol
                                            if($status_bersih != 'aktif') { 
                                            ?>
                                                <a href="activate.php?id=<?php echo $row['id_semester']; ?>" class="btn btn-sm btn-primary">⚡ Aktifkan</a>
                                                <a href="delete.php?id=<?php echo $row['id_semester']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus semester ini?')">Hapus</a>
                                            
                                            <?php } else { ?>
                                                
                                                <span class="text-success small fw-bold">Sedang Berjalan...</span>
                                            
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>