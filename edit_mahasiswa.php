<?php
require_once __DIR__.'/auth.php';require_login();require_once __DIR__.'/config.php';require_once __DIR__.'/layout.php';
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if (!$id || $id < 1) {flash('error','ID mahasiswa tidak valid.');redirect('data_mahasiswa.php');}
$conn=db();$stmt=$conn->prepare('SELECT id,nim,nama,program_studi,email,nomor_telepon FROM mahasiswa WHERE id=?');$stmt->bind_param('i',$id);$stmt->execute();$_student=$stmt->get_result()->fetch_assoc();$stmt->close();$conn->close();
if (!$_student) {flash('error','Data mahasiswa tidak ditemukan.');redirect('data_mahasiswa.php');}
if (isset($_SESSION['old_student'])) {$_student=array_merge($_student,$_SESSION['old_student']);unset($_SESSION['old_student']);}
page_top('Edit Mahasiswa','data','STUDENT MANAGEMENT');
?>
<div class="page-actions animate-in"><p class="muted">Perbarui data mahasiswa lalu klik Simpan Perubahan.</p><a href="data_mahasiswa.php" class="btn btn-ghost ripple-btn">← Kembali ke Tabel</a></div>
<section class="panel form-panel solo-form reveal"><div class="panel-heading"><div><p class="eyebrow">UPDATE RECORD</p><h2>Edit Data Mahasiswa</h2></div><span class="section-badge">ID #<?=(int)$id?></span></div><form action="proses_edit.php" method="post" class="student-form" data-loading-form><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$id?>"><?php include __DIR__.'/student_fields.php';?><div class="form-actions"><a href="data_mahasiswa.php" class="btn btn-secondary ripple-btn">Batal</a><button class="btn btn-primary ripple-btn" type="submit"><?=nav_icon('check')?> Simpan Perubahan</button></div></form></section>
<?php page_bottom();?>
