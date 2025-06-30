<?php require '../koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomor Antrian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@700;900&family=Roboto+Mono:wght@700&display=swap');

        :root {
            --primary-color: #1A5F7A;
            --success-color: #28a745;
            --info-color: #17a2b8;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            background-color: #f5f5f5;
            font-family: 'Roboto', sans-serif;
        }

        .main-container {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .header-section {
            background-color: white;
            padding: 1rem;
            border-bottom: 5px solid var(--primary-color);
            flex-shrink: 0;
        }
        .header-title {
            font-weight: 900;
            font-size: 3.5vw;
            color: var(--primary-color);
            margin: 0;
        }
        .header-instruction {
            font-size: 1.5vw;
            color: #6c757d;
        }

        .content-section {
            flex-grow: 1;
            display: flex;
            padding: 1rem;
            gap: 1rem;
        }

        .loket-btn {
            flex-basis: 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.2s ease-in-out;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
        }
        .loket-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .loket-btn i {
            font-size: 8vw;
        }
        .loket-btn span {
            font-size: 4vw;
            font-weight: 900;
            margin-top: 1rem;
        }

        .card-umum { background-color: var(--success-color); }
        .card-kia { background-color: var(--primary-color); }
        .card-lansia { background-color: var(--info-color); }

        #ticket-print {
            font-family: 'Roboto Mono', monospace;
            width: 300px; padding: 20px;
            border: 1px solid #ccc; margin: 0 auto;
        }
        #ticket-print .nomor-antrian {
            font-size: 4rem; font-weight: 700;
            margin: 10px 0; color: black;
        }
        #ticket-print hr { border-top: 2px dashed #333; }

        @media print {
            body * { visibility: hidden; }
            #ticket-print, #ticket-print * { visibility: visible; }
            #ticket-print { position: absolute; left: 0; top: 0; width: 100%; border: none; }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <header class="header-section text-center">
            <h1 class="header-title">SELAMAT DATANG</h1>
            <p class="header-instruction">Silakan Sentuh Layanan yang Anda Tuju</p>
        </header>

        <main class="content-section">
            <button class="loket-btn card-umum" data-loket="UMUM">
                <i class="fas fa-users"></i>
                <span>UMUM</span>
            </button>
            <button class="loket-btn card-kia" data-loket="KIA">
                <i class="fas fa-baby"></i>
                <span>KIA</span>
            </button>
            <button class="loket-btn card-lansia" data-loket="LANSIA">
                <i class="fas fa-wheelchair"></i>
                <span>LANSIA</span>
            </button>
        </main>
    </div>

    <div class="modal fade" id="ticketModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tiket Antrian Anda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="ticket-print" class="text-center">
                        <h5 class="fw-bold">PUSKESMAS PEMBANTU</h5>
                        <p style="font-size: 0.8rem;">Jl. Raya Gondang No. 123</p>
                        <hr>
                        <p class="mb-0">NOMOR ANTRIAN</p>
                        <h1 class="nomor-antrian" id="nomor-antrian-display">-</h1>
                        <p class="fw-bold">LOKET <span id="loket-display">-</span></p>
                        <hr>
                        <p id="waktu-daftar-display" style="font-size: 0.8rem;"></p>
                        <p class="fw-bold mt-2">TERIMA KASIH</p>
                        <p style="font-size: 0.7rem;">Simpan tiket ini dan tunggu nomor Anda dipanggil</p>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-2"></i>Cetak Tiket</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        const ticketModal = new bootstrap.Modal(document.getElementById('ticketModal'));

        $('.loket-btn').on('click', function() {
            const button = $(this);
            const loket = button.data('loket');

            $.ajax({
                url: '../api.php',
                type: 'POST',
                data: { action: 'buatAntrian', loket: loket },
                dataType: 'json',
                beforeSend: function() {
                    button.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        $('#nomor-antrian-display').text(response.data.nomor);
                        $('#loket-display').text(response.data.loket);
                        const event = new Date(response.data.terdaftar_pada);
                        const timeOptions = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                        $('#waktu-daftar-display').text(new Intl.DateTimeFormat('id-ID', timeOptions).format(event));
                        ticketModal.show();
                    } else {
                        alert('Gagal membuat antrian: ' + (response.message || 'Error tidak diketahui'));
                    }
                },
                error: function() {
                    alert('Tidak dapat terhubung ke server.');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        });
    });
    </script>
</body>
</html>