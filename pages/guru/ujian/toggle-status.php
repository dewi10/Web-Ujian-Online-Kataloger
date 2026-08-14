<?php
session_start();
include '../../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'error: method tidak valid';
    exit;
}

$id_guru = isset($_SESSION['id_guru']) ? $_SESSION['id_guru'] : '';
$id_ujian = isset($_POST['id_ujian']) ? (int)$_POST['id_ujian'] : 0;
$status_aktif = isset($_POST['status_aktif']) ? (int)$_POST['status_aktif'] : 1;

if ($id_guru === '' || $id_ujian <= 0 || !in_array($status_aktif, [0,1])) {
    echo 'error: parameter tidak valid';
    exit;
}

$cek_kolom = mysqli_query($kon, "SHOW COLUMNS FROM ujian LIKE 'status_aktif'");
if ($cek_kolom && mysqli_num_rows($cek_kolom) == 0) {
    mysqli_query($kon, "ALTER TABLE ujian ADD COLUMN status_aktif TINYINT(1) NOT NULL DEFAULT 1");
}

$sql = "UPDATE ujian SET status_aktif='$status_aktif' WHERE id_ujian='$id_ujian' AND id_guru='$id_guru'";
$ok = mysqli_query($kon, $sql);

if ($ok) {
    echo 'ok';
} else {
    echo 'error: ' . mysqli_error($kon);
}
