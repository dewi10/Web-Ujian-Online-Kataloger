<?php 
session_start();
include '../../../config/database.php';

// Memulai transaksi
mysqli_query($kon, "START TRANSACTION");

$id_ujian = $_GET["id"];
$id_siswa = $_SESSION["id_siswa"];
$jumlah_soal = 0;
$jumlah_benar = 0;

$hasil = mysqli_query($kon, "select * from hasil where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
$jumlah_soal = mysqli_num_rows($hasil);

$hasil2 = mysqli_query($kon, "select * from hasil h inner join jawaban j on j.id_jawaban=h.id_jawaban where h.id_ujian='$id_ujian' and j.jawaban=1 and h.id_siswa='$id_siswa'");
$jumlah_benar = mysqli_num_rows($hasil2);

$nilai = ($jumlah_benar / $jumlah_soal) * 100;

// Update nilai setelah siswa selesai mengerjakan soal
$simpan_nilai = mysqli_query($kon, "update nilai set nilai='$nilai' where id_ujian='$id_ujian' and id_siswa='$id_siswa'");

$tanggal = date('Y-m-d H:i');
// Masukan ke tabel riwayat sebagai tanda bahwa siswa telah mengikuti ujian
$simpan_riwayat = mysqli_query($kon, "insert into riwayat (id_ujian,id_siswa,tanggal) values ('$id_ujian','$id_siswa','$tanggal')");

// Kosongkan nilai dalam session
$_SESSION['mulai_ujian'] = '';
$_SESSION['nama_mapel'] = '';
$_SESSION['id_ujian'] = '';
// Hapus session
unset($_SESSION['mulai_ujian']); 
unset($_SESSION['nama_mapel']); 
unset($_SESSION['id_ujian']); 

$hasil = mysqli_query($kon, "select tipe_soal from ujian where id_ujian='$id_ujian'");
$data = mysqli_fetch_array($hasil);

if ($simpan_nilai && $simpan_riwayat) {
    mysqli_query($kon, "COMMIT");

    // Check if there's another exam today based on the order in peserta table
    date_default_timezone_set('Asia/Jakarta');
    $today = date('Y-m-d');

    $sql = "SELECT p.id_ujian 
            FROM peserta p
            INNER JOIN ujian u ON u.id_ujian = p.id_ujian
            INNER JOIN siswa s ON s.id_siswa = '$id_siswa' AND s.id_kelas = u.id_kelas
            WHERE p.id_siswa = '$id_siswa' 
            ORDER BY p.id_ujian ASC";

    $result = mysqli_query($kon, $sql);
    $next_exam_found = false;
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['id_ujian'] > $id_ujian) {
            $next_id_ujian = $row['id_ujian'];
            $next_exam_found = true;
            break;
        }
    }

    if ($next_exam_found) {
        // Redirect to the next exam and set a session variable to redirect to the results page after the next exam
        if (isset($_SESSION['redirect_to_results']) && $_SESSION['redirect_to_results'] === true) {
            unset($_SESSION['redirect_to_results']);
            header("Location: ../../../index.php?page=hasil-ujian-siswa");
        } else {
            $_SESSION['redirect_to_results'] = true;
            header("Location: ../../../index.php?page=review&id=$next_id_ujian");
        }
    } else {
        // If no more exams today, show the final result
        header("Location: ../../../index.php?page=hasil-ujian-siswa");
    }

} else {
    mysqli_query($kon, "ROLLBACK");
    echo "<script>alert('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'); window.location.href='../../../index.php?page=ujian';</script>";
}
?>
