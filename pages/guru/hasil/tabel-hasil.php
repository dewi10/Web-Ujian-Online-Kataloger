<?php
    include '../../../config/database.php';
    $id_ujian = $_POST["id_ujian"];
    $query ="select * from ujian u
    inner join kelas k on u.id_kelas=k.id_kelas
    inner join mapel m on m.id_mapel=u.id_mapel
    inner join guru g on g.id_guru=u.id_guru
    where u.id_ujian='$id_ujian' limit 1"; 
    $hasil=mysqli_query($kon,$query);
    $row = mysqli_fetch_array($hasil);

    $cek = mysqli_num_rows($hasil);

    if ($cek<=0){
        echo"<div class='alert alert-warning'>Belum ada data!</div>";
        exit;
    }
?>

<script> $('title').text('HASIL UJIAN <?php echo $row['kode_ujian']; ?> KELAS <?php echo strtoupper($row['nama_kelas'])?> MAPEL <?php echo strtoupper($row['nama_mapel'])?> - <?php echo strtoupper($row['nama_guru']); ?>'); </script> 
<div class="table-responsive">
    <table class="table table-border" id="tabel_ujian">
        <tbody>
        <tr>
            <td width="20%">Kategori</td>  <td>: <?php echo $row['nama_kelas']; ?> </td>
        </tr>
        <tr>
            <td>Judul</td>  <td>: <?php echo $row['judul']; ?> </td>
        </tr>
        <tr>
            <td>Tipe Soal</td>  <td>: <?php echo $row['tipe_soal'] == 1 ? 'Pilihan Ganda' : 'Essay';?> </td>
        </tr>
        <tr>
            <td>Mata Ujian</td>  <td>: <?php echo $row['nama_mapel']; ?> </td>
        </tr>
        <tr>
            <td>Nilai Minimum</td>  <td>: <?php echo $row['nilai_kelulusan']; ?> </td>
        </tr>
        <tr>
            <td>Pengawas</td>  <td>: <?php echo $row['nama_guru']; ?> </td>
        </tr>
        <tr>
            <td>Tanggal Ujian</td><td>: <?php echo tanggal(date('Y-m-d', strtotime($row["tanggal"]))); ?> </td>
        </tr>
        <tr>
            <td>Jam</td>  <td>: <?php echo date('H:i', strtotime($row["jam"])); ?> - <?php echo date("H:i", strtotime("+".$row['waktu']." minutes", strtotime($row["jam"]))); ?> WIB</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="thead-light">
        <tr>
            <th>No</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>Nilai</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php
            session_start();
            include '../../../config/database.php';
            $id_guru = $_SESSION["id_guru"];
            $id_ujian = $_POST["id_ujian"];
            $sql="select * from siswa s
            inner join nilai n on  n.id_siswa=s.id_siswa 
            where n.id_ujian='$id_ujian'";
            $hasil=mysqli_query($kon,$sql);
            $jum=mysqli_num_rows($hasil);
            $no=0;
            $rata=0;
            $total_rata=0;
            //Menampilkan data dengan perulangan while
            while ($data = mysqli_fetch_array($hasil)):
            $no++;

            $nilai = $data['nilai'];
                
            $hasil2=mysqli_query($kon,"select nilai_kelulusan from ujian where id_ujian='$id_ujian'");
            $data2 = mysqli_fetch_array($hasil2);
            $nilai_kelulusan=$data2['nilai_kelulusan'];

            $rata+=$nilai;

            $total_rata=$rata/$jum;
            $arr_nilai[] = $data['nilai'];
            
            $status="";
            if ($nilai>= $nilai_kelulusan){
                $status="<span class='text-success'>Kompeten</span>";
            }else {
                $status="<span class='text-warning'>Belum Kompeten</span>";
            }


            
        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $data['nis']; ?></td>
            <td><?php echo $data['nama_siswa'];?></td>
            <td>
                <?php 
                if ($row['tipe_soal'] == 2){
                    if ($nilai!=0){
                        echo number_format($data['nilai'],2); 
                    }
                }else {
                    echo number_format($data['nilai'],2); 
                }
                ?>
            </td>
            <td>
                <?php
                    if ($row['tipe_soal'] == 2){
                        if ($nilai!=0){
                            echo $status;
                        }
                    }else {
                        echo $status;
                    }
                ?>
            </td>
            <td>
                <?php if( $row['tipe_soal']=='2'):?>
                <button type="button" class="lihat_hasil btn btn-primary btn-circle" id_ujian="<?php echo $id_ujian; ?>" id_siswa="<?php echo $data['id_siswa']; ?>"  ><i class="fas fa-book"></i></button>
                <?php  endif; ?>
                <button type="button" class="btn_edit btn btn-dark btn-circle" id_nilai="<?php echo $data['id_nilai']; ?>"  ><i class="fa fa-edit"></i></button>
                <a href="pages/guru/hasil/cetak-siswa.php?id_ujian=<?php echo $id_ujian; ?>&id_siswa=<?php echo $data['id_siswa']; ?>" target="_blank" class="btn btn-info btn-circle" title="Print Hasil Siswa"><i class="fas fa-print"></i></a>
                <a href="pages/guru/hasil/cetak-soal-jawaban.php?id_ujian=<?php echo $id_ujian; ?>&id_siswa=<?php echo $data['id_siswa']; ?>" target="_blank" class="btn btn-success btn-circle" title="Print Soal &amp; Jawaban Siswa"><i class="fas fa-file-alt"></i></a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<div class="col-sm-4">
    <div class="table-responsive">
        <table class="table table-border">
            <tbody>
            <tr>
                <td>Nilai Tertinggi</td>  <td>: <?php if (isset($arr_nilai)) echo max($arr_nilai);  ?> </td>
            </tr>
            <tr>
                <td>Nilai Terendah</td>  <td>: <?php if (isset($arr_nilai)) echo min($arr_nilai); ?></td>
            </tr>
            <tr>
                <td>Nilai Rata-rata</td>  <td>: <?php echo $total_rata; ?> </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<br>

<div class="modal fade" id="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        <div class="modal-header" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); color: white;">
            <h4 class="modal-title" id="judul"></h4>
            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">&times;</button>
        </div>

        <div class="modal-body">
            <div id="tampil_data_modal">
                 <!-- Data akan di load menggunakan AJAX -->                   
            </div>  
        </div>
  
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

        </div>
    </div>
</div>


<a href="pages/guru/hasil/export-pdf.php?id=<?php echo $id_ujian; ?>"  target="_blank" class="btn_hapus btn btn-danger btn-circle" ><i class="fas fa-file-pdf"></i> Export PDF</a>
<a href="pages/guru/hasil/export-excel.php?id=<?php echo $id_ujian; ?>"  target="_blank" class="btn_hapus btn btn-success btn-circle" ><i class="fas fa-file-excel"></i> Export Excel</a>

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
    //Edit Nilai
    $('.btn_edit').on('click',function(){

        var id_nilai = $(this).attr("id_nilai");

        $.ajax({
            url: 'pages/guru/hasil/edit-nilai.php',
            method: 'post',
            data: {id_nilai:id_nilai},
            success:function(data){
                $('#tampil_data_modal').html(data);  
                document.getElementById("judul").innerHTML='Edit Nilai #'+id_nilai;
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });
</script>

<script>
    //Lihat hasil
    $('.lihat_hasil').on('click',function(){
        
        var id_siswa = $(this).attr("id_siswa");
        var id_ujian = $(this).attr("id_ujian");
        $.ajax({
            url: 'pages/guru/hasil/lihat-hasil.php',
            method: 'post',
            data: {id_siswa:id_siswa,id_ujian:id_ujian},
            success:function(data){
                $('#tampil_data_modal').html(data);  
                document.getElementById("judul").innerHTML='Lihat Hasil #'+id_siswa;
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });
</script>