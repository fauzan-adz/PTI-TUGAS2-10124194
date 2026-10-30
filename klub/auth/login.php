<?php

// auth/login.php  –  Halaman Login — Scorpion Basketball Club

require_once "../config/koneksi.php";
require_once "../config/bahasa.php";

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitasi input
    $username = trim(htmlspecialchars($_POST["username"] ?? "", ENT_QUOTES, "UTF-8"));
    $password = $_POST["password"] ?? "";

    // Validasi kelengkapan (percabangan if-else)
    if ($username === "" || $password === "") {
        $error = t("login_gagal");
    } else {
        // Query dengan prepared statement (cegah SQL Injection)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
        $stmt->execute([":u" => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);
            $_SESSION["user_id"]   = $user["id"];
            $_SESSION["nama_user"] = $user["nama"];
            header("Location: ../index.php");
            exit;
        } else {
            $error = t("login_gagal");
        }
    }
}

$lang = $_SESSION["bahasa"];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login – Scorpion Basketball Club">
    <title><?= t("login") ?> – Scorpion Basketball Club</title>
    <link rel="stylesheet" href="/klub/assets/css/style.css">
</head>
<body>
<div class="login-page">

    <!-- ===== SECTION KIRI ===== -->
    <div class="login-left">
        <div class="login-left-content">
            <img src="/klub/assets/img/logo.jpg" alt="Scorpion Basketball Club Logo">
            <h2>Scorpion<br>Basketball Club</h2>
            <p>Member Management System</p>
            <div class="login-dividers">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <!-- ===== SECTION KANAN ===== -->
    <div class="login-right">
        <div class="login-box">

            <!-- Logo (mobile only) -->
            <div class="login-logo-mobile">
                <img src="/klub/assets/img/logo.jpg" alt="Logo">
            </div>

            <h1>Scorpion BC</h1>
            <p class="login-sub">Member Management System — <?= t("login") ?></p>

            <!-- Pilih Bahasa -->
            <div class="login-lang">
                <a href="/klub/config/ganti_bahasa.php?lang=id"
                   class="<?= $lang === 'id' ? 'active-lang' : '' ?>">🇮🇩 Indonesia</a>
                <a href="/klub/config/ganti_bahasa.php?lang=en"
                   class="<?= $lang === 'en' ? 'active-lang' : '' ?>">🇺🇸 English</a>
            </div>

            <!-- Alert Error -->
            <?php if ($error !== ""): ?>
            <div class="alert alert-danger" role="alert">
                ⚠️ <?= $error ?>
            </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form method="POST" action="" novalidate>
                <div class="form-group">
                    <label for="username"><?= t("username") ?></label>
                    <input type="text" id="username" name="username"
                           value="<?= htmlspecialchars($_POST["username"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                           autocomplete="username" placeholder="Username" required>
                </div>
                <div class="form-group" style="margin-bottom:1.5rem">
                    <label for="password"><?= t("password") ?></label>
                    <input type="password" id="password" name="password"
                           autocomplete="current-password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary"
                        style="width:100%;justify-content:center;padding:.8rem;font-size:.95rem;letter-spacing:.04em">
                        <?= t("tombol_login") ?>
                </button>
            </form>

            <p style="text-align:center;margin-top:2rem;font-size:.72rem;color:var(--txt-muted)">
                © 2026 Scorpion Basketball Club. All rights reserved.
            </p>
        </div>
    </div>

</div>
</body>
</html>