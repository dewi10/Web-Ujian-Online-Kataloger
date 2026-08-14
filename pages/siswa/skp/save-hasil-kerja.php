<?php
session_start();
include "../../../config/database.php";

// Validasi login dan admin
if (!isset($_SESSION['username']) || $_SESSION['level'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

// Hapus semua data lama
mysqli_query($kon, "DELETE FROM skp_hasil_kerja");

// Insert data baru
foreach ($data as $item) {
    $kategori = mysqli_real_escape_string($kon, $item['kategori']);
    $uraian = mysqli_real_escape_string($kon, $item['uraian']);
    $ekspektasi = mysqli_real_escape_string($kon, $item['ekspektasi']);
    $urutan = intval($item['urutan']);
    
    $query = "INSERT INTO skp_hasil_kerja (kategori, uraian, ekspektasi, urutan) 
              VALUES ('$kategori', '$uraian', '$ekspektasi', $urutan)";
    mysqli_query($kon, $query);
}

echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
?>
