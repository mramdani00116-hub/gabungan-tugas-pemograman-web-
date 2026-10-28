<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config.php';
if (is_logged_in()) redirect('index.php');
$conn = db();
if ((int)$conn->query('SELECT COUNT(*) AS n FROM users')->fetch_assoc()['n'] === 0) redirect('install_migrasi.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $bucket = $_SESSION['login_attempts'] ?? ['count'=>0,'started'=>time()];
    if (time() - $bucket['started'] >= 600) $bucket = ['count'=>0,'started'=>time()];
    if ($bucket['count'] >= 8) $error = 'Terlalu banyak percobaan login. Coba kembali dalam 10 menit.';
    elseif ($username === '' || $password === '') $error = 'Username dan password wajib diisi.';
    else {
        $stmt = $conn->prepare('SELECT id,username,password_hash FROM users WHERE username=? LIMIT 1');
        $stmt->bind_param('s',$username); $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc(); $stmt->close();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['username'];
            unset($_SESSION['login_attempts']);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            flash('success','Selamat datang kembali! Kamu berhasil masuk ke dashboard.');
            redirect('index.php');
        }
        $bucket['count']++;
        $_SESSION['login_attempts'] = $bucket;
        $error = 'Username atau password tidak sesuai.';
    }
}
$flash = pull_flash();
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#0b1530"><title>Login • StudentSpace</title><link rel="stylesheet" href="assets/css/style.css?v=2"><script defer src="assets/js/app.js?v=2"></script></head>
<body class="auth-body"><main class="auth-wrap"><div class="auth-art"><div class="auth-art-orb orb-one"></div><div class="auth-art-orb orb-two"></div><div class="auth-brand">✦ <strong>StudentSpace.</strong></div><div class="auth-copy"><span class="auth-pill">WELCOME TO YOUR SPACE</span><h1>Satu tempat.<br><em>Semua ceritamu.</em></h1><p>Kenalan lebih dekat, kelola data mahasiswa, dan eksplor perjalanan kuliah dengan cara yang lebih menarik.</p><div class="auth-highlights"><span>✳ Biodata Pribadi</span><span>✳ Data Mahasiswa</span><span>✳ Dashboard Interaktif</span></div></div><div class="auth-art-footer">WEB PROGRAMMING 1 <span>•</span> 2026</div></div>
<section class="auth-panel"><div class="auth-form-box animate-in"><span class="form-kicker">SELAMAT DATANG KEMBALI</span><h2>Halo, Welcome Back! <span>👋</span></h2><p class="auth-lead">Silakan masuk untuk melanjutkan ke dashboard pribadimu.</p>
<?php if($error):?><div class="alert alert-error"><span>!</span><p><?=e($error)?></p></div><?php endif;?>
<?php if($flash):?><div class="alert alert-success"><span>✓</span><p><?=e($flash['message'])?></p></div><?php endif;?>
<?php if(isset($_GET['keluar'])):?><div class="alert alert-success"><span>✓</span><p>Kamu sudah berhasil logout. Sampai jumpa!</p></div><?php endif;?>
<form action="login.php" method="post" class="stack-form" data-loading-form><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><label for="username">Username</label><div class="field-wrap"><span class="input-symbol">@</span><input id="username" type="text" name="username" placeholder="Masukkan username" autocomplete="username" value="<?=e((string)($_POST['username']??''))?>" required autofocus></div><label for="password">Password</label><div class="field-wrap"><span class="input-symbol">⌑</span><input id="password" type="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required><button class="password-toggle" type="button" data-toggle-password="password" aria-label="Tampilkan password">Lihat</button></div><div class="auth-form-hint">🔒 Login dilindungi dengan sesi PHP</div><button type="submit" class="btn btn-primary btn-wide ripple-btn"><span>Masuk ke Dashboard</span><span>→</span></button></form><p class="auth-note">Website tugas <strong>Web Programming 1</strong> · Universitas Horizon Indonesia</p></div></section></main></body></html>
