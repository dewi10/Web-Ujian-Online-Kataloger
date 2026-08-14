<br>
<div class="card border-0">
    <div class="dashboard-header">
        <h3><i class="fas fa-user"></i> Profil</h3>
    </div>
    <div class="card-body">
    <?php
        include 'config/database.php';
        $query ="select * from siswa where id_siswa='".$_SESSION["id_siswa"]."' limit 1"; 
        $hasil=mysqli_query($kon,$query);
        $row = mysqli_fetch_array($hasil);
    ?>

        <form id="formProfilSiswa">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">NAMA</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="nama_siswa" value="<?php echo $row['nama_siswa']; ?>" required>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">NIP</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="nis" value="<?php echo $row['nis']; ?>" required>
                    <!-- <small class="text-muted">NIS/NIP Siswa</small> -->
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Nomor Seri Karpeg</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="nomor_seri_karpeg" value="<?php echo isset($row['nomor_seri_karpeg']) ? $row['nomor_seri_karpeg'] : ''; ?>">
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Tempat Tanggal Lahir</label>
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="tempat_lahir" value="<?php echo isset($row['tempat_lahir']) ? $row['tempat_lahir'] : ''; ?>" placeholder="Tempat Lahir">
                        </div>
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="tanggal_lahir" value="<?php echo isset($row['tanggal_lahir']) ? $row['tanggal_lahir'] : ''; ?>">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Jenis Kelamin</label>
                <div class="col-sm-9">
                    <select class="form-control" name="jk" required>
                        <option value="1" <?php echo $row['jk'] == 1 ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="2" <?php echo $row['jk'] == 2 ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Pangkat/Gol. Ruang</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="pangkat_gol" value="<?php echo isset($row['pangkat_gol']) ? $row['pangkat_gol'] : ''; ?>">
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Jabatan</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="jabatan" value="<?php echo isset($row['jabatan']) ? $row['jabatan'] : ''; ?>">
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Unit Kerja</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="unit_kerja" value="<?php echo isset($row['unit_kerja']) ? $row['unit_kerja'] : ''; ?>">
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Instansi</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="instansi" value="<?php echo isset($row['instansi']) ? $row['instansi'] : ''; ?>">
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Alamat</label>
                <div class="col-sm-9">
                    <textarea class="form-control" name="alamat" rows="3"><?php echo $row['alamat']; ?></textarea>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">No Telp</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="no_telp" value="<?php echo $row['no_telp']; ?>">
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Email</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" name="email" value="<?php echo $row['email']; ?>">
                </div>
            </div>
            
            <div class="form-group row">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
        
        <script>
        $(document).ready(function() {
            $('#formProfilSiswa').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: 'pages/siswa/profil/update.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('Profil berhasil diupdate!');
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
        <br>
    </div> 
</div>