<?php
    session_start();

    // Siswa: ujian masih berjalan → kembali ke halaman soal (bisa paksa: logout.php?force=1)
    if (isset($_SESSION['id_siswa']) && empty($_GET['force'])) {
        include __DIR__ . '/config/database.php';
        require_once __DIR__ . '/config/siswa_ujian_aktif.php';
        $aktif = siswa_id_ujian_berlangsung($kon, (string) $_SESSION['id_siswa']);
        if ($aktif > 0) {
            header('Location: pages/siswa/hasil/set-default.php?id=' . (int) $aktif . '&pesan=ujian-aktif');
            exit;
        }
    }

    if (isset($_SESSION['id_guru'])) {
        $_SESSION['id_guru'] = '';
        $_SESSION['kode_guru'] = '';
        unset($_SESSION['id_guru']);
        unset($_SESSION['kode_guru']);
    }

    session_unset();
    session_destroy();

    header('Location: login.php');
?>
