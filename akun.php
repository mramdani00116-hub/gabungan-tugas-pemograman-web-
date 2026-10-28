<?php
require_once __DIR__.'/auth.php';require_login();require_once __DIR__.'/config.php';require_once __DIR__.'/layout.php';
$conn=db();
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $current=(string)($_POST['old_password']??'');$new=(string)($_POST['new_password']??'');$confirm=(string)($_POST['confirm_password']??'');
    $id=$_SESSION['user_id'];$stmt=$conn->prepare('SELECT password_hash FROM users WHERE id=?');$stmt->bind_param('i',$id);$stmt->execute();$user=$stmt->get_result()->fetch_assoc();$stmt->close();
    if(!$user || !password_verify($current,$user['password_hash'])) flash('error','Password saat ini tidak sesuai.');
    elseif(strlen($new)<10) flash('error','Password baru minimal 10 karakter.');
    elseif($new!==$confirm) flash('error','Konfirmasi password belum cocok.');
    else{
        $hash=password_hash($new,PASSWORD_DEFAULT);$stmt=$conn->prepare('UPDATE users SET password_hash=? WHERE id=?');$stmt->bind_param('si',$hash,$id);$stmt->execute();$stmt->close();
        flash('success','Password berhasil diperbarui. Gunakan password baru pada login berikutnya.');
    }
    redirect('akun.php');
}
$conn->close();page_top('Pengaturan Akun','akun','ACCOUNT SECURITY');
?>
<div class="page-actions animate-in"><p class="muted">Kelola keamanan akun yang kamu gunakan untuk login.</p></div>
<div class="account-grid"><section class="panel form-panel reveal"><div class="panel-heading"><div><p class="eyebrow">SECURITY SETTINGS</p><h2>Ubah Password</h2></div><span class="section-badge">Aman & Privat</span></div><form action="akun.php" method="post" class="stack-form settings-form" data-loading-form><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><label for="old_password">Password Saat Ini</label><div class="field-wrap"><span class="input-symbol">⌑</span><input type="password" name="old_password" id="old_password" autocomplete="current-password" placeholder="Masukkan password lama" required><button type="button" class="password-toggle" data-toggle-password="old_password">Lihat</button></div><label for="new_password">Password Baru</label><div class="field-wrap"><span class="input-symbol">⌑</span><input type="password" name="new_password" id="new_password" autocomplete="new-password" minlength="10" placeholder="Minimal 10 karakter" required><button type="button" class="password-toggle" data-toggle-password="new_password">Lihat</button></div><label for="confirm_password">Konfirmasi Password Baru</label><div class="field-wrap"><span class="input-symbol">⌑</span><input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password" minlength="10" placeholder="Ulangi password baru" required><button type="button" class="password-toggle" data-toggle-password="confirm_password">Lihat</button></div><div class="form-actions"><button type="submit" class="btn btn-primary ripple-btn"><?=nav_icon('shield')?> Simpan Password</button></div></form></section><aside class="tips-card reveal"><div class="tips-emoji">🔐</div><h3>Keamanan Akun</h3><p>Password disimpan sebagai hash, bukan teks asli.</p><ul><li>Gunakan minimal 10 karakter.</li><li>Jangan bagikan akun kepada orang lain.</li><li>Logout setelah menggunakan komputer umum.</li></ul><div class="tips-note">Akun saat ini: <strong><?=e((string)($_SESSION['username']??''))?></strong></div></aside></div>
<?php page_bottom();?>
