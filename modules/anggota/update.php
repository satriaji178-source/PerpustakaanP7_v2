<?php 
$page_title = "Update Anggota";
require_once '../../config/database_tugas.php';
require_once '../../includes/header.php';

$errors = [];
$id = isset($_GET['id']) ? sanitize($_GET['id']) : '';

// 1. Ambil data lama untuk populate form
if ($id) {
    $result = $conn->query("SELECT * FROM anggota WHERE id_anggota = '$id'");
    $d = $result->fetch_assoc();
    if (!$d) {
        die("Data tidak ditemukan!");
    }
} else {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode    = sanitize($_POST['kode_anggota']);
    $nama    = sanitize($_POST['nama']);
    $email   = sanitize($_POST['email']);
    $telp    = sanitize($_POST['telepon']);
    $alamat  = sanitize($_POST['alamat']);
    $tgl_lhr = sanitize($_POST['tanggal_lahir']);
    $jk      = sanitize($_POST['jenis_kelamin']);
    $kerja   = sanitize($_POST['pekerjaan']);
    $status  = sanitize($_POST['status']);

    // --- VALIDASI ---
    
    // Required fields
    if (empty($kode) || empty($nama) || empty($email) || empty($telp) || empty($tgl_lhr)) {
        $errors[] = "Field bertanda bintang (*) wajib diisi.";
    }

    // Format Email & Telepon
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if (!preg_match("/^08[0-9]{8,11}$/", $telp)) {
        $errors[] = "Nomor telepon tidak valid (Gunakan format 08xxxxxxxx).";
    }

    // Umur Minimal 10 Tahun
    $umur = (new DateTime())->diff(new DateTime($tgl_lhr))->y;
    if ($umur < 10) {
        $errors[] = "Umur minimal 10 tahun.";
    }

    // Validasi UNIK (Kecuali milik sendiri)
    $check_email = $conn->query("SELECT id_anggota FROM anggota WHERE email = '$email' AND id_anggota != '$id'");
    if ($check_email->num_rows > 0) {
        $errors[] = "Email sudah digunakan oleh anggota lain.";
    }

    $check_kode = $conn->query("SELECT id_anggota FROM anggota WHERE kode_anggota = '$kode' AND id_anggota != '$id'");
    if ($check_kode->num_rows > 0) {
        $errors[] = "Kode Anggota sudah digunakan oleh anggota lain.";
    }

    // --- PROSES UPDATE ---
    if (empty($errors)) {
        $sql = "UPDATE anggota SET 
                kode_anggota = '$kode', 
                nama = '$nama', 
                email = '$email', 
                telepon = '$telp', 
                alamat = '$alamat', 
                tanggal_lahir = '$tgl_lhr', 
                jenis_kelamin = '$jk', 
                pekerjaan = '$kerja', 
                status = '$status' 
                WHERE id_anggota = '$id'";

        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?msg=updated");
            exit();
        } else {
            $errors[] = "Gagal mengupdate data: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Update Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary py-3">
                    <h5 class="mb-0 text-white">Update Data Anggota: <?= $d['nama'] ?></h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0"><?php foreach ($errors as $e): echo "<li>$e</li>"; endforeach; ?></ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Kode Anggota *</label>
                                <input type="text" name="kode_anggota" class="form-control" value="<?= $d['kode_anggota'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap *</label>
                                <input type="text" name="nama" class="form-control" value="<?= $d['nama'] ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email *</label>
                                <input type="email" name="email" class="form-control" value="<?= $d['email'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor Telepon *</label>
                                <input type="text" name="telepon" class="form-control" value="<?= $d['telepon'] ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status Keanggotaan</label>
                                <select name="status" class="form-select">
                                    <option value="Aktif" <?= $d['status'] == 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                                    <option value="Nonaktif" <?= $d['status'] == 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Tanggal Lahir *</label>
                                <input type="date" name="tanggal_lahir" class="form-control" value="<?= $d['tanggal_lahir'] ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select">
                                    <option value="Laki-laki" <?= $d['jenis_kelamin'] == 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= $d['jenis_kelamin'] == 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control" value="<?= $d['pekerjaan'] ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"><?= $d['alamat'] ?></textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-light">Kembali</a>
                            <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Perubahan</button>
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