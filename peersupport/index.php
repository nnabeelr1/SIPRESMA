<?php
session_start();
if (!isset($_SESSION['status'])) { header("Location: ../index.php"); exit(); }
if ($_SESSION['role'] != 'admin') { header("Location: ../dashboard/welcome_mhs.php"); exit(); }

include '../config/koneksi.php';

// --- ALGORITMA MATCHING V2 (LEBIH PINTAR) ---

// 1. Ambil semua Mentee (IPK < 2.50) yang BELUM punya mentor AKTIF/PENDING
// Kita exclude yang statusnya 'aktif' atau 'menunggu_dosen'
$q_mentee = mysqli_query($koneksi, "
    SELECT * FROM mahasiswa 
    WHERE ipk_terakhir < 2.50 
    AND nim NOT IN (SELECT mentee_nim FROM peer_support WHERE status IN ('aktif', 'menunggu_dosen'))
");

// 2. Ambil semua Mentor (IPK >= 3.50) dan simpan dalam Array
$mentors = [];
$q_mentor = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE ipk_terakhir >= 3.50");
while ($m = mysqli_fetch_assoc($q_mentor)) {
    $mentors[$m['id_prodi']][] = $m;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Peer Support Matchmaking</title>
    <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>🤝 Peer Support Matchmaking</h3>
            <a href="../dashboard/index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>

        <div class="card border-0 shadow-sm mb-5">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">🤖 Rekomendasi Pasangan (AI Matching V2)</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-light border">
                    Sistem kini mengacak mentor dan menghindari pasangan yang pernah ditolak sebelumnya.
                </div>

                <div class="row">
                    <?php
                    $found_match = false;

                    while ($mentee = mysqli_fetch_assoc($q_mentee)) {
                        $prodi_mentee = $mentee['id_prodi'];
                        
                        // Cek: Ada gak Mentor di prodi yang sama?
                        if (isset($mentors[$prodi_mentee]) && count($mentors[$prodi_mentee]) > 0) {
                            
                            // --- LOGIKA BARU: MENCARI MENTOR YANG COCOK ---
                            $calon_mentors = $mentors[$prodi_mentee];
                            shuffle($calon_mentors); // ACAK MENTOR BIAR GAK ITU-ITU AJA
                            
                            $mentor_terpilih = null;

                            foreach ($calon_mentors as $calon) {
                                // Cek History: Apakah pasangan Mentee & Mentor ini pernah ditolak?
                                $cek_tolak = mysqli_query($koneksi, "SELECT * FROM peer_support 
                                            WHERE mentee_nim = '{$mentee['nim']}' 
                                            AND mentor_nim = '{$calon['nim']}' 
                                            AND status = 'ditolak'");
                                
                                // Kalau belum pernah ditolak, pilih dia!
                                if (mysqli_num_rows($cek_tolak) == 0) {
                                    $mentor_terpilih = $calon;
                                    break; // Stop nyari, udah ketemu satu
                                }
                            }

                            // Kalau ketemu mentor yang pas (dan belum pernah ditolak)
                            if ($mentor_terpilih != null) {
                                $found_match = true;
                                ?>

                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border-info">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="text-center text-danger">
                                                    <h1 class="mb-0">🆘</h1>
                                                    <strong><?php echo $mentee['nama_lengkap']; ?></strong><br>
                                                    <small>IPK: <?php echo $mentee['ipk_terakhir']; ?></small>
                                                </div>

                                                <div class="text-muted fs-4">➡️</div>

                                                <div class="text-center text-success">
                                                    <h1 class="mb-0">🎓</h1>
                                                    <strong><?php echo $mentor_terpilih['nama_lengkap']; ?></strong><br>
                                                    <small>IPK: <?php echo $mentor_terpilih['ipk_terakhir']; ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-white border-0 text-center">
                                            <form action="create.php" method="POST">
                                                <input type="hidden" name="mentee_nim" value="<?php echo $mentee['nim']; ?>">
                                                <input type="hidden" name="mentor_nim" value="<?php echo $mentor_terpilih['nim']; ?>">
                                                <button type="submit" name="match" class="btn btn-success w-100">
                                                    ✅ Usulkan ke Dosen Wali
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            <?php 
                            } 
                        } 
                    } 

                    if (!$found_match) {
                        echo "<div class='col-12 text-center text-muted'><i>Semua mentee sudah punya pasangan atau tidak ada mentor yang tersedia.</i></div>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">📋 Monitoring Status Mentoring</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Mentee</th>
                            <th>Mentor</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q_active = mysqli_query($koneksi, "
                            SELECT ps.*, 
                                   m1.nama_lengkap as nama_mentee, 
                                   m2.nama_lengkap as nama_mentor 
                            FROM peer_support ps
                            JOIN mahasiswa m1 ON ps.mentee_nim = m1.nim
                            JOIN mahasiswa m2 ON ps.mentor_nim = m2.nim
                            ORDER BY ps.id_match DESC
                        ");
                        
                        while ($row = mysqli_fetch_assoc($q_active)) {
                        ?>
                            <tr>
                                <td><?php echo $row['nama_mentee']; ?></td>
                                <td><?php echo $row['nama_mentor']; ?></td>
                                <td>
                                    <?php 
                                    if ($row['status'] == 'aktif') echo '<span class="badge bg-success">AKTIF</span>';
                                    elseif ($row['status'] == 'ditolak') echo '<span class="badge bg-danger">DITOLAK DOSEN</span>';
                                    else echo '<span class="badge bg-warning text-dark">MENUNGGU DOSEN</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="delete.php?id=<?php echo $row['id_match']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>