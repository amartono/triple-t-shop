<?php
/**
 * JSON Audit Logger
 * Writes structured JSON log entries for all application actions.
 * Works standalone (admin panel) and inside WordPress.
 */

if (!defined('TTT_LOG_DIR')) {
    define('TTT_LOG_DIR', dirname(__DIR__) . '/logs');
}

define('TTT_LOG_MAX_SIZE', 5 * 1024 * 1024); // 5 MB rotation

if (!function_exists('ttt_log')) {
    function ttt_log($action, $data = [], $level = 'INFO') {
        static $log_file = null;
        static $seq = 0;

        if ($log_file === null) {
            if (!is_dir(TTT_LOG_DIR)) {
                @mkdir(TTT_LOG_DIR, 0755, true);
            }
            $log_file = TTT_LOG_DIR . '/audit.jsonl';

            if (file_exists($log_file) && filesize($log_file) > TTT_LOG_MAX_SIZE) {
                $rotated = TTT_LOG_DIR . '/audit-' . date('Ymd-His', filemtime($log_file)) . '.jsonl';
                @rename($log_file, $rotated);
            }
        }

        $seq++;

        $entry = [
            'timestamp' => date('c'),
            'seq'       => $seq,
            'level'     => strtoupper($level),
            'action'    => $action,
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'cli'),
            'user_agent'=> $_SERVER['HTTP_USER_AGENT'] ?? '',
            'method'    => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'uri'       => $_SERVER['REQUEST_URI'] ?? '',
            'data'      => $data,
        ];

        if (function_exists('wp_get_current_user') && is_user_logged_in()) {
            $user = wp_get_current_user();
            $entry['wp_user_id'] = $user->ID;
            $entry['wp_user_email'] = $user->user_email;
        }

        if (!empty($_SESSION['admin_logged_in'])) {
            $entry['admin_user'] = true;
        }

        @file_put_contents($log_file, json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);
    }
}
