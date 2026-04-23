<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireAuth();

$db = getDB();

$logs = $db->query(
    'SELECT al.*, CONCAT(t.nombre," ",t.apellido) AS tecnico_nombre
     FROM auditoria_log al
     LEFT JOIN tecnicos t ON al.id_tecnico = t.id_tecnico
     ORDER BY al.fecha DESC LIMIT 200'
)->fetchAll();

$paginaActiva = 'logs';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoría | SIGI — SIN Bolivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1>Registro de Auditoría</h1>
            <p>Historial completo de eventos del sistema — últimas 200 entradas</p>
        </div>
        
    </div>

    <div class="card">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Técnico</th>
                        <th>Acción</th>
                        <th>Tabla</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem;">
                        No hay registros en la bitácora aún.
                    </td></tr>
                <?php else: foreach ($logs as $log):
                    $esPeligro = stripos($log['accion'], 'fallido') !== false
                              || stripos($log['accion'], 'error')   !== false;
                ?>
                    <tr style="<?= $esPeligro ? 'background:rgba(192,57,43,.05)' : '' ?>">
                        <td style="font-family:monospace; font-size:.78rem; white-space:nowrap;">
                            <?= date('d/m/Y H:i:s', strtotime($log['fecha'])) ?>
                        </td>
                        <td><?= htmlspecialchars($log['tecnico_nombre'] ?? 'Sistema') ?></td>
                        <td style="<?= $esPeligro ? 'color:var(--danger);font-weight:600;' : '' ?>">
                            <?= htmlspecialchars($log['accion']) ?>
                        </td>
                        <td style="color:var(--text-muted); font-size:.78rem;">
                            <?= htmlspecialchars($log['tabla_afect'] ?? '—') ?>
                            <?= $log['id_registro'] ? ' #' . $log['id_registro'] : '' ?>
                        </td>
                        <td style="font-family:monospace; font-size:.75rem; color:var(--text-muted);">
                            <?= htmlspecialchars($log['ip_address'] ?? '—') ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="main.js"></script>
</body>
</html>
