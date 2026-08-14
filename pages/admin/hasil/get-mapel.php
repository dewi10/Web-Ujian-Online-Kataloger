<?php
    include '../../../config/database.php';
    $kelas=$_POST['kelas'];
    $guru=$_POST['guru'];
    $results = array();
    $query = mysqli_query($kon, "select u.id_ujian, u.kode_ujian, u.judul, m.id_mapel, m.kode_mapel, m.nama_mapel from ujian u inner join mapel m on m.id_mapel=u.id_mapel where u.id_kelas='".$kelas."' and u.id_guru='".$guru."' and u.status_aktif='1' order by u.kode_ujian");
    while ($data = mysqli_fetch_array($query)):
        $results[] = $data;
    endwhile;
    echo json_encode($results);
?>

