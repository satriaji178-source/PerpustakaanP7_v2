<?php 
$page_title = "Tambah Anggota";
require_once '../../config/database_tugas.php';
require_once '../../includes/header.php';

$errors = []; // Array untuk menampung pesan error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitasi & Ambil Input
    $kode    = sanitize($_POST['kode_anggota']);
    $nama    = sanitize($_POST['nama']);
    $email   = sanitize($_POST['email']);
    $telp    = sanitize($_POST['telepon']);
    $alamat  = sanitize($_POST['alamat']);
    $tgl_lhr = sanitize($_POST['tanggal_lahir']);
    $jk      = sanitize($_POST['jenis_kelamin']);
    $kerja   = sanitize($_POST['pekerjaan']);
    
    // Default Values
    $status  = 'Aktif';
    $tgl_df  = date('Y-m-d');

    // 2. Validasi Required (Semua field wajib diisi kecuali pekerjaan)
    if (empty($kode) || empty($nama) || empty($email) || empty($telp) || empty($alamat) || empty($tgl_lhr) || empty($jk)) {
        $errors[] = "Semua field bertanda bintang (*) wajib diisi.";
    }

    // 3. Validasi Format Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }

    // 4. Validasi Telepon (Hanya angka, diawali 08, panjang 10-13 karakter)
    if (!preg_match("/^08[0-9]{8,11}$/", $telp)) {
        $errors[] = "Nomor telepon harus diawali '08' dan terdiri dari 10-13 digit angka.";
    }

    // 5. Validasi Umur Minimal 10 Tahun
    $lahir = new DateTime($tgl_lhr);
    $hari_ini = new DateTime();
    $umur = $hari_ini->diff($lahir)->y;
    if ($umur < 10) {
        $errors[] = "Umur minimal anggota adalah 10 tahun (Umur Anda saat ini: $umur tahun).";
    }

    // 6. Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $sql = "INSERT INTO anggota (kode_anggota, nama, email, telepon, alamat, tanggal_lahir, jenis_kelamin, pekerjaan, tanggal_daftar, status) 
                VALUES ('$kode', '$nama', '$email', '$telp', '$alamat', '$tgl_lhr', '$jk', '$kerja', '$tgl_df', '$status')";

        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?msg=success");
            exit();
        } else {
            $errors[] = "Database Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Tambah Anggota - Validated</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">Form Pendaftaran Anggota</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= $e ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Anggota *</label>
                                <input type="text" name="kode_anggota" class="form-control" placeholder="Contoh: AGT-006" value="<?= @$kode ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap *</label>
                                <input type="text" name="nama" class="form-control" value="<?= @$nama ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email *</label>
                                <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="<?= @$email ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor Telepon *</label>
                                <input type="text" name="telepon" class="form-control" placeholder="08xxxxxxxx" value="<?= @$telp ?>" required>
                                <small class="text-muted">Format: 081234567890</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Kelamin *</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" <?= @$jk == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= @$jk == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?= @$tgl_lhr ?>" required>
                                <small class="text-muted">Minimal usia 10 tahun.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pekerjaan</label>
                            <input type="text" name="pekerjaan" class="form-control" value="<?= @$kerja ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat *</label>
                            <textarea name="alamat" class="form-control" rows="3" required><?= @$alamat ?></textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Simpan Anggota</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
closeConnection();
require_once '../../includes/footer.php';
?>