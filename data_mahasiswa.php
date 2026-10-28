<?php
require_once __DIR__.'/auth.php'; require_login();
require_once __DIR__.'/config.php'; require_once __DIR__.'/layout.php';
$conn=db();
$q=trim((string)($_GET['q']??''));
if (strlen($q)>100) $q=substr($q,0,100);
$total=(int)$conn->query('SELECT COUNT(*) AS n FROM mahasiswa')->fetch_assoc()['n'];
if($q!==''){
    $like='%'.$q.'%';
    $stmt=$conn->prepare('SELECT id,nim,nama,program_studi,email,nomor_telepon FROM mahasiswa WHERE nim LIKE ? OR nama LIKE ? OR program_studi LIKE ? OR email LIKE ? ORDER BY id DESC');
    $stmt->bind_param('ssss',$like,$like,$like,$like); $stmt->execute();
    $results=$stmt->get_result();
}else{
    $results=$conn->query('SELECT id,nim,nama,program_studi,email,nomor_telepon FROM mahasiswa ORDER BY id DESC');
}
$count=$results->num_rows;
page_top('Data Mahasiswa','data');
?>
<div class="page-actions animate-in"><div><p class="muted">Kelola daftar mahasiswa yang tersimpan di database MySQL.</p></div><a class="btn btn-primary ripple-btn" href="form_mahasiswa.php"><?=nav_icon('plus')?> Tambah Mahasiswa</a></div>
<section class="panel table-panel reveal"><div class="panel-heading"><div><p class="eyebrow">STUDENT DATABASE</p><h2>Daftar Mahasiswa <span class="counter-tag"><?=$total?></span></h2></div><span class="section-badge">Data Terkini</span></div>
<form action="data_mahasiswa.php" method="get" class="filter-row"><div class="search-box"><?=nav_icon('search')?><input type="search" name="q" placeholder="Cari nama, NIM, email, atau program studi..." value="<?=e($q)?>" aria-label="Cari mahasiswa" maxlength="100"></div><button type="submit" class="btn btn-secondary ripple-btn">Cari <?=nav_icon('arrow')?></button><?php if($q!==''):?><a href="data_mahasiswa.php" class="btn btn-ghost ripple-btn">Reset</a><?php endif;?></form>
<div class="table-scroll"><table class="data-table full-table"><thead><tr><th>No</th><th>NIM</th><th>Nama Mahasiswa</th><th>Program Studi</th><th>Email</th><th>No. Telepon</th><th>Aksi</th></tr></thead><tbody><?php if($count===0):?><tr><td class="empty-cell" colspan="7"><span class="empty-illustration">✳</span><strong><?= $q!==''?'Tidak ada hasil pencarian':'Belum ada data mahasiswa' ?></strong><small><?= $q!==''?'Coba gunakan kata kunci lain.':'Klik tombol Tambah Mahasiswa untuk mulai mengisi data.' ?></small></td></tr><?php else:?><?php $no=1;while($row=$results->fetch_assoc()):?><tr><td class="td-num"><?=$no++?></td><td class="td-nim"><?=e($row['nim'])?></td><td><div class="name-cell"><span class="student-avatar"><?=e(strtoupper(substr($row['nama'],0,1)))?></span><strong><?=e($row['nama'])?></strong></div></td><td><span class="tag-pill"><?=e($row['program_studi'])?></span></td><td><?=e($row['email'])?></td><td><?=e($row['nomor_telepon']?:'—')?></td><td><div class="action-buttons"><a href="edit_mahasiswa.php?id=<?=(int)$row['id']?>" class="action-icon action-edit ripple-btn" aria-label="Edit <?=e($row['nama'])?>" title="Edit"><?=nav_icon('edit')?></a><form action="hapus_mahasiswa.php" method="post" data-confirm-name="<?=e($row['nama'])?>"><input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>"><input type="hidden" name="id" value="<?=(int)$row['id']?>"><button type="submit" class="action-icon action-delete ripple-btn" aria-label="Hapus <?=e($row['nama'])?>" title="Hapus"><?=nav_icon('trash')?></button></form></div></td></tr><?php endwhile;?><?php endif;?></tbody></table></div>
<div class="table-bottom"><span>Menampilkan <strong><?=$count?></strong> dari <strong><?=$total?></strong> mahasiswa<?= $q!==''?' (hasil pencarian)':'' ?></span><span class="database-note">● Terhubung ke MySQL</span></div></section>
<dialog id="confirmDialog" class="confirm-dialog" aria-labelledby="confirmTitle"><div class="dialog-icon">!</div><h3 id="confirmTitle">Hapus data mahasiswa?</h3><p id="confirmMessage">Data ini akan dihapus secara permanen.</p><div class="dialog-actions"><button class="btn btn-secondary ripple-btn" type="button" data-cancel-delete>Batal</button><button class="btn btn-danger ripple-btn" type="button" data-approve-delete>Ya, Hapus Data</button></div></dialog>
<?php if(isset($stmt))$stmt->close();$conn->close();page_bottom();?>
