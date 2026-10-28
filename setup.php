<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config.php';
$conn = db();
if ((int)$conn->query('SELECT COUNT(*) AS n FROM users')->fetch_assoc()['n'] > 0) redirect('login.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirm  = (string)($_POST['confirm'] ?? '');
    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) $error = 'Username harus 3-50 karakter, berupa huruf, angka, atau garis bawah.';
    elseif (strlen($password) < 10) $error = 'Password minimal 10 karakter.';
    elseif ($password !== $confirm) $error = 'Konfirmasi password belum cocok.';
    else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $conn->prepare('INSERT INTO users(username, password_hash) VALUES (?,?)');
            $stmt->bind_param('ss', $username,$hash);
            $stmt->execute(); $stmt->close();
            flash('success', 'Akun admin dibuat. Sekarang silakan login menggunakan akunmu.');
            redirect('login.php');
        } catch (mysqli_sql_exception $e) { $error = 'Username sudah digunakan atau penyimpanan akun gagal.'; }
    }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Aktivasi Akun • StudentSpace</title><link rel="stylesheet" href="assets/css/style.css?v=2"><script defer src="assets/js/app.js?v=2"></script></head>
<body class="auth-body"><main class="auth-wrap"><div class="auth-art"><div class="auth-art-orb orb-one"></div><div class="auth-art-orb orb-two"></div><div class="auth-brand">✦ <strong>StudentSpace.</strong></div><div class="auth-copy"><span class="auth-pill">SETUP AWAL · HANYA SEKALI</span><h1>Mulai dari sini.<br><em>Ini ruang belajarmu.</em></h1><p>Buat akun admin sendiri, lalu kelola biodata pribadi dan data mahasiswa dalam satu website PHP.</p></div><div class="auth-art-footer">WEB PROGRAMMING 1 <span>•</span> 2026</div></div>
<section class="auth-panel"><div class="auth-form-box animate-in"><span class="form-kicker">LANGKAH TERAKHIR</span><h2>Buat Akun Admin <span>✳</span></h2><p class="auth-lead">Database sudah siap. Isi username dan password untuk mengaktifkan website.</p>
<?php if($error):?><div class="alert alert-error"><span>!</span><p><?=e($error)?></p></div><?php endif;?>
<form action="setup.php" method="post" class="stack-form" data-loading-form><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><label for="username">Username Admin</label><div class="field-wrap"><span class="input-symbol">@</span><input id="username" name="username" type="text" autocomplete="username" placeholder="contoh: ramdani" value="<?=e((string)($_POST['username']??''))?>" required minlength="3" maxlength="50"></div>
<label for="password">Password Baru</label><div class="field-wrap"><span class="input-symbol">⌑</span><input id="password" name="password" type="password" autocomplete="new-password" placeholder="Minimal 10 karakter" required minlength="10"><button class="password-toggle" type="button" data-toggle-password="password" aria-label="Tampilkan password">Lihat</button></div>
<label for="confirm">Ulangi Password</label><div class="field-wrap"><span class="input-symbol">⌑</span><input id="confirm" name="confirm" type="password" autocomplete="new-password" placeholder="Ketik kembali password" required minlength="10"><button class="password-toggle" type="button" data-toggle-password="confirm" aria-label="Tampilkan password">Lihat</button></div>
<button class="btn btn-primary btn-wide ripple-btn" type="submit">Aktifkan Website <span>→</span></button></form><p class="auth-note">Setelah akun berhasil dibuat, hapus file <code>setup.php</code> dan <code>install_migrasi.php</code> sebelum mengunggah website ke hosting publik.</p></div></section></main></body></html>
