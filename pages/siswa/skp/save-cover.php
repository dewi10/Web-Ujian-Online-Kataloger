<?php
session_start();
include "../../../config/database.php";

// Validasi login
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$username = $_SESSION['username'];

// Cek apakah tabel sudah ada, jika belum buat tabel
$check_table = mysqli_query($kon, "SHOW TABLES LIKE 'skp_cover'");
if (mysqli_num_rows($check_table) == 0) {
    $create_table = "CREATE TABLE `skp_cover` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(100) NOT NULL,
        `nama` varchar(150) NOT NULL,
        `nip` varchar(18) NOT NULL,
        `pangkat_gol` varchar(100) DEFAULT NULL,
        `jabatan` varchar(150) DEFAULT NULL,
        `unit_kerja` varchar(200) DEFAULT NULL,
        `periode_mulai` varchar(50) DEFAULT NULL,
        `periode_selesai` varchar(50) DEFAULT NULL,
        `tahun` varchar(4) DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    
    mysqli_query($kon, $create_table);
}

// Ambil data dari POST
$nama = mysqli_real_escape_string($kon, $_POST['nama']);
$nip = mysqli_real_escape_string($kon, $_POST['nip']);
$pangkat_gol = mysqli_real_escape_string($kon, $_POST['pangkat_gol']);
$jabatan = mysqli_real_escape_string($kon, $_POST['jabatan']);
$unit_kerja = mysqli_real_escape_string($kon, $_POST['unit_kerja']);
$periode_mulai = mysqli_real_escape_string($kon, $_POST['periode_mulai']);
$periode_selesai = mysqli_real_escape_string($kon, $_POST['periode_selesai']);
$tahun = mysqli_real_escape_string($kon, $_POST['tahun']);

// Cek apakah data sudah ada
$check = mysqli_query($kon, "SELECT * FROM skp_cover WHERE username='$username'");

if (mysqli_num_rows($check) > 0) {
    // Update data
    $query = "UPDATE skp_cover SET 
                nama='$nama',
                nip='$nip',
                pangkat_gol='$pangkat_gol',
                jabatan='$jabatan',
                unit_kerja='$unit_kerja',
                periode_mulai='$periode_mulai',
                periode_selesai='$periode_selesai',
                tahun='$tahun'
              WHERE username='$username'";
} else {
    // Insert data baru
    $query = "INSERT INTO skp_cover 
                (username, nama, nip, pangkat_gol, jabatan, unit_kerja, periode_mulai, periode_selesai, tahun) 
              VALUES 
                ('$username', '$nama', '$nip', '$pangkat_gol', '$jabatan', '$unit_kerja', '$periode_mulai', '$periode_selesai', '$tahun')";
}

if (mysqli_query($kon, $query)) {
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data']);
}
?>
