
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function rupiah($angka)
{
    return 'Rp ' . number_format(
        (float) $angka,
        0,
        ',',
        '.'
    );
}

function loginWajib()
{
    if (empty($_SESSION['user'])) {
        header('Location: /mekanik-go/login.php');
        exit;
    }
}

function roleWajib($role)
{
    loginWajib();

    if (($_SESSION['user']['role'] ?? '') !== $role) {
        http_response_code(403);
        exit('Akses ditolak.');
    }
}

function buatKode($prefix)
{
    return $prefix . date('ymd') .
        strtoupper(bin2hex(random_bytes(4)));
}

function csrfToken()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrfPeriksa()
{
    $token = $_POST['csrf'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf'] ?? '', $token)
    ) {
        http_response_code(419);
        exit('Sesi formulir tidak valid. Muat ulang halaman.');
    }
}
