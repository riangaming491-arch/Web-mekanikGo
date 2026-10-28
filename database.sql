
CREATE DATABASE IF NOT EXISTS mekanik_go
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE mekanik_go;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    telepon VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    alamat TEXT,
    role ENUM('pengguna','admin','mekanik')
        NOT NULL DEFAULT 'pengguna',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(120) NOT NULL,
    kategori VARCHAR(60) NOT NULL,
    merek VARCHAR(80),
    deskripsi TEXT,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    gambar VARCHAR(255),
    aktif TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(30) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    mekanik_id INT NULL,
    nama_pelanggan VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    kendaraan VARCHAR(120) NOT NULL,
    jenis_kendaraan ENUM('Motor','Mobil') NOT NULL,
    service_id INT NOT NULL,
    keluhan TEXT NOT NULL,
    lokasi TEXT NOT NULL,
    jadwal DATETIME NOT NULL,
    status ENUM(
        'Menunggu','Diterima','Ditolak',
        'Menuju Lokasi','Dikerjakan','Selesai','Dibatalkan'
    ) NOT NULL DEFAULT 'Menunggu',
    catatan_admin TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (mekanik_id) REFERENCES users(id)
        ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services(id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(30) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    nama_penerima VARCHAR(100) NOT NULL,
    telepon VARCHAR(20) NOT NULL,
    alamat TEXT NOT NULL,
    total DECIMAL(12,2) NOT NULL DEFAULT 0,
    metode_pembayaran ENUM('COD','Transfer Manual')
        NOT NULL DEFAULT 'COD',
    status ENUM(
        'Menunggu','Diproses','Dikirim',
        'Selesai','Dibatalkan'
    ) NOT NULL DEFAULT 'Menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    nama_produk VARCHAR(120) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    jumlah INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO services (nama, deskripsi, harga) VALUES
('Servis Ringan', 'Pemeriksaan dan perawatan rutin kendaraan', 75000),
('Ganti Oli', 'Penggantian oli mesin kendaraan', 25000),
('Perbaikan Ban', 'Pemeriksaan dan penanganan ban bermasalah', 35000),
('Pemeriksaan Aki', 'Pemeriksaan kondisi dan kelistrikan aki', 30000),
('Servis Mesin', 'Pemeriksaan awal masalah mesin kendaraan', 100000);

INSERT INTO products
(nama, kategori, merek, deskripsi, harga, stok)
VALUES
('Oli Mesin', 'Oli', 'Contoh Merek', 'Oli untuk perawatan mesin', 65000, 20),
('Aki Motor', 'Aki', 'Contoh Merek', 'Aki untuk kendaraan motor', 180000, 10),
('Busi Motor', 'Busi', 'Contoh Merek', 'Busi pengapian kendaraan', 25000, 30),
('Ban Motor', 'Ban', 'Contoh Merek', 'Ban untuk penggunaan harian', 220000, 8),
('Kampas Rem', 'Rem', 'Contoh Merek', 'Komponen sistem pengereman', 45000, 15);
