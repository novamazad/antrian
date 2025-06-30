<?php
require '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$loket_nama = strtoupper(htmlspecialchars($_GET['nama'] ?? ''));
$valid_loket = ['UMUM', 'KIA', 'LANSIA'];
if (!in_array($loket_nama, $valid_loket)) {
    header("Location: index.php");
    exit();
}

$antrian = getAntrian($loket_nama, ['Pending', 'Calling', 'Process']);
$nama_user = $_SESSION['name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loket <?= $loket_nama ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .btn-group {
            display: flex;
            justify-content: center;
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown">Manajemen Loket</a>
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

    <div class="container" style="margin-top: 100px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-3" style="color: var(--primary-color);"><i class="fas fa-tasks me-2"></i>LOKET: <strong><?= $loket_nama ?></strong></h1>
                    <button class="btn btn-sm btn-secondary" onclick="location.reload()"><i class="fas fa-sync-alt me-2"></i> Segarkan</button>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">No. Antrian</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Waktu Daftar</th>
                                        <th scope="col" style="width: 40%;">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($antrian->num_rows > 0):
                                        $isFirst = true;
                                        foreach ($antrian as $a): 
                                            $status_class = '';
                                            switch($a['status']) {
                                                case 'Calling': $status_class = 'bg-warning text-dark'; break;
                                                case 'Process': $status_class = 'bg-info text-dark'; break;
                                                case 'Pending': $status_class = 'bg-primary'; break;
                                            }
                                    ?>
                                            <tr id="row-<?= $a['id'] ?>" class="<?= $isFirst ? 'table-light fw-bold' : '' ?>">
                                                <td class="fs-4"><?= htmlspecialchars($a['nomor']) ?></td>
                                                <td><span class="badge rounded-pill fs-6 <?= $status_class ?>"><?= htmlspecialchars($a['status']) ?></span></td>
                                                <td><?= date('H:i:s', strtotime($a['terdaftar_pada'])) ?></td>
                                                <td>
                                                    <?php if ($isFirst): ?>
                                                        <div class="btn-group" role="group">
                                                            <?php if ($a['status'] == 'Pending'): ?>
                                                                <button class="btn btn-primary action-btn" data-id="<?= $a['id'] ?>" data-status="Calling" data-nomor="<?= $a['nomor'] ?>" data-loket="<?= $a['loket'] ?>"><i class="fas fa-volume-up me-2"></i>Panggil</button>
                                                            <?php elseif ($a['status'] == 'Calling'): ?>
                                                                <button class="btn btn-primary action-btn" data-id="<?= $a['id'] ?>" data-status="Calling" data-nomor="<?= $a['nomor'] ?>" data-loket="<?= $a['loket'] ?>"><i class="fas fa-redo-alt me-2"></i>Panggil Ulang</button>
                                                                <button class="btn btn-info action-btn text-white" data-id="<?= $a['id'] ?>" data-status="Process"><i class="fas fa-cogs me-2"></i>Proses</button>
                                                            <?php elseif ($a['status'] == 'Process'): ?>
                                                                <button class="btn btn-success action-btn" data-id="<?= $a['id'] ?>" data-status="Success"><i class="fas fa-check-circle me-2"></i>Selesai</button>
                                                            <?php endif; ?>
                                                            <button class="btn btn-secondary action-btn" data-id="<?= $a['id'] ?>" data-status="Skip"><i class="fas fa-forward me-2"></i>Lewati</button>
                                                        </div>
                                                        <?php $isFirst = false; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center p-5 text-muted">
                                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                                <p class="mb-0">Belum ada antrian aktif untuk loket ini.</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
    $(document).ready(function() {
        toastr.options = { "positionClass": "toast-bottom-right", "timeOut": 3000, "progressBar": true };

        function speak(nomor, loket) {
            const nomorFormatted = nomor.split('').join(' ');
            const text = `Nomor antrian, ${nomorFormatted}, silahkan menuju ke loket ${loket}`;
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 0.9;
                window.speechSynthesis.speak(utterance);
            } else {
                toastr.warning('Browser Anda tidak mendukung fitur suara otomatis.');
            }
        }

        $('.action-btn').on('click', function() {
            const button = $(this);
            const originalHtml = button.html();
            const antrianId = button.data('id');
            const newStatus = button.data('status');

            if (newStatus === 'Calling') {
                speak(button.data('nomor'), button.data('loket'));
            }

            $.ajax({
                url: '../api.php',
                type: 'POST',
                data: { action: 'updateStatus', id: antrianId, status: newStatus },
                dataType: 'json',
                beforeSend: function() {
                    $('.action-btn').prop('disabled', true);
                    button.html('<span class="spinner-border spinner-border-sm" role="status"></span>');
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Status berhasil diperbarui.');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        toastr.error(response.message || 'Terjadi kesalahan.');
                        $('.action-btn').prop('disabled', false);
                        button.html(originalHtml);
                    }
                },
                error: function() {
                    toastr.error('Tidak dapat terhubung ke server.');
                    $('.action-btn').prop('disabled', false);
                    button.html(originalHtml);
                }
            });
        });
    });
    </script>
</body>
</html>