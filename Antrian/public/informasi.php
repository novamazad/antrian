<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Antrian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@700;900&display=swap');

        :root {
            --primary-color: #1A5F7A;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
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
        .current-time {
            font-size: 1.5vw;
            color: #6c757d;
        }

        .content-section {
            flex-grow: 1;
            display: flex;
            padding: 1rem;
            gap: 1rem;
        }

        .info-card {
            flex-basis: 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .info-card .card-header {
            font-size: 4vw;
            font-weight: 900;
            padding: 0.5rem;
        }
        
        .info-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: white;
            position: relative; /* Diperlukan untuk footer */
        }
        
        .info-card .card-footer {
            font-size: 1.5vw;
            font-weight: 700;
            padding: 0.5rem;
            width: 100%;
            text-align: center;
            background-color: rgba(0,0,0,0.1); /* Latar belakang footer */
        }

        .label-nomor {
            font-size: 2vw;
            font-weight: 700;
            margin-bottom: -1rem;
        }

        .info-nomor {
            font-weight: 900;
            line-height: 1;
            font-size: 13vw;
            transition: transform 0.3s ease-in-out;
        }

        .info-nomor.updated {
            transform: scale(1.1);
            color: var(--warning-color) !important;
        }

        .card-umum { background-color: var(--success-color); }
        .text-umum { color: var(--success-color); }
        .card-kia { background-color: var(--primary-color); }
        .text-kia { color: var(--primary-color); }
        .card-lansia { background-color: var(--info-color); }
        .text-lansia { color: var(--info-color); }
        
        .footer-ticker {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem;
            flex-shrink: 0;
            overflow: hidden;
            white-space: nowrap;
        }
        .ticker-content {
            display: inline-block;
            padding-left: 100%;
            animation: ticker-animation 30s linear infinite;
        }
        .ticker-content i {
            margin: 0 0.5rem 0 2rem;
        }
        @keyframes ticker-animation {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <header class="header-section text-center">
            <h1 class="header-title">ANTRIAN PUSKESMAS PEMBANTU</h1>
            <p class="current-time" id="clock"></p>
        </header>

        <main class="content-section">
            <div class="info-card">
                <div class="card-header text-center card-umum">UMUM</div>
                <div class="card-body">
                    <p class="label-nomor text-umum">NOMOR</p>
                    <p class="info-nomor text-umum" id="info-umum-nomor">-</p>
                </div>
                <div class="card-footer text-umum">
                    SISA ANTRIAN: <span id="info-umum-sisa">0</span>
                </div>
            </div>
            <div class="info-card">
                <div class="card-header text-center card-kia">KIA</div>
                <div class="card-body">
                    <p class="label-nomor text-kia">NOMOR</p>
                    <p class="info-nomor text-kia" id="info-kia-nomor">-</p>
                </div>
                <div class="card-footer text-kia">
                    SISA ANTRIAN: <span id="info-kia-sisa">0</span>
                </div>
            </div>
            <div class="info-card">
                <div class="card-header text-center card-lansia">LANSIA</div>
                <div class="card-body">
                    <p class="label-nomor text-lansia">NOMOR</p>
                    <p class="info-nomor text-lansia" id="info-lansia-nomor">-</p>
                </div>
                <div class="card-footer text-lansia">
                    SISA ANTRIAN: <span id="info-lansia-sisa">0</span>
                </div>
            </div>
        </main>

        <footer class="footer-ticker">
            <div class="ticker-content">
                <i class="fas fa-clock"></i> Jam Operasional: Senin - Sabtu, 08:00 - 14:00 WIB
                <i class="fas fa-smoking-ban"></i> Ini adalah Kawasan Dilarang Merokok
                <i class="fas fa-head-side-cough"></i> Terapkan etika batuk dan bersin untuk kesehatan bersama
                <i class="fas fa-mobile-alt"></i> Harap getarkan nada dering ponsel Anda
                <i class="fas fa-hands-wash"></i> Jaga selalu kebersihan dan buang sampah pada tempatnya
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('clock').textContent = new Intl.DateTimeFormat('id-ID', options).format(now);
        }

        function updateMonitor() {
            $.ajax({
                url: '../api.php?action=getInfoMonitor', // Memanggil API untuk data monitor
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        Object.keys(data).forEach(loket => {
                            // Perbarui Nomor Antrian
                            const elNomor = $('#info-' + loket + '-nomor');
                            const nomorBaru = data[loket].nomor || '-';
                            if (elNomor.text() !== nomorBaru) {
                                elNomor.text(nomorBaru).addClass('updated');
                                setTimeout(() => elNomor.removeClass('updated'), 400);
                            }

                            // Perbarui Sisa Antrian
                            const elSisa = $('#info-' + loket + '-sisa');
                            const sisaBaru = data[loket].sisa || 0;
                            elSisa.text(sisaBaru);
                        });
                    }
                },
                error: function() {
                    console.error("Gagal mengambil data monitor.");
                }
            });
        }

        $(document).ready(function() {
            updateClock();
            setInterval(updateClock, 1000);
            updateMonitor();
            setInterval(updateMonitor, 3000); // Refresh data setiap 3 detik
        });
    </script>
</body>
</html>