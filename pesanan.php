
<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

loginWajib();

$orders = [];
$errorPesanan = false;

try {
    $stmt = $pdo->prepare(
        "SELECT *
         FROM orders
         WHERE user_id = ?
         ORDER BY created_at DESC"
    );

    $stmt->execute([$_SESSION['user']['id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $errorPesanan = true;
}

$judul = 'Pesanan Saya';
require __DIR__ . '/header.php';
?>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-label">AKUN SAYA</span>
                <h2>Pesanan Saya</h2>
                <p>Riwayat pembelian spare part Mekanik Go.</p>
            </div>

            <a href="produk.php" class="btn btn-primary">
                + Belanja Produk
            </a>
        </div>

        <?php if ($errorPesanan): ?>

            <div class="empty-state">
                <div class="empty-icon">⚠️</div>
                <h3>Data pesanan belum dapat ditampilkan</h3>
                <p>
                    Pastikan tabel orders sudah dibuat di database
                    mekanik_go.
                </p>
            </div>

        <?php elseif (!$orders): ?>

            <div class="empty-state">
                <div class="empty-icon">🛒</div>
                <h3>Belum ada pesanan produk</h3>
                <p>
                    Kamu belum memiliki riwayat pembelian spare part.
                    Yuk, cari produk yang kamu butuhkan.
                </p>
                <a href="produk.php" class="btn btn-primary">
                    Mulai Belanja →
                </a>
            </div>

        <?php else: ?>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Kode Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= e($o['kode']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= e(date(
                                        'd-m-Y H:i',
                                        strtotime($o['created_at'])
                                    )) ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= rupiah($o['total']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= e($o['metode_pembayaran']) ?>
                                </td>

                                <td>
                                    <span class="order-status">
                                        <?= e($o['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>
</section>

<style>
.order-status {
    display: inline-block;
    padding: 6px 11px;
    color: #1D4ED8;
    background: #DBEAFE;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.empty-state .btn {
    margin-top: 20px;
}
</style>

<?php require __DIR__ . '/footer.php'; ?>
