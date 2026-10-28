<?php
// Konfigurasi database lokal. Sesuaikan dengan XAMPP / Laragon.
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'web_programing_1';

function db(): mysqli
{
    if (!extension_loaded('mysqli')) {
        http_response_code(500);
        exit('Ekstensi mysqli belum aktif. Aktifkan ekstensi MySQL pada PHP (XAMPP/Laragon).');
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conn->set_charset('utf8mb4');
        return $conn;
    } catch (mysqli_sql_exception $e) {
        http_response_code(500);
        exit('Koneksi database belum siap. Jalankan Apache dan MySQL, import database.sql di phpMyAdmin, lalu periksa config.php.');
    }
}
