<?php
require '../koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Panggil fungsi getAntrian dengan parameter ketiga 'false' untuk mengambil SEMUA riwayat data
$antrian = getAntrian('ALL', ['Success', 'Skip'], false); 
$nama_user = $_SESSION['name'] ?? 'Admin'; // Nama user diambil dari session
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Antrian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css">
    <style>
        #rekabTable th, #rekabTable td {
            text-align: center;
            vertical-align: middle;
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
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Manajemen Loket</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="loket.php?nama=UMUM">UMUM</a></li>
                            <li><a class="dropdown-item" href="loket.php?nama=KIA">KIA</a></li>
                            <li><a class="dropdown-item" href="loket.php?nama=LANSIA">LANSIA</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link active" href="rekab.php">Rekapitulasi</a></li>
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

    <div class="container text-center" style="margin-top: 100px;">
        <h1 class="h3 mb-4" style="color: var(--primary-color);"><i class="fas fa-history me-2"></i> REKAPITULASI ANTRIAN KESELURUHAN</h1>
        <div class="card">
            <div class="card-header text-center">
                Daftar Semua Antrian yang Telah Selesai
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered" id="rekabTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>No. Antrian</th>
                            <th>Loket</th>
                            <th>Status</th>
                            <th>Waktu Pendaftaran</th>
                            <th>Panggilan Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($antrian): ?>
                            <?php foreach ($antrian as $a): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($a['nomor']) ?></td>
                                    <td><?= htmlspecialchars($a['loket']) ?></td>
                                    <td>
                                        <span class="badge rounded-pill <?= $a['status'] == 'Success' ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= htmlspecialchars($a['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($a['terdaftar_pada']) ?></td>
                                    <td><?= htmlspecialchars($a['updated_at'] ?? $a['terdaftar_pada']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    
    <script>
        // Ambil nama admin dari PHP dan simpan di variabel JavaScript
        const namaAdmin = "<?= htmlspecialchars($nama_user, ENT_QUOTES, 'UTF-8') ?>";
    </script>

    <script>
    $(document).ready(function() {
        const toDataURL = url => fetch(url)
            .then(response => response.blob())
            .then(blob => new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onloadend = () => resolve(reader.result);
                reader.onerror = reject;
                reader.readAsDataURL(blob);
            }));

        toDataURL('../assets/logo.png').then(dataUrl => {
            $('#rekabTable').DataTable({
                dom: "<'row mb-3'<'col-sm-12 d-flex justify-content-center'B>>" +
                     "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                buttons: [
                    { extend: 'copy', exportOptions: { columns: ':visible' } },
                    { extend: 'csv', exportOptions: { columns: ':visible' } },
                    { extend: 'excel', exportOptions: { columns: ':visible' } },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: ':visible'
                        },
                        customize: function (doc) {
                            const tableBody = [];
                            $('#rekabTable').DataTable().rows({ search: 'applied' }).every(function() {
                                const rowData = this.data();
                                tableBody.push([
                                    rowData[0], 
                                    rowData[1], 
                                    $(rowData[2]).text().trim(),
                                    rowData[3], 
                                    rowData[4]
                                ]);
                            });

                            doc.content = [
                                {
                                    image: dataUrl,
                                    width: 80,
                                    alignment: 'center',
                                    margin: [0, 0, 0, 12]
                                },
                                {
                                    text: 'Laporan Rekapitulasi Antrian',
                                    fontSize: 16,
                                    bold: true,
                                    alignment: 'center'
                                },
                                {
                                    // Tambahkan nama admin di sini
                                    text: 'Dicetak oleh: ' + namaAdmin,
                                    fontSize: 9,
                                    italics: true,
                                    alignment: 'center',
                                    margin: [0, 0, 0, 15] // Beri jarak ke tabel
                                },
                                {
                                    table: {
                                        headerRows: 1,
                                        widths: ['auto', 'auto', 'auto', '*', '*'],
                                        body: [
                                            ['No. Antrian', 'Loket', 'Status', 'Waktu Pendaftaran', 'Panggilan Terakhir'],
                                            ...tableBody
                                        ]
                                    },
                                    layout: {
                                        fillColor: function (rowIndex, node, columnIndex) {
                                            return (rowIndex === 0) ? '#CCCCCC' : null;
                                        }
                                    }
                                }
                            ];
                            
                            doc.defaultStyle.alignment = 'center';
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: { columns: ':visible' },
                        customize: function (win) {
                            $(win.document.body).css('font-size', '10pt');
                            $(win.document.body).prepend(
                                '<div style="text-align:center; margin-bottom: 20px;">' +
                                    '<img src="' + dataUrl + '" style="width:100px;" />' +
                                    '<h1 style="font-size: 18pt; margin-top: 10px;">Laporan Rekapitulasi Antrian</h1>' +
                                    // Tambahkan nama admin di sini
                                    '<p style="font-size: 9pt; font-style: italic; margin-top: 5px;">Dicetak oleh: ' + namaAdmin + '</p>' +
                                '</div>'
                            );
                            $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                            $(win.document.body).find('table th, table td').css('text-align', 'center');
                        }
                    }
                ],
                order: [[3, 'desc']], 
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json'
                }
            });
        }).catch(err => {
            console.error("Gagal memuat logo untuk laporan:", err);
        });
    });
    </script>
</body>
</html>