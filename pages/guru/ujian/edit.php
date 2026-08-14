<?php

    if (isset($_POST['edit_ujian'])) {
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
            mysqli_query($kon,"START TRANSACTION");
 
            $id_guru=$_SESSION["id_guru"];
            $judul=input($_POST["judul"]);
            $tipe_soal=input($_POST["tipe_soal"]);
            $id_ujian=input($_POST["id_ujian"]);
            $id_kelas=input($_POST["id_kelas"]);
            $id_mapel=input($_POST["id_mapel"]);
            $tanggal=input($_POST["tanggal"]);
            $jam=input($_POST["jam"]);
            $waktu=input($_POST["waktu"]);
            $nilai_kelulusan=input($_POST["nilai_kelulusan"]);

            $id_guru_esc = mysqli_real_escape_string($kon, (string) $id_guru);
            $id_ujian_esc = mysqli_real_escape_string($kon, (string) $id_ujian);

            $sql_cek = "SELECT 1 FROM ujian WHERE id_ujian='$id_ujian_esc' AND id_guru='$id_guru_esc' LIMIT 1";
            $hasil_cek = mysqli_query($kon, $sql_cek);
            if (!$hasil_cek || mysqli_num_rows($hasil_cek) === 0) {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=ujian&edit=gagal");
                exit;
            }

            $judul_esc = mysqli_real_escape_string($kon, $judul);
            $tipe_esc = mysqli_real_escape_string($kon, $tipe_soal);
            $id_kelas_esc = mysqli_real_escape_string($kon, (string) $id_kelas);
            $id_mapel_esc = mysqli_real_escape_string($kon, (string) $id_mapel);
            $tanggal_esc = mysqli_real_escape_string($kon, $tanggal);
            $jam_esc = mysqli_real_escape_string($kon, $jam);
            $waktu_esc = mysqli_real_escape_string($kon, (string) $waktu);
            $nilai_esc = mysqli_real_escape_string($kon, (string) $nilai_kelulusan);

            // Tetap satu id_ujian: soal & peserta tidak pindah. Untuk ujian ulang, centang reset di form → hapus hasil/nilai/riwayat.
            $sql="update ujian set
            id_guru='$id_guru_esc',
            judul='$judul_esc',
            tipe_soal='$tipe_esc',
            id_kelas='$id_kelas_esc',
            id_mapel='$id_mapel_esc',
            tanggal='$tanggal_esc',
            jam='$jam_esc',
            waktu='$waktu_esc',
            nilai_kelulusan='$nilai_esc'
            where id_ujian='$id_ujian_esc' AND id_guru='$id_guru_esc'";
        
            $simpan_ujian=mysqli_query($kon,$sql);

            if (!$simpan_ujian) {
                mysqli_query($kon,"ROLLBACK");
                header("Location:../../../index.php?page=ujian&edit=gagal");
                exit;
            }

            $minta_reset = isset($_POST['reset_ujian_ulang']) && $_POST['reset_ujian_ulang'] === '1';
            if ($minta_reset) {
                $hapus = ['hasil', 'nilai', 'riwayat'];
                foreach ($hapus as $tbl) {
                    if (!mysqli_query($kon, "DELETE FROM `$tbl` WHERE id_ujian='$id_ujian_esc'")) {
                        mysqli_query($kon,"ROLLBACK");
                        header("Location:../../../index.php?page=ujian&edit=gagal");
                        exit;
                    }
                }
            }

            mysqli_query($kon,"COMMIT");
            header("Location:../../../index.php?page=ujian&edit=berhasil");
            exit;
        }
    }
?>

<?php 
    include '../../../config/database.php';
    if (!isset($_POST["id_ujian"])) {
        echo '<div class="alert alert-danger">Data tidak valid.</div>';
        return;
    }
    $id_ujian_esc = mysqli_real_escape_string($kon, (string) $_POST["id_ujian"]);
    $sql="select * from ujian where id_ujian='$id_ujian_esc' limit 1";
    $hasil=mysqli_query($kon,$sql);
    $row = mysqli_fetch_array($hasil);
    if (!$row) {
        echo '<div class="alert alert-danger">Ujian tidak ditemukan.</div>';
        return;
    }
?>


<form action="pages/guru/ujian/edit.php" method="post">

    <input type="hidden" name="id_ujian" value="<?php echo $row['id_ujian'];?>" class="form-control" >

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Judul:</label>
                <input type="text" name="judul" id="judul" value="<?php echo $row['judul'];?>" class="form-control" placeholder="Masukan Judul Ujian" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Mata Ujian:</label>
                <select class="form-control" name="id_mapel">
                <?php
                    include '../../../config/database.php';
                    $id_mapel_ujian = (int)$row['id_mapel'];
                    $cek_mapel = mysqli_query($kon, "SHOW COLUMNS FROM mapel LIKE 'status_aktif'");
                    if ($cek_mapel && mysqli_num_rows($cek_mapel) > 0) {
                        $sql = "select * from mapel where status_aktif='1' OR id_mapel=".$id_mapel_ujian." order by nama_mapel";
                    } else {
                        $sql = "select * from mapel order by nama_mapel";
                    }
                    $hasil=mysqli_query($kon,$sql);
                    while ($data = mysqli_fetch_array($hasil)) {
                ?>
                    <option <?php if ($data['id_mapel']==$row['id_mapel']) echo "selected"; ?> value="<?php echo $data['id_mapel'];?>"><?php echo $data['nama_mapel'];?></option>
                <?php
                    }
                ?>
                </select>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Kategori:</label>
                <select class="form-control" name="id_kelas">
                <?php
                    include '../../../config/database.php';
                    $sql="select * from kelas";
                    $hasil=mysqli_query($kon,$sql);
                    while ($data = mysqli_fetch_array($hasil)) {
                ?>
                    <option <?php if ($data['id_kelas']==$row['id_kelas']) echo "selected"; ?> value="<?php echo $data['id_kelas'];?>"><?php echo $data['nama_kelas'];?></option>
                <?php
                    }
                ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Standar Minimal Nilai Kelulusan:</label>
                <select class="form-control" name="nilai_kelulusan">
                <?php
                    for($i=1;$i<=100;$i++){
                ?>
                    <option <?php if ($row['nilai_kelulusan']==$i) echo "selected"; ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php
                    }
                ?>
                </select>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Waktu (menit):</label>
                <input type="number" name="waktu" value="<?php echo $row['waktu'];?>" class="form-control" placeholder="Masukan Waktu" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Tanggal:</label>
                <input type="date" name="tanggal" value="<?php echo $row['tanggal'];?>" class="form-control" required>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Jam:</label>
                <input type="time" name="jam" value="<?php echo $row['jam'];?>" class="form-control" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Tipe Soal:</label>
                <div class="d-flex align-items-center" style="gap:20px;">
                    <label class="mb-0">
                        <input type="radio" name="tipe_soal" <?php if ($row['tipe_soal']==1) echo "checked";?> value="1" required class="tipe_soal"> Pilihan Ganda
                    </label>
                    <label class="mb-0">
                        <input type="radio" name="tipe_soal" <?php if ($row['tipe_soal']==2) echo "checked";?> value="2" required class="tipe_soal"> Essay
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group border rounded p-3 bg-light">
                <label class="d-block font-weight-bold text-danger">Ujian ulang (opsional)</label>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" name="reset_ujian_ulang" value="1" id="reset_ujian_ulang">
                    <label class="custom-control-label" for="reset_ujian_ulang">
                        Hapus semua jawaban, nilai, dan riwayat peserta untuk ujian ini
                    </label>
                </div>
                <small class="text-muted d-block mt-2">
                    Bank soal dan daftar peserta <strong>tetap</strong> (id ujian tidak berubah). Centang hanya jika peserta akan mengerjakan ulang:
                    halaman hasil tidak menampilkan nilai lama sampai ujian selesai lagi. Jangan centang jika hanya memperbaiki typo jadwal.
                </small>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 text-right">
            <button type="submit" name="edit_ujian" id="Submit" class="btn btn-danger" style="background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%); border: none;">Submit</button>
        </div>
    </div>
</form>