<?php
    //set session dulu dengan nama $_SESSION["mulai_ujian"]
    if (isset($_SESSION["mulai_ujian"])) { 
        //jika session sudah ada
        $telah_berlalu = time() - $_SESSION["mulai_ujian"];
    } else { 
        //jika session belum ada
        $_SESSION["mulai_ujian"]  = time();
        $telah_berlalu      = 0;
    } 
?>


<?php
        include 'config/database.php';
        $id_ujian=addslashes(trim($_GET['id']));
        $sql    = mysqli_query($kon,"select waktu,jam from ujian where id_ujian='$id_ujian'");   
        $data   = mysqli_fetch_array($sql);

        date_default_timezone_set('Asia/Jakarta'); 
   
        //Waktu ujian akan disesuaikan saat peserta masuk
        $start_date = new DateTime($data["jam"]);
        $since_start = $start_date->diff(new DateTime(date('H:i:s')));

        $minutes = $since_start->days * 24 * 60;
        $minutes += $since_start->h * 60;
        $minutes += $since_start->i;

        //Waktu ujian dikurangi waktu keterlambatan
        $waktu=$data['waktu']-$minutes;
       
    
        $temp_waktu = ($waktu*60) - $telah_berlalu; //dijadikan detik dan dikurangi waktu yang berlalu
        $temp_menit = (int)($temp_waktu/60);                //dijadikan menit lagi
        $temp_detik = $temp_waktu%60;                       //sisa bagi untuk detik
        
        if ($temp_menit < 60) { 
            /* Apabila $temp_menit yang kurang dari 60 meni */
            $jam    = 0; 
            $menit  = $temp_menit; 
            $detik  = $temp_detik; 
        } else { 
            /* Apabila $temp_menit lebih dari 60 menit */           
            $jam    = (int)($temp_menit/60);    //$temp_menit dijadikan jam dengan dibagi 60 dan dibulatkan menjadi integer 
            $menit  = $temp_menit%60;           //$temp_menit diambil sisa bagi ($temp_menit%60) untuk menjadi menit
            $detik  = $temp_detik;
        }   
?>

<!-- Script Timer -->
<script type="text/javascript">
    $(document).ready(function() {
        /** Membuat Waktu Mulai Hitung Mundur Dengan 
            * var detik;
            * var menit;
            * var jam;
        */
        var detik   = <?php echo $detik; ?>;
        var menit   = <?php echo $menit; ?>;
        var jam     = <?php echo $jam; ?>;
                
        /**
            * Membuat function hitung() sebagai Penghitungan Waktu
        */
        function hitung() {
            /** setTimout(hitung, 1000) digunakan untuk 
                    * mengulang atau merefresh halaman selama 1000 (1 detik) 
            */
            setTimeout(hitung,1000);

            var peringatan = '';
            /** Jika waktu kurang dari 10 menit maka Timer akan berubah menjadi warna merah */
            if(menit < 10 && jam == 0){
                peringatan = 'style="color:red"';
            }


            /** Menampilkan Waktu Timer pada Tag #Timer di HTML yang tersedia */
            $('#timer').html(
                '<h5 align="right"'+peringatan+'>Sisa waktu ' + jam + ' jam : ' + menit + ' menit : ' + detik + ' detik</h5><hr>'
            );

            /** Melakukan Hitung Mundur dengan Mengurangi variabel detik - 1 */
            detik --;

            /** Jika var detik < 0
                * var detik akan dikembalikan ke 59
                * Menit akan Berkurang 1
            */
            if(detik < 0) {
                detik = 59;
                menit --;

                /** Jika menit < 0
                    * Maka menit akan dikembali ke 59
                    * Jam akan Berkurang 1
                */
                if(menit < 0) {
                    menit = 59;
                    jam --;

                    /** Jika var jam < 0
                        * clearInterval() Memberhentikan Interval dan submit secara otomatis
                    */
                            
                    if(jam < 0) { 
                        clearInterval(hitung); 
                        //Saat waktu ujian telah selesai halaman akan di arahkan ke simpan-riwayat
                        alert('Waktu ujian telah selesai');
                         window.location.href = "pages/siswa/ujian/simpan-hasil.php?id=<?php echo $id_ujian;?>";
                    } 
                } 
            } 
        }           
        /** Menjalankan Function Hitung Waktu Mundur */
        hitung();
    });
</script>