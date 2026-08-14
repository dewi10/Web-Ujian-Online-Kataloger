<div class="table-responsive">
    <table class="table table-hover" id="tabel_siswa">
        <thead class="thead-light">
        <tr>
        <th width="5%" class="text-center">
            <script>
            function toggle(pilih) { 
                
                checkboxes = document.getElementsByName('siswa[]');
                for(var i=0, n=checkboxes.length;i<n;i++)
                { 
                    checkboxes[i].checked = pilih.checked;
                } 
            } 
            </script>
    
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="checkall"  onClick="toggle(this)"  />
                    <label class="custom-control-label" for="checkall"></label>
                </div>
            </th>
            <th>No</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th width="15%">Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php
            // include database
            include '../../../config/database.php';
            $kelas=$_POST['kelas'];
            if ($kelas==""){
                $sql="select * from siswa s LEFT join kelas k on k.id_kelas=s.id_kelas order by k.id_kelas,s.nis asc";
            } else {
                $sql="select * from siswa s LEFT join kelas k on k.id_kelas=s.id_kelas where k.id_kelas='".$kelas."' order by k.id_kelas,s.nis asc";
            } 
           
            $hasil=mysqli_query($kon,$sql);
            $no=0;
            //Menampilkan data dengan perulangan while
            while ($data = mysqli_fetch_array($hasil)):
            $no++;
        ?>
        <tr>
            <td class="text-center">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="customCheck<?php echo $no;?>" value="<?php echo $data['id_siswa']; ?>"  name="siswa[]" />
                    <label class="custom-control-label" for="customCheck<?php echo $no;?>"></label>
                </div>
            </td>
            <td><?php echo $no; ?></td>
            <td><?php echo $data['nis'];?></td>
            <td><?php echo $data['nama_siswa'];?></td>
            <td><?php echo $data['nama_kelas'];?></td>
            <td>
            <button type="button" class="setting_akun btn btn-primary btn-circle" id_siswa="<?php echo $data['id_siswa']; ?>"  kode_siswa="<?php echo $data['kode_siswa']; ?>" ><i class="fas fa-user"></i></button>
            <button type="button" class="btn_edit btn btn-warning btn-circle" id_siswa="<?php echo $data['id_siswa']; ?>" kode_siswa="<?php echo $data['kode_siswa']; ?>" ><i class="fa fa-edit"></i></button>
            <a href="pages/admin/siswa/hapus.php?id_siswa=<?php echo $data['id_siswa']; ?>" class="btn_hapus btn btn-danger btn-circle" ><i class="fas fa-trash"></i></a>
        </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#tabel_siswa').DataTable( {
            "searching": true,
            "paging":   true,
            "ordering": true,
            "info":     true,
            dom: 'Bfrtip',
            buttons: ['excel','print','copy']
        });
    });
</script>

<script>
    // Tambah siswa
    $('#btn_tambah').on('click',function(){
        $.ajax({
            url: 'pages/admin/siswa/tambah.php',
            method: 'post',
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Tambah Peserta';
            }
        });
        // Membuka modal
        $('#modal').modal('show');
    });


    // fungsi edit siswa
    $('.btn_edit').on('click',function(){

        var id_siswa = $(this).attr("id_siswa");
        var kode_siswa = $(this).attr("kode_siswa");
        $.ajax({
            url: 'pages/admin/siswa/edit.php',
            method: 'post',
            data: {kode_siswa:kode_siswa,id_siswa:id_siswa},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Edit Peserta ';
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });

    $('.setting_akun').on('click',function(){
        var id_siswa = $(this).attr("id_siswa");
        var kode_siswa = $(this).attr("kode_siswa");
        $.ajax({
            url: 'pages/admin/siswa/setting-akun.php',
            method: 'post',
            data: {kode_siswa:kode_siswa,id_siswa:id_siswa},
            success:function(data){
                $('#tampil_data').html(data);  
                document.getElementById("judul").innerHTML='Setting Akun';
            }
        });
            // Membuka modal
        $('#modal').modal('show');
    });

    // fungsi hapus siswa
    $('.btn_hapus').on('click',function(){
        konfirmasi=confirm("Yakin ingin menghapus data ini?")
        if (konfirmasi){
            return true;
        }else {
            return false;
        }
    });
</script>