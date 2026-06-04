<?php
require_once __DIR__ . '/config.php';
require_admin();

$log_file = TRIPLET_ROOT . '/wp-content/logs/audit.jsonl';
$entries = [];

$all_lines = file_exists($log_file) ? file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$all_lines = array_reverse($all_lines);

$page = max(1, intval($_GET['p'] ?? 1));
$per_page = 50;
$total = count($all_lines);
$lines = array_slice($all_lines, ($page - 1) * $per_page, $per_page);

foreach ($lines as $line) {
    $entry = json_decode($line, true);
    if ($entry) $entries[] = $entry;
}

$total_pages = ceil($total / $per_page);

$level_colors = [
    'ERROR' => '#dc3545',
    'WARN'  => '#e8a020',
    'INFO'  => '#198754',
    'DEBUG' => '#6c757d',
];

admin_header('Audit Log');
?>
<h1>Audit Log</h1>
<p style="color:#888;margin-bottom:16px;"><?= $total ?> total entries · showing <?= count($entries) ?> per page · newest first</p>

<table class="data-table" style="font-size:0.85rem;">
    <thead>
        <tr>
            <th>Time</th>
            <th>Level</th>
            <th>Action</th>
            <th>IP</th>
            <th>User</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($entries as $e):
        $level = $e['level'] ?? 'INFO';
        $color = $level_colors[$level] ?? '#6c757d';
        $action = htmlspecialchars($e['action'] ?? '');
        $ip = htmlspecialchars($e['ip'] ?? '');
        $user = '';
        if (!empty($e['admin_user'])) $user = 'Admin';
        if (!empty($e['wp_user_email'])) $user = htmlspecialchars($e['wp_user_email']);
        $data = $e['data'] ?? [];

        $detail = '';
        foreach ($data as $k => $v) {
            if (is_array($v)) $v = json_encode($v);
            $detail .= '<strong>' . htmlspecialchars($k) . '</strong>: ' . htmlspecialchars($v) . '<br>';
        }
    ?>
        <tr>
            <td style="white-space:nowrap;"><?= date('m/d H:i:s', strtotime($e['timestamp'] ?? '')) ?></td>
            <td><span class="badge" style="background:<?= $color ?>;color:#fff;"><?= htmlspecialchars($level) ?></span></td>
            <td style="font-weight:600;"><?= $action ?></td>
            <td><?= $ip ?></td>
            <td><?= $user ?></td>
            <td style="max-width:400px;word-break:break-word;"><?= $detail ?: '—' ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($entries)): ?>
        <tr><td colspan="6" style="text-align:center;color:#888;padding:40px;">No log entries yet. Actions will appear here as they happen.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php if ($total_pages > 1): ?>
<div style="display:flex;gap:8px;justify-content:center;margin-top:20px;">
    <?php for ($i = 1; $i <= min($total_pages, 20); $i++): ?>
        <a href="?p=<?= $i ?>" class="btn-sm <?= $i === $page ? 'btn' : 'btn-sm-dim' ?>" style="text-decoration:none;"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($total_pages > 20): ?>
        <span style="padding:4px 8px;">... <?= $total_pages ?> pages</span>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php admin_footer();
