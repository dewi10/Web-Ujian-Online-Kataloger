<?php
//Koneksi database
include '../../../config/database.php';

//Memulai transaksi
mysqli_query($kon, "START TRANSACTION");

$id_soal = isset($_GET['id_soal']) ? $_GET['id_soal'] : 0;
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$gambar = isset($_GET['gambar']) ? $_GET['gambar'] : '';
$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : '';

$is_ajax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

function kirim_respon_hapus($is_ajax, $status, $id, $pesan = '') {
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => $status,
            'id_ujian' => $id,
            'pesan' => $pesan
        ));
        exit;
    }

    if ($status === 'ok') {
        header("Location:../../../index.php?page=input-soal&id=$id&hapus=berhasil");
    } else {
        header("Location:../../../index.php?page=input-soal&id=$id&hapus=gagal");
    }
    exit;
}

if ($tipe == 'pg') {
    //Menghapus data dalam tabel soal
    $hapus_soal = mysqli_query($kon, "delete from soal where id_soal='$id_soal'");

    //Menghapus data dalam tabel jawaban
    $hapus_jawaban = mysqli_query($kon, "delete from jawaban where id_soal='$id_soal'");

    //Menghapus foto jika ada
    if (!empty($gambar) && file_exists("gambar/" . $gambar)) {
        unlink("gambar/" . $gambar);
    }

    if ($hapus_soal && $hapus_jawaban) {
        mysqli_query($kon, "COMMIT");
        kirim_respon_hapus($is_ajax, 'ok', $id, 'Soal telah dihapus');
    } else {
        mysqli_query($kon, "ROLLBACK");
        kirim_respon_hapus($is_ajax, 'error', $id, 'Soal gagal dihapus');
    }
} else if ($tipe == 'essay') {
    //Menghapus data dalam tabel soal
    $hapus_soal = mysqli_query($kon, "delete from soal where id_soal='$id_soal'");

    //Menghapus foto jika ada
    if (!empty($gambar) && file_exists("gambar/" . $gambar)) {
        unlink("gambar/" . $gambar);
    }

    if ($hapus_soal) {
        mysqli_query($kon, "COMMIT");
        kirim_respon_hapus($is_ajax, 'ok', $id, 'Soal telah dihapus');
    } else {
        mysqli_query($kon, "ROLLBACK");
        kirim_respon_hapus($is_ajax, 'error', $id, 'Soal gagal dihapus');
    }
} else {
    mysqli_query($kon, "ROLLBACK");
    kirim_respon_hapus($is_ajax, 'error', $id, 'Tipe soal tidak valid');
}
?>