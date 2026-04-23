<?php
// Ejecutar una vez antes del login
// Acceder desde: http://localhost/SIGI-SIN-Fase2/setup_passwords.php

require_once 'includes/db.php';

$tecnicos = [
    ['gabriel.jimenez@sin.gob.bo', 'Gabriel Isaac', 'Jimenez Tarqui', 'GabrielSIN#24'],
    ['pamela.canaza@sin.gob.bo',   'Pamela Esther', 'Canaza Luna',    'PamelaC@2026'],
    ['jorge.huanca@sin.gob.bo',    'Jorge Luis',    'Huanca Alarcon', 'JHuanca*SIN1'],
    ['juan.apaza@sin.gob.bo',      'Juan Carlos',   'Apaza Quispe',   'Apaza2026#TI'],
    ['luis.quispe@sin.gob.bo',     'Luis Donato',   'Quispe Ramirez', 'LuisQR@sin26'],
];

$db = getDB();
$ok = 0;

foreach ($tecnicos as [$email, $nombre, $apellido, $pass]) {
    $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);

    // Actualiza solo por el correo nuevo
    $stmt = $db->prepare(
        'UPDATE tecnicos SET nombre=?, apellido=?, contrasena_hash=? WHERE email=?'
    );
    $stmt->execute([$nombre, $apellido, $hash, $email]);

    // Si no existe ese correo, lo inserta
    if ($stmt->rowCount() === 0) {
        $db->prepare(
            'INSERT INTO tecnicos (email, nombre, apellido, contrasena_hash) VALUES (?, ?, ?, ?)'
        )->execute([$email, $nombre, $apellido, $hash]);
    }

    $ok++;
}
?>

<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Setup SIGI</title>
<style>body{font-family:Arial,sans-serif;max-width:700px;margin:3rem auto;padding:1rem}
table{width:100%;border-collapse:collapse;margin:1.5rem 0}
th,td{padding:.6rem 1rem;border:1px solid #ddd;font-size:.9rem;text-align:left}
th{background:#003366;color:#fff}
.note{background:#fff3e0;border-left:4px solid #f39c12;padding:1rem;margin-top:1.5rem;border-radius:4px}</style>
</head><body>
<h2>SIGI-SIN — Configuración Inicial</h2>
<p style="color:#27ae60;font-weight:bold;">&#10003; <?=$ok?> técnico(s) actualizados correctamente.</p>
<table>
<thead><tr><th>Nombre completo</th><th>Correo</th><th>Contraseña</th><th>Nivel</th></tr></thead>
<tbody>
<tr><td>Gabriel Isaac Jimenez Tarqui</td><td>gabriel.jimenez@sin.gob.bo</td><td>GabrielSIN#24</td><td>Administrador</td></tr>
<tr><td>Pamela Esther Canaza Luna</td><td>pamela.canaza@sin.gob.bo</td><td>PamelaC@2026</td><td>Nivel 3</td></tr>
<tr><td>Jorge Luis Huanca Alarcon</td><td>jorge.huanca@sin.gob.bo</td><td>JHuanca*SIN1</td><td>Nivel 2</td></tr>
<tr><td>Juan Carlos Apaza Quispe</td><td>juan.apaza@sin.gob.bo</td><td>Apaza2026#TI</td><td>Nivel 1</td></tr>
<tr><td>Luis Donato Quispe Ramirez</td><td>luis.quispe@sin.gob.bo</td><td>LuisQR@sin26</td><td>Nivel 1</td></tr>
</tbody></table>
<div class="note"><strong>&#9888; IMPORTANTE:</strong> Elimine este archivo después de verificar el login.<br><br>
<a href="login.php">&#8594; Ir al Login</a></div>
</body></html>
