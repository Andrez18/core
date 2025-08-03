<?php
// Configuración de la aplicación
define('APP_NAME', 'Melon Mind');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost');

// Configuración de base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'melon_mind');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de sesión
define('SESSION_TIMEOUT', 3600); // 1 hora

// Configuración de archivos
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Configuración de email (para futuras implementaciones)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');

// Timezone
date_default_timezone_set('America/Mexico_City');

// Error reporting
if (getenv('APP_ENV') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Iniciar sesión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>