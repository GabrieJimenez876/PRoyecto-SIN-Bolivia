<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireAuth();

$db = getDB();

// Datos para los gráficos
$porTipo = $db->query(
    'SELECT ti.nombre, COUNT(*) AS total
     FROM incidentes i JOIN tipos_incidente ti ON i.id_tipo = ti.id_tipo
     GROUP BY ti.id_tipo ORDER BY total DESC LIMIT 8'
)->fetchAll();

$porMes = $db->query(
    "SELECT DATE_FORMAT(MIN(fecha_registro),'%b %Y') AS mes,
            COUNT(*) AS total,
            YEAR(fecha_registro)  AS anio,
            MONTH(fecha_registro) AS mes_num
     FROM incidentes
     WHERE fecha_registro >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
     GROUP BY YEAR(fecha_registro), MONTH(fecha_registro)
     ORDER BY anio ASC, mes_num ASC"
)->fetchAll();

// Reporte por distritales (vista)
$distritalesReport = $db->query(
    'SELECT * FROM v_reporte_distritales WHERE total_tickets > 0 ORDER BY total_tickets DESC'
)->fetchAll();

$paginaActiva = 'reportes';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes | SIGI — SIN Bolivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1>Análisis de Rendimiento Institucional</h1>
            <p>Generación de reportes para Gerencia Distrital — SIN Bolivia</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-outline" onclick="window.print()">&#9113; Imprimir</button>
            
        </div>
    </div>

    <!-- Filtro de período -->
    <form method="GET" class="filter-bar">
        <span style="font-weight:600;font-size:.82rem;">Período:</span>
        <select name="periodo">
            <option>Últimos 30 días</option>
            <option>Este Trimestre</option>
            <option>Gestión 2026</option>
        </select>
        <select name="sede">
            <option>Todas las Sedes</option>
            <option>La Paz — Central</option>
            <option>Santa Cruz</option>
            <option>Cochabamba</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Generar</button>
    </form>

    <!-- Gráficos apilados verticalmente -->
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><h2 class="card-title">Frecuencia por Tipo de Incidente</h2></div>
        <div class="card-body">
            <div style="position:relative; height:300px;">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header"><h2 class="card-title">Tendencia Mensual de Incidentes</h2></div>
        <div class="card-body">
            <div style="position:relative; height:280px;">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabla por Distritales -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Resumen Ejecutivo por Distritales</h2>
        </div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Distrital</th>
                        <th>Departamento</th>
                        <th>Total Tickets</th>
                        <th>Resueltos</th>
                        <th>Eficiencia (%)</th>
                        <th>Tiempo Promedio</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($distritalesReport)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:2rem;">
                        Sin datos suficientes aún.
                    </td></tr>
                <?php else: foreach ($distritalesReport as $r): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($r['distrital']) ?></strong></td>
                        <td><?= htmlspecialchars($r['departamento']) ?></td>
                        <td><?= $r['total_tickets'] ?></td>
                        <td><?= $r['resueltos'] ?></td>
                        <td>
                            <?php $ef = (float)($r['eficiencia_pct'] ?? 0); ?>
                            <span style="color:<?= $ef >= 85 ? 'var(--success)' : ($ef >= 70 ? 'var(--warning)' : 'var(--danger)') ?>; font-weight:700;">
                                <?= number_format($ef, 1) ?>%
                            </span>
                        </td>
                        <td><?= $r['tiempo_prom_hrs'] ? number_format($r['tiempo_prom_hrs'], 1) . ' hrs' : '—' ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="main.js"></script>
<script>
const tipoLabels = <?= json_encode(array_column($porTipo, 'nombre')) ?>;
const tipoData   = <?= json_encode(array_column($porTipo, 'total')) ?>;
const mesLabels  = <?= json_encode(array_column($porMes, 'mes')) ?>;
const mesData    = <?= json_encode(array_column($porMes, 'total')) ?>;

new Chart(document.getElementById('barChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: tipoLabels.length ? tipoLabels : ['Sin datos'],
        datasets: [{ label: 'Cantidad de Reportes', data: tipoData.length ? tipoData : [0], backgroundColor: '#003366', borderRadius: 5 }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: {
                ticks: {
                    maxRotation: 0,
                    callback: function(val) {
                        const label = this.getLabelForValue(val);
                        return label.length > 18 ? label.substring(0, 18) + '…' : label;
                    },
                    font: { size: 11 }
                }
            },
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});

new Chart(document.getElementById('lineChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: mesLabels.length ? mesLabels : ['Sin datos'],
        datasets: [{ label: 'Incidentes', data: mesData.length ? mesData : [0],
            borderColor: '#c0392b', backgroundColor: 'rgba(192,57,43,.1)',
            tension: .3, fill: true, pointRadius: 4 }]
    },
    options: { responsive: true, maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } }
    }
});
</script>
</body>
</html>
