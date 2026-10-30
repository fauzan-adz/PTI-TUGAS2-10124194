<?php
require "config/koneksi.php";

$jumlah = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
echo "Koneksi berhasil. Jumlah anggota: ".$jumlah;