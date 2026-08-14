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
    $pdf->Cell(0,7,'DAFTAR HASIL UJIAN',0,1,'C');
    $pdf->Cell(0,5,'',0,1,'C');

    $id_ujian=addslashes(trim($_GET['id']));

    $sql ="select * from ujian u
    inner join kelas k on u.id_kelas=k.id_kelas
    inner join siswa s on s.id_kelas=k.id_kelas
    inner join mapel m on m.id_mapel=u.id_mapel
    inner join guru g on g.id_guru=u.id_guru
    where u.id_ujian='$id_ujian' limit 1"; 

    $hasil=mysqli_query($kon,$sql);

    $cek=mysqli_num_rows($hasil);
    if ($cek<=0){
        echo "<center><h5>Data tidak ditemukan</h5></center>";
        exit;
    }


    $data = mysqli_fetch_array($hasil); 

    $pdf->SetFont('Arial','',10);
    // Baris 1: Kategori | Nama Pengawas
    $pdf->Cell(28,6,'Kategori',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(63,6,$data['nama_kelas'],0,0);
    $pdf->Cell(32,6,'Nama Pengawas',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(69,6,$data['nama_guru'],0,1);
    
    // Baris 2: Mata Ujian | Tanggal
    $pdf->Cell(28,6,'Mata Ujian',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(63,6,$data['nama_mapel'],0,0);
    $pdf->Cell(32,6,'Tanggal',0,0);
    $pdf->Cell(2,6,':',0,0);
    $pdf->Cell(69,6,tanggal(date('Y-m-d', strtotime($data["tanggal"]))),0,1);

    //Membuat header tabel
    $pdf->Cell(10,3,'',0,1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(8,6,'No',1,0,'C');
    $pdf->Cell(33,6,'NIS',1,0,'C');
    $pdf->Cell(85,6,'Siswa',1,0,'C');
    $pdf->Cell(20,6,'Nilai',1,0,'C');
    $pdf->Cell(50,6,'Status',1,1,'C');

    $pdf->SetFont('Arial','',10);


    $no=0;
    $rata=0;
    $total_rata=0;

    $sql="select * from siswa s
    inner join nilai n on  n.id_siswa=s.id_siswa 
    where n.id_ujian='$id_ujian'";

    $hasil = mysqli_query($kon,$sql);
    $jum=mysqli_num_rows($hasil);
    while ($data = mysqli_fetch_array($hasil)){
        $no++;

        $nilai = $data['nilai'];
                
        $hasil2=mysqli_query($kon,"select nilai_kelulusan from ujian where id_ujian='$id_ujian'");
        $data2 = mysqli_fetch_array($hasil2);
        $nilai_kelulusan=$data2['nilai_kelulusan'];

        
        $rata+=$nilai;

        $total_rata=$rata/$jum;
        $arr_nilai[] = $data['nilai'];
        
        $status="";
        if ($nilai>= $nilai_kelulusan){
            $status="Kompeten";
        }else {
            $status="Belum Kompeten";
        }

        $pdf->Cell(8,6,$no,1,0,'C');
        $pdf->Cell(33,6,$data["nis"],1,0,'C');
        $pdf->Cell(85,6, $data['nama_siswa'],1,0);
        $pdf->Cell(20,6,number_format($nilai,2),1,0,'C');
        $pdf->Cell(50,6,$status,1,1,'C');

    }


    
    $nilai_max=0;
    $nilai_min=0;
    if (isset($arr_nilai)){
        $nilai_max=max($arr_nilai);
        $nilai_min=min($arr_nilai);
    }
    
    
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(10,3,'',0,1);
    $pdf->Cell(30,6,'Nilai Tertinggi ',0,0);
    $pdf->Cell(31,6,': '.$nilai_max,0,1);
    $pdf->Cell(30,6,'Nilai Terendah ',0,0);
    $pdf->Cell(31,6,': '.$nilai_min,0,1);
    $pdf->Cell(30,6,'Nilai Rata-rata ',0,0);
    $pdf->Cell(31,6,': '.$total_rata,0,1);



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