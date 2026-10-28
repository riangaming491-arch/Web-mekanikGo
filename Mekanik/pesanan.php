
<?php
require_once __DIR__ . '/../koneksi.php';
require_once __DIR__ . '/../functions.php';
roleWajib('mekanik');

$idMekanik = (int)$_SESSION['user']['id'];
$pesan = '';

$perubahan = [
    'terima' => 'Diterima',
    'tolak' => 'Ditolak',
    'jalan' => 'Menuju Lokasi',
    'kerjakan' => 'Dikerjakan',
    'selesai' => 'Selesai'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfPeriksa();

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $aksi = $_POST['aksi'] ?? '';

    if (!$id || !isset($perubahan[$aksi])) {
        $pesan = 'Aksi tidak valid.';
    } else {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "SELECT id,mekanik_id,status FROM bookings
                 WHERE id = ? FOR UPDATE"
            );
            $stmt->execute([$id]);
            $b = $stmt->fetch();

            if (!$b) {
                throw new RuntimeException('Booking tidak ditemukan.');
            }

            $statusBaru = $perubahan[$aksi];

            if ($aksi === 'terima') {
                if (
                    $b['status'] !== 'Menunggu' ||
                    ($b['mekanik_id'] !== null &&
                     (int)$b['mekanik_id'] !== $idMekanik)
                ) {
                    throw new RuntimeException(
                        'Booking ini sudah diambil atau tidak tersedia.'
                    );
                }

                $stmt = $pdo->prepare(
                    "UPDATE bookings SET mekanik_id=?, status='Diterima'
                     WHERE id=? AND status='Menunggu'
                     AND (mekanik_id IS NULL OR mekanik_id=?)"
                );
                $stmt->execute([$idMekanik, $id, $idMekanik]);
            } elseif ($aksi === 'tolak') {
                if (
                    $b['status'] !== 'Menunggu' ||
                    ($b['mekanik_id'] !== null &&
                     (int)$b['mekanik_id'] !== $idMekanik)
                ) {
                    throw new RuntimeException(
                        'Booking ini tidak dapat ditolak oleh akun Anda.'
                    );
                }

                $stmt = $pdo->prepare(
                    "UPDATE bookings SET mekanik_id=?, status='Ditolak'
                     WHERE id=? AND status='Menunggu'
                     AND (mekanik_id IS NULL OR mekanik_id=?)"
                );
                $stmt->execute([$idMekanik, $id, $idMekanik]);
            } else {
                $transisi = [
                    'jalan' => ['Diterima'],
                    'kerjakan' => ['Menuju Lokasi'],
                    'selesai' => ['Dikerjakan']
                ];

                if (
                    !in_array($b['status'], $transisi[$aksi], true) ||
                    (int)$b['mekanik_id'] !== $idMekanik
                ) {
                    throw new RuntimeException(
                        'Status booking belum sesuai untuk aksi ini.'
                    );
                }

                $stmt = $pdo->prepare(
                    "UPDATE bookings SET status=?
                     WHERE id=? AND mekanik_id=?"
                );
                $stmt->execute([$statusBaru, $id, $idMekanik]);
            }

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    'Status tidak berubah. Muat ulang halaman.'
                );
            }

            $pdo->commit();
            $pesan = 'Status booking berhasil diperbarui.';
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $pesan = $e instanceof RuntimeException
                ? $e->getMessage()
                : 'Terjadi kesalahan saat memperbarui booking.';
        }
    }
}

$stmt = $pdo->prepare(
    "SELECT b.*, s.nama AS nama_layanan
     FROM bookings b
     JOIN services s ON s.id = b.service_id
     WHERE b.status = 'Menunggu'
       OR b.mekanik_id = ?
     ORDER BY b.created_at DESC"
);
$stmt->execute([$idMekanik]);
$booking = $stmt->fetchAll();

$judul = 'Pesanan Mekanik';
require __DIR__ . '/../header.php';
?>

<section class="section">
    <div class="section-heading">
        <h2>Pesanan Servis</h2>
        <p>Terima pesanan dan perbarui progres pekerjaan.</p>
    </div>

    <?php if ($pesan): ?>
        <div class="alert alert-success"><?= e($pesan) ?></div>
    <?php endif; ?>

    <?php if (!$booking): ?>
        <div class="empty">Belum ada pesanan yang tersedia.</div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($booking as $b): ?>
                <article class="card">
                    <span class="badge"><?= e($b['status']) ?></span>
                    <h3><?= e($b['kode']) ?></h3>
                    <p><strong>Pelanggan:</strong>
                        <?= e($b['nama_pelanggan']) ?></p>
                    <p><strong>Telepon:</strong>
                        <?= e($b['telepon']) ?></p>
                    <p><strong>Kendaraan:</strong>
                        <?= e($b['jenis_kendaraan']) ?> —
                        <?= e($b['kendaraan']) ?></p>
                    <p><strong>Layanan:</strong>
                        <?= e($b['nama_layanan']) ?></p>
                    <p><strong>Keluhan:</strong>
                        <?= e($b['keluhan']) ?></p>
                    <p><strong>Lokasi:</strong>
                        <?= e($b['lokasi']) ?></p>
                    <p><strong>Jadwal:</strong>
                        <?= e(date('d-m-Y H:i', strtotime($b['jadwal']))) ?></p>

                    <div style="margin-top:15px">
                        <?php
                        $aksiTersedia = [];

                        if (
                            $b['status'] === 'Menunggu' &&
                            ($b['mekanik_id'] === null ||
                             (int)$b['mekanik_id'] === $idMekanik)
                        ) {
                            $aksiTersedia = [
                                'terima' => 'Terima',
                                'tolak' => 'Tolak'
                            ];
                        } elseif (
                            (int)$b['mekanik_id'] === $idMekanik
                        ) {
                            if ($b['status'] === 'Diterima') {
                                $aksiTersedia = ['jalan' => 'Mulai Perjalanan'];
                            } elseif ($b['status'] === 'Menuju Lokasi') {
                                $aksiTersedia = ['kerjakan' => 'Mulai Servis'];
                            } elseif ($b['status'] === 'Dikerjakan') {
                                $aksiTersedia = ['selesai' => 'Selesaikan Servis'];
                            }
                        }
                        ?>

                        <?php foreach ($aksiTersedia as $aksi => $label): ?>
                            <form method="POST" style="display:inline-block;margin:4px 0">
                                <input type="hidden" name="csrf"
                                    value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="id"
                                    value="<?= (int)$b['id'] ?>">
                                <input type="hidden" name="aksi"
                                    value="<?= e($aksi) ?>">
                                <button class="btn btn-small"
                                    type="submit"><?= e($label) ?></button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../footer.php'; ?>
