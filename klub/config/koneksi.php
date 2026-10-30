<?php
/* ============================================================
 *  config/koneksi.php  –  Koneksi PDO ke MySQL
 * ============================================================ */

$host     = "127.0.0.1";
$nama_db  = "db_klub";
$username = "root";
$password = "";

$dsn = "mysql:host={$host};dbname={$nama_db};charset=utf8mb4";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $opt);
} catch (PDOException $e) {
    die("Koneksi Gagal: " . $e->getMessage());
}