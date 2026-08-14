<?php
    //validasi hanya admin yang boleh mengakses halaman ini
    $username = $_SESSION['username'];
    $cek = mysqli_query ($kon,"select * from admin where username='".$username."' limit 1");
    $jum = mysqli_num_rows($cek);

    if ($jum<1){
        echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
        exit;
    }

    // Sinkronisasi akun siswa: jika username/password kosong, otomatis isi dari NIP
    // username = NIP, password = md5(NIP)
    mysqli_query($kon, "UPDATE siswa SET username=nis WHERE (username IS NULL OR username='')");
    mysqli_query($kon, "UPDATE siswa SET password=MD5(nis) WHERE (password IS NULL OR password='')");
?>
<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-user"></i> Data Peserta Ujian</h3>
    </div>
    <div class="card-body">
    <script> $('title').text('DATA PESERTA UJIAN'); </script>
        <?php
            if (isset($_SESSION['flash_tambah_siswa'])) {
                $flash_status = $_SESSION['flash_tambah_siswa']['status'];
                $flash_message = $_SESSION['flash_tambah_siswa']['message'];
                echo "<div class='alert alert-".$flash_status."'><strong>".($flash_status=='success'?'Berhasil!':'Gagal!')."</strong> ".$flash_message."</div>";
                echo "<script>alert('".addslashes($flash_message)."');</script>";
                unset($_SESSION['flash_tambah_siswa']);
            }

            //Validasi untuk menampilkan pesan pemberitahuan saat user menambah siswa
            if (isset($_GET['tambah'])) {
                if ($_GET['tambah']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah ditambah!</div>";
                }else if ($_GET['tambah']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal ditambahkan!</div>";
                }    
            }
            
            if (isset($_GET['edit'])) {
                if ($_GET['edit']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah diupdate!</div>";
                }else if ($_GET['edit']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal diupdate!</div>";
                }    
            }
            if (isset($_GET['hapus'])) {
                if ($_GET['hapus']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Data telah dihapus!</div>";
                }else if ($_GET['hapus']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Data gagal dihapus!</div>";
                }    
            }
            if (isset($_GET['akun'])) {
                if ($_GET['akun']=='berhasil'){
                    echo"<div class='alert alert-success'><strong>Berhasil!</strong> Username & password telah diatur</div>";
                }else if ($_GET['akun']=='gagal'){
                    echo"<div class='alert alert-danger'><strong>Gagal!</strong> Username & password gagal diatur</div>";
                }    
            }
        ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button type="button" class="btn btn-danger" id="btn_tambah" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;"><i class="fas fa-plus"></i> Tambah</button>
                <button type="button" class="btn btn-outline-danger ml-2" id="btn_hapus"><i class="fas fa-trash"></i> Hapus</button>
            </div>
            <div class="d-flex align-items-center">
                <select class="form-control mr-2" id="kelas" style="width: 200px;">
                    <option value="">Pilih Kategori</option>
                    <?php
                        include 'config/database.php';
                        $sql="select * from kelas";
                        $hasil=mysqli_query($kon,$sql);
                        while ($data = mysqli_fetch_array($hasil)) {
                            ?>
                        <option value="<?php echo $data['id_kelas'];?>"><?php echo $data['nama_kelas'];?> </option>
                        <?php
                        }
                    ?>
                </select>
                <input type="button" class="btn btn-warning" id="tampilkan" value="Tampilkan">
            </div>
        </div>

        <form id="form_siswa">
        <div id="tampil_tabel"></div>
        </form>

    </div> 
</div>

<div class="modal fade" id="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        <div class="modal-header" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); color: white;">
            <h4 class="modal-title" id="judul"></h4>
            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">&times;</button>
        </div>

        <div class="modal-body">
            <div id="tampil_data">
                 <!-- Data akan di load menggunakan AJAX -->                   
            </div>  
        </div>
  
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

        </div>
    </div>
</div>





<script>

    
    $(document).ready( function () {
        tabel_siswa();
    });



    $('#tampilkan').on('click',function(){
        tabel_siswa();
    });


    function tabel_siswa(){
        var kelas=$("#kelas").val();
        $.ajax({
            url: 'pages/admin/siswa/tabel-siswa.php',
            method: 'post',
            data: {kelas:kelas},
            success:function(data){
                $('#tampil_tabel').html(data);  
            }
        });
    }

    $('#btn_hapus').click(function(){

        konfirmasi=confirm("Semua peserta ujian yang di centang akan dihapus, Apakah anda yakin ingin melakukan tindakan ini? ")
        if (konfirmasi){
            var form = $('#form_siswa')[0];
            var data = new FormData(form);
       
            $.ajax({
                type	: 'POST',
                url: 'pages/admin/siswa/hapus-semua.php',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                error: function (request, error) {
                    alert('Gagal menghapus');
                },
                success	: function(data){
                    window.location.href = "index.php?page=siswa&multiplehapus=berhasil";
                }
            });
        }else {

        }
    });
</script>

