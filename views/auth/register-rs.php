<?php
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success'] ?? null;
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar RS - SIDORA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css?v=1780743553">
    <link rel="stylesheet" href="assets/css/auth.css?v=1780737744">
    </head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                
                <div style="text-align: center; font-size: 24px; font-weight: 600; color: #111827; margin-bottom: 8px; font-family: 'Poppins', sans-serif;">Daftar Rumah Sakit</div>
                <div style="text-align: center; font-size: 14px; color: #4B5563; margin-bottom: 24px; font-weight: 500; font-family: 'Poppins', sans-serif;">
                    Buat akun rumah sakit untuk mengakses permintaan darah.
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=register-rs-process" class="register-form">
                
                <div class="form-row" style="display: grid; gap: var(--spacing-md);">
                    <div class="form-group"><label>Nama Rumah Sakit</label><input type="text" name="nama_rs" placeholder="Contoh: RSUD Dr. H. Abdul Moeloek" required></div>
                    <div class="form-group"><label>Username</label><input type="text" name="username" placeholder="Username untuk login (opsional)"></div>
                </div>

                
                <div class="form-row" style="display: grid; gap: var(--spacing-md);">
                    <div class="form-group"><label>Email</label><input type="email" name="email" placeholder="Email resmi rumah sakit" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" placeholder="Password akun" required></div>
                </div>

                
                <div class="form-row" style="display: grid; gap: var(--spacing-md);">
                    <div class="form-group"><label>Telepon RS</label><input type="text" name="telepon" placeholder="Contoh: 0721-123456"></div>
                    <div class="form-group"><label>Nama Kontak / PIC</label><input type="text" name="kontak" placeholder="Nama penanggung jawab"></div>
                </div>

                
                <div class="form-group" style="margin-bottom: var(--spacing-md);">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" placeholder="Jalan, RT/RW, dsb."></textarea>
                </div>

                
                <div class="form-row" style="display: grid; gap: var(--spacing-md);">
                    <div class="form-group"><label>Kelurahan / Desa</label><input type="text" name="desa" placeholder="Kelurahan/Desa"></div>
                    <div class="form-group"><label>Kecamatan</label><input type="text" name="kecamatan" placeholder="Kecamatan"></div>
                </div>

                
                <div class="form-row" style="display: grid; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
                    <div class="form-group"><label>Provinsi</label><input type="text" name="provinsi" placeholder="Contoh: Lampung"></div>
                    <div class="form-group"><label>Kode Pos</label><input type="text" name="kode_pos" placeholder="Contoh: 35145"></div>
                </div>
                
                <div class="form-row" style="display: grid; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
                    <div class="form-group">
                        <label>Tipe Rumah Sakit</label>
                        <input type="text" name="tipe_rs" placeholder="Contoh: RS Umum Daerah" required>
                    </div>
                    <div class="form-group">
                        <label>No. Izin Operasional</label>
                        <input type="text" name="no_izin" placeholder="Contoh: 123/IZN/RSUD/2022" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary-sidora btn-login" style="width: 100%;"><i data-lucide="user-plus"></i> <span>Daftar</span></button>
            </form>
            <div class="auth-footer">
                <p>Sudah punya akun? <a href="index.php?page=login">Login</a></p>
            </div>
        </div>
        <div class="auth-side">
            <div class="side-content">
                <h2>Selamat Bergabung di<br>SIDORA</h2>
                <p>Daftarkan instansi Anda untuk kemudahan akses permintaan dan monitoring stok darah secara terintegrasi.</p>
                <div class="features">
                    <div class="feature-item">
                        <span class="feature-icon"><i data-lucide="check"></i></span>
                        <span>Akses prioritas permintaan darah</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon"><i data-lucide="check"></i></span>
                        <span>Monitoring stok darah real-time</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon"><i data-lucide="check"></i></span>
                        <span>Proses pengajuan yang efisien</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon"><i data-lucide="check"></i></span>
                        <span>Riwayat distribusi terorganisir</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/lucide/lucide.min.js"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
