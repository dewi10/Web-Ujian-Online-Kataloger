<div class="table-responsive">
    <table class="table table-bordered" id="tabel_ujian">
        <thead class="thead-light">
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Judul</th>
            <th>Mapel</th>
            <th>Pengawas</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Status</th>
            <th width="10%">Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php
            // include database
            require_once __DIR__ . '/../../../config/database.php';
            session_start();
            date_default_timezone_set('Asia/Jakarta'); 
            
            $id_siswa=$_SESSION["id_siswa"];
            $hasil=mysqli_query($kon,"select id_kelas from siswa where id_siswa='$id_siswa' ");
            $row=mysqli_fetch_array($hasil);
            $id_kelas=$row['id_kelas'];
            


            $guru = ""; 

            if (isset($_POST['guru'])) {
                foreach ($_POST['guru'] as $value)
                {
                    $guru .= "'$value'". ",";
                }
                $guru = substr($guru,0,-1);
            }

            $mapel = ""; 

            if (isset($_POST['mapel'])) {
                foreach ($_POST['mapel'] as $value)
                {
                    $mapel .= "'$value'". ",";
                }
                $mapel = substr($mapel,0,-1);
            }

            // Cek apakah ada ujian hari ini atau yang akan datang
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            
            // Cek apakah ada ujian >= hari ini (hanya yang terdaftar sebagai peserta)
            $cek_ujian_baru = mysqli_query($kon, "select count(*) as jumlah from ujian u
                inner join kelas k on u.id_kelas=k.id_kelas
                inner join siswa s on s.id_kelas=k.id_kelas
                inner join peserta p on p.id_ujian=u.id_ujian and p.id_siswa=s.id_siswa
                where k.id_kelas='$id_kelas' and s.id_siswa='$id_siswa' and u.tanggal >= '$today'");
            $row_cek = mysqli_fetch_array($cek_ujian_baru);
            $ada_ujian_baru = $row_cek['jumlah'] > 0;
            
            // Jika ada ujian baru (hari ini atau ke depan), hanya tampilkan ujian >= hari ini
            // Jika tidak ada ujian baru, tampilkan ujian kemarin (grace period 1 hari)
            $filter_tanggal = $ada_ujian_baru ? "u.tanggal >= '$today'" : "u.tanggal >= '$yesterday'";
            
            if (isset ($_POST['guru']) and !isset ($_POST['mapel'])){
                $sql="select * from ujian u
                inner join kelas k on u.id_kelas=k.id_kelas
                inner join mapel m on m.id_mapel=u.id_mapel
                inner join guru g on g.id_guru=u.id_guru
                inner join siswa s on s.id_kelas=k.id_kelas
                inner join peserta p on p.id_ujian=u.id_ujian and p.id_siswa=s.id_siswa
                where k.id_kelas='$id_kelas' and s.id_siswa='$id_siswa' and u.id_guru in (".$guru.") and $filter_tanggal order by u.tanggal asc, u.jam asc";
            }else if (isset ($_POST['guru']) and isset ($_POST['mapel'])){
                $sql="select * from ujian u
                inner join kelas k on u.id_kelas=k.id_kelas
                inner join mapel m on m.id_mapel=u.id_mapel
                inner join guru g on g.id_guru=u.id_guru
                inner join siswa s on s.id_kelas=k.id_kelas
                inner join peserta p on p.id_ujian=u.id_ujian and p.id_siswa=s.id_siswa
                where k.id_kelas='$id_kelas' and s.id_siswa='$id_siswa' and u.id_guru in (".$guru.") and u.id_mapel in (".$mapel.") and $filter_tanggal order by u.tanggal asc, u.jam asc";
            }else {
                $sql="select * from ujian u
                inner join kelas k on u.id_kelas=k.id_kelas
                inner join mapel m on m.id_mapel=u.id_mapel
                inner join guru g on g.id_guru=u.id_guru
                inner join siswa s on s.id_kelas=k.id_kelas
                inner join peserta p on p.id_ujian=u.id_ujian and p.id_siswa=s.id_siswa
                where k.id_kelas='$id_kelas' and s.id_siswa='$id_siswa' and $filter_tanggal order by u.tanggal asc, u.jam asc";
            }



       
            $hasil=mysqli_query($kon,$sql);
            $no=0;
            
            //Menampilkan data dengan perulangan while
            while ($data = mysqli_fetch_array($hasil)):
            $no++;

        ?>
        <tr>
            <td><?php echo $no; ?></td>
            <td><?php echo $data['kode_ujian'];?></td>
            <td>
                <?php echo $data['judul'];?>
                <p><small class="text-muted">Tipe Soal <?php echo $data['tipe_soal'] == 1 ? 'Pilihan Ganda' : 'Essay';?></small></p>
            </td>
            <td><?php echo $data['nama_mapel'];?></td>
            <td><?php echo $data['nama_guru'];?></td>
            <td><?php echo tanggal(date('Y-m-d', strtotime($data["tanggal"]))); ?> <p><small class="text-muted"><?php echo date('H:i', strtotime($data["jam"])); ?> - <?php echo date("H:i", strtotime("+".$data['waktu']." minutes", strtotime($data["jam"]))); ?> WIB</small</p></td>
            <td><?php echo $data['waktu'];?> menit</td>
            <td>

            <?php 

                $saat_ini = date('Y-m-d H:i:s');
                $mulai = $data["tanggal"]." ".$data["jam"];
                $selesai = date("Y-m-d H:i:s", strtotime("+".$data['waktu']." minutes", strtotime($mulai)));

                if ($saat_ini < $mulai){
                    echo "<span class='badge badge-pill badge-primary'>Belum Mulai</span>";
                }else if ($saat_ini > $selesai){
                    echo "<span class='badge badge-pill badge-success'>Telah Selesai</span>";
                }else {
                    echo "<span class='badge badge-pill badge-warning'>Sedang Berlangsung</span><br>";
                    $start_date = new DateTime($data["jam"]);
                    $since_start = $start_date->diff(new DateTime(date('H:i:s')));
                    
                    $minutes = $since_start->days * 24 * 60;
                    $minutes += $since_start->h * 60;
                    $minutes += $since_start->i;
                    echo "<small class='text-primary'> ".$minutes." menit lalu</small>";
                }
            ?> 
                
            </td>
            <td>
            <a href="index.php?page=review&id=<?php echo $data['id_ujian']; ?>" class="btn btn-primary btn-circle" ><i class="fas fa-paper-plane"></i> Pilih</a>
        </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
    $(document).ready( function () {
        $('#tabel_ujian').DataTable();
    } );
</script>


<?php 
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

?>