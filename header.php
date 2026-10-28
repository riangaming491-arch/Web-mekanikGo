
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

$namaPengguna = $_SESSION['user']['nama'] ?? '';
$judul = $judul ?? 'Mekanik Go - Solusi Servis Kendaraan';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul) ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/mekanik-go/style.css?v=11">
</head>
<body>

<header class="navbar">
    <div class="nav-content">

        <a href="/mekanik-go/index.php" class="logo">
            <span class="logo-icon">⚙</span>
            <span>Mekanik<span class="logo-orange">Go</span></span>
        </a>

        <nav class="nav-links">
            <a href="/mekanik-go/index.php">Beranda</a>
            <a href="/mekanik-go/booking.php">Booking Servis</a>
            <a href="/mekanik-go/produk.php">Spare Part</a>
        </nav>

        <div class="nav-action">
            <?php if ($namaPengguna !== ''): ?>

                <span class="user-name">
                    Halo, <?= e($namaPengguna) ?>
                </span>

                <a href="/mekanik-go/status.php" class="btn btn-outline">
                    Booking Saya
                </a>

                <a href="/mekanik-go/pesanan.php" class="btn btn-outline">
                    Pesanan
                </a>

                <form action="/mekanik-go/logout.php" method="POST">
                    <input type="hidden" name="csrf"
                           value="<?= e(csrfToken()) ?>">
                    <button type="submit" class="btn btn-primary">
                        Keluar
                    </button>
                </form>

            <?php else: ?>

                <a href="/mekanik-go/login.php" class="btn btn-outline">
                    Masuk
                </a>

                <a href="/mekanik-go/register.php" class="btn btn-primary">
                    Daftar
                </a>

            <?php endif; ?>
        </div>

    </div>
</header>

<main class="page-content">
