<?php
function import_parse_pg_snip(string $s, int $max = 120): string
{
    $s = trim(preg_replace('/\s+/u', ' ', $s));
    if (function_exists('mb_substr')) {
        return mb_substr($s, 0, $max, 'UTF-8');
    }
    return substr($s, 0, $max);
}

/**
 * Parse + laporan blok yang gagal (supaya pengguna tahu bedanya dengan yang lolos).
 *
 * @return array{items: array, laporan: array<string, mixed>}
 */
function parse_bank_soal_pg_with_report(string $text): array
{
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    $text = preg_replace("/[\x{00A0}\x{202F}\x{2007}\x{FEFF}]/u", ' ', $text);
    $text = trim($text);

    $kunciByNo = [];
    if (preg_match_all('/^Jawaban\s*\(?\s*(\d+)\s*\)?\s*[:：]\s*([a-eA-E])(?:\s*\.\s*|\s+|$)/mu', $text, $jm, PREG_SET_ORDER)) {
        foreach ($jm as $m) {
            $kunciByNo[(int) $m[1]] = strtolower($m[2]);
        }
    }

    // Pecah sebelum baris yang diawali nomor soal (longgar: tidak wajib ada teks di baris yang sama)
    $parts = preg_split('/\n(?=\d{1,3}[\.\)]\s*)/u', $text);
    $out = [];
    $need = ['a', 'b', 'c', 'd', 'e'];
    $maxSamples = 25;

    $cntBukanSoal = 0;
    $cntValidasi = 0;
    $sampleBukan = [];
    $sampleValidasi = [];

    $blokTerhitung = 0;
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') {
            continue;
        }
        $blokTerhitung++;

        $parsed = parse_bank_soal_pg_one_block($part);
        if ($parsed === null) {
            $cntBukanSoal++;
            if (count($sampleBukan) < $maxSamples) {
                $sampleBukan[] = [
                    'cuplikan' => import_parse_pg_snip($part, 140),
                    'alasan' => 'Baris pertama blok ini bukan pola nomor soal (contoh: 5. Teks atau 5) Teks). Bisa jadi sisa teks gabungan dari Word, atau baris sebelumnya belum dipisah nomor.',
                ];
            }
            continue;
        }
        $no = $parsed['no'];
        if ($parsed['kunci'] === null && isset($kunciByNo[$no])) {
            $parsed['kunci'] = $kunciByNo[$no];
        }
        $missing = [];
        foreach ($need as $L) {
            if (empty(trim((string) ($parsed['opts'][$L] ?? '')))) {
                $missing[] = $L;
            }
        }
        $optsNorm = [
            'a' => trim((string) ($parsed['opts']['a'] ?? '')),
            'b' => trim((string) ($parsed['opts']['b'] ?? '')),
            'c' => trim((string) ($parsed['opts']['c'] ?? '')),
            'd' => trim((string) ($parsed['opts']['d'] ?? '')),
            'e' => trim((string) ($parsed['opts']['e'] ?? '')),
        ];
        $kunciVal = $parsed['kunci'];
        if ($kunciVal !== null && !in_array($kunciVal, $need, true)) {
            $kunciVal = null;
        }
        if ($kunciVal !== null && $optsNorm[$kunciVal] === '') {
            $kunciVal = null;
        }

        unset($parsed['no']);
        $out[] = [
            'soal' => trim(preg_replace("/\n{3,}/", "\n\n", $parsed['soal'])),
            'opts' => $optsNorm,
            'kunci' => $kunciVal,
        ];

        if ($missing !== [] || $kunciVal === null) {
            $cntValidasi++;
            if (count($sampleValidasi) < $maxSamples) {
                $info = [];
                if ($missing !== []) {
                    $info[] = 'Diimpor tanpa pilihan: ' . implode(', ', $missing);
                }
                if ($kunciVal === null) {
                    $info[] = 'Tanpa kunci (semua pilihan disimpan sebagai salah)';
                }
                $sampleValidasi[] = [
                    'nomor_di_file' => $no,
                    'cuplikan_soal' => import_parse_pg_snip((string) $parsed['soal'], 90),
                    'alasan' => implode('. ', $info),
                ];
            }
        }
    }

    $laporan = [
        'blok_terdeteksi' => $blokTerhitung,
        'lolos_parser' => count($out),
        'gagal_bukan_soal_jumlah' => $cntBukanSoal,
        'gagal_validasi_jumlah' => 0,
        'contoh_bukan_soal' => $sampleBukan,
        'contoh_peringatan' => $sampleValidasi,
        'peringatan_tidak_lengkap_jumlah' => $cntValidasi,
        'petunjuk' => 'Setiap blok diawali baris N. atau N) tetap diimpor. Pilihan a–e dan Jawaban bersifat opsional; yang kosong tidak disimpan. Tanpa kunci, penilaian otomatis memperlakukan semua pilihan sebagai salah.',
    ];

    return ['items' => $out, 'laporan' => $laporan];
}

/**
 * Parse teks bank soal PG format:
 * 1. Teks soal (bisa beberapa baris) — juga 1) teks
 * a. / a) pilihan … sampai e
 * Jawaban : a / Jawaban: b / Jawaban c. teks (format Word tanpa titik dua)
 * Di akhir dokumen boleh: Jawaban 1: a … Jawaban 50: b
 *
 * @return array<int, array{soal:string, opts:array<string,string>, kunci:?string}>
 */
function parse_bank_soal_pg(string $text): array
{
    return parse_bank_soal_pg_with_report($text)['items'];
}

/**
 * Satu blok dimulai baris "1. …" atau "1) …".
 *
 * @return ?array{no:int, soal:string, opts:array<string,string>, kunci:?string}
 */
function parse_bank_soal_pg_one_block(string $part): ?array
{
    $lines = explode("\n", $part);
    $first = trim((string) array_shift($lines));
    if ($first === '' || !preg_match('/^(\d+)[\.\)]\s*(.*)$/us', $first, $fm)) {
        return null;
    }
    $no = (int) $fm[1];
    $qText = $fm[2];
    while (trim((string) $qText) === '' && $lines !== []) {
        $qText = trim((string) array_shift($lines));
    }
    $opts = [];
    $kunci = null;
    $inQuestion = true;

    foreach ($lines as $raw) {
        $line = trim($raw);
        if ($line === '') {
            if ($inQuestion && $qText !== null) {
                $qText .= "\n";
            }
            continue;
        }
        if (preg_match('/^Jawaban\s*\(?\s*\d+\s*\)?\s*[:：]\s*[a-eA-E]/u', $line)) {
            continue;
        }
        // Jawaban: c  atau  Jawaban c. ... (titik setelah huruf wajib jika tanpa titik dua — hindari "Jawaban cerita")
        if (preg_match('/^Jawaban\s*[:：]\s*([a-eA-E])(?:\s*\.\s*|\s+|$)/u', $line, $m)) {
            $kunci = strtolower($m[1]);
            continue;
        }
        if (preg_match('/^Jawaban\s+([a-eA-E])\.\s*(.*)$/u', $line, $m)) {
            $kunci = strtolower($m[1]);
            continue;
        }
        $optLine = preg_replace('/^[\-\*\x{2022}\x{25CF}\x{00B7}]\s*/u', '', $line);
        if (preg_match('/^([a-e])[\.\)]\s*(.*)$/iu', $optLine, $m)) {
            $opts[strtolower($m[1])] = $m[2];
            $inQuestion = false;
            continue;
        }
        if ($inQuestion && $qText !== null) {
            $qText = rtrim((string) $qText) . "\n" . $line;
        }
    }

    return [
        'no' => $no,
        'soal' => (string) $qText,
        'opts' => $opts,
        'kunci' => $kunci,
    ];
}

/**
 * Ekstrak word/document.xml mentah dari .docx (tanpa ZipArchive), memakai unzip CLI jika ada.
 */
function extract_docx_document_xml_raw(string $path): string
{
    $path = realpath($path);
    if ($path === false || !is_readable($path)) {
        return '';
    }
    if (!function_exists('shell_exec')) {
        return '';
    }
    $p = escapeshellarg($path);
    $out = @shell_exec("unzip -p {$p} word/document.xml 2>/dev/null");
    if (is_string($out) && $out !== '') {
        return $out;
    }
    $out = @shell_exec("unzip -p {$p} Word/document.xml 2>/dev/null");
    return is_string($out) ? $out : '';
}

/**
 * Tanpa ext-dom: pecah paragraf dari XML dan baca w:numPr + w:t (cukup untuk bank soal).
 *
 * @return array<int, array{text:string, numId:?int, ilvl:?int}>
 */
function docx_paragraphs_meta_regex(string $xml): array
{
    $xml = preg_replace('/^\xEF\xBB\xBF/', '', $xml);
    $xml = preg_replace('/<w:tab[^>]*\/>/i', "\t", $xml);
    $xml = preg_replace('/<w:br[^>]*\/>/i', "\n", $xml);

    $paras = preg_split('/<\/w:p>/i', $xml);
    $rows = [];
    foreach ($paras as $chunk) {
        if (!preg_match_all('/<w:t(?:\s[^>]*)?>([^<]*)<\/w:t>/iu', $chunk, $m)) {
            $plain = trim(strip_tags($chunk));
            if ($plain === '') {
                continue;
            }
            $rows[] = ['text' => $plain, 'numId' => null, 'ilvl' => null];
            continue;
        }
        $text = implode('', $m[1]);
        $numId = null;
        $ilvl = null;
        if (preg_match('/<w:numPr\b/i', $chunk)) {
            if (preg_match('/<w:ilvl\b[^>]*\bw:val=[\"\'](\d+)[\"\']/i', $chunk, $im)) {
                $ilvl = (int) $im[1];
            } elseif (preg_match('/<w:ilvl\b[^/>]*\/>/i', $chunk)) {
                $ilvl = 0;
            }
            if (preg_match('/<w:numId\b[^>]*\bw:val=[\"\'](\d+)[\"\']/i', $chunk, $nm)) {
                $numId = (int) $nm[1];
            }
            if ($numId !== null && $ilvl === null) {
                $ilvl = 0;
            }
        }
        $rows[] = ['text' => $text, 'numId' => $numId, 'ilvl' => $ilvl];
    }
    return $rows;
}

/**
 * numId daftar Word untuk soal = yang paling banyak paragraf lvl 0 (bukan daftar pertama di dokumen).
 *
 * @param array<int, array{text:string, numId:?int, ilvl:?int}> $rows
 */
function docx_detect_primary_num_id_from_rows(array $rows): ?int
{
    $counts = [];
    foreach ($rows as $r) {
        if ($r['numId'] === null) {
            continue;
        }
        $ilvl = $r['ilvl'] !== null ? (int) $r['ilvl'] : 0;
        if ($ilvl !== 0) {
            continue;
        }
        $nid = (int) $r['numId'];
        $counts[$nid] = ($counts[$nid] ?? 0) + 1;
    }
    if ($counts === []) {
        return null;
    }
    $bestId = null;
    $bestC = 0;
    foreach ($counts as $nid => $c) {
        if ($c > $bestC) {
            $bestC = $c;
            $bestId = $nid;
        }
    }
    return $bestId;
}

/**
 * Sisipkan "N. " untuk daftar Word (regex) — sama logika dengan versi DOM.
 */
function docx_apply_list_prefix_to_rows(array $rows): array
{
    $primaryNumId = docx_detect_primary_num_id_from_rows($rows);
    if ($primaryNumId === null) {
        return array_map(static function ($r) {
            return $r['text'];
        }, $rows);
    }
    $counter = 0;
    $out = [];
    foreach ($rows as $r) {
        $buf = $r['text'];
        $t = trim($buf);
        $prefix = '';
        $ilvlR = $r['ilvl'] !== null ? (int) $r['ilvl'] : 0;
        if ($r['numId'] !== null && $ilvlR === 0 && (int) $r['numId'] === $primaryNumId) {
            $counter++;
            if ($t !== '' && !preg_match('/^\d+[\.\)]\s/u', $t) && !preg_match('/^[a-eA-E][\.\)]\s/u', $t)) {
                $prefix = $counter . '. ';
            }
        }
        if ($prefix === '' && $t === '') {
            continue;
        }
        $out[] = $prefix . $buf;
    }
    return $out;
}

/**
 * Fallback: ubah document.xml jadi teks (regex + penomoran daftar Word).
 */
function docx_document_xml_to_plain_regex(string $xml): string
{
    $xml = preg_replace('/^\xEF\xBB\xBF/', '', $xml);
    $xml = preg_replace('/<w:tab[^>]*\/>/i', "\t", $xml);
    $xml = preg_replace('/<w:br[^>]*\/>/i', "\n", $xml);

    $paras = preg_split('/<\/w:p>/i', $xml);
    $lines = [];
    foreach ($paras as $chunk) {
        if (preg_match_all('/<w:t(?:\s[^>]*)?>([^<]*)<\/w:t>/iu', $chunk, $m)) {
            $lines[] = implode('', $m[1]);
        } else {
            $plain = trim(strip_tags($chunk));
            if ($plain !== '') {
                $lines[] = $plain;
            }
        }
    }
    $text = implode("\n", $lines);
    if (trim($text) === '') {
        $xml = preg_replace('/<\/w:p>/i', "\n", $xml);
        $text = strip_tags($xml);
    }
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if (function_exists('mb_check_encoding') && function_exists('mb_convert_encoding')
        && !mb_check_encoding($text, 'UTF-8')) {
        $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252, ISO-8859-1');
    }
    return $text;
}

function docx_document_xml_to_plain_regex_listfix(string $xml): string
{
    $rows = docx_paragraphs_meta_regex($xml);
    if ($rows === []) {
        return docx_document_xml_to_plain_regex($xml);
    }
    $lines = docx_apply_list_prefix_to_rows($rows);
    $text = implode("\n", $lines);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if (function_exists('mb_check_encoding') && function_exists('mb_convert_encoding')
        && !mb_check_encoding($text, 'UTF-8')) {
        $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252, ISO-8859-1');
    }
    return $text;
}

/** Namespace utama WordprocessingML */
function docx_w_ns(): string
{
    return 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
}

function docx_w_val(DOMElement $el, string $localName = 'val'): string
{
    $ns = docx_w_ns();
    $v = $el->getAttributeNS($ns, $localName);
    if ($v !== '') {
        return $v;
    }
    return $el->getAttribute('w:' . $localName);
}

/**
 * Isi satu paragraf w:p (teks + tab + baris baru dari w:br).
 */
function docx_paragraph_inner_text(DOMXPath $xpath, DOMElement $p): string
{
    $nodes = $xpath->query('.//w:t | .//w:tab | .//w:br', $p);
    if ($nodes === false) {
        return '';
    }
    $buf = '';
    foreach ($nodes as $n) {
        if (!($n instanceof DOMElement)) {
            continue;
        }
        $ln = $n->localName;
        if ($ln === 't') {
            $buf .= $n->textContent;
        } elseif ($ln === 'tab') {
            $buf .= "\t";
        } elseif ($ln === 'br') {
            $buf .= "\n";
        }
    }
    return $buf;
}

/**
 * Ubah Office WordprocessingML jadi teks; tambahkan "N. " untuk paragraf daftar bernomor Word
 * (nomor asli tidak ada di XML, hanya di w:numPr).
 */
function docx_document_xml_to_plain(string $xml): string
{
    $xml = preg_replace('/^\xEF\xBB\xBF/', '', $xml);
    if (!class_exists('DOMDocument') || !class_exists('DOMXPath')) {
        return docx_document_xml_to_plain_regex_listfix($xml);
    }
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    if (!@$dom->loadXML($xml)) {
        return docx_document_xml_to_plain_regex_listfix($xml);
    }
    $xpath = new DOMXPath($dom);
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    $paras = $xpath->query('//w:p');
    if ($paras === false || $paras->length === 0) {
        return docx_document_xml_to_plain_regex_listfix($xml);
    }

    $numIdCounts = [];
    for ($i = 0; $i < $paras->length; $i++) {
        $p = $paras->item($i);
        if (!($p instanceof DOMElement)) {
            continue;
        }
        $numPr = $xpath->query('w:pPr/w:numPr', $p)->item(0);
        if (!($numPr instanceof DOMElement)) {
            continue;
        }
        $ilvlEl = $xpath->query('w:ilvl', $numPr)->item(0);
        $ilvlVal = ($ilvlEl instanceof DOMElement) ? (int) docx_w_val($ilvlEl) : 0;
        if ($ilvlVal !== 0) {
            continue;
        }
        $numIdEl = $xpath->query('w:numId', $numPr)->item(0);
        if (!($numIdEl instanceof DOMElement)) {
            continue;
        }
        $nid = (int) docx_w_val($numIdEl);
        $numIdCounts[$nid] = ($numIdCounts[$nid] ?? 0) + 1;
    }
    $primaryNumId = null;
    $bestCnt = 0;
    foreach ($numIdCounts as $nid => $c) {
        if ($c > $bestCnt) {
            $bestCnt = $c;
            $primaryNumId = $nid;
        }
    }

    $counter = 0;
    $lines = [];
    for ($i = 0; $i < $paras->length; $i++) {
        $p = $paras->item($i);
        if (!($p instanceof DOMElement)) {
            continue;
        }
        $buf = docx_paragraph_inner_text($xpath, $p);
        $t = trim($buf);
        $prefix = '';
        if ($primaryNumId !== null) {
            $numPr = $xpath->query('w:pPr/w:numPr', $p)->item(0);
            if ($numPr instanceof DOMElement) {
                $ilvlEl = $xpath->query('w:ilvl', $numPr)->item(0);
                $ilvlVal = ($ilvlEl instanceof DOMElement) ? (int) docx_w_val($ilvlEl) : 0;
                $numIdEl = $xpath->query('w:numId', $numPr)->item(0);
                $numId = ($numIdEl instanceof DOMElement) ? (int) docx_w_val($numIdEl) : 0;
                if ($numId === $primaryNumId && $ilvlVal === 0) {
                    $counter++;
                    if ($t !== '' && !preg_match('/^\d+[\.\)]\s/u', $t) && !preg_match('/^[a-eA-E][\.\)]\s/u', $t)) {
                        $prefix = $counter . '. ';
                    }
                }
            }
        }
        if ($prefix === '' && $t === '') {
            continue;
        }
        $lines[] = $prefix . $buf;
    }

    $text = implode("\n", $lines);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    if (function_exists('mb_check_encoding') && function_exists('mb_convert_encoding')
        && !mb_check_encoding($text, 'UTF-8')) {
        $text = mb_convert_encoding($text, 'UTF-8', 'Windows-1252, ISO-8859-1');
    }
    return $text;
}

/**
 * Ambil isi word/document.xml dari .docx (ZIP) — toleran path & huruf besar/kecil.
 */
function extract_text_docx(string $path): string
{
    if (!class_exists('ZipArchive')) {
        $raw = extract_docx_document_xml_raw($path);
        return $raw !== '' ? docx_document_xml_to_plain($raw) : '';
    }
    $pathsToTry = [$path];
    $tmpCopy = tempnam(sys_get_temp_dir(), 'impdocx_');
    if ($tmpCopy !== false && is_readable($path) && @copy($path, $tmpCopy)) {
        $pathsToTry[] = $tmpCopy;
    }

    $xml = false;
    foreach ($pathsToTry as $tryPath) {
        $xml = false;
        $zip = new ZipArchive();
        if ($zip->open($tryPath) !== true) {
            continue;
        }
        $n = $zip->numFiles;
        for ($i = 0; $i < $n; $i++) {
            $name = $zip->getNameIndex($i);
            if ($name === false) {
                continue;
            }
            $norm = str_replace('\\', '/', $name);
            if (preg_match('#(^|/)word/document\.xml$#i', $norm)) {
                $xml = $zip->getFromIndex($i);
                break;
            }
        }
        if ($xml === false) {
            foreach (['word/document.xml', 'Word/document.xml'] as $entry) {
                $xml = $zip->getFromName($entry);
                if ($xml !== false && $xml !== '') {
                    break;
                }
            }
        }
        $zip->close();
        if ($xml !== false && $xml !== '') {
            break;
        }
        $xml = false;
    }
    if ($tmpCopy !== false && is_file($tmpCopy)) {
        @unlink($tmpCopy);
    }
    if ($xml === false || $xml === '') {
        $raw = extract_docx_document_xml_raw($path);
        return $raw !== '' ? docx_document_xml_to_plain($raw) : '';
    }

    return docx_document_xml_to_plain($xml);
}

/**
 * Ekstrak teks dari .doc (Word 97–2003) lewat beberapa backend CLI.
 * Urutan: LibreOffice → Pandoc → antiword → catdoc.
 */
function extract_doc_via_libreoffice(string $path): string
{
    if (!function_exists('shell_exec')) {
        return '';
    }
    $outDir = sys_get_temp_dir() . '/docimp_' . str_replace('.', '', uniqid('', true));
    if (!@mkdir($outDir, 0700)) {
        return '';
    }
    $in = escapeshellarg($path);
    $dirEsc = escapeshellarg($outDir);
    $content = '';
    foreach (['libreoffice', 'soffice'] as $bin) {
        $which = trim((string) @shell_exec('command -v ' . escapeshellarg($bin) . ' 2>/dev/null'));
        if ($which === '') {
            continue;
        }
        @shell_exec($bin . ' --headless --nologo --nofirststartwizard --convert-to txt:Text ' . $in . ' --outdir ' . $dirEsc . ' 2>/dev/null');
        foreach (array_merge(glob($outDir . '/*.txt') ?: [], glob($outDir . '/*.TXT') ?: []) as $tf) {
            if (!is_readable($tf)) {
                continue;
            }
            $raw = file_get_contents($tf);
            if ($raw !== false && strlen(trim($raw)) > 10) {
                $content = $raw;
                break 2;
            }
        }
    }
    foreach (glob($outDir . '/*') ?: [] as $f) {
        @unlink($f);
    }
    @rmdir($outDir);
    return $content;
}

function extract_text_doc_legacy(string $path): string
{
    if (!function_exists('shell_exec')) {
        return '';
    }
    $path = realpath($path);
    if ($path === false || !is_readable($path)) {
        return '';
    }

    $cukup = static function (?string $out): bool {
        return is_string($out) && strlen(trim($out)) > 10;
    };

    $lo = extract_doc_via_libreoffice($path);
    if ($cukup($lo)) {
        return $lo;
    }

    foreach (['pandoc -f doc -t plain', 'pandoc -f msword -t plain', 'pandoc -t plain'] as $tpl) {
        $pandoc = @shell_exec($tpl . ' ' . escapeshellarg($path) . ' 2>/dev/null');
        if ($cukup($pandoc)) {
            return $pandoc;
        }
    }

    foreach (['antiword', 'catdoc'] as $bin) {
        $cmd = $bin . ' ' . escapeshellarg($path) . ' 2>/dev/null';
        $out = @shell_exec($cmd);
        if ($cukup($out)) {
            return $out;
        }
    }

    return '';
}
