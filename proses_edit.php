<?php
require_once __DIR__.'/auth.php';require_login();
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('data_mahasiswa.php');
verify_csrf();
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
if(!$id || $id<1) {flash('error','ID mahasiswa tidak valid.');redirect('data_mahasiswa.php');}
require_once __DIR__.'/config.php';
try {
    [$nim,$nama,$prodi,$email,$telp]=valid_student($_POST);
    $conn=db();$check=$conn->prepare('SELECT id FROM mahasiswa WHERE nim=? AND id<>? LIMIT 1');
    $check->bind_param('si',$nim,$id);$check->execute();$duplicate=$check->get_result()->num_rows>0;$check->close();
    if ($duplicate) throw new InvalidArgumentException('NIM sudah digunakan mahasiswa lain.');
    $stmt=$conn->prepare('UPDATE mahasiswa SET nim=?,nama=?,program_studi=?,email=?,nomor_telepon=? WHERE id=?');
    $stmt->bind_param('sssssi',$nim,$nama,$prodi,$email,$telp,$id);$stmt->execute();$stmt->close();$conn->close();
    flash('success','Data mahasiswa berhasil diperbarui.');redirect('data_mahasiswa.php');
} catch (InvalidArgumentException $e) {flash('error',$e->getMessage());}
catch (mysqli_sql_exception $e) {flash('error',$e->getCode()===1062?'NIM sudah digunakan mahasiswa lain.':'Gagal memperbarui data. Periksa database.');}
$_SESSION['old_student']=array_intersect_key($_POST,array_flip(['nim','nama','program_studi','email','nomor_telepon']));
redirect('edit_mahasiswa.php?id='.$id);
