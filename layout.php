<?php
require_once __DIR__ . '/auth.php';

function nav_icon(string $name): string
{
    $svg = [
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>',
        'lock' => '<rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'arrow' => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
        'edit' => '<path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L9 17l-4 1 1-4Z"/>',
        'trash' => '<path d="M3 6h18M8 6V4h8v2M5 6l1 15h12l1-15M10 10v7m4-7v7"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
        'book' => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14Z"/><path d="M4 20a2 2 0 0 0 2 2h14"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close' => '<path d="M18 6 6 18M6 6l12 12"/>',
        'check' => '<path d="m5 12 4 4L19 6"/>',
    ];
    return '<svg class="icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . ($svg[$name] ?? $svg['grid']) . '</svg>';
}

function page_top(string $title, string $active, string $eyebrow = 'PORTAL MAHASISWA'): void
{
    $username = e((string)($_SESSION['username'] ?? 'Mahasiswa'));
    $flash = pull_flash();
    $nav = [
        ['index.php', 'Dashboard', 'grid', 'dashboard'],
        ['biodata.php', 'Biodata Saya', 'user', 'biodata'],
        ['data_mahasiswa.php', 'Data Mahasiswa', 'users', 'data'],
        ['form_mahasiswa.php', 'Tambah Mahasiswa', 'plus', 'tambah'],
        ['akun.php', 'Pengaturan Akun', 'lock', 'akun'],
    ];
    ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1530">
    <title><?= e($title) ?> • StudentSpace</title>
    <link rel="stylesheet" href="assets/css/style.css?v=2">
    <script defer src="assets/js/app.js?v=2"></script>
</head>
<body class="app-body">
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="index.php" aria-label="StudentSpace, kembali ke dashboard">
            <span class="brand-symbol"><?= nav_icon('book') ?></span>
            <span><strong>Student<span>Space.</span></strong><small>Web Programming 1</small></span>
        </a>
        <div class="side-heading">MENU UTAMA</div>
        <nav class="side-nav" aria-label="Navigasi utama">
          <?php foreach ($nav as [$href,$label,$icon,$key]): ?>
            <a href="<?= e($href) ?>" class="side-link <?= $active === $key ? 'active' : '' ?>" <?= $active === $key ? 'aria-current="page"' : '' ?>>
                <?= nav_icon($icon) ?><span><?= e($label) ?></span>
                <?php if ($active === $key): ?><span class="active-dot"></span><?php endif; ?>
            </a>
          <?php endforeach; ?>
        </nav>
        <div class="sidebar-bottom">
            <div class="side-help"><span class="help-icon"><?= nav_icon('shield') ?></span><strong>Data tersimpan aman</strong><small>PHP · MySQL · Session Login</small></div>
            <form action="logout.php" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button type="submit" class="side-link logout-link ripple-btn"><?= nav_icon('logout') ?><span>Keluar Akun</span></button></form>
        </div>
    </aside>
    <div class="sidebar-overlay" data-close-menu></div>
    <div class="main-shell">
        <header class="topbar">
            <div class="topbar-start"><button class="icon-btn menu-toggle" type="button" aria-label="Buka menu" data-menu-toggle><?= nav_icon('menu') ?></button><span class="breadcrumb">Beranda <span class="crumb-sep">/</span> <strong><?= e($title) ?></strong></span></div>
            <div class="topbar-end"><span class="online-indicator"><span></span> Sistem Aktif</span><span class="top-avatar" aria-hidden="true"><?= e(strtoupper(substr((string)($_SESSION['username'] ?? 'M'),0,1))) ?></span><span class="top-username"><?= $username ?></span></div>
        </header>
        <main class="page-content" id="content">
            <div class="page-title animate-in"><div><p class="eyebrow"><?= e($eyebrow) ?></p><h1><?= e($title) ?></h1></div><span class="title-watermark" aria-hidden="true">✦</span></div>
            <?php if ($flash): ?><div role="status" class="alert alert-<?= in_array($flash['kind'],['success','error','warning'],true) ? $flash['kind'] : 'success' ?> animate-in" data-dismissible><span><?= $flash['kind'] === 'success' ? '✓' : '!' ?></span><p><?= e($flash['message']) ?></p><button type="button" aria-label="Tutup pesan" data-dismiss>×</button></div><?php endif; ?>
    <?php
}

function page_bottom(): void
{
    ?>
        </main>
        <footer class="app-footer"><span>© <?= date('Y') ?> StudentSpace • Muhammad Ramdani Saputra</span><span>Built for Web Programming 1 <span class="footer-heart">♥</span></span></footer>
    </div>
</div>
</body>
</html>
    <?php
}
