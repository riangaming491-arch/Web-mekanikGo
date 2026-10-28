
<?php
require_once 'koneksi.php';
require_once 'functions.php';
loginWajib();

// Mengambil nomor telepon pengguna yang sedang login
$telepon = $_SESSION['user']['noHP']
    ?? $_SESSION['user']['telepon']
    ?? '';

// Mengambil data booking berdasarkan nomor telepon
$booking = [];

if ($telepon !== '') {
    $stmt = $pdo->prepare(
        "SELECT
            id,
            nama,
            telepon,
            kendaraan,
            layanan,
            keluhan,
            lokasi,
            jadwal,
            status,
            created_at
         FROM bookings
         WHERE telepon = ?
         ORDER BY created_at DESC"
    );

    $stmt->execute([$telepon]);
    $booking = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$judul = 'Status Booking';
require 'header.php';
?>

<section class="section">
    <div class="section-heading">
        <h2>Status Booking Servis</h2>
        <p>Pantau jadwal dan perkembangan layanan kendaraanmu.</p>
    </div>

    <?php if (empty($booking)): ?>
        <div class="empty">
            <p>Belum ada booking servis.</p>
            <p>
                <a href="booking.php">Buat booking sekarang</a>
            </p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Kendaraan</th>
                        <th>Layanan</th>
                        <th>Jadwal</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($booking as $b): ?>
                        <tr>
                            <td>
                                <?= e('BK-' . $b['id']) ?>
                            </td>

                            <td>
                                <?= e($b['kendaraan']) ?>
                            </td>

                            <td>
                                <?= e($b['layanan']) ?>
                            </td>

                            <td>
                                <?= e(date(
                                    'd-m-Y H:i',
                                    strtotime($b['jadwal'])
                                )) ?>
                            </td>

                            <td>
                                <span class="badge">
                                    <?= e($b['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php require 'footer.php'; ?>
