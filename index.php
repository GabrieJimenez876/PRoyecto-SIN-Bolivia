<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireAuth();

$db = getDB();

// KPIs desde la vista
$kpis = $db->query('SELECT * FROM v_kpis_dashboard')->fetch();

// Incidentes recientes (últimos 10)
$recientes = $db->query(
    'SELECT i.codigo, i.severidad, i.estado, i.prioridad, i.fecha_registro,
            COALESCE(c.razon_social, "Interno") AS origen_nombre,
            ti.nombre AS tipo_nombre,
            d.nombre  AS distrital
     FROM incidentes i
     JOIN tipos_incidente ti ON i.id_tipo      = ti.id_tipo
     JOIN distritales      d  ON i.id_distrital = d.id_distrital
     LEFT JOIN contribuyentes c ON i.id_contribuyente = c.id_contribuyente
     ORDER BY i.fecha_registro DESC LIMIT 10'
)->fetchAll();

// Datos para gráfico (por estado)
$estados = $db->query(
    'SELECT estado, COUNT(*) AS total FROM incidentes GROUP BY estado'
)->fetchAll(PDO::FETCH_KEY_PAIR);

// Incidentes por módulo (últimos 30 días)
$porModulo = $db->query(
    'SELECT m.nombre, COUNT(*) AS total
     FROM incidentes i
     JOIN modulos_sin m ON i.id_modulo = m.id_modulo
     WHERE i.fecha_registro >= DATE_SUB(NOW(), INTERVAL 30 DAY)
       AND i.id_modulo IS NOT NULL
     GROUP BY m.id_modulo
     ORDER BY total DESC LIMIT 6'
)->fetchAll();

$paginaActiva = 'dashboard';
include 'includes/sidebar.php';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SIGI — SIN Bolivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1>Dashboard Global</h1>
            <p>Resumen operativo del sistema de incidentes — <?= date('d/m/Y') ?></p>
        </div>
        <div class="header-actions">
            <a href="tickets.php?accion=nuevo" class="btn btn-primary">+ Nuevo Ticket</a>
            
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Tickets Abiertos</div>
            <div class="stat-value"><?= (int)($kpis['abiertos'] ?? 0) ?></div>
            <div class="stat-sub">Requieren atención</div>
        </div>
        <div class="stat-card green">
            <div class="stat-label">Resueltos Hoy</div>
            <div class="stat-value"><?= (int)($kpis['resueltos_hoy'] ?? 0) ?></div>
            <div class="stat-sub">En las últimas 24h</div>
        </div>
        <div class="stat-card yellow">
            <div class="stat-label">MTTR Promedio</div>
            <div class="stat-value"><?= number_format($kpis['mttr_promedio_horas'] ?? 0, 1) ?>h</div>
            <div class="stat-sub">Tiempo medio de resolución</div>
        </div>
        <div class="stat-card red">
            <div class="stat-label">Críticos Activos</div>
            <div class="stat-value"><?= (int)($kpis['criticos_activos'] ?? 0) ?></div>
            <div class="stat-sub">Requieren atención urgente</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-label">Total Registrados</div>
            <div class="stat-value"><?= (int)($kpis['total_incidentes'] ?? 0) ?></div>
            <div class="stat-sub">Histórico del sistema</div>
        </div>
    </div>

    <div class="content-grid">
        <!-- Formulario nuevo incidente -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Registrar Incidente</h2>
                </div>
                <div class="card-body">
                    <form id="formIncidente" method="POST" action="api/nuevo_incidente.php">
                        <div class="form-group">
                            <label class="form-label">Módulo Afectado</label>
                            <select name="id_modulo">
                                <?php
                                $modulos = $db->query('SELECT id_modulo, nombre FROM modulos_sin')->fetchAll();
                                foreach ($modulos as $m):
                                ?>
                                <option value="<?= $m['id_modulo'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tipo de Incidente</label>
                            <select name="id_tipo" required>
                                <?php
                                $tipos = $db->query('SELECT id_tipo, nombre, categoria FROM tipos_incidente ORDER BY categoria')->fetchAll();
                                $catActual = '';
                                foreach ($tipos as $t):
                                    if ($t['categoria'] !== $catActual):
                                        if ($catActual) echo '</optgroup>';
                                        echo '<optgroup label="' . htmlspecialchars($t['categoria']) . '">';
                                        $catActual = $t['categoria'];
                                    endif;
                                ?>
                                <option value="<?= $t['id_tipo'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                                <?php endforeach; if ($catActual) echo '</optgroup>'; ?>
                            </select>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:.8rem;">
                            <div class="form-group">
                                <label class="form-label">Severidad</label>
                                <select name="severidad" required>
                                    <option value="Bajo">Bajo</option>
                                    <option value="Medio" selected>Medio</option>
                                    <option value="Alto">Alto</option>
                                    <option value="Critico">Crítico</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Distrital</label>
                                <select name="id_distrital" required>
                                    <?php
                                    $distritales = $db->query(
                                        'SELECT d.id_distrital, d.nombre, dep.nombre AS dep
                                         FROM distritales d JOIN departamentos dep ON d.id_departamento = dep.id_departamento
                                         ORDER BY dep.nombre, d.nombre'
                                    )->fetchAll();
                                    $depActual = '';
                                    foreach ($distritales as $dist):
                                        if ($dist['dep'] !== $depActual):
                                            if ($depActual) echo '</optgroup>';
                                            echo '<optgroup label="' . htmlspecialchars($dist['dep']) . '">';
                                            $depActual = $dist['dep'];
                                        endif;
                                    ?>
                                    <option value="<?= $dist['id_distrital'] ?>"><?= htmlspecialchars($dist['nombre']) ?></option>
                                    <?php endforeach; if ($depActual) echo '</optgroup>'; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Descripción del Problema</label>
                            <textarea name="descripcion" required placeholder="Describa el problema técnico observado..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Generar Ticket
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Gráfico y accesos rápidos -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Estado de Tickets</h2>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="chartEstados"></canvas>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Accesos Rápidos — KEDB</h2>
                </div>
                <div class="card-body">
                    <ul style="list-style:none; font-size:.83rem; line-height:2.2;">
                        <li><a href="conocimiento.php#error403" style="color:var(--primary);">&#9658; Error 403 en envío de facturas</a></li>
                        <li><a href="conocimiento.php#token"    style="color:var(--primary);">&#9658; Reinstalación de drivers de Token</a></li>
                        <li><a href="conocimiento.php#conexion" style="color:var(--primary);">&#9658; Pérdida de conexión al SIN</a></li>
                        <li><a href="conocimiento.php#certificado" style="color:var(--primary);">&#9658; Certificado digital expirado</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de tickets recientes -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Tickets Recientes</h2>
            <a href="tickets.php" class="btn btn-outline btn-sm">Ver todos</a>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Tipo</th>
                            <th>Origen</th>
                            <th>Distrital</th>
                            <th>Severidad</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($recientes)): ?>
                        <tr><td colspan="7" style="text-align:center; color:var(--text-muted); padding:2rem;">
                            No hay incidentes registrados aún.
                        </td></tr>
                    <?php else: foreach ($recientes as $inc): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($inc['codigo']) ?></strong></td>
                            <td><?= htmlspecialchars($inc['tipo_nombre']) ?></td>
                            <td><?= htmlspecialchars($inc['origen_nombre']) ?></td>
                            <td><?= htmlspecialchars($inc['distrital']) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($inc['severidad']) ?>">
                                    <?= htmlspecialchars($inc['severidad']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= strtolower(str_replace('_','-',$inc['estado'])) ?>">
                                    <?= htmlspecialchars(str_replace('_',' ',$inc['estado'])) ?>
                                </span>
                            </td>
                            <td style="color:var(--text-muted);">
                                <?= date('d/m/Y H:i', strtotime($inc['fecha_registro'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="main.js"></script>
<script>
// Gráfico de estados
const estadosData = <?= json_encode(array_values($estados)) ?>;
const estadosLabels = <?= json_encode(array_keys($estados)) ?>;

const ctx = document.getElementById('chartEstados');
if (ctx) {
    new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: estadosLabels,
            datasets: [{
                data: estadosData,
                backgroundColor: ['#c0392b','#d35400','#f39c12','#27ae60','#8e44ad'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } }
        }
    });
}

// Envío del formulario por AJAX
document.getElementById('formIncidente').addEventListener('submit', function(e) {
    e.preventDefault();
    const data = new FormData(this);
    fetch('api/nuevo_incidente.php', { method: 'POST', body: data })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                alert('Ticket generado: ' + res.codigo);
                location.reload();
            } else {
                alert('Error: ' + (res.message || 'No se pudo registrar.'));
            }
        });
});
</script>
</body>
</html>
