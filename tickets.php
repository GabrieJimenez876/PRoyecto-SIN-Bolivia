<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireAuth();

$db  = getDB();
$tec = getTecnicoActual();

// Cambio de estado vía POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_incidente'], $_POST['nuevo_estado'])) {
    $estados_validos = ['Abierto','En_Proceso','Pendiente','Resuelto','Cerrado'];
    $nuevo  = $_POST['nuevo_estado'];
    $id_inc = (int)$_POST['id_incidente'];
    $sol    = trim($_POST['solucion'] ?? '');

    if (in_array($nuevo, $estados_validos)) {
        $fechaResolucion = in_array($nuevo, ['Resuelto','Cerrado']) ? ', fecha_resolucion = NOW()' : '';
        $fechaCierre     = $nuevo === 'Cerrado' ? ', fecha_cierre = NOW()' : '';
        $db->prepare(
            "UPDATE incidentes SET estado = ?, id_tecnico_asig = ?, solucion_aplicada = ?
             $fechaResolucion $fechaCierre
             WHERE id_incidente = ?"
        )->execute([$nuevo, $tec['id'], $sol ?: null, $id_inc]);
        logAuditoria($tec['id'], "Estado ticket #$id_inc → $nuevo", 'incidentes', $id_inc);
    }
    header('Location: tickets.php');
    exit;
}

// Filtros
$filtroEstado = $_GET['estado']   ?? '';
$filtroSev    = $_GET['severidad']?? '';
$filtroDist   = (int)($_GET['distrital'] ?? 0);

$where = ['1=1'];
$params = [];

if ($filtroEstado) { $where[] = 'i.estado = ?'; $params[] = $filtroEstado; }
if ($filtroSev)    { $where[] = 'i.severidad = ?'; $params[] = $filtroSev; }
if ($filtroDist)   { $where[] = 'i.id_distrital = ?'; $params[] = $filtroDist; }

$whereStr = implode(' AND ', $where);

$stmt = $db->prepare(
    "SELECT i.id_incidente, i.codigo, i.severidad, i.estado, i.prioridad,
            i.descripcion, i.fecha_registro, i.origen,
            COALESCE(c.razon_social, 'Interno') AS origen_nombre,
            ti.nombre   AS tipo_nombre,
            d.nombre    AS distrital,
            t.nombre || ' ' || t.apellido AS tecnico_asig
     FROM incidentes i
     JOIN tipos_incidente ti ON i.id_tipo       = ti.id_tipo
     JOIN distritales      d  ON i.id_distrital  = d.id_distrital
     LEFT JOIN contribuyentes c  ON i.id_contribuyente = c.id_contribuyente
     LEFT JOIN tecnicos      t  ON i.id_tecnico_asig   = t.id_tecnico
     WHERE $whereStr
     ORDER BY FIELD(i.severidad,'Critico','Alto','Medio','Bajo'),
              i.fecha_registro DESC"
);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

$distritales = $db->query(
    'SELECT d.id_distrital, d.nombre FROM distritales d ORDER BY d.nombre'
)->fetchAll();

$paginaActiva = 'tickets';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets | SIGI — SIN Bolivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1>Gestión de Tickets</h1>
            <p>Administración y seguimiento de incidentes registrados</p>
        </div>
        <div class="header-actions">
            
        </div>
    </div>

    <!-- Filtros -->
    <form method="GET" class="filter-bar">
        <span style="font-weight:600; font-size:.82rem;">Filtrar:</span>
        <select name="estado">
            <option value="">Todos los estados</option>
            <?php foreach (['Abierto','En_Proceso','Pendiente','Resuelto','Cerrado'] as $e): ?>
            <option value="<?= $e ?>" <?= $filtroEstado === $e ? 'selected' : '' ?>>
                <?= str_replace('_',' ',$e) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <select name="severidad">
            <option value="">Todas las severidades</option>
            <?php foreach (['Critico','Alto','Medio','Bajo'] as $s): ?>
            <option value="<?= $s ?>" <?= $filtroSev === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <select name="distrital">
            <option value="">Todas las distritales</option>
            <?php foreach ($distritales as $d): ?>
            <option value="<?= $d['id_distrital'] ?>" <?= $filtroDist === (int)$d['id_distrital'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['nombre']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
        <a href="tickets.php" class="btn btn-outline btn-sm">Limpiar</a>
    </form>

    <!-- Tabla de tickets -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Lista de Incidentes (<?= count($tickets) ?>)</h2>
        </div>
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
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($tickets)): ?>
                    <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:2rem;">
                        No se encontraron tickets con los filtros seleccionados.
                    </td></tr>
                <?php else: foreach ($tickets as $t): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($t['codigo']) ?></strong></td>
                        <td style="max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            <?= htmlspecialchars($t['tipo_nombre']) ?>
                        </td>
                        <td><?= htmlspecialchars($t['origen_nombre']) ?></td>
                        <td><?= htmlspecialchars($t['distrital']) ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower($t['severidad']) ?>">
                                <?= $t['severidad'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= strtolower(str_replace('_','-',$t['estado'])) ?>">
                                <?= str_replace('_',' ',$t['estado']) ?>
                            </span>
                        </td>
                        <td style="color:var(--text-muted);">
                            <?= date('d/m/Y H:i', strtotime($t['fecha_registro'])) ?>
                        </td>
                        <td>
                            <button class="btn btn-outline btn-sm"
                                onclick="abrirModal(<?= $t['id_incidente'] ?>, '<?= addslashes($t['codigo']) ?>', '<?= addslashes($t['descripcion']) ?>', '<?= $t['estado'] ?>')">
                                Gestionar
                            </button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal gestión de estado -->
<div id="modalGestion" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:var(--card-bg); border-radius:12px; padding:2rem; width:100%; max-width:500px; box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <h2 style="margin-bottom:1.2rem;" id="modalTitulo">Gestionar Ticket</h2>
        <form method="POST">
            <input type="hidden" name="id_incidente" id="modalId">
            <div class="form-group">
                <label class="form-label">Nuevo Estado</label>
                <select name="nuevo_estado" id="modalEstado">
                    <?php foreach (['Abierto','En_Proceso','Pendiente','Resuelto','Cerrado'] as $e): ?>
                    <option value="<?= $e ?>"><?= str_replace('_',' ',$e) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Descripción del Incidente</label>
                <textarea readonly id="modalDesc" style="background:var(--bg);"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Solución Aplicada (opcional)</label>
                <textarea name="solucion" placeholder="Describa la solución o acciones tomadas..."></textarea>
            </div>
            <div style="display:flex; gap:.8rem; justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script src="main.js"></script>
<script>
function abrirModal(id, codigo, desc, estado) {
    document.getElementById('modalId').value     = id;
    document.getElementById('modalTitulo').textContent = 'Gestionar ' + codigo;
    document.getElementById('modalDesc').value   = desc;
    document.getElementById('modalEstado').value = estado;
    document.getElementById('modalGestion').style.display = 'flex';
}
function cerrarModal() {
    document.getElementById('modalGestion').style.display = 'none';
}
</script>
</body>
</html>
