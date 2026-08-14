<?php
session_start();
include "../../../config/database.php";

// Check table exists, if not create it
$check_table = mysqli_query($kon, "SHOW TABLES LIKE 'skp_hasil_kerja'");
if (mysqli_num_rows($check_table) == 0) {
    $create_table = "CREATE TABLE `skp_hasil_kerja` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `kategori` enum('A','B') NOT NULL,
        `uraian` text NOT NULL,
        `ekspektasi` text,
        `urutan` int(11) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($kon, $create_table);
}

// Get all hasil kerja
$query = mysqli_query($kon, "SELECT * FROM skp_hasil_kerja ORDER BY kategori, urutan ASC");
$result = [];

while ($row = mysqli_fetch_assoc($query)) {
    $result[] = $row;
}

echo json_encode($result);
?>
