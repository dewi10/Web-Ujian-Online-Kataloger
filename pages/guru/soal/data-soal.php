<?php if ($_POST['tipe_soal']=='pg'):?>
<div class="table-responsive">
    <table class="table table-bordered" id="tabel_data_soal">
        <thead class="thead-light">
            <tr>
                <th width="6%">Pilih</th>
                <th width="6%">No</th>
                <th width="55%">Soal</th>
                <th>Jawaban</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // include database
            include '../../../config/database.php';
            $id_ujian=$_POST['ujian'];
            $sql="select * from soal where id_ujian='$id_ujian'";
            $hasil=mysqli_query($kon,$sql);
            $no=0;
            //Menampilkan data dengan perulangan while
            while ($data = mysqli_fetch_array($hasil)):
            $no++;
            ?>
            <tr>
                <td><input type="checkbox" name="soal[]" value="<?php echo $data['id_soal']; ?>"></td>
                <td><?php echo $no; ?></td>
                <td><?php echo $data['soal'];?>
                <?php if ($data['gambar']!='') echo "<p> <img src='pages/guru/soal/gambar/".$data['gambar']."' width='50%' class='img-thumbnail'></p>"; ?>
                </td>
                <td>
                    <ol type="A">
                        <?php
                        $id_soal=$data['id_soal'];
                        $result=mysqli_query($kon,"select * from jawaban where id_soal='$id_soal'");
                        while ($get = mysqli_fetch_array($result)):
                        ?>
                        <li>
                        <?php 
                            if ($get['jawaban']==1){
                                echo "<span class='text-success'>".$get['pilihan']."</span";
                            }else {
                                echo $get['pilihan'];
                            }
                        ?>
                        </li>
                        <?php endwhile; ?>
                    </ol> 
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if ($_POST['tipe_soal']=='essay'):?>
<div class="table-responsive">
    <table class="table table-bordered" id="tabel_data_soal">
        <thead class="thead-light">
            <tr>
                <th width="6%">Pilih</th>
                <th width="6%">No</th>
                <th width="55%">Soal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // include database
            include '../../../config/database.php';
            $id_ujian=$_POST['ujian'];
            $sql="select * from soal where id_ujian='$id_ujian'";
            $hasil=mysqli_query($kon,$sql);
            $no=0;
            //Menampilkan data dengan perulangan while
            while ($data = mysqli_fetch_array($hasil)):
            $no++;
            ?>
            <tr>
                <td><input type="checkbox" name="soal[]" value="<?php echo $data['id_soal']; ?>"></td>
                <td><?php echo $no; ?></td>
                <td><?php echo $data['soal'];?>
                <?php if ($data['gambar']!='') echo "<p> <img src='pages/guru/soal/gambar/".$data['gambar']."' width='50%' class='img-thumbnail'></p>"; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>


<script>
    $(document).ready(function() {
        $('#tabel_data_soal').DataTable( {
            "searching": false,
            "paging":   true,
            "ordering": false,
            "info":     true,
        });
    });
</script>

<?php if($no==0): ?>
<script> document.getElementById("salin_soal").disabled = true; </script>
<?php endif; ?>

<?php if($no>0): ?>
    <script> document.getElementById("salin_soal").disabled = false; </script>
<?php endif; ?>
