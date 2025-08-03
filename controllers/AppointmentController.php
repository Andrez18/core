<?php
require_once 'models/Appointment.php';
require_once 'models/Patient.php';

class AppointmentController {
    private $appointment;
    private $patient;

    public function __construct() {
        $this->appointment = new Appointment();
        $this->patient = new Patient();
    }

    // Mostrar lista de citas
    public function index() {
        $appointments = $this->appointment->getUpcoming();
        
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['appointments' => $appointments]);
            return;
        }
        
        include 'views/appointments/index.php';
    }

    // Obtener citas por paciente
    public function getByPatient($patientId) {
        $appointments = $this->appointment->getByPatientId($patientId);
        
        header('Content-Type: application/json');
        echo json_encode($appointments);
    }

    // Crear nueva cita
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->appointment->patient_id = $_POST['patient_id'] ?? '';
            $this->appointment->appointment_date = $_POST['appointment_date'] ?? '';
            $this->appointment->duration = $_POST['duration'] ?? 60;
            $this->appointment->status = $_POST['status'] ?? 'Programada';
            $this->appointment->notes = $_POST['notes'] ?? '';
            
            if ($this->appointment->create()) {
                if (isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Cita creada exitosamente']);
                    return;
                }
                header('Location: /appointments');
                return;
            } else {
                $error = 'Error al crear la cita';
            }
        }
        
        $patients = $this->patient->getAll();
        include 'views/appointments/create.php';
    }
}
?>