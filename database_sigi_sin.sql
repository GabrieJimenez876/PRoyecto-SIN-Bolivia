--  SIGI-SIN: Sistema Integral de Gestión de Incidentes
--  Servicio de Impuestos Nacionales - Bolivia

CREATE DATABASE IF NOT EXISTS sigi_sin
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_spanish_ci;

USE sigi_sin;

-- DIMENSIÓN: Departamentos de Bolivia
CREATE TABLE departamentos (
    id_departamento INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(60) NOT NULL,
    sigla           VARCHAR(5)  NOT NULL
) ENGINE=InnoDB;

INSERT INTO departamentos (nombre, sigla) VALUES
('La Paz',       'LPZ'),
('Santa Cruz',   'SCZ'),
('Cochabamba',   'CBB'),
('Oruro',        'ORU'),
('Potosí',       'POT'),
('Chuquisaca',   'CHQ'),
('Tarija',       'TAR'),
('Beni',         'BEN'),
('Pando',        'PAN');

-- DIMENSIÓN: Distritales del SIN
-- Numeración según estructura real del SIN Bolivia
-- Cada distrital pertenece a un departamento
CREATE TABLE distritales (
    id_distrital    INT AUTO_INCREMENT PRIMARY KEY,
    codigo          VARCHAR(10) NOT NULL UNIQUE,  -- Ej: LPZ-01, SCZ-01
    nombre          VARCHAR(100) NOT NULL,         -- Ej: Distrital 1 - Ballivián
    id_departamento INT NOT NULL,
    FOREIGN KEY (id_departamento) REFERENCES departamentos(id_departamento)
) ENGINE=InnoDB;

INSERT INTO distritales (codigo, nombre, id_departamento) VALUES
-- La Paz
('LPZ-01', 'Distrital 1 - Ballivián',     1),
('LPZ-02', 'Distrital 2 - Sopocachi',     1),
('LPZ-03', 'Distrital 3 - Miraflores',    1),
('LPZ-04', 'Distrital 4 - El Alto',       1),
-- Santa Cruz
('SCZ-01', 'Distrital 1 - Equipetrol',    2),
('SCZ-02', 'Distrital 2 - Norte',         2),
-- Cochabamba
('CBB-01', 'Distrital 1 - Central',       3),
('CBB-02', 'Distrital 2 - Quillacollo',   3),
-- Resto de departamentos (sede central distrital)
('ORU-01', 'Distrital 1 - Oruro Central', 4),
('POT-01', 'Distrital 1 - Potosí Central',5),
('CHQ-01', 'Distrital 1 - Sucre Central', 6),
('TAR-01', 'Distrital 1 - Tarija Central',7),
('BEN-01', 'Distrital 1 - Trinidad',      8),
('PAN-01', 'Distrital 1 - Cobija',        9);

-- DIMENSIÓN: Módulos / Sistemas del SIN
-- Los incidentes internos se clasifican por módulo de negocio
CREATE TABLE modulos_sin (
    id_modulo   INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(80)  NOT NULL,
    descripcion VARCHAR(200) NOT NULL
) ENGINE=InnoDB;

INSERT INTO modulos_sin (nombre, descripcion) VALUES
('Facturación Electrónica', 'Sistema de emisión y validación de facturas electrónicas (SIAT)'),
('Declaraciones Juradas',   'Módulo de llenado y envío de formularios tributarios'),
('Padrón Biométrico',       'Registro y verificación de contribuyentes con biometría'),
('Certificación Digital',   'Gestión de tokens y certificados digitales para firma'),
('Consultas y Reportes',    'Plataforma de reportes y consultas del contribuyente'),
('Red e Infraestructura',   'Conectividad LAN/WAN, servidores y equipos de red'),
('Seguridad Informática',   'Accesos, permisos, VPN y auditoría del sistema'),
('Base de Datos',           'Integridad, rendimiento y backups de las BDs del SIN');

-- DIMENSIÓN: Técnicos / Personal TI (Usuarios Internos)
CREATE TABLE tecnicos (
    id_tecnico      INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100) NOT NULL,
    apellido        VARCHAR(100) NOT NULL,
    email           VARCHAR(120) NOT NULL UNIQUE,
    contrasena_hash VARCHAR(255) NOT NULL,
    nivel           ENUM('Nivel1','Nivel2','Nivel3','Administrador') NOT NULL DEFAULT 'Nivel1',
    id_distrital    INT NOT NULL,
    activo          TINYINT(1) NOT NULL DEFAULT 1,
    ultima_conexion DATETIME,
    FOREIGN KEY (id_distrital) REFERENCES distritales(id_distrital)
) ENGINE=InnoDB;

-- Técnicos del proyecto
INSERT INTO tecnicos (nombre, apellido, email, contrasena_hash, nivel, id_distrital, activo) VALUES
('Gabriel',  'Jimenez Tarqui',   'gabriel.jimenez@sin.gob.bo',  '$2y$12$examplehashXXXXXXXXXXXXXXX', 'Administrador', 1, 1),
('Pamela',   'Canaza Luna',      'pamela.canaza@sin.gob.bo',   '$2y$12$examplehashXXXXXXXXXXXXXXX', 'Nivel3',        1, 1),
('Jorge',    'Huanca Alarcon',   'jorge.huanca@sin.gob.bo',   '$2y$12$examplehashXXXXXXXXXXXXXXX', 'Nivel2',        2, 1),
('Luis',     'Quispe Ramirez',   'luis.quispe@sin.gob.bo',   '$2y$12$examplehashXXXXXXXXXXXXXXX', 'Nivel1',        1, 1),
('Carlos',   'Apaza Quispe',     'juan.apaza@sin.gob.bo',    '$2y$12$examplehashXXXXXXXXXXXXXXX', 'Nivel2',        3, 1);


CREATE TABLE contribuyentes (
    id_contribuyente INT AUTO_INCREMENT PRIMARY KEY,
    razon_social     VARCHAR(150) NOT NULL,  -- Nombre o empresa
    nit              VARCHAR(20)  NOT NULL UNIQUE,
    email            VARCHAR(120),
    telefono         VARCHAR(20),
    id_distrital     INT NOT NULL,           -- Distrital donde está registrado
    FOREIGN KEY (id_distrital) REFERENCES distritales(id_distrital)
) ENGINE=InnoDB;

-- DIMENSIÓN: Tipos de Incidente
-- Categorización estructurada para análisis BI
CREATE TABLE tipos_incidente (
    id_tipo     INT AUTO_INCREMENT PRIMARY KEY,
    categoria   ENUM('Tecnico','Funcional','Seguridad','Datos','Infraestructura') NOT NULL,
    nombre      VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

INSERT INTO tipos_incidente (categoria, nombre, descripcion) VALUES
('Tecnico',         'Error 403 - Token/CUFD inválido',         'Rechazo de token o código CUFD en el SIAT'),
('Tecnico',         'Error 500 - Servidor Interno',            'Fallo interno del servidor de facturación'),
('Tecnico',         'Caída del sistema SIAT',                  'Servicio SIAT completamente no disponible'),
('Funcional',       'Error en declaración jurada',             'Fallos en el llenado o envío de formularios'),
('Funcional',       'Factura electrónica rechazada',           'La factura no pasa validación del SIN'),
('Seguridad',       'Certificado digital expirado',            'Token o certificado con vigencia vencida'),
('Seguridad',       'Intento de acceso no autorizado',         'Login fallido repetido o acceso sospechoso'),
('Datos',           'Error de sincronización padrón',          'Datos del padrón biométrico no sincronizan'),
('Datos',           'Error de validación de datos',            'Datos no cumplen formato requerido por SIN'),
('Infraestructura', 'Pérdida de conectividad a servidores SIN','Sin acceso a los servidores centrales del SIN'),
('Infraestructura', 'Problema de red LAN distrital',           'Fallo de red interna en la distrital');

-- TABLA DE HECHOS: Incidentes
-- Corazón del modelo estrella — registra cada evento
CREATE TABLE incidentes (
    id_incidente     INT AUTO_INCREMENT PRIMARY KEY,
    codigo           VARCHAR(15) NOT NULL UNIQUE,  -- Ej: INC-2026-00001

    -- Dimensiones (llaves foráneas)
    id_tipo          INT NOT NULL,
    id_distrital     INT NOT NULL,
    id_modulo        INT,          -- Solo para incidentes de usuarios internos
    id_tecnico_asig  INT,          -- Técnico asignado para resolver
    id_tecnico_reg   INT NOT NULL, -- Técnico que registró el ticket
    id_contribuyente INT,          -- NULL si es incidente interno

    -- Tipo de origen del incidente
    origen           ENUM('Externo','Interno') NOT NULL DEFAULT 'Externo',

    -- Atributos del incidente
    descripcion      TEXT NOT NULL,
    severidad        ENUM('Bajo','Medio','Alto','Critico') NOT NULL DEFAULT 'Medio',
    estado           ENUM('Abierto','En_Proceso','Pendiente','Resuelto','Cerrado') NOT NULL DEFAULT 'Abierto',
    prioridad        ENUM('Baja','Media','Alta','Urgente') NOT NULL DEFAULT 'Media',

    -- Solución documentada (base de conocimientos KEDB)
    solucion_aplicada TEXT,

    -- Tiempos (para calcular MTTR y SLA)
    fecha_registro   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_asignacion DATETIME,
    fecha_resolucion DATETIME,
    fecha_cierre     DATETIME,

    -- SLA — tiempo límite en horas según severidad
    sla_horas        INT          NOT NULL DEFAULT 4,

    FOREIGN KEY (id_tipo)          REFERENCES tipos_incidente(id_tipo),
    FOREIGN KEY (id_distrital)     REFERENCES distritales(id_distrital),
    FOREIGN KEY (id_modulo)        REFERENCES modulos_sin(id_modulo),
    FOREIGN KEY (id_tecnico_asig)  REFERENCES tecnicos(id_tecnico),
    FOREIGN KEY (id_tecnico_reg)   REFERENCES tecnicos(id_tecnico),
    FOREIGN KEY (id_contribuyente) REFERENCES contribuyentes(id_contribuyente)
) ENGINE=InnoDB;

-- LOG DE AUDITORÍA
-- Registra acciones clave para trazabilidad y seguridad
CREATE TABLE auditoria_log (
    id_log       INT AUTO_INCREMENT PRIMARY KEY,
    id_tecnico   INT,
    accion       VARCHAR(150) NOT NULL,
    tabla_afect  VARCHAR(60),
    id_registro  INT,
    ip_address   VARCHAR(45),
    fecha        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_tecnico) REFERENCES tecnicos(id_tecnico)
) ENGINE=InnoDB;

-- BASE DE CONOCIMIENTOS (KEDB)
-- Soluciones documentadas para incidentes recurrentes
CREATE TABLE conocimiento_kedb (
    id_articulo  INT AUTO_INCREMENT PRIMARY KEY,
    id_tipo      INT NOT NULL,
    titulo       VARCHAR(150) NOT NULL,
    sintoma      TEXT NOT NULL,
    solucion     TEXT NOT NULL,
    pasos        TEXT,          -- JSON o texto con pasos numerados
    creado_por   INT NOT NULL,
    fecha_crea   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    vistas       INT NOT NULL DEFAULT 0,
    FOREIGN KEY (id_tipo)     REFERENCES tipos_incidente(id_tipo),
    FOREIGN KEY (creado_por)  REFERENCES tecnicos(id_tecnico)
) ENGINE=InnoDB;

-- VISTA: Dashboard KPIs 
CREATE OR REPLACE VIEW v_kpis_dashboard AS
SELECT
    COUNT(*)                                                         AS total_incidentes,
    SUM(estado = 'Abierto')                                         AS abiertos,
    SUM(estado = 'En_Proceso')                                      AS en_proceso,
    SUM(estado IN ('Resuelto','Cerrado') AND DATE(fecha_cierre) = CURDATE()) AS resueltos_hoy,
    SUM(severidad = 'Critico' AND estado NOT IN ('Resuelto','Cerrado'))      AS criticos_activos,
    ROUND(AVG(TIMESTAMPDIFF(MINUTE, fecha_registro, fecha_resolucion)/60), 2) AS mttr_promedio_horas
FROM incidentes;

-- VISTA: Reporte por distrital (para reportes.php)
CREATE OR REPLACE VIEW v_reporte_distritales AS
SELECT
    d.codigo,
    d.nombre          AS distrital,
    dep.nombre        AS departamento,
    COUNT(i.id_incidente)                                 AS total_tickets,
    SUM(i.estado IN ('Resuelto','Cerrado'))               AS resueltos,
    ROUND(SUM(i.estado IN ('Resuelto','Cerrado'))*100.0 / COUNT(*), 1) AS eficiencia_pct,
    ROUND(AVG(TIMESTAMPDIFF(MINUTE, i.fecha_registro, i.fecha_resolucion)/60), 2) AS tiempo_prom_hrs
FROM distritales d
JOIN departamentos dep ON d.id_departamento = dep.id_departamento
LEFT JOIN incidentes i ON i.id_distrital = d.id_distrital
GROUP BY d.id_distrital;

-- TRIGGER: Auto-generar código de incidente
DELIMITER $$
CREATE TRIGGER trg_codigo_incidente
BEFORE INSERT ON incidentes
FOR EACH ROW
BEGIN
    DECLARE next_num INT;
    SELECT COUNT(*) + 1 INTO next_num FROM incidentes;
    SET NEW.codigo = CONCAT('INC-', YEAR(NOW()), '-', LPAD(next_num, 5, '0'));
END$$

-- TRIGGER: Log automático al cambiar estado de incidente
CREATE TRIGGER trg_log_estado_incidente
AFTER UPDATE ON incidentes
FOR EACH ROW
BEGIN
    IF OLD.estado <> NEW.estado THEN
        INSERT INTO auditoria_log (id_tecnico, accion, tabla_afect, id_registro)
        VALUES (NEW.id_tecnico_asig,
                CONCAT('Estado cambiado de "', OLD.estado, '" a "', NEW.estado, '"'),
                'incidentes',
                NEW.id_incidente);
    END IF;
END$$
DELIMITER ;