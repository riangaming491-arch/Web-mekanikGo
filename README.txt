MEKANIK GO — STARTER PJBL (PHP + MYSQL)

ISI FOLDER
- index.php      : beranda, daftar layanan, dan formulir booking
- style.css      : desain responsif
- config.php     : koneksi database PDO
- booking.php    : memproses dan menyimpan booking
- bookings.php   : daftar booking untuk demonstrasi lokal
- database.sql   : membuat database dan tabel bookings

CARA MENJALANKAN DI XAMPP
1. Ekstrak folder mekanik-go ke C:\xampp\htdocs\
2. Buka XAMPP Control Panel.
3. Klik Start pada Apache dan MySQL.
4. Buka http://localhost/phpmyadmin
5. Pilih tab Import, pilih file database.sql, lalu klik Import/Go.
6. Buka http://localhost/mekanik-go/
7. Isi formulir booking dan klik Kirim Permintaan Booking.
8. Untuk memeriksa data tersimpan, buka http://localhost/mekanik-go/bookings.php

JIKA KONEKSI DATABASE GAGAL
- Pastikan MySQL sudah aktif.
- Pastikan database mekanik_go sudah dibuat dari database.sql.
- Jika MySQL kamu menggunakan password, ubah $password di config.php.

BATASAN VERSI DEMO
- Belum ada login pengguna/admin/mekanik.
- Belum ada pembayaran online, pelacakan lokasi langsung, atau notifikasi.
- Halaman bookings.php tidak memakai autentikasi; gunakan hanya di localhost untuk demonstrasi.
- Harga layanan adalah ilustrasi, bukan harga resmi.
- Jangan mengklaim fitur yang belum dibuat sebagai fitur yang sudah berfungsi.
