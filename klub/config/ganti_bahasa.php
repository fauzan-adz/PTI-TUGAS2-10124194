<?php
session_start();

$bahasa_tersedia = ["id" , "en"];
$pilihan = $_GET["lang"] ?? "id";

if (in_array($pilihan, $bahasa_tersedia)){
    $_SESSION["bahasa"] = $pilihan;
}

$kembali = $_SERVER["HTTP_REFERER"] ?? "../index.php";
header("Location: " . $kembali);
exit;