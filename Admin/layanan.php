
<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../functions.php';
roleWajib('admin');

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfPeriksa();

    $aksi = $_POST['aksi'] ?? '';
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_FLOAT);

    if (
        $nama === '' || $harga === false ||
        $harga === null || $harga < 0
    ) {
        $pesan = 'Nama layanan dan harga harus valid.';
    } elseif ($aksi === 'tambah') {
        $stmt = $pdo->prepare(
            "INSERT INTO services (nama,deskripsi,harga)
             VALUES (?,?,?)"
        );
        $stmt->execute([$nama, $deskripsi, $harga]);
        $pesan = 'Layanan berhasil ditambahkan.';
    } elseif ($aksi === 'ubah') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $aktif = (int)($_POST['aktif'] ?? 0);

        if (!$id || !in_array($aktif, [0, 1], true)) {
            $pesan = 'Data perubahan tidak valid.';
        } else {
            $stmt = $pdo->prepare(
                "UPDATE services SET nama=?, deskripsi=?,
                 harga=?, aktif=? WHERE id=?"
            );
            $stmt->execute([
                $nama, $deskripsi, $harga, $aktif, $id
            ]);
            $pesan = 'Layanan berhasil diperbarui.';
        }
    }
}

$layanan = $pdo->query(
    "SELECT * FROM services ORDER BY id DESC"
)->fetchAll();

$judul = 'Kelola Layanan';
require __DIR__ . '/../header.php';
?>

<section class="section">
    <div class="section-heading">
        <h2>Kelola Layanan Servis</h2>
        <p>Atur nama, deskripsi, dan harga layanan.</p>
    </div>

    <?php if ($pesan): ?>
        <div class="alert alert-success"><?= e($pesan) ?></div>
    <?php endif; ?>

    <div class="form-card" style="margin-bottom:30px">
        <h2>Tambah Layanan</h2>
        <form method="POST">
            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="aksi" value="tambah">

            <div class="field">
                <label>Nama layanan</label>
                <input name="nama" maxlength="100" required>
            </div>
            <div class="field" style="margin-top:15px">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="2"></textarea>
            </div>
            <div class="field" style="margin-top:15px">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" min="0"
                    step="0.01" required>
            </div>
            <button class="btn btn-full">Tambah Layanan</button>
        </form>
    </div>

    <div class="grid">
        <?php foreach ($layanan as $s): ?>
            <div class="card">
                <form method="POST">
                    <input type="hidden" name="csrf"
                        value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="aksi" value="ubah">
                    <input type="hidden" name="id"
                        value="<?= (int)$s['id'] ?>">

                    <div class="field">
                        <label>Nama layanan</label>
                        <input name="nama" maxlength="100" required
                            value="<?= e($s['nama']) ?>">
                    </div>

                    <div class="field" style="margin-top:12px">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" rows="3"><?= e($s['deskripsi']) ?></textarea>
                    </div>

                    <div class="field" style="margin-top:12px">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" min="0"
                            step="0.01" required value="<?= e($s['harga']) ?>">
                    </div>

                    <div class="field" style="margin-top:12px">
                        <label>Status</label>
                        <select name="aktif">
                            <option value="1"
                                <?= $s['aktif'] ? 'selected' : '' ?>>
                                Aktif
                            </option>
                            <option value="0"
                                <?= !$s['aktif'] ? 'selected' : '' ?>>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                    <button class="btn btn-full">Simpan Perubahan</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>
