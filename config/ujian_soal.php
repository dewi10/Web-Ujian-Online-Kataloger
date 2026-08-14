<?php
/**
 * Bank soal bisa lebih besar; saat ujian hanya sejumlah ini yang ditampilkan per peserta (acak).
 */
define('UJIAN_JUMLAH_SOAL_TAMPIL', 50);

/**
 * Jumlah soal yang dikerjakan siswa (maks 50, atau seluruh bank jika kurang dari 50).
 */
function ujian_jumlah_soal_tampil(?int $total_bank = null): int
{
    if ($total_bank === null) {
        return UJIAN_JUMLAH_SOAL_TAMPIL;
    }
    return min(UJIAN_JUMLAH_SOAL_TAMPIL, max(0, $total_bank));
}

/**
 * Pilih subset id_soal acak (deterministik per seed_key).
 *
 * @param int[] $semua_id
 * @return int[]
 */
function ujian_pilih_id_soal(array $semua_id, int $limit, string $seed_key): array
{
    $ids = array_values(array_map('intval', $semua_id));
    sort($ids);
    if (count($ids) <= $limit) {
        return $ids;
    }
    $seed = crc32($seed_key);
    mt_srand($seed);
    shuffle($ids);
    mt_srand();
    return array_slice($ids, 0, $limit);
}
