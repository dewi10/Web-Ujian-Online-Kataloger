<form id="form_peserta">
    <input type="hidden" name="id_ujian" value="<?php echo $_POST['id_ujian'];?>"/>
    <input type="hidden" name="id_kelas" value="<?php echo $_POST['id_kelas'];?>"/>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>NIS</th>
                <th width="70%">Nama</th>
                <th class="text-center">
                <script> function toggle(pilih) { 
                 
                    checkboxes = document.getElementsByName('pilih[]');
                    stat = document.getElementsByName('stat[]');
                    for(var i=0, n=checkboxes.length;i<n;i++)
                        { 
                   
                            checkboxes[i].checked = pilih.checked;

                            if ($('#CheckAll').prop("checked") == false){
                                stat[i].value='0';
                            }else {
                                stat[i].value='1';
                            }
                    
                        }
                    } 
                </script>
                 <div class="custom-control custom-switch">
                    <input type="checkbox" id="CheckAll"  onClick="toggle(this)" class="custom-control-input">
                    <label class="custom-control-label" for="CheckAll"></label>
                </div>

                </th>
            </tr>
            </thead>
            <tbody>
            <?php         
                // include database
                include '../../../config/database.php';
                $id_kelas=$_POST["id_kelas"];
                $sql="select * from siswa where id_kelas='".$id_kelas."' order by nis asc";
                $hasil=mysqli_query($kon,$sql);
                $no=0;
                $jumlah_soal=0;
                $cek="";
                $status=0;
                $jumlah_peserta=0;
                //Menampilkan data dengan perulangan while
                while ($data = mysqli_fetch_array($hasil)):
                $no++;
                $query1=mysqli_query($kon,"select * from peserta where id_siswa='".$data['id_siswa']."' and id_kelas='".$_POST['id_kelas']."' and id_ujian='".$_POST['id_ujian']."'");
                $jumlah = mysqli_num_rows($query1);

                if ($jumlah>=1){
                    $cek="checked";
                    $status=1;
                }else {
                    $cek="";
                    $status=0;
                }

                $jumlah_peserta+=$status;
            ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $data['nis']; ?></td>
                <td>
                    <?php echo $data['nama_siswa']; ?>
                    <input type="hidden" name="id_siswa[]"  value="<?php echo $data['id_siswa']; ?>" />
                    <input type="hidden" name="stat[]" id="status<?php echo $no; ?>" value="<?php echo $status; ?>" />
                    <script>                    
                        $("#pilih<?php echo $no; ?>").change(function() {
                            if ($('#pilih<?php echo $no; ?>').prop("checked") == false){
                                $("#status<?php echo $no; ?>").val("0");
                            }else {
                                $("#status<?php echo $no; ?>").val("1");
                            }
                        });
                    </script>
                </td>
                <td class="text-center">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" <?php echo $cek; ?> class="custom-control-input" name="pilih[]" id="pilih<?php echo $no; ?>">
                        <label class="custom-control-label" for="pilih<?php echo $no; ?>"></label>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</form>
<?php if ($jumlah_peserta==$no): ?>
<script>
    $('#CheckAll').prop('checked', true);
</script>
<?php endif;?>

<button type="button" class="btn btn-primary mt-3"  id="simpan_peserta" >Simpan</button>

<script>
  
    
    $('#simpan_peserta').click(function(){

        konfirmasi=confirm("Apakah anda yakin ingin menyimpan pengaturan peserta ujian ini? ")
        if (konfirmasi){
            var form = $('#form_peserta')[0];
            var data = new FormData(form);
    
            $.ajax({
                type	: 'POST',
                url: 'pages/guru/ujian/tambah-peserta.php',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                success	: function(data){
                    window.location.href = "index.php?page=ujian&peserta=berhasil";
                }
            });
        }else {

        }
    });
</script>