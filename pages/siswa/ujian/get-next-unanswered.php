<?php
session_start();
require_once __DIR__ . '/../../../config/database.php';

if (!isset($kon) || !$kon) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => 'Koneksi database tidak tersedia'));
    exit;
}

$id_ujian = mysqli_real_escape_string($kon, (string) $_POST["id_ujian"]);
$id_siswa = mysqli_real_escape_string($kon, (string) $_SESSION["id_siswa"]);

// Ambil urutan soal dari session
$urutan_soal = array();
if (isset($_SESSION['urutan_soal'][$id_ujian]) && is_array($_SESSION['urutan_soal'][$id_ujian])) {
    $urutan_soal = $_SESSION['urutan_soal'][$id_ujian];
}

$response = array('success' => false);

// Cari soal pertama yang belum dijawab
foreach ($urutan_soal as $index => $id_soal) {
    // Cek apakah soal sudah dijawab
    $id_soal_esc = mysqli_real_escape_string($kon, (string) $id_soal);
    $cek_jawaban = mysqli_query($kon, "SELECT * FROM hasil WHERE id_soal='$id_soal_esc' AND id_siswa='$id_siswa' AND id_ujian='$id_ujian' LIMIT 1");
    $jawaban = ($cek_jawaban && mysqli_num_rows($cek_jawaban) > 0) ? mysqli_fetch_assoc($cek_jawaban) : false;
    
    $belum_dijawab = true;
    if ($jawaban) {
        $ada_pg = (int) $jawaban['id_jawaban'] !== 0;
        $ada_es = trim((string)($jawaban['essay'] ?? '')) !== '' || trim((string)($jawaban['jawaban_essay'] ?? '')) !== '';
        if ($ada_pg || $ada_es) {
            $belum_dijawab = false;
        }
    }
    
    if ($belum_dijawab) {
        $response = array(
            'success' => true,
            'id_soal' => $id_soal,
            'nomor' => $index + 1
        );
        break;
    }
}

echo json_encode($response);
?>