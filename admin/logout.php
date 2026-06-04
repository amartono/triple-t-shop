<?php
require_once __DIR__ . '/config.php';
ttt_log('admin_logout');
session_destroy();
header('Location: /admin/index.php');
