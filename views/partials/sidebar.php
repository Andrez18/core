<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-brain"></i>
        <span>Melon Mind</span>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="/core/views/patients/panel.php" class="nav-link" data-tooltip="Inicio">
                    <i class="fas fa-home"></i>
                    <span>Inicio</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/patients" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/patients') !== false) ? 'active' : ''; ?>" data-tooltip="Pacientes">
                    <i class="fas fa-users"></i>
                    <span>Pacientes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/appointments" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/appointments') !== false) ? 'active' : ''; ?>" data-tooltip="Citas">
                    <i class="fas fa-clock"></i>
                    <span>Citas</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/calendar" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/calendar') !== false) ? 'active' : ''; ?>" data-tooltip="Calendario">
                    <i class="fas fa-calendar"></i>
                    <span>Calendario</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/finances" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/finances') !== false) ? 'active' : ''; ?>" data-tooltip="Finanzas">
                    <i class="fas fa-chart-bar"></i>
                    <span>Finanzas</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-section">
        <h3 class="section-title">Opciones</h3>
        <ul class="options-list">
            <li class="option-item">
                <a href="/appointments/create" class="option-link" data-tooltip="Agendar cita">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Agendar cita</span>
                </a>
            </li>
            <li class="option-item">
                <a href="/patients/create" class="option-link" data-tooltip="Nuevo paciente">
                    <i class="fas fa-user-plus"></i>
                    <span>Nuevo paciente</span>
                </a>
            </li>
            <li class="option-item">
                <a href="/expenses/create" class="option-link" data-tooltip="Registrar gasto">
                    <i class="fas fa-receipt"></i>
                    <span>Registrar gasto</span>
                </a>
            </li>
            <li class="option-item">
                <a href="/trash" class="option-link" data-tooltip="Abrir papelera">
                    <i class="fas fa-folder-open"></i>
                    <span>Abrir papelera</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-bottom">
        <a href="/upgrade" class="bottom-link pro-link" data-tooltip="Obtener Pro">
            <i class="fas fa-rocket"></i>
            <span>Obtener Pro</span>
        </a>
        <a href="/feedback" class="bottom-link" data-tooltip="Hacer feedback">
            <i class="fas fa-comment"></i>
            <span>Hacer feedback</span>
        </a>
    </div>
</div>