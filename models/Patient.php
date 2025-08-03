<?php
require_once 'config/database.php';

class Patient {
    private $conn;
    private $table_name = "patients";

    public $id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $birth_date;
    public $gender;
    public $status;
    public $price_per_session;
    public $contact_info;
    public $study_center;
    public $education_level;
    public $identification;
    public $address;
    public $profile_image;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los pacientes
    public function getAll($status = null, $search = null, $orderBy = 'first_name', $orderDir = 'ASC') {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        if ($status && $status !== 'Todos') {
            $query .= " AND status = :status";
        }
        
        if ($search) {
            $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
        }
        
        $query .= " ORDER BY " . $orderBy . " " . $orderDir;
        
        $stmt = $this->conn->prepare($query);
        
        if ($status && $status !== 'Todos') {
            $stmt->bindParam(':status', $status);
        }
        
        if ($search) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search', $searchTerm);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un paciente por ID
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo paciente
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (first_name, last_name, email, phone, birth_date, gender, status, price_per_session, 
                   contact_info, study_center, education_level, identification, address) 
                  VALUES 
                  (:first_name, :last_name, :email, :phone, :birth_date, :gender, :status, :price_per_session,
                   :contact_info, :study_center, :education_level, :identification, :address)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':birth_date', $this->birth_date);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':price_per_session', $this->price_per_session);
        $stmt->bindParam(':contact_info', $this->contact_info);
        $stmt->bindParam(':study_center', $this->study_center);
        $stmt->bindParam(':education_level', $this->education_level);
        $stmt->bindParam(':identification', $this->identification);
        $stmt->bindParam(':address', $this->address);
        
        return $stmt->execute();
    }

    // Actualizar paciente
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET first_name = :first_name, last_name = :last_name, email = :email, 
                      phone = :phone, birth_date = :birth_date, gender = :gender, 
                      status = :status, price_per_session = :price_per_session,
                      contact_info = :contact_info, study_center = :study_center,
                      education_level = :education_level, identification = :identification,
                      address = :address
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':birth_date', $this->birth_date);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':price_per_session', $this->price_per_session);
        $stmt->bindParam(':contact_info', $this->contact_info);
        $stmt->bindParam(':study_center', $this->study_center);
        $stmt->bindParam(':education_level', $this->education_level);
        $stmt->bindParam(':identification', $this->identification);
        $stmt->bindParam(':address', $this->address);
        
        return $stmt->execute();
    }

    // Eliminar paciente
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // Obtener estadísticas
    public function getStats() {
        $stats = [];
        
        // Total de pacientes
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Pacientes activos
        $query = "SELECT COUNT(*) as active FROM " . $this->table_name . " WHERE status = 'Activo'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['active'] = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
        
        // Pacientes de alta
        $query = "SELECT COUNT(*) as discharged FROM " . $this->table_name . " WHERE status = 'De alta'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['discharged'] = $stmt->fetch(PDO::FETCH_ASSOC)['discharged'];
        
        // Pacientes inactivos
        $query = "SELECT COUNT(*) as inactive FROM " . $this->table_name . " WHERE status = 'Inactivo'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $stats['inactive'] = $stmt->fetch(PDO::FETCH_ASSOC)['inactive'];
        
        return $stats;
    }

    // Calcular edad
    public function calculateAge($birthDate) {
        $today = new DateTime();
        $birth = new DateTime($birthDate);
        return $today->diff($birth)->y;
    }

    // Obtener nombre completo
    public function getFullName($patient) {
        return $patient['first_name'] . ' ' . $patient['last_name'];
    }
}
?>