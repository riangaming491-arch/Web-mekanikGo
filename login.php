
<?php
require_once 'koneksi.php';
require_once 'functions.php';

if (!empty($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];

    if ($role === 'admin') {
        header('Location: admin/index.php');
    } elseif ($role === 'mekanik') {
        header('Location: mekanik/index.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrfPeriksa();

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT id,nama,email,telepon,password,role
         FROM users WHERE email = ? LIMIT 1"
    );
    $stmt->execute([$email]);
    $akun = $stmt->fetch();

    if ($akun && password_verify($password, $akun['password'])) {
        session_regenerate_id(true);

        unset($akun['password']);
        $_SESSION['user'] = $akun;

        if ($akun['role'] === 'admin') {
            header('Location: admin/index.php');
        } elseif ($akun['role'] === 'mekanik') {
            header('Location: mekanik/index.php');
        } else {
            header('Location: index.php');
        }
        exit;
    }

    $pesan = 'Email atau password tidak sesuai.';
}

$judul = 'Login Mekanik Go';
require 'header.php';
?>

<section class="form-wrap">
    <div class="form-card">
        <h1>Selamat Datang!</h1>
        <p class="form-description">
            Masuk untuk mengelola booking dan pesananmu.
        </p>

        <?php if (isset($_GET['daftar'])): ?>
            <div class="alert alert-success">
                Registrasi berhasil. Silakan login.
            </div>
        <?php endif; ?>

        <?php if ($pesan): ?>
            <div class="alert alert-error"><?= e($pesan) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" required
                    autocomplete="username">
            </div>

            <div class="field" style="margin-top:16px">
                <label>Password</label>
                <input type="password" name="password" required
                    autocomplete="current-password">
            </div>

            <button class="btn btn-full" type="submit">Masuk</button>
        </form>

        <p class="form-description">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>.
        </p>
    </div>
</section>

<?php require 'footer.php'; ?>
