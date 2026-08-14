<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

function json_fail(string $msg, int $code = 400): void
{
    // HTTP 200 + ok:false agar jQuery (dataType: json) memakai callback success dan menampilkan message.
    // Kode asli tetap dikirim di body untuk logging klien bila perlu.
    if ($code === 400 || $code === 404) {
        http_response_code(200);
    } else {
        http_response_code($code);
    }
    echo json_encode(['ok' => false, 'message' => $msg, 'status' => $code], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Normalisasi isi file .txt ke UTF-8 (aman jika mbstring tidak terpasang).
 */
function import_pg_normalize_txt_to_utf8(string $raw): string
{
    $text = $raw;
    if (strncmp($text, "\xEF\xBB\xBF", 3) === 0) {
        $text = substr($text, 3);
    }
    if (strncmp($text, "\xFF\xFE", 2) === 0 && function_exists('iconv')) {
        $text = (string) iconv('UTF-16LE', 'UTF-8//IGNORE', substr($text, 2));
    } elseif (strncmp($text, "\xFE\xFF", 2) === 0 && function_exists('iconv')) {
        $text = (string) iconv('UTF-16BE', 'UTF-8//IGNORE', substr($text, 2));
    }
    if (function_exists('mb_check_encoding') && function_exists('mb_convert_encoding')) {
        if (!mb_check_encoding($text, 'UTF-8')) {
            foreach (['Windows-1252', 'ISO-8859-1', 'CP1252'] as $enc) {
                $t = @mb_convert_encoding($text, 'UTF-8', $enc);
                if ($t !== false && mb_check_encoding($t, 'UTF-8')) {
                    $text = $t;
                    break;
                }
            }
        }
    } elseif (function_exists('iconv')) {
        if (!preg_match('//u', $text)) {
            $conv = @iconv('Windows-1252', 'UTF-8//IGNORE', $text);
            if ($conv !== false) {
                $text = $conv;
            }
        }
    }
    if (function_exists('iconv')) {
        $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $text);
        if ($clean !== false) {
            $text = $clean;
        }
    }
    return $text;
}

function import_pg_norm(string $s): string
{
    if ($s === '') {
        return '';
    }
    $u = @preg_replace('/\s+/u', ' ', $s);
    if ($u === null) {
        $u = preg_replace('/\s+/', ' ', $s);
    }
    return trim((string) $u);
}

/** Sidik jari soal PG untuk deteksi duplikat (teks + 5 pilihan + kunci). */
function import_pg_fingerprint_item(array $item): string
{
    $parts = [import_pg_norm($item['soal'])];
    foreach (['a', 'b', 'c', 'd', 'e'] as $L) {
        $parts[] = import_pg_norm((string) ($item['opts'][$L] ?? ''));
    }
    $parts[] = (string) ($item['kunci'] ?? '');
    return sha1(implode("\x1e", $parts));
}

/**
 * @return array<string, true>
 */
function import_pg_load_existing_fps(mysqli $kon, string $id_ujian_esc): array
{
    $set = [];
    $q = mysqli_query($kon, "SELECT id_soal, soal FROM soal WHERE id_ujian='$id_ujian_esc' AND tipe='1'");
    if (!$q) {
        return $set;
    }
    $order = ['a', 'b', 'c', 'd', 'e'];
    while ($row = mysqli_fetch_assoc($q)) {
        $id = (int) $row['id_soal'];
        $qj = mysqli_query($kon, "SELECT pilihan, jawaban FROM jawaban WHERE id_soal=$id ORDER BY id_jawaban ASC");
        if (!$qj) {
            continue;
        }
        $opts = array_fill_keys($order, '');
        $kunci = null;
        $i = 0;
        while ($j = mysqli_fetch_assoc($qj)) {
            if ($i >= 5) {
                break;
            }
            $L = $order[$i];
            $opts[$L] = $j['pilihan'];
            if ((int) $j['jawaban'] === 1) {
                $kunci = $L;
            }
            $i++;
        }
        $item = ['soal' => $row['soal'], 'opts' => $opts, 'kunci' => $kunci];
        $set[import_pg_fingerprint_item($item)] = true;
    }
    return $set;
}

/**
 * Hapus semua soal PG ujian + jawaban bank + baris hasil siswa yang merujuk soal itu.
 * Mengembalikan jumlah soal yang dihapus, atau -1 jika query gagal.
 */
function import_pg_hapus_semua_soal_pg(mysqli $kon, string $id_ujian_esc): int
{
    $q = mysqli_query($kon, "SELECT id_soal FROM soal WHERE id_ujian='$id_ujian_esc' AND tipe='1'");
    if (!$q) {
        return -1;
    }
    $ids = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $ids[] = (int) $r['id_soal'];
    }
    if ($ids === []) {
        return 0;
    }
    $in = implode(',', $ids);
    if (!mysqli_query($kon, "DELETE FROM hasil WHERE id_soal IN ($in)")) {
        return -1;
    }
    if (!mysqli_query($kon, "DELETE FROM jawaban WHERE id_soal IN ($in)")) {
        return -1;
    }
    if (!mysqli_query($kon, "DELETE FROM soal WHERE id_soal IN ($in)")) {
        return -1;
    }
    return count($ids);
}

$id_guru = $_SESSION['id_guru'] ?? null;
if (!$id_guru) {
    json_fail('Sesi tidak valid.', 403);
}

include '../../../config/database.php';
require_once __DIR__ . '/import-soal-parse.php';

$id_ujian = isset($_POST['id_ujian']) ? (int) $_POST['id_ujian'] : 0;
if ($id_ujian <= 0) {
    json_fail('ID ujian tidak valid.');
}

$id_guru_esc = mysqli_real_escape_string($kon, (string) $id_guru);
$id_ujian_esc = mysqli_real_escape_string($kon, (string) $id_ujian);
$qu = mysqli_query($kon, "SELECT id_ujian, tipe_soal FROM ujian WHERE id_ujian='$id_ujian_esc' AND id_guru='$id_guru_esc' LIMIT 1");
$ujian = mysqli_fetch_assoc($qu);
if (!$ujian) {
    json_fail('Ujian tidak ditemukan.');
}
if ((int) $ujian['tipe_soal'] !== 1) {
    json_fail('Hanya ujian pilihan ganda yang bisa di-import.');
}

if (empty($_FILES['bank_soal']['tmp_name']) || !is_uploaded_file($_FILES['bank_soal']['tmp_name'])) {
    json_fail('File tidak diunggah.');
}

$max = 8 * 1024 * 1024;
if ((int) $_FILES['bank_soal']['size'] > $max) {
    json_fail('File terlalu besar (maks. 8 MB).');
}

$name = $_FILES['bank_soal']['name'];
$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
if (!in_array($ext, ['txt', 'doc', 'docx'], true)) {
    json_fail('Ekstensi tidak didukung. Gunakan .txt, .doc, atau .docx.');
}

$tmp = $_FILES['bank_soal']['tmp_name'];
$text = '';

if ($ext === 'txt') {
    $text = file_get_contents($tmp);
    if ($text === false) {
        json_fail('Gagal membaca file teks.');
    }
    $text = import_pg_normalize_txt_to_utf8($text);
} elseif ($ext === 'docx') {
    $text = extract_text_docx($tmp);
    if (trim($text) === '') {
        json_fail('Gagal membaca .docx. Pastikan file asli disimpan “Save As” Word/Pages ke .docx (bukan rename dari .doc), atau gunakan .txt. Pastikan ekstensi php-zip (ZipArchive) aktif di server.');
    }
} else {
    // File upload PHP sering tanpa ekstensi (.doc); LibreOffice/Pandoc butuh nama .doc
    $workDoc = sys_get_temp_dir() . '/impbank_' . preg_replace('/\W+/', '', uniqid('', true)) . '.doc';
    if (@copy($tmp, $workDoc)) {
        $text = extract_text_doc_legacy($workDoc);
        @unlink($workDoc);
    } else {
        $text = extract_text_doc_legacy($tmp);
    }
    if (trim($text) === '') {
        json_fail('Gagal membaca .doc. Pasang salah satu di server: LibreOffice (paket libreoffice-writer / perintah soffice), Pandoc, antiword, atau catdoc. Atau simpan sebagai .docx / .txt.');
    }
}

$pr = parse_bank_soal_pg_with_report($text);
$parsed = $pr['items'];
$laporan = $pr['laporan'];
if (count($parsed) === 0) {
    http_response_code(200);
    echo json_encode([
        'ok' => false,
        'message' => 'Tidak ada soal yang lolos. Buka detail di bawah untuk melihat contoh yang ditolak.',
        'status' => 400,
        'laporan' => $laporan,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$import_mode = isset($_POST['import_mode']) ? (string) $_POST['import_mode'] : 'tambah';
if (!in_array($import_mode, ['tambah', 'timpa', 'skip_sama'], true)) {
    $import_mode = 'tambah';
}

mysqli_query($kon, 'START TRANSACTION');

$deleted_soal = 0;
$skipped = 0;
if ($import_mode === 'timpa') {
    $deleted_soal = import_pg_hapus_semua_soal_pg($kon, $id_ujian_esc);
    if ($deleted_soal < 0) {
        mysqli_query($kon, 'ROLLBACK');
        json_fail('Gagal menghapus soal lama: ' . mysqli_error($kon));
    }
}

$existingFp = [];
if ($import_mode === 'skip_sama') {
    $existingFp = import_pg_load_existing_fps($kon, $id_ujian_esc);
}

$imported = 0;
$order = ['a', 'b', 'c', 'd', 'e'];

foreach ($parsed as $item) {
    $fp = null;
    if ($import_mode === 'skip_sama') {
        $fp = import_pg_fingerprint_item($item);
        if (isset($existingFp[$fp])) {
            $skipped++;
            continue;
        }
    }

    $qmax = mysqli_query($kon, 'SELECT COALESCE(MAX(id_soal), 0) AS m FROM soal');
    $rmax = mysqli_fetch_assoc($qmax);
    $next_id = (int) $rmax['m'] + 1;
    $kode_soal = 'S' . sprintf('%03d', $next_id);

    $soal_esc = mysqli_real_escape_string($kon, $item['soal']);
    $sql_soal = "INSERT INTO soal (kode_soal, soal, id_ujian, gambar, tipe) VALUES (
        '$kode_soal', '$soal_esc', '$id_ujian_esc', '', '1')";
    if (!mysqli_query($kon, $sql_soal)) {
        mysqli_query($kon, 'ROLLBACK');
        json_fail('Gagal simpan soal: ' . mysqli_error($kon));
    }
    $id_soal = (int) mysqli_insert_id($kon);

    $kunci = $item['kunci'] ?? null;
    $adaPilihan = false;
    foreach ($order as $L) {
        $teksPil = trim((string) ($item['opts'][$L] ?? ''));
        if ($teksPil === '') {
            continue;
        }
        $adaPilihan = true;
        $pil = mysqli_real_escape_string($kon, $teksPil);
        $benar = ($kunci !== null && $L === $kunci) ? 1 : 0;
        $sql_j = "INSERT INTO jawaban (pilihan, jawaban, id_soal) VALUES ('$pil', '$benar', '$id_soal')";
        if (!mysqli_query($kon, $sql_j)) {
            mysqli_query($kon, 'ROLLBACK');
            json_fail('Gagal simpan jawaban: ' . mysqli_error($kon));
        }
    }
    if (!$adaPilihan) {
        $sql_j = "INSERT INTO jawaban (pilihan, jawaban, id_soal) VALUES ('(belum ada pilihan)', 0, '$id_soal')";
        if (!mysqli_query($kon, $sql_j)) {
            mysqli_query($kon, 'ROLLBACK');
            json_fail('Gagal simpan jawaban: ' . mysqli_error($kon));
        }
    }
    if ($import_mode === 'skip_sama' && $fp !== null) {
        $existingFp[$fp] = true;
    }
    $imported++;
}

mysqli_query($kon, 'COMMIT');
echo json_encode([
    'ok' => true,
    'imported' => $imported,
    'skipped' => $skipped,
    'deleted_soal' => $deleted_soal,
    'laporan' => $laporan,
], JSON_UNESCAPED_UNICODE);
