<?php
    session_start();
    //Koneksi database
    include '../../../config/database.php';

    $query = mysqli_query($kon, "select * from aplikasi limit 1");    
    $row = mysqli_fetch_array($query);

    //Membuat file format excel
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=DAFTAR HASIL UJIAN ".strtoupper($row['nama_aplikasi']).".xls");
?>  
<h2><center>LAPORAN HASIL UJIAN <?php echo strtoupper($row['nama_aplikasi']);?></center></h2>
<?php

    $id_ujian=addslashes(trim($_GET['id']));

    $query ="select * from ujian u
    inner join kelas k on u.id_kelas=k.id_kelas
    inner join siswa s on s.id_kelas=k.id_kelas
    inner join mapel m on m.id_mapel=u.id_mapel
    inner join guru g on g.id_guru=u.id_guru
    where u.id_ujian='$id_ujian' limit 1"; 
    $hasil=mysqli_query($kon,$query);
    $row = mysqli_fetch_array($hasil);
?>
<table class="table table-border" id="tabel_ujian">
    <tbody>
        <tr>
            <td width="20%">Kategori</td>  <td>: <?php echo $row['nama_kelas']; ?> </td>
        </tr>
        <tr>
            <td>Mata Ujian</td>  <td>: <?php echo $row['nama_mapel']; ?> </td>
        </tr>
        <tr>
            <td>Pengawas</td>  <td>: <?php echo $row['nama_guru']; ?> </td>
        </tr>
        <tr>
            <td>Tanggal</td><td>: <?php echo date('d-m-Y', strtotime($row["tanggal"])); ?> </td>
        </tr>
        <tr>
            <td>Jam</td>  <td>: <?php echo date('H:i', strtotime($row["jam"])); ?> - <?php echo date("H:i", strtotime("+".$row['waktu']." minutes", strtotime($row["jam"]))); ?> WIB</td>
        </tr>
        <tr>
            <td>Waktu</td>  <td>: <?php echo $row['waktu']; ?> Menit</td>
        </tr>
    </tbody>
</table>

<table border="1">
    <thead class="thead-light">
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>Nilai</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <?php
            include '../../../config/database.php';
            $id_guru = $_SESSION["id_guru"];
            $id_ujian=addslashes(trim($_GET['id']));
            $sql="select * from siswa s
            inner join nilai n on  n.id_siswa=s.id_siswa 
            where n.id_ujian='$id_ujian'";
            $hasil=mysqli_query($kon,$sql);
            $no=0;
            //Menampilkan data dengan perulangan while
            while ($data = mysqli_fetch_array($hasil)):
            $no++;

            $nilai = $data['nilai'];
                
            $hasil2=mysqli_query($kon,"select nilai_kelulusan from ujian where id_ujian='$id_ujian'");
            $data2 = mysqli_fetch_array($hasil2);
            $nilai_kelulusan=$data2['nilai_kelulusan'];
            
            $status="";
            if ($nilai>= $nilai_kelulusan){
                $status="<span class='text-success'>Kompeten</span>";
            }else {
                $status="<span class='text-danger'>Belum Kompeten</span>";
            }
        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $data['nis']; ?></td>
            <td><?php echo $data['nama_siswa'];?></td>
            <td><?php echo number_format($data['nilai'],2); ?></td>
            <td><?php echo $status; ?></td>

        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
