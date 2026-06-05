<?php
require_once __DIR__ . '/config.php';
require_admin();

$search = $_GET['search'] ?? '';

$where = "1=1";
if ($search) {
    $s = $db->real_escape_string($search);
    $where = "o.id = '" . intval($search) . "' OR o.status LIKE '%$s%' OR o.total_amount LIKE '%$s%' OR addr.first_name LIKE '%$s%' OR addr.last_name LIKE '%$s%' OR addr.email LIKE '%$s%'";
}

$sql = "
    SELECT o.id, o.status, o.total_amount, o.date_created_gmt,
           addr.first_name, addr.last_name, addr.email
    FROM wp_wc_orders o
    LEFT JOIN wp_wc_order_addresses addr ON o.id = addr.order_id AND addr.address_type = 'billing'
    WHERE $where
    ORDER BY o.id DESC
    LIMIT 100
";

$orders = $db->query($sql);

$status_labels = [
    'wc-pending'    => 'Pending',
    'wc-processing' => 'Processing',
    'wc-on-hold'    => 'On Hold',
    'wc-completed'  => 'Completed',
    'wc-cancelled'  => 'Cancelled',
    'wc-refunded'   => 'Refunded',
    'wc-failed'     => 'Failed',
    'wc-checkout-draft' => 'Draft',
];

function status_color($s) {
    $map = [
        'wc-processing' => '#198754', 'wc-completed' => '#0d6efd',
        'wc-on-hold' => '#e8a020', 'wc-pending' => '#6c757d',
        'wc-cancelled' => '#dc3545', 'wc-refunded' => '#dc3545',
        'wc-failed' => '#dc3545', 'wc-checkout-draft' => '#bbb',
    ];
    return $map[$s] ?? '#6c757d';
}

admin_header('Orders');
?>
<h1>Orders</h1>

<div style="margin-bottom:16px;">
    <input type="search" id="order-search" placeholder="Search by order #, customer name, email, or status..." value="<?= htmlspecialchars($search) ?>" style="padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:0.9rem;width:350px;font-family:inherit;" onkeydown="if(event.key==='Enter'){var s=this.value;window.location='?search='+encodeURIComponent(s)}">
    <button class="btn-sm" onclick="var s=document.getElementById('order-search').value;window.location='?search='+encodeURIComponent(s)" style="margin-left:8px;">Search</button>
    <?php if ($search): ?>
        <a href="orders.php" class="btn-sm btn-sm-dim" style="text-decoration:none;">Clear</a>
    <?php endif; ?>
</div>

<table class="data-table">
    <thead><tr><th>Order #</th><th>Date</th><th>Customer</th><th>Total</th><th>Status</th><th>Items</th></tr></thead>
    <tbody>
    <?php while ($o = $orders->fetch_assoc()): 
        $items = $db->query("SELECT order_item_name FROM wp_woocommerce_order_items WHERE order_id={$o['id']} AND order_item_type='line_item'");
        $names = [];
        while ($i = $items->fetch_assoc()) $names[] = $i['order_item_name'];
        $label = $status_labels[$o['status']] ?? $o['status'];
    ?>
        <tr>
            <td><strong>#<?= $o['id'] ?></strong></td>
            <td><?= $o['date_created_gmt'] ? date('M j, Y', strtotime($o['date_created_gmt'])) : '—' ?></td>
            <td><?= htmlspecialchars(trim(($o['first_name']??'').' '.($o['last_name']??'')) ?: 'N/A') ?><br><small style="color:#888;"><?= htmlspecialchars($o['email'] ?? '') ?></small></td>
            <td>$<?= number_format($o['total_amount'] ?? 0, 2) ?></td>
            <td><span class="badge" style="background:<?= status_color($o['status']) ?>;color:#fff;"><?= htmlspecialchars($label) ?></span></td>
            <td><small><?= htmlspecialchars(implode(', ', $names)) ?></small></td>
        </tr>
    <?php endwhile; ?>
    <?php if ($orders->num_rows === 0): ?>
        <tr><td colspan="6" style="text-align:center;color:#888;padding:40px;">No orders found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<?php admin_footer();
