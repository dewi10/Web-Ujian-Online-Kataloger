<?php
// Cek apakah ada kiriman form dari method post
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include '../../../config/database.php';
    $id_ujian_asal = mysqli_real_escape_string($kon, $_POST['id_ujian_asal']);
    $id_ujian_tujuan = mysqli_real_escape_string($kon, $_POST['id_ujian_tujuan']);

    // Mendapatkan daftar soal yang dicentang
    $soal_dicentang = $_POST['soal'] ?? [];
    
    // Validasi apakah ada soal yang dipilih
    if (empty($soal_dicentang)) {
        echo "<script>alert('Silakan pilih minimal 1 soal untuk disalin!');</script>";
        exit;
    }

    // Mendapatkan data soal dari ujian asal
    foreach ($soal_dicentang as $id_soal) {
        $id_soal = mysqli_real_escape_string($kon, $id_soal);
        
        $query = mysqli_query($kon, "SELECT * FROM soal WHERE id_soal='$id_soal'");
        $data = mysqli_fetch_array($query);
        
        if ($data) {
            $kode_soal = mysqli_real_escape_string($kon, $data['kode_soal']);
            $soal = mysqli_real_escape_string($kon, $data['soal']);
            $gambar = mysqli_real_escape_string($kon, $data['gambar']);
            
            // Insert soal baru
            $insert_soal = mysqli_query($kon, "INSERT INTO soal (kode_soal, soal, gambar, id_ujian, tipe) 
                                VALUES ('$kode_soal', '$soal', '$gambar', '$id_ujian_tujuan', '1')");
            
            if ($insert_soal) {
                // Mendapatkan id_soal yang baru saja diinsert
                $id_soal_baru = mysqli_insert_id($kon);
                
                // Mendapatkan data jawaban
                $query_jawaban = mysqli_query($kon, "SELECT * FROM jawaban WHERE id_soal='$id_soal'");
                while ($data_jawaban = mysqli_fetch_array($query_jawaban)) {
                    $pilihan = mysqli_real_escape_string($kon, $data_jawaban['pilihan']);
                    $jawaban = mysqli_real_escape_string($kon, $data_jawaban['jawaban']);
                    mysqli_query($kon, "INSERT INTO jawaban (pilihan, jawaban, id_soal) 
                                        VALUES ('$pilihan', '$jawaban', '$id_soal_baru')");
                }
            }
        }
    }
}
?>
