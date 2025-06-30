<?php
require 'koneksi.php'; // Otomatis menjalankan session_start()

header('Content-Type: application/json');

$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) ?? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING) ?? '';

if (empty($action)) {
    echo json_encode(['success' => false, 'message' => 'Aksi tidak ditentukan.']);
    exit();
}

// Aksi yang tidak memerlukan login
if ($action === 'buatAntrian') {
    $loket = $_POST['loket'] ?? '';
    $response = buatAntrianBaru($loket);
    echo json_encode($response);
    exit();
}

if ($action === 'getInfoMonitor') {
    $response = getInfoMonitor();
    echo json_encode(['success' => true, 'data' => $response]);
    exit();
}


// --- Mulai dari sini, semua aksi memerlukan login ---
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Silakan login terlebih dahulu.']);
    exit();
}

switch ($action) {
    case 'updateStatus':
        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $valid_statuses = ['Calling', 'Process', 'Success', 'Skip'];

        if ($id > 0 && in_array($status, $valid_statuses)) {
            global $conn;
            $stmt = $conn->prepare("UPDATE antrian SET status = ?, updated_at = NOW() WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $status, $id);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => "Status antrian berhasil diubah."]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Gagal mengubah status di database.']);
                }
            } else {
                 echo json_encode(['success' => false, 'message' => 'Gagal mempersiapkan statement SQL.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenal.']);
        break;
}
?>