<?php
session_start();
include '../../../config/database.php';

if (!isset($_SESSION["username"])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$username = $_SESSION['username'];

// Get POST data
$nama_pegawai = mysqli_real_escape_string($kon, $_POST['nama_pegawai']);
$nip_pegawai = mysqli_real_escape_string($kon, $_POST['nip_pegawai']);
$pangkat_gol_pegawai = mysqli_real_escape_string($kon, $_POST['pangkat_gol_pegawai']);
$jabatan_pegawai = mysqli_real_escape_string($kon, $_POST['jabatan_pegawai']);
$unit_kerja_pegawai = mysqli_real_escape_string($kon, $_POST['unit_kerja_pegawai']);

$nama_penilai = mysqli_real_escape_string($kon, $_POST['nama_penilai']);
$nip_penilai = mysqli_real_escape_string($kon, $_POST['nip_penilai']);
$pangkat_gol_penilai = mysqli_real_escape_string($kon, $_POST['pangkat_gol_penilai']);
$jabatan_penilai = mysqli_real_escape_string($kon, $_POST['jabatan_penilai']);
$unit_kerja_penilai = mysqli_real_escape_string($kon, $_POST['unit_kerja_penilai']);

$nama_atasan = mysqli_real_escape_string($kon, $_POST['nama_atasan']);
$nip_atasan = mysqli_real_escape_string($kon, $_POST['nip_atasan']);
$pangkat_gol_atasan = mysqli_real_escape_string($kon, $_POST['pangkat_gol_atasan']);
$jabatan_atasan = mysqli_real_escape_string($kon, $_POST['jabatan_atasan']);
$unit_kerja_atasan = mysqli_real_escape_string($kon, $_POST['unit_kerja_atasan']);

// Check if data exists
$check = mysqli_query($kon, "SELECT id FROM skp_data_pegawai WHERE username='$username'");

if (mysqli_num_rows($check) > 0) {
    // Update
    $query = "UPDATE skp_data_pegawai SET 
        nama_pegawai='$nama_pegawai',
        nip_pegawai='$nip_pegawai',
        pangkat_gol_pegawai='$pangkat_gol_pegawai',
        jabatan_pegawai='$jabatan_pegawai',
        unit_kerja_pegawai='$unit_kerja_pegawai',
        nama_penilai='$nama_penilai',
        nip_penilai='$nip_penilai',
        pangkat_gol_penilai='$pangkat_gol_penilai',
        jabatan_penilai='$jabatan_penilai',
        unit_kerja_penilai='$unit_kerja_penilai',
        nama_atasan='$nama_atasan',
        nip_atasan='$nip_atasan',
        pangkat_gol_atasan='$pangkat_gol_atasan',
        jabatan_atasan='$jabatan_atasan',
        unit_kerja_atasan='$unit_kerja_atasan'
        WHERE username='$username'";
} else {
    // Insert
    $query = "INSERT INTO skp_data_pegawai (
        username, nama_pegawai, nip_pegawai, pangkat_gol_pegawai, jabatan_pegawai, unit_kerja_pegawai,
        nama_penilai, nip_penilai, pangkat_gol_penilai, jabatan_penilai, unit_kerja_penilai,
        nama_atasan, nip_atasan, pangkat_gol_atasan, jabatan_atasan, unit_kerja_atasan
    ) VALUES (
        '$username', '$nama_pegawai', '$nip_pegawai', '$pangkat_gol_pegawai', '$jabatan_pegawai', '$unit_kerja_pegawai',
        '$nama_penilai', '$nip_penilai', '$pangkat_gol_penilai', '$jabatan_penilai', '$unit_kerja_penilai',
        '$nama_atasan', '$nip_atasan', '$pangkat_gol_atasan', '$jabatan_atasan', '$unit_kerja_atasan'
    )";
}

if (mysqli_query($kon, $query)) {
    echo json_encode(['success' => true, 'message' => 'Data berhasil disimpan']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($kon)]);
}
?>
