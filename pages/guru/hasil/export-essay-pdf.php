<?php
    session_start();
    //Mengambil plugin fpdf
    require('../../../vendor/fpdf/fpdf.php');
    $pdf = new FPDF('P', 'mm','Letter');

    //Mengambil profil aplikasi
    include '../../../config/database.php';
    $query = mysqli_query($kon, "select * from aplikasi limit 1");    
    $row = mysqli_fetch_array($query);

    //Membuat halaman pdf
    $pdf->AddPage();

    //Membuat header dengan logo
    $pdf->Image('../../../img/logokemhan.png',10,5,30,30);
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,7,'PUSAT KODIFIKASI',0,1,'C');
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(0,6,'BADAN LOGISTIK PERTAHANAN KEMHAN',0,1,'C');
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(0,5,$row['alamat'],0,1,'C');
    $pdf->Cell(0,5,'Kode Pos 12450 , Telp '.$row['no_telp'],0,1,'C');
    $pdf->Cell(0,5,$row['website'],0,1,'C');

    //Membuat garis (line)
    $pdf->SetLineWidth(1);
    $pdf->Line(10,37,206,37);
    $pdf->SetLineWidth(0);
    $pdf->Line(10,38,206,38);
    
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,6,'',0,1,'C');
    $pdf->Cell(0,7,'HASIL UJIAN ESSAY',0,1,'C');
    $pdf->Cell(0,5,'',0,1,'C');

    $id_ujian = addslashes(trim($_GET['id_ujian']));
    $id_siswa = addslashes(trim($_GET['id_siswa']));

    // Mengambil data ujian
    $sql_ujian = "select * from ujian u
        inner join kelas k on u.id_kelas=k.id_kelas
        inner join mapel m on m.id_mapel=u.id_mapel
        inner join guru g on g.id_guru=u.id_guru
        where u.id_ujian='$id_ujian' limit 1"; 

    $hasil_ujian = mysqli_query($kon, $sql_ujian);
    $data_ujian = mysqli_fetch_array($hasil_ujian);

    // Mengambil data siswa
    $sql_siswa = "select * from siswa where id_siswa='$id_siswa' limit 1";
    $hasil_siswa = mysqli_query($kon, $sql_siswa);
    $data_siswa = mysqli_fetch_array($hasil_siswa);

    // Mengambil nilai
    $sql_nilai = "select nilai from nilai where id_ujian='$id_ujian' and id_siswa='$id_siswa' limit 1";
    $hasil_nilai = mysqli_query($kon, $sql_nilai);
    $data_nilai = mysqli_fetch_array($hasil_nilai);
    $nilai = $data_nilai['nilai'];

    $pdf->SetFont('Arial','',10);
    // Detail Info
    $pdf->Cell(28,6,'NIP',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(63,6,$data_siswa['nis'],0,0);
    $pdf->Cell(32,6,'Kategori',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(69,6,$data_ujian['nama_kelas'],0,1);
    
    $pdf->Cell(28,6,'Nama',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(63,6,$data_siswa['nama_siswa'],0,0);
    $pdf->Cell(32,6,'Tanggal',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(69,6,tanggal(date('Y-m-d', strtotime($data_ujian["tanggal"]))),0,1);
    
    $pdf->Cell(28,6,'Judul',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(63,6,$data_ujian['judul'],0,0);
    $pdf->Cell(32,6,'Nilai',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(69,6,number_format($nilai,2),0,1);
    
    $pdf->Cell(28,6,'Mata Ujian',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(166,6,$data_ujian['nama_mapel'],0,1);

    $pdf->Cell(0,5,'',0,1);

    // Mengambil soal dan jawaban
    $sql_soal = "select * from soal where id_ujian='$id_ujian' order by id_soal asc";
    $hasil_soal = mysqli_query($kon, $sql_soal);
    $no = 0;

    while ($row_soal = mysqli_fetch_array($hasil_soal)) {
        $no++;
        
        // Soal dalam kotak
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetFont('Arial','B',10);
        $pdf->MultiCell(0, 6, $no.'. '.$row_soal['soal'], 1, 'L', true);
        
        // Jawaban dalam kotak
        $get_jawaban = mysqli_query($kon, "select * from hasil where id_ujian='".$id_ujian."' and id_soal='".$row_soal['id_soal']."' and id_siswa='".$id_siswa."'");
        $data_jawaban = mysqli_fetch_array($get_jawaban);
        
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(0, 6, 'Jawaban:', 0, 1, 'L');
        $pdf->SetFont('Arial','',10);
        $pdf->MultiCell(0, 6, ($data_jawaban['essay'] ? $data_jawaban['essay'] : '(Tidak dijawab)'), 1, 'L');
        $pdf->Cell(0, 3, '', 0, 1);
    }

    //Membuat format tanggal
    function tanggal($tanggal)
    {
        $bulan = array (1 =>   'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        );
        $split = explode('-', $tanggal);
        return $split[2] . ' ' . $bulan[ (int)$split[1] ] . ' ' . $split[0];
    }

    $pdf->Output();
?>
