<?php
// includes/db.php
// Conexión central a la base de datos MySQL

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Usuario por defecto
define('DB_PASS', '');            // Contraseña vacía por defecto
define('DB_NAME', 'sigi_sin');
define('DB_PORT', 3306);

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                DB_HOST, DB_PORT, DB_NAME
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode([
                'error' => true,
                'message' => 'Error de conexión a la base de datos: ' . $e->getMessage()
            ]));
        }
    }
    return $pdo;
}

// Registrar una entrada en la bitácora de auditoría
function logAuditoria(int $idTecnico = null, string $accion = '', string $tabla = '', int $idReg = null): void {
    $db = getDB();
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $stmt = $db->prepare(
        'INSERT INTO auditoria_log (id_tecnico, accion, tabla_afect, id_registro, ip_address)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$idTecnico, $accion, $tabla, $idReg, $ip]);
}
