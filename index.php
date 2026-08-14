<?php 
session_start();

if (!isset($_SESSION["username"]) || !$_SESSION["username"]) {
  header("Location:login.php");
  exit;
}

include 'config/database.php';
require_once __DIR__ . '/config/siswa_ujian_aktif.php';

date_default_timezone_set('Asia/Jakarta');

$page_req = isset($_GET['page']) ? $_GET['page'] : '';
$aktif_ujian_id = 0;
if (isset($_SESSION['id_siswa'])) {
    $aktif_ujian_id = siswa_id_ujian_berlangsung($kon, (string) $_SESSION['id_siswa']);
}
if ($aktif_ujian_id > 0 && isset($_SESSION['id_siswa'])) {
    $idAktif = (int) $aktif_ujian_id;
    $page_id_ujian = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $id_siswa_cek = (string) $_SESSION['id_siswa'];

    // URL ?id=103 (Pertama) jangan ditimpa ujian aktif lain (mis. U104) bila siswa memang peserta ujian itu
    $target_ujian = $idAktif;
    if ($page_id_ujian > 0 && siswa_boleh_ujian_berlangsung($kon, $id_siswa_cek, $page_id_ujian)) {
        $target_ujian = $page_id_ujian;
    }

    $halaman_ujian_aktif = in_array($page_req, array('', 'ujian-siswa', 'hasil-ujian-siswa', 'review', 'mulai-ujian'), true)
        && ($page_req === '' || $page_id_ujian === 0 || $page_id_ujian === $target_ujian);

    if ($halaman_ujian_aktif) {
        $id_siswa_esc = mysqli_real_escape_string($kon, $id_siswa_cek);
        $q_hasil_aktif = mysqli_query($kon, "SELECT 1 FROM hasil WHERE id_ujian='$target_ujian' AND id_siswa='$id_siswa_esc' LIMIT 1");
        $belum_mulai = (!$q_hasil_aktif || mysqli_num_rows($q_hasil_aktif) === 0);
        if ($belum_mulai) {
            header('Location: pages/siswa/hasil/set-default.php?id=' . $target_ujian);
            exit;
        }
    }
}

$id_siswa_esc = null;
$sudah_ada_ujian_selesai = false;
if (isset($_SESSION['id_siswa'])) {
    $id_siswa_esc = mysqli_real_escape_string($kon, (string) $_SESSION['id_siswa']);
    // Hanya riwayat = ujian pernah diselesaikan (tombol SELESAI). Nilai 0 dari "Kerjakan" bukan indikator selesai.
    $q_r = mysqli_query($kon, "SELECT 1 FROM riwayat WHERE id_siswa='$id_siswa_esc' LIMIT 1");
    $sudah_ada_ujian_selesai = ($q_r && mysqli_num_rows($q_r) > 0);
}

// Tanpa ?page konten utama kosong; redirect sebelum ada output HTML
if (!isset($_GET['page']) || $_GET['page'] === '') {
    if (isset($_SESSION["id_admin"])) {
        header("Location: index.php?page=beranda");
        exit;
    }
    if (isset($_SESSION["id_guru"])) {
        header("Location: index.php?page=beranda");
        exit;
    }
    if (isset($_SESSION["id_siswa"]) && $id_siswa_esc !== null) {
        if ($sudah_ada_ujian_selesai) {
            header("Location: index.php?page=hasil-ujian-siswa");
        } else {
            header("Location: index.php?page=ujian-siswa");
        }
        exit;
    }
}

// page=ujian-siswa: sudah pernah selesai → hasil; ada ujian hari ini → review (harus sebelum HTML, sama seperti header() di include)
if (isset($_GET['page']) && $_GET['page'] === 'ujian-siswa' && isset($_SESSION['id_siswa']) && $id_siswa_esc !== null) {
    if ($sudah_ada_ujian_selesai) {
        header("Location: index.php?page=hasil-ujian-siswa");
        exit;
    }
    $today = date('Y-m-d');
    $cek_col = mysqli_query($kon, "SHOW COLUMNS FROM ujian LIKE 'status_aktif'");
    $aktif_sql = ($cek_col && mysqli_num_rows($cek_col) > 0) ? " AND u.status_aktif='1'" : '';
    $sql_today = "SELECT u.id_ujian FROM ujian u
        INNER JOIN siswa s ON s.id_siswa = '$id_siswa_esc' AND s.id_kelas = u.id_kelas
        INNER JOIN peserta p ON p.id_ujian = u.id_ujian AND p.id_siswa = s.id_siswa
        WHERE u.tanggal = '$today'$aktif_sql
        ORDER BY u.jam ASC, u.id_ujian ASC
        LIMIT 1";
    $result = mysqli_query($kon, $sql_today);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        header("Location: index.php?page=review&id=" . (int) $row['id_ujian']);
        exit;
    }
}

$hasil=mysqli_query($kon,"select * from aplikasi limit 1");
$aplikasi = mysqli_fetch_array($hasil); 

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $aplikasi['nama_aplikasi'];?></title>
  
  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <script src="vendor/jquery/jquery-3.4.1.js"></script>
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <!-- Custom styles for this template -->
  <link href="css/simple-sidebar.css" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
  <!-- ChartJS -->
  <script src="vendor/chart.js/chart.js"></script>

  <!-- Select2 -->
  <script src="vendor/select2/select2.min.js"></script>
  <link href="vendor/select2/select2.min.css" rel="stylesheet" crossorigin="anonymous" />
  

  <style type="text/css">
    body, html, p, h1, h2, h3, h4, h5, h6, div, span, a, button, input, select, textarea, label {
      font-family: 'Calibri', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }
    /* Pastikan icon Font Awesome tidak terpengaruh */
    i.fas, i.fab, i.far, i.fa {
      font-family: 'Font Awesome 5 Free', 'Font Awesome 5 Brands' !important;
      font-style: normal;
    }
    body {
      font-family: 'Calibri', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }
    .preloader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 9999;
      background-color: #fff;
    }
    .preloader .loading {
      position: absolute;
      left: 50%;
      top: 50%;
      transform: translate(-50%,-50%);
      font: 14px Calibri, arial;
    }
  </style>

  <script>
    $(document).ready(function(){
      $(".preloader").fadeOut();
    })
  </script>


</head>

<body>

<?php
  include 'config/database.php';
  
  if (isset($_SESSION["id_guru"])){
      $query = mysqli_query($kon, "select * from guru where id_guru='".$_SESSION["id_guru"]."' limit 1"); 
      $row = mysqli_fetch_array($query);
      $nama=$row['nama_guru']; 
  } else if (isset($_SESSION["id_admin"])){
      $query = mysqli_query($kon, "select * from admin where id_admin='".$_SESSION["id_admin"]."' limit 1"); 
      $row = mysqli_fetch_array($query);
      $nama=$row['nama_admin'];
  } else if (isset($_SESSION["id_siswa"])){
      $query = mysqli_query($kon, "select * from siswa where id_siswa='".$_SESSION["id_siswa"]."' limit 1"); 
      $row = mysqli_fetch_array($query);
      $nama=$row['nama_siswa'];
  }
?>

  <div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-light border-right" id="sidebar-wrapper">
      <div class="sidebar-heading">
        <div style="font-size: 1.1rem; font-weight: 700; line-height: 1.3;">
          SISTEM INFORMASI UKOM<br>KATALOGER
        </div>
      </div>
      <div class="list-group list-group-flush">
        
        <?php if (isset($_SESSION["id_admin"])): ?>
          <a href="index.php?page=beranda" class="list-group-item list-group-item-action bg-light"><i class="fas fa-home"></i> Beranda</a>
          <a href="index.php?page=guru" class="list-group-item list-group-item-action bg-light"><i class="fas fa-user-tie"></i> Pengawas</a>
          <a href="index.php?page=siswa" class="list-group-item list-group-item-action bg-light"><i class="fas fa-user"></i> Peserta Ujian</a>
          <a href="index.php?page=kelas" class="list-group-item list-group-item-action bg-light"><i class="fas fa-th-large"></i> Klasifikasi</a>
          <a href="index.php?page=mapel" class="list-group-item list-group-item-action bg-light"><i class="fas fa-book"></i> Mata Ujian</a>
          <a href="index.php?page=semua-hasil-ujian" class="list-group-item list-group-item-action bg-light"><i class="fas fa-clipboard-check"></i> Hasil Ujian</a>
          <a href="index.php?page=skp" class="list-group-item list-group-item-action bg-light"><i class="fas fa-file-alt"></i> SKP</a>
          <a href="index.php?page=pak" class="list-group-item list-group-item-action bg-light"><i class="fas fa-file-signature"></i> PAK</a>
          <!-- <a href="index.php?page=aplikasi" class="list-group-item list-group-item-action bg-light"><i class="fas fa-cog"></i> Aplikasi</a> -->
          <?php endif; ?>

        <?php if (isset($_SESSION["id_guru"])): ?>
          <a href="index.php?page=beranda" class="list-group-item list-group-item-action bg-light"><i class="fas fa-home"></i> Beranda</a>

        <a href="index.php?page=ujian" class="list-group-item list-group-item-action bg-light"><i class="fas fa-clipboard-list"></i> Daftar Ujian</a>
        <a href="index.php?page=hasil-ujian" class="list-group-item list-group-item-action bg-light"><i class="fas fa-clipboard-check"></i> Hasil Ujian</a>
        <a href="index.php?page=skp" class="list-group-item list-group-item-action bg-light"><i class="fas fa-file-alt"></i> SKP</a>
        <a href="index.php?page=pak" class="list-group-item list-group-item-action bg-light"><i class="fas fa-file-signature"></i> PAK</a>
        <?php endif; ?>
        <?php if (isset($_SESSION["id_siswa"])): ?>
        <!-- <a href="index.php?page=ujian-siswa" class="list-group-item list-group-item-action bg-light"><i class="fas fa-clipboard-list"></i> Daftar Ujian</a>
        <a href="index.php?page=hasil-ujian-siswa" class="list-group-item list-group-item-action bg-light"><i class="fas fa-clipboard-check"></i> Hasil Ujian</a> -->
        <a href="index.php?page=skp" class="list-group-item list-group-item-action bg-light"><i class="fas fa-file-alt"></i> SKP</a>
        <a href="index.php?page=pak" class="list-group-item list-group-item-action bg-light"><i class="fas fa-file-signature"></i> PAK</a>
        <?php endif; ?>
      </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">

      <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
  
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo $nama; ?>
              </a>
              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="index.php?page=profil">Profil</a>
                <a class="dropdown-item" href="logout.php">Keluar</a>
              </div>
            </li>
          </ul>
        </div>
      </nav>

      <div class="container-fluid">

        <?php
            include 'config/database.php';
            if(isset($_GET['page'])){
                $page = $_GET['page'];
            
                switch ($page) {
                    case 'beranda':

                      if (isset($_SESSION["id_admin"])){
                        include "pages/admin/beranda/index.php";
                      } else if (isset($_SESSION["id_guru"])){
                        include "pages/guru/beranda/index.php";
                      }
                      
                      else if (isset($_SESSION["id_siswa"])){
                        include "pages/siswa/beranda/index.php";
                      }
                      
                      break;
                    case 'profil':

                      if (isset($_SESSION["id_admin"])){
                        include "pages/admin/profil/index.php";
                      } else if (isset($_SESSION["id_guru"])){
                        include "pages/guru/profil/index.php";
                      }else if (isset($_SESSION["id_siswa"])){
                        include "pages/siswa/profil/index.php";
                      }

                      break;
                    case 'aplikasi':
                      include "pages/admin/aplikasi/index.php";
                      break;
                    case 'guru':
                      include "pages/admin/guru/index.php";
                      break;
                    case 'upload-guru':
                      include "pages/admin/guru/upload.php";
                      break;
                    case 'siswa':
                      include "pages/admin/siswa/index.php";
                      break;
                    case 'kelas':
                      include "pages/admin/kelas/index.php";
                      break;
                    case 'mapel':
                      include "pages/admin/mapel/index.php";
                      break;
                    case 'semua-hasil-ujian':
                      include "pages/admin/hasil/index.php";
                      break;
                    case 'skp':
                      if (isset($_SESSION["id_admin"])){
                        include "pages/admin/skp/index.php";
                      } else if (isset($_SESSION["id_guru"])){
                        include "pages/guru/skp/index.php";
                      } else if (isset($_SESSION["id_siswa"])){
                        include "pages/siswa/skp/index.php";
                      }
                      break;
                    case 'pak':
                      if (isset($_SESSION["id_admin"])){
                        include "pages/admin/pak/index.php";
                      } else if (isset($_SESSION["id_guru"])){
                        include "pages/guru/pak/index.php";
                      } else if (isset($_SESSION["id_siswa"])){
                        include "pages/siswa/pak/index.php";
                      }
                      break;
                    case 'ujian':
                      include "pages/guru/ujian/index.php";
                      break;
                    case 'input-soal':
                      include "pages/guru/soal/input-soal.php";
                      break;
                    case 'hasil-ujian':
                      include "pages/guru/hasil/index.php";
                      break;
                    case 'ujian-siswa':
                      include "pages/siswa/ujian/index.php";
                      break;
                    case 'review':
                      include "pages/siswa/ujian/review.php";
                      break;
                      case 'mulai-ujian':
                        include "pages/siswa/ujian/mulai.php";
                        break;
                      case 'hasil-ujian-siswa':
                        include "pages/siswa/hasil/index.php";
                        break;
                      case 'lihat-hasil':
                        include "pages/siswa/hasil/lihat-hasil.php";
                        break;

                default:
                    echo "<center><h3>Maaf. Halaman tidak di temukan !</h3></center>";
                    break;
                }
            }
        ?>


      </div>
    </div>
    <!-- /#page-content-wrapper -->

  </div>
  <!-- /#wrapper -->

    <div class="preloader">
      <div class="loading">
        <div class="spinner-border text-primary"></div> 
      </div>
    </div>

    <div id='ajax-wait'>
      <div class="spinner-border text-primary"></div> 
    </div>

    <style>
        #ajax-wait {
        display: none;
        position: fixed;
        z-index: 1999
        }
    </style>
    <script>

    $(document).ready( function () {
        loading();
    });

    //Fungsi untuk efek loading
    function loading(){
        $( document ).ajaxStart(function() {
        $( "#ajax-wait" ).css({
            left: ( $( window ).width() - 32 ) / 2 + "px", // 32 = lebar gambar
            top: ( $( window ).height() - 32 ) / 2 + "px", // 32 = tinggi gambar
            display: "block"
        })
        })
        .ajaxComplete( function() {
            $( "#ajax-wait" ).fadeOut();
        });
    }
    </script>


  <!-- Bootstrap core JavaScript -->
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script> 
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
  <script src="vendor/datatables/dataTables.buttons.min.js"></script>
  <script src="vendor/datatables/jszip.min.js"></script>
  <script src="vendor/datatables/vfs_fonts.js"></script>
  <script src="vendor/datatables/buttons.html5.min.js"></script>
  <script src="vendor/datatables/buttons.print.min.js"></script>


  <!-- Menu Toggle Script -->
  <script>
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });
  </script>

</body>

</html>
