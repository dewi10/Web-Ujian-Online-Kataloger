<?php
    
    if (isset($_POST['tambah_ujian'])) {
        session_start();
        //Fungsi untuk mencegah inputan karakter yang tidak sesuai
        function input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        //Cek apakah ada kiriman form dari method post
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            include '../../../config/database.php';
            //Memulai transaksi
            mysqli_query($kon,"START TRANSACTION");

            $cek_kolom = mysqli_query($kon, "SHOW COLUMNS FROM ujian LIKE 'status_aktif'");
            if ($cek_kolom && mysqli_num_rows($cek_kolom) == 0) {
                mysqli_query($kon, "ALTER TABLE ujian ADD COLUMN status_aktif TINYINT(1) NOT NULL DEFAULT 1");
            }
            
            $id_guru=$_SESSION["id_guru"];
            $judul=input($_POST["judul"]);
            $tipe_soal=input($_POST["tipe_soal"]);
            $id_mapel=input($_POST["id_mapel"]);
            $tanggal=input($_POST["tanggal"]);
            $jam=input($_POST["jam"]);
            $waktu=input($_POST["waktu"]);
            $nilai_kelulusan=input($_POST["nilai_kelulusan"]);

            $kelas=$_POST["kelas"];

            for ($i=0; $i < count($kelas) ; $i++){

                $query = mysqli_query($kon, "SELECT max(id_ujian) as id_terbesar FROM ujian");
                $ambil= mysqli_fetch_array($query);
                $id_ujian = $ambil['id_terbesar'];
                $id_ujian++;
                //Membuat kode ujian
                $huruf = "U";
                $kode_ujian = $huruf . sprintf("%03s", $id_ujian);
          
                $sql="insert into ujian (judul,tipe_soal,kode_ujian,id_kelas,id_mapel,id_guru,tanggal,jam,waktu,nilai_kelulusan,status_aktif) values
                ('$judul','$tipe_soal','$kode_ujian','$kelas[$i]','$id_mapel','$id_guru','$tanggal','$jam','$waktu','$nilai_kelulusan','1')";

                //Menyimpan ke tabel ujian
                $simpan_ujian=mysqli_query($kon,$sql);

            }
            
            if ($simpan_ujian) {
                mysqli_query($kon,"COMMIT");
                header("Location:../../../index.php?page=ujian&tambah=berhasil");
            }
            else {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=ujian&tambah=gagal");
            }
        }
    }
?>




<form action="pages/guru/ujian/tambah.php" method="post">


    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Judul:</label>
                <input type="text" name="judul" id="judul" class="form-control" placeholder="Masukan Judul Ujian" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Mata Ujian:</label>
                <select class="form-control" name="id_mapel">
                <?php
                    include '../../../config/database.php';
                    $cek_mapel = mysqli_query($kon, "SHOW COLUMNS FROM mapel LIKE 'status_aktif'");
                    if ($cek_mapel && mysqli_num_rows($cek_mapel) > 0) {
                        $sql = "select * from mapel where status_aktif='1' order by nama_mapel";
                    } else {
                        $sql = "select * from mapel order by nama_mapel";
                    }
                    $hasil=mysqli_query($kon,$sql);
                    while ($data = mysqli_fetch_array($hasil)) {
                        ?>
                    <option value="<?php echo $data['id_mapel'];?>"><?php echo $data['nama_mapel'];?></option>
                    <?php
                    }
                ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Pilih Kategori: (Dapat diilih lebih dari satu)</label>
                <select class="select2" multiple="multiple" style="width: 100%" name="kelas[]">
                <?php
                    include '../../../config/database.php';
                    $sql="select * from kelas";
                    $hasil=mysqli_query($kon,$sql);
                    while ($data = mysqli_fetch_array($hasil)){
                        ?>
                    <option value="<?php echo $data['id_kelas'];?>"><?php echo $data['nama_kelas'];?></option>
                    <?php
                    }
                ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Standar Minimal Nilai Kelulusan:</label>
                <select class="form-control" name="nilai_kelulusan">
                <?php
                    for($i=1;$i<=100;$i++){    
                ?>
                    <option <?php if ($i==50) echo "selected"; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php
                    }
                ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Tanggal:</label>
                <input type="date" name="tanggal" class="form-control"  required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Jam:</label>
                <input type="time" name="jam" class="form-control"  required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Waktu:</label>
                <input type="number" name="waktu" id="waktu" class="form-control" placeholder="Masukan Waktu" required>
                <label><div id="tampil_waktu"></div></label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Tipe Soal:</label>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" name="tipe_soal" value="1" required class="tipe_soal form-check-input" >Pilihan Ganda
                    </label>
                </div>
                <div class="form-check">
                    <label class="form-check-label">
                        <input type="radio" name="tipe_soal" value="2" required class="tipe_soal form-check-input" >Essay
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-4">
            <button type="submit" name="tambah_ujian" id="Submit" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit</button>
            <button type="reset" class="btn btn-secondary">Reset</button>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $(".select2").select2({
        });
    });

    $('#waktu').bind('keyup', function () {
        var waktu=$("#waktu").val();
        $("#tampil_waktu").html("<br><span class='badge badge-primary'>"+waktu+" Menit</span>");   
    }); 
</script>