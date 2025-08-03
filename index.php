<?php
$title = 'Pacientes - Melon Mind';
include 'views/layouts/header.php';

session_start();

$route = $_GET['page'] ?? 'panel';

switch ($route) {
    case 'panel':
        require_once __DIR__ . '/views/patients/panel.php';
        break;
    case 'login':
        require_once __DIR__ . '/views/auth/login.php';
        break;
    case 'register':
        require_once __DIR__ . '/views/auth/register.php';
        break;
    default:
        http_response_code(404);
        echo "Página no encontrada";
        break;
}

?>

<body>
    <!-- Sidebar -->
    <?php include 'views/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <h1>Pacientes</h1>
            <div class="header-actions">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Buscar" class="search-input" id="searchInput">
                </div>
                <button class="btn-primary" onclick="openAddPatientModal()">
                    <i class="fas fa-plus"></i>
                    Agregar paciente
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-label">Totales</div>
                <div class="stat-value" id="totalPatients"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Activos</div>
                <div class="stat-value" id="activePatients"><?php echo $stats['active']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">De alta</div>
                <div class="stat-value" id="dischargedPatients"><?php echo $stats['discharged']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Inactivos</div>
                <div class="stat-value" id="inactivePatients"><?php echo $stats['inactive']; ?></div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-container">
            <div class="filter-group">
                <label>Estado:</label>
                <select class="filter-select" id="statusFilter">
                    <option value="Todos">Todos</option>
                    <option value="Activo">Activos</option>
                    <option value="Inactivo">Inactivos</option>
                    <option value="De alta">De alta</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Ordenar por:</label>
                <select class="filter-select" id="orderByFilter">
                    <option value="first_name">Nombre</option>
                    <option value="created_at">Fecha</option>
                    <option value="status">Estado</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Orden:</label>
                <select class="filter-select" id="orderDirFilter">
                    <option value="ASC">Ascendente</option>
                    <option value="DESC">Descendente</option>
                </select>
            </div>
        </div>

        <!-- Patient List -->
        <div class="patient-list" id="patientList">
            <?php foreach ($patients as $patient): ?>
                <div class="patient-item" data-patient-id="<?php echo $patient['id']; ?>" onclick="selectPatient(<?php echo $patient['id']; ?>)">
                    <div class="patient-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="patient-info">
                        <div class="patient-name"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></div>
                        <div class="patient-status <?php echo strtolower($patient['status']); ?>"><?php echo $patient['status']; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="load-more-container">
            <button class="btn-secondary" onclick="loadMorePatients()">Cargar más</button>
        </div>
    </div>

    <!-- Patient Profile Panel -->
    <div class="patient-profile-panel" id="profilePanel" style="display: none;">
        <div class="panel-header">
            <button class="close-panel" onclick="closeProfilePanel()">
                <i class="fas fa-times"></i>
            </button>
            <span>Perfil del paciente</span>
            <div class="panel-actions">
                <button class="btn-icon" onclick="editPatient()" id="editPatientBtn">
                    <i class="fas fa-edit"></i>
                    Editar datos
                </button>
                <button class="btn-primary-small" onclick="scheduleAppointment()" id="scheduleBtn">
                    <i class="fas fa-plus"></i>
                    Agendar cita
                </button>
            </div>
        </div>

        <div class="patient-profile" id="patientProfileContent">
            <!-- El contenido se cargará dinámicamente -->
        </div>
    </div>

    <!-- Modal para agregar paciente -->
    <div id="addPatientModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Agregar Nuevo Paciente</h2>
                <span class="close" onclick="closeAddPatientModal()">&times;</span>
            </div>
            <form id="addPatientForm" onsubmit="submitPatientForm(event)">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Nombre *</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Apellido *</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="phone">Teléfono</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="birth_date">Fecha de Nacimiento</label>
                        <input type="date" id="birth_date" name="birth_date">
                    </div>
                    <div class="form-group">
                        <label for="gender">Género</label>
                        <select id="gender" name="gender">
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Estado</label>
                        <select id="status" name="status">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                            <option value="De alta">De alta</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price_per_session">Precio por Sesión</label>
                        <input type="number" id="price_per_session" name="price_per_session" step="0.01" min="0">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Dirección</label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddPatientModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Paciente</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPatientId = null;

        // Búsqueda en tiempo real
        document.getElementById('searchInput').addEventListener('input', function() {
            filterPatients();
        });

        // Filtros
        document.getElementById('statusFilter').addEventListener('change', filterPatients);
        document.getElementById('orderByFilter').addEventListener('change', filterPatients);
        document.getElementById('orderDirFilter').addEventListener('change', filterPatients);

        function filterPatients() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const orderBy = document.getElementById('orderByFilter').value;
            const orderDir = document.getElementById('orderDirFilter').value;

            const params = new URLSearchParams({
                ajax: '1',
                search: search,
                status: status,
                order_by: orderBy,
                order_dir: orderDir
            });

            fetch(`/patients?${params}`)
                .then(response => response.json())
                .then(data => {
                    updatePatientList(data.patients);
                    updateStats(data.stats);
                })
                .catch(error => console.error('Error:', error));
        }

        function updatePatientList(patients) {
            const patientList = document.getElementById('patientList');
            patientList.innerHTML = '';

            patients.forEach(patient => {
                const patientItem = document.createElement('div');
                patientItem.className = 'patient-item';
                patientItem.setAttribute('data-patient-id', patient.id);
                patientItem.onclick = () => selectPatient(patient.id);
                
                patientItem.innerHTML = `
                    <div class="patient-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="patient-info">
                        <div class="patient-name">${patient.first_name} ${patient.last_name}</div>
                        <div class="patient-status ${patient.status.toLowerCase()}">${patient.status}</div>
                    </div>
                `;
                
                patientList.appendChild(patientItem);
            });
        }

        function updateStats(stats) {
            document.getElementById('totalPatients').textContent = stats.total;
            document.getElementById('activePatients').textContent = stats.active;
            document.getElementById('dischargedPatients').textContent = stats.discharged;
            document.getElementById('inactivePatients').textContent = stats.inactive;
        }

        function selectPatient(patientId) {
            // Remover clase active de todos los pacientes
            document.querySelectorAll('.patient-item').forEach(item => {
                item.classList.remove('active');
            });

            // Agregar clase active al paciente seleccionado
            document.querySelector(`[data-patient-id="${patientId}"]`).classList.add('active');

            currentPatientId = patientId;

            // Cargar perfil del paciente
            fetch(`/patients/${patientId}?ajax=1`)
                .then(response => response.json())
                .then(data => {
                    displayPatientProfile(data);
                    document.getElementById('profilePanel').style.display = 'flex';
                })
                .catch(error => console.error('Error:', error));
        }

        function displayPatientProfile(data) {
            const patient = data.patient;
            const age = data.age;
            
            const profileContent = document.getElementById('patientProfileContent');
            profileContent.innerHTML = `
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="/placeholder.svg?height=80&width=80" alt="${patient.first_name} ${patient.last_name}">
                    </div>
                    <div class="profile-info">
                        <h2>${patient.first_name} ${patient.last_name}</h2>
                        <div class="profile-details">
                            <span>${patient.gender}</span>
                            <span>${age} años</span>
                            <span>${formatDate(patient.birth_date)}</span>
                        </div>
                    </div>
                </div>

                <div class="profile-tabs">
                    <button class="tab-button active" onclick="showTab('datos')">
                        <i class="fas fa-user"></i>
                        Datos
                    </button>
                    <button class="tab-button" onclick="showTab('citas')">
                        <i class="fas fa-calendar"></i>
                        Citas
                    </button>
                    <button class="tab-button" onclick="showTab('documentos')">
                        <i class="fas fa-file-alt"></i>
                        Autorizaciones y consentimientos
                    </button>
                </div>

                <div class="profile-content">
                    <div class="info-section" id="datosTab">
                        <div class="info-row">
                            <label>Estado del paciente</label>
                            <span class="status-badge ${patient.status.toLowerCase()}">${patient.status}</span>
                        </div>
                        <div class="info-row">
                            <label>Precio por cita</label>
                            <span>$${parseFloat(patient.price_per_session).toFixed(2)}</span>
                        </div>
                        <div class="info-row">
                            <label>Contacto</label>
                            <span class="${patient.phone ? '' : 'text-muted'}">${patient.phone || 'No especificado'}</span>
                        </div>
                        <div class="info-row">
                            <label>Email</label>
                            <span class="${patient.email ? '' : 'text-muted'}">${patient.email || 'No especificado'}</span>
                        </div>
                        <div class="info-row">
                            <label>Centro de estudios</label>
                            <span class="${patient.study_center ? '' : 'text-muted'}">${patient.study_center || 'No especificado'}</span>
                        </div>
                        <div class="info-row">
                            <label>Nivel de estudios</label>
                            <span class="${patient.education_level ? '' : 'text-muted'}">${patient.education_level || 'No especificado'}</span>
                        </div>
                        <div class="info-row">
                            <label>Identificación</label>
                            <span class="${patient.identification ? '' : 'text-muted'}">${patient.identification || 'No especificado'}</span>
                        </div>
                        <div class="info-row">
                            <label>Dirección</label>
                            <span class="${patient.address ? '' : 'text-muted'}">${patient.address || 'No especificado'}</span>
                        </div>
                    </div>
                    <div class="info-section" id="citasTab" style="display: none;">
                        <p>Cargando historial de citas...</p>
                    </div>
                    <div class="info-section" id="documentosTab" style="display: none;">
                        <p>No hay documentos disponibles.</p>
                    </div>
                </div>
            `;
        }

        function showTab(tabName) {
            // Remover clase active de todos los tabs
            document.querySelectorAll('.tab-button').forEach(tab => {
                tab.classList.remove('active');
            });

            // Ocultar todo el contenido de tabs
            document.querySelectorAll('[id$="Tab"]').forEach(content => {
                content.style.display = 'none';
            });

            // Mostrar tab seleccionado
            document.querySelector(`[onclick="showTab('${tabName}')"]`).classList.add('active');
            document.getElementById(`${tabName}Tab`).style.display = 'block';

            if (tabName === 'citas' && currentPatientId) {
                loadAppointments(currentPatientId);
            }
        }

        function loadAppointments(patientId) {
            fetch(`/appointments/patient/${patientId}?ajax=1`)
                .then(response => response.json())
                .then(appointments => {
                    const citasTab = document.getElementById('citasTab');
                    if (appointments.length === 0) {
                        citasTab.innerHTML = '<p class="text-muted">No hay citas registradas.</p>';
                        return;
                    }

                    let appointmentsHtml = '<div class="appointments-list">';
                    appointments.forEach(appointment => {
                        appointmentsHtml += `
                            <div class="appointment-item">
                                <div class="appointment-date">${formatDateTime(appointment.appointment_date)}</div>
                                <div class="appointment-status ${appointment.status.toLowerCase()}">${appointment.status}</div>
                                <div class="appointment-notes">${appointment.notes || 'Sin notas'}</div>
                            </div>
                        `;
                    });
                    appointmentsHtml += '</div>';
                    citasTab.innerHTML = appointmentsHtml;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('citasTab').innerHTML = '<p class="text-error">Error al cargar las citas.</p>';
                });
        }

        function closeProfilePanel() {
            document.getElementById('profilePanel').style.display = 'none';
            document.querySelectorAll('.patient-item').forEach(item => {
                item.classList.remove('active');
            });
            currentPatientId = null;
        }

        function openAddPatientModal() {
            document.getElementById('addPatientModal').style.display = 'block';
        }

        function closeAddPatientModal() {
            document.getElementById('addPatientModal').style.display = 'none';
            document.getElementById('addPatientForm').reset();
        }

        function submitPatientForm(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('ajax', '1');

            fetch('/patients/create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeAddPatientModal();
                    filterPatients(); // Recargar la lista
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Error al crear el paciente', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al crear el paciente', 'error');
            });
        }

        function editPatient() {
            if (currentPatientId) {
                window.location.href = `/patients/${currentPatientId}/edit`;
            }
        }

        function scheduleAppointment() {
            if (currentPatientId) {
                showNotification('Abriendo calendario para agendar cita...', 'info');
                // Aquí implementarías la lógica para abrir el modal de citas
            }
        }

        function loadMorePatients() {
            showNotification('No hay más pacientes para cargar', 'info');
        }

        // Utility functions
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function formatDateTime(dateTimeString) {
            const date = new Date(dateTimeString);
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.textContent = message;
            
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 20px;
                border-radius: 6px;
                font-size: 14px;
                z-index: 1000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transform: translateX(100%);
                transition: transform 0.3s ease;
                color: white;
                background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (document.body.contains(notification)) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }

        // Cargar primer paciente al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            const firstPatient = document.querySelector('.patient-item');
            if (firstPatient) {
                const patientId = firstPatient.getAttribute('data-patient-id');
                selectPatient(patientId);
            }
        });
    </script>

    <link rel="stylesheet" href="/assets/css/styles.css">
</body>
</html>

<?php include 'views/layouts/footer.php'; ?>