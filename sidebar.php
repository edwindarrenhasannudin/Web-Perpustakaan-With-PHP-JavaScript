<?php
$active_page = $active_page ?? ''; // Pastikan $active_page terdefinisi
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="CSS/styles-sidebar.css">

<div class="sidebar bg-dark text-white">
    <div class="sidebar-header text-center py-4">
        <img src="../assets/img/logo.png" alt="Logo" class="img-fluid" width="80">
        <h4 class="mt-3">Perpustakaan</h4>
    </div>

    <ul class="list-unstyled components">
        <li class="<?= $active_page == 'dashboard' ? 'active' : '' ?>">
            <a href="../dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
        </li>
        <li class="<?= $active_page == 'buku' ? 'active' : '' ?>">
            <a href="../buku/index.php"><i class="bi bi-book"></i> Manajemen Buku</a>
        </li>
        <li class="<?= $active_page == 'anggota' ? 'active' : '' ?>">
            <a href="../anggota/index.php"><i class="bi bi-people"></i> Data Anggota</a>
        </li>
        <li class="<?= $active_page == 'peminjaman' ? 'active' : '' ?>">
            <a href="../peminjaman/index.php"><i class="bi bi-journal-plus"></i> Peminjaman</a>
        </li>
        <li class="<?= $active_page == 'pengembalian' ? 'active' : '' ?>">
            <a href="../pengembalian/index.php"><i class="bi bi-journal-check"></i> Pengembalian</a>
        </li>
        <li class="<?= $active_page == 'laporan' ? 'active' : '' ?>">
            <a href="../laporan/index.php"><i class="bi bi-file-earmark-bar-graph"></i> Laporan</a>
        </li>
        <li class="<?= $active_page == 'profile' ? 'active' : '' ?>">
            <a href="../profile.php"><i class="bi bi-person"></i> Profil</a>
        </li>
    </ul>
</div>