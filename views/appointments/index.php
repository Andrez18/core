<?php
$title = 'Citas - Melon Mind';
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
            <h1>Calendario de Citas</h1>
            <div class="header-actions">
                <button class="btn-secondary" onclick="toggleView()">
                    <i class="fas fa-list" id="viewToggleIcon"></i>
                    <span id="viewToggleText">Vista Lista</span>
                </button>
                <button class="btn-primary" onclick="window.location.href='/appointments/create'">
                    <i class="fas fa-plus"></i>
                    Nueva Cita
                </button>
            </div>
        </div>

        <!-- Calendar Navigation -->
        <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <button onclick="previousMonth()" class="btn-icon" style="background: none; border: 1px solid #dee2e6; border-radius: 6px; padding: 8px; cursor: pointer;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h2 id="currentMonth" style="margin: 0; color: #212529; font-size: 24px;">
                        <?php echo date('F Y'); ?>
                    </h2>
                    <button onclick="nextMonth()" class="btn-icon" style="background: none; border: 1px solid #dee2e6; border-radius: 6px; padding: 8px; cursor: pointer;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button onclick="goToToday()" class="btn-secondary" style="font-size: 14px; padding: 8px 16px;">
                        Hoy
                    </button>
                    <select id="statusFilter" onchange="filterAppointments()" style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                        <option value="">Todos los estados</option>
                        <option value="Programada">Programada</option>
                        <option value="Completada">Completada</option>
                        <option value="Cancelada">Cancelada</option>
                        <option value="No asistió">No asistió</option>
                    </select>
                </div>
            </div>

            <!-- Calendar Legend -->
            <div style="display: flex; gap: 20px; font-size: 14px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #28a745; border-radius: 2px;"></div>
                    <span>Completada</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #007bff; border-radius: 2px;"></div>
                    <span>Programada</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #dc3545; border-radius: 2px;"></div>
                    <span>Cancelada</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <div style="width: 12px; height: 12px; background: #ffc107; border-radius: 2px;"></div>
                    <span>No asistió</span>
                </div>
            </div>
        </div>

        <!-- Calendar View -->
        <div id="calendarView" style="background: white; border-radius: 8px; border: 1px solid #e9ecef; overflow: hidden;">
            <!-- Calendar Header -->
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); background: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Dom</div>
                <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Lun</div>
                <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Mar</div>
                <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Mié</div>
                <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Jue</div>
                <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057; border-right: 1px solid #e9ecef;">Vie</div>
                <div style="padding: 16px; text-align: center; font-weight: 600; color: #495057;">Sáb</div>
            </div>

            <!-- Calendar Body -->
            <div id="calendarBody" style="display: grid; grid-template-columns: repeat(7, 1fr);">
                <!-- Calendar days will be generated by JavaScript -->
            </div>
        </div>

        <!-- List View -->
        <div id="listView" style="display: none; background: white; border-radius: 8px; border: 1px solid #e9ecef; overflow: hidden;">
            <div id="appointmentsList">
                <!-- Appointments list will be loaded here -->
            </div>
        </div>

        <!-- Today's Appointments -->
        <div style="margin-top: 24px; background: white; border-radius: 8px; border: 1px solid #e9ecef; overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid #e9ecef;">
                <h3 style="margin: 0; color: #212529;">Citas de Hoy</h3>
            </div>
            <div id="todayAppointments">
                <!-- Today's appointments will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalles de la Cita</h2>
                <span class="close" onclick="closeAppointmentModal()">&times;</span>
            </div>
            <div id="appointmentDetails">
                <!-- Appointment details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        let currentDate = new Date();
        let appointments = [];
        let isCalendarView = true;

        document.addEventListener('DOMContentLoaded', function() {
            loadAppointments();
            generateCalendar();
            loadTodayAppointments();
        });

        function loadAppointments() {
            fetch('/api/appointments/all')
                .then(response => response.json())
                .then(data => {
                    appointments = data.appointments || [];
                    generateCalendar();
                    if (!isCalendarView) {
                        generateListView();
                    }
                })
                .catch(error => console.error('Error loading appointments:', error));
        }

        function generateCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            // Update month display
            document.getElementById('currentMonth').textContent = 
                currentDate.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - firstDay.getDay());

            const calendarBody = document.getElementById('calendarBody');
            calendarBody.innerHTML = '';

            for (let i = 0; i < 42; i++) {
                const cellDate = new Date(startDate);
                cellDate.setDate(startDate.getDate() + i);
                
                const dayAppointments = appointments.filter(apt => {
                    const aptDate = new Date(apt.appointment_date);
                    return aptDate.toDateString() === cellDate.toDateString();
                });

                const isCurrentMonth = cellDate.getMonth() === month;
                const isToday = cellDate.toDateString() === new Date().toDateString();

                const cell = document.createElement('div');
                cell.style.cssText = `
                    min-height: 120px;
                    padding: 8px;
                    border-right: 1px solid #e9ecef;
                    border-bottom: 1px solid #e9ecef;
                    background: ${isCurrentMonth ? 'white' : '#f8f9fa'};
                    cursor: pointer;
                    transition: background-color 0.2s ease;
                `;

                if (isToday) {
                    cell.style.background = '#e3f2fd';
                }

                cell.addEventListener('click', () => openDayModal(cellDate));
                cell.addEventListener('mouseenter', () => {
                    if (!isToday) cell.style.background = '#f8f9fa';
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
                `;
                cell.appendChild(dayNumber);

                // Appointments
                dayAppointments.slice(0, 3).forEach(apt => {
                    const aptElement = document.createElement('div');
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
                    `;
                    
                    const time = new Date(apt.appointment_date).toLocaleTimeString('es-ES', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    aptElement.textContent = `${time} ${apt.patient_name}`;
                    aptElement.addEventListener('click', (e) => {
                        e.stopPropagation();
                        viewAppointment(apt.id);
                    });
                    
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
                    `;
                    cell.appendChild(moreElement);
                }

                calendarBody.appendChild(cell);
            }
        }

        function generateListView() {
            const appointmentsList = document.getElementById('appointmentsList');
            
            if (appointments.length === 0) {
                appointmentsList.innerHTML = `
                    <div style="padding: 40px; text-align: center; color: #6c757d;">
                        <i class="fas fa-calendar" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>No hay citas programadas</p>
                    </div>
                `;
                return;
            }

            // Group appointments by date
            const groupedAppointments = appointments.reduce((groups, apt) => {
                const date = new Date(apt.appointment_date).toDateString();
                if (!groups[date]) groups[date] = [];
                groups[date].push(apt);
                return groups;
            }, {});

            let html = '';
            Object.keys(groupedAppointments).sort().forEach(dateStr => {
                const date = new Date(dateStr);
                const dayAppointments = groupedAppointments[dateStr];
                
                html += `
                    <div style="padding: 20px; border-bottom: 1px solid #e9ecef;">
                        <h4 style="margin: 0 0 16px 0; color: #212529;">
                            ${date.toLocaleDateString('es-ES', { 
                                weekday: 'long', 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric' 
                            })}
                        </h4>
                        <div style="display: grid; gap: 12px;">
                `;
                
                dayAppointments.forEach(apt => {
                    const time = new Date(apt.appointment_date).toLocaleTimeString('es-ES', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    html += `
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f8f9fa; border-radius: 6px; cursor: pointer;" 
                             onclick="viewAppointment(${apt.id})">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="font-weight: 600; color: #495057; min-width: 60px;">
                                    ${time}
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: #212529;">
                                        ${apt.patient_name}
                                    </div>
                                    ${apt.notes ? `<div style="font-size: 14px; color: #6c757d;">${apt.notes}</div>` : ''}
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span class="status-badge ${apt.status.toLowerCase()}">
                                    ${apt.status}
                                </span>
                                <div style="font-size: 14px; color: #6c757d;">
                                    ${apt.duration} min
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            });

            appointmentsList.innerHTML = html;
        }

        function loadTodayAppointments() {
            const today = new Date().toDateString();
            const todayAppointments = appointments.filter(apt => {
                return new Date(apt.appointment_date).toDateString() === today;
            });

            const container = document.getElementById('todayAppointments');
            
            if (todayAppointments.length === 0) {
                container.innerHTML = `
                    <div style="padding: 40px; text-align: center; color: #6c757d;">
                        <i class="fas fa-calendar-check" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>No hay citas programadas para hoy</p>
                        <button class="btn-primary" onclick="window.location.href='/appointments/create'" style="margin-top: 16px;">
                            <i class="fas fa-plus"></i>
                            Agendar Cita
                        </button>
                    </div>
                `;
                return;
            }

            let html = '';
            todayAppointments.sort((a, b) => new Date(a.appointment_date) - new Date(b.appointment_date))
                .forEach(apt => {
                    const time = new Date(apt.appointment_date).toLocaleTimeString('es-ES', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    html += `
                        <div style="padding: 16px 20px; border-bottom: 1px solid #f1f3f4; display: flex; justify-content: space-between; align-items: center; cursor: pointer;" 
                             onclick="viewAppointment(${apt.id})">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div style="font-weight: 600; color: #495057; min-width: 60px;">
                                    ${time}
                                </div>
                                <div>
                                    <div style="font-weight: 500; color: #212529; margin-bottom: 4px;">
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
                                <button onclick="event.stopPropagation(); editAppointment(${apt.id})" 
                                        style="background: none; border: none; color: #6c757d; cursor: pointer; padding: 4px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });

            container.innerHTML = html;
        }

        function getStatusColor(status) {
            const colors = {
                'Programada': '#007bff',
                'Completada': '#28a745',
                'Cancelada': '#dc3545',
                'No asistió': '#ffc107'
            };
            return colors[status] || '#6c757d';
        }

        function toggleView() {
            const calendarView = document.getElementById('calendarView');
            const listView = document.getElementById('listView');
            const toggleIcon = document.getElementById('viewToggleIcon');
            const toggleText = document.getElementById('viewToggleText');

            isCalendarView = !isCalendarView;

            if (isCalendarView) {
                calendarView.style.display = 'block';
                listView.style.display = 'none';
                toggleIcon.className = 'fas fa-list';
                toggleText.textContent = 'Vista Lista';
            } else {
                calendarView.style.display = 'none';
                listView.style.display = 'block';
                toggleIcon.className = 'fas fa-calendar';
                toggleText.textContent = 'Vista Calendario';
                generateListView();
            }
        }

        function previousMonth() {
            currentDate.setMonth(currentDate.getMonth() - 1);
            generateCalendar();
        }

        function nextMonth() {
            currentDate.setMonth(currentDate.getMonth() + 1);
            generateCalendar();
        }

        function goToToday() {
            currentDate = new Date();
            generateCalendar();
        }

        function filterAppointments() {
            const statusFilter = document.getElementById('statusFilter').value;
            // Implement filtering logic
            loadAppointments();
        }

        function openDayModal(date) {
            // Implement day modal functionality
            console.log('Open day modal for:', date);
        }

        function viewAppointment(appointmentId) {
            window.location.href = `/appointments/view/${appointmentId}`;
        }

        function editAppointment(appointmentId) {
            window.location.href = `/appointments/edit/${appointmentId}`;
        }

        function closeAppointmentModal() {
            document.getElementById('appointmentModal').style.display = 'none';
        }
    </script>
</body>

<?php include 'views/layouts/footer.php'; ?>