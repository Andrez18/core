<?php
// Middleware de autenticación
function requireAuth() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $timeout = 3600; // 1 hora
    
    // Verificar si está logueado
    if (!isset($_SESSION['user_id'])) {
        redirectToLogin();
        return false;
    }
    
    // Verificar timeout de sesión
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity']) > $timeout) {
        session_destroy();
        redirectToLogin();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

function redirectToLogin() {
    if (isset($_GET['ajax']) || isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado', 'redirect' => '/login']);
        exit;
    }
    
    header('Location: /login');
    exit;
}

function getCurrentUser() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'full_name' => $_SESSION['full_name'],
        'role' => $_SESSION['role']
    ];
}

function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] === $role;
}

function isAdmin() {
    return hasRole('admin');
}
?>