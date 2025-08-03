<?php
$title = 'Dashboard - Melon Mind';
include 'views/layouts/header.php';

require_once 'models/Patient.php';
require_once 'models/Appointment.php';

$patient = new Patient();
$appointment = new Appointment();

$stats = $patient->getStats();
$upcomingAppointments = $appointment->getUpcoming(5);
?>

<body>
    <!-- Sidebar -->
    <?php include 'views/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="content-header">
            <h1>Dashboard</h1>
            <div class="header-actions">
                <button class="btn-primary" onclick="window.location.href='/patients/create'">
                    <i class="fas fa-plus"></i>
                    Agregar paciente
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-label">Total Pacientes</div>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pacientes Activos</div>
                <div class="stat-value"><?php echo $stats['active']; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Citas Hoy</div>
                <div class="stat-value"><?php echo count($upcomingAppointments); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Ingresos del Mes</div>
                <div class="stat-value">$0.00</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions" style="margin-bottom: 24px;">
            <h2 style="margin-bottom: 16px; color: #212529;">Acciones Rápidas</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div class="action-card" onclick="window.location.href='/patients/create'" style="padding: 20px; background: white; border-radius: 8px; border: 1px solid #e9ecef; cursor: pointer; text-align: center; transition: transform 0.2s ease;">
                    <i class="fas fa-user-plus" style="font-size: 24px; color: #28a745; margin-bottom: 8px;"></i>
                    <h3 style="margin: 0; font-size: 16px; color: #212529;">Nuevo Paciente</h3>
                    <p style="margin: 4px 0 0 0; font-size: 14px; color: #6c757d;">Registrar un nuevo paciente</p>
                </div>
                <div class="action-card" onclick="window.location.href='/appointments/create'" style="padding: 20px; background: white; border-radius: 8px; border: 1px solid #e9ecef; cursor: pointer; text-align: center; transition: transform 0.2s ease;">
                    <i class="fas fa-calendar-plus" style="font-size: 24px; color: #007bff; margin-bottom: 8px;"></i>
                    <h3 style="margin: 0; font-size: 16px; color: #212529;">Agendar Cita</h3>
                    <p style="margin: 4px 0 0 0; font-size: 14px; color: #6c757d;">Programar nueva cita</p>
                </div>
                <div class="action-card" onclick="window.location.href='/patients'" style="padding: 20px; background: white; border-radius: 8px; border: 1px solid #e9ecef; cursor: pointer; text-align: center; transition: transform 0.2s ease;">
                    <i class="fas fa-users" style="font-size: 24px; color: #6f42c1; margin-bottom: 8px;"></i>
                    <h3 style="margin: 0; font-size: 16px; color: #212529;">Ver Pacientes</h3>
                    <p style="margin: 4px 0 0 0; font-size: 14px; color: #6c757d;">Gestionar pacientes</p>
                </div>
                <div class="action-card" onclick="window.location.href='/finances'" style="padding: 20px; background: white; border-radius: 8px; border: 1px solid #e9ecef; cursor: pointer; text-align: center; transition: transform 0.2s ease;">
                    <i class="fas fa-chart-line" style="font-size: 24px; color: #fd7e14; margin-bottom: 8px;"></i>
                    <h3 style="margin: 0; font-size: 16px; color: #212529;">Finanzas</h3>
                    <p style="margin: 4px 0 0 0; font-size: 14px; color: #6c757d;">Ver reportes financieros</p>
                </div>
            </div>
        </div>

        <!-- Upcoming Appointments -->
        <div class="upcoming-appointments">
            <h2 style="margin-bottom: 16px; color: #212529;">Próximas Citas</h2>
            <div style="background: white; border-radius: 8px; border: 1px solid #e9ecef; overflow: hidden;">
                <?php if (empty($upcomingAppointments)): ?>
                    <div style="padding: 40px; text-align: center; color: #6c757d;">
                        <i class="fas fa-calendar" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                        <p>No hay citas programadas</p>
                        <button class="btn-primary" onclick="window.location.href='/appointments/create'" style="margin-top: 16px;">
                            <i class="fas fa-plus"></i>
                            Agendar Primera Cita
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcomingAppointments as $appointment): ?>
                        <div style="padding: 16px 20px; border-bottom: 1px solid #f1f3f4; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 500; color: #212529; margin-bottom: 4px;">
                                    <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>
                                </div>
                                <div style="font-size: 14px; color: #6c757d;">
                                    <?php echo date('d M Y - H:i', strtotime($appointment['appointment_date'])); ?>
                                </div>
                            </div>
                            <div>
                                <span class="status-badge <?php echo strtolower($appointment['status']); ?>">
                                    <?php echo $appointment['status']; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div style="padding: 16px 20px; text-align: center;">
                        <button class="btn-secondary" onclick="window.location.href='/appointments'">
                            Ver Todas las Citas
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Add hover effects to action cards
        document.addEventListener('DOMContentLoaded', function() {
            const actionCards = document.querySelectorAll('.action-card');
            
            actionCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 12px rgba(0,0,0,0.1)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = 'none';
                });
            });
        });
    </script>
</body>

<?php include 'views/layouts/footer.php'; ?>