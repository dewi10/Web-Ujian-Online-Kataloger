<form action="pages/guru/soal/simpan-import.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id_ujian" value="<?php echo isset($_POST['id_ujian']) ? $_POST['id_ujian'] : ''; ?>">
    <input type="hidden" name="tipe_soal" value="<?php echo isset($_POST['tipe_soal']) ? $_POST['tipe_soal'] : ''; ?>">

    <div class="alert alert-info">
        <strong>Format CSV:</strong><br>
        - Untuk PG: <code>soal,A,B,C,D,E,kunci</code> (kunci bisa A/B/C/D/E atau 1/2/3/4/5)<br>
        - Untuk Essay: <code>soal</code><br>
        - Baris header boleh ada, boleh tidak.
    </div>

    <div class="form-group">
        <label>Pilih file CSV:</label>
        <input type="file" name="file_import" class="form-control" accept=".csv" required>
    </div>

    <button type="submit" name="import_soal" class="btn btn-primary">Import</button>
</form>
