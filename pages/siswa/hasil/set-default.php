<?php
    session_start();
    $id_siswa =  $_SESSION["id_siswa"];
    $id_ujian=addslashes(trim($_GET['id']));
    $id_jawaban = 0; // set secara defaul untuk jawaban


    require_once __DIR__ . '/../../../config/database.php';
    require_once __DIR__ . '/../../../config/ujian_soal.php';

    $sql="select * from ujian u 
    inner join soal s on u.id_ujian=s.id_ujian 
    inner join mapel m on m.id_mapel=u.id_mapel
    inner join peserta p on p.id_ujian=u.id_ujian
    where u.id_ujian='$id_ujian' and p.id_siswa='$id_siswa' ";
    $hasil=mysqli_query($kon,$sql);

    //Cek terlebih dahulu apakah ujian yang akan diikuti telah tersedia soalnya
    $cek=mysqli_num_rows($hasil);
    $jumlah_soal=mysqli_num_rows($hasil);

    if ($jumlah_soal<=0){
        header("Location:../../../index.php?page=review&id=$id_ujian&auth=soal-tidak-tersedia");
        exit;
    }

    // Ambil tanggal ujian aktif saat ini
    $q_ujian = mysqli_query($kon, "select tanggal from ujian where id_ujian='$id_ujian' limit 1");
    $d_ujian = mysqli_fetch_array($q_ujian);
    $tanggal_ujian = $d_ujian ? $d_ujian['tanggal'] : '';

    // Cek riwayat ujian siswa (riwayat terakhir)
    $q_riwayat_semua = mysqli_query($kon, "select * from riwayat where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
    $ada_riwayat_semua = mysqli_num_rows($q_riwayat_semua) > 0;

    $ada_riwayat_periode_ini = false;
    if ($tanggal_ujian != '') {
        $q_riwayat_terakhir = mysqli_query($kon, "SELECT MAX(tanggal) AS terakhir FROM riwayat WHERE id_ujian='$id_ujian' AND id_siswa='$id_siswa'");
        $d_riwayat_terakhir = mysqli_fetch_assoc($q_riwayat_terakhir);
        if (!empty($d_riwayat_terakhir['terakhir']) && strtotime($d_riwayat_terakhir['terakhir']) >= strtotime($tanggal_ujian . ' 00:00:00')) {
            $ada_riwayat_periode_ini = true;
        }
    }

    // Cek data hasil yang sudah ada (untuk melanjutkan jika belum selesai)
    $q_hasil = mysqli_query($kon, "select * from hasil where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
    $ada_hasil = mysqli_num_rows($q_hasil) > 0;

    // Jika ada riwayat lama tapi bukan periode ujian saat ini, reset jawaban agar bisa ujian ulang
    if ($ada_riwayat_semua && !$ada_riwayat_periode_ini) {
        mysqli_query($kon, "delete from hasil where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
        mysqli_query($kon, "delete from nilai where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
        $ada_hasil = false;
    }

    // Jika belum ada data hasil (baru pertama kali / habis reset), buat default
    if (!$ada_hasil){

        $sql="select id_soal from soal where id_ujian='$id_ujian'";
        $hasil=mysqli_query($kon,$sql);
        $semua_id_soal = array();
        while ($data = mysqli_fetch_array($hasil)) {
            $semua_id_soal[] = (int) $data['id_soal'];
        }

        $limit = ujian_jumlah_soal_tampil(count($semua_id_soal));
        $seed_key = session_id() . '|' . $id_siswa . '|' . $id_ujian;
        $id_soal_aktif = ujian_pilih_id_soal($semua_id_soal, $limit, $seed_key);

        foreach ($id_soal_aktif as $id_soal_pilih) {
            $sql="insert into hasil (id_siswa,id_ujian,id_soal,id_jawaban) values ('$id_siswa','$id_ujian','$id_soal_pilih','$id_jawaban')";
            mysqli_query($kon,$sql);
        }

        //Masukan nilai 0 sebagai nilai default
        mysqli_query($kon,"insert into nilai (id_ujian,id_siswa,nilai) values ('$id_ujian','$id_siswa','0')");
     
    }

    //Halaman akan dialihkan pada pengerjaan soal
    header("Location:../../../index.php?page=mulai-ujian&id=$id_ujian");

?>