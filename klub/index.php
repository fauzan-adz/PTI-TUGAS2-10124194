<?php
/* ============================================================
 *  index.php  –  Dashboard
 * ============================================================ */
require_once "config/cek_login.php";   // auth + koneksi + bahasa
require_once "config/layout.php";

// ── Data statistik ──────────────────────────────────────────
$total   = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
$divisi  = $pdo->query("SELECT COUNT(DISTINCT divisi) FROM anggota")->fetchColumn();
$baru    = $pdo->query("SELECT COUNT(*) FROM anggota WHERE tahun_bergabung = YEAR(NOW())")->fetchColumn();

// Anggota terbaru (5 entri)
$terbaru = $pdo->query(
    "SELECT no_anggota, nama, divisi, tahun_bergabung
     FROM anggota
     ORDER BY dibuat_pada DESC
     LIMIT 5"
)->fetchAll();

// Distribusi divisi untuk tabel ringkasan
$divisi_list = $pdo->query(
    "SELECT divisi, COUNT(*) as jumlah
     FROM anggota
     GROUP BY divisi
     ORDER BY jumlah DESC"
)->fetchAll();

// Warna badge divisi (berputar)
$badge_colors = ['badge-blue','badge-green','badge-orange','badge-purple'];

layout_open(t('dashboard'), 'dashboard');
layout_topbar(t('dashboard'), t('selamat_datang') . ', ' . htmlspecialchars($_SESSION['nama_user']) . '!');
?>

<!-- ── Stats ────────────────────────────────────────────── -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">👥</div>
        <div>
            <div class="stat-val"><?= $total ?></div>
            <div class="stat-lbl"><?= t('total_anggota') ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">🏢</div>
        <div>
            <div class="stat-val"><?= $divisi ?></div>
            <div class="stat-lbl"><?= t('divisi_aktif') ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">🌟</div>
        <div>
            <div class="stat-val"><?= $baru ?></div>
            <div class="stat-lbl"><?= t('anggota_baru') ?></div>
        </div>
    </div>
</div>

<!-- ── Grid bawah ────────────────────────────────────────── -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;flex-wrap:wrap">

    <!-- Anggota Terbaru -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">⏱ <?= t('aktivitas_terakhir') ?></span>
            <a href="/klub/anggota/index.php" class="btn btn-ghost btn-sm">→ <?= t('daftar_anggota') ?></a>
        </div>
        <?php if ($terbaru): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th><?= t('no_anggota') ?></th>
                        <th><?= t('nama') ?></th>
                        <th><?= t('divisi') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($terbaru as $i => $a): ?>
                    <tr>
                        <td><code style="color:var(--accent-light);font-size:.8rem"><?= htmlspecialchars($a['no_anggota']) ?></code></td>
                        <td><?= htmlspecialchars($a['nama']) ?></td>
                        <td><span class="badge <?= $badge_colors[$i % 4] ?>"><?= htmlspecialchars($a['divisi']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state"><div class="icon">📭</div><p><?= t('data_kosong') ?></p></div>
        <?php endif; ?>
    </div>

    <!-- Distribusi Divisi -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📊 <?= t('divisi_aktif') ?></span>
        </div>
        <?php if ($divisi_list): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th><?= t('divisi') ?></th>
                        <th><?= t('total_anggota') ?></th>
                        <th>%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($divisi_list as $i => $d): ?>
                    <tr>
                        <td><span class="badge <?= $badge_colors[$i % 4] ?>"><?= htmlspecialchars($d['divisi']) ?></span></td>
                        <td><?= $d['jumlah'] ?></td>
                        <td>
                            <?php $pct = $total > 0 ? round($d['jumlah'] / $total * 100) : 0; ?>
                            <div style="display:flex;align-items:center;gap:.5rem">
                                <div style="flex:1;height:6px;background:rgba(255,255,255,.08);border-radius:99px;overflow:hidden">
                                    <div style="width:<?= $pct ?>%;height:100%;background:var(--accent);border-radius:99px"></div>
                                </div>
                                <span style="font-size:.75rem;color:var(--txt-muted)"><?= $pct ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state"><div class="icon">📭</div><p><?= t('data_kosong') ?></p></div>
        <?php endif; ?>
    </div>

</div>

<?php
layout_close();
?>