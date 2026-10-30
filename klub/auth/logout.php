<?php
/* ============================================================
 *  auth/logout.php  –  Proses logout
 * ============================================================ */
session_start();
session_unset();
session_destroy();

header("Location: /klub/auth/login.php");
exit;