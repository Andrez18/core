<?php
require_once 'models/User.php';

class AuthController {
    private $user;

    public function __construct() {
        $this->user = new User();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Mostrar formulario de login
    public function showLogin() {
        if ($this->isLoggedIn()) {
            header('Location: /dashboard');
            return;
        }
        
        include 'views/auth/login.php';
    }

    // Procesar login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error = 'Por favor complete todos los campos';
                include 'views/auth/login.php';
                return;
            }
            
            $user = $this->user->authenticate($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                
                if (isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Login exitoso',
                        'redirect' => '/dashboard'
                    ]);
                    return;
                }
                
                header('Location: /dashboard');
                return;
            } else {
                $error = 'Credenciales incorrectas';
            }
        }
        
        include 'views/auth/login.php';
    }

    // Logout
    public function logout() {
        session_destroy();
        header('Location: /login');
    }

    // Verificar si está logueado
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && $this->checkSessionTimeout();
    }

    // Verificar timeout de sesión
    private function checkSessionTimeout() {
        $timeout = 3600; // 1 hora
        
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity']) > $timeout) {
            session_destroy();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }

    // Middleware de autenticación
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            if (isset($_GET['ajax'])) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'No autorizado']);
                return;
            }
            header('Location: /login');
            exit;
        }
    }

    // Verificar sesión (para AJAX)
    public function checkSession() {
        header('Content-Type: application/json');
        echo json_encode(['valid' => $this->isLoggedIn()]);
    }

    // Mostrar formulario de registro
    public function showRegister() {
        if ($this->isLoggedIn()) {
            header('Location: /dashboard');
            return;
        }
        
        include 'views/auth/register.php';
    }

    // Procesar registro
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->user->username = $_POST['username'] ?? '';
            $this->user->email = $_POST['email'] ?? '';
            $this->user->password = $_POST['password'] ?? '';
            $this->user->full_name = $_POST['full_name'] ?? '';
            $this->user->role = 'therapist';
            
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Validaciones
            if (empty($this->user->username) || empty($this->user->email) || 
                empty($this->user->password) || empty($this->user->full_name)) {
                $error = 'Por favor complete todos los campos';
            } elseif ($this->user->password !== $confirm_password) {
                $error = 'Las contraseñas no coinciden';
            } elseif (strlen($this->user->password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } else {
                if ($this->user->create()) {
                    $success = 'Usuario creado exitosamente. Puede iniciar sesión.';
                } else {
                    $error = 'Error al crear el usuario. El nombre de usuario o email ya existe.';
                }
            }
        }
        
        include 'views/auth/register.php';
    }
}
?>