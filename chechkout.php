
<?php
require_once 'koneksi.php';
require_once 'functions.php';
loginWajib();

if ($_SESSION['user']['role'] !== 'pengguna') {
    exit('Akses ditolak.');
}

if (empty($_SESSION['cart'])) {
    header('Location: keranjang.php');
    exit;
}

$pesan = '';
$kodePesanan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfPeriksa();

    $nama = trim($_POST['nama'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $metode = $_POST['metode'] ?? '';

    if (
        $nama === '' || $alamat === '' ||
        !preg_match('/^[0-9+ -]{8,20}$/', $telepon) ||
        !in_array($metode, ['COD', 'Transfer Manual'], true)
    ) {
        $pesan = 'Periksa nama, telepon, alamat, dan pembayaran.';
    } else {
        try {
            $pdo->beginTransaction();

            $total = 0;
            $detail = [];

            foreach ($_SESSION['cart'] as $id => $jumlah) {
                $stmt = $pdo->prepare(
                    "SELECT * FROM products
                     WHERE id = ? AND aktif = 1 FOR UPDATE"
                );
                $stmt->execute([(int)$id]);
                $p = $stmt->fetch();

                $jumlah = (int)$jumlah;

                if (!$p || $jumlah < 1 || $jumlah > (int)$p['stok']) {
                    throw new RuntimeException(
                        'Stok produk berubah. Periksa keranjang.'
                    );
                }

                $subtotal = $jumlah * (float)$p['harga'];
                $total += $subtotal;

                $detail[] = [
                    'id' => (int)$p['id'],
                    'nama' => $p['nama'],
                    'harga' => (float)$p['harga'],
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal
                ];
            }

            if (!$detail) {
                throw new RuntimeException('Keranjang kosong.');
            }

            $kodePesanan = buatKode('ORD-');

            $stmt = $pdo->prepare(
                "INSERT INTO orders
                (kode,user_id,nama_penerima,telepon,alamat,total,metode_pembayaran)
                VALUES (?,?,?,?,?,?,?)"
            );

            $stmt->execute([
                $kodePesanan,
                $_SESSION['user']['id'],
                $nama,
                $telepon,
                $alamat,
                $total,
                $metode
            ]);

            $orderId = (int)$pdo->lastInsertId();

            foreach ($detail as $d) {
                $stmt = $pdo->prepare(
                    "INSERT INTO order_items
                    (order_id,product_id,nama_produk,harga,jumlah,subtotal)
                    VALUES (?,?,?,?,?,?)"
                );

                $stmt->execute([
                    $orderId,
                    $d['id'],
                    $d['nama'],
                    $d['harga'],
                    $d['jumlah'],
                    $d['subtotal']
                ]);

                $stmt = $pdo->prepare(
                    "UPDATE products SET stok = stok - ?
                     WHERE id = ? AND stok >= ?"
                );

                $stmt->execute([
                    $d['jumlah'], $d['id'], $d['jumlah']
                ]);

                if ($stmt->rowCount() !== 1) {
                    throw new RuntimeException(
                        'Stok tidak mencukupi.'
                    );
                }
            }

            $pdo->commit();
            $_SESSION['cart'] = [];

        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $kodePesanan = '';

            $pesan = $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Pesanan gagal diproses. Silakan coba lagi.';
        }
    }
}

$judul = 'Checkout';
require 'header.php';
?>

<section class="form-wrap">
    <div class="form-card">
        <?php if ($kodePesanan): ?>
            <div class="alert alert-success">
                <h2>Pesanan berhasil dibuat!</h2>
                <p>Kode pesanan:</p>
                <h2><?= e($kodePesanan) ?></h2>
                <p>Status awal: Menunggu.</p>
                <p>Metode pembayaran yang dipilih dicatat pada pesanan.</p>
            </div>
            <a class="btn" href="pesanan.php">Lihat Pesanan Saya</a>
        <?php else: ?>
            <h1>Checkout</h1>
            <p class="form-description">
                Pastikan alamat dan kontak sudah benar.
            </p>

            <?php if ($pesan): ?>
                <div class="alert alert-error"><?= e($pesan) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf"
                    value="<?= e(csrfToken()) ?>">

                <div class="field">
                    <label>Nama penerima</label>
                    <input name="nama" required maxlength="100"
                        value="<?= e($_SESSION['user']['nama']) ?>">
                </div>

                <div class="field" style="margin-top:15px">
                    <label>Nomor telepon</label>
                    <input name="telepon" required maxlength="20"
                        value="<?= e($_SESSION['user']['telepon']) ?>">
                </div>

                <div class="field" style="margin-top:15px">
                    <label>Alamat pengiriman</label>
                    <textarea name="alamat" required rows="3"><?= e($_SESSION['user']['alamat'] ?? '') ?></textarea>
                </div>

                <div class="field" style="margin-top:15px">
                    <label>Metode pembayaran</label>
                    <select name="metode" required>
                        <option value="COD">Bayar di tempat (COD)</option>
                        <option value="Transfer Manual">Transfer manual</option>
                    </select>
                </div>

                <p class="form-description" style="margin-top:18px">
                    Total dihitung ulang dari harga dan stok database
                    saat pesanan diproses.
                </p>

                <button class="btn btn-full" type="submit">
                    Buat Pesanan
                </button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php require 'footer.php'; ?>
