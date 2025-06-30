<?php
require '../koneksi.php';

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$error_message = '';
if(isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_POST['LOGIN'])) {
    $result = loginUser($_POST);
    if ($result['status'] === 'error') {
        $error_message = $result['message'];
    } else {
        header("Location: index.php");
        exit();
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
    </style>
</head>
<body>
    <div class="card shadow-lg" style="width: 25rem;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <img src="../assets/logo.png" alt="Logo Puskesmas" class="mb-3" style="width: 80px; height: auto;">
                
                <h2 class="fw-bold text-primary">ADMIN LOGIN</h2>
                <p class="text-muted">Selamat datang kembali, silakan masuk.</p>
            </div>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger" role="alert"><i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success" role="alert"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success_message) ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="username" id="username" placeholder="Username" autocomplete="off" required>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Kata Sandi" autocomplete="off" required>
                    <label for="password">Kata Sandi</label>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" name="LOGIN" class="btn btn-primary fw-bold py-3">MASUK</button>
                </div>
                <div class="text-center mt-4">
                    <small>Belum punya akun? <a href="register.php" class="fw-bold text-primary">Daftar di sini</a></small>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>