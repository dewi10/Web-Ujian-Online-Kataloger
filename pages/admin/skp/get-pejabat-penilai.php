<?php
session_start();
include "../../../config/database.php";

// Create table if not exists
$create_table = "CREATE TABLE IF NOT EXISTS skp_pejabat_penilai (
    id INT PRIMARY KEY AUTO_INCREMENT,
    puskod VARCHAR(200),
    periode_awal DATE,
    periode_akhir DATE,
    pejabat_nama VARCHAR(200),
    pejabat_nip VARCHAR(100),
    pejabat_pangkat VARCHAR(200),
    pejabat_jabatan VARCHAR(200),
    pejabat_unit VARCHAR(200),
    atasan_nama VARCHAR(200),
    atasan_nip VARCHAR(100),
    atasan_pangkat VARCHAR(200),
    atasan_jabatan VARCHAR(200),
    atasan_unit VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
mysqli_query($kon, $create_table);

// Get data
$query = mysqli_query($kon, "SELECT * FROM skp_pejabat_penilai LIMIT 1");

if($row = mysqli_fetch_assoc($query)) {
    echo json_encode($row);
} else {
    // Return default data
    echo json_encode([
        'puskod' => 'PUSKOD BALOGHAN KEMHAN',
        'periode_awal' => '2025-01-02',
        'periode_akhir' => '2025-12-31',
        'pejabat_nama' => 'Tisna Kurniawan',
        'pejabat_nip' => '518837',
        'pejabat_pangkat' => 'Marsekal Pertama TNI',
        'pejabat_jabatan' => 'Kepala Pusat Kodifikasi',
        'pejabat_unit' => 'Baloghan Kemhan',
        'atasan_nama' => 'Yusuf Jauhari, M.Eng',
        'atasan_nip' => '514557',
        'atasan_pangkat' => 'Marsekal Madya TNI',
        'atasan_jabatan' => 'Kepala Badan Logistik Pertahanan',
        'atasan_unit' => 'Baloghan Kemhan'
    ]);
}

mysqli_close($kon);
?>
