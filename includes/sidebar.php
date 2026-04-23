<?php
// includes/sidebar.php
// Requiere que $paginaActiva esté definida antes de incluir este archivo
// Ej: $paginaActiva = 'dashboard';
if (!isset($paginaActiva)) $paginaActiva = '';
$tec = getTecnicoActual();
?>
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="sidebar-logo">
            <div class="logo-icon">SIN</div>
            <div class="logo-text">
                <strong>SIGI</strong>
                <span>Bolivia</span>
            </div>
        </a>
    </div>

    <div class="sidebar-nav">
        <p class="nav-section-label">Principal</p>
        <ul class="nav-links">
            <li class="<?= $paginaActiva === 'dashboard' ? 'active' : '' ?>">
                <a href="index.php">
                    <!-- Tabler: layout-dashboard -->
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                    <span>Dashboard Global</span>
                </a>
            </li>
            <li class="<?= $paginaActiva === 'tickets' ? 'active' : '' ?>">
                <a href="tickets.php">
                    <!-- Tabler: ticket -->
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l0 2"/><path d="M15 11l0 2"/><path d="M15 17l0 2"/><path d="M5 5h14a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-3a2 2 0 0 0 0 -4v-3a2 2 0 0 1 2 -2"/></svg>
                    <span>Gestión de Tickets</span>
                </a>
            </li>
        </ul>

        <p class="nav-section-label">Gestión</p>
        <ul class="nav-links">
            <li class="<?= $paginaActiva === 'conocimiento' ? 'active' : '' ?>">
                <a href="conocimiento.php">
                    <!-- Tabler: books -->
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1"/><path d="M15 4h.01"/><path d="M19 4h.01"/><path d="M11 8h9"/><path d="M11 16h9"/><path d="M14 4v16"/><path d="M18 4v16"/></svg>
                    <span>Base de Conocimientos</span>
                </a>
            </li>
            <li class="<?= $paginaActiva === 'usuarios' ? 'active' : '' ?>">
                <a href="usuarios.php">
                    <!-- Tabler: users-group -->
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="7" r="4"/><path d="M5.5 21h13a2 2 0 0 0 2-2v-1a5 5 0 0 0-5-5h-7a5 5 0 0 0-5 5v1a2 2 0 0 0 2 2z"/></svg>
                    <span>Técnicos</span>
                </a>
            </li>
        </ul>

        <p class="nav-section-label">Análisis</p>
        <ul class="nav-links">
            <li class="<?= $paginaActiva === 'reportes' ? 'active' : '' ?>">
                <a href="reportes.php">
                    <!-- Tabler: chart-bar -->
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="12" width="4" height="8" rx="1"/><rect x="9" y="8" width="4" height="12" rx="1"/><rect x="15" y="4" width="4" height="16" rx="1"/><line x1="3" y1="21" x2="21" y2="21"/></svg>
                    <span>Reportes</span>
                </a>
            </li>
        </ul>

        <p class="nav-section-label">Sistema</p>
        <ul class="nav-links">
            <li class="<?= $paginaActiva === 'configuracion' ? 'active' : '' ?>">
                <a href="configuracion.php">
                    <!-- Tabler: settings -->
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                    <span>Configuración</span>
                </a>
            </li>
            <li class="<?= $paginaActiva === 'logs' ? 'active' : '' ?>">
                <a href="logs.php">
                    <!-- Tabler: shield-check -->
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3"/><path d="M9 12l2 2l4 -4"/></svg>
                    <span>Auditoría</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <!-- Tabler: logout -->
                    <svg class="nav-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"/><path d="M9 12h12l-3 -3"/><path d="M18 15l3 -3"/></svg>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <strong><?= htmlspecialchars($tec['nombre']) ?></strong><br>
        <?= htmlspecialchars($tec['nivel']) ?>
    </div>
</nav>
