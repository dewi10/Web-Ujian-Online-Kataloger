<?php
    //validasi hanya admin yang boleh mengakses halaman ini
    $username = $_SESSION['username'];
    $cek = mysqli_query ($kon,"select * from admin where username='".$username."' limit 1");
    $jum = mysqli_num_rows($cek);

    if ($jum<1){
        echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
        exit;
    }
?>
<br>
<div class="dashboard-header">
    <h3><i class="fas fa-home"></i> Dashboard Beranda</h3>
</div>
<div class="card border-0">
    <div class="card-body">
        <div class="row">
            <?php 
                include 'config/database.php';  
                $hasil = mysqli_query($kon,"select id_guru from guru");
                $jumlah_guru = mysqli_num_rows($hasil);
          
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card red">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs text-uppercase mb-2 text-muted" style="font-weight: 600;">JUMLAH PENGAWAS</div>
                        <div class="h4 mb-0 font-weight-bold"><?php echo $jumlah_guru; ?></div>
                        <div class="text-xs text-muted">Total Pengawas Aktif</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat-icon red">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <?php 
                include 'config/database.php';  
                $hasil = mysqli_query($kon,"select id_siswa from siswa s inner join kelas k on k.id_kelas=s.id_kelas");
                $jumlah_siswa = mysqli_num_rows($hasil);
          
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card yellow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs text-uppercase mb-2 text-muted" style="font-weight: 600;">JUMLAH PESERTA UJIAN</div>
                        <div class="h4 mb-0 font-weight-bold"><?php echo $jumlah_siswa; ?></div>
                        <div class="text-xs text-muted">Total Peserta Ujian Terdaftar</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat-icon yellow">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <?php 
                include 'config/database.php';  
                $hasil = mysqli_query($kon,"SELECT COUNT(DISTINCT kode_kelas) AS total_kategori FROM kelas WHERE kode_kelas<>''");
                $data_kategori = mysqli_fetch_assoc($hasil);
                $jumlah_kelas = isset($data_kategori['total_kategori']) ? (int)$data_kategori['total_kategori'] : 0;
          
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card blue">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs text-uppercase mb-2 text-muted" style="font-weight: 600;">JUMLAH KLASIFIKASI</div>
                        <div class="h4 mb-0 font-weight-bold"><?php echo $jumlah_kelas; ?></div>
                        <div class="text-xs text-muted">Total Klasifikasi / Kategori</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat-icon blue">
                            <i class="fas fa-th-large"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            <?php 
                include 'config/database.php';  
                $hasil = mysqli_query($kon,"SELECT COUNT(id_mapel) AS total_mapel FROM mapel WHERE status_aktif='1'");
                $data_mapel = mysqli_fetch_assoc($hasil);
                $jumlah_mapel = isset($data_mapel['total_mapel']) ? (int)$data_mapel['total_mapel'] : 0;
          
            ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card green">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs text-uppercase mb-2 text-muted" style="font-weight: 600;">JUMLAH MATA UJIAN</div>
                        <div class="h4 mb-0 font-weight-bold"><?php echo $jumlah_mapel; ?></div>
                        <div class="text-xs text-muted">Total Mata Ujian Aktif</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat-icon green">
                            <i class="fas fa-book-open"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="card border-0 shadow-sm">
                    <div class="chart-section">
                        <h5><i class="fas fa-chart-bar"></i> Statistik Ujian Bulanan</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="total_ujian" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card border-0 shadow-sm">
                    <div class="schedule-section">
                        <h5><i class="fas fa-calendar-alt"></i> Jadwal Ujian Terbaru</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-wrapper-scroll" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover mb-0" id="tabel_status_ujian">
                                <thead style="background-color: #f8f9fa; position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th style="border-top: none;">Kategori</th>
                                        <th style="border-top: none;">Mata Ujian</th>
                                        <th style="border-top: none;">Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $no=0;
                                        $status="";
                                        $label="";
                    
        
                                        $sql="select * from ujian u inner join mapel m on m.id_mapel=u.id_mapel inner join kelas k on k.id_kelas=u.id_kelas order by tanggal desc";
                                        $hasil=mysqli_query($kon,$sql);
                                        while ($data = mysqli_fetch_array($hasil)):
                                        $no++;

            
                                    ?>
                                    <tr>
                                        <td><?php echo $data['nama_kelas'];  ?></td>
                                        <td><?php echo $data['nama_mapel']; ?></td>
                                        <td><span class="badge badge-danger"><?php echo date('d/m/Y', strtotime($data["tanggal"])); ?></span></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> 
</div>


<?php
   include 'config/database.php';
   $tahun = date('Y');
    for($bulan = 1;$bulan <= 12;$bulan++)
    {

        $hasil1=mysqli_query($kon,"select count(*) as total_ujian from ujian where MONTH(tanggal)='$bulan' and YEAR(tanggal)='$tahun' ");
        $data1=mysqli_fetch_array($hasil1);
        $total_ujian[] = $data1['total_ujian'];

    }
?>
<script>
	var randomScalingFactor = function(){ return Math.round(Math.random()*100)};

	var barChartData = {
		labels : ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"],
		datasets : [
			{
   
				fillColor : "#8b1a1a",
				strokeColor : "#c92a2a",
				highlightFill: "#a82222",
				highlightStroke: "#dc3545",
				data : <?php echo json_encode($total_ujian); ?>
			}
		]
	}



    //Memanggil chart
    window.onload = function(){

        var bar = document.getElementById("total_ujian").getContext("2d");
        window.myBar = new Chart(bar).Bar(barChartData, {
            responsive : true,
            maintainAspectRatio: false
        });
    };

</script>

<style>
    /* Scrollable table styling */
    .table-wrapper-scroll {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }
    
    .table-wrapper-scroll::-webkit-scrollbar {
        width: 8px;
    }
    
    .table-wrapper-scroll::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 10px;
    }
    
    .table-wrapper-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }
    
    .table-wrapper-scroll::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
    
    /* Sticky header styling */
    .table-wrapper-scroll thead th {
        background-color: #f8f9fa !important;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1);
    }
</style>



