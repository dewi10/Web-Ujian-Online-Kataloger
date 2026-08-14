<?php
session_start();
include "../../../config/database.php";

// Check if user is admin or guru
if(!isset($_SESSION['level']) || ($_SESSION['level'] != 'admin' && $_SESSION['level'] != 'guru')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

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

// Check if record exists
$check = mysqli_query($kon, "SELECT id FROM skp_pejabat_penilai LIMIT 1");
$exists = mysqli_num_rows($check) > 0;

if($exists) {
    // Update
    $stmt = mysqli_prepare($kon, "UPDATE skp_pejabat_penilai SET 
        puskod = ?,
        periode_awal = ?,
        periode_akhir = ?,
        pejabat_nama = ?,
        pejabat_nip = ?,
        pejabat_pangkat = ?,
        pejabat_jabatan = ?,
        pejabat_unit = ?,
        atasan_nama = ?,
        atasan_nip = ?,
        atasan_pangkat = ?,
        atasan_jabatan = ?,
        atasan_unit = ?
        WHERE id = (SELECT id FROM (SELECT id FROM skp_pejabat_penilai LIMIT 1) as tmp)");
    
    mysqli_stmt_bind_param($stmt, "sssssssssssss",
        $data['puskod'],
        $data['periode_awal'],
        $data['periode_akhir'],
        $data['pejabat_nama'],
        $data['pejabat_nip'],
        $data['pejabat_pangkat'],
        $data['pejabat_jabatan'],
        $data['pejabat_unit'],
        $data['atasan_nama'],
        $data['atasan_nip'],
        $data['atasan_pangkat'],
        $data['atasan_jabatan'],
        $data['atasan_unit']
    );
} else {
    // Insert
    $stmt = mysqli_prepare($kon, "INSERT INTO skp_pejabat_penilai 
        (puskod, periode_awal, periode_akhir, pejabat_nama, pejabat_nip, pejabat_pangkat, pejabat_jabatan, pejabat_unit,
         atasan_nama, atasan_nip, atasan_pangkat, atasan_jabatan, atasan_unit)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    mysqli_stmt_bind_param($stmt, "sssssssssssss",
        $data['puskod'],
        $data['periode_awal'],
        $data['periode_akhir'],
        $data['pejabat_nama'],
        $data['pejabat_nip'],
        $data['pejabat_pangkat'],
        $data['pejabat_jabatan'],
        $data['pejabat_unit'],
        $data['atasan_nama'],
        $data['atasan_nip'],
        $data['atasan_pangkat'],
        $data['atasan_jabatan'],
        $data['atasan_unit']
    );
}

if(mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => mysqli_error($kon)]);
}

mysqli_stmt_close($stmt);
mysqli_close($kon);
?>
