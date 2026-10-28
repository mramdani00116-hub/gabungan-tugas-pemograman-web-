<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('HORIZON_PORTAL');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']) && is_int($_SESSION['user_id']);
}

function require_login(): void
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    if (!is_logged_in()) {
        flash('warning', 'Silakan login terlebih dahulu untuk mengakses halaman.');
        redirect('login.php');
    }
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' ||
        !is_string($_POST['csrf_token'] ?? null) ||
        !hash_equals(csrf_token(), $_POST['csrf_token'])) {
        http_response_code(403);
        exit('Permintaan ditolak: token keamanan tidak valid. Kembali ke halaman sebelumnya lalu coba lagi.');
    }
}

function flash(string $kind, string $message): void
{
    $_SESSION['flash'] = ['kind' => $kind, 'message' => $message];
}

function pull_flash(): ?array
{
    $value = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($value) ? $value : null;
}

function valid_student(array $src): array
{
    $nim = trim((string)($src['nim'] ?? ''));
    $nama = trim((string)($src['nama'] ?? ''));
    $prodi = trim((string)($src['program_studi'] ?? ''));
    $email = trim((string)($src['email'] ?? ''));
    $telp = trim((string)($src['nomor_telepon'] ?? ''));

    if ($nim === '' || $nama === '' || $prodi === '' || $email === '') {
        throw new InvalidArgumentException('NIM, nama, program studi, dan email wajib diisi.');
    }
    $length = static fn(string $v): int => function_exists('mb_strlen') ? mb_strlen($v, 'UTF-8') : strlen($v);
    if ($length($nim) > 20 || $length($nama) > 100 || $length($prodi) > 100 || $length($email) > 100 || $length($telp) > 25) {
        throw new InvalidArgumentException('Ada data yang melebihi batas jumlah karakter.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Format alamat email tidak valid.');
    }
    if ($telp !== '' && !preg_match('/^[0-9+() .-]{6,25}$/', $telp)) {
        throw new InvalidArgumentException('Nomor telepon hanya boleh berisi angka dan simbol telepon umum.');
    }
    return [$nim, $nama, $prodi, $email, $telp];
}
