<?php
// API Endpoints para funcionalidades AJAX
require_once 'config/app.php';
require_once 'middleware/auth.php';
require_once 'utils/helpers.php';

// Obtener la ruta de la API
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remover /api del path
$apiPath = str_replace('/api', '', $path);

// Headers para CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Routing de API
switch ($apiPath) {
    case '/check-session':
        checkSessionAPI();
        break;
        
    case '/patients/search':
        searchPatientsAPI();
        break;
        
    case '/appointments/upcoming':
        getUpcomingAppointmentsAPI();
        break;
        
    case '/stats/dashboard':
        getDashboardStatsAPI();
        break;
        
    case '/export/patients':
        exportPatientsAPI();
        break;
        
    case '/backup/database':
        backupDatabaseAPI();
        break;
        
    default:
        jsonResponse(['error' => 'Endpoint no encontrado'], 404);
        break;
}

function checkSessionAPI() {
    $isValid = isset($_SESSION['user_id']) && 
               isset($_SESSION['last_activity']) && 
               (time() - $_SESSION['last_activity']) < SESSION_TIMEOUT;
    
    if ($isValid) {
        $_SESSION['last_activity'] = time();
    }
    
    jsonResponse(['valid' => $isValid]);
}

function searchPatientsAPI() {
    if (!requireAuth()) return;
    
    $query = $_GET['q'] ?? '';
    $limit = min(intval($_GET['limit'] ?? 10), 50);
    
    require_once 'models/Patient.php';
    $patient = new Patient();
    
    $results = $patient->search($query, $limit);
    
    jsonResponse(['patients' => $results]);
}

function getUpcomingAppointmentsAPI() {
    if (!requireAuth()) return;
    
    $limit = min(intval($_GET['limit'] ?? 5), 20);
    
    require_once 'models/Appointment.php';
    $appointment = new Appointment();
    
    $appointments = $appointment->getUpcoming($limit);
    
    jsonResponse(['appointments' => $appointments]);
}

function getDashboardStatsAPI() {
    if (!requireAuth()) return;
    
    require_once 'models/Patient.php';
    require_once 'models/Appointment.php';
    require_once 'models/Expense.php';
    
    $patient = new Patient();
    $appointment = new Appointment();
    $expense = new Expense();
    
    $stats = [
        'patients' => $patient->getStats(),
        'appointments' => $appointment->getUpcoming(5),
        'finances' => $expense->getFinancialStats()
    ];
    
    jsonResponse(['stats' => $stats]);
}

function exportPatientsAPI() {
    if (!requireAuth()) return;
    
    $format = $_GET['format'] ?? 'csv';
    
    require_once 'models/Patient.php';
    $patient = new Patient();
    
    $patients = $patient->getAll();
    
    if ($format === 'csv') {
        $headers = ['ID', 'Nombre', 'Apellido', 'Email', 'Teléfono', 'Estado', 'Fecha de Registro'];
        
        $data = array_map(function($p) {
            return [
                $p['id'],
                $p['first_name'],
                $p['last_name'],
                $p['email'],
                $p['phone'],
                $p['status'],
                formatDate($p['created_at'])
            ];
        }, $patients);
        
        exportToCSV($data, 'pacientes_' . date('Y-m-d') . '.csv', $headers);
    } else {
        jsonResponse(['patients' => $patients]);
    }
}

function backupDatabaseAPI() {
    if (!requireAuth() || !isAdmin()) {
        jsonResponse(['error' => 'No autorizado'], 403);
        return;
    }
    
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $tables = ['users', 'patients', 'appointments', 'expenses'];
        $backup = "-- Backup de Melon Mind - " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($tables as $table) {
            $backup .= "-- Tabla: $table\n";
            $backup .= "DROP TABLE IF EXISTS `$table`;\n";
            
            // Obtener estructura de la tabla
            $stmt = $conn->query("SHOW CREATE TABLE `$table`");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $backup .= $row['Create Table'] . ";\n\n";
            
            // Obtener datos de la tabla
            $stmt = $conn->query("SELECT * FROM `$table`");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $backup .= "INSERT INTO `$table` VALUES (";
                $values = array_map(function($value) use ($conn) {
                    return $value === null ? 'NULL' : $conn->quote($value);
                }, array_values($row));
                $backup .= implode(', ', $values);
                $backup .= ");\n";
            }
            $backup .= "\n";
        }
        
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="melon_mind_backup_' . date('Y-m-d_H-i-s') . '.sql"');
        echo $backup;
        
    } catch (Exception $e) {
        jsonResponse(['error' => 'Error al crear backup: ' . $e->getMessage()], 500);
    }
}
?>