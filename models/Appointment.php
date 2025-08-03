<?php
require_once 'config/database.php';

class Appointment {
    private $conn;
    private $table_name = "appointments";

    public $id;
    public $patient_id;
    public $appointment_date;
    public $duration;
    public $status;
    public $notes;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener citas por paciente
    public function getByPatientId($patient_id) {
        $query = "SELECT a.*, p.first_name, p.last_name 
                  FROM " . $this->table_name . " a
                  JOIN patients p ON a.patient_id = p.id
                  WHERE a.patient_id = :patient_id
                  ORDER BY a.appointment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear nueva cita
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (patient_id, appointment_date, duration, status, notes) 
                  VALUES 
                  (:patient_id, :appointment_date, :duration, :status, :notes)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':patient_id', $this->patient_id);
        $stmt->bindParam(':appointment_date', $this->appointment_date);
        $stmt->bindParam(':duration', $this->duration);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':notes', $this->notes);
        
        return $stmt->execute();
    }

    // Obtener próximas citas
    public function getUpcoming($limit = 10) {
        $query = "SELECT a.*, p.first_name, p.last_name 
                  FROM " . $this->table_name . " a
                  JOIN patients p ON a.patient_id = p.id
                  WHERE a.appointment_date >= NOW() AND a.status = 'Programada'
                  ORDER BY a.appointment_date ASC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>