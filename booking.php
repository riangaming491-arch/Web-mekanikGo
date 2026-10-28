
<?php
require_once 'koneksi.php';
require_once 'functions.php';
loginWajib();

if ($_SESSION['user']['role'] !== 'pengguna') {
    http_response_code(403);
    exit('Halaman ini hanya untuk pengguna.');
}

$layanan = $pdo->query(
    "SELECT id,nama,harga FROM services
     WHERE aktif = 1 ORDER BY nama"
)->fetchAll();

$pesan = '';
$kodeBaru = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfPeriksa();

    $kendaraan = trim($_POST['kendaraan'] ?? '');
    $jenis = $_POST['jenis_kendaraan'] ?? '';
    $serviceId = filter_input(INPUT_POST, 'service_id', FILTER_VALIDATE_INT);
    $keluhan = trim($_POST['keluhan'] ?? '');
    $lokasi = trim($_POST['lokasi'] ?? '');
    $jadwalInput = $_POST['jadwal'] ?? '';
    $jadwalTimestamp = strtotime($jadwalInput);

    $stmt = $pdo->prepare(
        "SELECT id FROM services WHERE id = ? AND aktif = 1"
    );
    $stmt->execute([$serviceId]);
    $layananValid = $stmt->fetch();

    if (
        $kendaraan === '' || $keluhan === '' || $lokasi === '' ||
        !$serviceId || !$layananValid ||
        !in_array($jenis, ['Motor', 'Mobil'], true) ||
        $jadwalTimestamp === false || $jadwalTimestamp <= time()
    ) {
        $pesan = 'Periksa kembali kendaraan, layanan, lokasi, dan jadwal.';
    } else {
        $kodeBaru = buatKode('MG-');
        $userId = (int) $_SESSION['user']['id'];

        $stmt = $pdo->prepare(
            "INSERT INTO bookings
            (kode,user_id,nama_pelanggan,telepon,kendaraan,
             jenis_kendaraan,service_id,keluhan,lokasi,jadwal)
            VALUES (?,?,?,?,?,?,?,?,?,?)"
        );

        $stmt->execute([
            $kodeBaru,
            $userId,
            $_SESSION['user']['nama'],
            $_SESSION['user']['telepon'],
            $kendaraan,
            $jenis,
            $serviceId,
            $keluhan,
            $lokasi,
            date('Y-m-d H:i:s', $jadwalTimestamp)
        ]);
    }
}

$judul = 'Booking Servis';
require 'header.php';
?>

<section class="form-wrap">
    <div class="form-card">
        <?php if ($kodeBaru): ?>
            <div class="alert alert-success">
                <h2>Booking berhasil dibuat!</h2>
                <p>Kode booking kamu:</p>
                <h2><?= e($kodeBaru) ?></h2>
                <p>Status awal: Menunggu</p>
            </div>
            <a class="btn" href="status.php">Pantau Status Booking</a>
        <?php else: ?>
            <h1>Booking Servis</h1>
            <p class="form-description">
                Isi data kendaraan, keluhan, dan lokasi servis.
            </p>

            <?php if ($pesan): ?>
                <div class="alert alert-error"><?= e($pesan) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

                <div class="form-grid">
                    <div class="field">
                        <label>Nama pelanggan</label>
                        <input value="<?= e($_SESSION['user']['nama']) ?>" disabled>
                    </div>

                    <div class="field">
                        <label>Nomor telepon</label>
                        <input value="<?= e($_SESSION['user']['telepon']) ?>" disabled>
                    </div>

                    <div class="field">
                        <label>Jenis kendaraan</label>
                        <select name="jenis_kendaraan" required>
                            <option value="">Pilih kendaraan</option>
                            <option value="Motor">Motor</option>
                            <option value="Mobil">Mobil</option>
                        </select>
                    </div>

                    <div class="field">
                        <label>Merek dan tipe kendaraan</label>
                        <input name="kendaraan" maxlength="120"
                            placeholder="Contoh: Honda Beat 2022" required>
                    </div>

                    <div class="field field-full">
                        <label>Pilih layanan</label>
                        <select name="service_id" required>
                            <option value="">Pilih layanan servis</option>
                            <?php foreach ($layanan as $item): ?>
                                <option value="<?= (int)$item['id'] ?>">
                                    <?= e($item['nama']) ?> —
                                    <?= rupiah($item['harga']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field field-full">
                        <label>Keluhan kendaraan</label>
                        <textarea name="keluhan" rows="3"
                            maxlength="2000" required></textarea>
                    </div>

                    <div class="field field-full">
                        <label>Alamat lengkap lokasi servis</label>
                        <textarea name="lokasi" rows="3"
                            maxlength="2000" required></textarea>
                    </div>

                    <div class="field field-full">
                        <label>Jadwal servis</label>
                        <input type="datetime-local" name="jadwal"
                            min="<?= e(date('Y-m-d\TH:i')) ?>" required>
                    </div>
                </div>

                <button class="btn btn-full" type="submit">
                    Konfirmasi Booking
                </button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php require 'footer.php'; ?>
