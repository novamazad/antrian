<?php
require '../koneksi.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$nama_user = $_SESSION['name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .loket-card {
            text-decoration: none;
            color: white;
            border-radius: 15px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
            height: 180px;
        }
        .loket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .loket-card i {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        .loket-card span {
            font-size: 1.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg admin-nav fixed-top">
        <div class="container">
             <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="../assets/logo.png" alt="Logo Dashboard" style="height: 28px; margin-right: 10px;">
                DASHBOARD
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Manajemen Loket</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="loket.php?nama=UMUM">UMUM</a></li>
                            <li><a class="dropdown-item" href="loket.php?nama=KIA">KIA</a></li>
                            <li><a class="dropdown-item" href="loket.php?nama=LANSIA">LANSIA</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="rekab.php">Rekapitulasi</a></li>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($nama_user) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container content-wrapper" style="margin-top: 100px;">
        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold" style="color: var(--primary-color);">Selamat Datang, <?= htmlspecialchars($nama_user) ?>!</h1>
                <p class="col-md-8 fs-4">Pilih loket yang ingin Anda kelola dari dasbor di bawah ini.</p>
            </div>
        </div>
        
        <div class="row text-center g-4">
            <div class="col-lg-4 col-md-6">
                <a href="loket.php?nama=UMUM" class="loket-card shadow" style="background-color: var(--success-color);">
                    <i class="fas fa-users"></i>
                    <span>UMUM</span>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="loket.php?nama=KIA" class="loket-card shadow" style="background-color: var(--primary-color);">
                    <i class="fas fa-baby"></i>
                    <span>KIA</span>
                </a>
            </div>
            <div class="col-lg-4 col-md-6">
                <a href="loket.php?nama=LANSIA" class="loket-card shadow" style="background-color: var(--info-color);">
                    <i class="fas fa-wheelchair"></i>
                    <span>LANSIA</span>
                </a>
            </div>
        </div>
    </main>

    <footer class="footer-ticker">
        <div class="ticker-content">
            <i class="fas fa-info-circle"></i> &copy; <?= date('Y') ?> Pelayanan Kesehatan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i class="fas fa-desktop"></i> Dikelola oleh Administrator &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i class="fas fa-hospital"></i> Puskesmas Pembantu 
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>