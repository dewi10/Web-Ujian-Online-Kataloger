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
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-file-signature"></i> PAK (Penilaian Angka Kredit)</h3>
    </div>
    <div class="card-body">
        <script> $('title').text('PAK'); </script>
        
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
                <div class="card">
                    <div class="card-body">
                        <h5>Konversi</h5>
                        <p>Konten untuk konversi predikat kinerja akan ditampilkan di sini.</p>
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
</style>
