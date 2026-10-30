<?php
/* ============================================================
 *  config/cek_login.php  –  Proteksi halaman (harus login)
 *  Juga memuat koneksi & bahasa sehingga halaman cukup
 *  memanggil satu file ini.
 * ============================================================ */

require_once __DIR__ . "/koneksi.php";
require_once __DIR__ . "/bahasa.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: /klub/auth/login.php");
    exit;
}