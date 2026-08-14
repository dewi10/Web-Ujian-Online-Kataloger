<?php
session_start();
require_once "../../../config/database.php";

// Validasi login
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda belum login']);
    exit;
}

// Ambil data dari form
$id_siswa = $_POST['id_siswa'];
$nomor_dokumen = $_POST['nomor_dokumen'];
$instansi_kementerian = $_POST['instansi_kementerian'];
$periode = $_POST['periode'];

// Data tanda tangan
$ttd_yang_dinilai = $_POST['ttd_yang_dinilai'];
$ttd_penilai = $_POST['ttd_penilai'];
$tanggal_ttd_yang_dinilai = $_POST['tanggal_ttd_yang_dinilai'];
$tanggal_ttd_penilai = $_POST['tanggal_ttd_penilai'];

// Data konversi (5 baris)
$hasil_1 = $_POST['hasil_1'];
$perilaku_1 = $_POST['perilaku_1'];
$presentase_1 = $_POST['presentase_1'];
$koefisien_1 = $_POST['koefisien_1'];
$keterangan_1 = $_POST['keterangan_1'];

$hasil_2 = $_POST['hasil_2'];
$perilaku_2 = $_POST['perilaku_2'];
$presentase_2 = $_POST['presentase_2'];
$koefisien_2 = $_POST['koefisien_2'];
$keterangan_2 = $_POST['keterangan_2'];

$hasil_3 = $_POST['hasil_3'];
$perilaku_3 = $_POST['perilaku_3'];
$presentase_3 = $_POST['presentase_3'];
$koefisien_3 = $_POST['koefisien_3'];
$keterangan_3 = $_POST['keterangan_3'];

$hasil_4 = $_POST['hasil_4'];
$perilaku_4 = $_POST['perilaku_4'];
$presentase_4 = $_POST['presentase_4'];
$koefisien_4 = $_POST['koefisien_4'];
$keterangan_4 = $_POST['keterangan_4'];

$hasil_5 = $_POST['hasil_5'];
$perilaku_5 = $_POST['perilaku_5'];
$presentase_5 = $_POST['presentase_5'];
$koefisien_5 = $_POST['koefisien_5'];
$keterangan_5 = $_POST['keterangan_5'];

// Cek apakah data sudah ada
$cek = mysqli_query($kon, "SELECT * FROM pak_konversi WHERE id_siswa='$id_siswa'");
$ada = mysqli_num_rows($cek);

if ($ada > 0) {
    // Update data
    $query = "UPDATE pak_konversi SET 
        nomor_dokumen = '$nomor_dokumen',
        instansi_kementerian = '$instansi_kementerian',
        periode = '$periode',
        hasil_1 = '$hasil_1',
        perilaku_1 = '$perilaku_1',
        presentase_1 = '$presentase_1',
        koefisien_1 = '$koefisien_1',
        keterangan_1 = '$keterangan_1',
        hasil_2 = '$hasil_2',
        perilaku_2 = '$perilaku_2',
        presentase_2 = '$presentase_2',
        koefisien_2 = '$koefisien_2',
        keterangan_2 = '$keterangan_2',
        hasil_3 = '$hasil_3',
        perilaku_3 = '$perilaku_3',
        presentase_3 = '$presentase_3',
        koefisien_3 = '$koefisien_3',
        keterangan_3 = '$keterangan_3',
        hasil_4 = '$hasil_4',
        perilaku_4 = '$perilaku_4',
        presentase_4 = '$presentase_4',
        koefisien_4 = '$koefisien_4',
        keterangan_4 = '$keterangan_4',
        hasil_5 = '$hasil_5',
        perilaku_5 = '$perilaku_5',
        presentase_5 = '$presentase_5',
        koefisien_5 = '$koefisien_5',
        keterangan_5 = '$keterangan_5',
        ttd_yang_dinilai = '$ttd_yang_dinilai',
        ttd_penilai = '$ttd_penilai',
        tanggal_ttd_yang_dinilai = '$tanggal_ttd_yang_dinilai',
        tanggal_ttd_penilai = '$tanggal_ttd_penilai',
        updated_at = NOW()
        WHERE id_siswa = '$id_siswa'";
} else {
    // Insert data baru
    $query = "INSERT INTO pak_konversi (
        id_siswa, nomor_dokumen, instansi_kementerian, periode,
        hasil_1, perilaku_1, presentase_1, koefisien_1, keterangan_1,
        hasil_2, perilaku_2, presentase_2, koefisien_2, keterangan_2,
        hasil_3, perilaku_3, presentase_3, koefisien_3, keterangan_3,
        hasil_4, perilaku_4, presentase_4, koefisien_4, keterangan_4,
        hasil_5, perilaku_5, presentase_5, koefisien_5, keterangan_5,
        ttd_yang_dinilai, ttd_penilai, tanggal_ttd_yang_dinilai, tanggal_ttd_penilai,
        created_at, updated_at
    ) VALUES (
        '$id_siswa', '$nomor_dokumen', '$instansi_kementerian', '$periode',
        '$hasil_1', '$perilaku_1', '$presentase_1', '$koefisien_1', '$keterangan_1',
        '$hasil_2', '$perilaku_2', '$presentase_2', '$koefisien_2', '$keterangan_2',
        '$hasil_3', '$perilaku_3', '$presentase_3', '$koefisien_3', '$keterangan_3',
        '$hasil_4', '$perilaku_4', '$presentase_4', '$koefisien_4', '$keterangan_4',
        '$hasil_5', '$perilaku_5', '$presentase_5', '$koefisien_5', '$keterangan_5',
        '$ttd_yang_dinilai', '$ttd_penilai', '$tanggal_ttd_yang_dinilai', '$tanggal_ttd_penilai',
        NOW(), NOW()
    )";
}

$result = mysqli_query($kon, $query);

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . mysqli_error($kon)]);
}
?>
