<?php
/**
 * Authentication and Role-based Access Control for UTC
 */

require_once __DIR__ . '/database.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) {
        return null;
    }
    $stmt = $pdo->prepare("SELECT id, name, email, phone, role, avatar, bio, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

function requireLogin($redirect = 'login.php') {
    if (!isLoggedIn()) {
        // Compute relative path to root login.php
        $loginUrl = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false || 
                     strpos($_SERVER['SCRIPT_NAME'], '/teacher/') !== false || 
                     strpos($_SERVER['SCRIPT_NAME'], '/student/') !== false) ? '../login.php' : 'login.php';
        header("Location: " . $loginUrl . "?msg=login_required");
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (!hasRole($role) && !hasRole('admin')) { // admin can access or debug if needed
        $redirectUrl = '../index.php?error=unauthorized';
        header("Location: " . $redirectUrl);
        exit;
    }
}

function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
}

function logoutUser() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
