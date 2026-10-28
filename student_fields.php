<?php
// Komponen form bersama: $_student berisi array data mahasiswa.
if (!function_exists('e') || !is_logged_in()) { http_response_code(403); exit('Akses ditolak'); }
$_student = $_student ?? [];
$_programs = ['Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Manajemen Informatika'];
$_selected = (string)($_student['program_studi'] ?? '');
if ($_selected !== '' && !in_array($_selected,$_programs,true)) $_programs[]=$_selected;
?>
<div class="form-grid">
 <div class="form-group"><label for="nim">Nomor Induk Mahasiswa <span>*</span></label><input name="nim" id="nim" type="text" placeholder="Contoh: 4112755201250084" value="<?=e((string)($_student['nim']??''))?>" maxlength="20" autocomplete="off" required><small>Gunakan NIM unik, maksimal 20 karakter.</small></div>
 <div class="form-group"><label for="nama">Nama Lengkap <span>*</span></label><input name="nama" id="nama" type="text" placeholder="Masukkan nama lengkap" value="<?=e((string)($_student['nama']??''))?>" maxlength="100" required></div>
 <div class="form-group"><label for="program_studi">Program Studi <span>*</span></label><select name="program_studi" id="program_studi" required><option value="" disabled <?= $_selected===''?'selected':'' ?>>Pilih program studi</option><?php foreach($_programs as $_program): ?><option value="<?=e($_program)?>" <?=$_selected===$_program?'selected':''?>><?=e($_program)?></option><?php endforeach;?></select></div>
 <div class="form-group"><label for="email">Alamat Email <span>*</span></label><input name="email" id="email" type="email" placeholder="nama@email.com" value="<?=e((string)($_student['email']??''))?>" maxlength="100" required></div>
 <div class="form-group field-wide"><label for="nomor_telepon">Nomor Telepon <span class="optional">(Opsional)</span></label><input name="nomor_telepon" id="nomor_telepon" type="tel" placeholder="Contoh: 081234567890" value="<?=e((string)($_student['nomor_telepon']??''))?>" maxlength="25"><small>Dapat dikosongkan jika belum tersedia.</small></div>
</div>
