<?php
/**
 * Universal Technology Corporation (UTC) - Helper Functions
 */

require_once __DIR__ . '/database.php';

function formatBDT($amount) {
    return '৳' . number_format((float)$amount, 0);
}

function sanitize($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

function setFlash($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'error', 'info', 'warning'
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

function renderFlash() {
    $flash = getFlash();
    if (!$flash) return '';

    $colors = [
        'success' => 'bg-emerald-500/10 border-emerald-500 text-emerald-600 dark:text-emerald-400',
        'error' => 'bg-rose-500/10 border-rose-500 text-rose-600 dark:text-rose-400',
        'info' => 'bg-blue-500/10 border-blue-500 text-blue-600 dark:text-blue-400',
        'warning' => 'bg-amber-500/10 border-amber-500 text-amber-600 dark:text-amber-400',
    ];

    $classes = $colors[$flash['type']] ?? $colors['info'];
    $icon = match($flash['type']) {
        'success' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
        'error' => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
        default => '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
    };

    return '<div class="p-4 mb-6 rounded-xl border flex items-center ' . $classes . ' shadow-sm animate-fade-in" role="alert">' .
           $icon . '<span>' . sanitize($flash['message']) . '</span>' .
           '<button type="button" onclick="this.parentElement.remove()" class="ml-auto opacity-70 hover:opacity-100">&times;</button></div>';
}

function getSetting($key, $default = '') {
    global $pdo;
    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $res = $stmt->fetch();
    return $res ? $res['setting_value'] : $default;
}

function getAppRoot() {
    $script = $_SERVER['SCRIPT_NAME'];
    // If inside student/, teacher/, admin/
    if (strpos($script, '/student/') !== false || strpos($script, '/teacher/') !== false || strpos($script, '/admin/') !== false) {
        return '../';
    }
    return '';
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}
