<?php
// includes/auth.php
// Manejo de sesiones y autenticación básica

session_start();

function estaAutenticado(): bool {
    return isset($_SESSION['tecnico_id']) && !empty($_SESSION['tecnico_id']);
}

function requireAuth(): void {
    if (!estaAutenticado()) {
        header('Location: login.php');
        exit;
    }
}

function getTecnicoActual(): array {
    return [
        'id'     => $_SESSION['tecnico_id']     ?? null,
        'nombre' => $_SESSION['tecnico_nombre'] ?? 'Técnico',
        'nivel'  => $_SESSION['tecnico_nivel']  ?? 'Nivel1',
    ];
}

function cerrarSesion(): void {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
