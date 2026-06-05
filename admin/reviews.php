<?php
require_once __DIR__ . '/config.php';
require_admin();

// Handle approve/trash
if (isset($_GET['approve'])) {
    $db->query("UPDATE wp_comments SET comment_approved='1' WHERE comment_ID=".intval($_GET['approve']));
    header('Location: reviews.php');
    exit;
}
if (isset($_GET['trash'])) {
    $db->query("UPDATE wp_comments SET comment_approved='trash' WHERE comment_ID=".intval($_GET['trash']));
    header('Location: reviews.php');
    exit;
}

$reviews = $db->query("SELECT c.comment_ID, c.comment_content, c.comment_date, c.comment_approved, c.comment_author, c.comment_author_email, c.comment_post_ID, p.post_title as product_name FROM wp_comments c JOIN wp_posts p ON p.ID=c.comment_post_ID WHERE c.comment_type='review' OR comment_post_ID IN (SELECT ID FROM wp_posts WHERE post_type='product') ORDER BY c.comment_date DESC LIMIT 50");

admin_header('Reviews');
?>
<h1>Reviews</h1>
<table class="data-table">
    <thead><tr><th>Product</th><th>Author</th><th>Review</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody>
    <?php while ($r = $reviews->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($r['product_name']) ?></td>
            <td><?= htmlspecialchars($r['comment_author']) ?><br><small><?= htmlspecialchars($r['comment_author_email']) ?></small></td>
            <td style="max-width:300px"><?= htmlspecialchars(substr($r['comment_content'], 0, 150)) ?><?= strlen($r['comment_content'])>150?'...':'' ?></td>
            <td><?= date('Y-m-d H:i', strtotime($r['comment_date'])) ?></td>
            <td><span class="badge <?= $r['comment_approved']==='1'?'badge-publish':($r['comment_approved']==='0'?'badge-draft':'') ?>"><?= $r['comment_approved']==='1'?'Approved':($r['comment_approved']==='0'?'Pending':'Trash') ?></span></td>
            <td class="actions">
                <?php if ($r['comment_approved'] === '0'): ?>
                    <a href="?approve=<?= $r['comment_ID'] ?>" class="btn-sm">Approve</a>
                <?php endif; ?>
                <a href="?trash=<?= $r['comment_ID'] ?>" class="btn-sm btn-sm-del" onclick="return confirm('Trash?')">Trash</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php
admin_footer();
