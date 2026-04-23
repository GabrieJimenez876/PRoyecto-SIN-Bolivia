<?php
// login.php
require_once 'includes/db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['contrasena'] ?? '';

    if ($email && $pass) {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id_tecnico, nombre, apellido, contrasena_hash, nivel, activo
             FROM tecnicos WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $tec = $stmt->fetch();

        if ($tec && $tec['activo'] && password_verify($pass, $tec['contrasena_hash'])) {
            $_SESSION['tecnico_id']     = $tec['id_tecnico'];
            $_SESSION['tecnico_nombre'] = $tec['nombre'] . ' ' . $tec['apellido'];
            $_SESSION['tecnico_nivel']  = $tec['nivel'];

            // Actualizar última conexión
            $db->prepare('UPDATE tecnicos SET ultima_conexion = NOW() WHERE id_tecnico = ?')
               ->execute([$tec['id_tecnico']]);

            logAuditoria($tec['id_tecnico'], 'Inicio de sesión exitoso', 'tecnicos', $tec['id_tecnico']);
            header('Location: index.php');
            exit;
        } else {
            $error = 'Correo o contraseña incorrectos.';
            // Registrar intento fallido
            logAuditoria(null, 'Intento de acceso fallido: ' . htmlspecialchars($email), 'tecnicos');
        }
    } else {
        $error = 'Por favor complete todos los campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | SIGI — SIN Bolivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body { justify-content: center; align-items: center; background: #001f3f; }
        .login-box {
            background: var(--card-bg);
            padding: 2.5rem 2.2rem;
            border-radius: 14px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
        }
        .login-logo {
            display: flex; align-items: center; justify-content: center; gap: .8rem;
            margin-bottom: 1.8rem;
        }
        .login-logo .icon {
            width: 48px; height: 48px; background: var(--primary);
            border-radius: 10px; display: flex; align-items: center;
            justify-content: center; font-weight: 900; color: #fff; font-size: 1.2rem;
        }
        .login-logo h1 { font-size: 1.3rem; font-weight: 800; }
        .login-logo p  { font-size: .72rem; color: var(--text-muted); margin-top: .1rem; }
        .error-msg {
            background: #fdecea; color: var(--danger);
            padding: .6rem .9rem; border-radius: 7px;
            font-size: .8rem; margin-bottom: 1rem;
            border-left: 3px solid var(--danger);
        }
    </style>
</head>
<body>
<div class="login-box">
    <div class="login-logo">
        <div class="icon">SIN</div>
        <div>
            <h1>SIGI</h1>
            <p>Servicio de Impuestos Nacionales · Bolivia</p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="form-group">
            <label class="form-label">Correo institucional</label>
            <input type="email" name="email" placeholder="usuario@sin.gob.bo" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">Contraseña</label>
            <div style="position:relative;">
                <input type="password" name="contrasena" id="inputPass" placeholder="••••••••" required
                       style="padding-right:2.8rem;">
                <button type="button" onclick="togglePass()" id="eyeBtn"
                        style="position:absolute;right:.7rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-muted);padding:0;display:flex;align-items:center;">
                    <!-- ojo abierto -->
                    <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    <!-- ojo cerrado (oculto por defecto) -->
                    <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                         style="display:none;">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                </button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg mt-2" style="justify-content: center;">
            Ingresar
        </button>
    </form>

    <p style="text-align:center; margin-top:1.2rem; font-size:.72rem; color:var(--text-muted);">
        Acceso restringido al personal técnico del SIN
    </p>
</div>
<script>
function togglePass() {
    const input = document.getElementById('inputPass');
    const open  = document.getElementById('eyeOpen');
    const closed = document.getElementById('eyeClosed');
    if (input.type === 'password') {
        input.type = 'text';
        open.style.display   = 'none';
        closed.style.display = 'block';
    } else {
        input.type = 'password';
        open.style.display   = 'block';
        closed.style.display = 'none';
    }
}
</script>
</body>
</html>
