<?php
$title = 'Agendar Cita - Melon Mind';
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
            <h1>Agendar Nueva Cita</h1>
            <div class="header-actions">
                <button class="btn-secondary" onclick="window.location.href='/appointments'">
                    <i class="fas fa-arrow-left"></i>
                    Volver a Citas
                </button>
            </div>
        </div>

        <!-- Appointment Form -->
        <div style="max-width: 800px;">
            <div style="background: white; padding: 32px; border-radius: 12px; border: 1px solid #e9ecef;">
                <form id="appointmentForm" onsubmit="submitAppointmentForm(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="patient_id">Paciente *</label>
                            <select id="patient_id" name="patient_id" required style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                                <option value="">Seleccionar paciente</option>
                                <?php foreach ($patients as $patient): ?>
                                    <option value="<?php echo $patient['id']; ?>">
                                        <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="appointment_date">Fecha y Hora *</label>
                            <input type="datetime-local" id="appointment_date" name="appointment_date" required 
                                   style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;"
                                   min="<?php echo date('Y-m-d\TH:i'); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="duration">Duración (minutos)</label>
                            <select id="duration" name="duration" style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                                <option value="30">30 minutos</option>
                                <option value="45">45 minutos</option>
                                <option value="60" selected>60 minutos</option>
                                <option value="90">90 minutos</option>
                                <option value="120">120 minutos</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select id="status" name="status" style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                                <option value="Programada" selected>Programada</option>
                                <option value="Completada">Completada</option>
                                <option value="Cancelada">Cancelada</option>
                                <option value="No asistió">No asistió</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notas</label>
                        <textarea id="notes" name="notes" rows="4" 
                                  style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px; resize: vertical;"
                                  placeholder="Notas adicionales sobre la cita..."></textarea>
                    </div>

                    <!-- Quick Time Slots -->
                    <div style="margin: 24px 0;">
                        <label style="display: block; margin-bottom: 12px; font-weight: 500; color: #495057;">Horarios Sugeridos</label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 8px;">
                            <button type="button" class="time-slot-btn" onclick="setQuickTime('09:00')" 
                                    style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 4px; background: white; cursor: pointer; font-size: 14px; transition: all 0.2s ease;">
                                09:00
                            </button>
                            <button type="button" class="time-slot-btn" onclick="setQuickTime('10:00')" 
                                    style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 4px; background: white; cursor: pointer; font-size: 14px; transition: all 0.2s ease;">
                                10:00
                            </button>
                            <button type="button" class="time-slot-btn" onclick="setQuickTime('11:00')" 
                                    style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 4px; background: white; cursor: pointer; font-size: 14px; transition: all 0.2s ease;">
                                11:00
                            </button>
                            <button type="button" class="time-slot-btn" onclick="setQuickTime('14:00')" 
                                    style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 4px; background: white; cursor: pointer; font-size: 14px; transition: all 0.2s ease;">
                                14:00
                            </button>
                            <button type="button" class="time-slot-btn" onclick="setQuickTime('15:00')" 
                                    style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 4px; background: white; cursor: pointer; font-size: 14px; transition: all 0.2s ease;">
                                15:00
                            </button>
                            <button type="button" class="time-slot-btn" onclick="setQuickTime('16:00')" 
                                    style="padding: 8px 12px; border: 1px solid #dee2e6; border-radius: 4px; background: white; cursor: pointer; font-size: 14px; transition: all 0.2s ease;">
                                16:00
                            </button>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px;">
                        <button type="button" class="btn-secondary" onclick="window.location.href='/appointments'">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-calendar-plus"></i>
                            Agendar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function setQuickTime(time) {
            const today = new Date();
            const dateStr = today.toISOString().split('T')[0];
            document.getElementById('appointment_date').value = `${dateStr}T${time}`;
            
            // Highlight selected time slot
            document.querySelectorAll('.time-slot-btn').forEach(btn => {
                btn.style.background = 'white';
                btn.style.borderColor = '#dee2e6';
            });
            
            event.target.style.background = '#e3f2fd';
            event.target.style.borderColor = '#2196f3';
        }

        function submitAppointmentForm(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            formData.append('ajax', '1');

            fetch('/appointments/create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        window.location.href = '/appointments';
                    }, 1000);
                } else {
                    showNotification(data.message || 'Error al agendar la cita', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error al agendar la cita', 'error');
            });
        }

        // Add hover effects to time slot buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.time-slot-btn').forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    if (this.style.background !== 'rgb(227, 242, 253)') {
                        this.style.background = '#f8f9fa';
                    }
                });
                
                btn.addEventListener('mouseleave', function() {
                    if (this.style.background !== 'rgb(227, 242, 253)') {
                        this.style.background = 'white';
                    }
                });
            });
        });
    </script>
</body>

<?php include 'views/layouts/footer.php'; ?>