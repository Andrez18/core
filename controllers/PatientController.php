<?php
require_once 'models/Patient.php';
require_once 'models/Appointment.php';

class PatientController {
    private $patient;
    private $appointment;

    public function __construct() {
        $this->patient = new Patient();
        $this->appointment = new Appointment();
    }

    // Mostrar lista de pacientes
    public function index() {
        $status = $_GET['status'] ?? 'Todos';
        $search = $_GET['search'] ?? '';
        $orderBy = $_GET['order_by'] ?? 'first_name';
        $orderDir = $_GET['order_dir'] ?? 'ASC';
        
        $patients = $this->patient->getAll($status, $search, $orderBy, $orderDir);
        $stats = $this->patient->getStats();
        
        // Si es una petición AJAX, devolver JSON
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'patients' => $patients,
                'stats' => $stats
            ]);
            return;
        }
        
        include 'views/patients/index.php';
    }

    // Mostrar perfil de paciente
    public function show($id) {
        $patient = $this->patient->getById($id);
        $appointments = $this->appointment->getByPatientId($id);
        
        if (!$patient) {
            header('HTTP/1.0 404 Not Found');
            include 'views/errors/404.php';
            return;
        }
        
        // Si es una petición AJAX, devolver JSON
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'patient' => $patient,
                'appointments' => $appointments,
                'age' => $this->patient->calculateAge($patient['birth_date'])
            ]);
            return;
        }
        
        include 'views/patients/show.php';
    }

    // Crear nuevo paciente
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->patient->first_name = $_POST['first_name'] ?? '';
            $this->patient->last_name = $_POST['last_name'] ?? '';
            $this->patient->email = $_POST['email'] ?? '';
            $this->patient->phone = $_POST['phone'] ?? '';
            $this->patient->birth_date = $_POST['birth_date'] ?? '';
            $this->patient->gender = $_POST['gender'] ?? 'Masculino';
            $this->patient->status = $_POST['status'] ?? 'Activo';
            $this->patient->price_per_session = $_POST['price_per_session'] ?? 0.00;
            $this->patient->contact_info = $_POST['contact_info'] ?? '';
            $this->patient->study_center = $_POST['study_center'] ?? '';
            $this->patient->education_level = $_POST['education_level'] ?? '';
            $this->patient->identification = $_POST['identification'] ?? '';
            $this->patient->address = $_POST['address'] ?? '';
            
            if ($this->patient->create()) {
                if (isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Paciente creado exitosamente']);
                    return;
                }
                header('Location: /patients');
                return;
            } else {
                $error = 'Error al crear el paciente';
            }
        }
        
        include 'views/patients/create.php';
    }

    // Actualizar paciente
    public function update($id) {
        $patient = $this->patient->getById($id);
        
        if (!$patient) {
            header('HTTP/1.0 404 Not Found');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->patient->id = $id;
            $this->patient->first_name = $_POST['first_name'] ?? '';
            $this->patient->last_name = $_POST['last_name'] ?? '';
            $this->patient->email = $_POST['email'] ?? '';
            $this->patient->phone = $_POST['phone'] ?? '';
            $this->patient->birth_date = $_POST['birth_date'] ?? '';
            $this->patient->gender = $_POST['gender'] ?? 'Masculino';
            $this->patient->status = $_POST['status'] ?? 'Activo';
            $this->patient->price_per_session = $_POST['price_per_session'] ?? 0.00;
            $this->patient->contact_info = $_POST['contact_info'] ?? '';
            $this->patient->study_center = $_POST['study_center'] ?? '';
            $this->patient->education_level = $_POST['education_level'] ?? '';
            $this->patient->identification = $_POST['identification'] ?? '';
            $this->patient->address = $_POST['address'] ?? '';
            
            if ($this->patient->update()) {
                if (isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Paciente actualizado exitosamente']);
                    return;
                }
                header('Location: /patients/' . $id);
                return;
            } else {
                $error = 'Error al actualizar el paciente';
            }
        }
        
        include 'views/patients/edit.php';
    }

    // Eliminar paciente
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->patient->delete($id)) {
                if (isset($_POST['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Paciente eliminado exitosamente']);
                    return;
                }
                header('Location: /patients');
                return;
            } else {
                $error = 'Error al eliminar el paciente';
            }
        }
    }
}
?>