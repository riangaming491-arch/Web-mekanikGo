
<?php
require_once 'koneksi.php';
require_once 'functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfPeriksa();

    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    if ($_SESSION['user']['role'] !== 'pengguna') {
        exit('Akun ini tidak dapat berbelanja.');
    }

    $id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

    $stmt = $pdo->prepare(
        "SELECT id,stok FROM products
         WHERE id = ? AND aktif = 1"
    );
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if ($item && $item['stok'] > 0) {
        $jumlah = (int)($_SESSION['cart'][$id] ?? 0);
        if ($jumlah < (int)$item['stok']) {
            $_SESSION['cart'][$id] = $jumlah + 1;
        }
    }

    header('Location: produk.php');
    exit;
}

$cari = trim($_GET['q'] ?? '');
$kategori = trim($_GET['kategori'] ?? '');

$sql = "SELECT * FROM products WHERE aktif = 1";
$params = [];

if ($cari !== '') {
    $sql .= " AND (nama LIKE ? OR merek LIKE ?)";
    $params[] = "%$cari%";
    $params[] = "%$cari%";
}

if ($kategori !== '') {
    $sql .= " AND kategori = ?";
    $params[] = $kategori;
}

$sql .= " ORDER BY nama";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produk = $stmt->fetchAll();

$kategoriList = $pdo->query(
    "SELECT DISTINCT kategori FROM products
     WHERE aktif = 1 ORDER BY kategori"
)->fetchAll();

$judul = 'Spare Part';
require 'header.php';
?>

<
<section class="section product-catalog">
    <div class="catalog-hero">
        <div class="catalog-hero-content">
            <span class="catalog-label">⚙️ MEKANIK GO • SPARE PART</span>
            <h1>Rawat Kendaraanmu,<br><span>Mulai dari Sini.</span></h1>
            <p>Temukan oli, aki, ban, dan kebutuhan kendaraan lainnya dengan mudah.</p>

            <div class="catalog-benefits">
                <span>✓ Produk tersedia</span>
                <span>✓ Harga transparan</span>
                <span>✓ Mudah dipesan</span>
            </div>
        </div>
        <div class="catalog-hero-art">🏍️</div>
    </div>

    <div class="catalog-heading">
        <div>
            <span class="section-label">PILIH KEBUTUHANMU</span>
            <h2>Katalog Spare Part</h2>
            <p>Temukan produk yang cocok untuk kendaraanmu.</p>
        </div>
        <a href="keranjang.php" class="cart-link">
            🛒 Lihat Keranjang
            <span><?= array_sum($_SESSION['cart'] ?? []) ?></span>
        </a>
    </div>

    <form method="GET" class="catalog-filter">
        <div class="catalog-search">
            <span>🔎</span>
            <input
                type="search"
                name="q"
                value="<?= e($cari) ?>"
                placeholder="Cari nama produk atau merek..."
            >
        </div>

        <select name="kategori" aria-label="Filter kategori">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategoriList as $k): ?>
                <option
                    value="<?= e($k['kategori']) ?>"
                    <?= $kategori === $k['kategori'] ? 'selected' : '' ?>
                >
                    <?= e($k['kategori']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="btn btn-primary" type="submit">Cari Produk</button>
        <?php if ($cari !== '' || $kategori !== ''): ?>
            <a class="filter-reset" href="produk.php">Reset</a>
        <?php endif; ?>
    </form>

    <div class="catalog-result">
        <span>Produk yang tersedia</span>
        <strong><?= count($produk) ?> produk</strong>
    </div>

    <?php if ($produk): ?>
        <div class="catalog-grid">
            <?php foreach ($produk as $p): ?>
                <article class="catalog-card">
                    <div class="catalog-product-image">
                        <span class="catalog-category">
                            <?= e($p['kategori']) ?>
                        </span>

                        <div class="catalog-product-emoji">⚙️</div>

                        <?php if ((int)$p['stok'] > 0): ?>
                            <span class="catalog-stock available">
                                ● Stok tersedia
                            </span>
                        <?php else: ?>
                            <span class="catalog-stock unavailable">
                                Stok habis
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="catalog-product-info">
                        <p class="catalog-brand">
                            <?= e($p['merek'] ?? 'Spare Part') ?>
                        </p>

                        <h3><?= e($p['nama']) ?></h3>

                        <p class="catalog-description">
                            <?= e($p['deskripsi'] ?? 'Produk kebutuhan kendaraan.') ?>
                        </p>

                        <div class="catalog-product-bottom">
                            <div>
                                <span class="catalog-price-label">Harga produk</span>
                                <strong class="catalog-price">
                                    <?= rupiah($p['harga']) ?>
                                </strong>
                            </div>

                            <span class="catalog-stock-number">
                                Stok: <?= (int)$p['stok'] ?>
                            </span>
                        </div>

                        <?php if ((int)$p['stok'] > 0): ?>
                            <form method="POST" class="catalog-cart-form">
                                <input
                                    type="hidden"
                                    name="csrf"
                                    value="<?= e(csrfToken()) ?>"
                                >
                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?= (int)$p['id'] ?>"
                                >
                                <button class="catalog-add-button" type="submit">
                                    <span>＋</span> Tambahkan ke Keranjang
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="catalog-add-button disabled" disabled>
                                Stok Sedang Habis
                            </button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="catalog-empty">
            <div>🔍</div>
            <h3>Produk belum ditemukan</h3>
            <p>Coba kata kunci lain atau pilih kategori yang berbeda.</p>
            <a href="produk.php" class="btn btn-primary">Lihat Semua Produk</a>
        </div>
    <?php endif; ?>
</section>

<?php require 'footer.php'; ?>
