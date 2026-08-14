<?php
    include '../../../config/database.php';
    $kelas=$_POST['kelas'];
    $results = array();
    $query = mysqli_query($kon, "select DISTINCT g.id_guru, g.nip, g.nama_guru, g.username from guru g inner join ujian u on g.id_guru=u.id_guru where u.id_kelas='".$kelas."' and u.status_aktif='1' order by g.nama_guru");
    while ($data = mysqli_fetch_array($query)):
        $results[] = $data;
    endwhile;
    echo json_encode($results);
?>

