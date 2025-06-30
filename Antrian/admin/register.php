<?php
require '../koneksi.php';

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$error_message = '';
if (isset($_POST['REGISTER'])) {
    $result = registerUser($_POST);
    if ($result['status'] === 'error') {
        $error_message = $result['message'];
    } else {
        $_SESSION['success_message'] = $result['message'];
        header("Location: login.php");
        exit();
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 0;
        }
    </style>
</head>
<body>
    <div class="card shadow-lg" style="width: 28rem;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <img src="../assets/logo.png" alt="Logo Daftar" class="mb-3" style="width: 80px; height: auto;">

                <h2 class="fw-bold text-primary">BUAT AKUN BARU</h2>
                <p class="text-muted">Lengkapi formulir untuk mendaftar.</p>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Nama Lengkap" autocomplete="off" required>
                    <label for="name">Nama Lengkap</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" autocomplete="off" required>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Kata Sandi" autocomplete="off" required>
                    <label for="password">Kata Sandi</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="confirmPassword" id="confirmPassword" placeholder="Konfirmasi Kata Sandi" autocomplete="off" required>
                    <label for="confirmPassword">Konfirmasi Kata Sandi</label>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="REGISTER" class="btn btn-primary fw-bold py-3">DAFTAR</button>
                </div>
                <div class="text-center mt-4">
                    <small>Sudah punya akun? <a href="login.php" class="fw-bold text-primary">Masuk di sini</a></small>
                </div>
            </form>
        </div>
    </div>
</body>
</html>