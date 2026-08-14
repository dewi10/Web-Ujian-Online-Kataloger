
<div class="mb-3">
    <a href="pages/guru/hasil/export-essay-pdf.php?id_ujian=<?php echo $_POST['id_ujian']; ?>&id_siswa=<?php echo $_POST['id_siswa']; ?>" target="_blank" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
</div>

<ul class="list-group">
<?php
    include '../../../config/database.php';
    $id_ujian=$_POST["id_ujian"];
    $id_siswa=$_POST["id_siswa"];
    $sql="select * from soal where id_ujian='$id_ujian' order by id_soal asc";
    $hasil=mysqli_query($kon,$sql);
    $no=0;
    while ($row = mysqli_fetch_array($hasil)):
        $no++;
?>
  <li class="list-group-item"><?php echo $no; ?>. <?php echo $row['soal'];?></li>
<?php 
    $get=mysqli_query($kon,"select * from hasil where id_ujian='".$id_ujian."' and id_soal='".$row['id_soal']."' and id_siswa='".$id_siswa."'");
    $data = mysqli_fetch_array($get);
    echo "<li class='list-group-item'>Jawaban : ".($data['essay'] ? $data['essay'] : '<em>(Tidak dijawab)</em>')."</li>";
?>
<?php endwhile; ?>
</ul>