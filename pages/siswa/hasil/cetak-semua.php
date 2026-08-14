<?php
    session_start();
    //Mengambil plugin fpdf
    require('../../../vendor/fpdf/fpdf.php');
    $pdf = new FPDF('P', 'mm','Letter');

    //Mengambil profil aplikasi
    require_once __DIR__ . '/../../../config/database.php';
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
    $pdf->Cell(0,7,'DAFTAR HASIL UJIAN',0,1,'C');
    $pdf->Cell(0,5,'',0,1,'C');

    $id_siswa=$_SESSION["id_siswa"];


    //Menampilkan detail data siswa
    $sql="select * from siswa s
    inner join kelas k on k.id_kelas=s.id_kelas
    where s.id_siswa='$id_siswa' limit 1";

    $hasil=mysqli_query($kon,$sql);
    $data = mysqli_fetch_array($hasil); 

    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,6,'NIP',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(31,6,$data['nis'],0,1);
    $pdf->Cell(30,6,'Nama',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(31,6,$data['nama_siswa'],0,1);
    $pdf->Cell(30,6,'Kategori',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(31,6,$data['nama_kelas'],0,1);



  
    //Membuat header tabel
    $pdf->Cell(10,3,'',0,1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(8,6,'No',1,0,'C');
    $pdf->Cell(25,6,'Tanggal',1,0,'C');
    $pdf->Cell(65,6,'Judul',1,0,'C');
    $pdf->Cell(50,6,'Mata Ujian',1,0,'C');
    $pdf->Cell(20,6,'Nilai',1,0,'C');
    $pdf->Cell(29,6,'Status',1,1,'C');

    $pdf->SetFont('Arial','',10);

    $no=0;
    $id_siswa = $_SESSION["id_siswa"];
    
    $sql="select * from ujian u
    inner join mapel m on m.id_mapel=u.id_mapel
    inner join nilai n on n.id_ujian=u.id_ujian
    where n.id_siswa='$id_siswa'";

    $hasil = mysqli_query($kon,$sql);
    $jumlah_matkul = mysqli_num_rows($hasil);
    while ($data = mysqli_fetch_array($hasil)){
        $no++;

        $id_ujian=$data['id_ujian'];

        $jumlah_soal=0;
        $jumlah_benar=0;
        $hasil1=mysqli_query($kon,"select * from hasil where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
        $jumlah_soal=mysqli_num_rows($hasil1);

        $hasil2=mysqli_query($kon,"select * from hasil h inner join jawaban j on j.id_jawaban=h.id_jawaban  where h.id_ujian='$id_ujian' and j.jawaban=1 and h.id_siswa='$id_siswa'");
        $jumlah_benar=mysqli_num_rows($hasil2);
    
        $nilai = $data['nilai'];

        $hasil3=mysqli_query($kon,"select nilai_kelulusan from ujian where id_ujian='$id_ujian'");
        $get = mysqli_fetch_array($hasil3);
    
        $nilai_kelulusan=$get['nilai_kelulusan'];
        $status="";

        if ($nilai >= $nilai_kelulusan){
            $status="Kompeten";
        }else {
            $status="Belum Kompeten";
        }

        $pdf->Cell(8,6,$no,1,0,'C');
        $pdf->Cell(25,6,date('d-m-Y', strtotime($data["tanggal"])),1,0,'C');
        $pdf->Cell(65,6, $data['judul'],1,0,'C');
        $pdf->Cell(50,6, $data['nama_mapel'],1,0,'C');
        $pdf->Cell(20,6,number_format($nilai,2),1,0,'C');
        $pdf->Cell(29,6,$status,1,1,'C');

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