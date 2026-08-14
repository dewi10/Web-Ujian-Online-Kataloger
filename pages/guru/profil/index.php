<?php
//validasi hanya guru yang boleh mengakses halaman ini
$username = $_SESSION['username'];
$cek = mysqli_query ($kon,"select * from guru where username='".$username."' limit 1");
$jum = mysqli_num_rows($cek);

if ($jum<1){
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}
?>
<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-user"></i> Profil</h3>
    </div>
    <div class="card-body">
    <?php
        include 'config/database.php';
        $query ="select * from guru where id_guru='".$_SESSION["id_guru"]."' limit 1"; 
        $hasil=mysqli_query($kon,$query);
        $row = mysqli_fetch_array($hasil);
    ?>


        <div class="table-responsive">
            <table class="table table-border">
                <tbody>
                <tr>
                    <td width="20%">Nama</td>  
                    <td>: <?php echo $row['nama_guru'];?></td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td> <td>: <?php echo $row['jk'] == 1 ? 'Laki-laki' : 'Perempuan';?></td>
                </tr>
                <tr>
                    <td>Alamat</td>  <td>:  <?php echo $row['alamat'];  ?> </td>
                </tr>
                <tr>
                    <td>No Telp</td><td>:  <?php echo $row['no_telp'];  ?> </td>
                </tr>
                <tr>
                    <td>Email</td>  <td>:  <?php echo $row['email'];  ?> </td>
                </tr>

                </tbody>
            </table>
        </div>
    </div> 
</div>


