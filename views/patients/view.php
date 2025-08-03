<?php
$title = 'Perfil del Paciente - Melon Mind';
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
            <div style="display: flex; align-items: center; gap: 16px;">
                <button class="btn-icon" onclick="window.history.back()" style="background: none; border: 1px solid #dee2e6; border-radius: 6px; padding: 8px; cursor: pointer;">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div>
                    <h1 style="margin: 0;"><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h1>
                    <p style="margin: 4px 0 0 0; color: #6c757d;">Paciente ID: <?php echo $patient['id']; ?></p>
                </div>
            </div>
            <div class="header-actions">
                <button class="btn-secondary" onclick="exportPatientData()">
                    <i class="fas fa-download"></i>
                    Exportar
                </button>
                <button class="btn-secondary" onclick="window.location.href='/patients/edit/<?php echo $patient['id']; ?>'">
                    <i class="fas fa-edit"></i>
                    Editar
                </button>
                <button class="btn-primary" onclick="window.location.href='/appointments/create?patient_id=<?php echo $patient['id']; ?>'">
                    <i class="fas fa-calendar-plus"></i>
                    Agendar Cita
                </button>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 24px;">
            <!-- Main Content -->
            <div>
                <!-- Patient Info Card -->
                <div style="background: white; border-radius: 12px; border: 1px solid #e9ecef; overflow: hidden; margin-bottom: 24px;">
                    <div style="padding: 32px;">
                        <!-- Patient Header -->
                        <div style="display: flex; align-items: center; margin-bottom: 32px;">
                            <div class="patient-avatar" style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 32px; margin-right: 24px;">
                                <?php echo strtoupper(substr($patient['first_name'], 0, 1) . substr($patient['last_name'], 0, 1)); ?>
                            </div>
                            <div style="flex: 1;">
                                <h2 style="margin: 0 0 8px 0; color: #212529; font-size: 28px;">
                                    <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                </h2>
                                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 8px;">
                                    <span class="status-badge <?php echo strtolower($patient['status']); ?>" style="font-size: 14px;">
                                        <?php echo $patient['status']; ?>
                                    </span>
                                    <?php if ($patient['birth_date']): ?>
                                        <span style="color: #6c757d;">
                                            <?php echo calculateAge($patient['birth_date']); ?> años
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div style="color: #6c757d; font-size: 14px;">
                                    Registrado el <?php echo formatDate($patient['created_at']); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Patient Details Grid -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px;">
                            <!-- Contact Information -->
                            <div>
                                <h4 style="margin: 0 0 16px 0; color: #495057; font-size: 16px; font-weight: 600;">Información de Contacto</h4>
                                <div style="space-y: 12px;">
                                    <?php if ($patient['email']): ?>
                                        <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                            <i class="fas fa-envelope" style="width: 20px; color: #6c757d; margin-right: 12px;"></i>
                                            <div>
                                                <div style="font-size: 14px; color: #6c757d;">Email</div>
                                                <div style="color: #212529;"><?php echo htmlspecialchars($patient['email']); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($patient['phone']): ?>
                                        <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                            <i class="fas fa-phone" style="width: 20px; color: #6c757d; margin-right: 12px;"></i>
                                            <div>
                                                <div style="font-size: 14px; color: #6c757d;">Teléfono</div>
                                                <div style="color: #212529;"><?php echo htmlspecialchars($patient['phone']); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($patient['address']): ?>
                                        <div style="display: flex; align-items: flex-start; margin-bottom: 12px;">
                                            <i class="fas fa-map-marker-alt" style="width: 20px; color: #6c757d; margin-right: 12px; margin-top: 2px;"></i>
                                            <div>
                                                <div style="font-size: 14px; color: #6c757d;">Dirección</div>
                                                <div style="color: #212529;"><?php echo htmlspecialchars($patient['address']); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div>
                                <h4 style="margin: 0 0 16px 0; color: #495057; font-size: 16px; font-weight: 600;">Información Personal</h4>
                                <div style="space-y: 12px;">
                                    <?php if ($patient['birth_date']): ?>
                                        <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                            <i class="fas fa-birthday-cake" style="width: 20px; color: #6c757d; margin-right: 12px;"></i>
                                            <div>
                                                <div style="font-size: 14px; color: #6c757d;">Fecha de Nacimiento</div>
                                                <div style="color: #212529;"><?php echo formatDate($patient['birth_date']); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($patient['gender']): ?>
                                        <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                            <i class="fas fa-user" style="width: 20px; color: #6c757d; margin-right: 12px;"></i>
                                            <div>
                                                <div style="font-size: 14px; color: #6c757d;">Género</div>
                                                <div style="color: #212529;"><?php echo htmlspecialchars($patient['gender']); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($patient['occupation']): ?>
                                        <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                            <i class="fas fa-briefcase" style="width: 20px; color: #6c757d; margin-right: 12px;"></i>
                                            <div>
                                                <div style="font-size: 14px; color: #6c757d;">Ocupación</div>
                                                <div style="color: #212529;"><?php echo htmlspecialchars($patient['occupation']); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Treatment Information -->
                            <div>
                                <h4 style="margin: 0 0 16px 0; color: #495057; font-size: 16px; font-weight: 600;">Información de Tratamiento</h4>
                                <div style="space-y: 12px;">
                                    <?php if ($patient['price_per_session']): ?>
                                        <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                            <i class="fas fa-dollar-sign" style="width: 20px; color: #6c757d; margin-right: 12px;"></i>
                                            <div>
                                                <div style="font-size: 14px; color: #6c757d;">Precio por Sesión</div>
                                                <div style="color: #212529; font-weight: 600;"><?php echo formatCurrency($patient['price_per_session']); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($patient['emergency_contact']): ?>
                                        <div style="display: flex; align-items: center; margin-bottom: 12px;">
                                            <i class="fas fa-phone-alt" style="width: 20px; color: #6c757d; margin-right: 12px;"></i>
                                            <div>
                                                <div style="font-size: 14px; color: #6c757d;">Contacto de Emergencia</div>
                                                <div style="color: #212529;"><?php echo htmlspecialchars($patient['emergency_contact']); ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <?php if ($patient['notes']): ?>
                            <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e9ecef;">
                                <h4 style="margin: 0 0 16px 0; color: #495057; font-size: 16px; font-weight: 600;">Notas</h4>
                                <div style="background: #f8f9fa; padding: 16px; border-radius: 6px; color: #495057; line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($patient['notes'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Appointments History -->
                <div style="background: white; border-radius: 12px; border: 1px solid #e9ecef; overflow: hidden;">
                    <div style="padding: 24px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                        <h3 style="margin: 0; color: #212529;">Historial de Citas</h3>
                        <button class="btn-secondary" onclick="window.location.href='/appointments/create?patient_id=<?php echo $patient['id']; ?>'">
                            <i class="fas fa-plus"></i>
                            Nueva Cita
                        </button>
                    </div>
                    
                    <div id="appointmentsHistory">
                        <?php if (empty($appointments)): ?>
                            <div style="padding: 40px; text-align: center; color: #6c757d;">
                                <i class="fas fa-calendar" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                                <p>No hay citas registradas</p>
                                <button class="btn-primary" onclick="window.location.href='/appointments/create?patient_id=<?php echo $patient['id']; ?>'" style="margin-top: 16px;">
                                    <i class="fas fa-calendar-plus"></i>
                                    Agendar Primera Cita
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($appointments as $appointment): ?>
                                <div style="padding: 20px 24px; border-bottom: 1px solid #f1f3f4; display: flex; justify-content: space-between; align-items: center; cursor: pointer;" 
                                     onclick="viewAppointment(<?php echo $appointment['id']; ?>)">
                                    <div style="display: flex; align-items: center; gap: 16px;">
                                        <div style="text-align: center; min-width: 60px;">
                                            <div style="font-weight: 600; color: #495057; font-size: 16px;">
                                                <?php echo date('d', strtotime($appointment['appointment_date'])); ?>
                                            </div>
                                            <div style="font-size: 12px; color: #6c757d; text-transform: uppercase;">
                                                <?php echo date('M', strtotime($appointment['appointment_date'])); ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div style="font-weight: 500; color: #212529; margin-bottom: 4px;">
                                                <?php echo date('H:i', strtotime($appointment['appointment_date'])); ?> - 
                                                <?php echo $appointment['duration']; ?> minutos
                                            </div>
                                            <?php if ($appointment['notes']): ?>
                                                <div style="font-size: 14px; color: #6c757d;">
                                                    <?php echo htmlspecialchars($appointment['notes']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <span class="status-badge <?php echo strtolower($appointment['status']); ?>">
                                            <?php echo $appointment['status']; ?>
                                        </span>
                                        <button onclick="event.stopPropagation(); editAppointment(<?php echo $appointment['id']; ?>)" 
                                                style="background: none; border: none; color: #6c757d; cursor: pointer; padding: 4px;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Quick Stats -->
                <div style="background: white; border-radius: 12px; border: 1px solid #e9ecef; padding: 24px; margin-bottom: 24px;">
                    <h4 style="margin: 0 0 20px 0; color: #495057; font-size: 16px; font-weight: 600;">Estadísticas</h4>
                    
                    <div style="display: grid; gap: 16px;">
                        <div style="text-align: center; padding: 16px; background: #f8f9fa; border-radius: 8px;">
                            <div style="font-size: 24px; font-weight: 700; color: #007bff; margin-bottom: 4px;">
                                <?php echo count($appointments); ?>
                            </div>
                            <div style="font-size: 14px; color: #6c757d;">Total de Citas</div>
                        </div>
                        
                        <div style="text-align: center; padding: 16px; background: #f8f9fa; border-radius: 8px;">
                            <div style="font-size: 24px; font-weight: 700; color: #28a745; margin-bottom: 4px;">
                                <?php 
                                $completedAppointments = array_filter($appointments, function($apt) {
                                    return $apt['status'] === 'Completada';
                                });
                                echo count($completedAppointments);
                                ?>
                            </div>
                            <div style="font-size: 14px; color: #6c757d;">Citas Completadas</div>
                        </div>
                        
                        <div style="text-align: center; padding: 16px; background: #f8f9fa; border-radius: 8px;">
                            <div style="font-size: 24px; font-weight: 700; color: #17a2b8; margin-bottom: 4px;">
                                <?php 
                                $totalRevenue = 0;
                                foreach ($completedAppointments as $apt) {
                                    $totalRevenue += $patient['price_per_session'] ?? 0;
                                }
                                echo formatCurrency($totalRevenue);
                                ?>
                            </div>
                            <div style="font-size: 14px; color: #6c757d;">Ingresos Generados</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div style="background: white; border-radius: 12px; border: 1px solid #e9ecef; padding: 24px; margin-bottom: 24px;">
                    <h4 style="margin: 0 0 20px 0; color: #495057; font-size: 16px; font-weight: 600;">Acciones Rápidas</h4>
                    
                    <div style="display: grid; gap: 12px;">
                        <button onclick="window.location.href='/appointments/create?patient_id=<?php echo $patient['id']; ?>'" 
                                style="width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fas fa-calendar-plus"></i>
                            Agendar Cita
                        </button>
                        
                        <button onclick="sendEmail()" 
                                style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fas fa-envelope"></i>
                            Enviar Email
                        </button>
                        
                        <button onclick="callPatient()" 
                                style="width: 100%; padding: 12px; background: #17a2b8; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fas fa-phone"></i>
                            Llamar
                        </button>
                        
                        <button onclick="window.location.href='/patients/edit/<?php echo $patient['id']; ?>'" 
                                style="width: 100%; padding: 12px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i class="fas fa-edit"></i>
                            Editar Perfil
                        </button>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div style="background: white; border-radius: 12px; border: 1px solid #e9ecef; padding: 24px;">
                    <h4 style="margin: 0 0 20px 0; color: #495057; font-size: 16px; font-weight: 600;">Actividad Reciente</h4>
                    
                    <div style="display: grid; gap: 12px;">
                        <?php 
                        $recentAppointments = array_slice(array_reverse($appointments), 0, 3);
                        foreach ($recentAppointments as $apt): 
                        ?>
                            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid <?php echo getStatusColor($apt['status']); ?>;">
                                <div style="font-size: 14px; font-weight: 500; color: #212529; margin-bottom: 4px;">
                                    Cita <?php echo strtolower($apt['status']); ?>
                                </div>
                                <div style="font-size: 12px; color: #6c757d;">
                                    <?php echo formatDateTime($apt['appointment_date']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if (empty($recentAppointments)): ?>
                            <div style="text-align: center; color: #6c757d; padding: 20px;">
                                <i class="fas fa-clock" style="font-size: 24px; margin-bottom: 8px; opacity: 0.5;"></i>
                                <p style="margin: 0; font-size: 14px;">No hay actividad reciente</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewAppointment(appointmentId) {
            window.location.href = `/appointments/view/${appointmentId}`;
        }

        function editAppointment(appointmentId) {
            window.location.href = `/appointments/edit/${appointmentId}`;
        }

        function exportPatientData() {
            const patientId = <?php echo $patient['id']; ?>;
            window.location.href = `/api/patients/export/${patientId}`;
        }

        function sendEmail() {
            const email = '<?php echo $patient['email']; ?>';
            if (email) {
                window.location.href = `mailto:${email}`;
            } else {
                showNotification('Este paciente no tiene email registrado', 'error');
            }
        }

        function callPatient() {
            const phone = '<?php echo $patient['phone']; ?>';
            if (phone) {
                window.location.href = `tel:${phone}`;
            } else {
                showNotification('Este paciente no tiene teléfono registrado', 'error');
            }
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
    </script>
</body>

<?php include 'views/layouts/footer.php'; ?>