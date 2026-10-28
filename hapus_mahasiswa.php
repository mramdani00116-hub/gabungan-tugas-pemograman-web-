<?php
require_once __DIR__.'/auth.php';require_login();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('data_mahasiswa.php');
verify_csrf();
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if (!$id || $id<1) {flash('error','ID mahasiswa tidak valid.');redirect('data_mahasiswa.php');}
require_once __DIR__.'/config.php';
try {
    $conn=db();$stmt=$conn->prepare('DELETE FROM mahasiswa WHERE id=?');
    $stmt->bind_param('i',$id);$stmt->execute();$deleted=$stmt->affected_rows;$stmt->close();$conn->close();
    flash($deleted?'success':'warning',$deleted?'Data mahasiswa berhasil dihapus.':'Data mahasiswa tidak ditemukan.');
} catch (mysqli_sql_exception $e) { flash('error','Tidak dapat menghapus data saat ini.'); }
redirect('data_mahasiswa.php');
