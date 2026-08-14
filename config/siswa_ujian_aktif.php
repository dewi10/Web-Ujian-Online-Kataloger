<?php
/**
 * Ujian yang sedang dalam jadwal untuk siswa tertentu.
 * Hanya ujian yang id_kelas-nya sama dengan kategori/kelas siswa (hindari nyasar U104 saat siswa Pertama).
 */

function siswa_ujian_aktif_where_sql(mysqli $kon, string $id_siswa, string $alias_ujian = 'u'): string
{
    $id_siswa_esc = mysqli_real_escape_string($kon, (string) $id_siswa);
    $a = preg_replace('/[^a-z_]/', '', $alias_ujian) ?: 'u';

    $aktif_sql = '';
    $cek_col = mysqli_query($kon, "SHOW COLUMNS FROM ujian LIKE 'status_aktif'");
    if ($cek_col && mysqli_num_rows($cek_col) > 0) {
        $aktif_sql = " AND {$a}.status_aktif='1'";
    }

    return "NOW() >= TIMESTAMP({$a}.tanggal, {$a}.jam)
        AND NOW() <= DATE_ADD(TIMESTAMP({$a}.tanggal, {$a}.jam), INTERVAL {$a}.waktu MINUTE)
        {$aktif_sql}
        AND NOT EXISTS (
            SELECT 1 FROM riwayat r
            WHERE r.id_ujian = {$a}.id_ujian AND r.id_siswa = '$id_siswa_esc'
            AND r.tanggal >= {$a}.tanggal
        )";
}

/**
 * @return int id_ujian atau 0
 */
function siswa_id_ujian_berlangsung(mysqli $kon, $id_siswa): int
{
    date_default_timezone_set('Asia/Jakarta');
    $id_siswa_esc = mysqli_real_escape_string($kon, (string) $id_siswa);
    $where_jadwal = siswa_ujian_aktif_where_sql($kon, $id_siswa_esc, 'u');

    $sql = "SELECT u.id_ujian FROM ujian u
        INNER JOIN peserta p ON p.id_ujian = u.id_ujian AND p.id_siswa = '$id_siswa_esc'
        INNER JOIN siswa s ON s.id_siswa = '$id_siswa_esc' AND s.id_kelas = u.id_kelas
        WHERE $where_jadwal
        ORDER BY u.tanggal ASC, u.jam ASC, u.id_ujian ASC
        LIMIT 1";

    $q = mysqli_query($kon, $sql);
    if (!$q || mysqli_num_rows($q) === 0) {
        return 0;
    }
    $row = mysqli_fetch_assoc($q);
    return (int) $row['id_ujian'];
}

/**
 * Cek apakah siswa boleh mengakses ujian ini sekarang (peserta + kelas cocok + jadwal).
 */
function siswa_boleh_ujian_berlangsung(mysqli $kon, $id_siswa, $id_ujian): bool
{
    $id_siswa_esc = mysqli_real_escape_string($kon, (string) $id_siswa);
    $id_ujian_esc = mysqli_real_escape_string($kon, (string) $id_ujian);
    $where_jadwal = siswa_ujian_aktif_where_sql($kon, $id_siswa_esc, 'u');

    $sql = "SELECT 1 FROM ujian u
        INNER JOIN peserta p ON p.id_ujian = u.id_ujian AND p.id_siswa = '$id_siswa_esc'
        INNER JOIN siswa s ON s.id_siswa = '$id_siswa_esc' AND s.id_kelas = u.id_kelas
        WHERE u.id_ujian = '$id_ujian_esc' AND $where_jadwal
        LIMIT 1";

    $q = mysqli_query($kon, $sql);
    return ($q && mysqli_num_rows($q) > 0);
}
