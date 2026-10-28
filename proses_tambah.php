<?php
require_once __DIR__.'/auth.php'; require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('form_mahasiswa.php');
verify_csrf();
require_once __DIR__.'/config.php';
try {
    [$nim,$nama,$prodi,$email,$telp]=valid_student($_POST);
    $conn=db();
    $check=$conn->prepare('SELECT id FROM mahasiswa WHERE nim=? LIMIT 1');
    $check->bind_param('s',$nim); $check->execute();
    $duplicate=$check->get_result()->num_rows>0; $check->close();
    if ($duplicate) throw new InvalidArgumentException('NIM sudah terdaftar. Gunakan NIM yang berbeda.');
    $stmt=$conn->prepare('INSERT INTO mahasiswa (nim,nama,program_studi,email,nomor_telepon) VALUES (?,?,?,?,?)');
    $stmt->bind_param('sssss',$nim,$nama,$prodi,$email,$telp);
    $stmt->execute(); $stmt->close(); $conn->close();
    flash('success','Data mahasiswa '.$nama.' berhasil ditambahkan!');
    redirect('data_mahasiswa.php');
} catch (InvalidArgumentException $e) {
    $_SESSION['old_student'] = array_intersect_key($_POST,array_flip(['nim','nama','program_studi','email','nomor_telepon']));
    flash('error',$e->getMessage());
} catch (mysqli_sql_exception $e) {
    $_SESSION['old_student'] = array_intersect_key($_POST,array_flip(['nim','nama','program_studi','email','nomor_telepon']));
    flash('error',$e->getCode()===1062?'NIM sudah terdaftar. Gunakan NIM yang berbeda.':'Data gagal disimpan. Periksa koneksi database dan coba lagi.');
}
redirect('form_mahasiswa.php');
