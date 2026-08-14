<?php
session_start();
include "../../../config/database.php";

// Validasi login
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$username = $_SESSION['username'];

// Cek apakah tabel sudah ada
$check_table = mysqli_query($kon, "SHOW TABLES LIKE 'dok_evaluasi_kinerja'");
if (mysqli_num_rows($check_table) == 0) {
    $create_table = "CREATE TABLE `dok_evaluasi_kinerja` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(100) NOT NULL,
        `periode_triwulan` varchar(50) DEFAULT 'I-II / III / IV',
        `periode_akhir` varchar(100) DEFAULT 'AKHIR',
        `periode_penilaian` varchar(100) DEFAULT '02 JANUARI SD 31 DESEMBER TAHUN 2025',
        `pejabat_nama` varchar(150) DEFAULT NULL,
        `pejabat_nip` varchar(50) DEFAULT NULL,
        `pejabat_pangkat` varchar(100) DEFAULT NULL,
        `pejabat_jabatan` varchar(150) DEFAULT NULL,
        `pejabat_unit` varchar(200) DEFAULT NULL,
        `atasan_nama` varchar(150) DEFAULT NULL,
        `atasan_nip` varchar(50) DEFAULT NULL,
        `atasan_pangkat` varchar(100) DEFAULT NULL,
        `atasan_jabatan` varchar(150) DEFAULT NULL,
        `atasan_unit` varchar(200) DEFAULT NULL,
        `capaian_kinerja` varchar(20) DEFAULT 'BAIK',
        `predikat_kinerja` varchar(20) DEFAULT 'BAIK',
        `keberatan` text,
        `penjelasan_pejabat` text,
        `keputusan_atasan` text,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    
    mysqli_query($kon, $create_table);
}

// Ambil data dari POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
    exit;
}

// Escape data untuk keamanan
$periode_triwulan = mysqli_real_escape_string($kon, $data['periode_triwulan']);
$periode_akhir = mysqli_real_escape_string($kon, $data['periode_akhir']);
$periode_penilaian = mysqli_real_escape_string($kon, $data['periode_penilaian']);
$pejabat_nama = mysqli_real_escape_string($kon, $data['pejabat_nama']);
// Support both pejabat_nip (for guru/siswa) and pejabat_username (for admin)
$pejabat_nip = mysqli_real_escape_string($kon, isset($data['pejabat_username']) ? $data['pejabat_username'] : $data['pejabat_nip']);
$pejabat_pangkat = mysqli_real_escape_string($kon, $data['pejabat_pangkat']);
$pejabat_jabatan = mysqli_real_escape_string($kon, $data['pejabat_jabatan']);
$pejabat_unit = mysqli_real_escape_string($kon, $data['pejabat_unit']);
$atasan_nama = mysqli_real_escape_string($kon, $data['atasan_nama']);
// Support both atasan_nip (for guru/siswa) and atasan_username (for admin)
$atasan_nip = mysqli_real_escape_string($kon, isset($data['atasan_username']) ? $data['atasan_username'] : $data['atasan_nip']);
$atasan_pangkat = mysqli_real_escape_string($kon, $data['atasan_pangkat']);
$atasan_jabatan = mysqli_real_escape_string($kon, $data['atasan_jabatan']);
$atasan_unit = mysqli_real_escape_string($kon, $data['atasan_unit']);
$capaian_kinerja = mysqli_real_escape_string($kon, $data['capaian_kinerja']);
$predikat_kinerja = mysqli_real_escape_string($kon, $data['predikat_kinerja']);
$keberatan = mysqli_real_escape_string($kon, $data['keberatan']);
$penjelasan_pejabat = mysqli_real_escape_string($kon, $data['penjelasan_pejabat']);
$keputusan_atasan = mysqli_real_escape_string($kon, $data['keputusan_atasan']);

// Cek apakah data sudah ada
$check_existing = mysqli_query($kon, "SELECT id FROM dok_evaluasi_kinerja WHERE username='".$username."'");

if (mysqli_num_rows($check_existing) > 0) {
    // Update data yang sudah ada
    $query = "UPDATE dok_evaluasi_kinerja SET
        periode_triwulan = '".$periode_triwulan."',
        periode_akhir = '".$periode_akhir."',
        periode_penilaian = '".$periode_penilaian."',
        pejabat_nama = '".$pejabat_nama."',
        pejabat_nip = '".$pejabat_nip."',
        pejabat_pangkat = '".$pejabat_pangkat."',
        pejabat_jabatan = '".$pejabat_jabatan."',
        pejabat_unit = '".$pejabat_unit."',
        atasan_nama = '".$atasan_nama."',
        atasan_nip = '".$atasan_nip."',
        atasan_pangkat = '".$atasan_pangkat."',
        atasan_jabatan = '".$atasan_jabatan."',
        atasan_unit = '".$atasan_unit."',
        capaian_kinerja = '".$capaian_kinerja."',
        predikat_kinerja = '".$predikat_kinerja."',
        keberatan = '".$keberatan."',
        penjelasan_pejabat = '".$penjelasan_pejabat."',
        keputusan_atasan = '".$keputusan_atasan."'
        WHERE username = '".$username."'";
} else {
    // Insert data baru
    $query = "INSERT INTO dok_evaluasi_kinerja (
        username, periode_triwulan, periode_akhir, periode_penilaian,
        pejabat_nama, pejabat_nip, pejabat_pangkat, pejabat_jabatan, pejabat_unit,
        atasan_nama, atasan_nip, atasan_pangkat, atasan_jabatan, atasan_unit,
        capaian_kinerja, predikat_kinerja, keberatan, penjelasan_pejabat, keputusan_atasan
    ) VALUES (
        '".$username."', '".$periode_triwulan."', '".$periode_akhir."', '".$periode_penilaian."',
        '".$pejabat_nama."', '".$pejabat_nip."', '".$pejabat_pangkat."', '".$pejabat_jabatan."', '".$pejabat_unit."',
        '".$atasan_nama."', '".$atasan_nip."', '".$atasan_pangkat."', '".$atasan_jabatan."', '".$atasan_unit."',
        '".$capaian_kinerja."', '".$predikat_kinerja."', '".$keberatan."', '".$penjelasan_pejabat."', '".$keputusan_atasan."'
    )";
}

if (mysqli_query($kon, $query)) {
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . mysqli_error($kon)]);
}
?>
