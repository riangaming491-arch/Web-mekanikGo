<?php
require_once "config.php";
$stmt = $pdo->query("SELECT id, nama, telepon, kendaraan, layanan, lokasi, jadwal, status, created_at FROM bookings ORDER BY created_at DESC");
$bookings = $stmt->fetchAll();
function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Booking | Mekanik Go</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <a class="brand" href="index.php"><span class="brand-icon">MG</span> Mekanik<span>Go</span></a>
        <a class="nav-button" href="index.php#booking">Tambah Booking</a>
    </header>
    <main class="records-wrap">
        <div class="records-heading">
            <p class="eyebrow">DEMO DATABASE</p>
            <h1>Data Booking Servis</h1>
            <p>Daftar permintaan booking yang tersimpan di database lokal.</p>
        </div>
        <div class="records-card">
            <div class="records-summary">Jumlah booking tersimpan: <strong><?= count($bookings) ?></strong></div>
            <?php if (!$bookings): ?>
                <div class="empty-state">
                    <div>📋</div><h2>Belum ada booking</h2>
                    <p>Buat satu booking melalui formulir untuk menguji penyimpanan database.</p>
                    <a class="button primary" href="index.php#booking">Buat Booking</a>
                </div>
            <?php else: ?>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th>No. Booking</th><th>Nama</th><th>Kendaraan</th><th>Layanan</th><th>Jadwal</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($bookings as $item): ?>
                            <tr>
                                <td>MG-<?= str_pad((string)$item["id"], 5, "0", STR_PAD_LEFT) ?></td>
                                <td><?= h($item["nama"]) ?><small><?= h($item["telepon"]) ?></small></td>
                                <td><?= h($item["kendaraan"]) ?></td>
                                <td><?= h($item["layanan"]) ?><small><?= h($item["lokasi"]) ?></small></td>
                                <td><?= h($item["jadwal"]) ?></td>
                                <td><span class="status-pill"><?= h($item["status"]) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <p class="records-note">Halaman ini hanya untuk demonstrasi lokal. Belum dilengkapi login admin dan tidak boleh dipasang ke server publik tanpa pengamanan.</p>
    </main>
    <footer>
        <a class="brand footer-brand" href="index.php"><span class="brand-icon">MG</span> Mekanik<span>Go</span></a>
        <p>Proyek PJBL • Pengembangan Aplikasi Berbasis Web</p>
        <p>© <?= date("Y") ?> Mekanik Go — Prototipe akademik</p>
    </footer>
</body>
</html>
