<?php
require_once __DIR__ . '/config.php';
require_admin();

$msg = '';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['product_id'] ?? 0);
    $title = $db->real_escape_string($_POST['title'] ?? '');
    $desc = $db->real_escape_string($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock_qty = intval($_POST['stock_qty'] ?? -1);

    if ($id && $title) {
        $db->query("UPDATE wp_posts SET post_title='$title', post_content='$desc' WHERE ID=$id");
        update_meta($db, $id, '_price', $price);
        update_meta($db, $id, '_regular_price', $price);

        if ($stock_qty >= 0) {
            update_meta($db, $id, '_manage_stock', 'yes');
            update_meta($db, $id, '_stock', $stock_qty);
            update_meta($db, $id, '_stock_status', $stock_qty > 0 ? 'instock' : 'outofstock');
        }

        $msg = 'Product updated!';
    } elseif ($title) {
        $db->query("INSERT INTO wp_posts (post_title,post_content,post_status,post_type,post_date,post_date_gmt) VALUES ('$title','$desc','publish','product',NOW(),UTC_TIMESTAMP())");
        $new = $db->insert_id;
        update_meta($db, $new, '_price', $price);
        update_meta($db, $new, '_regular_price', $price);
        update_meta($db, $new, '_visibility', 'visible');
        update_meta($db, $new, '_product_type', 'simple');

        if ($stock_qty >= 0) {
            update_meta($db, $new, '_manage_stock', 'yes');
            update_meta($db, $new, '_stock', $stock_qty);
            update_meta($db, $new, '_stock_status', $stock_qty > 0 ? 'instock' : 'outofstock');
        } else {
            update_meta($db, $new, '_manage_stock', 'no');
            update_meta($db, $new, '_stock_status', 'instock');
        }

        $msg = 'Product created! ID: ' . $new;
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $db->query("UPDATE wp_posts SET post_status='trash' WHERE ID=".intval($_GET['delete']));
    $msg = 'Product deleted.';
}

// Handle toggle
if (isset($_GET['toggle'])) {
    $c = $db->query("SELECT post_status FROM wp_posts WHERE ID=".intval($_GET['toggle']))->fetch_column();
    $db->query("UPDATE wp_posts SET post_status='".($c==='publish'?'draft':'publish')."' WHERE ID=".intval($_GET['toggle']));
}

// Handle quick stock update
if (isset($_GET['restock'])) {
    $qty = intval($_GET['restock_qty'] ?? 100);
    $pid = intval($_GET['restock']);
    update_meta($db, $pid, '_manage_stock', 'yes');
    update_meta($db, $pid, '_stock', $qty);
    update_meta($db, $pid, '_stock_status', $qty > 0 ? 'instock' : 'outofstock');
    $msg = "Stock updated to $qty.";
}

$products = $db->query("SELECT ID, post_title, post_status FROM wp_posts WHERE post_type='product' AND post_status!='trash' ORDER BY post_date DESC");

$edit = null;
if (isset($_GET['edit'])) {
    $edit = $db->query("SELECT * FROM wp_posts WHERE ID=".intval($_GET['edit']))->fetch_assoc();
}

admin_header('Products');
?>
<h1>Products</h1>
<?php if ($msg): ?><div class="notice"><?= $msg ?></div><?php endif; ?>

<button class="btn" onclick="document.getElementById('pf').classList.toggle('hidden')">+ New Product</button>

<form method="post" id="pf" class="product-form <?= $edit ? '' : 'hidden' ?>">
    <h3><?= $edit ? 'Edit Product #'.$edit['ID'] : 'New Product' ?></h3>
    <?php if ($edit): ?><input type="hidden" name="product_id" value="<?= $edit['ID'] ?>"><?php endif; ?>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div>
            <label>Name</label>
            <input type="text" name="title" value="<?= htmlspecialchars($edit['post_title'] ?? '') ?>" required>
        </div>
        <div>
            <label>Price ($)</label>
            <input type="number" step="0.01" name="price" value="<?= $edit ? get_meta($db, $edit['ID'], '_price') : '' ?>" required>
        </div>
    </div>
    <label>Description</label>
    <textarea name="description" rows="3"><?= htmlspecialchars($edit['post_content'] ?? '') ?></textarea>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div>
            <label>Stock Quantity (leave empty for unlimited)</label>
            <input type="number" name="stock_qty" min="0" value="<?= $edit ? (get_meta($db, $edit['ID'], '_manage_stock')==='yes' ? get_meta($db, $edit['ID'], '_stock') : '') : '' ?>" placeholder="e.g. 50">
        </div>
    </div>
    <button type="submit" class="btn"><?= $edit ? 'Update' : 'Create' ?></button>
    <?php if ($edit): ?><a href="products.php" class="btn btn-outline">Cancel</a><?php endif; ?>
</form>

<table class="data-table">
    <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php while ($p = $products->fetch_assoc()): 
        $pr = get_meta($db, $p['ID'], '_price');
        $st = get_meta($db, $p['ID'], '_stock_status');
        $manage = get_meta($db, $p['ID'], '_manage_stock') === 'yes';
        $sq = $manage ? get_meta($db, $p['ID'], '_stock') : '—';
    ?>
        <tr>
            <td><?= $p['ID'] ?></td>
            <td><?= htmlspecialchars($p['post_title']) ?></td>
            <td>$<?= number_format($pr, 2) ?></td>
            <td>
                <?php if ($manage): ?>
                    <span class="badge <?= $st==='instock'?'badge-publish':($st==='outofstock'?'badge-outofstock':'badge-onbackorder') ?>"><?= $st ?></span>
                    (<?= $sq ?> left)
                <?php else: ?>
                    <span class="badge">Unmanaged</span>
                <?php endif; ?>
            </td>
            <td><span class="badge <?= $p['post_status']==='publish'?'badge-publish':'badge-draft' ?>"><?= $p['post_status'] ?></span></td>
            <td class="actions">
                <a href="?edit=<?= $p['ID'] ?>" class="btn-sm">Edit</a>
                <a href="?restock=<?= $p['ID'] ?>&restock_qty=50" class="btn-sm">+50</a>
                <a href="?restock=<?= $p['ID'] ?>&restock_qty=100" class="btn-sm btn-sm-dim">+100</a>
                <a href="?toggle=<?= $p['ID'] ?>" class="btn-sm btn-sm-dim"><?= $p['post_status']==='publish'?'Hide':'Show' ?></a>
                <a href="?delete=<?= $p['ID'] ?>" class="btn-sm btn-sm-del" onclick="return confirm('Delete?')">Del</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<p style="margin-top:12px;color:#888;font-size:.85rem">💡 When stock reaches 0, WooCommerce automatically marks it Out of Stock. Use +50 or +100 to restock.</p>
<?php admin_footer(); ?>
