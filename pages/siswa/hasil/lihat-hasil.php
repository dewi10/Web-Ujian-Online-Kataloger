<?php
    //validasi hanya siswa yang boleh mengakses halaman ini
    $username = $_SESSION['username'];
    $cek = mysqli_query ($kon,"select * from siswa where username='".$username."' limit 1");
    $jum = mysqli_num_rows($cek);

    if ($jum<1){
        echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
        exit;
    }
?>
<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-clipboard-check"></i> Hasil Ujian</h3>
    </div>
    <div class="card-body">
    <?php
        include 'config/database.php';
        $id_ujian=addslashes(trim($_GET['id']));
        $id_siswa=$_SESSION["id_siswa"];
        $sql="select * from siswa s
        inner join kelas k on k.id_kelas=s.id_kelas
        inner join ujian u on u.id_kelas=k.id_kelas
        inner join mapel m on m.id_mapel=u.id_mapel
        inner join guru g on g.id_guru=u.id_guru
        where u.id_ujian='$id_ujian' and s.id_siswa='$id_siswa' limit 1";

        $hasil=mysqli_query($kon,$sql);

        $cek=mysqli_num_rows($hasil);

        if ($cek<=0){
            echo "<center><h5>Data tidak ditemukan</h5></center>";
            exit;
        }


        $row = mysqli_fetch_array($hasil);
    ?>
        <div class="table-responsive">
            <table class="table table-border">
                <tbody>
                <tr>
                    <td width="15%">NIP</td>  <td>: <?php echo $row['nis']; ?> </td>
                </tr>
                <tr>
                    <td>Nama</td>  <td>: <?php echo $row['nama_siswa']; ?> </td>
                </tr>
                <tr>
                    <td>Kategori</td>  <td>: <?php echo $row['nama_kelas']; ?> </td>
                </tr>
                <tr>
                    <td>Judul</td>  <td>: <?php echo $row['judul']; ?> </td>
                </tr>
                <!-- <tr>
                    <td>Mata Ujian</td>  <td>: <?php echo $row['nama_mapel']; ?> </td>
                </tr> -->
                <!-- <tr>
                    <td>Pengawas Pengampuh</td>  <td>: <?php echo $row['nama_guru']; ?></td>
                </tr> -->
                <tr>
                    <td>Tanggal</td>   <td>: <?php echo tanggal(date('Y-m-d', strtotime($row["tanggal"]))); ?> </td>
                </tr>
                </tbody>
            </table>
        </div>
<?php 
    include 'config/database.php';

    $id_siswa = $_SESSION["id_siswa"];

    $hasil=mysqli_query($kon,"select * from hasil where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
    $jumlah_soal=mysqli_num_rows($hasil);

    $hasil2=mysqli_query($kon,"select * from hasil h inner join jawaban j on j.id_jawaban=h.id_jawaban  where h.id_ujian='$id_ujian' and j.jawaban=1 and h.id_siswa='$id_siswa'");
    $jumlah_benar=mysqli_num_rows($hasil2);

    $hasil1=mysqli_query($kon,"select * from nilai where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
    $data1 = mysqli_fetch_array($hasil1);
    $nilai=$data1['nilai'];

    $hasil2=mysqli_query($kon,"select nilai_kelulusan from ujian where id_ujian='$id_ujian'");
    $data2 = mysqli_fetch_array($hasil2);

    $nilai_kelulusan=$data2['nilai_kelulusan'];
    $status="";
    if ($nilai>= $nilai_kelulusan){
        $status="<span class='text-success'>Kompeten</span>";
    }else {
        $status="<span class='text-warning'>Belum Kompeten</span>";
    }


?>

        <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Jumlah Soal</th>
                        <th>Benar</th>
                        <th>Nilai</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?php echo $jumlah_soal; ?></td>  
                        <td><?php echo $jumlah_benar; ?></td>  
                        <td><?php echo $nilai; ?></td>
                        <td><?php echo $status; ?></td> 
                    </tr>
    
                    </tbody>
                </table>
            </div>
        </div>
        </div>
        <br>
        <a href="pages/siswa/hasil/cetak-hasil.php?id=<?php echo $id_ujian; ?>" target='_blank' class="btn_hapus btn btn-danger btn-circle" ><i class="fas fa-file-pdf"></i> Cetak</a>
    </div> 
</div>


<?php 
    //Membuat format tanggal
    function tanggal($tanggal)
    {
        $bulan = array (1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
    }

?>
