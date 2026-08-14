<?php
    require_once __DIR__ . '/../../../config/database.php';
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
        <h3><i class="fas fa-clipboard-check"></i> Hasil Ujian Saya</h3>
    </div>
    <div class="card-body">
        <?php 
        if (isset($_SESSION["mulai_ujian"])):
            $nama_mapel=$_SESSION["nama_mapel"];
            $id_ujian=$_SESSION["id_ujian"];
            echo "<div class='alert alert-warning'>Ujian <strong>".$nama_mapel."</strong> sedang berlangsung <a href='index.php?page=review&id=".$id_ujian."' class='btn btn-warning btn-sm' role='button'>lanjut & selesaikan</a> </div>";
        endif;

        //Menampilkan detail data siswa
        $id_siswa = $_SESSION["id_siswa"];
        $sql="select * from siswa s
        inner join kelas k on k.id_kelas=s.id_kelas
        where s.id_siswa='$id_siswa' limit 1";

        $hasil=mysqli_query($kon,$sql);
        $data = mysqli_fetch_array($hasil); 
        ?>

        <div class="table-responsive">
            <table class="table table-border" id="tabel_ujian">
                <tbody>
                <tr>
                    <td width="20%">Nama</td>  <td>: <?php echo $data['nama_siswa']; ?> </td>
                </tr>
                <tr>
                    <td>NIP</td>  <td>: <?php echo $data['nis']; ?> </td>
                </tr>
                <tr>
                    <td>KLASIFIKASI</td>  <td>: <?php echo $data['nama_kelas']; ?> </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="tabel_hasil">
                <thead class="thead-light">
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Tanggal</th>
                    <th>Klasifikasi</th>
                                     <th>Status Ujian</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    // include database
                    include 'config/database.php';
                    $id_siswa = $_SESSION["id_siswa"];
                    $cek_mapel = mysqli_query($kon, "SHOW COLUMNS FROM mapel LIKE 'status_aktif'");
                    $aktif_mapel_sql = ($cek_mapel && mysqli_num_rows($cek_mapel) > 0) ? " and m.status_aktif='1'" : '';
                    
                    $sql="select * from ujian u
                    inner join guru g on g.id_guru=u.id_guru
                    inner join mapel m on m.id_mapel=u.id_mapel
                    inner join nilai n on n.id_ujian=u.id_ujian
                    where n.id_siswa='$id_siswa'$aktif_mapel_sql
                    order by tanggal desc";
                    $hasil=mysqli_query($kon,$sql);
                    $no=0;
                    //Menampilkan data dengan perulangan while
                    while ($data = mysqli_fetch_array($hasil)):
                    $no++;

                    $id_ujian=$data['id_ujian'];

                    $hasil1=mysqli_query($kon,"select * from nilai where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
                    $data1 = mysqli_fetch_array($hasil1);
                    $nilai=$data1['nilai'];
                
                    $hasil2=mysqli_query($kon,"select nilai_kelulusan from ujian where id_ujian='$id_ujian'");
                    $data2 = mysqli_fetch_array($hasil2);
                
                    $nilai_kelulusan=$data2['nilai_kelulusan'];
                    $status="Ujian Telah Selesai";                
                ?>
                <tr>
                    <td><?php echo $no; ?></td>
                    <td><?php echo $data['kode_ujian'];?></td>
                    <td>
                        <?php echo $data['judul'];?>
                        <p><small class="text-muted">Tipe Soal <?php echo $data['tipe_soal'] == 1 ? 'Pilihan Ganda' : 'Essay';?></small></p>
                    </td>
                    <td><?php echo tanggal(date('Y-m-d', strtotime($data["tanggal"]))); ?></td>
                    <td><?php echo $data['nama_mapel'];?><br> <small>Oleh <?php echo $data['nama_guru']?></small></td>
                                       <td>
                        <?php
                           
                                echo $status;
                            
                        ?>
                    </td>

                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <br>
        <a href="pages/siswa/hasil/cetak-semua.php"  target="_blank" class="btn_hapus btn btn-danger btn-circle" ><i class="fas fa-file-pdf"></i> Cetak</a>
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

<script>
    $(document).ready( function () {
        $('#tabel_hasil').DataTable();
    } );
</script>
