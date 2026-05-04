<?php
$page_title = "Data Anggota";
// Memastikan path file sesuai dengan struktur folder kamu
require_once '../../config/database_tugas.php';
require_once '../../includes/header.php';

// Logic Search & Pagination
$keyword = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$limit = 10; 
$page = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;
$search_query = "%$keyword%";

// Count total data untuk pagination
$sql_count = "SELECT COUNT(*) AS total FROM anggota WHERE nama LIKE ? OR email LIKE ? OR telepon LIKE ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("sss", $search_query, $search_query, $search_query);
$stmt_count->execute();
$total_data = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data anggota
$sql_data = "SELECT * FROM anggota WHERE nama LIKE ? OR email LIKE ? OR telepon LIKE ? ORDER BY created_at DESC LIMIT ?, ?";
$stmt_data = $conn->prepare($sql_data);
$stmt_data->bind_param("sssii", $search_query, $search_query, $search_query, $start, $limit);
$stmt_data->execute();
$result = $stmt_data->get_result();
?>

<style>
    :root {
        --primary-color: #4e73df;
        --secondary-color: #858796;
    }
    body { background-color: #f8f9fc; }
    .card { border: none; border-radius: 15px; }
    .table thead th {
        background-color: #f8f9fc;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        color: var(--secondary-color);
        border-top: none;
    }
    .avatar-circle {
        width: 40px;
        height: 40px;
        background-color: #4e73df;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: bold;
    }
    .badge-soft-laki { background-color: #e3f2fd; color: #0d6efd; }
    .badge-soft-perempuan { background-color: #f3e5f5; color: #6f42c1; }
    .badge-soft-aktif { background-color: #e8f5e9; color: #2e7d32; }
    .badge-soft-non { background-color: #ffebee; color: #c62828; }
    .search-input { border-radius: 10px 0 0 10px; border-right: none; }
    .search-btn { border-radius: 0 10px 10px 0; }
    .table-hover tbody tr:hover { background-color: #f1f4ff; transition: 0.3s; }
</style>

<div class="container py-4">
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div>
                    <?php 
                        if($_GET['msg'] == 'success') echo "<strong>Berhasil!</strong> Anggota baru telah ditambahkan.";
                        elseif($_GET['msg'] == 'updated') echo "<strong>Berhasil!</strong> Data anggota telah diperbarui.";
                        elseif($_GET['msg'] == 'deleted') echo "<strong>Terhapus!</strong> Data anggota telah berhasil dihapus.";
                    ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h3 class="fw-bold text-dark m-0">Anggota Perpustakaan</h3>
            <p class="text-muted">Total data terdaftar: <span class="badge bg-primary rounded-pill"><?= $total_data ?></span></p>
        </div>
        <div class="col-auto">
            <a href="create.php" class="btn btn-primary shadow-sm px-4 py-2" style="border-radius: 10px;">
                <i class="fas fa-plus me-2"></i>Tambah Anggota
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="row mb-4">
                <div class="col-md-5">
                    <form action="" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control search-input py-2" 
                                   placeholder="Cari nama atau email..." value="<?= stripslashes($keyword) ?>">
                            <button class="btn btn-primary search-btn px-4" type="submit">Cari</button>
                            <?php if($keyword): ?>
                                <a href="index.php" class="btn btn-outline-secondary ms-2 rounded-3">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th width="50">NO</th>
                            <th>PROFIL</th>
                            <th>INFO KONTAK</th>
                            <th>JENIS KELAMIN</th>
                            <th>STATUS</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = $start + 1;
                        if ($result->num_rows > 0):
                            while($row = $result->fetch_assoc()): 
                                $inisial = strtoupper(substr($row['nama'], 0, 1));
                        ?>
                        <tr>
                            <td class="text-muted"><?= $no++ ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3"><?= $inisial ?></div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama']) ?></div>
                                        <div class="text-muted small"><?= $row['kode_anggota'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small"><i class="far fa-envelope me-1 text-primary"></i> <?= htmlspecialchars($row['email']) ?></div>
                                <div class="text-muted small"><i class="fas fa-phone me-1 text-success"></i> <?= $row['telepon'] ?></div>
                            </td>
                            <td>
                                <?php if($row['jenis_kelamin'] == 'Laki-laki'): ?>
                                    <span class="badge badge-soft-laki px-3 py-2 rounded-pill">Laki-laki</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-perempuan px-3 py-2 rounded-pill">Perempuan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Aktif'): ?>
                                    <span class="badge badge-soft-aktif px-3 py-2 rounded-pill">● Aktif</span>
                                <?php else: ?>
                                    <span class="badge badge-soft-non px-3 py-2 rounded-pill">● Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm border rounded-3" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu shadow border-0">
                                        <li>
                                            <a class="dropdown-item" href="update.php?id=<?= $row['id_anggota'] ?>">
                                                <i class="far fa-edit me-2 text-warning"></i>Edit Data
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="delete.php?id=<?= $row['id_anggota'] ?>" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus anggota: <?= addslashes($row['nama']) ?>?')">
                                                <i class="far fa-trash-alt me-2"></i>Hapus Anggota
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-search fa-4x text-light mb-3"></i>
                                <p class="text-muted">Data yang kamu cari tidak ditemukan.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($total_pages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-end">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link border-0 shadow-sm mx-1 rounded-3" href="?halaman=<?= $page - 1 ?>&search=<?= urlencode($keyword) ?>">Prev</a>
                    </li>
                    <?php for($i=1; $i<=$total_pages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link border-0 shadow-sm mx-1 rounded-3" href="?halaman=<?= $i ?>&search=<?= urlencode($keyword) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                        <a class="page-link border-0 shadow-sm mx-1 rounded-3" href="?halaman=<?= $page + 1 ?>&search=<?= urlencode($keyword) ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
closeConnection();
require_once '../../includes/footer.php';
?>