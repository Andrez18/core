<?php
// Funciones de ayuda

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    return date($format, strtotime($datetime));
}

function calculateAge($birthDate) {
    $today = new DateTime();
    $birth = new DateTime($birthDate);
    return $today->diff($birth)->y;
}

function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

function uploadFile($file, $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'], $maxSize = MAX_FILE_SIZE) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No se seleccionó ningún archivo'];
    }
    
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileError = $file['error'];
    
    if ($fileError !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error al subir el archivo'];
    }
    
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipo de archivo no permitido'];
    }
    
    if ($fileSize > $maxSize) {
        return ['success' => false, 'message' => 'El archivo es demasiado grande'];
    }
    
    $newFileName = uniqid('', true) . '.' . $fileExt;
    $uploadPath = UPLOAD_PATH . $newFileName;
    
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }
    
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        return ['success' => true, 'filename' => $newFileName, 'path' => $uploadPath];
    } else {
        return ['success' => false, 'message' => 'Error al guardar el archivo'];
    }
}

function sendEmail($to, $subject, $body, $isHTML = true) {
    // Implementación básica de envío de email
    // En producción, usar una librería como PHPMailer o SwiftMailer
    
    $headers = "From: " . APP_NAME . " <noreply@melonmind.com>\r\n";
    $headers .= "Reply-To: noreply@melonmind.com\r\n";
    
    if ($isHTML) {
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    } else {
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    }
    
    return mail($to, $subject, $body, $headers);
}

function logActivity($action, $details = '') {
    $user = getCurrentUser();
    $userId = $user ? $user['id'] : null;
    
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'user_id' => $userId,
        'action' => $action,
        'details' => $details,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    // Guardar en archivo de log
    $logFile = 'logs/activity.log';
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    return preg_match('/^[\+]?[1-9][\d]{0,15}$/', $phone);
}

function generatePDF($html, $filename = 'document.pdf') {
    // Implementación básica para generar PDF
    // En producción, usar una librería como TCPDF o DomPDF
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Por ahora, devolver HTML que se puede imprimir como PDF
    echo $html;
}

function exportToCSV($data, $filename = 'export.csv', $headers = []) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    if (!empty($headers)) {
        fputcsv($output, $headers);
    }
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
}

function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function getMonthName($monthNumber) {
    $months = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    return $months[$monthNumber] ?? '';
}

function getDayName($dayNumber) {
    $days = [
        0 => 'Domingo', 1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles',
        4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado'
    ];
    
    return $days[$dayNumber] ?? '';
}
?>