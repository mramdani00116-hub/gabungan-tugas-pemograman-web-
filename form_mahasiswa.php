<?php
require_once __DIR__.'/auth.php';require_login();require_once __DIR__.'/layout.php';
$_student=$_SESSION['old_student']??[]; unset($_SESSION['old_student']);
page_top('Tambah Mahasiswa','tambah','STUDENT MANAGEMENT');
?>
<div class="page-actions animate-in"><p class="muted">Tambahkan mahasiswa baru melalui formulir berikut. Data akan disimpan ke database.</p><a href="data_mahasiswa.php" class="btn btn-ghost ripple-btn">← Kembali ke Tabel</a></div>
<div class="form-page-grid"><section class="panel form-panel reveal"><div class="panel-heading"><div><p class="eyebrow">NEW RECORD</p><h2>Form Data Mahasiswa</h2></div><span class="form-step">01 / 01</span></div><form action="proses_tambah.php" method="post" class="student-form" data-loading-form><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><?php include __DIR__.'/student_fields.php'; ?><div class="form-actions"><button type="reset" class="btn btn-secondary ripple-btn">Bersihkan</button><button type="submit" class="btn btn-primary ripple-btn"><?=nav_icon('plus')?> Simpan Data</button></div></form></section><aside class="tips-card reveal"><div class="tips-emoji">✳</div><h3>Tips Pengisian</h3><p>Pastikan informasi mahasiswa benar sebelum disimpan.</p><ul><li>Kolom bertanda <strong>*</strong> wajib diisi.</li><li>NIM harus unik, tidak boleh sama dengan mahasiswa lain.</li><li>Gunakan alamat email yang valid.</li><li>Setelah disimpan, data langsung muncul pada tabel.</li></ul><div class="tips-note">🔒 Data diproses melalui PHP dan prepared statement.</div></aside></div>
<?php page_bottom();?>
