<?php
/* ============================================================
 *  config/layout.php  –  Helper fungsi render layout
 *  Dipanggil dari halaman CRUD setelah cek_login
 * ============================================================ */

/**
 * Cetak tag <head> + awal <body> + sidebar.
 * $halaman_aktif = key nav-item agar bisa di-highlight.
 */
function layout_open(string $judul_halaman, string $halaman_aktif = ''): void
{
    $lang   = $_SESSION['bahasa'];
    $nama   = htmlspecialchars($_SESSION['nama_user'] ?? 'User', ENT_QUOTES, 'UTF-8');
    $inisial = strtoupper(mb_substr($_SESSION['nama_user'] ?? 'U', 0, 1));
    $base   = '/klub';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= t('judul_app') ?> — Sistem Informasi Manajemen Direktori Anggota Klub">
    <title><?= $judul_halaman ?> — <?= t('judul_app') ?></title>
    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>
<div class="wrapper">
    <!-- ====== SIDEBAR ====== -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="/klub/assets/img/logo.jpg" alt="Scorpion BC Logo" class="site-logo">
            <h1>Scorpion BC</h1>
            <p>Management System v1.0</p>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label"><?= t('navigasi') ?></div>
            <a href="<?= $base ?>/index.php"
               class="nav-item <?= $halaman_aktif === 'dashboard' ? 'active' : '' ?>">
                <span class="icon">🏠</span><?= t('dashboard') ?>
            </a>
            <a href="<?= $base ?>/anggota/index.php"
               class="nav-item <?= $halaman_aktif === 'anggota' ? 'active' : '' ?>">
                <span class="icon">👥</span><?= t('daftar_anggota') ?>
            </a>
            <a href="<?= $base ?>/anggota/tambah.php"
               class="nav-item <?= $halaman_aktif === 'tambah' ? 'active' : '' ?>">
                <span class="icon">➕</span><?= t('tambah_anggota') ?>
            </a>

            <div class="nav-label"><?= t('pengaturan') ?></div>
            <a href="<?= $base ?>/config/ganti_bahasa.php?lang=id"
               class="nav-item <?= $lang === 'id' ? 'active' : '' ?>">
                <span class="icon">🇮🇩</span> Indonesia
            </a>
            <a href="<?= $base ?>/config/ganti_bahasa.php?lang=en"
               class="nav-item <?= $lang === 'en' ? 'active' : '' ?>">
                <span class="icon">🇺🇸</span> English
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar"><?= $inisial ?></div>
                <div>
                    <div class="user-name"><?= $nama ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <a href="<?= $base ?>/auth/logout.php" class="nav-item" style="margin-top:.5rem;color:#fca5a5">
                <span class="icon">🚪</span><?= t('logout') ?>
            </a>
        </div>
    </aside>

    <!-- ====== MAIN ====== -->
    <main class="main-content">
<?php
}

/** Tutup main, wrapper, body, html */
function layout_close(): void
{
?>
    </main><!-- .main-content -->
</div><!-- .wrapper -->
</body>
</html>
<?php
}

/** Topbar standar */
function layout_topbar(string $judul, string $subjudul = ''): void
{
    $lang = $_SESSION['bahasa'];
    $id_active = $lang === 'id' ? 'active-lang' : '';
    $en_active = $lang === 'en' ? 'active-lang' : '';
?>
    <div class="topbar">
        <div class="page-title">
            <h2><?= $judul ?></h2>
            <?php if ($subjudul): ?><p><?= $subjudul ?></p><?php endif; ?>
        </div>
        <div class="topbar-actions">
            <div class="lang-switch">
                <a href="/klub/config/ganti_bahasa.php?lang=id" class="<?= $id_active ?>">🇮🇩 ID</a>
                <a href="/klub/config/ganti_bahasa.php?lang=en" class="<?= $en_active ?>">🇺🇸 EN</a>
            </div>
        </div>
    </div>
<?php
}
