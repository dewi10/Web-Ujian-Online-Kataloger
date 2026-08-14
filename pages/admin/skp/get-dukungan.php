<?php
session_start();
require_once('../../../config/database.php');

// Cek tabel ada atau tidak
$check_table = mysqli_query($kon, "SHOW TABLES LIKE 'skp_dukungan_sumber_daya'");
if(mysqli_num_rows($check_table) == 0) {
    // Buat tabel jika belum ada
    mysqli_query($kon, "CREATE TABLE IF NOT EXISTS skp_dukungan_sumber_daya (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uraian TEXT,
        urutan INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

$query = mysqli_query($kon, "SELECT * FROM skp_dukungan_sumber_daya ORDER BY urutan ASC");
$items = [];

while($row = mysqli_fetch_assoc($query)) {
    $items[] = $row;
}

header('Content-Type: application/json');
echo json_encode($items);
?>
