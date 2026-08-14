<?php
session_start();
include "../../../config/database.php";

// Validasi login admin
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$username = $_SESSION['username'];

// Cek apakah user adalah admin
$cek = mysqli_query($kon, "SELECT * FROM admin WHERE username='".$username."' LIMIT 1");
if (mysqli_num_rows($cek) < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Hanya admin yang bisa edit']);
    exit;
}

// Cek apakah tabel sudah ada, jika belum buat tabel
$check_table = mysqli_query($kon, "SHOW TABLES LIKE 'pola_distribusi'");
if (mysqli_num_rows($check_table) == 0) {
    $create_table = "CREATE TABLE `pola_distribusi` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(100) NOT NULL,
        `kategori` varchar(50) NOT NULL,
        `nilai` int(11) NOT NULL DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username_kategori` (`username`, `kategori`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
    
    mysqli_query($kon, $create_table);
}

// Ambil data dari POST
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['kategori']) || !isset($data['nilai'])) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    exit;
}

$kategori = mysqli_real_escape_string($kon, $data['kategori']);
$nilai = (int) $data['nilai'];

// Cek apakah data sudah ada
$check = mysqli_query($kon, "SELECT * FROM pola_distribusi WHERE username='default' AND kategori='$kategori'");

if (mysqli_num_rows($check) > 0) {
    // Update data
    $query = "UPDATE pola_distribusi SET nilai=$nilai WHERE username='default' AND kategori='$kategori'";
} else {
    // Insert data baru
    $query = "INSERT INTO pola_distribusi (username, kategori, nilai) VALUES ('default', '$kategori', $nilai)";
}

if (mysqli_query($kon, $query)) {
    echo json_encode(['status' => 'success', 'message' => 'Data berhasil disimpan']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . mysqli_error($kon)]);
}
?>
