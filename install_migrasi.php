<?php
// Jalankan setelah import SQL. Aman diulang: tidak menghapus baris lama.
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config.php';
$conn = db();
$exists = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc();
if ((int)$exists['total'] > 0) {
    redirect('login.php');
}
// Skema dari tugas sebelumnya bisa saja sudah ada sebelum zip ini dipakai.
$columns = [];
foreach ($conn->query('SHOW COLUMNS FROM mahasiswa') as $r) $columns[$r['Field']] = true;
if (!isset($columns['nomor_telepon'])) $conn->query('ALTER TABLE mahasiswa ADD nomor_telepon VARCHAR(25) NULL');
if (!isset($columns['created_at'])) $conn->query('ALTER TABLE mahasiswa ADD created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
// Pastikan NIM hanya satu record: untuk database baru, UNIQUE dibuat pada tabel kosong.
// Pada database lama dengan NIM ganda, jangan mengubah/merusak data pengguna.
$hasIndex = false;
foreach ($conn->query("SHOW INDEX FROM mahasiswa WHERE Key_name = 'nim_unique'") as $index) $hasIndex = true;
if (!$hasIndex) {
    try { $conn->query('ALTER TABLE mahasiswa ADD UNIQUE KEY nim_unique (nim)'); } catch (mysqli_sql_exception $e) { /* data lama mungkin duplikat */ }
}
$nim = '4112755201250084';
$stmt = $conn->prepare('SELECT id FROM mahasiswa WHERE nim = ? LIMIT 1');
$stmt->bind_param('s', $nim); $stmt->execute(); $found = $stmt->get_result()->num_rows; $stmt->close();
if (!$found && (int)($conn->query('SELECT COUNT(*) AS n FROM mahasiswa')->fetch_assoc()['n']) === 0) {
    $nama='Muhammad Ramdani Saputra'; $prodi='Informatika'; $email='mramdani00116@gmail.com'; $phone='';
    $stmt=$conn->prepare('INSERT INTO mahasiswa (nim,nama,program_studi,email,nomor_telepon) VALUES (?,?,?,?,?)');
    $stmt->bind_param('sssss',$nim,$nama,$prodi,$email,$phone); $stmt->execute(); $stmt->close();
}
$conn->close();
redirect('setup.php');
