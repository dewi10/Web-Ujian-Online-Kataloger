<?php
session_start();
require_once('../../../config/database.php');

// Cek akses
if(!isset($_SESSION['level']) || ($_SESSION['level'] != 'admin' && $_SESSION['level'] != 'guru')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$json = file_get_contents('php://input');
$items = json_decode($json, true);

// Hapus semua data lama
mysqli_query($kon, "DELETE FROM skp_skema_pertanggungjawaban");

// Insert data baru
foreach($items as $index => $item) {
    $uraian = mysqli_real_escape_string($kon, $item['uraian']);
    $urutan = $index + 1;
    
    mysqli_query($kon, "INSERT INTO skp_skema_pertanggungjawaban (uraian, urutan) 
                        VALUES ('$uraian', $urutan)");
}

echo json_encode(['success' => true]);
?>
