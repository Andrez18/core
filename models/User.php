<?php
require_once 'config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $full_name;
    public $role;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear usuario
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password, full_name, role) 
                  VALUES 
                  (:username, :email, :password, :full_name, :role)";
        
        $stmt = $this->conn->prepare($query);
        
        // Hash password
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':role', $this->role);
        
        return $stmt->execute();
    }

    // Autenticar usuario
    public function authenticate($username, $password) {
        $query = "SELECT id, username, email, password, full_name, role 
                  FROM " . $this->table_name . " 
                  WHERE username = :username OR email = :username";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }

    // Obtener usuario por ID
    public function getById($id) {
        $query = "SELECT id, username, email, full_name, role, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar perfil
    public function updateProfile() {
        $query = "UPDATE " . $this->table_name . " 
                  SET full_name = :full_name, email = :email";
        
        if (!empty($this->password)) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        
        if (!empty($this->password)) {
            $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed_password);
        }
        
        return $stmt->execute();
    }
}
?>