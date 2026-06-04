<?php
require_once __DIR__ . '/config.php';
require_admin();

$orders = $db->query("SELECT ID, post_date, post_status FROM wp_posts WHERE post_type='shop_order' ORDER BY post_date DESC LIMIT 50");

admin_header('Orders');
?>
<h1>Orders</h1>
<table class="data-table">
    <thead><tr><th>Order #</th><th>Date</th><th>Customer</th><th>Total</th><th>Status</th><th>Items</th></tr></thead>
    <tbody>
    <?php while ($o = $orders->fetch_assoc()): 
        $total = get_meta($db, $o['ID'], '_order_total');
        $fn = get_meta($db, $o['ID'], '_billing_first_name');
        $ln = get_meta($db, $o['ID'], '_billing_last_name');
        $em = get_meta($db, $o['ID'], '_billing_email');
        $items = $db->query("SELECT order_item_name FROM wp_woocommerce_order_items WHERE order_id={$o['ID']} AND order_item_type='line_item'");
        $names = [];
        while ($i = $items->fetch_assoc()) $names[] = $i['order_item_name'];
    ?>
        <tr>
            <td>#<?= $o['ID'] ?></td>
            <td><?= date('M j, Y', strtotime($o['post_date'])) ?></td>
            <td><?= htmlspecialchars(trim("$fn $ln") ?: 'N/A') ?><br><small><?= htmlspecialchars($em) ?></small></td>
            <td>$<?= number_format($total, 2) ?></td>
            <td><span class="badge"><?= $o['post_status'] ?></span></td>
            <td><small><?= htmlspecialchars(implode(', ', $names)) ?></small></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php admin_footer(); ?>
