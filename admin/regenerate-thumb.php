<?php
/**
 * Regenerates WordPress attachment metadata for a given attachment ID.
 * Called by admin panel via exec() to avoid loading WP in the admin context.
 * Usage: php regenerate-thumb.php <attachment_id>
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('CLI only');
}

$attach_id = intval($argv[1] ?? 0);
if (!$attach_id) die('No attachment ID');

require_once dirname(__DIR__) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$file = get_post_meta($attach_id, '_wp_attached_file', true);
if (!$file) die('No file meta');

$full_path = ABSPATH . 'wp-content/uploads/' . $file;
if (!file_exists($full_path)) die('File not found: ' . $full_path);

$meta = wp_generate_attachment_metadata($attach_id, $full_path);
if (!$meta) die('Failed to generate metadata');

wp_update_attachment_metadata($attach_id, $meta);
echo "OK: attachment $attach_id metadata generated\n";
