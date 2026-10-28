
<?php
require_once 'koneksi.php';
require_once 'functions.php';
loginWajib();

if ($_SESSION['user']['role'] !== 'pengguna') {
    exit('Akses ditolak.');
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfPeriksa();

    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'hapus') {
        unset($_SESSION['cart'][$id]);
    } elseif ($aksi === 'ubah' && $id) {
        $jumlah = filter_input(
            INPUT_POST, 'jumlah', FILTER_VALIDATE_INT
        );

        $stmt = $pdo->prepare(
            "SELECT stok FROM products WHERE id = ? AND aktif = 1"
        );
        $stmt->execute([$id]);
        $p = $stmt->fetch();

        if ($p && $jumlah > 0) {
            $_SESSION['cart'][$id] = min(
                $jumlah, (int)$p['stok']
            );
        } else {
            unset($_SESSION['cart'][$id]);
        }
    }

    header('Location: keranjang.php');
    exit;
}

$cart = $_SESSION['cart'];
$items = [];
$total = 0;

if ($cart) {
    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare(
        "SELECT * FROM products
         WHERE aktif = 1 AND id IN ($placeholders)"
    );
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as $p) {
        $jumlah = min(
            (int)$cart[$p['id']],
            (int)$p['stok']
        );

        if ($jumlah < 1) {
            unset($_SESSION['cart'][$p['id']]);
            continue;
        }

        $_SESSION['cart'][$p['id']] = $jumlah;
        $p['jumlah'] = $jumlah;
        $p['subtotal'] = $jumlah * (float)$p['harga'];
        $total += $p['subtotal'];
        $items[] = $p;
    }
}

$judul = 'Keranjang Belanja';
require 'header.php';
?>

<section class="section">
    <div class="section-heading">
        <h2>Keranjang Belanja</h2>
        <p>Periksa barang dan jumlah sebelum checkout.</p>
    </div>

    <?php if (!$items): ?>
        <div class="empty">
            Keranjang masih kosong.
            <p><a href="produk.php">Cari spare part</a></p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Subtotal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $p): ?>
                        <tr>
                            <td><?= e($p['nama']) ?></td>
                            <td><?= rupiah($p['harga']) ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="csrf"
                                        value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="product_id"
                                        value="<?= (int)$p['id'] ?>">
                                    <input type="hidden" name="aksi" value="ubah">
                                    <input type="number" name="jumlah"
                                        min="1" max="<?= (int)$p['stok'] ?>"
                                        value="<?= (int)$p['jumlah'] ?>"
                                        style="width:85px">
                                    <button class="btn btn-small">Ubah</button>
                                </form>
                            </td>
                            <td><?= rupiah($p['subtotal']) ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="csrf"
                                        value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="product_id"
                                        value="<?= (int)$p['id'] ?>">
                                    <input type="hidden" name="aksi" value="hapus">
                                    <button class="btn btn-small btn-orange">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card" style="margin-top:22px">
            <h3>Total belanja</h3>
            <div class="price"><?= rupiah($total) ?></div>
            <a class="btn" href="checkout.php">Lanjut Checkout</a>
        </div>
    <?php endif; ?>
</section>

<?php require 'footer.php'; ?>
