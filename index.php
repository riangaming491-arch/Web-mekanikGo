
<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/functions.php';

// Ambil data layanan aktif.
$stmtLayanan = $pdo->query("
    SELECT id, nama, deskripsi, harga
    FROM services
    WHERE aktif = 1
    ORDER BY id DESC
");
$layanan = $stmtLayanan->fetchAll(PDO::FETCH_ASSOC);

// Ambil data produk aktif.
$stmtProduk = $pdo->query("
    SELECT id, nama, kategori, harga, stok, image
    FROM products
    WHERE aktif = 1
    ORDER BY id DESC
");
$produk = $stmtProduk->fetchAll(PDO::FETCH_ASSOC);

$judul = 'Beranda Mekanik Go';

require __DIR__ . '/header.php';
?>

<!-- HERO SECTION -->
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-text">
            <span class="hero-label">
                <span class="status-dot"></span>
                Layanan Mekanik Panggilan
            </span>

            <h1>
                Kendaraan Bermasalah?
                <span>Kami Siap Membantu!</span>
            </h1>

            <p class="hero-description">
                Tidak perlu repot datang ke bengkel.
                Pesan mekanik melalui Mekanik Go dan dapatkan
                layanan perbaikan kendaraan di lokasi Anda.
            </p>

            <div class="hero-buttons">
                <a href="/mekanik-go/booking.php" class="btn btn-primary btn-large">
                    Booking Servis <span>→</span>
                </a>

                <a href="/mekanik-go/produk.php" class="btn btn-secondary btn-large">
                    Lihat Spare Part
                </a>
            </div>

            <div class="hero-benefits">
                <div class="benefit">
                    <span class="benefit-icon">✓</span>
                    <span>Mudah dipesan</span>
                </div>

                <div class="benefit">
                    <span class="benefit-icon">✓</span>
                    <span>Mekanik panggilan</span>
                </div>

                <div class="benefit">
                    <span class="benefit-icon">✓</span>
                    <span>Informasi harga</span>
                </div>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-glow"></div>

            <div class="hero-image-card">
                <div class="hero-top-badge">
                    <span class="badge-icon">⚙</span>
                    <div>
                        <strong>Mekanik Go</strong>
                        <small>Solusi servis kendaraan</small>
                    </div>
                </div>

                <div class="hero-illustration">
                    <div class="illustration-circle">
                        <span class="motorcycle">🏍️</span>
                    </div>
                    <span class="tool tool-one">🔧</span>
                    <span class="tool tool-two">🛠️</span>
                </div>

                <div class="hero-bottom-card">
                    <div class="hero-bottom-icon">📍</div>
                    <div>
                        <strong>Servis di lokasi Anda</strong>
                        <p>Praktis, mudah, dan nyaman</p>
                    </div>
                    <span class="arrow-circle">→</span>
                </div>
            </div>

            <div class="floating-card floating-card-top">
                <span class="floating-icon orange">⚡</span>
                <div>
                    <strong>Praktis</strong>
                    <small>Pesan dari rumah</small>
                </div>
            </div>

            <div class="floating-card floating-card-bottom">
                <span class="floating-icon green">✓</span>
                <div>
                    <strong>Layanan mudah</strong>
                    <small>Proses lebih sederhana</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- KEUNGGULAN -->
<section class="feature-strip">
    <div class="container feature-grid">
        <div class="feature-item">
            <div class="feature-icon blue">🔧</div>
            <div>
                <h3>Servis Panggilan</h3>
                <p>Mekanik datang ke lokasi</p>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-icon orange">📅</div>
            <div>
                <h3>Atur Jadwal</h3>
                <p>Pilih waktu servis Anda</p>
            </div>
        </div>

        <div class="feature-item">
            <div class="feature-icon green">🛒</div>
            <div>
                <h3>Spare Part</h3>
                <p>Temukan produk kendaraan</p>
            </div>
        </div>
    </div>
</section>

<!-- LAYANAN SERVIS -->
<section class="section" id="layanan">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-label">LAYANAN KAMI</span>
                <h2>Butuh servis apa hari ini?</h2>
                <p>Pilih layanan yang sesuai dengan kebutuhan kendaraan Anda.</p>
            </div>

            <a href="/mekanik-go/booking.php" class="text-link">
                Booking servis <span>→</span>
            </a>
        </div>

        <?php if (!empty($layanan)): ?>
            <div class="card-grid">
                <?php foreach ($layanan as $item): ?>
                    <article class="service-card">
                        <div class="service-card-top">
                            <div class="service-icon">🔧</div>
                            <span class="service-tag">Layanan</span>
                        </div>

                        <h3><?= e($item['nama']) ?></h3>

                        <p class="card-description">
                            <?= e($item['deskripsi'] ?: 'Layanan perawatan dan perbaikan kendaraan Anda.') ?>
                        </p>

                        <div class="card-footer">
                            <div>
                                <span class="price-label">Mulai dari</span>
                                <strong class="price">
                                    <?= rupiah($item['harga']) ?>
                                </strong>
                            </div>

                            <a
                                class="card-arrow"
                                href="/mekanik-go/booking.php"
                                aria-label="Booking <?= e($item['nama']) ?>"
                            >→</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">🔧</div>
                <h3>Belum ada layanan tersedia</h3>
                <p>Layanan servis akan ditampilkan di sini setelah ditambahkan oleh admin.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- BANNER BOOKING -->
<section class="booking-banner-section">
    <div class="container">
        <div class="booking-banner">
            <div class="banner-content">
                <span class="banner-label">BUTUH BANTUAN SEKARANG?</span>
                <h2>Jangan biarkan kendaraan bermasalah menghambat aktivitasmu.</h2>
                <p>
                    Buat booking servis dan sampaikan kebutuhan perbaikan
                    kendaraan melalui Mekanik Go.
                </p>

                <a href="/mekanik-go/booking.php" class="btn btn-white btn-large">
                    Booking Sekarang <span>→</span>
                </a>
            </div>

            <div class="banner-art" aria-hidden="true">
                <div class="banner-circle"></div>
                <span class="banner-wrench">🔧</span>
                <span class="banner-motor">🏍️</span>
            </div>
        </div>
    </div>
</section>

<!-- PRODUK SPARE PART -->
<section class="section products-section" id="produk">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-label">TOKO MEKANIK GO</span>
                <h2>Spare part pilihan</h2>
                <p>Temukan kebutuhan perawatan kendaraan Anda.</p>
            </div>

            <a href="/mekanik-go/produk.php" class="text-link">
                Lihat semua produk <span>→</span>
            </a>
        </div>

        <?php if (!empty($produk)): ?>
            <div class="card-grid product-grid">
                <?php foreach ($produk as $item): ?>
                    <article class="product-card">
                        <div class="product-image">
                            <?php if (!empty($item['image'])): ?>
                                <img
                                    src="<?= e($item['image']) ?>"
                                    alt="<?= e($item['nama']) ?>"
                                    loading="lazy"
                                >
                            <?php else: ?>
                                <div class="product-placeholder">
                                    <span>⚙</span>
                                    <small>Spare Part</small>
                                </div>
                            <?php endif; ?>

                            <?php if ((int) $item['stok'] > 0): ?>
                                <span class="stock-badge available">Tersedia</span>
                            <?php else: ?>
                                <span class="stock-badge unavailable">Stok Habis</span>
                            <?php endif; ?>
                        </div>

                        <div class="product-info">
                            <span class="product-category">
                                <?= e($item['kategori'] ?: 'Spare Part') ?>
                            </span>

                            <h3><?= e($item['nama']) ?></h3>

                            <div class="product-bottom">
                                <strong class="price">
                                    <?= rupiah($item['harga']) ?>
                                </strong>

                                <span class="stock-text">
                                    Stok: <?= (int) $item['stok'] ?>
                                </span>
                            </div>

                            <a href="/mekanik-go/produk.php" class="btn btn-product">
                                Lihat Produk <span>→</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">🛒</div>
                <h3>Belum ada produk tersedia</h3>
                <p>Produk spare part akan muncul di bagian ini setelah ditambahkan oleh admin.</p>
            </div>
        <?php endif; ?>

        <div class="home-more">
            <a href="/mekanik-go/produk.php" class="btn btn-secondary">
                Jelajahi Semua Produk <span>→</span>
            </a>
        </div>
    </div>
</section>

<!-- CARA KERJA -->
<section class="section how-section">
    <div class="container">
        <div class="section-heading centered-heading">
            <div>
                <span class="section-label">CARA KERJA</span>
                <h2>Servis kendaraan jadi lebih mudah</h2>
                <p>Ikuti langkah sederhana untuk memulai layanan.</p>
            </div>
        </div>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">01</div>
                <div class="step-icon">📝</div>
                <h3>Pilih Layanan</h3>
                <p>Tentukan jenis servis yang dibutuhkan kendaraan Anda.</p>
            </div>

            <div class="step-connector">→</div>

            <div class="step-card">
                <div class="step-number">02</div>
                <div class="step-icon">📍</div>
                <h3>Isi Detail Booking</h3>
                <p>Masukkan lokasi, waktu, dan informasi kendaraan.</p>
            </div>

            <div class="step-connector">→</div>

            <div class="step-card">
                <div class="step-number">03</div>
                <div class="step-icon">🛠️</div>
                <h3>Pantau Pesanan</h3>
                <p>Pantau status booking melalui website Mekanik Go.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA AKHIR -->
<section class="final-cta">
    <div class="container final-cta-inner">
        <div>
            <span class="section-label">MEKANIK GO</span>
            <h2>Siap merawat kendaraanmu?</h2>
            <p>Mulai booking servis dengan lebih praktis.</p>
        </div>

        <a href="/mekanik-go/booking.php" class="btn btn-primary btn-large">
            Mulai Booking <span>→</span>
        </a>
    </div>
</section>

<?php require __DIR__ . '/footer.php'; ?>
