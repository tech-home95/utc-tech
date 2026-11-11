<?php
/**
 * Universal Technology Corporation (UTC) - Logout
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

logoutUser();
header("Location: index.php?msg=logged_out");
exit;
