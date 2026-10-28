
<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../functions.php';
roleWajib('admin');

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfPeriksa();
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'tambah') {
        $nama = trim($_POST['nama'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');
        $merek = trim($_POST['merek'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $harga = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_FLOAT);
        $stok = filter_input(INPUT_POST, 'stok', FILTER_VALIDATE_INT);

        if (
            $nama === '' || $kategori === '' ||
            $harga === false || $harga === null || $harga < 0 ||
            $stok === false || $stok === null || $stok < 0
        ) {
            $pesan = 'Periksa nama, kategori, harga, dan stok.';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO products
                (nama,kategori,merek,deskripsi,harga,stok)
                VALUES (?,?,?,?,?,?)"
            );
            $stmt->execute([
                $nama, $kategori, $merek, $deskripsi, $harga, $stok
            ]);
            $pesan = 'Produk berhasil ditambahkan.';
        }
    } elseif ($aksi === 'ubah') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $harga = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_FLOAT);
        $stok = filter_input(INPUT_POST, 'stok', FILTER_VALIDATE_INT);
        $aktif = (int)($_POST['aktif'] ?? 0);

        if (
            !$id || $harga === false || $harga === null || $harga < 0 ||
            $stok === false || $stok === null || $stok < 0 ||
            !in_array($aktif, [0, 1], true)
        ) {
            $pesan = 'Data perubahan tidak valid.';
        } else {
            $stmt = $pdo->prepare(
                "UPDATE products SET harga=?, stok=?, aktif=? WHERE id=?"
            );
            $stmt->execute([$harga, $stok, $aktif, $id]);
            $pesan = 'Produk berhasil diperbarui.';
        }
    }
}

$produk = $pdo->query(
    "SELECT * FROM products ORDER BY id DESC"
)->fetchAll();

$judul = 'Kelola Produk';
require __DIR__ . '/../header.php';
?>

<section class="section">
    <div class="section-heading">
        <h2>Kelola Produk</h2>
        <p>Tambah produk dan kelola stok spare part.</p>
    </div>

    <?php if ($pesan): ?>
        <div class="alert alert-success"><?= e($pesan) ?></div>
    <?php endif; ?>

    <div class="form-card" style="margin-bottom:30px">
        <h2>Tambah Produk Baru</h2>

        <form method="POST">
            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="aksi" value="tambah">

            <div class="form-grid">
                <div class="field">
                    <label>Nama produk</label>
                    <input name="nama" required maxlength="120">
                </div>
                <div class="field">
                    <label>Kategori</label>
                    <input name="kategori" required maxlength="60">
                </div>
                <div class="field">
                    <label>Merek</label>
                    <input name="merek" maxlength="80">
                </div>
                <div class="field">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga"
                        min="0" step="0.01" required>
                </div>
                <div class="field">
                    <label>Stok</label>
                    <input type="number" name="stok"
                        min="0" required>
                </div>
                <div class="field field-full">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="2"></textarea>
                </div>
            </div>

            <button class="btn btn-full">Tambah Produk</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aktif</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produk as $p): ?>
                    <tr>
                        <td>
                            <strong><?= e($p['nama']) ?></strong><br>
                            <?= e($p['kategori']) ?>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="csrf"
                                    value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="aksi" value="ubah">
                                <input type="hidden" name="id"
                                    value="<?= (int)$p['id'] ?>">
                                <input type="number" name="harga"
                                    min="0" step="0.01" required
                                    value="<?= e($p['harga']) ?>">
                        </td>
                        <td>
                                <input type="number" name="stok"
                                    min="0" required
                                    value="<?= (int)$p['stok'] ?>">
                        </td>
                        <td>
                                <select name="aktif">
                                    <option value="1"
                                        <?= $p['aktif'] ? 'selected' : '' ?>>
                                        Aktif
                                    </option>
                                    <option value="0"
                                        <?= !$p['aktif'] ? 'selected' : '' ?>>
                                        Nonaktif
                                    </option>
                                </select>
                        </td>
                        <td>
                                <button class="btn btn-small">Simpan</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>
