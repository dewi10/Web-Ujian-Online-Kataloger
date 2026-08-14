<?php
//validasi hanya siswa yang boleh mengakses halaman ini
$username = $_SESSION['username'];
$cek = mysqli_query($kon, "select * from siswa where username='" . $username . "' limit 1");
$jum = mysqli_num_rows($cek);

if ($jum < 1) {
    echo "<br><div class='alert alert-danger'>TIDAK MEMILIKI HAK AKSES</div>";
    exit;
}
?>
<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-file-signature"></i> PAK (Penilaian Angka Kredit)</h3>
    </div>
    <div class="card-body">
        <script>
            $('title').text('PAK');
        </script>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="pakTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="angka-kredit-tab" data-toggle="tab" href="#angka-kredit" role="tab" aria-controls="angka-kredit" aria-selected="true">Angka Kredit JF</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="konversi-tab" data-toggle="tab" href="#konversi" role="tab" aria-controls="konversi" aria-selected="false">Konversi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="akumulasi-tab" data-toggle="tab" href="#akumulasi" role="tab" aria-controls="akumulasi" aria-selected="false">AKUMULASI</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pak-tab" data-toggle="tab" href="#pak" role="tab" aria-controls="pak" aria-selected="false">PAK</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="hitung-tab" data-toggle="tab" href="#hitung" role="tab" aria-controls="hitung" aria-selected="false">Hitung</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content mt-3">
            <!-- Tab Angka Kredit JF -->
            <div class="tab-pane fade show active" id="angka-kredit" role="tabpanel" aria-labelledby="angka-kredit-tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">A. ANGKA KREDIT JF</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="text-white text-center">
                                    <tr>
                                        <th rowspan="2">Kategori</th>
                                        <th rowspan="2">Jenjang</th>
                                        <th rowspan="2">Pangkat</th>
                                        <th rowspan="2">Koefisien<br>Angka Kredit<br>Tahunan</th>
                                        <th colspan="2">Angka Kredit Kumulatif<br>Minimal Kenakan</th>
                                    </tr>
                                    <tr>
                                        <th>PANGKAT</th>
                                        <th>JENJANG*</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td rowspan="4" class="align-middle"><strong>Ahli</strong></td>
                                        <td>Ahli Utama</td>
                                        <td>IV/d – IV/e</td>
                                        <td class="text-center">50</td>
                                        <td class="text-center">200</td>
                                        <td class="text-center">-</td>
                                    </tr>
                                    <tr>
                                        <td>Ahli Madya</td>
                                        <td>IV/a – IV/b – IV/c</td>
                                        <td class="text-center">37,5</td>
                                        <td class="text-center">150</td>
                                        <td class="text-center">450</td>
                                    </tr>
                                    <tr>
                                        <td>Ahli Muda</td>
                                        <td>III/c – III/d</td>
                                        <td class="text-center">25</td>
                                        <td class="text-center">100</td>
                                        <td class="text-center">200</td>
                                    </tr>
                                    <tr>
                                        <td>Ahli Pertama</td>
                                        <td>III/a – III/b</td>
                                        <td class="text-center">12,5</td>
                                        <td class="text-center">50</td>
                                        <td class="text-center">100</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="4" class="align-middle"><strong>Keterampilan</strong></td>
                                        <td>Penyelia</td>
                                        <td>III/c – III/d</td>
                                        <td class="text-center">25</td>
                                        <td class="text-center">100</td>
                                        <td class="text-center">-</td>
                                    </tr>
                                    <tr>
                                        <td>Mahir</td>
                                        <td>III/a – III/b</td>
                                        <td class="text-center">12,5</td>
                                        <td class="text-center">50</td>
                                        <td class="text-center">100</td>
                                    </tr>
                                    <tr>
                                        <td>Terampil</td>
                                        <td>II/b – II/c – II/d</td>
                                        <td class="text-center">5</td>
                                        <td class="text-center">20</td>
                                        <td class="text-center">60</td>
                                    </tr>
                                    <tr>
                                        <td>Pemula</td>
                                        <td>II/a</td>
                                        <td class="text-center">3,75</td>
                                        <td class="text-center">15</td>
                                        <td class="text-center">15</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="text-muted small"><em>*dapat bersifat proporsional berdasarkan pangkat awal jenjang jabatan pada saat penetapan JF</em></p>
                        </div>

                        <h5 class="mb-4 mt-5">B. KONVERSI PREDIKAT KINERJA TAHUNAN MENJADI ANGKA KREDIT TAHUNAN</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="text-white text-center">
                                    <tr>
                                        <th rowspan="2">Simulasi<br>per<br>tahun</th>
                                        <th rowspan="2">Koefisien per<br>tahun</th>
                                        <th colspan="5">Angka Kredit yang Diperoleh per Tahun</th>
                                    </tr>
                                    <tr>
                                        <th>Sangat<br>Baik<br>100%</th>
                                        <th>Baik<br>75%</th>
                                        <th>Butuh<br>Perbaikan<br>50%</th>
                                        <th>Kurang<br>25%</th>
                                        <th>Sangat<br>Kurang<br>25%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td rowspan="4" class="align-middle"><strong>Ahli</strong></td>
                                        <td>Ahli Pertama<br>12,5</td>
                                        <td class="text-center">18,75</td>
                                        <td class="text-center">12,5</td>
                                        <td class="text-center">9,38</td>
                                        <td class="text-center">6,25</td>
                                        <td class="text-center">3,13</td>
                                    </tr>
                                    <tr>
                                        <td>Ahli Muda<br>25</td>
                                        <td class="text-center">37,50</td>
                                        <td class="text-center">25</td>
                                        <td class="text-center">18,75</td>
                                        <td class="text-center">12,50</td>
                                        <td class="text-center">6,25</td>
                                    </tr>
                                    <tr>
                                        <td>Ahli Madya<br>37,5</td>
                                        <td class="text-center">56,25</td>
                                        <td class="text-center">37,5</td>
                                        <td class="text-center">28,13</td>
                                        <td class="text-center">18,75</td>
                                        <td class="text-center">9,375</td>
                                    </tr>
                                    <tr>
                                        <td>Ahli Utama<br>50</td>
                                        <td class="text-center">75</td>
                                        <td class="text-center">50</td>
                                        <td class="text-center">37,50</td>
                                        <td class="text-center">25</td>
                                        <td class="text-center">12,50</td>
                                    </tr>
                                    <tr>
                                        <td rowspan="4" class="align-middle"><strong>Keterampilan</strong></td>
                                        <td>Pemula<br>3,75</td>
                                        <td class="text-center">5,63</td>
                                        <td class="text-center">3,75</td>
                                        <td class="text-center">2,81</td>
                                        <td class="text-center">1,88</td>
                                        <td class="text-center">0,94</td>
                                    </tr>
                                    <tr>
                                        <td>Terampil<br>5</td>
                                        <td class="text-center">7,50</td>
                                        <td class="text-center">5</td>
                                        <td class="text-center">3,75</td>
                                        <td class="text-center">2,50</td>
                                        <td class="text-center">1,25</td>
                                    </tr>
                                    <tr>
                                        <td>Mahir<br>12,5</td>
                                        <td class="text-center">18,75</td>
                                        <td class="text-center">12,5</td>
                                        <td class="text-center">9,38</td>
                                        <td class="text-center">6,25</td>
                                        <td class="text-center">3,13</td>
                                    </tr>
                                    <tr>
                                        <td>Penyelia<br>25</td>
                                        <td class="text-center">37,50</td>
                                        <td class="text-center">25</td>
                                        <td class="text-center">18,75</td>
                                        <td class="text-center">12,5</td>
                                        <td class="text-center">6,25</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab Konversi -->
            <div class="tab-pane fade" id="konversi" role="tabpanel" aria-labelledby="konversi-tab">
                <?php
                // Ambil data siswa
                $id_siswa = $_SESSION['id_siswa'];
                $query_siswa = mysqli_query($kon, "SELECT * FROM siswa WHERE id_siswa='$id_siswa'");
                $data_siswa = mysqli_fetch_array($query_siswa);

                // Ambil data konversi jika ada
                $query_konversi = mysqli_query($kon, "SELECT * FROM pak_konversi WHERE id_siswa='$id_siswa' LIMIT 1");
                $data_konversi = mysqli_fetch_array($query_konversi);
                ?>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"></h5>
                    <div>
                        <button onclick="toggleEditKonversi()" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button onclick="printKonversi()" class="btn btn-primary btn-sm">
                            <i class="fas fa-print"></i> Cetak
                        </button>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <!-- Header & Action Buttons -->


                        <!-- Form Edit (Hidden by default) -->
                        <div id="editFormKonversi" style="display:none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Mode Edit - Silakan ubah data dan klik Simpan
                            </div>
                            <!-- Header Instansi -->
                            <div class="text-center mb-4" style="border-bottom: 3px solid #000; padding-bottom: 10px;">
                                <h6 class="mb-1 font-weight-bold">KEMENTERIAN PERTAHANAN RI</h6>
                                <h6 class="mb-1 font-weight-bold">BADAN LOGISTIK PERTAHANAN</h6>
                                <h5 class="mt-3 font-weight-bold">KONVERSI PREDIKAT KINERJA KE ANGKA KREDIT</h5>
                            </div>

                            <!-- Form Data -->
                            <form id="formKonversi">
                                <input type="hidden" name="id_siswa" value="<?php echo $id_siswa; ?>">

                                <!-- Nomor & Instansi -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Nomor</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="nomor_dokumen"
                                                    value="<?php echo $data_konversi['nomor_dokumen'] ?? 'KPK-AK/ /XII/2025'; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Instansi/Kementerian</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="instansi_kementerian"
                                                    value="<?php echo $data_konversi['instansi_kementerian'] ?? ($data_siswa['instansi'] ?? 'Pertahanan'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Periode -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-4 col-form-label">Periode</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="periode"
                                                    value="<?php echo $data_konversi['periode'] ?? '02 Januari - 31 Desember 2025'; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section: Pejabat Fungsional Yang Dinilai -->
                                <h6 class="font-weight-bold mb-3 mt-4">PEJABAT FUNGSIONAL YANG DINILAI</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                            <tr>
                                                <td width="5%" class="text-center font-weight-bold">1</td>
                                                <td width="30%">Nama</td>
                                                <td class="bg-light">
                                                    <?php echo $data_siswa['nama_siswa'] ?? '-'; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center font-weight-bold">2</td>
                                                <td>NIP</td>
                                                <td class="bg-light">
                                                    <?php echo $data_siswa['nis'] ?? '-'; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center font-weight-bold">3</td>
                                                <td>Nomor Seri Karpeg</td>
                                                <td class="bg-light">
                                                    <?php echo $data_siswa['nomor_seri_karpeg'] ?? '-'; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center font-weight-bold">4</td>
                                                <td>Tempat, Tanggal Lahir</td>
                                                <td class="bg-light">
                                                    <?php
                                                    $tempat = $data_siswa['tempat_lahir'] ?? '';
                                                    $tanggal = $data_siswa['tanggal_lahir'] ?? '';
                                                    if ($tanggal) {
                                                        $tanggal = date('d-m-Y', strtotime($tanggal));
                                                    }
                                                    echo $tempat . ($tempat && $tanggal ? ', ' : '') . $tanggal;
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center font-weight-bold">5</td>
                                                <td>Jenis Kelamin</td>
                                                <td class="bg-light">
                                                    <?php
                                                    $jk = $data_siswa['jk'] ?? '';
                                                    if ($jk == 1) {
                                                        echo 'Laki-laki';
                                                    } elseif ($jk == 2) {
                                                        echo 'Perempuan';
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center font-weight-bold">6</td>
                                                <td>Pangkat/Gol. Ruang</td>
                                                <td class="bg-light">
                                                    <?php echo $data_siswa['pangkat_gol'] ?? '-'; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center font-weight-bold">7</td>
                                                <td>Jabatan</td>
                                                <td class="bg-light">
                                                    <?php echo $data_siswa['jabatan'] ?? '-'; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center font-weight-bold">8</td>
                                                <td>Unit Kerja</td>
                                                <td class="bg-light">
                                                    <?php echo $data_siswa['unit_kerja'] ?? '-'; ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Section: Konversi Predikat Kinerja -->
                                <h6 class="font-weight-bold mb-3 mt-4">KONVERSI PREDIKAT KINERJA KE ANGKA KREDIT</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead class="text-center align-middle" style="background-color: #e9ecef;">
                                            <tr>
                                                <th rowspan="2">No</th>
                                                <th rowspan="2">Hasil Penilaian Kinerja</th>
                                                <th rowspan="2">Perilaku</th>
                                                <th rowspan="2">Presentase (%)</th>
                                                <th rowspan="2">Koefisien Per Tahun</th>
                                                <th colspan="2">AK yang diperoleh</th>
                                            </tr>
                                            <tr>
                                                <th>Presentase x Koefisien</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $hasil_options = ['Sangat Baik', 'Baik', 'Butuh Perbaikan', 'Kurang', 'Sangat Kurang'];
                                            $presentase_default = [100, 75, 50, 25, 25];

                                            for ($i = 1; $i <= 5; $i++) {
                                                $hasil = $data_konversi['hasil_' . $i] ?? $hasil_options[$i - 1];
                                                $perilaku = $data_konversi['perilaku_' . $i] ?? '';
                                                $presentase = $data_konversi['presentase_' . $i] ?? $presentase_default[$i - 1];
                                                $koefisien = $data_konversi['koefisien_' . $i] ?? '';
                                                $keterangan = $data_konversi['keterangan_' . $i] ?? '';
                                            ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $i; ?></td>
                                                    <td>
                                                        <select class="form-control form-control-sm" name="hasil_<?php echo $i; ?>">
                                                            <?php foreach ($hasil_options as $opt) { ?>
                                                                <option value="<?php echo $opt; ?>" <?php echo ($hasil == $opt) ? 'selected' : ''; ?>>
                                                                    <?php echo $opt; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="perilaku_<?php echo $i; ?>"
                                                            value="<?php echo $perilaku; ?>">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm text-center presentase-input"
                                                            name="presentase_<?php echo $i; ?>"
                                                            value="<?php echo $presentase; ?>"
                                                            data-row="<?php echo $i; ?>"
                                                            step="0.01">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control form-control-sm text-center koefisien-input"
                                                            name="koefisien_<?php echo $i; ?>"
                                                            value="<?php echo $koefisien; ?>"
                                                            data-row="<?php echo $i; ?>"
                                                            step="0.01">
                                                    </td>
                                                    <td class="text-center bg-light">
                                                        <span id="ak_hasil_<?php echo $i; ?>">
                                                            <?php
                                                            if ($presentase && $koefisien) {
                                                                echo number_format(($presentase / 100) * $koefisien, 2);
                                                            } else {
                                                                echo '0.00';
                                                            }
                                                            ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="keterangan_<?php echo $i; ?>"
                                                            value="<?php echo $keterangan; ?>">
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Tanda Tangan -->
                                <div class="row mt-5">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Yang Dinilai</label>
                                            <input type="text" class="form-control" name="ttd_yang_dinilai"
                                                value="<?php echo $data_konversi['ttd_yang_dinilai'] ?? ''; ?>"
                                                placeholder="Nama & NIP">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Pejabat Penilai Kinerja</label>
                                            <input type="text" class="form-control" name="ttd_penilai"
                                                value="<?php echo $data_konversi['ttd_penilai'] ?? ''; ?>"
                                                placeholder="Nama & NIP">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tanggal</label>
                                            <input type="date" class="form-control" name="tanggal_ttd_yang_dinilai"
                                                value="<?php echo $data_konversi['tanggal_ttd_yang_dinilai'] ?? ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tanggal</label>
                                            <input type="date" class="form-control" name="tanggal_ttd_penilai"
                                                value="<?php echo $data_konversi['tanggal_ttd_penilai'] ?? ''; ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol Simpan -->
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Simpan Data
                                    </button>
                                    <button type="button" class="btn btn-secondary" onclick="toggleEditKonversi()">
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Preview (Default view) -->
                        <div id="konversiPreview">
                            <!-- Header dengan Logo di Tengah -->
                            <div class="d-flex justify-content-center align-items-start mb-3">
                                <div class="mr-3" style="margin-top: -20px;">
                                    <img src="img/logokemhan.png" alt="Logo Kemhan" style="width: 85px; height: auto;">
                                </div>
                                <div class="text-center">
                                    <h6 class="mb-0 font-weight-bold" style="font-size: 15px;">KEMENTERIAN PERTAHANAN RI</h6>
                                    <h6 class="mb-2 font-weight-bold" style="font-size: 15px;">BADAN LOGISTIK PERTAHANAN</h6>
                                    <h6 class="mt-2 font-weight-bold" style="font-size: 13px; text-decoration: underline;">KONVERSI PREDIKAT KINERJA KE ANGKA KREDIT</h6>
                                    <p class="mb-0" style="font-size: 12px;">NOMOR : <span id="display_nomor"><?php echo $data_konversi['nomor_dokumen'] ?? 'KPKAK/ /I/2025'; ?></span></p>
                                </div>
                            </div>

                            <!-- Instansi dan Periode dalam 1 baris -->
                            <div class="row mb-3" style="font-size: 11px;">
                                <div class="col-6">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="width: 100px;">Instansi</td>
                                            <td style="width: 10px;">:</td>
                                            <td id="display_instansi"><?php echo $data_konversi['instansi_kementerian'] ?? ($data_siswa['instansi'] ?? 'Kementerian Pertahanan'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td style="width: 80px;">Periode</td>
                                            <td style="width: 10px;">:</td>
                                            <td id="display_periode"><?php echo $data_konversi['periode'] ?? '1 September - 31 Desember 2025'; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Tabel Pejabat Fungsional Yang Dinilai -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" style="font-size: 11px;">
                                    <thead style="background-color: #d3d3d3;">
                                        <tr>
                                            <th colspan="3" class="text-center font-weight-bold">PEJABAT FUNGSIONAL YANG DINILAI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td width="5%" class="text-center">1</td>
                                            <td width="30%">Nama</td>
                                            <td>: <?php echo $data_siswa['nama_siswa'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">2</td>
                                            <td>NIP</td>
                                            <td>: <?php echo $data_siswa['nis'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">3</td>
                                            <td>Nomor Seri Karpeg</td>
                                            <td>: <?php echo $data_siswa['nomor_seri_karpeg'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">4</td>
                                            <td>Tempat, Tanggal Lahir</td>
                                            <td>: <?php
                                                    $tempat = $data_siswa['tempat_lahir'] ?? '';
                                                    $tanggal = $data_siswa['tanggal_lahir'] ?? '';
                                                    if ($tanggal) {
                                                        $tanggal = date('d F Y', strtotime($tanggal));
                                                    }
                                                    echo $tempat . ($tempat && $tanggal ? ', ' : '') . $tanggal;
                                                    ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">5</td>
                                            <td>Jenis Kelamin</td>
                                            <td>: <?php
                                                    $jk = $data_siswa['jk'] ?? '';
                                                    if ($jk == 1) {
                                                        echo 'Laki-laki';
                                                    } elseif ($jk == 2) {
                                                        echo 'Perempuan';
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">6</td>
                                            <td>Pangkat/Golongan Ruang/ TMT</td>
                                            <td>: <?php echo $data_siswa['pangkat_gol'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">7</td>
                                            <td>Jabatan/ TMT</td>
                                            <td>: <?php echo $data_siswa['jabatan'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">8</td>
                                            <td>Unit Kerja</td>
                                            <td>: <?php echo $data_siswa['unit_kerja'] ?? '-'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">9</td>
                                            <td>Instansi</td>
                                            <td>: <?php echo $data_siswa['instansi'] ?? 'Kementerian Pertahanan'; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tabel Konversi -->
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-sm" style="font-size: 11px;">
                                    <thead style="background-color: #d3d3d3;">
                                        <tr>
                                            <th colspan="4" class="text-center font-weight-bold">KONVERSI PREDIKAT KINERJA KE ANGKA KREDIT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td rowspan="2" style="vertical-align: middle; font-weight: 600;">Hasil Penilaian Kinerja</td>
                                            <td rowspan="2" style="vertical-align: middle; font-weight: 600;">Persentase</td>
                                            <td rowspan="2" style="vertical-align: middle; font-weight: 600;">Koefisien Per Tahun</td>
                                            <td rowspan="2" style="vertical-align: middle; font-weight: 600;">AK yang diperoleh<br>(Kolom 2 X Kolom 3)</td>
                                        </tr>
                                        <tr></tr>
                                        <tr class="text-center">
                                            <td>Predikat</td>
                                            <td>Persentase</td>
                                            <td>Koefisien</td>
                                            <td>AK</td>
                                        </tr>
                                        <tr class="text-center">
                                            <td>1</td>
                                            <td>2</td>
                                            <td>3</td>
                                            <td>4</td>
                                        </tr>
                                        <?php
                                        // Tampilkan 1 baris data (atau lebih sesuai kebutuhan)
                                        $hasil = $data_konversi['hasil_1'] ?? 'Baik';
                                        $perilaku = $data_konversi['perilaku_1'] ?? '';
                                        $presentase = $data_konversi['presentase_1'] ?? 100;
                                        $koefisien = $data_konversi['koefisien_1'] ?? 37.5;
                                        $ak_hasil = ($presentase && $koefisien) ? number_format(($presentase / 100) * $koefisien, 1) : '0.0';
                                        ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php echo $hasil; ?>
                                                <?php if ($perilaku) { ?>
                                                    <br><small style="font-size: 9px;"><?php echo $perilaku; ?></small>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">x <?php echo $presentase; ?>%</td>
                                            <td class="text-center"><?php echo $koefisien; ?></td>
                                            <td class="text-center"><?php echo $ak_hasil; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer dengan Tanda Tangan -->
                            <div class="row mt-4" style="font-size: 11px;">
                                <div class="col-6">
                                    <p class="mb-1">Ditetapkan di Jakarta</p>
                                    <p class="mb-1">Pada Tanggal : <?php echo $data_konversi['tanggal_ttd_yang_dinilai'] ? date('d F Y', strtotime($data_konversi['tanggal_ttd_yang_dinilai'])) : 'Januari 2026'; ?></p>
                                    <br><br><br>
                                    <p class="mb-0">a.n. <?php echo $data_konversi['ttd_yang_dinilai'] ?? 'Kepala Badan Logistik Pertahanan'; ?></p>
                                    <p class="mb-0">Kepala <?php echo $data_siswa['unit_kerja'] ?? 'Pusat Kodifikasi'; ?>,</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1 text-right"><?php echo $data_konversi['ttd_penilai'] ?? 'Tiana Kurniawan'; ?></p>
                                    <p class="mb-1 text-right">Marsekal Pertama TNI</p>
                                </div>
                            </div>

                            <!-- Tembusan -->
                            <div class="mt-4" style="font-size: 10px;">
                                <p class="mb-1 font-weight-bold">Tembusan:</p>
                                <ol style="padding-left: 20px; margin: 0;">
                                    <li>Sekretaris Jenderal Komhan</li>
                                    <li>Kabagbhyan Kemhan</li>
                                    <li>Sesbaloghan Kemhan</li>
                                    <li>Kasatker Baloghan Kemhan</li>
                                    <li>Sakretaris Tim Penilai yang bersangkutan</li>
                                    <li>Katalogeer yang bersangkutan.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab AKUMULASI -->
            <div class="tab-pane fade" id="akumulasi" role="tabpanel" aria-labelledby="akumulasi-tab">
                <div class="card">
                    <div class="card-body">
                        <h5>AKUMULASI</h5>
                        <p>Konten untuk akumulasi angka kredit akan ditampilkan di sini.</p>
                    </div>
                </div>
            </div>

            <!-- Tab PAK -->
            <div class="tab-pane fade" id="pak" role="tabpanel" aria-labelledby="pak-tab">
                <div class="card">
                    <div class="card-body">
                        <h5>PAK</h5>
                        <p>Konten untuk penilaian angka kredit akan ditampilkan di sini.</p>
                    </div>
                </div>
            </div>

            <!-- Tab Hitung -->
            <div class="tab-pane fade" id="hitung" role="tabpanel" aria-labelledby="hitung-tab">
                <div class="card">
                    <div class="card-body">
                        <h5>Hitung</h5>
                        <p>Konten untuk perhitungan angka kredit akan ditampilkan di sini.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        color: #495057;
    }

    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
    }

    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: bold;
    }

    .table thead {
        background: linear-gradient(135deg, #8b1a1a 0%, #c92a2a 100%);
    }

    @media print {

        .btn,
        .nav-tabs,
        .sidebar,
        .navbar {
            display: none !important;
        }

        #konversi-content {
            width: 100%;
        }

        .table {
            font-size: 12px;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>

<script>
    // Fungsi Toggle Edit Mode
    function toggleEditKonversi() {
        var editForm = document.getElementById('editFormKonversi');
        var preview = document.getElementById('konversiPreview');

        if (editForm.style.display === 'none') {
            editForm.style.display = 'block';
            preview.style.display = 'none';
        } else {
            editForm.style.display = 'none';
            preview.style.display = 'block';
        }
    }

    // Fungsi Print Konversi
    function printKonversi() {
        var printContents = document.getElementById('konversiPreview').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }

    // Perhitungan AK Otomatis
    $(document).ready(function() {
        // Event listener untuk perubahan presentase dan koefisien
        $('.presentase-input, .koefisien-input').on('input', function() {
            var row = $(this).data('row');
            hitungAK(row);
        });

        function hitungAK(row) {
            var presentase = parseFloat($('input[name="presentase_' + row + '"]').val()) || 0;
            var koefisien = parseFloat($('input[name="koefisien_' + row + '"]').val()) || 0;
            var hasil = (presentase / 100) * koefisien;
            $('#ak_hasil_' + row).text(hasil.toFixed(2));
        }

        // Submit form konversi
        $('#formKonversi').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: 'pages/siswa/pak/save-konversi.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            });
        });
    });
</script>