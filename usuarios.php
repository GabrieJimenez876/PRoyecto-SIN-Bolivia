<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireAuth();

$db  = getDB();
$tec = getTecnicoActual();

// Alta de nuevo técnico
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $pass     = $_POST['contrasena']    ?? '';
    $nivel    = $_POST['nivel']         ?? 'Nivel1';
    $id_dist  = (int)($_POST['id_distrital'] ?? 1);

    if ($nombre && $apellido && $email && $pass) {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        try {
            $db->prepare(
                'INSERT INTO tecnicos (nombre, apellido, email, contrasena_hash, nivel, id_distrital)
                 VALUES (?,?,?,?,?,?)'
            )->execute([$nombre, $apellido, $email, $hash, $nivel, $id_dist]);
            logAuditoria($tec['id'], "Nuevo técnico registrado: $nombre $apellido", 'tecnicos');
            header('Location: usuarios.php?ok=1');
            exit;
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    } else {
        $error = 'Complete todos los campos obligatorios.';
    }
}

$tecnicos = $db->query(
    'SELECT t.*, d.nombre AS distrital,
            (SELECT COUNT(*) FROM incidentes WHERE id_tecnico_asig = t.id_tecnico) AS tickets_asignados
     FROM tecnicos t
     JOIN distritales d ON t.id_distrital = d.id_distrital
     ORDER BY t.nivel DESC, t.nombre'
)->fetchAll();

$distritales = $db->query('SELECT id_distrital, nombre FROM distritales ORDER BY nombre')->fetchAll();

$coloresAvatar = ['Administrador'=>'#003366','Nivel3'=>'#660429','Nivel2'=>'#055a0a','Nivel1'=>'#c57b05'];

$paginaActiva = 'usuarios';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Técnicos | SIGI — SIN Bolivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1>Personal Técnico</h1>
            <p>Administración del equipo de soporte del SIN</p>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary" onclick="document.getElementById('modalNuevo').style.display='flex'">
                + Nuevo Técnico
            </button>
            
        </div>
    </div>

    <?php if (isset($_GET['ok'])): ?>
    <div style="background:#e8f5e9;color:#2e7d32;padding:.8rem 1rem;border-radius:8px;margin-bottom:1rem;border-left:4px solid #27ae60;">
        Técnico registrado correctamente.
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div style="background:#fdecea;color:var(--danger);padding:.8rem 1rem;border-radius:8px;margin-bottom:1rem;border-left:4px solid var(--danger);">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Lista de Operadores (<?= count($tecnicos) ?>)</h2>
            <input type="text" placeholder="Buscar técnico..." oninput="filtrarTecnicos(this.value)"
                   style="max-width:220px; margin:0; padding:.45rem .8rem;">
        </div>
        <div class="table-wrapper">
            <table class="table" id="tablaTecnicos">
                <thead>
                    <tr>
                        <th>Técnico</th>
                        <th>Nivel</th>
                        <th>Distrital</th>
                        <th>Tickets Asignados</th>
                        <th>Estado</th>
                        <th>Última Conexión</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($tecnicos as $t):
                    $iniciales = strtoupper(substr($t['nombre'],0,1) . substr($t['apellido'],0,1));
                    $color = $coloresAvatar[$t['nivel']] ?? '#555';
                    $nivelBadge = 'badge-' . strtolower(str_replace('Nivel','nivel',$t['nivel']));
                    $estaActivo = $t['activo'];
                    $ultimaCon  = $t['ultima_conexion'] ? date('d/m/Y H:i', strtotime($t['ultima_conexion'])) : 'Nunca';
                ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;">
                            <div class="avatar" style="background:<?= $color ?>; flex-shrink:0;"><?= $iniciales ?></div>
                            <div>
                                <strong><?= htmlspecialchars($t['nombre'] . ' ' . $t['apellido']) ?></strong>
                                <div style="font-size:.72rem;color:var(--text-muted);"><?= htmlspecialchars($t['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge <?= $t['nivel'] === 'Administrador' ? 'badge-admin' : 'badge-nivel1' ?>">
                            <?= htmlspecialchars($t['nivel']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($t['distrital']) ?></td>
                    <td style="text-align:center;"><?= (int)$t['tickets_asignados'] ?></td>
                    <td>
                        <span class="status-dot <?= $estaActivo ? 'online' : '' ?>"></span>
                        <?= $estaActivo ? 'Activo' : 'Inactivo' ?>
                    </td>
                    <td style="color:var(--text-muted); font-size:.78rem;"><?= $ultimaCon ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Nuevo Técnico -->
<div id="modalNuevo" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:var(--card-bg);border-radius:12px;padding:2rem;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <h2 style="margin-bottom:1.2rem;">Registrar Nuevo Técnico</h2>
        <form method="POST">
            <input type="hidden" name="accion" value="crear">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;">
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Apellido *</label>
                    <input type="text" name="apellido" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Correo Institucional *</label>
                <input type="email" name="email" placeholder="usuario@sin.gob.bo" required>
            </div>
            <div class="form-group">
                <label class="form-label">Contraseña *</label>
                <input type="password" name="contrasena" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.8rem;">
                <div class="form-group">
                    <label class="form-label">Nivel</label>
                    <select name="nivel">
                        <?php foreach (['Nivel1','Nivel2','Nivel3','Administrador'] as $n): ?>
                        <option value="<?= $n ?>"><?= $n ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Distrital</label>
                    <select name="id_distrital">
                        <?php foreach ($distritales as $d): ?>
                        <option value="<?= $d['id_distrital'] ?>"><?= htmlspecialchars($d['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:.8rem;justify-content:flex-end;margin-top:.5rem;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalNuevo').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>
</div>

<script src="main.js"></script>
<script>
function filtrarTecnicos(q) {
    document.querySelectorAll('#tablaTecnicos tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
    });
}
</script>
</body>
</html>
