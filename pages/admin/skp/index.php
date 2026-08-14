<?php
    // SKP dapat diakses oleh semua user
    $username = $_SESSION['username'];
?>

<style>
/* Print styles */
@media print {
    .btn, .nav-tabs, .card-header, .dashboard-header, #wrapper, #sidebar-wrapper {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .table-bordered {
        border: 1px solid #000 !important;
    }
    
    .table-bordered td, .table-bordered th {
        border: 1px solid #000 !important;
    }
    
    body {
        font-size: 11pt;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}

/* Better table styling */
.table-bordered {
    border: 1px solid #dee2e6;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
    padding: 0.5rem;
    vertical-align: top;
}

.table-sm td, .table-sm th {
    padding: 0.3rem;
}
</style>
<br>
<div class="card border-0">
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <h3><i class="fas fa-file-alt"></i> SKP (Sasaran Kinerja Pegawai)</h3>
        <button onclick="printAllSKP()" class="btn btn-success">
            <i class="fas fa-print"></i> Print Semua
        </button>
    </div>
    <div class="card-body">
        <script> $('title').text('SKP'); </script>
        
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="skpTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="data-tab" data-toggle="tab" href="#data" role="tab" aria-controls="data" aria-selected="true">DATA</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="skp-jaif-tab" data-toggle="tab" href="#skp-jaif" role="tab" aria-controls="skp-jaif" aria-selected="false">SKP JAIF (Kualitatif)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="lampiran-skp-tab" data-toggle="tab" href="#lampiran-skp" role="tab" aria-controls="lampiran-skp" aria-selected="false">Lampiran SKP</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="evaluasi-kinerja-tab" data-toggle="tab" href="#evaluasi-kinerja" role="tab" aria-controls="evaluasi-kinerja" aria-selected="false">Evaluasi Kinerja Kualitatif</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="cover-tab" data-toggle="tab" href="#cover" role="tab" aria-controls="cover" aria-selected="false">COVER</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="dok-evaluasi-tab" data-toggle="tab" href="#dok-evaluasi" role="tab" aria-controls="dok-evaluasi" aria-selected="false">Dok. Evaluasi Kinerja Pegawai</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="kuadran-tab" data-toggle="tab" href="#kuadran" role="tab" aria-controls="kuadran" aria-selected="false">Kuadran</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pola-distribusi-contoh-tab" data-toggle="tab" href="#pola-distribusi-contoh" role="tab" aria-controls="pola-distribusi-contoh" aria-selected="false">Pola Distribusi (Contoh)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pola-distribusi-tab" data-toggle="tab" href="#pola-distribusi" role="tab" aria-controls="pola-distribusi" aria-selected="false">Pola Distribusi</a>
            </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="data" role="tabpanel" aria-labelledby="data-tab">
                <?php
                    // Get current user data from admin table
                    $query_current_user = mysqli_query($kon, "SELECT * FROM admin WHERE username='$username' LIMIT 1");
                    $current_user = mysqli_fetch_array($query_current_user);
                    
                    // Get data pegawai from skp_data_pegawai
                    $query_pegawai = mysqli_query($kon, "SELECT * FROM skp_data_pegawai WHERE username='$username' LIMIT 1");
                    $data_pegawai = mysqli_fetch_array($query_pegawai);
                ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Data Pegawai dan Pejabat Penilai</h5>
                        
                        <form id="formDataPegawai">
                            <!-- Data Pegawai yang Dinilai -->
                            <div class="border p-3 mb-4">
                                <h6 class="font-weight-bold mb-3" style="background-color: #4472C4; color: white; padding: 8px;">DATA PEGAWAI YANG DINILAI</h6>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">NAMA</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control bg-light" name="nama_pegawai" value="<?php echo isset($current_user['nama']) ? $current_user['nama'] : ''; ?>" readonly>
                                        <small class="text-muted">Data dari akun yang login</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">NIP</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control bg-light" name="nip_pegawai" value="<?php echo isset($current_user['nip']) ? $current_user['nip'] : $username; ?>" readonly>
                                        <small class="text-muted">Data dari akun yang login</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">PANGKAT/GOL. RUANG</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control bg-light" name="pangkat_gol_pegawai" value="<?php echo isset($current_user['pangkat_gol']) ? $current_user['pangkat_gol'] : ''; ?>" readonly>
                                        <small class="text-muted">Data dari akun yang login</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">JABATAN</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control bg-light" name="jabatan_pegawai" value="<?php echo isset($current_user['jabatan']) ? $current_user['jabatan'] : ''; ?>" readonly>
                                        <small class="text-muted">Data dari akun yang login</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">UNIT KERJA</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control bg-light" name="unit_kerja_pegawai" value="<?php echo isset($current_user['unit_kerja']) ? $current_user['unit_kerja'] : ''; ?>" readonly>
                                        <small class="text-muted">Data dari akun yang login</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Data Pejabat Penilai Kinerja -->
                            <div class="border p-3 mb-4">
                                <h6 class="font-weight-bold mb-3" style="background-color: #70AD47; color: white; padding: 8px;">PEJABAT PENILAI KINERJA</h6>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">NAMA</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nama_penilai" value="<?php echo isset($data_pegawai['nama_penilai']) ? $data_pegawai['nama_penilai'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">NRP</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nip_penilai" value="<?php echo isset($data_pegawai['nip_penilai']) ? $data_pegawai['nip_penilai'] : ''; ?>">
                                        <small class="form-text text-muted">NRP/NIP Pejabat Penilai</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">PANGKAT/GOL. RUANG</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="pangkat_gol_penilai" value="<?php echo isset($data_pegawai['pangkat_gol_penilai']) ? $data_pegawai['pangkat_gol_penilai'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">JABATAN</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="jabatan_penilai" value="<?php echo isset($data_pegawai['jabatan_penilai']) ? $data_pegawai['jabatan_penilai'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">UNIT KERJA</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="unit_kerja_penilai" value="<?php echo isset($data_pegawai['unit_kerja_penilai']) ? $data_pegawai['unit_kerja_penilai'] : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Data Atasan Pejabat Penilai Kinerja -->
                            <div class="border p-3 mb-4">
                                <h6 class="font-weight-bold mb-3" style="background-color: #FFC000; color: black; padding: 8px;">ATASAN PEJABAT PENILAI KINERJA</h6>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">NAMA</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nama_atasan" value="<?php echo isset($data_pegawai['nama_atasan']) ? $data_pegawai['nama_atasan'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">NRP</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="nip_atasan" value="<?php echo isset($data_pegawai['nip_atasan']) ? $data_pegawai['nip_atasan'] : ''; ?>">
                                        <small class="form-text text-muted">NRP/NIP Atasan Pejabat Penilai</small>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">PANGKAT/GOL. RUANG</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="pangkat_gol_atasan" value="<?php echo isset($data_pegawai['pangkat_gol_atasan']) ? $data_pegawai['pangkat_gol_atasan'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">JABATAN</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="jabatan_atasan" value="<?php echo isset($data_pegawai['jabatan_atasan']) ? $data_pegawai['jabatan_atasan'] : ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label font-weight-bold">UNIT KERJA</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="unit_kerja_atasan" value="<?php echo isset($data_pegawai['unit_kerja_atasan']) ? $data_pegawai['unit_kerja_atasan'] : ''; ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <script>
                $(document).ready(function() {
                    $('#formDataPegawai').on('submit', function(e) {
                        e.preventDefault();
                        
                        $.ajax({
                            url: 'pages/admin/skp/save-data.php',
                            type: 'POST',
                            data: $(this).serialize(),
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    alert('Data berhasil disimpan!');
                                    location.reload();
                                } else {
                                    alert('Error: ' + response.message);
                                }
                            },
                            error: function() {
                                alert('Terjadi kesalahan saat menyimpan data');
                            }
                        });
                    });
                });
                </script>
            </div>

            <div class="tab-pane fade" id="skp-jaif" role="tabpanel" aria-labelledby="skp-jaif-tab">
                <?php
                    // Get current user
                    $username = $_SESSION['username'];
                    $query_user = mysqli_query($kon, "SELECT * FROM admin WHERE username='".$username."' LIMIT 1");
                    $user_data = mysqli_fetch_array($query_user);
                    
                    // Get SKP data
                    $query_skp = mysqli_query($kon, "SELECT * FROM skp_data WHERE username='".$username."' LIMIT 1");
                    $skp_data = mysqli_fetch_array($query_skp);
                    
                    // Get Hasil Kerja items
                    $query_hasil_a = mysqli_query($kon, "SELECT * FROM skp_hasil_kerja WHERE kategori='A' ORDER BY urutan ASC");
                    $query_hasil_b = mysqli_query($kon, "SELECT * FROM skp_hasil_kerja WHERE kategori='B' ORDER BY urutan ASC");
                    
                    // Get Perilaku Kerja items
                    $query_perilaku = mysqli_query($kon, "SELECT * FROM skp_perilaku_kerja ORDER BY urutan ASC");
                    
                    // Get Pejabat Penilai data
                    $query_pejabat = mysqli_query($kon, "SELECT * FROM skp_pejabat_penilai LIMIT 1");
                    if($pejabat_data = mysqli_fetch_array($query_pejabat)) {
                        // Data from database
                    } else {
                        // Default data
                        $pejabat_data = [
                            'puskod' => 'PUSKOD BALOGHAN KEMHAN',
                            'periode_awal' => '2025-01-02',
                            'periode_akhir' => '2025-12-31',
                            'pejabat_nama' => 'Tisna Kurniawan',
                            'pejabat_nip' => '518837',
                            'pejabat_pangkat' => 'Marsekal Pertama TNI',
                            'pejabat_jabatan' => 'Kepala Pusat Kodifikasi',
                            'pejabat_unit' => 'Baloghan Kemhan',
                            'atasan_nama' => 'Yusuf Jauhari, M.Eng',
                            'atasan_nip' => '514557',
                            'atasan_pangkat' => 'Marsekal Madya TNI',
                            'atasan_jabatan' => 'Kepala Badan Logistik Pertahanan',
                            'atasan_unit' => 'Baloghan Kemhan'
                        ];
                    }
                ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h5 class="font-weight-bold mb-1">SASARAN KINERJA PEGAWAI</h5>
                            <h6 class="mb-1">JABATAN FUNGSIONAL KATALOGER AHLI MADYA</h6>
                            <h6 class="mb-3">PENDEKATAN HASIL KERJA KUALITATIF</h6>
                        </div>
                        
                        <!-- Tombol Edit Pejabat Penilai -->
                        <div class="text-right mb-3">
                            <button class="btn btn-success btn-sm" onclick="window.print()">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editPejabatPenilai()">
                                <i class="fas fa-edit"></i> Edit Info Pejabat Penilai
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="toggleManageHasilKerja()">
                                <i class="fas fa-tasks"></i> Kelola Hasil Kerja
                            </button>
                            <button class="btn btn-info btn-sm" onclick="toggleManagePerilaku()">
                                <i class="fas fa-user-check"></i> Kelola Perilaku Kerja
                            </button>
                        </div>
                        
                        <!-- Info Header dengan Border -->
                        <table class="table table-bordered table-sm mb-3">
                            <tbody>
                                <tr>
                                    <td width="50%" class="p-0">
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <td colspan="3" class="bg-light font-weight-bold">
                                                    <span id="display_puskod"><?php echo $pejabat_data['puskod']; ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30" class="bg-light font-weight-bold border-top-0">NO</td>
                                                <td colspan="2" class="bg-light font-weight-bold border-top-0">PEGAWAI YANG DINILAI</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td width="150">NAMA</td>
                                                <td><?php echo $user_data['nama_lengkap']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">2</td>
                                                <td>NIP</td>
                                                <td><?php echo $user_data['username']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">3</td>
                                                <td>PANGKAT/GOL. RUANG</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">4</td>
                                                <td>JABATAN</td>
                                                <td>Kataloger Ahli Madya Puskod</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">5</td>
                                                <td>UNIT KERJA</td>
                                                <td>Baloghan Kemhan</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" class="p-0 border-left">
                                        <table class="table table-sm mb-0">
                                            <tr>
                                                <td colspan="3" class="bg-light font-weight-bold">
                                                    PERIODE PENILAIAN: 
                                                    <span id="display_periode">
                                                        <?php 
                                                        echo strtoupper(date('d F', strtotime($pejabat_data['periode_awal']))) . ' SD ' . 
                                                             strtoupper(date('d F', strtotime($pejabat_data['periode_akhir']))) . ' TAHUN ' . 
                                                             date('Y', strtotime($pejabat_data['periode_akhir'])); 
                                                        ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="30" class="bg-light font-weight-bold border-top-0">NO</td>
                                                <td colspan="2" class="bg-light font-weight-bold border-top-0">PEJABAT PENILAI KINERJA</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td width="150">NAMA</td>
                                                <td><span id="display_pejabat_nama"><?php echo $pejabat_data['pejabat_nama']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">2</td>
                                                <td>NIP/NRP</td>
                                                <td><span id="display_pejabat_nip"><?php echo $pejabat_data['pejabat_nip']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">3</td>
                                                <td>PANGKAT/GOL. RUANG</td>
                                                <td><span id="display_pejabat_pangkat"><?php echo $pejabat_data['pejabat_pangkat']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">4</td>
                                                <td>JABATAN</td>
                                                <td><span id="display_pejabat_jabatan"><?php echo $pejabat_data['pejabat_jabatan']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">5</td>
                                                <td>UNIT KERJA</td>
                                                <td><span id="display_pejabat_unit"><?php echo $pejabat_data['pejabat_unit']; ?></span></td>
                                            </tr>
                                        </table>
                                        
                                        <!-- ATASAN PEJABAT PENILAI -->
                                        <!-- <table class="table table-sm mb-0 border-top">
                                            <tr>
                                                <td width="30" class="bg-light font-weight-bold">NO</td>
                                                <td colspan="2" class="bg-light font-weight-bold">ATASAN PEJABAT PENILAI KINERJA</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td width="150">NAMA</td>
                                                <td><span id="display_atasan_nama"><?php echo $pejabat_data['atasan_nama']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">2</td>
                                                <td>NIP/NRP</td>
                                                <td><span id="display_atasan_nip"><?php echo $pejabat_data['atasan_nip']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">3</td>
                                                <td>PANGKAT/GOL. RUANG</td>
                                                <td><span id="display_atasan_pangkat"><?php echo $pejabat_data['atasan_pangkat']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">4</td>
                                                <td>JABATAN</td>
                                                <td><span id="display_atasan_jabatan"><?php echo $pejabat_data['atasan_jabatan']; ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">5</td>
                                                <td>UNIT KERJA</td>
                                                <td><span id="display_atasan_unit"><?php echo $pejabat_data['atasan_unit']; ?></span></td>
                                            </tr>
                                        </table> -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- HASIL KERJA -->
                        <table class="table table-bordered table-sm mb-3">
                            <thead>
                                <tr class="bg-light">
                                    <th colspan="2" class="font-weight-bold">HASIL KERJA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-light">
                                    <td colspan="2" class="font-weight-bold">A. UTAMA</td>
                                </tr>
                                <?php 
                                $no = 1;
                                if($query_hasil_a && mysqli_num_rows($query_hasil_a) > 0):
                                    while($row = mysqli_fetch_assoc($query_hasil_a)):
                                ?>
                                <tr>
                                    <td width="30" class="text-center align-top"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="mb-2"><?php echo nl2br(htmlspecialchars($row['uraian'])); ?></div>
                                        <?php if(!empty($row['ekspektasi'])): ?>
                                        <div class="text-muted small">
                                            <strong>Ukuran keberhasilan/ indikator kinerja individu dan Target:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($row['ekspektasi'])); ?>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada data</td>
                                </tr>
                                <?php endif; ?>
                                
                                <tr class="bg-light">
                                    <td colspan="2" class="font-weight-bold">B. TAMBAHAN</td>
                                </tr>
                                <?php 
                                $no = 1;
                                if($query_hasil_b && mysqli_num_rows($query_hasil_b) > 0):
                                    while($row = mysqli_fetch_assoc($query_hasil_b)):
                                ?>
                                <tr>
                                    <td width="30" class="text-center align-top"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="mb-2"><?php echo nl2br(htmlspecialchars($row['uraian'])); ?></div>
                                        <?php if(!empty($row['ekspektasi'])): ?>
                                        <div class="text-muted small">
                                            <strong>Ukuran keberhasilan/ indikator kinerja individu dan Target:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($row['ekspektasi'])); ?>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada data</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        
                        <!-- PERILAKU KERJA -->
                        <table class="table table-bordered table-sm mb-3">
                            <thead>
                                <tr class="bg-light">
                                    <th colspan="2" class="font-weight-bold">PERILAKU KERJA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if($query_perilaku && mysqli_num_rows($query_perilaku) > 0):
                                    while($row = mysqli_fetch_assoc($query_perilaku)):
                                ?>
                                <tr>
                                    <td width="30" class="text-center align-top"><?php echo $no++; ?></td>
                                    <td>
                                        <div class="mb-2"><?php echo nl2br(htmlspecialchars($row['uraian'])); ?></div>
                                        <?php if(!empty($row['ekspektasi'])): ?>
                                        <div class="text-muted small">
                                            <strong>Ekspektasi Khusus Pimpinan:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($row['ekspektasi'])); ?>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada data</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Modal Edit Pejabat Penilai -->
                <div class="modal fade" id="modalPejabatPenilai" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title">Edit Informasi Pejabat Penilai Kinerja</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <form id="formPejabatPenilai">
                                    <div class="form-group">
                                        <label>PUSKOD / Unit Organisasi</label>
                                        <input type="text" class="form-control" id="input_puskod" name="puskod">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Periode Mulai</label>
                                                <input type="date" class="form-control" id="input_periode_awal" name="periode_awal">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Periode Selesai</label>
                                                <input type="date" class="form-control" id="input_periode_akhir" name="periode_akhir">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h6 class="bg-light p-2 mt-3">PEJABAT PENILAI KINERJA</h6>
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" class="form-control" id="input_pejabat_nama" name="pejabat_nama">
                                    </div>
                                    <div class="form-group">
                                        <label>NIP/NRP</label>
                                        <input type="text" class="form-control" id="input_pejabat_nip" name="pejabat_nip">
                                    </div>
                                    <div class="form-group">
                                        <label>Pangkat/Gol. Ruang</label>
                                        <input type="text" class="form-control" id="input_pejabat_pangkat" name="pejabat_pangkat">
                                    </div>
                                    <div class="form-group">
                                        <label>Jabatan</label>
                                        <input type="text" class="form-control" id="input_pejabat_jabatan" name="pejabat_jabatan">
                                    </div>
                                    <div class="form-group">
                                        <label>Unit Kerja</label>
                                        <input type="text" class="form-control" id="input_pejabat_unit" name="pejabat_unit">
                                    </div>
                                    
                                    <h6 class="bg-light p-2 mt-3">ATASAN PEJABAT PENILAI KINERJA</h6>
                                    <div class="form-group">
                                        <label>Nama</label>
                                        <input type="text" class="form-control" id="input_atasan_nama" name="atasan_nama">
                                    </div>
                                    <div class="form-group">
                                        <label>NIP/NRP</label>
                                        <input type="text" class="form-control" id="input_atasan_nip" name="atasan_nip">
                                    </div>
                                    <div class="form-group">
                                        <label>Pangkat/Gol. Ruang</label>
                                        <input type="text" class="form-control" id="input_atasan_pangkat" name="atasan_pangkat">
                                    </div>
                                    <div class="form-group">
                                        <label>Jabatan</label>
                                        <input type="text" class="form-control" id="input_atasan_jabatan" name="atasan_jabatan">
                                    </div>
                                    <div class="form-group">
                                        <label>Unit Kerja</label>
                                        <input type="text" class="form-control" id="input_atasan_unit" name="atasan_unit">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-primary" onclick="simpanPejabatPenilai()">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Kelola Hasil Kerja (Admin Only) -->
                <div class="modal fade" id="modalHasilKerja" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Kelola Hasil Kerja</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#tabUtama">A. UTAMA</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#tabTambahan">B. TAMBAHAN</a>
                                    </li>
                                </ul>
                                
                                <div class="tab-content mt-3">
                                    <!-- Tab A. UTAMA -->
                                    <div class="tab-pane fade show active" id="tabUtama">
                                        <button class="btn btn-success btn-sm mb-3" onclick="tambahItemHasil('A')">
                                            <i class="fas fa-plus"></i> Tambah Item
                                        </button>
                                        <div id="formHasilA"></div>
                                    </div>
                                    
                                    <!-- Tab B. TAMBAHAN -->
                                    <div class="tab-pane fade" id="tabTambahan">
                                        <button class="btn btn-success btn-sm mb-3" onclick="tambahItemHasil('B')">
                                            <i class="fas fa-plus"></i> Tambah Item
                                        </button>
                                        <div id="formHasilB"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" onclick="simpanHasilKerja()">Simpan Semua</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Kelola Perilaku Kerja (Admin Only) -->
                <div class="modal fade" id="modalPerilaku" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Kelola Perilaku Kerja</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <button class="btn btn-success btn-sm mb-3" onclick="tambahItemPerilaku()">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                                <div id="formPerilaku"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" onclick="simpanPerilakuKerja()">Simpan Semua</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                let itemsHasilA = [];
                let itemsHasilB = [];
                let itemsPerilaku = [];
                
                // Edit Pejabat Penilai
                function editPejabatPenilai() {
                    $.get('pages/admin/skp/get-pejabat-penilai.php', function(data) {
                        const pejabat = JSON.parse(data);
                        $('#input_puskod').val(pejabat.puskod || '');
                        $('#input_periode_awal').val(pejabat.periode_awal || '');
                        $('#input_periode_akhir').val(pejabat.periode_akhir || '');
                        $('#input_pejabat_nama').val(pejabat.pejabat_nama || '');
                        $('#input_pejabat_nip').val(pejabat.pejabat_nip || '');
                        $('#input_pejabat_pangkat').val(pejabat.pejabat_pangkat || '');
                        $('#input_pejabat_jabatan').val(pejabat.pejabat_jabatan || '');
                        $('#input_pejabat_unit').val(pejabat.pejabat_unit || '');
                        $('#input_atasan_nama').val(pejabat.atasan_nama || '');
                        $('#input_atasan_nip').val(pejabat.atasan_nip || '');
                        $('#input_atasan_pangkat').val(pejabat.atasan_pangkat || '');
                        $('#input_atasan_jabatan').val(pejabat.atasan_jabatan || '');
                        $('#input_atasan_unit').val(pejabat.atasan_unit || '');
                        $('#modalPejabatPenilai').modal('show');
                    });
                }
                
                function simpanPejabatPenilai() {
                    const formData = {
                        puskod: $('#input_puskod').val(),
                        periode_awal: $('#input_periode_awal').val(),
                        periode_akhir: $('#input_periode_akhir').val(),
                        pejabat_nama: $('#input_pejabat_nama').val(),
                        pejabat_nip: $('#input_pejabat_nip').val(),
                        pejabat_pangkat: $('#input_pejabat_pangkat').val(),
                        pejabat_jabatan: $('#input_pejabat_jabatan').val(),
                        pejabat_unit: $('#input_pejabat_unit').val(),
                        atasan_nama: $('#input_atasan_nama').val(),
                        atasan_nip: $('#input_atasan_nip').val(),
                        atasan_pangkat: $('#input_atasan_pangkat').val(),
                        atasan_jabatan: $('#input_atasan_jabatan').val(),
                        atasan_unit: $('#input_atasan_unit').val()
                    };
                    
                    $.ajax({
                        url: 'pages/admin/skp/save-pejabat-penilai.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(formData),
                        success: function(response) {
                            const result = JSON.parse(response);
                            if(result.success) {
                                alert('Data pejabat penilai berhasil disimpan!');
                                $('#modalPejabatPenilai').modal('hide');
                                location.reload();
                            } else {
                                alert('Gagal menyimpan data: ' + (result.message || 'Unknown error'));
                            }
                        },
                        error: function() {
                            alert('Gagal menyimpan data!');
                        }
                    });
                }
                
                function toggleManageHasilKerja() {
                    loadHasilKerja();
                    $('#modalHasilKerja').modal('show');
                }
                
                function toggleManagePerilaku() {
                    loadPerilakuKerja();
                    $('#modalPerilaku').modal('show');
                }
                
                function loadHasilKerja() {
                    $.get('pages/admin/skp/get-hasil-kerja.php', function(data) {
                        const hasil = JSON.parse(data);
                        itemsHasilA = hasil.filter(h => h.kategori === 'A');
                        itemsHasilB = hasil.filter(h => h.kategori === 'B');
                        renderFormHasil('A');
                        renderFormHasil('B');
                    });
                }
                
                function loadPerilakuKerja() {
                    $.get('pages/admin/skp/get-perilaku-kerja.php', function(data) {
                        itemsPerilaku = JSON.parse(data);
                        renderFormPerilaku();
                    });
                }
                
                function tambahItemHasil(kategori) {
                    const items = kategori === 'A' ? itemsHasilA : itemsHasilB;
                    items.push({
                        id: null,
                        kategori: kategori,
                        uraian: '',
                        ekspektasi: '',
                        urutan: items.length + 1
                    });
                    renderFormHasil(kategori);
                }
                
                function hapusItemHasil(kategori, index) {
                    if(confirm('Hapus item ini?')) {
                        const items = kategori === 'A' ? itemsHasilA : itemsHasilB;
                        items.splice(index, 1);
                        renderFormHasil(kategori);
                    }
                }
                
                function renderFormHasil(kategori) {
                    const items = kategori === 'A' ? itemsHasilA : itemsHasilB;
                    const container = kategori === 'A' ? '#formHasilA' : '#formHasilB';
                    
                    let html = '';
                    items.forEach((item, index) => {
                        html += `
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Uraian</label>
                                        <textarea class="form-control form-control-sm" rows="3" 
                                            onchange="itemsHasil${kategori}[${index}].uraian = this.value">${item.uraian || ''}</textarea>
                                    </div>
                                    <div class="col-md-5">
                                        <label>Ekspektasi Khusus Pimpinan</label>
                                        <textarea class="form-control form-control-sm" rows="3"
                                            onchange="itemsHasil${kategori}[${index}].ekspektasi = this.value">${item.ekspektasi || ''}</textarea>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center">
                                        <button class="btn btn-danger btn-sm" onclick="hapusItemHasil('${kategori}', ${index})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
                    
                    $(container).html(html);
                }
                
                function tambahItemPerilaku() {
                    itemsPerilaku.push({
                        id: null,
                        uraian: '',
                        ekspektasi: '',
                        urutan: itemsPerilaku.length + 1
                    });
                    renderFormPerilaku();
                }
                
                function hapusItemPerilaku(index) {
                    if(confirm('Hapus item ini?')) {
                        itemsPerilaku.splice(index, 1);
                        renderFormPerilaku();
                    }
                }
                
                function renderFormPerilaku() {
                    let html = '';
                    itemsPerilaku.forEach((item, index) => {
                        html += `
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Uraian</label>
                                        <textarea class="form-control form-control-sm" rows="3"
                                            onchange="itemsPerilaku[${index}].uraian = this.value">${item.uraian || ''}</textarea>
                                    </div>
                                    <div class="col-md-5">
                                        <label>Ekspektasi Khusus Pimpinan</label>
                                        <textarea class="form-control form-control-sm" rows="3"
                                            onchange="itemsPerilaku[${index}].ekspektasi = this.value">${item.ekspektasi || ''}</textarea>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center">
                                        <button class="btn btn-danger btn-sm" onclick="hapusItemPerilaku(${index})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
                    
                    $('#formPerilaku').html(html);
                }
                
                function simpanHasilKerja() {
                    const allItems = [...itemsHasilA, ...itemsHasilB];
                    
                    $.ajax({
                        url: 'pages/admin/skp/save-hasil-kerja.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(allItems),
                        success: function(response) {
                            alert('Data berhasil disimpan!');
                            $('#modalHasilKerja').modal('hide');
                            location.reload();
                        },
                        error: function() {
                            alert('Gagal menyimpan data!');
                        }
                    });
                }
                
                function simpanPerilakuKerja() {
                    $.ajax({
                        url: 'pages/admin/skp/save-perilaku-kerja.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(itemsPerilaku),
                        success: function(response) {
                            alert('Data berhasil disimpan!');
                            $('#modalPerilaku').modal('hide');
                            location.reload();
                        },
                        error: function() {
                            alert('Gagal menyimpan data!');
                        }
                    });
                }
                </script>
            </div>

            <div class="tab-pane fade" id="lampiran-skp" role="tabpanel" aria-labelledby="lampiran-skp-tab">
                <?php
                    // Get Lampiran SKP data
                    $query_dukungan = mysqli_query($kon, "SELECT * FROM skp_dukungan_sumber_daya ORDER BY urutan ASC");
                    $query_skema = mysqli_query($kon, "SELECT * FROM skp_skema_pertanggungjawaban ORDER BY urutan ASC");
                    $query_konsekuensi = mysqli_query($kon, "SELECT * FROM skp_konsekuensi ORDER BY urutan ASC");
                ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h5 class="font-weight-bold mb-1">LAMPIRAN SASARAN KINERJA PEGAWAI</h5>
                            <h6 class="mb-3">JABATAN FUNGSIONAL KATALOGER AHLI PERTAMA</h6>
                        </div>
                        
                        <!-- Tombol Edit -->
                        <div class="text-right mb-3">
                            <button class="btn btn-success btn-sm" onclick="window.print()">
                                <i class="fas fa-print"></i> Cetak
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="toggleManageDukungan()">
                                <i class="fas fa-tools"></i> Kelola Dukungan Sumber Daya
                            </button>
                            <button class="btn btn-info btn-sm" onclick="toggleManageSkema()">
                                <i class="fas fa-sitemap"></i> Kelola Skema Pertanggungjawaban
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="toggleManageKonsekuensi()">
                                <i class="fas fa-exclamation-triangle"></i> Kelola Konsekuensi
                            </button>
                        </div>
                        
                        <!-- DUKUNGAN SUMBER DAYA -->
                        <table class="table table-bordered table-sm mb-4">
                            <thead class="thead-light">
                                <tr>
                                    <th colspan="2" class="font-weight-bold">DUKUNGAN SUMBER DAYA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if($query_dukungan && mysqli_num_rows($query_dukungan) > 0):
                                    while($row = mysqli_fetch_assoc($query_dukungan)):
                                ?>
                                <tr>
                                    <td width="30" class="text-center align-top"><?php echo $no++; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($row['uraian'])); ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada data</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        
                        <!-- SKEMA PERTANGGUNGJAWABAN -->
                        <table class="table table-bordered table-sm mb-4">
                            <thead class="thead-light">
                                <tr>
                                    <th colspan="2" class="font-weight-bold">SKEMA PERTANGGUNGJAWABAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if($query_skema && mysqli_num_rows($query_skema) > 0):
                                    while($row = mysqli_fetch_assoc($query_skema)):
                                ?>
                                <tr>
                                    <td width="30" class="text-center align-top"><?php echo $no++; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($row['uraian'])); ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada data</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        
                        <!-- KONSEKUENSI -->
                        <table class="table table-bordered table-sm mb-3">
                            <thead class="thead-light">
                                <tr>
                                    <th colspan="2" class="font-weight-bold">KONSEKUENSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                if($query_konsekuensi && mysqli_num_rows($query_konsekuensi) > 0):
                                    while($row = mysqli_fetch_assoc($query_konsekuensi)):
                                ?>
                                <tr>
                                    <td width="30" class="text-center align-top"><?php echo $no++; ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($row['uraian'])); ?></td>
                                </tr>
                                <?php 
                                    endwhile;
                                else:
                                ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Belum ada data</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        
                        <!-- Signature Section -->
                        <div class="row mt-5">
                            <div class="col-md-6 text-center">
                                <p class="mb-5">Pegawai yang Dinilai,</p>
                                <p class="font-weight-bold mb-0"><?php echo $user_data['nama_lengkap']; ?></p>
                                <p>NIP/NRP <?php echo $user_data['username']; ?></p>
                            </div>
                            <div class="col-md-6 text-center">
                                <p class="mb-1">Jakarta, Desember 2025</p>
                                <p class="mb-5">Pejabat Penilai Kinerja,</p>
                                <p class="font-weight-bold mb-0"><span id="display_pejabat_nama_lampiran">TISNA KURNIAWAN</span></p>
                                <p>MARSEKAL PERTAMA TNI</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Kelola Dukungan Sumber Daya -->
                <div class="modal fade" id="modalDukungan" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Kelola Dukungan Sumber Daya</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <button class="btn btn-success btn-sm mb-3" onclick="tambahItemDukungan()">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                                <div id="formDukungan"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" onclick="simpanDukungan()">Simpan Semua</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Kelola Skema Pertanggungjawaban -->
                <div class="modal fade" id="modalSkema" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">Kelola Skema Pertanggungjawaban</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <button class="btn btn-success btn-sm mb-3" onclick="tambahItemSkema()">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                                <div id="formSkema"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" onclick="simpanSkema()">Simpan Semua</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Kelola Konsekuensi -->
                <div class="modal fade" id="modalKonsekuensi" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning text-white">
                                <h5 class="modal-title">Kelola Konsekuensi</h5>
                                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <button class="btn btn-success btn-sm mb-3" onclick="tambahItemKonsekuensi()">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                                <div id="formKonsekuensi"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                <button type="button" class="btn btn-primary" onclick="simpanKonsekuensi()">Simpan Semua</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                let itemsDukungan = [];
                let itemsSkema = [];
                let itemsKonsekuensi = [];
                
                // Dukungan Sumber Daya Functions
                function toggleManageDukungan() {
                    loadDukungan();
                    $('#modalDukungan').modal('show');
                }
                
                function loadDukungan() {
                    $.get('pages/admin/skp/get-dukungan.php', function(data) {
                        itemsDukungan = JSON.parse(data);
                        renderFormDukungan();
                    });
                }
                
                function tambahItemDukungan() {
                    itemsDukungan.push({
                        id: null,
                        uraian: '',
                        urutan: itemsDukungan.length + 1
                    });
                    renderFormDukungan();
                }
                
                function hapusItemDukungan(index) {
                    if(confirm('Hapus item ini?')) {
                        itemsDukungan.splice(index, 1);
                        renderFormDukungan();
                    }
                }
                
                function renderFormDukungan() {
                    let html = '';
                    itemsDukungan.forEach((item, index) => {
                        html += `
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong>Item ${index + 1}</strong>
                                    <button class="btn btn-danger btn-sm" onclick="hapusItemDukungan(${index})">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                                <textarea class="form-control" rows="3" 
                                    onchange="itemsDukungan[${index}].uraian = this.value"
                                    placeholder="Uraian dukungan sumber daya...">${item.uraian || ''}</textarea>
                            </div>
                        </div>`;
                    });
                    $('#formDukungan').html(html);
                }
                
                function simpanDukungan() {
                    $.ajax({
                        url: 'pages/admin/skp/save-dukungan.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(itemsDukungan),
                        success: function(response) {
                            alert('Data berhasil disimpan!');
                            $('#modalDukungan').modal('hide');
                            location.reload();
                        },
                        error: function() {
                            alert('Gagal menyimpan data!');
                        }
                    });
                }
                
                // Skema Pertanggungjawaban Functions
                function toggleManageSkema() {
                    loadSkema();
                    $('#modalSkema').modal('show');
                }
                
                function loadSkema() {
                    $.get('pages/admin/skp/get-skema.php', function(data) {
                        itemsSkema = JSON.parse(data);
                        renderFormSkema();
                    });
                }
                
                function tambahItemSkema() {
                    itemsSkema.push({
                        id: null,
                        uraian: '',
                        urutan: itemsSkema.length + 1
                    });
                    renderFormSkema();
                }
                
                function hapusItemSkema(index) {
                    if(confirm('Hapus item ini?')) {
                        itemsSkema.splice(index, 1);
                        renderFormSkema();
                    }
                }
                
                function renderFormSkema() {
                    let html = '';
                    itemsSkema.forEach((item, index) => {
                        html += `
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong>Item ${index + 1}</strong>
                                    <button class="btn btn-danger btn-sm" onclick="hapusItemSkema(${index})">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                                <textarea class="form-control" rows="3" 
                                    onchange="itemsSkema[${index}].uraian = this.value"
                                    placeholder="Uraian skema pertanggungjawaban...">${item.uraian || ''}</textarea>
                            </div>
                        </div>`;
                    });
                    $('#formSkema').html(html);
                }
                
                function simpanSkema() {
                    $.ajax({
                        url: 'pages/admin/skp/save-skema.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(itemsSkema),
                        success: function(response) {
                            alert('Data berhasil disimpan!');
                            $('#modalSkema').modal('hide');
                            location.reload();
                        },
                        error: function() {
                            alert('Gagal menyimpan data!');
                        }
                    });
                }
                
                // Konsekuensi Functions
                function toggleManageKonsekuensi() {
                    loadKonsekuensi();
                    $('#modalKonsekuensi').modal('show');
                }
                
                function loadKonsekuensi() {
                    $.get('pages/admin/skp/get-konsekuensi.php', function(data) {
                        itemsKonsekuensi = JSON.parse(data);
                        renderFormKonsekuensi();
                    });
                }
                
                function tambahItemKonsekuensi() {
                    itemsKonsekuensi.push({
                        id: null,
                        uraian: '',
                        urutan: itemsKonsekuensi.length + 1
                    });
                    renderFormKonsekuensi();
                }
                
                function hapusItemKonsekuensi(index) {
                    if(confirm('Hapus item ini?')) {
                        itemsKonsekuensi.splice(index, 1);
                        renderFormKonsekuensi();
                    }
                }
                
                function renderFormKonsekuensi() {
                    let html = '';
                    itemsKonsekuensi.forEach((item, index) => {
                        html += `
                        <div class="card mb-2">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong>Item ${index + 1}</strong>
                                    <button class="btn btn-danger btn-sm" onclick="hapusItemKonsekuensi(${index})">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                                <textarea class="form-control" rows="3" 
                                    onchange="itemsKonsekuensi[${index}].uraian = this.value"
                                    placeholder="Uraian konsekuensi...">${item.uraian || ''}</textarea>
                            </div>
                        </div>`;
                    });
                    $('#formKonsekuensi').html(html);
                }
                
                function simpanKonsekuensi() {
                    $.ajax({
                        url: 'pages/admin/skp/save-konsekuensi.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(itemsKonsekuensi),
                        success: function(response) {
                            alert('Data berhasil disimpan!');
                            $('#modalKonsekuensi').modal('hide');
                            location.reload();
                        },
                        error: function() {
                            alert('Gagal menyimpan data!');
                        }
                    });
                }
                </script>
            </div>

            <div class="tab-pane fade" id="evaluasi-kinerja" role="tabpanel" aria-labelledby="evaluasi-kinerja-tab">
                <div class="card">
                    <div class="card-body">
                        <h5>Evaluasi Kinerja Kualitatif</h5>
                        <p>Konten untuk evaluasi kinerja kualitatif akan ditampilkan di sini.</p>
                        <!-- Tambahkan konten evaluasi kinerja di sini -->
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="cover" role="tabpanel" aria-labelledby="cover-tab">
                <?php
                    // Ambil data guru yang sedang login
                    $username = $_SESSION['username'];
                    $query = mysqli_query($kon, "SELECT * FROM admin WHERE username='".$username."' LIMIT 1");
                    $data_guru = mysqli_fetch_array($query);
                    
                    // Ambil data cover dari database jika ada
                    $query_cover = mysqli_query($kon, "SELECT * FROM skp_cover WHERE username='".$username."' LIMIT 1");
                    $data_cover = mysqli_fetch_array($query_cover);
                    
                    // Set default values
                    $nama = $data_cover['nama'] ?? $data_guru['nama_lengkap'];
                    $username = $data_cover['username'] ?? $data_guru['username'];
                    $pangkat_gol = $data_cover['pangkat_gol'] ?? '-';
                    $jabatan = $data_cover['jabatan'] ?? '-';
                    $unit_kerja = $data_cover['unit_kerja'] ?? '-';
                    $periode_mulai = $data_cover['periode_mulai'] ?? '02 JUNI';
                    $periode_selesai = $data_cover['periode_selesai'] ?? '31 DESEMBER';
                    $tahun = $data_cover['tahun'] ?? date('Y');
                ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="text-right mb-3">
                            <button class="btn btn-primary" onclick="toggleEditMode()">
                                <i class="fas fa-edit"></i> Edit Cover
                            </button>
                            <button class="btn btn-success" onclick="printCover()">
                                <i class="fas fa-print"></i> Cetak Cover
                            </button>
                        </div>
                        
                        <!-- Form Edit (Hidden by default) -->
                        <div id="editForm" style="display:none;">
                            <form id="formCover" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input type="text" class="form-control" name="nama" id="edit_nama" value="<?php echo $nama; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NIP</label>
                                            <input type="text" class="form-control" name="username" id="edit_nip" value="<?php echo $username; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Pangkat/Golongan</label>
                                            <input type="text" class="form-control" name="pangkat_gol" id="edit_pangkat" value="<?php echo $pangkat_gol; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jabatan</label>
                                            <input type="text" class="form-control" name="jabatan" id="edit_jabatan" value="<?php echo $jabatan; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Unit Kerja</label>
                                            <input type="text" class="form-control" name="unit_kerja" id="edit_unit_kerja" value="<?php echo $unit_kerja; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tahun</label>
                                            <input type="text" class="form-control" name="tahun" id="edit_tahun" value="<?php echo $tahun; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Periode Mulai</label>
                                            <input type="text" class="form-control" name="periode_mulai" id="edit_periode_mulai" value="<?php echo $periode_mulai; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Periode Selesai</label>
                                            <input type="text" class="form-control" name="periode_selesai" id="edit_periode_selesai" value="<?php echo $periode_selesai; ?>">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success" onclick="saveCover()">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="toggleEditMode()">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                            </form>
                        </div>
                        
                        <!-- Cover Preview -->
                        <div id="coverPreview">
                            <div class="cover-page" style="background: white; padding: 40px; min-height: 800px; border: 1px solid #ddd;">
                                <div class="text-center">
                                    <!-- Logo Garuda -->
                                    <div class="mb-4">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Garuda_Pancasila%2C_Coat_of_Arms_of_Indonesia.svg/960px-Garuda_Pancasila%2C_Coat_of_Arms_of_Indonesia.svg.png" alt="Garuda Pancasila" style="width: 120px; height: auto;">
                                    </div>
                                    
                                    <!-- Title -->
                                    <h2 class="font-weight-bold mb-4" style="font-size: 24px;">PENILAIAN KINERJA</h2>
                                    <h3 class="font-weight-bold mb-2" style="font-size: 20px;">PEGAWAI APARATUR SIPIL NEGARA</h3>
                                    <p class="mb-5" style="font-size: 14px; text-decoration: underline;">SESUAI PERMENPAN RB 06 TAHUN 2022</p>
                                    
                                    <!-- Periode -->
                                    <div class="my-5 py-4">
                                        <p class="font-weight-bold mb-2" style="font-size: 16px;">JANGKA WAKTU PENILAIAN</p>
                                        <p class="font-weight-bold" style="font-size: 16px;" id="display_periode">
                                            <?php echo strtoupper($periode_mulai); ?> S.D <?php echo strtoupper($periode_selesai); ?> <?php echo $tahun; ?>
                                        </p>
                                    </div>
                                    
                                    <!-- Data Pegawai -->
                                    <div class="mt-5 pt-5">
                                        <table class="mx-auto" style="text-align: left; font-size: 14px;">
                                            <tr>
                                                <td style="padding: 8px; width: 150px; font-weight: 600;">NAMA</td>
                                                <td style="padding: 8px; width: 20px;">:</td>
                                                <td style="padding: 8px;" id="display_nama"><?php echo $nama; ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px; font-weight: 600;">NIP</td>
                                                <td style="padding: 8px;">:</td>
                                                <td style="padding: 8px;" id="display_nip"><?php echo $username; ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px; font-weight: 600;">PANGKAT /GOL</td>
                                                <td style="padding: 8px;">:</td>
                                                <td style="padding: 8px;" id="display_pangkat"><?php echo $pangkat_gol; ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px; font-weight: 600;">JABATAN</td>
                                                <td style="padding: 8px;">:</td>
                                                <td style="padding: 8px;" id="display_jabatan"><?php echo $jabatan; ?></td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px; font-weight: 600;">UNIT KERJA</td>
                                                <td style="padding: 8px;">:</td>
                                                <td style="padding: 8px;" id="display_unit_kerja"><?php echo $unit_kerja; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="mt-5 pt-5">
                                        <h3 class="font-weight-bold mt-5" style="font-size: 18px;">KEMENTERIAN PERTAHANAN RI</h3>
                                        <p class="font-weight-bold" style="font-size: 16px;" id="display_tahun">TAHUN <?php echo $tahun; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                function toggleEditMode() {
                    var editForm = document.getElementById('editForm');
                    var coverPreview = document.getElementById('coverPreview');
                    
                    if (editForm.style.display === 'none') {
                        editForm.style.display = 'block';
                        coverPreview.style.display = 'none';
                    } else {
                        editForm.style.display = 'none';
                        coverPreview.style.display = 'block';
                    }
                }
                
                function saveCover() {
                    var nama = document.getElementById('edit_nama').value;
                    var username = document.getElementById('edit_nip').value;
                    var pangkat = document.getElementById('edit_pangkat').value;
                    var jabatan = document.getElementById('edit_jabatan').value;
                    var unit_kerja = document.getElementById('edit_unit_kerja').value;
                    var periode_mulai = document.getElementById('edit_periode_mulai').value;
                    var periode_selesai = document.getElementById('edit_periode_selesai').value;
                    var tahun = document.getElementById('edit_tahun').value;
                    
                    $.ajax({
                        url: 'pages/admin/skp/save-cover.php',
                        type: 'POST',
                        data: {
                            nama: nama,
                            username: username,
                            pangkat_gol: pangkat,
                            jabatan: jabatan,
                            unit_kerja: unit_kerja,
                            periode_mulai: periode_mulai,
                            periode_selesai: periode_selesai,
                            tahun: tahun
                        },
                        success: function(response) {
                            // Update display
                            document.getElementById('display_nama').innerText = nama;
                            document.getElementById('display_nip').innerText = username;
                            document.getElementById('display_pangkat').innerText = pangkat;
                            document.getElementById('display_jabatan').innerText = jabatan;
                            document.getElementById('display_unit_kerja').innerText = unit_kerja;
                            document.getElementById('display_periode').innerText = periode_mulai.toUpperCase() + ' S.D ' + periode_selesai.toUpperCase() + ' ' + tahun;
                            document.getElementById('display_tahun').innerText = 'TAHUN ' + tahun;
                            
                            alert('Data cover berhasil disimpan!');
                            toggleEditMode();
                        },
                        error: function() {
                            alert('Gagal menyimpan data!');
                        }
                    });
                }
                
                function printCover() {
                    var printContents = document.getElementById('coverPreview').innerHTML;
                    var originalContents = document.body.innerHTML;
                    
                    document.body.innerHTML = printContents;
                    window.print();
                    document.body.innerHTML = originalContents;
                    location.reload();
                }
                </script>
            </div>

            <div class="tab-pane fade" id="dok-evaluasi" role="tabpanel" aria-labelledby="dok-evaluasi-tab">
                <?php
                    // Ambil data guru yang sedang login
                    $username = $_SESSION['username'];
                    $query_pegawai = mysqli_query($kon, "SELECT * FROM admin WHERE username='".$username."' LIMIT 1");
                    $data_pegawai = mysqli_fetch_array($query_pegawai);
                    
                    // Ambil data dokumen evaluasi dari database
                    $query_dok = mysqli_query($kon, "SELECT * FROM dok_evaluasi_kinerja WHERE username='".$username."' LIMIT 1");
                    $data_dok = mysqli_fetch_array($query_dok);
                    
                    // Set default values
                    $periode_triwulan = $data_dok['periode_triwulan'] ?? 'I-II / III / IV';
                    $periode_akhir = $data_dok['periode_akhir'] ?? 'AKHIR';
                    $periode_penilaian = $data_dok['periode_penilaian'] ?? '02 JANUARI SD 31 DESEMBER TAHUN 2025';
                    
                    $pejabat_nama = $data_dok['pejabat_nama'] ?? '';
                    $pejabat_username = $data_dok['pejabat_username'] ?? '';
                    $pejabat_pangkat = $data_dok['pejabat_pangkat'] ?? '';
                    $pejabat_jabatan = $data_dok['pejabat_jabatan'] ?? '';
                    $pejabat_unit = $data_dok['pejabat_unit'] ?? '';
                    
                    $atasan_nama = $data_dok['atasan_nama'] ?? '';
                    $atasan_username = $data_dok['atasan_username'] ?? '';
                    $atasan_pangkat = $data_dok['atasan_pangkat'] ?? '';
                    $atasan_jabatan = $data_dok['atasan_jabatan'] ?? '';
                    $atasan_unit = $data_dok['atasan_unit'] ?? '';
                    
                    $capaian_kinerja = $data_dok['capaian_kinerja'] ?? 'BAIK';
                    $predikat_kinerja = $data_dok['predikat_kinerja'] ?? 'BAIK';
                    
                    $keberatan = $data_dok['keberatan'] ?? '';
                    $penjelasan_pejabat = $data_dok['penjelasan_pejabat'] ?? '';
                    $keputusan_atasan = $data_dok['keputusan_atasan'] ?? '';
                ?>               
                <div class="card">
                    <div class="card-body">
                        <div class="text-right mb-3 no-print">
                            <button class="btn btn-primary" onclick="toggleEditDokEvaluasi()">
                                <i class="fas fa-edit"></i> Edit Dokumen
                            </button>
                            <button class="btn btn-success" onclick="printDokEvaluasi()">
                                <i class="fas fa-print"></i> Cetak Dokumen
                            </button>
                        </div>
                        
                        <!-- Form Edit (Hidden) -->
                        <div id="editFormDokEvaluasi" style="display:none;" class="no-print">
                            <form id="formDokEvaluasi">
                                <h6 class="font-weight-bold mb-3">Edit Data Pejabat Penilai</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Pejabat Penilai</label>
                                            <input type="text" class="form-control" id="pejabat_nama" value="<?php echo $pejabat_nama; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NIP/NRP Pejabat</label>
                                            <input type="text" class="form-control" id="pejabat_username" value="<?php echo $pejabat_username; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Pangkat/Gol. Ruang</label>
                                            <input type="text" class="form-control" id="pejabat_pangkat" value="<?php echo $pejabat_pangkat; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Jabatan</label>
                                            <input type="text" class="form-control" id="pejabat_jabatan" value="<?php echo $pejabat_jabatan; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Unit Kerja</label>
                                            <input type="text" class="form-control" id="pejabat_unit" value="<?php echo $pejabat_unit; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <h6 class="font-weight-bold mb-3 mt-4">Edit Data Atasan Pejabat Penilai</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Atasan</label>
                                            <input type="text" class="form-control" id="atasan_nama" value="<?php echo $atasan_nama; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>NIP/NRP Atasan</label>
                                            <input type="text" class="form-control" id="atasan_username" value="<?php echo $atasan_username; ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Pangkat/Gol. Ruang</label>
                                            <input type="text" class="form-control" id="atasan_pangkat" value="<?php echo $atasan_pangkat; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Jabatan</label>
                                            <input type="text" class="form-control" id="atasan_jabatan" value="<?php echo $atasan_jabatan; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Unit Kerja</label>
                                            <input type="text" class="form-control" id="atasan_unit" value="<?php echo $atasan_unit; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-success" onclick="saveDokEvaluasi()">
                                    <i class="fas fa-save"></i> Simpan
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="toggleEditDokEvaluasi()">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                            </form>
                        </div>
                        
                        <!-- Preview Dokumen -->
                        <div id="dokEvaluasiPreview">
                            <div class="dokumen-evaluasi" style="background: white; padding: 40px; border: 1px solid #ddd;">
                                <!-- Logo Garuda -->
                                <div class="text-center mb-3">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Garuda_Pancasila%2C_Coat_of_Arms_of_Indonesia.svg/960px-Garuda_Pancasila%2C_Coat_of_Arms_of_Indonesia.svg.png" alt="Garuda Pancasila" style="width: 80px; height: auto;">
                                </div>
                                
                                <!-- Header -->
                                <div class="text-center mb-4">
                                    <h5 class="font-weight-bold">DOKUMEN EVALUASI KINERJA PEGAWAI</h5>
                                    <p class="mb-1">PERIODE: TRIWULAN <span id="display_triwulan"><?php echo $periode_triwulan; ?></span> - <span id="display_akhir"><?php echo $periode_akhir; ?></span></p>
                                    <p class="mb-0">PERIODE PENILAIAN:</p>
                                    <p class="font-weight-bold" id="display_periode"><?php echo $periode_penilaian; ?></p>
                                </div>
                                
                                <!-- Tabel Data -->
                                <table class="table table-bordered table-sm" style="font-size: 13px;">
                                    <tbody>
                                        <tr class="bg-light">
                                            <td colspan="3" class="font-weight-bold">PUSKOD BALOGBAH KEMHAN</td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td width="30">1.</td>
                                            <td colspan="2" class="font-weight-bold">PEGAWAI YANG DINILAI</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td width="200">NAMA</td>
                                            <td>: <?php echo $data_pegawai['nama_lengkap']; ?></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>NIP</td>
                                            <td>: <?php echo $data_pegawai['username']; ?></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>PANGKAT/GOL. RUANG</td>
                                            <td>: -</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>JABATAN</td>
                                            <td>: Administrator</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>UNIT KERJA</td>
                                            <td>: -</td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td>2.</td>
                                            <td colspan="2" class="font-weight-bold">PEJABAT PENILAI KINERJA</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>NAMA</td>
                                            <td>: <span id="display_pejabat_nama"><?php echo $pejabat_nama; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>NIP/NRP</td>
                                            <td>: <span id="display_pejabat_username"><?php echo $pejabat_username; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>PANGKAT/GOL. RUANG</td>
                                            <td>: <span id="display_pejabat_pangkat"><?php echo $pejabat_pangkat; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>JABATAN</td>
                                            <td>: <span id="display_pejabat_jabatan"><?php echo $pejabat_jabatan; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>UNIT KERJA</td>
                                            <td>: <span id="display_pejabat_unit"><?php echo $pejabat_unit; ?></span></td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td>3.</td>
                                            <td colspan="2" class="font-weight-bold">ATASAN PEJABAT PENILAI KINERJA</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>NAMA</td>
                                            <td>: <span id="display_atasan_nama"><?php echo $atasan_nama; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>NIP/NRP</td>
                                            <td>: <span id="display_atasan_username"><?php echo $atasan_username; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>PANGKAT/GOL. RUANG</td>
                                            <td>: <span id="display_atasan_pangkat"><?php echo $atasan_pangkat; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>JABATAN</td>
                                            <td>: <span id="display_atasan_jabatan"><?php echo $atasan_jabatan; ?></span></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>UNIT KERJA</td>
                                            <td>: <span id="display_atasan_unit"><?php echo $atasan_unit; ?></span></td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td>4.</td>
                                            <td colspan="2" class="font-weight-bold">EVALUASI KINERJA</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>CAPAIAN KINERJA ORGANISASI</td>
                                            <td>: <?php echo $capaian_kinerja; ?></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>PREDIKAT KINERJA PEGAWAI</td>
                                            <td>: <?php echo $predikat_kinerja; ?></td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td>5.</td>
                                            <td colspan="2" class="font-weight-bold">CATATAN/REKOMENDASI</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="height: 60px; vertical-align: top;">&nbsp;</td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td>6.</td>
                                            <td colspan="2" class="font-weight-bold">KEBERATAN</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="height: 60px; vertical-align: top;">&nbsp;</td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td>7.</td>
                                            <td colspan="2" class="font-weight-bold">PENJELASAN PEJABAT PENILAI KINERJA ATAS KEBERATAN</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="height: 60px; vertical-align: top;">&nbsp;</td>
                                        </tr>
                                        <tr class="bg-light">
                                            <td>8.</td>
                                            <td colspan="2" class="font-weight-bold">KEPUTUSAN DAN REKOMENDASI ATASAN PEJABAT PENILAI KINERJA</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" style="height: 60px; vertical-align: top;">&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                <!-- Tanda Tangan -->
                                <div class="row mt-4" style="font-size: 12px;">
                                    <div class="col-4 text-center">
                                        <p class="mb-1">Jakarta, Desember 2025</p>
                                        <p class="mb-5">Pegawai yang Dinilai,</p>
                                        <p class="font-weight-bold mb-0" style="text-decoration: underline;"><?php echo $data_pegawai['nama_lengkap']; ?></p>
                                        <p class="mb-0">NIP. <?php echo $data_pegawai['username']; ?></p>
                                    </div>
                                    <div class="col-4 text-center">
                                        <p class="mb-1">Jakarta, Desember 2025</p>
                                        <p class="mb-5">Pejabat Penilai Kinerja,</p>
                                        <p class="font-weight-bold mb-0" style="text-decoration: underline;" id="ttd_pejabat_nama"><?php echo $pejabat_nama; ?></p>
                                        <p class="mb-0" id="ttd_pejabat_pangkat"><?php echo $pejabat_pangkat; ?></p>
                                    </div>
                                    <div class="col-4">&nbsp;</div>
                                </div>
                                <div class="row mt-3" style="font-size: 12px;">
                                    <div class="col-4">&nbsp;</div>
                                    <div class="col-4 text-center">
                                        <p class="mb-1">Jakarta, Desember 2025</p>
                                        <p class="mb-5">Atasan Pejabat Penilai Kinerja,</p>
                                        <p class="font-weight-bold mb-0" style="text-decoration: underline;" id="ttd_atasan_nama"><?php echo $atasan_nama; ?></p>
                                        <p class="mb-0" id="ttd_atasan_pangkat"><?php echo $atasan_pangkat; ?></p>
                                    </div>
                                    <div class="col-4">&nbsp;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <script>
                function toggleEditDokEvaluasi() {
                    var editForm = document.getElementById('editFormDokEvaluasi');
                    var preview = document.getElementById('dokEvaluasiPreview');
                    
                    if (editForm.style.display === 'none') {
                        editForm.style.display = 'block';
                        preview.style.display = 'none';
                    } else {
                        editForm.style.display = 'none';
                        preview.style.display = 'block';
                    }
                }
                
                function saveDokEvaluasi() {
                    var data = {
                        periode_triwulan: '<?php echo $periode_triwulan; ?>',
                        periode_akhir: '<?php echo $periode_akhir; ?>',
                        periode_penilaian: '<?php echo $periode_penilaian; ?>',
                        pejabat_nama: document.getElementById('pejabat_nama').value,
                        pejabat_username: document.getElementById('pejabat_username').value,
                        pejabat_pangkat: document.getElementById('pejabat_pangkat').value,
                        pejabat_jabatan: document.getElementById('pejabat_jabatan').value,
                        pejabat_unit: document.getElementById('pejabat_unit').value,
                        atasan_nama: document.getElementById('atasan_nama').value,
                        atasan_username: document.getElementById('atasan_username').value,
                        atasan_pangkat: document.getElementById('atasan_pangkat').value,
                        atasan_jabatan: document.getElementById('atasan_jabatan').value,
                        atasan_unit: document.getElementById('atasan_unit').value,
                        capaian_kinerja: 'BAIK',
                        predikat_kinerja: 'BAIK',
                        keberatan: '',
                        penjelasan_pejabat: '',
                        keputusan_atasan: ''
                    };
                    
                    $.ajax({
                        url: 'pages/admin/skp/save-dok-evaluasi.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify(data),
                        success: function(response) {
                            // Update display
                            document.getElementById('display_pejabat_nama').innerText = data.pejabat_nama;
                            document.getElementById('display_pejabat_username').innerText = data.pejabat_username;
                            document.getElementById('display_pejabat_pangkat').innerText = data.pejabat_pangkat;
                            document.getElementById('display_pejabat_jabatan').innerText = data.pejabat_jabatan;
                            document.getElementById('display_pejabat_unit').innerText = data.pejabat_unit;
                            
                            document.getElementById('display_atasan_nama').innerText = data.atasan_nama;
                            document.getElementById('display_atasan_username').innerText = data.atasan_username;
                            document.getElementById('display_atasan_pangkat').innerText = data.atasan_pangkat;
                            document.getElementById('display_atasan_jabatan').innerText = data.atasan_jabatan;
                            document.getElementById('display_atasan_unit').innerText = data.atasan_unit;
                            
                            document.getElementById('ttd_pejabat_nama').innerText = data.pejabat_nama;
                            document.getElementById('ttd_pejabat_pangkat').innerText = data.pejabat_pangkat;
                            document.getElementById('ttd_atasan_nama').innerText = data.atasan_nama;
                            document.getElementById('ttd_atasan_pangkat').innerText = data.atasan_pangkat;
                            
                            alert('Data berhasil disimpan!');
                            toggleEditDokEvaluasi();
                        },
                        error: function() {
                            alert('Gagal menyimpan data!');
                        }
                    });
                }
                
                function printDokEvaluasi() {
                    // Hide no-print elements
                    var style = document.createElement('style');
                    style.innerHTML = '@media print { .no-print { display: none !important; } }';
                    document.head.appendChild(style);
                    
                    var printContents = document.getElementById('dokEvaluasiPreview').innerHTML;
                    var originalContents = document.body.innerHTML;
                    
                    document.body.innerHTML = printContents;
                    window.print();
                    document.body.innerHTML = originalContents;
                    location.reload();
                }
                </script>           </div>

            <div class="tab-pane fade" id="kuadran" role="tabpanel" aria-labelledby="kuadran-tab">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center" style="font-size: 0.9rem;">
                                <tbody>
                                    <!-- Header Row untuk HASIL KERJA -->
                                    <tr>
                                        <td colspan="5" class="bg-light font-weight-bold">HASIL KERJA</td>
                                        <td rowspan="4" class="align-middle bg-light" style="width: 30px;"></td>
                                    </tr>
                                    
                                    <!-- Row 1: Di Atas Ekspektasi -->
                                    <tr>
                                        <td class="bg-light text-left">Di Atas<br>Ekspektasi</td>
                                        <td>KURANG/MISS<br>CONDUCT</td>
                                        <td>BAIK</td>
                                        <td>SANGAT BAIK</td>
                                        <td rowspan="3" class="align-middle bg-light" style="width: 30px;"></td>
                                    </tr>
                                    
                                    <!-- Row 2: Sesuai Ekspektasi -->
                                    <tr>
                                        <td class="bg-light text-left">Sesuai<br>Ekspektasi</td>
                                        <td>KURANG/MISS<br>CONDUCT</td>
                                        <td>BAIK</td>
                                        <td>BAIK</td>
                                    </tr>
                                    
                                    <!-- Row 3: Di Bawah Ekspektasi -->
                                    <tr>
                                        <td class="bg-light text-left">Di Bawah<br>Ekspektasi</td>
                                        <td>SANGAT KURANG</td>
                                        <td>BUTUH PERBAIKAN</td>
                                        <td>BUTUH PERBAIKAN</td>
                                    </tr>
                                    
                                    <!-- Bottom row untuk label PERILAKU KERJA -->
                                    <tr>
                                        <td class="bg-light"></td>
                                        <td class="bg-light">Di Bawah<br>Ekspektasi</td>
                                        <td class="bg-light">Sesuai<br>Ekspektasi</td>
                                        <td class="bg-light">Di Atas<br>Ekspektasi</td>
                                        <td class="bg-light"></td>
                                        <td class="bg-light font-weight-bold" style="writing-mode: vertical-rl; text-orientation: mixed;">PERILAKU KERJA</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-5">
                           
                            <h6 class="font-weight-bold mb-3 mt-3">Keterangan:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width: 150px;">Hasil Kerja</th>
                                            <th style="width: 200px;">Perilaku Kerja</th>
                                            <th>Hasil</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Di Atas Ekspektasi</td>
                                            <td>Di Atas Ekspektasi</td>
                                            <td>SANGAT BAIK</td>
                                        </tr>
                                        <tr>
                                            <td>Di Atas Ekspektasi</td>
                                            <td>Sesuai Ekspektasi</td>
                                            <td>BAIK</td>
                                        </tr>
                                        <tr>
                                            <td>Di Bawah Ekspektasi</td>
                                            <td>Di Atas Ekspektasi</td>
                                            <td>BUTUH PERBAIKAN</td>
                                        </tr>
                                        <tr>
                                            <td>Sesuai Ekspektasi</td>
                                            <td>Di Atas Ekspektasi</td>
                                            <td>BAIK</td>
                                        </tr>
                                        <tr>
                                            <td>Sesuai Ekspektasi</td>
                                            <td>Sesuai Ekspektasi</td>
                                            <td>BAIK</td>
                                        </tr>
                                        <tr>
                                            <td>Di Bawah Ekspektasi</td>
                                            <td>Sesuai Ekspektasi</td>
                                            <td>BUTUH PERBAIKAN</td>
                                        </tr>
                                        <tr>
                                            <td>Di Atas Ekspektasi</td>
                                            <td>Di Bawah Ekspektasi</td>
                                            <td>KURANG/MISS CONDUCT</td>
                                        </tr>
                                        <tr>
                                            <td>Sesuai Ekspektasi</td>
                                            <td>Di Bawah Ekspektasi</td>
                                            <td>KURANG/MISS CONDUCT</td>
                                        </tr>
                                        <tr>
                                            <td>Di Bawah Ekspektasi</td>
                                            <td>Di Bawah Ekspektasi</td>
                                            <td>SANGAT KURANG</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="pola-distribusi-contoh" role="tabpanel" aria-labelledby="pola-distribusi-contoh-tab">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-4">Pola Distribusi (Contoh)</h5>
                        
                        <div class="row">
                            <!-- Sangat Baik 1 -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-center font-weight-bold">Sangat Baik</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered mb-3">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Pola Distribusi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Sangat Kurang</td>
                                                    <td class="text-center">0</td>
                                                </tr>
                                                <tr>
                                                    <td>Kurang/Misconduct</td>
                                                    <td class="text-center">1</td>
                                                </tr>
                                                <tr>
                                                    <td>Butuh Perbaikan</td>
                                                    <td class="text-center">3</td>
                                                </tr>
                                                <tr>
                                                    <td>Baik</td>
                                                    <td class="text-center">7</td>
                                                </tr>
                                                <tr>
                                                    <td>Sangat Baik</td>
                                                    <td class="text-center">13</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td>Jumlah</td>
                                                    <td class="text-center">24</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div style="position: relative; height: 280px; width: 100%; background: #ffffff; padding: 10px; border: 2px solid #dee2e6; margin-top: 15px;">
                                            <canvas id="chart1" width="400" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Baik -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-center font-weight-bold">Baik</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered mb-3">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Pola Distribusi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Sangat Kurang</td>
                                                    <td class="text-center">2</td>
                                                </tr>
                                                <tr>
                                                    <td>Kurang/Misconduct</td>
                                                    <td class="text-center">3</td>
                                                </tr>
                                                <tr>
                                                    <td>Butuh Perbaikan</td>
                                                    <td class="text-center">6</td>
                                                </tr>
                                                <tr>
                                                    <td>Baik</td>
                                                    <td class="text-center">11</td>
                                                </tr>
                                                <tr>
                                                    <td>Sangat Baik</td>
                                                    <td class="text-center">2</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td>Jumlah</td>
                                                    <td class="text-center">24</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div style="position: relative; height: 280px; width: 100%; background: #ffffff; padding: 10px; border: 2px solid #dee2e6; margin-top: 15px;">
                                            <canvas id="chart2" width="400" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Butuh Perbaikan -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-center font-weight-bold">Butuh Perbaikan</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered mb-3">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Pola Distribusi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Sangat Kurang</td>
                                                    <td class="text-center">3</td>
                                                </tr>
                                                <tr>
                                                    <td>Kurang/Misconduct</td>
                                                    <td class="text-center">4</td>
                                                </tr>
                                                <tr>
                                                    <td>Butuh Perbaikan</td>
                                                    <td class="text-center">10</td>
                                                </tr>
                                                <tr>
                                                    <td>Baik</td>
                                                    <td class="text-center">4</td>
                                                </tr>
                                                <tr>
                                                    <td>Sangat Baik</td>
                                                    <td class="text-center">3</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td>Jumlah</td>
                                                    <td class="text-center">24</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div style="position: relative; height: 280px; width: 100%; background: #ffffff; padding: 10px; border: 2px solid #dee2e6; margin-top: 15px;">
                                            <canvas id="chart3" width="400" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kurang/Misconduct -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-center font-weight-bold">Kurang/Misconduct</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered mb-3">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Pola Distribusi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Sangat Kurang</td>
                                                    <td class="text-center">2</td>
                                                </tr>
                                                <tr>
                                                    <td>Kurang/Misconduct</td>
                                                    <td class="text-center">11</td>
                                                </tr>
                                                <tr>
                                                    <td>Butuh Perbaikan</td>
                                                    <td class="text-center">6</td>
                                                </tr>
                                                <tr>
                                                    <td>Baik</td>
                                                    <td class="text-center">3</td>
                                                </tr>
                                                <tr>
                                                    <td>Sangat Baik</td>
                                                    <td class="text-center">2</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td>Jumlah</td>
                                                    <td class="text-center">24</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div style="position: relative; height: 280px; width: 100%; background: #ffffff; padding: 10px; border: 2px solid #dee2e6; margin-top: 15px;">
                                            <canvas id="chart4" width="400" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Sangat Kurang -->
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 text-center font-weight-bold">Sangat Kurang</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered mb-3">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Pola Distribusi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Sangat Kurang</td>
                                                    <td class="text-center">13</td>
                                                </tr>
                                                <tr>
                                                    <td>Kurang/Misconduct</td>
                                                    <td class="text-center">7</td>
                                                </tr>
                                                <tr>
                                                    <td>Butuh Perbaikan</td>
                                                    <td class="text-center">3</td>
                                                </tr>
                                                <tr>
                                                    <td>Baik</td>
                                                    <td class="text-center">1</td>
                                                </tr>
                                                <tr>
                                                    <td>Sangat Baik</td>
                                                    <td class="text-center">0</td>
                                                </tr>
                                                <tr class="font-weight-bold">
                                                    <td>Jumlah</td>
                                                    <td class="text-center">24</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div style="position: relative; height: 280px; width: 100%; background: #ffffff; padding: 10px; border: 2px solid #dee2e6; margin-top: 15px;">
                                            <canvas id="chart5" width="400" height="250"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <script>
                        // Object untuk menyimpan chart instances
                        var chartInstances = {};
                        
                        // Fungsi untuk membuat semua chart
                        function createPolaDistribusiCharts() {
                            console.log('createPolaDistribusiCharts() dipanggil');
                            
                            // Cek apakah Chart sudah di-define
                            if (typeof Chart === 'undefined') {
                                console.error('Chart.js tidak ditemukan');
                                return;
                            }
                            
                            console.log('Chart.js berhasil di-load');
                            
                            // Cek apakah tab visible
                            var isVisible = $('#pola-distribusi-contoh').is(':visible');
                            console.log('Tab visible:', isVisible);
                            
                            if (!isVisible) {
                                console.warn('Tab belum visible, skip creating charts');
                                return;
                            }
                            
                            // Chart 1 - Sangat Baik
                            var ctx1 = document.getElementById('chart1');
                            console.log('canvas chart1:', ctx1);
                            
                            if (ctx1) {
                                var parent = ctx1.parentElement;
                                console.log('Parent dimensions:', parent.offsetWidth, 'x', parent.offsetHeight);
                                console.log('Canvas dimensions:', ctx1.width, 'x', ctx1.height);
                                
                                // Destroy existing chart if any
                                if (chartInstances['chart1'] && typeof chartInstances['chart1'].destroy === 'function') {
                                    chartInstances['chart1'].destroy();
                                }
                                console.log('Membuat chart 1...');
                                try {
                                    chartInstances['chart1'] = new Chart(ctx1.getContext('2d'), {
                                        type: 'line',
                                        data: {
                                            labels: ['Sangat Kurang', 'Kurang/Misconduct', 'Butuh Perbaikan', 'Baik', 'Sangat Baik'],
                                            datasets: [{
                                                label: 'Pola Distribusi',
                                                data: [0, 1, 3, 7, 13],
                                                borderColor: 'rgb(54, 162, 235)',
                                                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                                borderWidth: 3,
                                                tension: 0.4,
                                                fill: true,
                                                pointRadius: 5,
                                                pointBackgroundColor: 'rgb(54, 162, 235)',
                                                pointBorderColor: '#fff',
                                                pointBorderWidth: 2
                                            }]
                                        },
                                        options: {
                                            responsive: false,
                                            maintainAspectRatio: true,
                                            plugins: {
                                                legend: { display: false },
                                                title: { 
                                                    display: true, 
                                                    text: 'Pola Distribusi', 
                                                    font: { size: 14, weight: 'bold' },
                                                    padding: 10
                                                }
                                            },
                                            scales: {
                                                y: { 
                                                    beginAtZero: true, 
                                                    max: 15,
                                                    grid: { color: 'rgba(0, 0, 0, 0.1)' },
                                                    ticks: { font: { size: 11 } } 
                                                },
                                                x: { 
                                                    grid: { display: false },
                                                    ticks: { font: { size: 10 }, maxRotation: 45, minRotation: 45 } 
                                                }
                                            }
                                        }
                                    });
                                    console.log('Chart 1 created successfully!', chartInstances['chart1']);
                                } catch(e) {
                                    console.error('Error creating chart 1:', e);
                                }
                            } else {
                                console.error('Canvas chart1 not found');
                            }
                            
                            // Chart 2 - Baik
                            var ctx2 = document.getElementById('chart2');
                            if (ctx2) {
                                if (chartInstances['chart2'] && typeof chartInstances['chart2'].destroy === 'function') chartInstances['chart2'].destroy();
                                chartInstances['chart2'] = new Chart(ctx2.getContext('2d'), {
                                    type: 'line',
                                    data: {
                                        labels: ['Sangat Kurang', 'Kurang/Misconduct', 'Butuh Perbaikan', 'Baik', 'Sangat Baik'],
                                        datasets: [{
                                            label: 'Pola Distribusi',
                                            data: [2, 3, 6, 11, 2],
                                            borderColor: 'rgb(54, 162, 235)',
                                            borderWidth: 2,
                                            tension: 0.3,
                                            fill: false,
                                            pointRadius: 3,
                                            pointBackgroundColor: 'rgb(54, 162, 235)'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { display: false },
                                            title: { display: true, text: 'Pola Distribusi', font: { size: 12 } }
                                        },
                                        scales: {
                                            y: { beginAtZero: true, max: 15, ticks: { font: { size: 10 } } },
                                            x: { ticks: { font: { size: 9 }, maxRotation: 45, minRotation: 45 } }
                                        }
                                    }
                                });
                            }
                            
                            // Chart 3 - Butuh Perbaikan
                            var ctx3 = document.getElementById('chart3');
                            if (ctx3) {
                                if (chartInstances['chart3'] && typeof chartInstances['chart3'].destroy === 'function') chartInstances['chart3'].destroy();
                                chartInstances['chart3'] = new Chart(ctx3.getContext('2d'), {
                                    type: 'line',
                                    data: {
                                        labels: ['Sangat Kurang', 'Kurang/Misconduct', 'Butuh Perbaikan', 'Baik', 'Sangat Baik'],
                                        datasets: [{
                                            label: 'Pola Distribusi',
                                            data: [3, 4, 10, 4, 3],
                                            borderColor: 'rgb(54, 162, 235)',
                                            borderWidth: 2,
                                            tension: 0.3,
                                            fill: false,
                                            pointRadius: 3,
                                            pointBackgroundColor: 'rgb(54, 162, 235)'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { display: false },
                                            title: { display: true, text: 'Pola Distribusi', font: { size: 12 } }
                                        },
                                        scales: {
                                            y: { beginAtZero: true, max: 15, ticks: { font: { size: 10 } } },
                                            x: { ticks: { font: { size: 9 }, maxRotation: 45, minRotation: 45 } }
                                        }
                                    }
                                });
                            }
                            
                            // Chart 4 - Kurang/Misconduct
                            var ctx4 = document.getElementById('chart4');
                            if (ctx4) {
                                if (chartInstances['chart4'] && typeof chartInstances['chart4'].destroy === 'function') chartInstances['chart4'].destroy();
                                chartInstances['chart4'] = new Chart(ctx4.getContext('2d'), {
                                    type: 'line',
                                    data: {
                                        labels: ['Sangat Kurang', 'Kurang/Misconduct', 'Butuh Perbaikan', 'Baik', 'Sangat Baik'],
                                        datasets: [{
                                            label: 'Pola Distribusi',
                                            data: [2, 11, 6, 3, 2],
                                            borderColor: 'rgb(54, 162, 235)',
                                            borderWidth: 2,
                                            tension: 0.3,
                                            fill: false,
                                            pointRadius: 3,
                                            pointBackgroundColor: 'rgb(54, 162, 235)'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { display: false },
                                            title: { display: true, text: 'Pola Distribusi', font: { size: 12 } }
                                        },
                                        scales: {
                                            y: { beginAtZero: true, max: 15, ticks: { font: { size: 10 } } },
                                            x: { ticks: { font: { size: 9 }, maxRotation: 45, minRotation: 45 } }
                                        }
                                    }
                                });
                            }
                            
                            // Chart 5 - Sangat Kurang
                            var ctx5 = document.getElementById('chart5');
                            if (ctx5) {
                                if (chartInstances['chart5'] && typeof chartInstances['chart5'].destroy === 'function') chartInstances['chart5'].destroy();
                                chartInstances['chart5'] = new Chart(ctx5.getContext('2d'), {
                                    type: 'line',
                                    data: {
                                        labels: ['Sangat Kurang', 'Kurang/Misconduct', 'Butuh Perbaikan', 'Baik', 'Sangat Baik'],
                                        datasets: [{
                                            label: 'Pola Distribusi',
                                            data: [13, 7, 3, 1, 0],
                                            borderColor: 'rgb(54, 162, 235)',
                                            borderWidth: 2,
                                            tension: 0.3,
                                            fill: false,
                                            pointRadius: 3,
                                            pointBackgroundColor: 'rgb(54, 162, 235)'
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: {
                                            legend: { display: false },
                                            title: { display: true, text: 'Pola Distribusi', font: { size: 12 } }
                                        },
                                        scales: {
                                            y: { beginAtZero: true, max: 15, ticks: { font: { size: 10 } } },
                                            x: { ticks: { font: { size: 9 }, maxRotation: 45, minRotation: 45 } }
                                        }
                                    }
                                });
                            }
                        }
                        
                        // Event listener untuk tab pola distribusi contoh
                        $(document).ready(function() {
                            // Panggil saat tab ditampilkan (shown.bs.tab event)
                            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                                var target = $(e.target).attr("href");
                                if (target === '#pola-distribusi-contoh') {
                                    console.log('Tab pola distribusi contoh shown, creating charts...');
                                    setTimeout(function() {
                                        createPolaDistribusiCharts();
                                    }, 200);
                                }
                            });
                            
                            // Juga handle click event untuk fallback
                            $('#pola-distribusi-contoh-tab').on('click', function() {
                                setTimeout(function() {
                                    console.log('Creating charts on tab click');
                                    createPolaDistribusiCharts();
                                }, 300);
                            });
                            
                            // Auto-trigger jika tab sudah aktif saat page load
                            setTimeout(function() {
                                if ($('#pola-distribusi-contoh').hasClass('active')) {
                                    console.log('Creating charts on page load');
                                    createPolaDistribusiCharts();
                                }
                            }, 500);
                        });
                        </script>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="pola-distribusi" role="tabpanel" aria-labelledby="pola-distribusi-tab">
                <?php
                    // Ambil data pola distribusi dari database
                    $data_pola = [];
                    $query_pola = mysqli_query($kon, "SELECT kategori, nilai FROM pola_distribusi WHERE username='default' ORDER BY 
                        CASE kategori 
                            WHEN 'Sangat Kurang' THEN 1
                            WHEN 'Kurang/Misconduct' THEN 2
                            WHEN 'Butuh Perbaikan' THEN 3
                            WHEN 'Baik' THEN 4
                            WHEN 'Sangat Baik' THEN 5
                        END");
                    
                    if ($query_pola && mysqli_num_rows($query_pola) > 0) {
                        while ($row = mysqli_fetch_assoc($query_pola)) {
                            $data_pola[$row['kategori']] = $row['nilai'];
                        }
                    } else {
                        // Data default jika belum ada di database
                        $data_pola = [
                            'Sangat Kurang' => 2,
                            'Kurang/Misconduct' => 1,
                            'Butuh Perbaikan' => 6,
                            'Baik' => 11,
                            'Sangat Baik' => 2
                        ];
                    }
                    
                    // Hitung total
                    $total = array_sum($data_pola);
                    
                    // Data hardcode untuk kolom Pola Distribusi lainnya
                    $pola_istimewa = ['Sangat Kurang' => 0, 'Kurang/Misconduct' => 2, 'Butuh Perbaikan' => 3, 'Baik' => 2, 'Sangat Baik' => 13];
                    $pola_baik = ['Sangat Kurang' => 1, 'Kurang/Misconduct' => 3, 'Butuh Perbaikan' => 10, 'Baik' => 11, 'Sangat Baik' => 7];
                    $pola_butuh = ['Sangat Kurang' => 3, 'Kurang/Misconduct' => 4, 'Butuh Perbaikan' => 10, 'Baik' => 4, 'Sangat Baik' => 3];
                    $pola_kurang = ['Sangat Kurang' => 2, 'Kurang/Misconduct' => 11, 'Butuh Perbaikan' => 6, 'Baik' => 3, 'Sangat Baik' => 2];
                    $pola_sangat_kurang = ['Sangat Kurang' => 13, 'Kurang/Misconduct' => 7, 'Butuh Perbaikan' => 3, 'Baik' => 1, 'Sangat Baik' => 0];
                ?>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h5>BAIK</h5>
                                <p class="small text-muted">KURVA DISTRIBUSI PENINGKAT KINERJA PEGAWAI<br>DENGAN CAPAIAN KINERJA ORGANISASI BAIK</p>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th rowspan="2" class="align-middle" style="min-width: 150px;">Kategori</th>
                                        <th rowspan="2" class="align-middle text-center" style="width: 80px;">Nilai</th>
                                        <th colspan="5" class="text-center">Pola Distribusi</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="width: 100px;">Istimewa</th>
                                        <th class="text-center" style="width: 100px;">Baik</th>
                                        <th class="text-center" style="width: 100px;">Butuh Perbaikan</th>
                                        <th class="text-center" style="width: 100px;">Kurang/Misconduct</th>
                                        <th class="text-center" style="width: 100px;">Sangat Kurang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $kategori_list = ['Sangat Kurang', 'Kurang/Misconduct', 'Butuh Perbaikan', 'Baik', 'Sangat Baik'];
                                    foreach ($kategori_list as $kat) {
                                        $nilai = isset($data_pola[$kat]) ? $data_pola[$kat] : 0;
                                        echo "<tr>";
                                        echo "<td>{$kat}</td>";
                                        echo "<td class='text-center'>{$nilai}</td>";
                                        echo "<td class='text-center'>{$pola_istimewa[$kat]}</td>";
                                        echo "<td class='text-center'>{$pola_baik[$kat]}</td>";
                                        echo "<td class='text-center'>{$pola_butuh[$kat]}</td>";
                                        echo "<td class='text-center'>{$pola_kurang[$kat]}</td>";
                                        echo "<td class='text-center'>{$pola_sangat_kurang[$kat]}</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                    <tr class="font-weight-bold bg-light">
                                        <td>Jumlah</td>
                                        <td class="text-center"><?php echo $total; ?></td>
                                        <td class="text-center">24</td>
                                        <td class="text-center">24</td>
                                        <td class="text-center">24</td>
                                        <td class="text-center">24</td>
                                        <td class="text-center">24</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printAllSKP() {
    // Simpan tab yang sedang aktif
    var currentTab = $('.nav-tabs .nav-link.active').attr('href');
    
    // Daftar tab yang akan di-print
    var tabsToPrint = [
        '#cover',
        '#skp-jaif', 
        '#lampiran-skp',
        '#evaluasi-kinerja',
        '#dok-evaluasi'
    ];
    
    // Aktifkan semua tab yang akan di-print agar kontennya ter-render
    var activatePromises = [];
    tabsToPrint.forEach(function(tabId) {
        var tabLink = $('a[href="' + tabId + '"]');
        if (tabLink.length) {
            tabLink.tab('show');
        }
    });
    
    // Tunggu sebentar untuk memastikan semua konten ter-render
    setTimeout(function() {
        // Kembali ke tab awal
        $('a[href="' + currentTab + '"]').tab('show');
        
        // Sembunyikan elemen yang tidak perlu di-print
        $('.btn, .nav-tabs, .dashboard-header, #wrapper, #sidebar-wrapper').hide();
        
        // Tambahkan page break CSS untuk setiap tab
        var style = document.createElement('style');
        style.innerHTML = `
            @media print {
                .tab-pane { 
                    display: block !important; 
                    page-break-after: always;
                }
                .tab-pane:last-child {
                    page-break-after: auto;
                }
                .nav-tabs, .btn, .dashboard-header {
                    display: none !important;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Print
        window.print();
        
        // Cleanup: hapus style dan tampilkan kembali elemen
        setTimeout(function() {
            document.head.removeChild(style);
            $('.btn, .nav-tabs, .dashboard-header, #wrapper, #sidebar-wrapper').show();
        }, 100);
    }, 500);
}
</script>
