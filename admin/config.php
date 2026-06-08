<?php
// Admin Panel Config — standalone, no WordPress dependency
session_start();

// Session security: regenerate ID periodically
if (empty($_SESSION['_created'])) {
    $_SESSION['_created'] = time();
    session_regenerate_id(true);
} elseif (time() - $_SESSION['_created'] > 3600) {
    session_regenerate_id(true);
    $_SESSION['_created'] = time();
}

define('TRIPLET_ROOT', dirname(__DIR__));
define('ADMIN_USER', 'admin');
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wordpress');

require_once TRIPLET_ROOT . '/wp-content/mu-plugins/logger.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

function admin_nonce() {
    if (empty($_SESSION['admin_nonce'])) {
        $_SESSION['admin_nonce'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['admin_nonce'];
}

function admin_verify_nonce($nonce) {
    if (empty($_SESSION['admin_nonce']) || empty($nonce)) return false;
    return hash_equals($_SESSION['admin_nonce'], $nonce);
}

function admin_require_nonce() {
    $nonce = $_POST['_admin_nonce'] ?? $_GET['_admin_nonce'] ?? '';
    if (!admin_verify_nonce($nonce)) {
        http_response_code(403);
        die('Invalid or expired security token. Please go back and refresh the page.');
    }
}

function is_admin_logged_in() {
    return !empty($_SESSION['admin_logged_in']);
}

function require_admin() {
    if (!is_admin_logged_in()) {
        header('Location: /admin/index.php');
        exit;
    }
}

function admin_header($title) {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> — TripleT Admin</title>
    <link rel="stylesheet" href="/admin/admin.css">
</head>
<body>
<div class="admin-wrap">
    <nav class="admin-nav">
        <div class="admin-brand">⚙️ TripleT Admin</div>
        <a href="/admin/dashboard.php">Dashboard</a>
        <a href="/admin/products.php">Products</a>
        <a href="/admin/orders.php">Orders</a>
        <a href="/admin/reviews.php">Reviews</a>
        <a href="/admin/audit.php">Audit Log</a>
        <a href="/admin/logout.php" class="logout">Logout</a>
    </nav>
    <div class="admin-content">
    <?php
}

function admin_footer() {
    ?>
    </div>
</div>
</body>
</html>
    <?php
}

function get_meta($db, $post_id, $key) {
    $r = $db->query("SELECT meta_value FROM wp_postmeta WHERE post_id=".intval($post_id)." AND meta_key='".$db->real_escape_string($key)."' LIMIT 1");
    return $r ? $r->fetch_column() : '';
}

function update_meta($db, $post_id, $key, $value) {
    $exists = $db->query("SELECT meta_id FROM wp_postmeta WHERE post_id=".intval($post_id)." AND meta_key='".$db->real_escape_string($key)."' LIMIT 1");
    if ($exists && $exists->num_rows > 0) {
        $db->query("UPDATE wp_postmeta SET meta_value='".$db->real_escape_string($value)."' WHERE post_id=".intval($post_id)." AND meta_key='".$db->real_escape_string($key)."'");
    } else {
        $db->query("INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES (".intval($post_id).", '".$db->real_escape_string($key)."', '".$db->real_escape_string($value)."')");
    }
}
