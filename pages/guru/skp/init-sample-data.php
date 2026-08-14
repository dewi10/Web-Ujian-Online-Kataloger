<?php
session_start();
include "../../../config/database.php";

// Create tables
$create_hasil = "CREATE TABLE IF NOT EXISTS skp_hasil_kerja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kategori VARCHAR(1),
    uraian TEXT,
    ekspektasi TEXT,
    urutan INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($kon, $create_hasil);

$create_perilaku = "CREATE TABLE IF NOT EXISTS skp_perilaku_kerja (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uraian TEXT,
    ekspektasi TEXT,
    urutan INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($kon, $create_perilaku);

// Check if data exists
$check_hasil = mysqli_query($kon, "SELECT COUNT(*) as total FROM skp_hasil_kerja");
$row_hasil = mysqli_fetch_assoc($check_hasil);

if($row_hasil['total'] == 0) {
    // Insert sample data for A. UTAMA
    $sample_a = [
        [
            'uraian' => 'Melaksanakan penetapan Kodifikasi Materiel Sistem NSN TNI AD sebanyak 301 item, sesuai Surat Perintah Kabaranahan Nomor: SPRIN/555/VI/2025 tanggal 8 Juli 2025',
            'ekspektasi' => "Ukuran keberhasilan/ indikator kinerja individu dan Target:\n- Pelaksanaan penetapan Kodifikasi sebanyak 17 item materiel Pusnerbad, Pustopad, Puspalad, sasaran sebesar 100%.\n- Pencarian data menggunakan Aplikasi NMCRL, data sesuai dikompulir kedalam format sesuai sasaran 100%.\n- Tingkat kesesuaian pelaksanaan kegiatan dengan perencanaan sebesar 90-100%."
        ],
        [
            'uraian' => 'Sebagai Tenaga Pengajar pada pelatihan Fungsional Kataloger Tingkat Ahli Materi Pengenalan Materiel Non Alutsista beserta Komponenya pada tanggal 2 Juni 2025, sesuai Surat Perintah Kabaranahan Nomor: SPRIN/430/VI/2025 tanggal 17 Juni 2025.',
            'ekspektasi' => "Ukuran keberhasilan/ indikator kinerja individu dan Target:\n- Menyusun Bahan Ajar dan Bahan Tayang sesuai sesuai Rangka Pokok Pelajaran (RPP), sasaran sebesar 100%.\n- Pembuatan Soal soal sesui TIU sebesar 100%.\n- Melaksanakan Kegiatan Belajar Mengajar (KBM) selama 7 jam Pelajaran, sesuai sasaran sebesar 90-100%."
        ],
        [
            'uraian' => 'Bertugas Jaga Stand pada kegiatan Pameran Indo Defence di JExpo Kemayoran pada tanggal 11 sd 14 Juni 2025, sesuai Surat Perintah Kabaranahan Nomor: SPRIN/1841/VI/2025 Tanggal 4 Juni 2025.',
            'ekspektasi' => "Ukuran keberhasilan/ Indikator Kinerja individu, Target, dan Perspektif:\n- Pelaksanaan Jaga Stand dilaksanakan dengan baik sesuai jadwal dapat terlaksana sebesar 100%.\n- Tingkat kesesuaian materi pameran yang dijelaskan oleh pengunjung sebesar 90-100%.\n- Tingkat kesesuaian pelaksanaan kegiatan dengan perencanaan sebesar 90-100%."
        ]
    ];
    
    foreach($sample_a as $idx => $item) {
        $stmt = mysqli_prepare($kon, "INSERT INTO skp_hasil_kerja (kategori, uraian, ekspektasi, urutan) VALUES (?, ?, ?, ?)");
        $kategori = 'A';
        $urutan = $idx + 1;
        mysqli_stmt_bind_param($stmt, "sssi", $kategori, $item['uraian'], $item['ekspektasi'], $urutan);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    echo "Sample data inserted successfully!";
} else {
    echo "Data already exists. Total records: " . $row_hasil['total'];
}

mysqli_close($kon);
?>
