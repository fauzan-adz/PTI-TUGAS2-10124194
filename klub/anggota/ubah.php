<?php
/* ============================================================
 *  anggota/ubah.php  –  UPDATE: Edit Anggota
 * ============================================================ */
require_once "../config/cek_login.php";
require_once "../config/layout.php";

// ── Ambil ID dari URL ───────────────────────────────────────
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: /klub/anggota/index.php");
    exit;
}

// ── Ambil data lama ─────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM anggota WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    header("Location: /klub/anggota/index.php");
    exit;
}

$errors = [];
$data   = $row; // isi awal dari DB

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ── Sanitasi input ──────────────────────────────────────
    // no_anggota tidak diambil dari POST — tetap pakai nilai dari DB
    $data['nama']            = trim(htmlspecialchars($_POST['nama']            ?? '', ENT_QUOTES, 'UTF-8'));
    $data['email']           = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $data['divisi']          = trim(htmlspecialchars($_POST['divisi']          ?? '', ENT_QUOTES, 'UTF-8'));
    $data['tahun_bergabung'] = trim($_POST['tahun_bergabung'] ?? '');
    $data['alamat']          = trim(htmlspecialchars($_POST['alamat']          ?? '', ENT_QUOTES, 'UTF-8'));

    // ── Validasi ────────────────────────────────────────────
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
        case (!ctype_digit($data['tahun_bergabung'])):
        case ((int)$data['tahun_bergabung'] < 1900 || (int)$data['tahun_bergabung'] > 2099):
            $errors['tahun_bergabung'] = t('err_tahun');
            break;
    }

    // ── Simpan jika valid ───────────────────────────────────
    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "UPDATE anggota
             SET nama = :nama, email = :email,
                 divisi = :divisi, tahun_bergabung = :tahun, alamat = :alamat
             WHERE id = :id"
        );
        $stmt->execute([
            ':nama'   => $data['nama'],
            ':email'  => $data['email'],
            ':divisi' => $data['divisi'],
            ':tahun'  => (int)$data['tahun_bergabung'],
            ':alamat' => $data['alamat'],
            ':id'     => $id,
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => t('sukses_ubah')];
        header("Location: /klub/anggota/index.php");
        exit;
    }
}

layout_open(t('ubah_anggota'), 'anggota');
layout_topbar(t('ubah_anggota'), t('daftar_anggota'));
?>

<div style="max-width:700px">
    <div class="card">
        <div class="card-header">
            <span class="card-title">✏️ <?= t('ubah_anggota') ?></span>
            <a href="/klub/anggota/index.php" class="btn btn-ghost btn-sm">← <?= t('batal') ?></a>
        </div>

        <form method="POST" action="" novalidate>
            <div class="form-grid">
                <!-- No. Anggota (tidak dapat diubah) -->
                <div class="form-group">
                    <label for="no_anggota"><?= t('no_anggota') ?></label>
                    <input type="text" id="no_anggota"
                           value="<?= htmlspecialchars($data['no_anggota']) ?>"
                           readonly>
                    <small style="color:var(--text-muted,#6b7280)">🔒 Nomor anggota tidak dapat diubah.</small>
                </div>

                <!-- Nama -->
                <div class="form-group">
                    <label for="nama"><?= t('nama') ?><span class="required">*</span></label>
                    <input type="text" id="nama" name="nama"
                           value="<?= htmlspecialchars($data['nama']) ?>"
                           class="<?= isset($errors['nama']) ? 'input-error' : '' ?>">
                    <?php if (isset($errors['nama'])): ?>
                    <span class="field-error">⚠ <?= $errors['nama'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email"><?= t('email') ?><span class="required">*</span></label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($data['email']) ?>"
                           class="<?= isset($errors['email']) ? 'input-error' : '' ?>">
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
                           min="1900" max="2099">
                    <?php if (isset($errors['tahun_bergabung'])): ?>
                    <span class="field-error">⚠ <?= $errors['tahun_bergabung'] ?></span>
                    <?php endif; ?>
                </div>

                <!-- Alamat -->
                <div class="form-group full">
                    <label for="alamat"><?= t('alamat') ?></label>
                    <textarea id="alamat" name="alamat"><?= htmlspecialchars($data['alamat']) ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 <?= t('perbarui') ?></button>
                <a href="/klub/anggota/index.php" class="btn btn-ghost">✖ <?= t('batal') ?></a>
            </div>
        </form>
    </div>
</div>

<?php layout_close(); ?>
