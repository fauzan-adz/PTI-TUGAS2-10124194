<?php
/* ============================================================
 *  config/bahasa.php  –  Inisialisasi sesi & fungsi terjemahan
 * ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$bahasa_tersedia = ["id", "en"];

if (!isset($_SESSION["bahasa"]) || !in_array($_SESSION["bahasa"], $bahasa_tersedia)) {
    $_SESSION["bahasa"] = "id";
}

$teks = require __DIR__ . "/../lang/" . $_SESSION["bahasa"] . ".php";

/**
 * t() — ambil string terjemahan berdasarkan kunci
 */
function t(string $kunci): string
{
    global $teks;
    return htmlspecialchars($teks[$kunci] ?? $kunci, ENT_QUOTES, 'UTF-8');
}