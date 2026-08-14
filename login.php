<?php 
session_start();
// Jika terdeteksi ada session pengguna yang aktif, maka session tersebut di hapuskan
if (isset($_SESSION["username"])) {
    session_unset();
    session_destroy();
}

$pesan = "";
// Fungsi untuk mencegah inputan karakter yang tidak sesuai
function input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Cek apakah ada kiriman form dari method post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "config/database.php";
    // Mengambil username dan password
    $username = input($_POST["username"]);
    $password = input(md5($_POST["password"]));

    $cek_tabel_guru = mysqli_query($kon, "SELECT * FROM guru WHERE username='$username' AND password='$password' LIMIT 1");
    $guru = mysqli_num_rows($cek_tabel_guru);

    $cek_tabel_siswa = mysqli_query($kon, "SELECT * FROM siswa WHERE username='$username' AND password='$password' LIMIT 1");
    $siswa = mysqli_num_rows($cek_tabel_siswa);

    $cek_tabel_admin = mysqli_query($kon, "SELECT * FROM admin WHERE username='$username' AND password='$password' LIMIT 1");
    $admin = mysqli_num_rows($cek_tabel_admin);

    // jika rememberme di klik
    if (!empty($_POST["remember"])) {
        // buat cookie
        setcookie("username", $_POST["username"], time() + (3600 * 365 * 24 * 60 * 60));
        setcookie("password", $_POST["password"], time() + (3600 * 365 * 24 * 60 * 60));
    } else {
        if (isset($_COOKIE["username"])) {
            setcookie("username", "");
        }
        if (isset($_COOKIE["password"])) {
            setcookie("password", "");
        }
    }

    // Kondisi jika pengguna merupakan pengawas
    if ($guru > 0) {
        $row = mysqli_fetch_assoc($cek_tabel_guru);
        // menyimpan data pengawas dalam session
        $_SESSION["username"] = $row["username"];
        $_SESSION["id_guru"] = $row["id_guru"];
        $_SESSION["kode_guru"] = $row["kode_guru"];
        header("Location: index.php?page=beranda");
        exit;
    } else if ($admin > 0) {
        $row = mysqli_fetch_assoc($cek_tabel_admin);
        // menyimpan data admin dalam session
        $_SESSION["username"] = $row["username"];
        $_SESSION["id_admin"] = $row["id_admin"];
        $_SESSION["kode_admin"] = $row["kode_admin"];
        header("Location: index.php?page=beranda");
        exit;
    } else if ($siswa > 0) {
      $row = mysqli_fetch_assoc($cek_tabel_siswa);
      // menyimpan data peserta ujian dalam session
      $_SESSION["username"] = $row["username"];
      $_SESSION["id_siswa"] = $row["id_siswa"];
      $_SESSION["kode_siswa"] = $row["kode_siswa"];
  
      $id_siswa = $row["id_siswa"];
      require_once __DIR__ . '/config/siswa_ujian_aktif.php';
      $aktif_ujian = siswa_id_ujian_berlangsung($kon, (string) $id_siswa);
      if ($aktif_ujian > 0) {
          header("Location: pages/siswa/hasil/set-default.php?id=" . (int) $aktif_ujian);
          exit;
      }
      $q_selesai = mysqli_query($kon, "SELECT 1 FROM riwayat WHERE id_siswa='$id_siswa' LIMIT 1");
      // Hanya riwayat = pernah menyelesaikan ujian; baris nilai 0 dari "Kerjakan" bukan indikator selesai.
      $ada_hasil = ($q_selesai && mysqli_num_rows($q_selesai) > 0);
      if ($ada_hasil) {
          header("Location: index.php?page=hasil-ujian-siswa");
      } else {
          header("Location: index.php?page=ujian-siswa");
      }
      exit;
      
  } else {
      $pesan = "<div class='alert alert-danger'><strong>Error!</strong> Username dan password salah.</div>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <title>Login - Sistem Informasi UKOM Kataloger</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <style>
    body, html, p, h1, h2, h3, h4, h5, h6, div, span, a, button, input, select, textarea, label {
      font-family: 'Calibri', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }
    /* Pastikan icon Font Awesome tidak terpengaruh */
    i.fas, i.fab, i.far, i.fa {
      font-family: 'Font Awesome 5 Free', 'Font Awesome 5 Brands' !important;
      font-style: normal;
    }
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: url('https://media.istockphoto.com/id/1344749939/id/vektor/garis-garis-diagonal-merah-gelap-atau-merah-marun-bertekstur-kosong-latar-belakang-vektor.jpg?s=612x612&w=0&k=20&c=UrjKztGra1wzstprshVXiTZPPgHhnlV1I5PxDHdVamU=') center center / cover no-repeat;
      position: relative;
      overflow: hidden;
      font-family: 'Calibri', 'Segoe UI', 'Helvetica Neue', Arial, sans-serif !important;
    }
    
    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(139, 26, 26, 0.3) 0%, rgba(178, 34, 34, 0.2) 50%, rgba(139, 0, 0, 0.3) 100%);
      opacity: 0.6;
    }

    .login-container {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 450px;
      padding: 20px;
    }

    .login-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
      overflow: hidden;
      border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .login-header {
      background: linear-gradient(180deg, #8B1A1A 0%, #A52A2A 100%);
      padding: 40px 30px;
      text-align: center;
      color: white;
    }

    .logo-circle {
      width: 100px;
      height: 100px;
      background: radial-gradient(circle at 30% 30%, #FFF8DC, #FFD700 40%, #DAA520 70%, #B8860B);
      border-radius: 50%;
      margin: 0 auto 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3), inset 0 2px 10px rgba(255, 255, 255, 0.5);
      border: 4px solid rgba(255, 255, 255, 0.9);
      padding: 5px;
      position: relative;
    }

    .logo-circle::before {
      content: '';
      position: absolute;
      top: 10%;
      left: 20%;
      width: 40%;
      height: 40%;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.6), transparent);
      border-radius: 50%;
      filter: blur(8px);
    }

    .logo-circle img {
      width: 85px;
      height: 85px;
      object-fit: contain;
      position: relative;
      z-index: 1;
    }

    .login-header h2 {
      font-size: 20px;
      font-weight: 700;
      margin: 0 0 8px 0;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    .login-header p {
      font-size: 13px;
      margin: 0;
      opacity: 0.95;
      color: #FFD700;
      font-weight: 500;
    }

    .login-body {
      padding: 40px 35px;
    }

    .form-label {
      font-size: 13px;
      color: #666;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      font-weight: 600;
    }

    .form-label i {
      margin-right: 8px;
      color: #8B1A1A;
    }

    .input-group {
      position: relative;
      margin-bottom: 20px;
    }

    .input-icon {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #8B1A1A;
      z-index: 10;
    }

    .form-control-custom {
      width: 100%;
      padding: 14px 15px 14px 45px;
      border: 2px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
      transition: all 0.3s;
    }

    .form-control-custom:focus {
      outline: none;
      border-color: #8B1A1A;
      box-shadow: 0 0 0 3px rgba(139, 26, 26, 0.1);
    }

    .form-control-custom::placeholder {
      color: #999;
    }

    .checkbox-container {
      display: flex;
      align-items: center;
      margin: 20px 0;
    }

    .checkbox-container input[type="checkbox"] {
      width: 18px;
      height: 18px;
      margin-right: 8px;
      cursor: pointer;
    }

    .checkbox-container label {
      font-size: 14px;
      color: #666;
      margin: 0;
      cursor: pointer;
      user-select: none;
    }

    .btn-login {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #8B1A1A 0%, #A52A2A 100%);
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 16px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      cursor: pointer;
      transition: all 0.3s;
      box-shadow: 0 4px 15px rgba(139, 26, 26, 0.3);
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(139, 26, 26, 0.4);
      background: linear-gradient(135deg, #A52A2A 0%, #8B1A1A 100%);
    }

    .btn-login i {
      margin-right: 8px;
    }

    .alert-custom {
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .footer-text {
      text-align: center;
      color: white;
      font-size: 13px;
      margin-top: 30px;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
  </style>
</head>
<body>

<?php 
include 'config/database.php';
$hasil = mysqli_query($kon, "SELECT * FROM aplikasi LIMIT 1");
$aplikasi = mysqli_fetch_array($hasil); 
?>

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <div class="logo-circle">
          <img src="img/logokemhan.png" alt="Logo Kemhan RI">
        </div>
        <h2 style="font-size: 1.8rem; font-weight: 700;">SISTEM INFORMASI UKOM<br>KATALOGER</h2>
        <p>Pusat Kodifikasi Baloghan Kemhan RI</p>
      </div>
      
      <div class="login-body">
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($pesan)) { 
          echo str_replace('alert alert-danger', 'alert alert-danger alert-custom', $pesan);
        } ?>
        
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
          <div class="form-label">
            <i class="fas fa-user"></i> Username
          </div>
          <div class="input-group">
            <i class="fas fa-user input-icon"></i>
            <input type="text" name="username" class="form-control-custom" value="<?php if (isset($_COOKIE["username"])) { echo $_COOKIE["username"]; } ?>" placeholder="Masukkan username" required>
          </div>

          <div class="form-label">
            <i class="fas fa-lock"></i> Password
          </div>
          <div class="input-group">
            <i class="fas fa-shield-alt input-icon"></i>
            <input type="password" name="password" class="form-control-custom" value="<?php if (isset($_COOKIE["password"])) { echo $_COOKIE["password"]; } ?>" placeholder="Masukkan password" required>
          </div>

          <div class="checkbox-container">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Ingat saya</label>
          </div>

          <button type="submit" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> MASUK
          </button>
        </form>
      </div>
    </div>

    <div class="footer-text">
      © 2026 Kementerian Pertahanan RI. All rights reserved.
    </div>
  </div>

</body>
</html>
