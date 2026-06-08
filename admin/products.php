<?php
require_once __DIR__ . '/config.php';
require_admin();

$msg = '';

function handle_image_upload($db, $file, $product_id, &$err = null) {
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        $codes = [
            UPLOAD_ERR_INI_SIZE   => 'File too large (exceeds server limit)',
            UPLOAD_ERR_FORM_SIZE  => 'File too large (exceeds form limit)',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Server temp directory missing',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write file to disk',
        ];
        $err_code = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        $err = $codes[$err_code] ?? 'Upload error code: ' . $err_code;
        return false;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
        $err = 'Invalid file type: .' . $ext;
        return false;
    }

    $upload_base = TRIPLET_ROOT . '/wp-content/uploads';
    $upload_dir = $upload_base . '/' . date('Y') . '/' . date('m');
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $err = 'Cannot create upload directory: ' . $upload_dir;
            return false;
        }
    }
    if (!is_writable($upload_dir)) {
        $err = 'Upload directory not writable: ' . $upload_dir;
        return false;
    }

    $filename = 'product-' . $product_id . '-' . time() . '.' . $ext;
    $dest = $upload_dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest) && !copy($file['tmp_name'], $dest)) {
        $err = 'Failed to save uploaded file to ' . $dest;
        return false;
    }

    $rel_path = date('Y') . '/' . date('m') . '/' . $filename;
    $mime = $db->real_escape_string($file['type']);
    $title = $db->real_escape_string($file['name']);

    $db->query("INSERT INTO wp_posts (post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,post_name,post_status,post_type,post_mime_type,guid,post_date,post_date_gmt) VALUES ('$title','','','','','','attachment-{$product_id}','inherit','attachment','$mime','$rel_path',NOW(),UTC_TIMESTAMP())");

    if ($db->error) {
        $err = 'Database error: ' . $db->error;
        return false;
    }

    $attach_id = $db->insert_id;

    update_meta($db, $attach_id, '_wp_attached_file', $rel_path);
    update_meta($db, $product_id, '_thumbnail_id', $attach_id);

    // Generate WordPress image sizes (srcset, large_image, etc.)
    $wp_load = TRIPLET_ROOT . '/wp-load.php';
    if (file_exists($wp_load)) {
        @include_once $wp_load;
        if (function_exists('wp_generate_attachment_metadata')) {
            $full_path = TRIPLET_ROOT . '/wp-content/uploads/' . $rel_path;
            require_once TRIPLET_ROOT . '/wp-admin/includes/image.php';
            $metadata = wp_generate_attachment_metadata($attach_id, $full_path);
            if ($metadata) {
                $meta_json = $db->real_escape_string(json_encode($metadata));
                $db->query("INSERT INTO wp_postmeta (post_id, meta_key, meta_value) VALUES ($attach_id, '_wp_attachment_metadata', '$meta_json') ON DUPLICATE KEY UPDATE meta_value='$meta_json'");
            }
        }
    }

    ttt_log('product_image_uploaded', ['product_id' => $product_id, 'attachment_id' => $attach_id, 'file' => $rel_path]);

    return $rel_path;
}

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
        if (!empty($_FILES['product_image']['tmp_name'])) {
            $img_err = null;
            if (!handle_image_upload($db, $_FILES['product_image'], $id, $img_err)) {
                $msg .= ' (Image upload failed: ' . $img_err . ')';
            }
        }
        ttt_log('product_updated', ['id' => $id, 'title' => $_POST['title'] ?? '', 'price' => $price, 'stock_qty' => $stock_qty]);
        $_SESSION['flash_msg'] = $msg;
        header('Location: products.php');
        exit;
    } elseif ($title) {
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $_POST['title'] ?? ''));
    $slug = $db->real_escape_string($slug);
    // Ensure unique slug
    $existing = $db->query("SELECT ID FROM wp_posts WHERE post_name='$slug' AND post_type='product' AND post_status!='trash'");
    if ($existing && $existing->num_rows > 0) {
        $slug .= '-' . time();
    }
    $db->query("INSERT INTO wp_posts (post_title,post_content,post_excerpt,to_ping,pinged,post_content_filtered,post_name,post_status,post_type,post_date,post_date_gmt) VALUES ('$title','$desc','','','','','$slug','publish','product',NOW(),UTC_TIMESTAMP())");
    $new = $db->insert_id;
    update_meta($db, $new, '_price', $price);
    update_meta($db, $new, '_regular_price', $price);
    update_meta($db, $new, '_visibility', 'visible');
    update_meta($db, $new, '_product_type', 'simple');

    // WooCommerce required defaults
    update_meta($db, $new, '_tax_status', 'taxable');
    update_meta($db, $new, '_tax_class', '');
    update_meta($db, $new, '_virtual', 'no');
    update_meta($db, $new, '_downloadable', 'no');
    update_meta($db, $new, '_sold_individually', 'no');
    update_meta($db, $new, '_backorders', 'no');
    update_meta($db, $new, '_sku', '');
    update_meta($db, $new, '_product_version', '10.7.0');

    // Assign default category
    $cat = $db->query("SELECT tt.term_taxonomy_id FROM wp_terms t JOIN wp_term_taxonomy tt ON t.term_id=tt.term_id WHERE t.name='Uncategorized' AND tt.taxonomy='product_cat' LIMIT 1");
    if ($cat && $cat->num_rows > 0) {
        $tt_id = $cat->fetch_column();
        $db->query("INSERT INTO wp_term_relationships (object_id, term_taxonomy_id) VALUES ($new, $tt_id)");
    }

        if ($stock_qty >= 0) {
            update_meta($db, $new, '_manage_stock', 'yes');
            update_meta($db, $new, '_stock', $stock_qty);
            update_meta($db, $new, '_stock_status', $stock_qty > 0 ? 'instock' : 'outofstock');
        } else {
            update_meta($db, $new, '_manage_stock', 'no');
            update_meta($db, $new, '_stock_status', 'instock');
        }

        $msg = 'Product created! ID: ' . $new;
        ttt_log('product_created', ['id' => $new, 'title' => $_POST['title'] ?? '', 'price' => $price, 'stock_qty' => $stock_qty]);

        if (!empty($_FILES['product_image']['tmp_name'])) {
            $img_err = null;
            if (!handle_image_upload($db, $_FILES['product_image'], $new, $img_err)) {
                $msg .= ' (Image upload failed: ' . $img_err . ')';
            }
        }
        $_SESSION['flash_msg'] = $msg;
        header('Location: products.php');
        exit;
    }
}

// After redirect, restore flash message
if (!empty($_SESSION['flash_msg'])) {
    $msg = $_SESSION['flash_msg'];
    unset($_SESSION['flash_msg']);
}

// Handle delete
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $db->query("UPDATE wp_posts SET post_status='trash', post_name=CONCAT(post_name,'-trash-',$del_id) WHERE ID=" . $del_id);
    ttt_log('product_deleted', ['id' => $del_id]);
    $_SESSION['flash_msg'] = 'Product deleted.';
    header('Location: products.php');
    exit;
}

// Handle toggle
if (isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    $c = $db->query("SELECT post_status FROM wp_posts WHERE ID=" . $toggle_id)->fetch_column();
    $new_status = ($c === 'publish' ? 'draft' : 'publish');
    $db->query("UPDATE wp_posts SET post_status='$new_status' WHERE ID=" . $toggle_id);
    ttt_log('product_toggled', ['id' => $toggle_id, 'from' => $c, 'to' => $new_status]);
    $_SESSION['flash_msg'] = 'Product status changed.';
    header('Location: products.php');
    exit;
}

// Handle quick stock update
if (isset($_GET['restock'])) {
    $qty = intval($_GET['restock_qty'] ?? 100);
    $pid = intval($_GET['restock']);
    update_meta($db, $pid, '_manage_stock', 'yes');
    update_meta($db, $pid, '_stock', $qty);
    update_meta($db, $pid, '_stock_status', $qty > 0 ? 'instock' : 'outofstock');
    $_SESSION['flash_msg'] = "Stock updated to $qty.";
    ttt_log('product_restocked', ['id' => $pid, 'qty' => $qty]);
    header('Location: products.php');
    exit;
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

<form method="post" enctype="multipart/form-data" id="pf" class="product-form <?= $edit ? '' : 'hidden' ?>">
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
        <div>
            <label>Product Image</label>
            <input type="file" name="product_image" accept="image/*">
            <?php if ($edit):
                $thumb_id = get_meta($db, $edit['ID'], '_thumbnail_id');
                if ($thumb_id):
                    $thumb_file = get_meta($db, $thumb_id, '_wp_attached_file');
                    if ($thumb_file): ?>
                        <div style="margin-top:8px;"><img src="/wp-content/uploads/<?= htmlspecialchars($thumb_file) ?>" style="max-width:120px;border-radius:8px;"></div>
                    <?php endif;
                endif;
            endif; ?>
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
