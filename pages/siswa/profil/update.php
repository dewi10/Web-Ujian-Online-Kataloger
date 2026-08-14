<?php
session_start();
include '../../../config/database.php';

if (!isset($_SESSION["id_siswa"])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$id_siswa = $_SESSION['id_siswa'];

// Get POST data
$nama_siswa = mysqli_real_escape_string($kon, $_POST['nama_siswa']);
$nis = mysqli_real_escape_string($kon, $_POST['nis']);
$nomor_seri_karpeg = mysqli_real_escape_string($kon, $_POST['nomor_seri_karpeg']);
$tempat_lahir = mysqli_real_escape_string($kon, $_POST['tempat_lahir']);
$tanggal_lahir = mysqli_real_escape_string($kon, $_POST['tanggal_lahir']);
$pangkat_gol = mysqli_real_escape_string($kon, $_POST['pangkat_gol']);
$jabatan = mysqli_real_escape_string($kon, $_POST['jabatan']);
$unit_kerja = mysqli_real_escape_string($kon, $_POST['unit_kerja']);
$instansi = mysqli_real_escape_string($kon, $_POST['instansi']);
$jk = mysqli_real_escape_string($kon, $_POST['jk']);
$alamat = mysqli_real_escape_string($kon, $_POST['alamat']);
$no_telp = mysqli_real_escape_string($kon, $_POST['no_telp']);
$email = mysqli_real_escape_string($kon, $_POST['email']);

// Update
$query = "UPDATE siswa SET 
    nama_siswa='$nama_siswa',
    nis='$nis',
    nomor_seri_karpeg='$nomor_seri_karpeg',
    tempat_lahir='$tempat_lahir',
    tanggal_lahir='$tanggal_lahir',
    pangkat_gol='$pangkat_gol',
    jabatan='$jabatan',
    unit_kerja='$unit_kerja',
    instansi='$instansi',
    jk='$jk',
    alamat='$alamat',
    no_telp='$no_telp',
    email='$email'
    WHERE id_siswa='$id_siswa'";

if (mysqli_query($kon, $query)) {
    echo json_encode(['success' => true, 'message' => 'Profil berhasil diupdate']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($kon)]);
}
?>
