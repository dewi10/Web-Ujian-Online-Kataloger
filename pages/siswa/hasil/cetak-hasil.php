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

    //Membuat header
    $pdf->Image('../../../pages/admin/aplikasi/logo/'.$row['logo'],15,5,20,20);
    $pdf->SetFont('Arial','B',21);
    $pdf->Cell(0,7,strtoupper($row['nama_aplikasi']),0,1,'C');
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(0,7,$row['alamat'].', Telp '.$row['no_telp'],0,1,'C');
    $pdf->Cell(0,7,$row['website'],0,1,'C');

    //Membuat garis (line)
    $pdf->SetLineWidth(1);
    $pdf->Line(10,31,206,31);
    $pdf->SetLineWidth(0);
    $pdf->Line(10,32,206,32);
    
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,5,'',0,1,'C');
    $pdf->Cell(0,7,'HASIL UJIAN',0,1,'C');

    $id_siswa=$_SESSION["id_siswa"];
    $id_ujian=addslashes(trim($_GET['id']));

    //Menampilkan detail data siswa
    $sql="select * from siswa s
    inner join kelas k on k.id_kelas=s.id_kelas
    inner join ujian u on u.id_kelas=k.id_kelas
    inner join mapel m on m.id_mapel=u.id_mapel
    inner join guru g on g.id_guru=u.id_guru
    where u.id_ujian='$id_ujian' and id_siswa='$id_siswa' limit 1";

    $hasil=mysqli_query($kon,$sql);

    $cek=mysqli_num_rows($hasil);
    if ($cek<=0){
        echo "<center><h5>Data tidak ditemukan</h5></center>";
        exit;
    }

    $data = mysqli_fetch_array($hasil); 

    $pdf->SetFont('Arial','',10);
    $pdf->Cell(30,6,'NIS ',0,0);
    $pdf->Cell(31,6,': '.$data['nis'],0,1);
    $pdf->Cell(30,6,'Nama ',0,0);
    $pdf->Cell(31,6,': '.$data['nama_siswa'],0,1);
    $pdf->Cell(30,6,'Kategori ',0,0);
    $pdf->Cell(31,6,': '.$data['nama_kelas'],0,1);
    $pdf->Cell(30,6,'Judul ',0,0);
    $pdf->Cell(31,6,': '.$data['judul'],0,1);
    $pdf->Cell(30,6,'Mata Ujian ',0,0);
    $pdf->Cell(31,6,': '.$data['nama_mapel'],0,1);
    $pdf->Cell(30,6,'Tanggal ',0,0);
    $pdf->Cell(31,6,': '.date('d-m-Y', strtotime($data["tanggal"])),0,1);


    $jumlah_soal=0;
    $jumlah_benar=0;
 
    $hasil=mysqli_query($kon,"select * from hasil where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
    $jumlah_soal=mysqli_num_rows($hasil);

    $hasil2=mysqli_query($kon,"select * from hasil h inner join jawaban j on j.id_jawaban=h.id_jawaban  where h.id_ujian='$id_ujian' and j.jawaban=1 and h.id_siswa='$id_siswa'");
    $jumlah_benar=mysqli_num_rows($hasil2);

    $hasil1=mysqli_query($kon,"select * from nilai where id_ujian='$id_ujian' and id_siswa='$id_siswa'");
    $data1 = mysqli_fetch_array($hasil1);
    $nilai=$data1['nilai'];

    $hasil2=mysqli_query($kon,"select nilai_kelulusan from ujian where id_ujian='$id_ujian'");
    $data2 = mysqli_fetch_array($hasil2);

    $nilai_kelulusan=$data2['nilai_kelulusan'];
    $status="";
    if ($nilai>= $nilai_kelulusan){
        $status="Kompeten";
    }else {
        $status="Belum Kompeten";
    }


  
    //Membuat header tabel
    $pdf->Cell(10,3,'',0,1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(33,6,'Jumlah Soal',1,0,'C');
    $pdf->Cell(72,6,'Benar',1,0,'C');
    $pdf->Cell(30,6,'Nilai',1,0,'C');
    $pdf->Cell(60,6,'Status',1,1,'C');

    $pdf->SetFont('Arial','',10);

    $pdf->Cell(33,6,$jumlah_soal,1,0,'C');
    $pdf->Cell(72,6,$jumlah_benar,1,0,'C');
    $pdf->Cell(30,6,number_format($nilai,2),1,0,'C');
    $pdf->Cell(60,6,$status,1,1,'C');
    
    

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