<?php
    $host = "localhost";
    $user = "your_db_user";
    $password = "your_db_password";
    $db = "db_ujian_online";

    $kon = mysqli_connect($host, $user, $password, $db);
    if (!$kon) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
?>
