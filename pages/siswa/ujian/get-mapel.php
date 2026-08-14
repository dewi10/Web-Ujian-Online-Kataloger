<?php
    session_start();
    require_once __DIR__ . '/../../../config/database.php';
    $guru = ""; 
    if (isset($_POST['guru'])) {
        foreach ($_POST['guru'] as $value)
        {
            $guru .= "'$value'". ",";
        }
        $guru = substr($guru,0,-1);
    }else {
        $guru = "0"; 
    }

    $results = array();
    $id_siswa=$_SESSION["id_siswa"];
    $hasil=mysqli_query($kon,"select id_kelas from siswa where id_siswa='$id_siswa' ");
    $row=mysqli_fetch_array($hasil);
    $id_kelas=$row['id_kelas'];

    $cek_col_ujian = mysqli_query($kon, "SHOW COLUMNS FROM ujian LIKE 'status_aktif'");
    $aktif_ujian_sql = ($cek_col_ujian && mysqli_num_rows($cek_col_ujian) > 0) ? " and u.status_aktif='1'" : '';

    $sql="select * from ujian u
    inner join kelas k on u.id_kelas=k.id_kelas
    inner join mapel m on m.id_mapel=u.id_mapel
    inner join guru g on g.id_guru=u.id_guru
    inner join siswa s on s.id_kelas=k.id_kelas
    inner join peserta p on p.id_ujian=u.id_ujian and p.id_siswa=s.id_siswa
    where k.id_kelas='$id_kelas' and s.id_siswa='$id_siswa' and u.id_guru in (".$guru.")$aktif_ujian_sql
    group by m.id_mapel";


    $query = mysqli_query($kon,$sql);
    while ($data = mysqli_fetch_array($query)):
        $results[] = $data;
    endwhile;
    echo json_encode($results);
?>

