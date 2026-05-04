<?php
$page_title = "Data Buku";
require_once '../../config/database.php';
require_once '../../includes/header.php';
 
// Query semua buku
$query = "SELECT * FROM buku ORDER BY created_at DESC";
$result = $conn->query($query);
?>
 
<div class="container">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2><i class="bi bi-book"></i> Data Buku Perpustakaan</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="create.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Buku Baru
            </a>
        </div>
    </div>
    
    <?php
    // Tampilkan pesan success/error
    if (isset($_GET['success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show">';
        echo '<i class="bi bi-check-circle"></i> ' . htmlspecialchars($_GET['success']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
    
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show">';
        echo '<i class="bi bi-x-circle"></i> ' . htmlspecialchars($_GET['error']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
    ?>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Daftar Buku</h5>
        </div>
        <div class="card-body">
            <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th width="100">Kode</th>
                            <th>Judul Buku</th>
                            <th>Kategori</th>
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th width="80">Tahun</th>
                            <th width="120">Harga</th>
                            <th width="60">Stok</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = $result->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><code><?php echo htmlspecialchars($row['kode_buku']); ?></code></td>
                            <td><?php echo htmlspecialchars($row['judul']); ?></td>
                            <td>
                                <span class="badge bg-primary">
                                    <?php echo htmlspecialchars($row['kategori']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['pengarang']); ?></td>
                            <td><?php echo htmlspecialchars($row['penerbit']); ?></td>
                            <td><?php echo $row['tahun_terbit']; ?></td>
                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <?php if ($row['stok'] > 0): ?>
                                    <span class="badge bg-success"><?php echo $row['stok']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Habis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id_buku']; ?>" 
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="delete.php?id=<?php echo $row['id_buku']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Yakin ingin menghapus buku ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info mt-3 mb-0">
                <i class="bi bi-info-circle"></i> 
                <strong>Total:</strong> <?php echo $result->num_rows; ?> buku terdaftar
            </div>
            
            <?php else: ?>
            <div class="alert alert-warning mb-0">
                <i class="bi bi-exclamation-triangle"></i> 
                Belum ada data buku. Silakan tambah buku baru.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
 
<?php
closeConnection();
require_once '../../includes/footer.php';
?>