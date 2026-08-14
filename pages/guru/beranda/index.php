<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-home"></i> Beranda</h3>
    </div>
    <div class="card-body">
        <p>Computer Based Test atau Tes Berbasis Komputer adalah tes dengan sistem pelaksanaan menggunakan komputer sebagai media untuk melakukan tes. Anda dapat mengatur pelaksanaan ujian serta dapat melihat hasil ujian peserta.</p>
        <hr>
        
        <div class="row">
            <?php 
                include 'config/database.php';
                $id_guru = $_SESSION["id_guru"];
                $hasil = mysqli_query($kon,"SELECT COUNT(id_ujian) AS total_ujian_aktif FROM ujian WHERE id_guru='$id_guru' AND status_aktif='1'");
                $row_ujian = mysqli_fetch_assoc($hasil);
                $jumlah_ujian = isset($row_ujian['total_ujian_aktif']) ? (int)$row_ujian['total_ujian_aktif'] : 0;
            ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card stat-card red">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs text-uppercase mb-2 text-muted" style="font-weight: 600;">TOTAL UJIAN</div>
                        <div class="h4 mb-0 font-weight-bold"><?php echo $jumlah_ujian; ?></div>
                        <div class="text-xs text-muted">Ujian aktif</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat-icon red">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            
            <?php 
                $sql = "SELECT COUNT(id_siswa) as total FROM siswa";
                $hasil = mysqli_query($kon,$sql);
                $row = mysqli_fetch_array($hasil);
                $jumlah_peserta = $row['total'];
            ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card stat-card yellow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs text-uppercase mb-2 text-muted" style="font-weight: 600;">TOTAL PESERTA</div>
                        <div class="h4 mb-0 font-weight-bold"><?php echo $jumlah_peserta; ?></div>
                        <div class="text-xs text-muted">Peserta terdaftar</div>
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
                                $sql = "SELECT COUNT(DISTINCT CONCAT(n.id_ujian,'-',n.id_siswa)) as total
                                        FROM nilai n
                                        INNER JOIN ujian u ON n.id_ujian = u.id_ujian
                                        INNER JOIN mapel m ON u.id_mapel = m.id_mapel
                                        INNER JOIN siswa s ON n.id_siswa = s.id_siswa
                                        WHERE u.id_guru='$id_guru'
                                            AND u.status_aktif='1'
                                            AND m.status_aktif='1'
                                            AND YEAR(u.tanggal)=YEAR(CURDATE())";
                $hasil = mysqli_query($kon,$sql);
                $row = mysqli_fetch_array($hasil);
                $total_hasil = $row['total'];
            ?>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card stat-card blue">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs text-uppercase mb-2 text-muted" style="font-weight: 600;">HASIL UJIAN</div>
                        <div class="h4 mb-0 font-weight-bold"><?php echo $total_hasil; ?></div>
                        <div class="text-xs text-muted">Hasil ujian aktif tahun ini</div>
                    </div>
                    <div class="col-auto">
                        <div class="stat-icon blue">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div> 
</div>
