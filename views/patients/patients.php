<?php
$title = 'Pacientes - Melon Mind';
include 'views/layouts/header.php';

// Require auth
require_once 'controllers/AuthController.php';
$auth = new AuthController();
$auth->requireAuth();
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
                <button class="btn-secondary" onclick="exportPatients()">
                    <i class="fas fa-download"></i>
                    Exportar
                </button>
                <button class="btn-primary" onclick="window.location.href='/patients/create'">
                    <i class="fas fa-plus"></i>
                    Nuevo Paciente
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: 1fr auto auto; gap: 16px; align-items: center;">
                <div style="position: relative;">
                    <input type="text" id="searchInput" placeholder="Buscar pacientes..." 
                           style="width: 100%; padding: 12px 16px 12px 40px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                    <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                </div>
                <select id="statusFilter" style="padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                    <option value="">Todos los estados</option>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                    <option value="Completado">Completado</option>
                </select>
                <button class="btn-secondary" onclick="clearFilters()">
                    <i class="fas fa-times"></i>
                    Limpiar
                </button>
            </div>
        </div>

        <!-- Patients Grid -->
        <div id="patientsGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php foreach ($patients as $patient): ?>
                <div class="patient-card" style="background: white; border-radius: 12px; border: 1px solid #e9ecef; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer;" 
                     onclick="viewPatient(<?php echo $patient['id']; ?>)">
                    <div style="padding: 24px;">
                        <!-- Patient Header -->
                        <div style="display: flex; align-items: center; margin-bottom: 16px;">
                            <div class="patient-avatar" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 18px; margin-right: 16px;">
                                <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                            </div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 4px 0; color: #212529; font-size: 18px;">
                                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                </h3>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="status-badge <?php echo strtolower($patient['status']); ?>">
                                        <?php echo $patient['status']; ?>
                                    </span>
                                    <?php if ($patient['birth_date']): ?>
                                        <span style="font-size: 14px; color: #6c757d;">
                                            <?php echo calculateAge($patient['birth_date']); ?> años
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="patient-actions" style="opacity: 0; transition: opacity 0.2s ease;">
                                <button onclick="event.stopPropagation(); editPatient(<?php echo $patient['id']; ?>)" 
                                        style="background: none; border: none; color: #6c757d; cursor: pointer; padding: 4px; margin-left: 4px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Patient Info -->
                        <div style="space-y: 8px;">
                            <?php if ($patient['email']): ?>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-envelope" style="width: 16px; color: #6c757d; margin-right: 12px;"></i>
                                    <span style="font-size: 14px; color: #495057;"><?php echo htmlspecialchars($patient['email']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($patient['phone']): ?>
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-phone" style="width: 16px; color: #6c757d; margin-right: 12px;"></i>
                                    <span style="font-size: 14px; color: #495057;"><?php echo htmlspecialchars($patient['phone']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                <i class="fas fa-calendar" style="width: 16px; color: #6c757d; margin-right: 12px;"></i>
                                <span style="font-size: 14px; color: #495057;">
                                    Registrado: <?php echo formatDate($patient['created_at']); ?>
                                </span>
                            </div>

                            <?php if ($patient['price_per_session']): ?>
                                <div style="display: flex; align-items: center;">
                                    <i class="fas fa-dollar-sign" style="width: 16px; color: #6c757d; margin-right: 12px;"></i>
                                    <span style="font-size: 14px; color: #495057;">
                                        <?php echo formatCurrency($patient['price_per_session']); ?> por sesión
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Patient Footer -->
                    <div style="padding: 16px 24px; background: #f8f9fa; border-top: 1px solid #e9ecef;">
                        <div style="display: flex; justify-content: between; align-items: center;">
                            <div style="display: flex; gap: 12px;">
                                <button onclick="event.stopPropagation(); scheduleAppointment(<?php echo $patient['id']; ?>)" 
                                        class="btn-sm btn-primary" style="font-size: 12px; padding: 6px 12px;">
                                    <i class="fas fa-calendar-plus"></i>
                                    Agendar
                                </button>
                                <button onclick="event.stopPropagation(); viewHistory(<?php echo $patient['id']; ?>)" 
                                        class="btn-sm btn-secondary" style="font-size: 12px; padding: 6px 12px;">
                                    <i class="fas fa-history"></i>
                                    Historial
                                </button>
                            </div>
                            <span style="font-size: 12px; color: #6c757d;">
                                ID: <?php echo $patient['id']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty State -->
        <div id="emptyState" style="display: none; text-align: center; padding: 60px 20px; color: #6c757d;">
            <i class="fas fa-users" style="font-size: 64px; margin-bottom: 20px; opacity: 0.3;"></i>
            <h3 style="margin-bottom: 12px; color: #495057;">No se encontraron pacientes</h3>
            <p style="margin-bottom: 24px;">Comienza agregando tu primer paciente al sistema</p>
            <button class="btn-primary" onclick="window.location.href='/patients/create'">
                <i class="fas fa-plus"></i>
                Agregar Primer Paciente
            </button>
        </div>

        <!-- Loading State -->
        <div id="loadingState" style="display: none; text-align: center; padding: 40px;">
            <div class="spinner" style="margin: 0 auto 16px;"></div>
            <p style="color: #6c757d;">Cargando pacientes...</p>
        </div>
    </div>

    <!-- Patient Quick View Modal -->
    <div id="patientModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2 id="modalPatientName">Información del Paciente</h2>
                <span class="close" onclick="closePatientModal()">&times;</span>
            </div>
            <div id="modalPatientContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <script>
        let allPatients = <?php echo json_encode($patients); ?>;
        let filteredPatients = [...allPatients];

        document.addEventListener('DOMContentLoaded', function() {
            setupSearch();
            setupFilters();
            addHoverEffects();
            checkEmptyState();
        });

        function setupSearch() {
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterPatients();
                }, 300);
            });
        }

        function setupFilters() {
            document.getElementById('statusFilter').addEventListener('change', filterPatients);
        }

        function filterPatients() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;

            filteredPatients = allPatients.filter(patient => {
                const matchesSearch = !searchTerm || 
                    patient.first_name.toLowerCase().includes(searchTerm) ||
                    patient.last_name.toLowerCase().includes(searchTerm) ||
                    patient.email.toLowerCase().includes(searchTerm) ||
                    patient.phone.includes(searchTerm);

                const matchesStatus = !statusFilter || patient.status === statusFilter;

                return matchesSearch && matchesStatus;
            });

            renderPatients();
            checkEmptyState();
        }

        function renderPatients() {
            const grid = document.getElementById('patientsGrid');
            
            if (filteredPatients.length === 0) {
                grid.innerHTML = '';
                return;
            }

            grid.innerHTML = filteredPatients.map(patient => `
                <div class="patient-card" style="background: white; border-radius: 12px; border: 1px solid #e9ecef; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: pointer;" 
                     onclick="viewPatient(${patient.id})">
                    <div style="padding: 24px;">
                        <div style="display: flex; align-items: center; margin-bottom: 16px;">
                            <div class="patient-avatar" style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 18px; margin-right: 16px;">
                                ${patient.first_name.charAt(0).toUpperCase()}${patient.last_name.charAt(0).toUpperCase()}
                            </div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 4px 0; color: #212529; font-size: 18px;">
                                    ${patient.first_name} ${patient.last_name}
                                </h3>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="status-badge ${patient.status.toLowerCase()}">
                                        ${patient.status}
                                    </span>
                                    ${patient.birth_date ? `<span style="font-size: 14px; color: #6c757d;">${calculateAge(patient.birth_date)} años</span>` : ''}
                                </div>
                            </div>
                        </div>
                        
                        <div style="space-y: 8px;">
                            ${patient.email ? `
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-envelope" style="width: 16px; color: #6c757d; margin-right: 12px;"></i>
                                    <span style="font-size: 14px; color: #495057;">${patient.email}</span>
                                </div>
                            ` : ''}
                            
                            ${patient.phone ? `
                                <div style="display: flex; align-items: center; margin-bottom: 8px;">
                                    <i class="fas fa-phone" style="width: 16px; color: #6c757d; margin-right: 12px;"></i>
                                    <span style="font-size: 14px; color: #495057;">${patient.phone}</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <div style="padding: 16px 24px; background: #f8f9fa; border-top: 1px solid #e9ecef;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; gap: 12px;">
                                <button onclick="event.stopPropagation(); scheduleAppointment(${patient.id})" 
                                        class="btn-sm btn-primary" style="font-size: 12px; padding: 6px 12px;">
                                    <i class="fas fa-calendar-plus"></i>
                                    Agendar
                                </button>
                                <button onclick="event.stopPropagation(); viewHistory(${patient.id})" 
                                        class="btn-sm btn-secondary" style="font-size: 12px; padding: 6px 12px;">
                                    <i class="fas fa-history"></i>
                                    Historial
                                </button>
                            </div>
                            <span style="font-size: 12px; color: #6c757d;">
                                ID: ${patient.id}
                            </span>
                        </div>
                    </div>
                </div>
            `).join('');

            addHoverEffects();
        }

        function addHoverEffects() {
            document.querySelectorAll('.patient-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-4px)';
                    this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.1)';
                    
                    const actions = this.querySelector('.patient-actions');
                    if (actions) actions.style.opacity = '1';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                    
                    const actions = this.querySelector('.patient-actions');
                    if (actions) actions.style.opacity = '0';
                });
            });
        }

        function checkEmptyState() {
            const grid = document.getElementById('patientsGrid');
            const emptyState = document.getElementById('emptyState');
            
            if (filteredPatients.length === 0) {
                grid.style.display = 'none';
                emptyState.style.display = 'block';
            } else {
                grid.style.display = 'grid';
                emptyState.style.display = 'none';
            }
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            filteredPatients = [...allPatients];
            renderPatients();
            checkEmptyState();
        }

        function viewPatient(patientId) {
            window.location.href = `/patients/view/${patientId}`;
        }

        function editPatient(patientId) {
            window.location.href = `/patients/edit/${patientId}`;
        }

        function scheduleAppointment(patientId) {
            window.location.href = `/appointments/create?patient_id=${patientId}`;
        }

        function viewHistory(patientId) {
            window.location.href = `/patients/history/${patientId}`;
        }

        function exportPatients() {
            const format = confirm('¿Exportar como CSV? (Cancelar para JSON)') ? 'csv' : 'json';
            window.location.href = `/api/export/patients?format=${format}`;
        }

        function calculateAge(birthDate) {
            const today = new Date();
            const birth = new Date(birthDate);
            let age = today.getFullYear() - birth.getFullYear();
            const monthDiff = today.getMonth() - birth.getMonth();
            
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            
            return age;
        }
    </script>
</body>

<?php include 'views/layouts/footer.php'; ?>