<?php
include '../../../config/database.php';

$id_ujian = $_POST['id_ujian'];

$sql = "SELECT id_kelas FROM ujian WHERE id_ujian = '$id_ujian' LIMIT 1";
$hasil = mysqli_query($kon, $sql);

$response = array();
if($data = mysqli_fetch_array($hasil)) {
    $response['id_kelas'] = $data['id_kelas'];
}

echo json_encode($response);
?>
