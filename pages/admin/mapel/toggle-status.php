<?php
include '../../../config/database.php';

$id_mapel = isset($_GET['id_mapel']) ? (int)$_GET['id_mapel'] : 0;
$status = isset($_GET['status']) ? (int)$_GET['status'] : 1;
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'aktif';
$tab = ($tab === 'nonaktif') ? 'nonaktif' : 'aktif';

if ($id_mapel <= 0 || !in_array($status, [0,1])) {
    header("Location:../../../index.php?page=mapel&tab={$tab}&status=gagal");
    exit;
}

$cek_kolom = mysqli_query($kon, "SHOW COLUMNS FROM mapel LIKE 'status_aktif'");
if ($cek_kolom && mysqli_num_rows($cek_kolom) == 0) {
    mysqli_query($kon, "ALTER TABLE mapel ADD COLUMN status_aktif TINYINT(1) NOT NULL DEFAULT 1");
}

$update = mysqli_query($kon, "UPDATE mapel SET status_aktif='$status' WHERE id_mapel='$id_mapel'");

if ($update) {
    header("Location:../../../index.php?page=mapel&tab={$tab}&status=berhasil");
} else {
    header("Location:../../../index.php?page=mapel&tab={$tab}&status=gagal");
}
exit;
