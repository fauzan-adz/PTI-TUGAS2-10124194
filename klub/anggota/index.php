<?php
/* ============================================================
 *  anggota/index.php  –  READ: Daftar Anggota
 * ============================================================ */
require_once "../config/cek_login.php";
require_once "../config/layout.php";

// ── Flash Message dari session ──────────────────────────────
$flash = $_SESSION["flash"] ?? null;
unset($_SESSION["flash"]);

// ── Filter & Pencarian (GET) ────────────────────────────────
$cari        = trim(filter_input(INPUT_GET, 'cari', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
$filter_div  = trim(filter_input(INPUT_GET, 'divisi', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

// ── Query dinamis ───────────────────────────────────────────
$sql    = "SELECT * FROM anggota WHERE 1=1";
$params = [];

if ($cari !== '') {
    $sql    .= " AND (nama LIKE :cari OR no_anggota LIKE :cari OR email LIKE :cari)";
    $params[':cari'] = "%{$cari}%";
}
if ($filter_div !== '') {
    $sql    .= " AND divisi = :divisi";
    $params[':divisi'] = $filter_div;
}

$sql .= " ORDER BY CAST(SUBSTRING(no_anggota, 5) AS UNSIGNED) ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$anggota_list = $stmt->fetchAll();

// ── Daftar divisi untuk dropdown filter ────────────────────
$divisi_opts = $pdo->query(
    "SELECT DISTINCT divisi FROM anggota ORDER BY divisi"
)->fetchAll(PDO::FETCH_COLUMN);

// Warna badge
$badge_colors = ['badge-blue','badge-green','badge-orange','badge-purple'];
$divisi_badge = [];
foreach ($divisi_opts as $i => $d) {
    $divisi_badge[$d] = $badge_colors[$i % 4];
}

layout_open(t('daftar_anggota'), 'anggota');
layout_topbar(t('daftar_anggota'), t('judul_app'));
?>

<!-- ── Flash ────────────────────────────────────────────── -->
<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>" role="alert">
    <?= $flash['type'] === 'success' ? '✅' : '⚠️' ?> <?= htmlspecialchars($flash['msg']) ?>
</div>
<?php endif; ?>

<!-- ── Card Utama ────────────────────────────────────────── -->
<div class="card">
    <div class="card-header">
        <span class="card-title">👥 <?= t('daftar_anggota') ?> (<?= count($anggota_list) ?>)</span>
        <a href="/klub/anggota/tambah.php" class="btn btn-primary">
            ➕ <?= t('tambah_anggota') ?>
        </a>
    </div>

    <!-- Filter & Search -->
    <form method="GET" action="" style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.25rem">
        <input type="text" name="cari" placeholder="<?= t('cari') ?>"
               value="<?= htmlspecialchars($cari) ?>"
               style="flex:1;min-width:200px">
        <select name="divisi" style="width:200px">
            <option value=""><?= t('filter_divisi') ?></option>
            <?php foreach ($divisi_opts as $d): ?>
            <option value="<?= htmlspecialchars($d) ?>" <?= $filter_div === $d ? 'selected' : '' ?>>
                <?= htmlspecialchars($d) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">🔍</button>
        <?php if ($cari !== '' || $filter_div !== ''): ?>
        <a href="/klub/anggota/index.php" class="btn btn-ghost">✖ Reset</a>
        <?php endif; ?>
    </form>

    <!-- Tabel -->
    <?php if ($anggota_list): ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th><?= t('no_anggota') ?></th>
                    <th><?= t('nama') ?></th>
                    <th><?= t('email') ?></th>
                    <th><?= t('divisi') ?></th>
                    <th><?= t('tahun_bergabung') ?></th>
                    <th><?= t('aksi') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($anggota_list as $no => $a): ?>
                <tr>
                    <td style="color:var(--txt-muted);font-size:.8rem"><?= $no + 1 ?></td>
                    <td>
                        <code style="color:var(--accent-light);font-size:.82rem">
                            <?= htmlspecialchars($a['no_anggota']) ?>
                        </code>
                    </td>
                    <td style="font-weight:500"><?= htmlspecialchars($a['nama']) ?></td>
                    <td style="color:var(--txt-sec);font-size:.84rem"><?= htmlspecialchars($a['email']) ?></td>
                    <td>
                        <span class="badge <?= $divisi_badge[$a['divisi']] ?? 'badge-blue' ?>">
                            <?= htmlspecialchars($a['divisi']) ?>
                        </span>
                    </td>
                    <td style="color:var(--txt-sec)"><?= htmlspecialchars($a['tahun_bergabung']) ?></td>
                    <td>
                        <div style="display:flex;gap:.4rem">
                            <a href="/klub/anggota/ubah.php?id=<?= $a['id'] ?>"
                               class="btn btn-ghost btn-sm" title="<?= t('ubah') ?>">✏️</a>
                            <a href="/klub/anggota/hapus.php?id=<?= $a['id'] ?>"
                               class="btn btn-danger btn-sm"
                               title="<?= t('hapus') ?>"
                               onclick="return confirm('<?= t('konfirmasi_hapus') ?>')">🗑</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="icon">📭</div>
        <p><?= t('data_kosong') ?></p>
        <a href="/klub/anggota/tambah.php" class="btn btn-primary" style="margin-top:1rem">
            ➕ <?= t('tambah_anggota') ?>
        </a>
    </div>
    <?php endif; ?>
</div>

<?php
layout_close();
?>
