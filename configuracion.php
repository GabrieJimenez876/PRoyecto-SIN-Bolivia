<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireAuth();

$db = getDB();

// Últimos 5 logs para el resumen de configuración
$recentLogs = $db->query(
    'SELECT al.fecha, al.accion, al.ip_address, CONCAT(t.nombre," ",t.apellido) AS tec
     FROM auditoria_log al
     LEFT JOIN tecnicos t ON al.id_tecnico = t.id_tecnico
     ORDER BY al.fecha DESC LIMIT 5'
)->fetchAll();

$paginaActiva = 'configuracion';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración | SIGI — SIN Bolivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1>Configuración del Sistema</h1>
            <p>Parámetros de seguridad, notificaciones y preferencias del SIGI</p>
        </div>
        <button class="btn-theme" onclick="toggleTheme()">&#9680; Tema</button>
    </div>

    <div class="config-grid">
        <section>
            <!-- Seguridad -->
            <div class="card">
                <div class="card-header"><h2 class="card-title">Seguridad y Acceso</h2></div>
                <div class="card-body">
                    <div class="setting-item">
                        <div>
                            <strong>Autenticación de Dos Pasos (2FA)</strong>
                            <p>Obligatorio para técnicos Nivel 2 y superiores.</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <div>
                            <strong>Bloqueo por Intentos Fallidos</strong>
                            <p>Máximo 3 intentos antes de bloquear la cuenta.</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <div>
                            <strong>Registro de Auditoría Activo</strong>
                            <p>Guarda todas las acciones en la bitácora.</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notificaciones -->
            <div class="card">
                <div class="card-header"><h2 class="card-title">Notificaciones</h2></div>
                <div class="card-body">
                    <div class="setting-item">
                        <span>Alertas de SLA vencido por Email</span>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <span>Notificaciones de tickets críticos</span>
                        <label class="switch">
                            <input type="checkbox" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="setting-item">
                        <span>Resumen diario automático</span>
                        <label class="switch">
                            <input type="checkbox">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Personalización -->
            <div class="card">
                <div class="card-header"><h2 class="card-title">Apariencia e Idioma</h2></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Tema del Sistema</label>
                        <div style="display:flex; gap:.6rem; margin-top:.3rem;">
                            <button class="btn btn-outline btn-sm" onclick="toggleTheme()" style="display:flex;align-items:center;gap:.4rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                                Cambiar Claro / Oscuro
                            </button>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:1rem;">
                        <label class="form-label">Idioma de la Interfaz</label>
                        <p style="font-size:.75rem;color:var(--text-muted);margin-bottom:.5rem;">
                            Módulo de integración con Inglés — cambia los textos del sistema.
                        </p>
                        <div class="lang-toggle">
                            <button class="lang-btn active" data-lang="es" onclick="setLang('es')">
                                🇧🇴 Español
                            </button>
                            <button class="lang-btn" data-lang="en" onclick="setLang('en')">
                                🇬🇧 English
                            </button>
                        </div>
                        <p style="font-size:.73rem;color:var(--text-muted);margin-top:.5rem;">
                            En modo English, los términos técnicos en la Base de Conocimientos muestran sus nombres originales en inglés con tooltip en español.
                        </p>
                    </div>
                    <div class="form-group" style="margin-top:1rem;">
                        <label class="form-label">Color Principal del Sistema</label>
                        <input type="color" id="primaryColorPicker" value="#003366"
                               style="width:80px;height:40px;padding:2px;border-radius:6px;cursor:pointer;">
                    </div>
                </div>
            </div>
        </section>

        <section>
            <!-- Logs recientes -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Auditoría Reciente</h2>
                    <a href="logs.php" class="btn btn-outline btn-sm">Ver todo</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentLogs)): ?>
                        <p class="text-muted">Sin registros recientes.</p>
                    <?php else: foreach ($recentLogs as $log):
                        $esPeligro = stripos($log['accion'], 'fallido') !== false;
                    ?>
                    <div class="log-entry <?= $esPeligro ? 'danger' : '' ?>">
                        [<?= date('d/m H:i', strtotime($log['fecha'])) ?>]
                        <?= htmlspecialchars($log['tec'] ?? 'Sistema') ?> —
                        <?= htmlspecialchars($log['accion']) ?>
                        <?= $log['ip_address'] ? ' (' . $log['ip_address'] . ')' : '' ?>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- Zona de peligro -->
            <div class="card" style="border-top: 4px solid var(--danger);">
                <div class="card-header">
                    <h2 class="card-title" style="color:var(--danger);">&#9888; Zona de Peligro</h2>
                </div>
                <div class="card-body">
                    <p style="font-size:.82rem; color:var(--text-muted); margin-bottom:1rem;">
                        Las siguientes acciones son irreversibles. Proceder con precaución.
                    </p>
                    <button class="btn btn-danger btn-sm" onclick="if(confirm('¿Confirma limpiar el caché de incidentes resueltos?')) alert('Caché limpiada correctamente.')">
                        Limpiar caché de incidentes resueltos
                    </button>
                </div>
            </div>
        </section>
    </div>
</main>

<script src="main.js"></script>
</body>
</html>
