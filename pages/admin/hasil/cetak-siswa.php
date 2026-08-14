<?php
    session_start();
    require('../../../vendor/fpdf/fpdf.php');
    include '../../../config/database.php';

    $username = $_SESSION['username'];
    $cek_guru = mysqli_query($kon, "select * from admin where username='".$username."' limit 1");
    if (mysqli_num_rows($cek_guru) < 1) {
        die("<center><h5>Tidak memiliki hak akses</h5></center>");
    }

    $query   = mysqli_query($kon, "select * from aplikasi limit 1");
    $row_app = mysqli_fetch_array($query);

    $id_ujian = addslashes(trim($_GET['id_ujian']));
    $id_siswa = addslashes(trim($_GET['id_siswa']));

    $sql = "select s.*, k.nama_kelas, u.judul, u.tipe_soal, u.tanggal, u.nilai_kelulusan,
                   m.nama_mapel, g.nama_guru
            from siswa s
            inner join kelas k on k.id_kelas = s.id_kelas
            inner join ujian u on u.id_kelas = k.id_kelas
            inner join mapel m on m.id_mapel = u.id_mapel
            inner join guru g on g.id_guru = u.id_guru
            where u.id_ujian = '$id_ujian' and s.id_siswa = '$id_siswa'
            limit 1";

    $hasil = mysqli_query($kon, $sql);
    if (mysqli_num_rows($hasil) <= 0) die("<center><h5>Data tidak ditemukan</h5></center>");
    $data = mysqli_fetch_array($hasil);

    $hasil_nilai     = mysqli_query($kon, "select * from nilai where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
    $data_nilai      = mysqli_fetch_array($hasil_nilai);
    $nilai           = isset($data_nilai['nilai']) ? $data_nilai['nilai'] : 0;
    $nilai_kelulusan = $data['nilai_kelulusan'];
    $status          = ($nilai >= $nilai_kelulusan) ? 'Kompeten' : 'Belum Kompeten';

    $jumlah_soal = $jumlah_benar = 0;
    if ($data['tipe_soal'] == 1) {
        $r1 = mysqli_query($kon, "select * from hasil where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
        $jumlah_soal  = mysqli_num_rows($r1);
        $r2 = mysqli_query($kon, "select h.* from hasil h inner join jawaban j on j.id_jawaban=h.id_jawaban where h.id_ujian='$id_ujian' and j.jawaban=1 and h.id_siswa='$id_siswa'");
        $jumlah_benar = mysqli_num_rows($r2);
    }

    function tgl($t) {
        $b=[1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $s=explode('-',$t); return $s[2].' '.$b[(int)$s[1]].' '.$s[0];
    }

    // ── PDF ───────────────────────────────────────────────────────────────────
    $pdf = new FPDF('P','mm','Letter');
    $pdf->AddPage();
    $pdf->SetMargins(20,15,20);
    $pdf->SetAutoPageBreak(true, 20);

    // ── HEADER ────────────────────────────────────────────────────────────────
    $pdf->Image('../../../img/logokemhan.png', 20, 12, 25, 25);
    $pdf->SetFont('Arial','B',15);
    $pdf->SetTextColor(20,40,100);
    $pdf->Cell(0,9,'PUSAT KODIFIKASI',0,1,'C');
    $pdf->SetFont('Arial','B',11);
    $pdf->Cell(0,6,'BADAN LOGISTIK PERTAHANAN KEMHAN',0,1,'C');
    $pdf->SetFont('Arial','',9);
    $pdf->SetTextColor(80,80,80);
    $pdf->Cell(0,5,$row_app['alamat'],0,1,'C');
    $pdf->Cell(0,5,'Kode Pos 12450,  Telp '.$row_app['no_telp'],0,1,'C');

    // Garis ganda
    $pdf->SetTextColor(0,0,0);
    $yg = $pdf->GetY()+3;
    $pdf->SetLineWidth(1); $pdf->SetDrawColor(20,40,100);
    $pdf->Line(20,$yg,196,$yg);
    $pdf->SetLineWidth(0.3); $pdf->SetDrawColor(20,40,100);
    $pdf->Line(20,$yg+2,196,$yg+2);
    $pdf->SetDrawColor(0,0,0); $pdf->SetLineWidth(0.2);
    $pdf->Ln(10);

    // ── JUDUL SERTIFIKAT ──────────────────────────────────────────────────────
    $pdf->SetFont('Arial','B',14);
    $pdf->SetTextColor(20,40,100);
    $pdf->Cell(0,8,'HASIL UJIAN',0,1,'C');

    // Garis dekoratif di bawah judul
    $yt = $pdf->GetY()+1;
    $cx = (216/2); $hw = 35;
    $pdf->SetDrawColor(20,40,100); $pdf->SetLineWidth(0.6);
    $pdf->Line($cx-$hw,$yt,$cx+$hw,$yt);
    $pdf->SetLineWidth(0.2); $pdf->SetDrawColor(0,0,0);
    $pdf->Ln(7);

    // ── TEKS NARASI ───────────────────────────────────────────────────────────
    $pdf->SetFont('Arial','',10);
    $pdf->SetTextColor(50,50,50);
    $pdf->Cell(0,6,'Dengan ini dinyatakan bahwa:',0,1,'L');
    $pdf->Ln(2);

    // ── DATA SISWA (tabel info) ───────────────────────────────────────────────
    $lw = 45; $sw = 5; $vw = 106;
    $rows = [
        ['Nama',          $data['nama_siswa']],
        ['NIP',           $data['nis']],
        ['Kategori',      $data['nama_kelas']],
    ];
    foreach ($rows as $r) {
        $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(30,30,30);
        $pdf->Cell($lw,7,$r[0],0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell($sw,7,':',0,0);
        $pdf->Cell($vw,7,$r[1],0,1);
    }

    $pdf->Ln(2);
    $pdf->SetFont('Arial','',10); $pdf->SetTextColor(50,50,50);
    $pdf->MultiCell(0,6,'Telah mengikuti ujian yang diselenggarakan oleh '.$data['nama_guru'].' dengan hasil sebagai berikut:',0,'L');
    $pdf->Ln(3);

    // ── INFO UJIAN (2 kolom, 1 baris) ────────────────────────────────────────
    $bx=20; $bw=176; $bh=16;
    $by=$pdf->GetY();
    $pdf->SetFillColor(248,249,252);
    $pdf->SetDrawColor(210,215,230);
    $pdf->SetLineWidth(0.3);
    $pdf->Rect($bx,$by,$bw,$bh,'DF');
    $pdf->Line($bx+$bw/2,$by,$bx+$bw/2,$by+$bh);

    $half=$bw/2; $lbl=42; $sep=4; $val=$half-$lbl-$sep-4;
    $pairs=[['Tanggal',tgl(date('Y-m-d',strtotime($data['tanggal'])))],['Nilai Minimum',number_format($nilai_kelulusan,2)]];
    foreach([$pairs[0],$pairs[1]] as $ci=>$pair){
        $pdf->SetXY($bx + $ci*$half + 3, $by+4);
        $pdf->SetFont('Arial','B',9); $pdf->SetTextColor(30,30,30);
        $pdf->Cell($lbl,7,$pair[0],0,0);
        $pdf->SetFont('Arial','',9);
        $pdf->Cell($sep,7,':',0,0);
        $pdf->Cell($val,7,$pair[1],0,0);
    }
    $pdf->SetXY($bx,$by+$bh);
    $pdf->Ln(6);

    // ── TABEL HASIL ───────────────────────────────────────────────────────────
    $pdf->SetDrawColor(20,40,100);
    $pdf->SetLineWidth(0.5);

if ($data['tipe_soal']==1) {
    $cols=[['No.',10],['Komponen Penilaian',80],['Keterangan',86]];
    $score_rows=[
        ['1','Jumlah Soal',(string)$jumlah_soal],
        ['2','Nilai Akhir',number_format($nilai,2)],
        ['3','Status Kelulusan',$status],
    ];
} else {
    $cols=[['No.',10],['Komponen Penilaian',80],['Keterangan',86]];
    $score_rows=[
        ['1','Nilai Akhir',number_format($nilai,2)],
        ['2','Status Kelulusan',$status],
    ];
}

    // Header tabel
    $pdf->SetFillColor(20,40,100);
    $pdf->SetTextColor(255,255,255);
    $pdf->SetFont('Arial','B',10);
    foreach($cols as $c) $pdf->Cell($c[1],8,$c[0],'TB',0,'C',true);
    $pdf->Ln();

    // Garis bawah header
    $pdf->SetDrawColor(20,40,100); $pdf->SetLineWidth(0.5);
    $pdf->Line(20,$pdf->GetY(),196,$pdf->GetY());
    $pdf->SetLineWidth(0.3);

    // Baris data
    $pdf->SetTextColor(30,30,30);
    foreach($score_rows as $ri=>$row){
        $fill = ($ri%2==0);
        $pdf->SetFillColor(248,249,252);

        // Kolom status punya warna khusus
        $pdf->SetFont('Arial','',10);
        $pdf->Cell($cols[0][1],8,$row[0],$fill?'B':'B',0,'C',$fill);
        $pdf->Cell($cols[1][1],8,$row[1],$fill?'B':'B',0,'L',$fill);

        // Kolom terakhir: warna teks status
        if(strpos($row[1],'Status')!==false){
            if($status==='Kompeten'){
                $pdf->SetTextColor(15,110,50); $pdf->SetFont('Arial','B',10);
            } else {
                $pdf->SetTextColor(170,50,0); $pdf->SetFont('Arial','B',10);
            }
        }
        $pdf->Cell($cols[2][1],8,$row[2],$fill?'B':'B',0,'C',$fill);
        $pdf->SetTextColor(30,30,30); $pdf->SetFont('Arial','',10);
        $pdf->Ln();
    }

    // Garis bawah tabel
    $pdf->SetDrawColor(20,40,100); $pdf->SetLineWidth(0.5);
    $pdf->Line(20,$pdf->GetY(),196,$pdf->GetY());
    $pdf->SetLineWidth(0.2); $pdf->SetDrawColor(0,0,0);
    $pdf->Ln(12);

    // ── TANDA TANGAN ─────────────────────────────────────────────────────────
    $pdf->SetFont('Arial','',10); $pdf->SetTextColor(50,50,50);
    $pdf->Cell(0,6,'Jakarta, '.tgl(date('Y-m-d')),0,1,'R');
    $pdf->Cell(0,6,'Pengawas,',0,1,'R');
    $pdf->Ln(16);
    $pdf->SetFont('Arial','B',10); $pdf->SetTextColor(20,40,100);
    $pdf->Cell(0,6,$data['nama_guru'],0,1,'R');
    $pdf->SetFont('Arial','',9); $pdf->SetTextColor(100,100,100);
    $pdf->Cell(0,5,'NIP. ______________________',0,1,'R');

    $pdf->Output();
?>
