<?php
/* ============================================================
 *  anggota/hapus.php  –  DELETE: Hapus Anggota
 *  Diakses via link dari tabel, sudah di-confirm JS di sisi client.
 *  Di sisi server: validasi ID, then DELETE.
 * ============================================================ */
require_once "../config/cek_login.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    // Pastikan data ada sebelum dihapus
    $cek = $pdo->prepare("SELECT id FROM anggota WHERE id = :id LIMIT 1");
    $cek->execute([':id' => $id]);

    if ($cek->fetch()) {
        $stmt = $pdo->prepare("DELETE FROM anggota WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'msg' => t('sukses_hapus')];
    }
}

header("Location: /klub/anggota/index.php");
exit;
