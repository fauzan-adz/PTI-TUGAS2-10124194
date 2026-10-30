<?php
/* ============================================================
 *  anggota/tambah.php  –  CREATE: Tambah Anggota
 * ============================================================ */
require_once "../config/cek_login.php";
require_once "../config/layout.php";

$errors = [];
$data   = ['no_anggota' => '', 'nama' => '', 'email' => '', 'divisi' => '', 'tahun_bergabung' => '', 'alamat' => ''];

// ── Auto-generate No. Anggota ────────────────────────────────
$lastNo = $pdo->query(
    "SELECT no_anggota FROM anggota
     WHERE no_anggota REGEXP '^KLB-[0-9]+$'
     ORDER BY CAST(SUBSTRING(no_anggota, 5) AS UNSIGNED) DESC
     LIMIT 1"
)->fetchColumn();

if ($lastNo) {
    $nextNum = (int) substr($lastNo, 4) + 1;
} else {
    $nextNum = 1;
}
$data['no_anggota'] = 'KLB-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ── Sanitasi input ──────────────────────────────────────
    $data['nama']            = trim(htmlspecialchars($_POST['nama']            ?? '', ENT_QUOTES, 'UTF-8'));
    $data['email']           = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $data['divisi']          = trim(htmlspecialchars($_POST['divisi']          ?? '', ENT_QUOTES, 'UTF-8'));
    $data['tahun_bergabung'] = trim($_POST['tahun_bergabung'] ?? '');
    $data['alamat']          = trim(htmlspecialchars($_POST['alamat']          ?? '', ENT_QUOTES, 'UTF-8'));

    // ── Validasi (percabangan if-else & switch) ─────────────
    if ($data['nama'] === '') {
        $errors['nama'] = t('err_nama_kosong');
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = t('err_email');
    }

    if ($data['divisi'] === '') {
        $errors['divisi'] = t('err_divisi');
    }

    // switch untuk validasi tahun
    switch (true) {
        case ($data['tahun_bergabung'] === ''):
            $errors['tahun_bergabung'] = t('err_tahun');
            break;
        case (!ctype_digit($data['tahun_bergabung'])):
            $errors['tahun_bergabung'] = t('err_tahun');
            break;
        case ((int)$data['tahun_bergabung'] < 1900 || (int)$data['tahun_bergabung'] > 2099):
            $errors['tahun_bergabung'] = t('err_tahun');
            break;
    }

    // ── Simpan jika valid ───────────────────────────────────
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO anggota (no_anggota, nama, email, divisi, tahun_bergabung, alamat)
             VALUES (:no, :nama, :email, :divisi, :tahun, :alamat)"
        );
        $stmt->execute([
            ':no'     => $data['no_anggota'],
            ':nama'   => $data['nama'],
            ':email'  => $data['email'],
            ':divisi' => $data['divisi'],
            ':tahun'  => (int)$data['tahun_bergabung'],
            ':alamat' => $data['alamat'],
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => t('sukses_tambah')];
        header("Location: /klub/anggota/index.php");
        exit;
    }
}

layout_open(t('tambah_anggota'), 'tambah');
layout_topbar(t('tambah_anggota'), t('daftar_anggota'));
?>

<div style="max-width:700px">
    <div class="card">
        <div class="card-header">
            <span class="card-title">➕ <?= t('tambah_anggota') ?></span>
            <a href="/klub/anggota/index.php" class="btn btn-ghost btn-sm">← <?= t('batal') ?></a>
        </div>

        <form method="POST" action="" novalidate>
            <div class="form-grid">
                <!-- No. Anggota (auto-generate) -->
                <div class="form-group">
                    <label for="no_anggota"><?= t('no_anggota') ?></label>
                    <input type="text" id="no_anggota"
                           value="<?= htmlspecialchars($data['no_anggota']) ?>"
                           readonly>
                    <small style="color:var(--text-muted,#6b7280)"></small>
                </div>

                <!-- Nama -->
                <div class="form-group">
                    <label for="nama"><?= t('nama') ?><span class="required">*</span></label>
                    <input type="text" id="nama" name="nama"
                           value="<?= htmlspecialchars($data['nama']) ?>"
                           class="<?= isset($errors['nama']) ? 'input-error' : '' ?>"
                           placeholder="Nama Lengkap">
                    <?php if (isset($errors['nama'])): ?>
                    <span class="field-error">⚠ <?= $errors['nama'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email"><?= t('email') ?><span class="required">*</span></label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($data['email']) ?>"
                           class="<?= isset($errors['email']) ? 'input-error' : '' ?>"
                           placeholder="email@contoh.com">
                    <?php if (isset($errors['email'])): ?>
                    <span class="field-error">⚠ <?= $errors['email'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Divisi -->
                <div class="form-group">
                    <label for="divisi"><?= t('divisi') ?><span class="required">*</span></label>
                    <?php
                    $divisi_options = [
                        'Kepelatihan',
                        'Manajerial',
                        'Keuangan',
                        'Humas & Publikasi',
                        'Logistik & Perlengkapan',
                        'Medis & Kesehatan',
                        'Administrasi',
                        'Sponsorship',
                        'Media & Dokumentasi',
                    ];
                    ?>
                    <select id="divisi" name="divisi"
                            class="<?= isset($errors['divisi']) ? 'input-error' : '' ?>">
                        <option value=""><?= t('pilih_divisi') ?></option>
                        <?php foreach ($divisi_options as $opt): ?>
                        <option value="<?= htmlspecialchars($opt) ?>"
                            <?= $data['divisi'] === $opt ? 'selected' : '' ?>>
                            <?= htmlspecialchars($opt) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['divisi'])): ?>
                    <span class="field-error">⚠ <?= $errors['divisi'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Tahun Bergabung -->
                <div class="form-group">
                    <label for="tahun_bergabung"><?= t('tahun_bergabung') ?><span class="required">*</span></label>
                    <input type="number" id="tahun_bergabung" name="tahun_bergabung"
                           value="<?= htmlspecialchars($data['tahun_bergabung']) ?>"
                           class="<?= isset($errors['tahun_bergabung']) ? 'input-error' : '' ?>"
                           min="1900" max="2099" placeholder="<?= date('Y') ?>">
                    <?php if (isset($errors['tahun_bergabung'])): ?>
                    <span class="field-error">⚠ <?= $errors['tahun_bergabung'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Alamat (full width) -->
                <div class="form-group full">
                    <label for="alamat"><?= t('alamat') ?></label>
                    <textarea id="alamat" name="alamat"
                              placeholder="Jl. Contoh No. 1, Kota"><?= htmlspecialchars($data['alamat']) ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 <?= t('simpan') ?></button>
                <a href="/klub/anggota/index.php" class="btn btn-ghost">✖ <?= t('batal') ?></a>
            </div>
        </form>
    </div>
</div>

<?php layout_close(); ?>
