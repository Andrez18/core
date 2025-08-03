<?php
$title = 'Calendario - Melon Mind';
include 'views/layouts/header.php';

// Require auth
require_once 'controllers/AuthController.php';
$auth = new AuthController();
$auth->requireAuth();

// Load appointments and patients data
require_once 'models/Appointment.php';
require_once 'models/Patient.php';

$appointmentModel = new Appointment();
$patientModel = new Patient();

$appointments = $appointmentModel->getAll();
$patients = $patientModel->getAll();
?>

<body>
    <!-- Sidebar -->
    <?php include 'views/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <h1>Calendario</h1>
            <div class="header-actions">
                <button class="btn-secondary" onclick="printCalendar()">
                    <i class="fas fa-print"></i>
                    Imprimir
                </button>
                <button class="btn-secondary" onclick="exportCalendar()">
                    <i class="fas fa-download"></i>
                    Exportar
                </button>
                <button class="btn-primary" onclick="openNewAppointmentModal()">
                    <i class="fas fa-plus"></i>
                    Nueva Cita
                </button>
            </div>
        </div>

        <!-- Calendar Controls -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <!-- Navigation -->
                <div style="display: flex; align-items: center; gap: 16px;">
                    <button onclick="previousPeriod()" class="btn-icon" style="background: none; border: 1px solid #dee2e6; border-radius: 6px; padding: 8px; cursor: pointer;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h2 id="currentPeriod" style="margin: 0; color: #212529; font-size: 24px; min-width: 250px; text-align: center;">
                        <?php echo date('F Y'); ?>
                    </h2>
                    <button onclick="nextPeriod()" class="btn-icon" style="background: none; border: 1px solid #dee2e6; border-radius: 6px; padding: 8px; cursor: pointer;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>

                <!-- View Controls -->
                <div style="display: flex; gap: 8px; align-items: center;">
                    <button onclick="goToToday()" class="btn-secondary" style="font-size: 14px; padding: 8px 16px;">
                        Hoy
                    </button>
                    <div style="display: flex; border: 1px solid #dee2e6; border-radius: 6px; overflow: hidden;">
                        <button id="monthViewBtn" onclick="setView('month')" class="view-btn active" style="padding: 8px 16px; border: none; background: #007bff; color: white; cursor: pointer; font-size: 14px;">
                            Mes
                        </button>
                        <button id="weekViewBtn" onclick="setView('week')" class="view-btn" style="padding: 8px 16px; border: none; background: white; color: #495057; cursor: pointer; font-size: 14px;">
                            Semana
                        </button>
                        <button id="dayViewBtn" onclick="setView('day')" class="view-btn" style="padding: 8px 16px; border: none; background: white; color: #495057; cursor: pointer; font-size: 14px;">
                            Día
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 16px;">
                <div style="position: relative; flex: 1; max-width: 300px;">
                    <input type="text" id="searchInput" placeholder="Buscar paciente..." 
                           style="width: 100%; padding: 8px 12px 8px 36px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                    <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                </div>
                
                <select id="statusFilter" onchange="filterAppointments()" style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                    <option value="">Todos los estados</option>
                    <option value="Programada">Programada</option>
                    <option value="Completada">Completada</option>
                    <option value="Cancelada">Cancelada</option>
                    <option value="No asistió">No asistió</option>
                </select>
                
                <select id="patientFilter" onchange="filterAppointments()" style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                    <option value="">Todos los pacientes</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?php echo $patient['id']; ?>">
                            <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button onclick="clearFilters()" class="btn-secondary" style="font-size: 14px; padding: 8px 12px;">
                    <i class="fas fa-times"></i>
                    Limpiar
                </button>
            </div>

            <!-- Legend -->
            <div style="display: flex; gap: 20px; font-size: 14px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #007bff; border-radius: 2px;"></div>
                    <span>Programada</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #28a745; border-radius: 2px;"></div>
                    <span>Completada</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #dc3545; border-radius: 2px;"></div>
                    <span>Cancelada</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #ffc107; border-radius: 2px;"></div>
                    <span>No asistió</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #6f42c1; border-radius: 2px;"></div>
                    <span>Reprogramada</span>
                </div>
            </div>
        </div>

        <!-- Calendar Container -->
        <div id="calendarContainer" style="background: white; border-radius: 8px; border: 1px solid #e9ecef; overflow: hidden;">
            <!-- Month View -->
            <div id="monthView">
                <!-- Calendar Header -->
                <div style="display: grid; grid-template-columns: repeat(7, 1fr); background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                    <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Domingo</div>
                    <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Lunes</div>
                    <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Martes</div>
                    <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Miércoles</div>
                    <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Jueves</div>
                    <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Viernes</div>
                    <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057;">Sábado</div>
                </div>

                <!-- Calendar Body -->
                <div id="monthCalendarBody" style="display: grid; grid-template-columns: repeat(7, 1fr);">
                    <!-- Calendar days will be generated by JavaScript -->
                </div>
            </div>

            <!-- Week View -->
            <div id="weekView" style="display: none;">
                <div style="display: grid; grid-template-columns: 80px repeat(7, 1fr); border-bottom: 1px solid #e9ecef;">
                    <div style="padding: 16px; background: #f8f9fa; border-right: 1px solid #e9ecef; text-align: center; font-weight: 600; color: #495057;">Hora</div>
                    <div id="weekDays" style="display: contents;">
                        <!-- Week days will be generated by JavaScript -->
                    </div>
                </div>
                <div id="weekCalendarBody" style="display: grid; grid-template-columns: 80px repeat(7, 1fr); min-height: 600px; position: relative;">
                    <!-- Week hours and appointments will be generated by JavaScript -->
                </div>
            </div>

            <!-- Day View -->
            <div id="dayView" style="display: none;">
                <div style="display: grid; grid-template-columns: 80px 1fr; border-bottom: 1px solid #e9ecef;">
                    <div style="padding: 16px; background: #f8f9fa; border-right: 1px solid #e9ecef; text-align: center; font-weight: 600; color: #495057;">Hora</div>
                    <div id="dayHeader" style="padding: 16px; background: #f8f9fa; text-align: center; font-weight: 600; color: #495057;">
                        <!-- Day header will be generated by JavaScript -->
                    </div>
                </div>
                <div id="dayCalendarBody" style="display: grid; grid-template-columns: 80px 1fr; min-height: 600px; position: relative;">
                    <!-- Day hours and appointments will be generated by JavaScript -->
                </div>
            </div>
        </div>

        <!-- Calendar Statistics -->
        <div style="margin-top: 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #007bff; margin-bottom: 8px;" id="totalAppointments">0</div>
                <div style="color: #6c757d; font-size: 14px;">Total de Citas</div>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #28a745; margin-bottom: 8px;" id="completedAppointments">0</div>
                <div style="color: #6c757d; font-size: 14px;">Completadas</div>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #ffc107; margin-bottom: 8px;" id="pendingAppointments">0</div>
                <div style="color: #6c757d; font-size: 14px;">Pendientes</div>
            </div>
            <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; text-align: center;">
                <div style="font-size: 32px; font-weight: 700; color: #dc3545; margin-bottom: 8px;" id="cancelledAppointments">0</div>
                <div style="color: #6c757d; font-size: 14px;">Canceladas</div>
            </div>
        </div>
    </div>

    <!-- Appointment Quick View Modal -->
    <div id="appointmentQuickView" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2>Detalles de la Cita</h2>
                <span class="close" onclick="closeQuickView()">&times;</span>
            </div>
            <div id="quickViewContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- New Appointment Modal -->
    <div id="newAppointmentModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2>Nueva Cita</h2>
                <span class="close" onclick="closeNewAppointmentModal()">&times;</span>
            </div>
            <form id="quickAppointmentForm" onsubmit="createQuickAppointment(event)">
                <div style="padding: 20px;">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="quickPatient" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Paciente *</label>
                        <select id="quickPatient" name="patient_id" required style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                            <option value="">Seleccionar paciente</option>
                            <?php foreach ($patients as $patient): ?>
                                <option value="<?php echo $patient['id']; ?>">
                                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label for="quickDate" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Fecha *</label>
                            <input type="date" id="quickDate" name="date" required style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                        </div>
                        <div class="form-group">
                            <label for="quickTime" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Hora *</label>
                            <input type="time" id="quickTime" name="time" required style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                        </div>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="quickDuration" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Duración</label>
                        <select id="quickDuration" name="duration" style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                            <option value="30">30 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="60" selected>60 minutos</option>
                            <option value="90">90 minutos</option>
                            <option value="120">120 minutos</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="quickNotes" style="display: block; margin-bottom: 6px; font-weight: 500; color: #495057;">Notas</label>
                        <textarea id="quickNotes" name="notes" rows="3" placeholder="Notas adicionales sobre la cita..." style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; resize: vertical;"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer" style="padding: 16px 20px; border-top: 1px solid #e9ecef; display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn-secondary" onclick="closeNewAppointmentModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-calendar-plus"></i>
                        Crear Cita
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Day Appointments Modal -->
    <div id="dayAppointmentsModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h2 id="dayModalTitle">Citas del Día</h2>
                <span class="close" onclick="closeDayModal()">&times;</span>
            </div>
            <div id="dayModalContent" style="max-height: 500px; overflow-y: auto;">
                <!-- Day appointments will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 40px; border-radius: 8px; text-align: center;">
            <div class="spinner" style="margin: 0 auto 16px;"></div>
            <p style="margin: 0; color: #495057;">Cargando calendario...</p>
        </div>
    </div>

    <script>
        let currentDate = new Date();
        let currentView = 'month';
        let appointments = <?php echo json_encode($appointments); ?>;
        let patients = <?php echo json_encode($patients); ?>;
        let filteredAppointments = [...appointments];
        let draggedAppointment = null;

        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
            setupEventListeners();
            generateCalendar();
            updateStatistics();
        });

        function initializeCalendar() {
            // Set minimum date for new appointments to today
            document.getElementById('quickDate').min = new Date().toISOString().split('T')[0];
            
            // Set default time to next hour
            const now = new Date();
            const nextHour = new Date(now.getTime() + 60 * 60 * 1000);
            document.getElementById('quickTime').value = nextHour.toTimeString().slice(0, 5);
        }

        function setupEventListeners() {
            // Search functionality
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterAppointments();
                }, 300);
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 'n':
                            e.preventDefault();
                            openNewAppointmentModal();
                            break;
                        case 'ArrowLeft':
                            e.preventDefault();
                            previousPeriod();
                            break;
                        case 'ArrowRight':
                            e.preventDefault();
                            nextPeriod();
                            break;
                        case 't':
                            e.preventDefault();
                            goToToday();
                            break;
                    }
                }
                
                if (e.key === 'Escape') {
                    closeAllModals();
                }
            });

            // Click outside modal to close
            window.addEventListener('click', function(e) {
                if (e.target.classList.contains('modal')) {
                    closeAllModals();
                }
            });
        }

        function setView(view) {
            currentView = view;
            
            // Update button states
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.style.background = 'white';
                btn.style.color = '#495057';
            });
            
            document.getElementById(`${view}ViewBtn`).style.background = '#007bff';
            document.getElementById(`${view}ViewBtn`).style.color = 'white';
            
            // Show/hide views
            document.getElementById('monthView').style.display = view === 'month' ? 'block' : 'none';
            document.getElementById('weekView').style.display = view === 'week' ? 'block' : 'none';
            document.getElementById('dayView').style.display = view === 'day' ? 'block' : 'none';
            
            generateCalendar();
        }

        function generateCalendar() {
            showLoading();
            
            setTimeout(() => {
                switch (currentView) {
                    case 'month':
                        generateMonthView();
                        break;
                    case 'week':
                        generateWeekView();
                        break;
                    case 'day':
                        generateDayView();
                        break;
                }
                updatePeriodDisplay();
                hideLoading();
            }, 100);
        }

        function generateMonthView() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            const calendarBody = document.getElementById('monthCalendarBody');
            calendarBody.innerHTML = '';

            for (let i = 0; i < 42; i++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(startDate.getDate() + i);
                
                const dayAppointments = filteredAppointments.filter(apt => {
                    const aptDate = new Date(apt.appointment_date);
                    return aptDate.toDateString() === cellDate.toDateString();
                });

                const isCurrentMonth = cellDate.getMonth() === month;
                const isToday = cellDate.toDateString() === new Date().toDateString();
                const isPast = cellDate < new Date().setHours(0, 0, 0, 0);

                const cell = document.createElement('div');
                cell.className = 'calendar-cell';
                cell.style.cssText = `
                    min-height: 120px;
                    padding: 8px;
                    border-right: 1px solid #e9ecef;
                    border-bottom: 1px solid #e9ecef;
                    background: ${isCurrentMonth ? 'white' : '#f8f9fa'};
                    cursor: pointer;
                    transition: all 0.2s ease;
                    position: relative;
                `;

                if (isToday) {
                    cell.style.background = '#e3f2fd';
                    cell.style.borderColor = '#2196f3';
                }

                if (isPast && isCurrentMonth) {
                    cell.style.opacity = '0.7';
                }

                // Add drop zone functionality
                cell.addEventListener('dragover', handleDragOver);
                cell.addEventListener('drop', (e) => handleDrop(e, cellDate));
                cell.addEventListener('click', () => openNewAppointmentModal(cellDate));
                
                cell.addEventListener('mouseenter', () => {
                    if (!isToday) cell.style.background = isCurrentMonth ? '#f8f9fa' : '#e9ecef';
                });
                cell.addEventListener('mouseleave', () => {
                    cell.style.background = isCurrentMonth ? (isToday ? '#e3f2fd' : 'white') : '#f8f9fa';
                });

                // Day number
                const dayNumber = document.createElement('div');
                dayNumber.textContent = cellDate.getDate();
                dayNumber.style.cssText = `
                    font-weight: ${isToday ? '700' : '500'};
                    color: ${isCurrentMonth ? (isToday ? '#1976d2' : '#212529') : '#6c757d'};
                    margin-bottom: 4px;
                    font-size: ${isToday ? '16px' : '14px'};
                `;
                cell.appendChild(dayNumber);

                // Appointments
                dayAppointments.slice(0, 3).forEach((apt, index) => {
                    const aptElement = createAppointmentElement(apt, 'month');
                    cell.appendChild(aptElement);
                });

                // More appointments indicator
                if (dayAppointments.length > 3) {
                    const moreElement = document.createElement('div');
                    moreElement.textContent = `+${dayAppointments.length - 3} más`;
                    moreElement.style.cssText = `
                        font-size: 10px;
                        color: #6c757d;
                        font-weight: 500;
                        cursor: pointer;
                        padding: 2px 4px;
                        background: #f8f9fa;
                        border-radius: 2px;
                        margin-top: 2px;
                    `;
                    moreElement.addEventListener('click', (e) => {
                        e.stopPropagation();
                        showDayAppointments(cellDate, dayAppointments);
                    });
                    cell.appendChild(moreElement);
                }

                calendarBody.appendChild(cell);
            }
        }

        function generateWeekView() {
            const weekStart = new Date(currentDate);
            weekStart.setDate(currentDate.getDate() - currentDate.getDay());
            
            // Generate week days header
            const weekDays = document.getElementById('weekDays');
            weekDays.innerHTML = '';
            
            for (let i = 0; i < 7; i++) {
                const day = new Date(weekStart);
                day.setDate(weekStart.getDate() + i);
                
                const isToday = day.toDateString() === new Date().toDateString();
                
                const dayElement = document.createElement('div');
                dayElement.style.cssText = `
                    padding: 16px;
                    text-align: center;
                    font-weight: 600;
                    color: ${isToday ? '#1976d2' : '#495057'};
                    background: ${isToday ? '#e3f2fd' : '#f8f9fa'};
                    border-right: 1px solid #e9ecef;
                `;
                
                dayElement.innerHTML = `
                    <div style="font-size: 12px; text-transform: uppercase; margin-bottom: 4px;">
                        ${day.toLocaleDateString('es-ES', { weekday: 'short' })}
                    </div>
                    <div style="font-size: 18px; font-weight: 700;">
                        ${day.getDate()}
                    </div>
                `;
                
                weekDays.appendChild(dayElement);
            }
            
            // Generate time slots and appointments
            const weekBody = document.getElementById('weekCalendarBody');
            weekBody.innerHTML = '';
            
            // Time column
            const timeColumn = document.createElement('div');
            timeColumn.style.cssText = `
                background: #f8f9fa;
                border-right: 1px solid #e9ecef;
            `;
            
            for (let hour = 7; hour <= 21; hour++) {
                const timeSlot = document.createElement('div');
                timeSlot.style.cssText = `
                    height: 60px;
                    padding: 8px;
                    border-bottom: 1px solid #e9ecef;
                    font-size: 12px;
                    color: #6c757d;
                    text-align: center;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                `;
                timeSlot.textContent = `${hour.toString().padStart(2, '0')}:00`;
                timeColumn.appendChild(timeSlot);
            }
            weekBody.appendChild(timeColumn);
            
            // Day columns
            for (let i = 0; i < 7; i++) {
                const day = new Date(weekStart);
                day.setDate(weekStart.getDate() + i);
                
                const dayColumn = document.createElement('div');
                dayColumn.style.cssText = `
                    border-right: 1px solid #e9ecef;
                    position: relative;
                `;
                
                // Time slots
                for (let hour = 7; hour <= 21; hour++) {
                    const timeSlot = document.createElement('div');
                    timeSlot.style.cssText = `
                        height: 60px;
                        border-bottom: 1px solid #e9ecef;
                        cursor: pointer;
                        transition: background-color 0.2s ease;
                    `;
                    
                    timeSlot.addEventListener('click', () => {
                        const slotDate = new Date(day);
                        slotDate.setHours(hour, 0, 0, 0);
                        openNewAppointmentModal(slotDate);
                    });
                    
                    timeSlot.addEventListener('mouseenter', () => {
                        timeSlot.style.background = '#f8f9fa';
                    });
                    
                    timeSlot.addEventListener('mouseleave', () => {
                        timeSlot.style.background = 'transparent';
                    });
                    
                    dayColumn.appendChild(timeSlot);
                }
                
                // Add appointments for this day
                const dayAppointments = filteredAppointments.filter(apt => {
                    const aptDate = new Date(apt.appointment_date);
                    return aptDate.toDateString() === day.toDateString();
                });
                
                dayAppointments.forEach(apt => {
                    const aptDate = new Date(apt.appointment_date);
                    const hour = aptDate.getHours();
                    const minute = aptDate.getMinutes();
                    
                    if (hour >= 7 && hour <= 21) {
                        const aptElement = document.createElement('div');
                        aptElement.style.cssText = `
                            position: absolute;
                            top: ${(hour - 7) * 60 + (minute / 60) * 60}px;
                            left: 4px;
                            right: 4px;
                            height: ${Math.max((apt.duration / 60) * 60, 30)}px;
                            background: ${getStatusColor(apt.status)};
                            color: white;
                            padding: 4px 8px;
                            border-radius: 4px;
                            font-size: 12px;
                            cursor: pointer;
                            overflow: hidden;
                            z-index: 1;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            transition: transform 0.2s ease;
                        `;
                        
                        aptElement.innerHTML = `
                            <div style="font-weight: 600; margin-bottom: 2px;">${apt.patient_name}</div>
                            <div style="font-size: 10px;">${aptDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}</div>
                        `;
                        
                        aptElement.addEventListener('click', () => showQuickView(apt));
                        aptElement.addEventListener('mouseenter', () => {
                            aptElement.style.transform = 'scale(1.02)';
                        });
                        aptElement.addEventListener('mouseleave', () => {
                            aptElement.style.transform = 'scale(1)';
                        });
                        
                        // Add drag functionality
                        aptElement.draggable = true;
                        aptElement.addEventListener('dragstart', (e) => handleDragStart(e, apt));
                        
                        dayColumn.appendChild(aptElement);
                    }
                });
                
                weekBody.appendChild(dayColumn);
            }
        }

        function generateDayView() {
            const dayHeader = document.getElementById('dayHeader');
            dayHeader.innerHTML = `
                <div style="font-size: 18px; font-weight: 700; margin-bottom: 4px;">
                    ${currentDate.toLocaleDateString('es-ES', { weekday: 'long' })}
                </div>
                <div style="font-size: 14px; color: #6c757d;">
                    ${currentDate.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' })}
                </div>
            `;
            
            const dayBody = document.getElementById('dayCalendarBody');
            dayBody.innerHTML = '';
            
            // Time column
            const timeColumn = document.createElement('div');
            timeColumn.style.cssText = `
                background: #f8f9fa;
                border-right: 1px solid #e9ecef;
            `;
            
            for (let hour = 7; hour <= 21; hour++) {
                const timeSlot = document.createElement('div');
                timeSlot.style.cssText = `
                    height: 80px;
                    padding: 8px;
                    border-bottom: 1px solid #e9ecef;
                    font-size: 14px;
                    color: #6c757d;
                    text-align: center;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                `;
                timeSlot.textContent = `${hour.toString().padStart(2, '0')}:00`;
                timeColumn.appendChild(timeSlot);
            }
            dayBody.appendChild(timeColumn);
            
            // Day column
            const dayColumn = document.createElement('div');
            dayColumn.style.cssText = `
                position: relative;
            `;
            
            // Time slots
            for (let hour = 7; hour <= 21; hour++) {
                const timeSlot = document.createElement('div');
                timeSlot.style.cssText = `
                    height: 80px;
                    border-bottom: 1px solid #e9ecef;
                    cursor: pointer;
                    padding: 8px;
                    transition: background-color 0.2s ease;
                `;
                
                timeSlot.addEventListener('click', () => {
                    const slotDate = new Date(currentDate);
                    slotDate.setHours(hour, 0, 0, 0);
                    openNewAppointmentModal(slotDate);
                });
                
                timeSlot.addEventListener('mouseenter', () => {
                    timeSlot.style.background = '#f8f9fa';
                });
                
                timeSlot.addEventListener('mouseleave', () => {
                    timeSlot.style.background = 'transparent';
                });
                
                dayColumn.appendChild(timeSlot);
            }
            
            // Add appointments for this day
            const dayAppointments = filteredAppointments.filter(apt => {
                const aptDate = new Date(apt.appointment_date);
                return aptDate.toDateString() === currentDate.toDateString();
            });
            
            dayAppointments.forEach(apt => {
                const aptDate = new Date(apt.appointment_date);
                const hour = aptDate.getHours();
                const minute = aptDate.getMinutes();
                
                if (hour >= 7 && hour <= 21) {
                    const aptElement = document.createElement('div');
                    aptElement.style.cssText = `
                        position: absolute;
                        top: ${(hour - 7) * 80 + (minute / 60) * 80}px;
                        left: 8px;
                        right: 8px;
                        height: ${Math.max((apt.duration / 60) * 80, 40)}px;
                        background: ${getStatusColor(apt.status)};
                        color: white;
                        padding: 12px;
                        border-radius: 6px;
                        cursor: pointer;
                        overflow: hidden;
                        z-index: 1;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                        transition: transform 0.2s ease;
                    `;
                    
                    aptElement.innerHTML = `
                        <div style="font-weight: 600; font-size: 16px; margin-bottom: 4px;">${apt.patient_name}</div>
                        <div style="font-size: 14px; margin-bottom: 4px;">${aptDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })} - ${apt.duration} min</div>
                        ${apt.notes ? `<div style="font-size: 12px; opacity: 0.9;">${apt.notes}</div>` : ''}
                    `;
                    
                    aptElement.addEventListener('click', () => showQuickView(apt));
                    aptElement.addEventListener('mouseenter', () => {
                        aptElement.style.transform = 'scale(1.02)';
                    });
                    aptElement.addEventListener('mouseleave', () => {
                        aptElement.style.transform = 'scale(1)';
                    });
                    
                    // Add drag functionality
                    aptElement.draggable = true;
                    aptElement.addEventListener('dragstart', (e) => handleDragStart(e, apt));
                    
                    dayColumn.appendChild(aptElement);
                }
            });
            
            dayBody.appendChild(dayColumn);
        }

        function createAppointmentElement(apt, viewType) {
            const aptElement = document.createElement('div');
            const aptDate = new Date(apt.appointment_date);
            
            aptElement.style.cssText = `
                background: ${getStatusColor(apt.status)};
                color: white;
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 11px;
                margin-bottom: 2px;
                cursor: pointer;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                transition: transform 0.2s ease;
            `;
            
            const time = aptDate.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            aptElement.textContent = `${time} ${apt.patient_name}`;
            
            aptElement.addEventListener('click', (e) => {
                e.stopPropagation();
                showQuickView(apt);
            });
            
            aptElement.addEventListener('mouseenter', () => {
                aptElement.style.transform = 'scale(1.05)';
            });
            
            aptElement.addEventListener('mouseleave', () => {
                aptElement.style.transform = 'scale(1)';
            });
            
            // Add drag functionality for month view
            if (viewType === 'month') {
                aptElement.draggable = true;
                aptElement.addEventListener('dragstart', (e) => handleDragStart(e, apt));
            }
            
            return aptElement;
        }

        function updatePeriodDisplay() {
            const periodElement = document.getElementById('currentPeriod');
            
            switch (currentView) {
                case 'month':
                    periodElement.textContent = currentDate.toLocaleDateString('es-ES', {
                        month: 'long',
                        year: 'numeric'
                    });
                    break;
                case 'week':
                    const weekStart = new Date(currentDate);
                    weekStart.setDate(currentDate.getDate() - currentDate.getDay());
                    const weekEnd = new Date(weekStart);
                    weekEnd.setDate(weekStart.getDate() + 6);
                    
                    if (weekStart.getMonth() === weekEnd.getMonth()) {
                        periodElement.textContent = `${weekStart.getDate()} - ${weekEnd.getDate()} ${weekStart.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })}`;
                    } else {
                        periodElement.textContent = `${weekStart.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })} - ${weekEnd.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' })}`;
                    }
                    break;
                case 'day':
                    periodElement.textContent = currentDate.toLocaleDateString('es-ES', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    break;
            }
        }

        function updateStatistics() {
            const total = filteredAppointments.length;
            const completed = filteredAppointments.filter(apt => apt.status === 'Completada').length;
            const pending = filteredAppointments.filter(apt => apt.status === 'Programada').length;
            const cancelled = filteredAppointments.filter(apt => apt.status === 'Cancelada' || apt.status === 'No asistió').length;
            
            document.getElementById('totalAppointments').textContent = total;
            document.getElementById('completedAppointments').textContent = completed;
            document.getElementById('pendingAppointments').textContent = pending;
            document.getElementById('cancelledAppointments').textContent = cancelled;
        }

        function previousPeriod() {
            switch (currentView) {
                case 'month':
                    currentDate.setMonth(currentDate.getMonth() - 1);
                    break;
                case 'week':
                    currentDate.setDate(currentDate.getDate() - 7);
                    break;
                case 'day':
                    currentDate.setDate(currentDate.getDate() - 1);
                    break;
            }
            generateCalendar();
        }

        function nextPeriod() {
            switch (currentView) {
                case 'month':
                    currentDate.setMonth(currentDate.getMonth() + 1);
                    break;
                case 'week':
                    currentDate.setDate(currentDate.getDate() + 7);
                    break;
                case 'day':
                    currentDate.setDate(currentDate.getDate() + 1);
                    break;
            }
            generateCalendar();
        }

        function goToToday() {
            currentDate = new Date();
            generateCalendar();
        }

        function filterAppointments() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const patientFilter = document.getElementById('patientFilter').value;
            
            filteredAppointments = appointments.filter(apt => {
                const matchesSearch = !searchTerm || 
                    apt.patient_name.toLowerCase().includes(searchTerm) ||
                    (apt.notes && apt.notes.toLowerCase().includes(searchTerm));
                
                const matchesStatus = !statusFilter || apt.status === statusFilter;
                const matchesPatient = !patientFilter || apt.patient_id == patientFilter;
                
                return matchesSearch && matchesStatus && matchesPatient;
            });
            
            generateCalendar();
            updateStatistics();
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('patientFilter').value = '';
            filteredAppointments = [...appointments];
            generateCalendar();
            updateStatistics();
        }

        function getStatusColor(status) {
            const colors = {
                'Programada': '#007bff',
                'Completada': '#28a745',
                'Cancelada': '#dc3545',
                'No asistió': '#ffc107',
                'Reprogramada': '#6f42c1'
            };
            return colors[status] || '#6c757d';
        }

        function showQuickView(appointment) {
            const modal = document.getElementById('appointmentQuickView');
            const content = document.getElementById('quickViewContent');
            
            const aptDate = new Date(appointment.appointment_date);
            const patient = patients.find(p => p.id == appointment.patient_id);
            
            content.innerHTML = `
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin: 0 0 8px 0; color: #212529; font-size: 20px;">${appointment.patient_name}</h3>
                            <div style="color: #6c757d; font-size: 14px;">
                                ${aptDate.toLocaleDateString('es-ES', { 
                                    weekday: 'long', 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric' 
                                })}
                            </div>
                        </div>
                        <span class="status-badge ${appointment.status.toLowerCase()}" style="font-size: 14px;">
                            ${appointment.status}
                        </span>
                    </div>
                    
                    <div style="display: grid; gap: 16px; margin-bottom: 24px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fas fa-clock" style="color: #6c757d; width: 20px;"></i>
                            <span style="font-size: 16px;">${aptDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })} - ${appointment.duration} minutos</span>
                        </div>
                        
                        ${patient && patient.phone ? `
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fas fa-phone" style="color: #6c757d; width: 20px;"></i>
                                <span style="font-size: 16px;">${patient.phone}</span>
                            </div>
                        ` : ''}
                        
                        ${appointment.notes ? `
                            <div style="display: flex; align-items: start; gap: 12px;">
                                <i class="fas fa-sticky-note" style="color: #6c757d; width: 20px; margin-top: 2px;"></i>
                                <span style="font-size: 16px; line-height: 1.5;">${appointment.notes}</span>
                            </div>
                        ` : ''}
                    </div>
                    
                    <div style="display: flex; gap: 12px; justify-content: flex-end;">
                        ${appointment.status === 'Programada' ? `
                            <button onclick="markAsCompleted(${appointment.id})" class="btn-success" style="background: #28a745;">
                                <i class="fas fa-check"></i>
                                Marcar Completada
                            </button>
                        ` : ''}
                        <button onclick="editAppointment(${appointment.id})" class="btn-secondary">
                            <i class="fas fa-edit"></i>
                            Editar
                        </button>
                        <button onclick="viewAppointment(${appointment.id})" class="btn-primary">
                            <i class="fas fa-eye"></i>
                            Ver Detalles
                        </button>
                    </div>
                </div>
            `;
            
            modal.style.display = 'block';
        }

        function closeQuickView() {
            document.getElementById('appointmentQuickView').style.display = 'none';
        }

        function openNewAppointmentModal(date = null) {
            const modal = document.getElementById('newAppointmentModal');
            
            if (date) {
                document.getElementById('quickDate').value = date.toISOString().split('T')[0];
                if (date.getHours() > 0) {
                    document.getElementById('quickTime').value = date.toTimeString().slice(0, 5);
                }
            }
            
            modal.style.display = 'block';
            document.getElementById('quickPatient').focus();
        }

        function closeNewAppointmentModal() {
            document.getElementById('newAppointmentModal').style.display = 'none';
            document.getElementById('quickAppointmentForm').reset();
        }

        function createQuickAppointment(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const date = formData.get('date');
            const time = formData.get('time');
            
            // Combine date and time
            const appointmentDate = `${date} ${time}`;
            formData.set('appointment_date', appointmentDate);
            formData.append('ajax', '1');

            showLoading();

            fetch('/appointments/create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    closeNewAppointmentModal();
                    showNotification(data.message || 'Cita creada exitosamente', 'success');
                    
                    // Add new appointment to local array
                    const newAppointment = {
                        id: data.appointment_id,
                        patient_id: formData.get('patient_id'),
                        patient_name: patients.find(p => p.id == formData.get('patient_id'))?.first_name + ' ' + patients.find(p => p.id == formData.get('patient_id'))?.last_name,
                        appointment_date: appointmentDate,
                        duration: formData.get('duration'),
                        notes: formData.get('notes'),
                        status: 'Programada'
                    };
                    
                    appointments.push(newAppointment);
                    filteredAppointments = [...appointments];
                    generateCalendar();
                    updateStatistics();
                } else {
                    showNotification(data.message || 'Error al crear la cita', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showNotification('Error al crear la cita', 'error');
            });
        }

        function showDayAppointments(date, dayAppointments) {
            const modal = document.getElementById('dayAppointmentsModal');
            const title = document.getElementById('dayModalTitle');
            const content = document.getElementById('dayModalContent');
            
            title.textContent = `Citas del ${date.toLocaleDateString('es-ES', { 
                weekday: 'long', 
                day: 'numeric', 
                month: 'long' 
            })}`;
            
            if (dayAppointments.length === 0) {
                content.innerHTML = `
                    <div style="padding: 40px; text-align: center; color: #6c757d;">
                        <i class="fas fa-calendar" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>No hay citas programadas para este día</p>
                        <button class="btn-primary" onclick="closeDayModal(); openNewAppointmentModal(new Date('${date.toISOString()}'))" style="margin-top: 16px;">
                            <i class="fas fa-plus"></i>
                            Agendar Cita
                        </button>
                    </div>
                `;
            } else {
                let html = '<div style="padding: 20px;">';
                
                dayAppointments.sort((a, b) => new Date(a.appointment_date) - new Date(b.appointment_date))
                    .forEach(apt => {
                        const aptDate = new Date(apt.appointment_date);
                        const time = aptDate.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                        
                        html += `
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid #e9ecef; border-radius: 8px; margin-bottom: 12px; cursor: pointer;" 
                                 onclick="closeDayModal(); showQuickView(${JSON.stringify(apt).replace(/"/g, '&quot;')})">
                                <div style="display: flex; align-items: center; gap: 16px;">
                                    <div style="font-weight: 600; color: #495057; min-width: 60px; font-size: 16px;">
                                        ${time}
                                    </div>
                                    <div>
                                        <div style="font-weight: 500; color: #212529; font-size: 16px; margin-bottom: 4px;">
                                            ${apt.patient_name}
                                        </div>
                                        <div style="font-size: 14px; color: #6c757d;">
                                            ${apt.duration} minutos
                                            ${apt.notes ? ` • ${apt.notes}` : ''}
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span class="status-badge ${apt.status.toLowerCase()}">
                                        ${apt.status}
                                    </span>
                                    <i class="fas fa-chevron-right" style="color: #6c757d;"></i>
                                </div>
                            </div>
                        `;
                    });
                
                html += '</div>';
                content.innerHTML = html;
            }
            
            modal.style.display = 'block';
        }

        function closeDayModal() {
            document.getElementById('dayAppointmentsModal').style.display = 'none';
        }

        function closeAllModals() {
            closeQuickView();
            closeNewAppointmentModal();
            closeDayModal();
        }

        // Drag and Drop functionality
        function handleDragStart(e, appointment) {
            draggedAppointment = appointment;
            e.dataTransfer.effectAllowed = 'move';
            e.target.style.opacity = '0.5';
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            e.currentTarget.style.background = '#e3f2fd';
        }

        function handleDrop(e, newDate) {
            e.preventDefault();
            e.currentTarget.style.background = '';
            
            if (draggedAppointment) {
                const oldDate = new Date(draggedAppointment.appointment_date);
                const newDateTime = new Date(newDate);
                newDateTime.setHours(oldDate.getHours(), oldDate.getMinutes());
                
                if (confirm(`¿Mover la cita de ${draggedAppointment.patient_name} al ${newDateTime.toLocaleDateString('es-ES')} a las ${newDateTime.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}?`)) {
                    moveAppointment(draggedAppointment.id, newDateTime);
                }
                
                draggedAppointment = null;
            }
        }

        function moveAppointment(appointmentId, newDate) {
            showLoading();
            
            fetch(`/appointments/move/${appointmentId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    new_date: newDate.toISOString().slice(0, 19).replace('T', ' ')
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showNotification('Cita movida exitosamente', 'success');
                    
                    // Update local appointment
                    const aptIndex = appointments.findIndex(apt => apt.id == appointmentId);
                    if (aptIndex !== -1) {
                        appointments[aptIndex].appointment_date = newDate.toISOString().slice(0, 19).replace('T', ' ');
                        filteredAppointments = [...appointments];
                        generateCalendar();
                    }
                } else {
                    showNotification(data.message || 'Error al mover la cita', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                showNotification('Error al mover la cita', 'error');
            });
        }

        function markAsCompleted(appointmentId) {
            if (confirm('¿Marcar esta cita como completada?')) {
                showLoading();
                
                fetch(`/appointments/complete/${appointmentId}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    if (data.success) {
                        showNotification('Cita marcada como completada', 'success');
                        
                        // Update local appointment
                        const aptIndex = appointments.findIndex(apt => apt.id == appointmentId);
                        if (aptIndex !== -1) {
                            appointments[aptIndex].status = 'Completada';
                            filteredAppointments = [...appointments];
                            generateCalendar();
                            updateStatistics();
                        }
                        
                        closeQuickView();
                    } else {
                        showNotification(data.message || 'Error al actualizar la cita', 'error');
                    }
                })
                .catch(error => {
                    hideLoading();
                    console.error('Error:', error);
                    showNotification('Error al actualizar la cita', 'error');
                });
            }
        }

        function editAppointment(appointmentId) {
            window.location.href = `/appointments/edit/${appointmentId}`;
        }

        function viewAppointment(appointmentId) {
            window.location.href = `/appointments/view/${appointmentId}`;
        }

        function exportCalendar() {
            const format = prompt('Formato de exportación:\n1. CSV\n2. PDF\n3. iCal\n\nIngrese el número:', '1');
            
            let exportFormat = 'csv';
            switch(format) {
                case '2':
                    exportFormat = 'pdf';
                    break;
                case '3':
                    exportFormat = 'ical';
                    break;
                default:
                    exportFormat = 'csv';
            }
            
            const startDate = new Date(currentDate);
            const endDate = new Date(currentDate);
            
            if (currentView === 'month') {
                startDate.setDate(1);
                endDate.setMonth(endDate.getMonth() + 1, 0);
            } else if (currentView === 'week') {
                startDate.setDate(currentDate.getDate() - currentDate.getDay());
                endDate.setDate(startDate.getDate() + 6);
            }
            
            window.location.href = `/api/appointments/export?format=${exportFormat}&start=${startDate.toISOString().split('T')[0]}&end=${endDate.toISOString().split('T')[0]}`;
        }

        function printCalendar() {
            window.print();
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 16px 20px;
                border-radius: 6px;
                color: white;
                font-weight: 500;
                z-index: 10000;
                animation: slideIn 0.3s ease;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            
            // Set background color based on type
            const colors = {
                'success': '#28a745',
                'error': '#dc3545',
                'warning': '#ffc107',
                'info': '#007bff'
            };
            
            notification.style.background = colors[type] || colors.info;
            notification.textContent = message;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);
        }

        // Add CSS animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
            
            .calendar-cell:hover {
                background: #f8f9fa !important;
            }
            
            .spinner {
                width: 40px;
                height: 40px;
                border: 4px solid #f3f3f3;
                border-top: 4px solid #007bff;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            @media print {
                .header-actions,
                .modal,
                #loadingOverlay {
                    display: none !important;
                }
                
                .main-content {
                    margin-left: 0 !important;
                }
                
                .sidebar {
                    display: none !important;
                }
            }
            
            @media (max-width: 768px) {
                .content-header {
                    flex-direction: column;
                    gap: 16px;
                    align-items: stretch;
                }
                
                .header-actions {
                    justify-content: center;
                }
                
                #monthCalendarBody {
                    grid-template-columns: repeat(7, minmax(0, 1fr));
                }
                
                .calendar-cell {
                    min-height: 80px;
                    font-size: 12px;
                }
                
                #weekCalendarBody,
                #dayCalendarBody {
                    grid-template-columns: 60px 1fr;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

<?php include 'views/layouts/footer.php'; ?>