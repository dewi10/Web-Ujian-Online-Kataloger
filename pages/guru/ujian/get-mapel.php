<?php
session_start();
include '../../../config/database.php';

$id_guru = $_SESSION["id_guru"];
$kelas_ids = isset($_POST['kelas_ids']) ? $_POST['kelas_ids'] : array();

$mapel_list = array();

if (!empty($kelas_ids)) {
    $kelas_ids_str = implode(',', array_map('intval', $kelas_ids));
    
    $sql = "SELECT DISTINCT m.id_mapel, m.nama_mapel, m.kode_mapel 
            FROM mapel m 
            INNER JOIN ujian u ON m.id_mapel = u.id_mapel 
            WHERE u.id_kelas IN ($kelas_ids_str) AND u.id_guru = '$id_guru'
            ORDER BY m.nama_mapel";
    
    $hasil = mysqli_query($kon, $sql);
    
    while ($row = mysqli_fetch_assoc($hasil)) {
        $mapel_list[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($mapel_list);
?>
