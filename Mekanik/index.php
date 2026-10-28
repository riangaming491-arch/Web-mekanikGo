
<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../functions.php';
roleWajib('mekanik');

$idMekanik = (int)$_SESSION['user']['id'];

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM bookings
     WHERE status = 'Menunggu'
       AND (mekanik_id IS NULL OR mekanik_id = ?)"
);
$stmt->execute([$idMekanik]);
$menunggu = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare(
    "SELECT COUNT(*) FROM bookings
     WHERE mekanik_id = ?
       AND status IN ('Diterima','Menuju Lokasi','Dikerjakan')"
);
$stmt->execute([$idMekanik]);
$aktif = (int)$stmt->fetchColumn();

$judul = 'Dashboard Mekanik';
require __DIR__ . '/../header.php';
?>

<section class="section">
    <div class="section-heading">
        <h2>Dashboard Mekanik</h2>
        <p>Selamat bekerja, <?= e($_SESSION['user']['nama']) ?>!</p>
    </div>

    <div class="grid">
        <div class="card">
            <div class="card-icon">📥</div>
            <p>Pesanan menunggu</p>
            <h2><?= $menunggu ?></h2>
        </div>

        <div class="card">
            <div class="card-icon">🔧</div>
            <p>Pesanan aktif</p>
            <h2><?= $aktif ?></h2>
        </div>

        <a class="card" href="pesanan.php">
            <div class="card-icon">📋</div>
            <h3>Lihat Pesanan</h3>
            <p>Terima pesanan dan perbarui status pekerjaan.</p>
        </a>
    </div>
</section>

<?php require __DIR__ . '/../footer.php'; ?>
