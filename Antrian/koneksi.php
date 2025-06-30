<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pelayanan');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

function loginUser($data) {
    global $conn;
    $username = strtolower(htmlspecialchars($data['username']));
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT id, name, username, password, role FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return ['status' => 'error', 'message' => 'Gagal masuk! Pengguna tidak terdaftar.'];
    }

    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return ['status' => 'success'];
    } else {
        return ['status' => 'error', 'message' => 'Gagal masuk! Username atau kata sandi salah.'];
    }
}

function registerUser($data) {
    global $conn;
    $name = htmlspecialchars($data['name']);
    $username = strtolower(htmlspecialchars($data['username']));
    $password = $data['password'];
    $confirmPassword = $data['confirmPassword'];

    $stmt = $conn->prepare("SELECT id FROM user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['status' => 'error', 'message' => 'Registrasi gagal! Username ini sudah terdaftar.'];
    }

    if ($password !== $confirmPassword) {
        return ['status' => 'error', 'message' => 'Registrasi gagal! Kata sandi dan konfirmasi tidak cocok.'];
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO user (name, username, password, role) VALUES (?, ?, ?, 2)");
    $stmt->bind_param("sss", $name, $username, $hashedPassword);
    
    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => 'Registrasi berhasil! Silakan masuk.'];
    } else {
        return ['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data.'];
    }
}

function getAntrian($loket, $statuses, $hanyaHariIni = true) {
    global $conn;
    
    $statusPlaceholders = implode(',', array_fill(0, count($statuses), '?'));
    
    $query = "SELECT * FROM antrian WHERE status IN ($statusPlaceholders)";
    $params = $statuses;
    $types = str_repeat('s', count($statuses));

    if ($hanyaHariIni) {
        $query .= " AND DATE(terdaftar_pada) = CURDATE()";
    }

    if ($loket !== 'ALL') {
        $query .= " AND loket = ?";
        $params[] = $loket;
        $types .= 's';
    }
    
    if (!$hanyaHariIni) {
        $query .= " ORDER BY id DESC";
    } else {
        $query .= " ORDER BY FIELD(status, 'Calling', 'Process', 'Pending'), id ASC";
    }
    
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }
    return false;
}

function buatAntrianBaru($loket) {
    global $conn;
    $prefixes = ['UMUM' => 'U', 'KIA' => 'K', 'LANSIA' => 'L'];
    if (!isset($prefixes[$loket])) {
        return ['success' => false, 'message' => 'Loket tidak valid.'];
    }
    $prefix = $prefixes[$loket];
    
    $conn->begin_transaction();
    try {
        $sql = "SELECT nomor FROM antrian WHERE loket = ? AND DATE(terdaftar_pada) = CURDATE() ORDER BY id DESC LIMIT 1 FOR UPDATE";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $loket);
        $stmt->execute();
        $result = $stmt->get_result();

        $nomorInt = 0;
        if ($result->num_rows > 0) {
            $lastNomor = $result->fetch_assoc()['nomor'];
            $nomorInt = intval(substr($lastNomor, 1));
        }
        $nomorInt++;
        $nomorFormatted = $prefix . str_pad($nomorInt, 3, '0', STR_PAD_LEFT);
        
        $status = 'Pending';
        $terdaftar_pada = date('Y-m-d H:i:s');
        
        $insertStmt = $conn->prepare("INSERT INTO antrian (nomor, loket, status, terdaftar_pada) VALUES (?, ?, ?, ?)");
        $insertStmt->bind_param("ssss", $nomorFormatted, $loket, $status, $terdaftar_pada);
        
        if ($insertStmt->execute()) {
            $conn->commit();
            return ['success' => true, 'data' => ['nomor' => $nomorFormatted, 'loket' => $loket, 'terdaftar_pada' => $terdaftar_pada]];
        } else {
            throw new Exception("Gagal menyimpan antrian.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Mendapatkan informasi untuk layar monitor.
 * Logika baru:
 * 1. Prioritaskan nomor yang sedang 'Calling' atau 'Process' sebagai nomor utama.
 * Jika ada, 'sisa' adalah total semua antrian 'Pending'.
 * 2. Jika tidak ada yang aktif, ambil nomor 'Pending' pertama sebagai nomor utama.
 * 'sisa' adalah total semua antrian 'Pending' dikurangi satu.
 * 3. Jika tidak ada antrian sama sekali, tampilkan default.
 */
function getInfoMonitor() {
    global $conn;
    $lokets = ['UMUM', 'KIA', 'LANSIA'];
    $info = [];
    
    $sql_nomor_aktif = "SELECT nomor FROM antrian WHERE loket = ? AND status IN ('Calling', 'Process') AND DATE(terdaftar_pada) = CURDATE() ORDER BY FIELD(status, 'Process', 'Calling'), id ASC LIMIT 1";
    $sql_nomor_selanjutnya = "SELECT nomor FROM antrian WHERE loket = ? AND status = 'Pending' AND DATE(terdaftar_pada) = CURDATE() ORDER BY id ASC LIMIT 1";
    $sql_total_pending = "SELECT COUNT(id) as total FROM antrian WHERE loket = ? AND status = 'Pending' AND DATE(terdaftar_pada) = CURDATE()";
    
    $stmt_nomor_aktif = $conn->prepare($sql_nomor_aktif);
    $stmt_nomor_selanjutnya = $conn->prepare($sql_nomor_selanjutnya);
    $stmt_total_pending = $conn->prepare($sql_total_pending);

    foreach ($lokets as $loket) {
        $key = strtolower($loket);
        
        $stmt_nomor_aktif->bind_param("s", $loket);
        $stmt_nomor_aktif->execute();
        $result_nomor_aktif = $stmt_nomor_aktif->get_result();

        if ($result_nomor_aktif->num_rows > 0) {
            // Kasus 1: Ada antrian yang sedang aktif (dipanggil/diproses)
            $info[$key]['nomor'] = $result_nomor_aktif->fetch_assoc()['nomor'];
            
            $stmt_total_pending->bind_param("s", $loket);
            $stmt_total_pending->execute();
            $result_total_pending = $stmt_total_pending->get_result();
            $info[$key]['sisa'] = $result_total_pending->fetch_assoc()['total'] ?? 0;

        } else {
            // Kasus 2: Tidak ada antrian aktif, cek antrian yang menunggu
            $stmt_nomor_selanjutnya->bind_param("s", $loket);
            $stmt_nomor_selanjutnya->execute();
            $result_nomor_selanjutnya = $stmt_nomor_selanjutnya->get_result();

            if ($result_nomor_selanjutnya->num_rows > 0) {
                // Tampilkan nomor 'Pending' pertama sebagai nomor utama
                $info[$key]['nomor'] = $result_nomor_selanjutnya->fetch_assoc()['nomor'];

                // Hitung 'sisa' sebagai total antrian 'Pending' dikurangi 1
                $stmt_total_pending->bind_param("s", $loket);
                $stmt_total_pending->execute();
                $total_pending = $stmt_total_pending->get_result()->fetch_assoc()['total'] ?? 0;
                $info[$key]['sisa'] = $total_pending > 0 ? $total_pending - 1 : 0;

            } else {
                // Kasus 3: Tidak ada antrian sama sekali
                $info[$key]['nomor'] = '-';
                $info[$key]['sisa'] = 0;
            }
        }
    }
    return $info;
}
?>