
<?php
require_once 'koneksi.php';
require_once 'functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfPeriksa();

    $nama = trim($_POST['nama'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $telepon = trim($_POST['telepon'] ?? '');
    $password = $_POST['password'] ?? '';
    $alamat = trim($_POST['alamat'] ?? '');

    if (
        $nama === '' ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        !preg_match('/^[0-9+ -]{8,20}$/', $telepon) ||
        strlen($password) < 8
    ) {
        $pesan = 'Periksa kembali data. Password minimal 8 karakter.';
    } else {
        try {
            // Pastikan tabel dan nama kolom sesuai database kamu.
            $stmt = $pdo->prepare(
                "INSERT INTO users
                (nama, email, telepon, password, alamat, role)
                VALUES (?, ?, ?, ?, ?, 'pengguna')"
            );

            $stmt->execute([
                $nama,
                $email,
                $telepon,
                password_hash($password, PASSWORD_DEFAULT),
                $alamat
            ]);

            header('Location: login.php?daftar=berhasil');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $pesan = 'Email atau data tersebut sudah terdaftar.';
            } else {
                error_log($e->getMessage());
                $pesan = 'Registrasi gagal. Periksa tabel dan kolom database.';
            }
        }
    }
}

$judul = 'Daftar Akun';
require 'header.php';
?>

<section class="form-wrap">
    <div class="form-card">
        <h1>Buat Akun Mekanik Go</h1>

        <p class="form-description">
            Daftar untuk mulai memesan layanan servis.
        </p>

        <?php if ($pesan !== ''): ?>
            <div class="alert alert-error">
                <?= e($pesan) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <input type="hidden" name="csrf"
                   value="<?= e(csrfToken()) ?>">

            <div class="field">
                <label for="nama">Nama lengkap</label>
                <input id="nama" name="nama" maxlength="100"
                       required value="<?= e($_POST['nama'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email"
                       maxlength="120" required
                       value="<?= e($_POST['email'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="telepon">Nomor telepon</label>
                <input id="telepon" type="tel" name="telepon"
                       maxlength="20" required
                       value="<?= e($_POST['telepon'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" rows="2"><?= e($_POST['alamat'] ?? '') ?></textarea>
            </div>

            <div class="field">
                <label for="password">Password (minimal 8 karakter)</label>
                <input id="password" type="password" name="password"
                       minlength="8" autocomplete="new-password"
                       required>
            </div>

            <button class="btn btn-full" type="submit">
                Daftar Sekarang
            </button>
        </form>

        <p class="form-description">
            Sudah punya akun?
            <a href="login.php">Masuk di sini</a>.
        </p>
    </div>
</section>

<?php require 'footer.php'; ?>
