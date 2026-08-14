<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_soal'])) {
    include '../../../config/database.php';

    $id_ujian = isset($_POST['id_ujian']) ? (int)$_POST['id_ujian'] : 0;
    $tipe_soal = isset($_POST['tipe_soal']) ? trim($_POST['tipe_soal']) : '';

    if ($id_ujian <= 0 || ($tipe_soal !== 'pg' && $tipe_soal !== 'essay')) {
        header("Location:../../../index.php?page=input-soal&id={$id_ujian}&import=gagal&pesan=" . urlencode('Parameter import tidak valid.'));
        exit;
    }

    if (!isset($_FILES['file_import']) || $_FILES['file_import']['error'] !== UPLOAD_ERR_OK) {
        header("Location:../../../index.php?page=input-soal&id={$id_ujian}&import=gagal&pesan=" . urlencode('File CSV gagal diupload.'));
        exit;
    }

    $tmpName = $_FILES['file_import']['tmp_name'];
    $handle = fopen($tmpName, 'r');
    if (!$handle) {
        header("Location:../../../index.php?page=input-soal&id={$id_ujian}&import=gagal&pesan=" . urlencode('File CSV tidak dapat dibaca.'));
        exit;
    }

    mysqli_query($kon, "START TRANSACTION");

    $qMax = mysqli_query($kon, "SELECT COALESCE(MAX(id_soal),0) AS max_id FROM soal");
    $rMax = mysqli_fetch_assoc($qMax);
    $nextId = (int)$rMax['max_id'];

    $isFirstRow = true;
    $inserted = 0;
    $failed = false;

    while (($row = fgetcsv($handle, 0, ',')) !== false) {
        if (!$row || (count($row) === 1 && trim($row[0]) === '')) {
            continue;
        }

        if ($isFirstRow) {
            $firstCol = strtolower(trim($row[0]));
            if ($firstCol === 'soal' || $firstCol === 'pertanyaan' || $firstCol === 'question') {
                $isFirstRow = false;
                continue;
            }
        }
        $isFirstRow = false;

        $teksSoal = mysqli_real_escape_string($kon, trim($row[0]));
        if ($teksSoal === '') {
            continue;
        }

        $nextId++;
        $kode_soal = 'S' . sprintf('%03s', $nextId);
        $tipe = ($tipe_soal === 'pg') ? 1 : 2;

        $sqlSoal = "INSERT INTO soal (kode_soal, soal, gambar, id_ujian, tipe)
                    VALUES ('{$kode_soal}', '{$teksSoal}', '', '{$id_ujian}', '{$tipe}')";
        $okSoal = mysqli_query($kon, $sqlSoal);

        if (!$okSoal) {
            $failed = true;
            break;
        }

        $id_soal_baru = mysqli_insert_id($kon);

        if ($tipe_soal === 'pg') {
            // Format: soal,A,B,C,D,E,kunci
            $pilihan = [
                1 => isset($row[1]) ? trim($row[1]) : '',
                2 => isset($row[2]) ? trim($row[2]) : '',
                3 => isset($row[3]) ? trim($row[3]) : '',
                4 => isset($row[4]) ? trim($row[4]) : '',
                5 => isset($row[5]) ? trim($row[5]) : ''
            ];

            $kunciRaw = isset($row[6]) ? strtoupper(trim($row[6])) : '';
            $mapHuruf = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5];
            if (isset($mapHuruf[$kunciRaw])) {
                $kunci = $mapHuruf[$kunciRaw];
            } else {
                $kunci = (int)$kunciRaw;
            }
            if ($kunci < 1 || $kunci > 5) {
                $kunci = 1;
            }

            for ($i = 1; $i <= 5; $i++) {
                $teksPilihan = mysqli_real_escape_string($kon, $pilihan[$i]);
                $jawaban = ($i === $kunci) ? 1 : 0;
                $okJawaban = mysqli_query($kon, "INSERT INTO jawaban (pilihan, jawaban, id_soal)
                            VALUES ('{$teksPilihan}', '{$jawaban}', '{$id_soal_baru}')");
                if (!$okJawaban) {
                    $failed = true;
                    break;
                }
            }

            if ($failed) {
                break;
            }
        }

        $inserted++;
    }

    fclose($handle);

    if ($failed || $inserted === 0) {
        mysqli_query($kon, "ROLLBACK");
        $msg = $failed ? mysqli_error($kon) : 'Tidak ada baris valid untuk diimport.';
        header("Location:../../../index.php?page=input-soal&id={$id_ujian}&import=gagal&pesan=" . urlencode($msg));
        exit;
    }

    mysqli_query($kon, "COMMIT");
    header("Location:../../../index.php?page=input-soal&id={$id_ujian}&import=berhasil");
    exit;
}

header("Location:../../../index.php?page=ujian");
exit;
