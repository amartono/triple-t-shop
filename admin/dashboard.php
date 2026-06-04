<?php
require_once __DIR__ . '/config.php';
require_admin();

$products = $db->query("SELECT COUNT(*) FROM wp_posts WHERE post_type='product' AND post_status='publish'")->fetch_column();
$orders = $db->query("SELECT COUNT(*) FROM wp_posts WHERE post_type='shop_order'")->fetch_column();
$reviews = $db->query("SELECT COUNT(*) FROM wp_comments WHERE comment_approved='0'")->fetch_column();
$revenue = $db->query("SELECT COALESCE(SUM(meta_value),0) FROM wp_postmeta pm JOIN wp_posts p ON p.ID=pm.post_id WHERE p.post_type='shop_order' AND pm.meta_key='_order_total'")->fetch_column();

admin_header('Dashboard');
?>
<h1>Dashboard</h1>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-num"><?= $products ?></div>
        <div class="stat-label">Products</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $orders ?></div>
        <div class="stat-label">Orders</div>
    </div>
    <div class="stat-card">
        <div class="stat-num"><?= $reviews ?></div>
        <div class="stat-label">Pending Reviews</div>
    </div>
    <div class="stat-card">
        <div class="stat-num">$<?= number_format($revenue, 2) ?></div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>
<?php
admin_footer();
