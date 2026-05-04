<?php
$page_title = "Hapus Anggota";
require_once '../../config/database_tugas.php';
require_once '../../includes/header.php';

// Cek apakah ada parameter ID yang dikirim
if (isset($_GET['id'])) {
    // Sanitasi ID menggunakan fungsi buatanmu
    $id = sanitize($_GET['id']);

    // Validasi tambahan: Pastikan ID tidak kosong
    if (!empty($id)) {
        // Query Hapus
        $sql = "DELETE FROM anggota WHERE id_anggota = '$id'";

        if ($conn->query($sql) === TRUE) {
            // Redirect dengan pesan sukses
            header("Location: index.php?msg=deleted");
            exit();
        } else {
            // Jika gagal karena constraint (misal: anggota masih meminjam buku)
            echo "
            <script>
                alert('Gagal menghapus: Data ini mungkin terhubung dengan tabel lain.');
                window.location.href = 'index.php';
            </script>";
        }
    }
} else {
    // Jika mencoba akses langsung tanpa ID
    header("Location: index.php");
    exit();
}

closeConnection();
require_once '../../includes/footer.php';
?>