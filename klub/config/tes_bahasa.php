<?php
require "config/bahasa.php";
?>
<h2><?php echo t("judul_app"); ?></h2>
<p><?php echo t("bahasa"); ?>:
    <a href="config/ganti_bahasa.php?lang=id">Indonesia</a>
    <a href="config/ganti_bahasa.php?lang=en">English</a>
</p>
<p><?php echo t("daftar_anggota"); ?> <?php echo t("tambah_anggota"); ?></p>