<?php
// Konfigurasi koneksi database untuk XAMPP lokal.
$host = "localhost";
$dbname = "mekanik_go";
$username = "root";
$password = ""; // Default XAMPP biasanya kosong. Sesuaikan jika MySQL kamu memakai password.

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit("Koneksi database gagal. Pastikan MySQL aktif dan database mekanik_go sudah diimpor melalui phpMyAdmin.");
}
