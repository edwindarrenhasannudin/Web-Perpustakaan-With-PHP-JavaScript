<?php
require_once 'config.php';

$page_title = "Dashboard";
$active_page = 'dashboard';
include 'header.php';

// Pastikan koneksi database tersedia
if (!isset($db) || !$db instanceof PDO) {
    die("Koneksi database tidak tersedia.");
}

try {
    // Hitung total buku
    $stmt = $db->prepare("SELECT COUNT(*) FROM buku");
    $stmt->execute();
    $total_buku = $stmt->fetchColumn();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $total_buku = 0; // Default jika terjadi kesalahan
}
?>

<head>
    <link rel="stylesheet" href="CSS/styles-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    
    <!-- Cards Statistik -->
    <div class="row">
        <!-- Total Buku -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-primary border-5 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Buku</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= htmlspecialchars($total_buku) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Anggota -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-success border-5 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Total Anggota</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $db->query("SELECT COUNT(*) FROM anggota")->fetchColumn() ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Peminjaman Aktif -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-info border-5 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Peminjaman Aktif</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                <?= $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Dipinjam'")->fetchColumn() ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Total Denda -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-warning border-5 shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Total Denda</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">
                                Rp <?= number_format($db->query("SELECT COALESCE(SUM(denda), 0) FROM pengembalian")->fetchColumn(), 0, ',', '.') ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafik dan Tabel -->
    <div class="row">
        <!-- Grafik Peminjaman -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Statistik Peminjaman 7 Hari Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="peminjamanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Buku Populer -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Buku Terpopuler</h6>
                </div>
                <div class="card-body">
                    <?php
                    $books = $db->query("
                        SELECT b.judul, COUNT(dp.buku_id) as jumlah 
                        FROM detail_peminjaman dp
                        JOIN buku b ON dp.buku_id = b.id
                        GROUP BY dp.buku_id 
                        ORDER BY jumlah DESC 
                        LIMIT 5
                    ")->fetchAll();
                    
                    foreach ($books as $book): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold"><?= $book['judul'] ?></span>
                            <span class="badge bg-primary rounded-pill"><?= $book['jumlah'] ?>x</span>
                        </div>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: <?= ($book['jumlah'] / max(1, $books[0]['jumlah'])) * 100 ?>%" 
                                 aria-valuenow="<?= $book['jumlah'] ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="<?= max(1, $books[0]['jumlah']) ?>">
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Peminjaman Terakhir -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-primary">Peminjaman Terakhir</h6>
            <a href="peminjaman/" class="btn btn-sm btn-primary">Lihat Semua</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Kode</th>
                            <th>Anggota</th>
                            <th>Tanggal Pinjam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $loans = $db->query("
                            SELECT p.*, a.nama as anggota 
                            FROM peminjaman p
                            JOIN anggota a ON p.anggota_id = a.id
                            ORDER BY p.tgl_pinjam DESC 
                            LIMIT 5
                        ")->fetchAll();
                        
                        foreach ($loans as $loan): 
                            $status_class = [
                                'Dipinjam' => 'primary',
                                'Dikembalikan' => 'success',
                                'Terlambat' => 'danger',
                                'Hilang' => 'dark'
                            ];
                        ?>
                        <tr>
                            <td><?= $loan['kode_peminjaman'] ?></td>
                            <td><?= $loan['anggota'] ?></td>
                            <td><?= date('d M Y', strtotime($loan['tgl_pinjam'])) ?></td>
                            <td>
                                <span class="badge bg-<?= $status_class[$loan['status']] ?? 'secondary' ?>">
                                    <?= $loan['status'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="peminjaman/detail.php?id=<?= $loan['id'] ?>" 
                                   class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
include 'footer.php';
?>