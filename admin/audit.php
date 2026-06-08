<?php
require_once __DIR__ . '/config.php';
require_admin();

$log_file = TRIPLET_ROOT . '/wp-content/logs/audit.jsonl';
$all_logs = [];
if (file_exists($log_file)) {
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach (array_reverse($lines) as $line) {
        $entry = json_decode($line, true);
        if ($entry) $all_logs[] = $entry;
    }
}

$level_colors = [
    'ERROR' => '#dc3545',
    'WARN'  => '#e8a020',
    'INFO'  => '#198754',
    'DEBUG' => '#6c757d',
];

admin_header('Audit Log');
?>
<style>
.audit-toolbar {
    display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: flex-end;
}
.audit-toolbar input, .audit-toolbar select {
    padding: 8px 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 0.9rem;
    font-family: inherit; background: #fff;
}
.audit-toolbar input[type="search"] { flex: 2; min-width: 280px; max-width: 500px; }
.audit-toolbar input[type="date"] { width: 150px; }
.audit-toolbar select { width: 110px; }
.audit-toolbar label { font-size: 0.8rem; color: #888; display: block; margin-bottom: 2px; }
.audit-summary { font-size: 0.85rem; color: #888; margin-bottom: 12px; }
.audit-summary span { font-weight: 600; color: #3D0C02; }
#audit-pagination { display: flex; gap: 6px; justify-content: center; margin-top: 20px; flex-wrap: wrap; }
#audit-pagination button {
    padding: 6px 14px; border: 1px solid #ddd; border-radius: 6px; background: #fff;
    cursor: pointer; font-size: 0.85rem; font-family: inherit;
}
#audit-pagination button.active { background: #C6742E; color: #fff; border-color: #C6742E; }
#audit-pagination button:hover:not(.active) { background: #f5e6d3; }
</style>

<h1>Audit Log</h1>

<div class="audit-toolbar">
    <div>
        <label>Search</label>
        <input type="search" id="audit-search" placeholder="Search actions, IPs, details..." onkeydown="if(event.key==='Enter')renderAudit()">
    </div>
    <div>
        <label>Level</label>
        <select id="audit-level" onchange="renderAudit()">
            <option value="">All Levels</option>
            <option value="INFO">INFO</option>
            <option value="WARN">WARN</option>
            <option value="ERROR">ERROR</option>
            <option value="DEBUG">DEBUG</option>
        </select>
    </div>
    <div>
        <label>From</label>
        <input type="date" id="audit-from" onchange="renderAudit()">
    </div>
    <div>
        <label>To</label>
        <input type="date" id="audit-to" onchange="renderAudit()">
    </div>
    <button class="btn-sm" onclick="renderAudit()" style="margin-top:auto;">Search</button>
    <button class="btn-sm btn-sm-dim" onclick="clearFilters()" style="margin-top:auto;">Clear</button>
    <button class="btn-sm" onclick="exportCSV()" style="margin-top:auto;background:#198754;color:#fff;">Export CSV</button>
</div>

<div class="audit-summary" id="audit-summary"></div>

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
    <tbody id="audit-body"></tbody>
</table>

<div id="audit-pagination"></div>

<script>
var ALL_LOGS = <?= json_encode($all_logs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
var LEVEL_COLORS = <?= json_encode($level_colors) ?>;
var PER_PAGE = 25;
var currentPage = 1;

function pad(n) { return n < 10 ? '0' + n : '' + n; }

function formatDate(ts) {
    var d = new Date(ts);
    return d.getFullYear() + '-' + pad(d.getMonth()+1) + '-' + pad(d.getDate()) + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
}

function formatData(data) {
    if (!data || Object.keys(data).length === 0) return '—';
    var out = '';
    for (var k in data) {
        var v = data[k];
        if (typeof v === 'object') v = JSON.stringify(v);
        v = String(v).replace(/</g,'&lt;').replace(/>/g,'&gt;');
        out += '<strong>' + k.replace(/</g,'&lt;') + '</strong>: ' + v + '<br>';
    }
    return out;
}

function getUser(e) {
    if (e.admin_user) return 'Admin';
    if (e.wp_user_email) return e.wp_user_email.replace(/</g,'&lt;');
    return '';
}

function getFilteredLogs() {
    var search = (document.getElementById('audit-search').value || '').toLowerCase();
    var level = document.getElementById('audit-level').value;
    var from = document.getElementById('audit-from').value;
    var to = document.getElementById('audit-to').value;

    return ALL_LOGS.filter(function(e) {
        if (level && (e.level || 'INFO') !== level) return false;
        if (from || to) {
            var ts = e.timestamp ? e.timestamp.substring(0, 10) : '';
            if (from && ts < from) return false;
            if (to && ts > to) return false;
        }
        if (search) {
            var haystack = [
                e.action, e.ip,
                getUser(e),
                JSON.stringify(e.data || {}).toLowerCase()
            ].join(' ').toLowerCase();
            if (haystack.indexOf(search) === -1) return false;
        }
        return true;
    });
}

function renderAudit() {
    var filtered = getFilteredLogs();
    var total = filtered.length;
    var totalPages = Math.ceil(total / PER_PAGE);
    if (currentPage > totalPages) currentPage = Math.max(1, totalPages);
    var page = filtered.slice((currentPage - 1) * PER_PAGE, currentPage * PER_PAGE);

    document.getElementById('audit-summary').innerHTML =
        '<span>' + total + '</span> matching entries' +
        (total > 0 ? ' · page <span>' + currentPage + '</span> of <span>' + totalPages + '</span>' : '') +
        ' · total logs: <span>' + ALL_LOGS.length + '</span>';

    var tbody = document.getElementById('audit-body');
    if (page.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#888;padding:40px;">No matching entries.</td></tr>';
    } else {
        tbody.innerHTML = page.map(function(e) {
            var level = e.level || 'INFO';
            var color = LEVEL_COLORS[level] || '#6c757d';
            return '<tr>' +
                '<td style="white-space:nowrap;">' + formatDate(e.timestamp) + '</td>' +
                '<td><span class="badge" style="background:' + color + ';color:#fff;">' + level + '</span></td>' +
                '<td style="font-weight:600;">' + (e.action || '').replace(/</g,'&lt;') + '</td>' +
                '<td>' + (e.ip || '').replace(/</g,'&lt;') + '</td>' +
                '<td>' + getUser(e) + '</td>' +
                '<td style="max-width:400px;word-break:break-word;">' + formatData(e.data) + '</td>' +
                '</tr>';
        }).join('');
    }

    var pag = document.getElementById('audit-pagination');
    if (totalPages <= 1) { pag.innerHTML = ''; return; }

    var html = '';
    var end = totalPages;

    html += '<button onclick="goPage(' + Math.max(1, currentPage - 1) + ')"' + (currentPage === 1 ? ' disabled style="opacity:0.4;"' : '') + '>← Prev</button>';

    if (end <= 7) {
        for (var i = 1; i <= end; i++) {
            html += pageBtn(i);
        }
    } else if (currentPage <= 4) {
        for (var i = 1; i <= 4; i++) html += pageBtn(i);
        html += jumpInput(end);
        html += pageBtn(end);
    } else if (currentPage >= end - 3) {
        html += pageBtn(1);
        html += jumpInput(end);
        for (var i = end - 3; i <= end; i++) html += pageBtn(i);
    } else {
        html += pageBtn(1);
        html += jumpInput(end);
        for (var i = currentPage - 2; i <= currentPage + 2; i++) html += pageBtn(i);
        html += jumpInput(end);
        html += pageBtn(end);
    }

    html += '<button onclick="goPage(' + Math.min(end, currentPage + 1) + ')"' + (currentPage === end ? ' disabled style="opacity:0.4;"' : '') + '>Next →</button>';
    pag.innerHTML = html;
}

function pageBtn(n) {
    return '<button class="' + (n === currentPage ? 'active' : '') + '" onclick="goPage(' + n + ')">' + n + '</button>';
}

function jumpInput(max) {
    return '<input type="number" min="1" max="' + max + '" placeholder="…" class="jump-input" onkeydown="if(event.key===\'Enter\')goPage(parseInt(this.value)||1)" style="width:48px;padding:4px 6px;border:1px solid #ddd;border-radius:6px;text-align:center;font-size:0.85rem;font-family:inherit;">';
}

function goPage(p) {
    currentPage = p;
    renderAudit();
    window.scrollTo({top: 0, behavior: 'smooth'});
}

function clearFilters() {
    document.getElementById('audit-search').value = '';
    document.getElementById('audit-level').value = '';
    document.getElementById('audit-from').value = '';
    document.getElementById('audit-to').value = '';
    currentPage = 1;
    renderAudit();
}

function exportCSV() {
    var filtered = getFilteredLogs();
    if (filtered.length === 0) { alert('No entries to export.'); return; }

    var escapeCSV = function(s) { return '"' + String(s).replace(/"/g, '""') + '"'; };

    var header = ['Timestamp','Level','Action','IP','User','Method','URI','Details'].map(escapeCSV).join(',');
    var rows = filtered.map(function(e) {
        var details = JSON.stringify(e.data || {}).replace(/"/g, '""');
        return [
            escapeCSV(e.timestamp || ''),
            escapeCSV(e.level || ''),
            escapeCSV(e.action || ''),
            escapeCSV(e.ip || ''),
            escapeCSV(getUser(e) || ''),
            escapeCSV(e.method || ''),
            escapeCSV(e.uri || ''),
            escapeCSV(details)
        ].join(',');
    });
    var csv = [header].concat(rows).join('\n');
    var blob = new Blob(['\uFEFF' + csv], {type: 'text/csv;charset=utf-8;'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'audit-log-' + new Date().toISOString().substring(0, 10) + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

renderAudit();
</script>

<?php admin_footer();
