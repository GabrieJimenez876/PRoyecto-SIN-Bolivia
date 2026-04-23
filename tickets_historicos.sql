--  Ejecutar en phpMyAdmin después de importar database_sigi_sin.sql
USE sigi_sin;

DROP TRIGGER IF EXISTS trg_codigo_incidente;

-- Insertar tickets con fechas retroactivas (6 meses)
INSERT INTO incidentes
  (codigo, id_tipo, id_distrital, id_modulo, id_tecnico_reg, id_tecnico_asig,
   origen, descripcion, severidad, prioridad, estado, sla_horas,
   fecha_registro, fecha_asignacion, fecha_resolucion, fecha_cierre)
VALUES

-- NOVIEMBRE 2025 (12 tickets)
('INC-2025-00001',1,1,1,1,2,'Interno','Error 403 al enviar paquete de facturas. Token vencido.','Alto','Alta','Cerrado',2,'2025-11-03 08:15:00','2025-11-03 08:30:00','2025-11-03 10:10:00','2025-11-03 10:30:00'),
('INC-2025-00002',3,2,1,2,3,'Interno','SIAT no responde en toda la distrital Sopocachi.','Critico','Urgente','Cerrado',1,'2025-11-05 14:00:00','2025-11-05 14:10:00','2025-11-05 15:45:00','2025-11-05 16:00:00'),
('INC-2025-00003',6,1,4,1,4,'Interno','Certificado digital expirado en equipo de facturación.','Medio','Media','Cerrado',4,'2025-11-07 09:00:00','2025-11-07 09:20:00','2025-11-07 12:00:00','2025-11-07 12:30:00'),
('INC-2025-00004',10,3,6,2,5,'Interno','Pérdida de conectividad WAN en distrital Cochabamba.','Alto','Alta','Cerrado',2,'2025-11-10 11:30:00','2025-11-10 11:40:00','2025-11-10 14:00:00','2025-11-10 14:15:00'),
('INC-2025-00005',4,1,2,1,2,'Interno','Error al enviar formulario 200 del período octubre.','Bajo','Baja','Cerrado',8,'2025-11-12 10:00:00','2025-11-12 10:30:00','2025-11-12 13:00:00','2025-11-12 13:20:00'),
('INC-2025-00006',2,4,1,3,3,'Interno','Caída del sistema SIAT en El Alto. Afecta 3 equipos.','Critico','Urgente','Cerrado',1,'2025-11-14 08:00:00','2025-11-14 08:05:00','2025-11-14 09:30:00','2025-11-14 09:45:00'),
('INC-2025-00007',1,2,1,2,4,'Interno','Token inválido al intentar enviar facturas de semana.','Alto','Alta','Cerrado',2,'2025-11-18 13:00:00','2025-11-18 13:15:00','2025-11-18 15:00:00','2025-11-18 15:20:00'),
('INC-2025-00008',11,1,6,4,5,'Interno','Fallo de red LAN en piso 2 sede central La Paz.','Alto','Alta','Cerrado',2,'2025-11-20 09:30:00','2025-11-20 09:45:00','2025-11-20 11:30:00','2025-11-20 12:00:00'),
('INC-2025-00009',7,1,7,1,1,'Interno','Intento de acceso fallido repetido desde IP externa.','Critico','Urgente','Cerrado',1,'2025-11-22 16:00:00','2025-11-22 16:05:00','2025-11-22 17:00:00','2025-11-22 17:15:00'),
('INC-2025-00010',8,3,3,2,3,'Interno','Datos del padrón biométrico no sincronizan en CBB.','Medio','Media','Cerrado',4,'2025-11-25 10:00:00','2025-11-25 10:30:00','2025-11-25 14:00:00','2025-11-25 14:30:00'),
('INC-2025-00011',5,5,1,3,2,'Interno','Factura rechazada por CUFD no válido del día.','Alto','Alta','Cerrado',2,'2025-11-27 11:00:00','2025-11-27 11:10:00','2025-11-27 12:30:00','2025-11-27 13:00:00'),
('INC-2025-00012',9,1,1,1,4,'Interno','Error de validación en campo monto de factura.','Bajo','Baja','Cerrado',8,'2025-11-29 15:00:00','2025-11-29 15:20:00','2025-11-29 17:00:00','2025-11-29 17:20:00'),

-- DICIEMBRE 2025 (15 tickets — mes de mayor actividad tributaria)
('INC-2025-00013',1,1,1,1,2,'Interno','Error 403 masivo. Múltiples usuarios no pueden facturar.','Critico','Urgente','Cerrado',1,'2025-12-02 08:00:00','2025-12-02 08:05:00','2025-12-02 09:30:00','2025-12-02 09:45:00'),
('INC-2025-00014',3,2,1,2,3,'Interno','SIAT caído. Cierre de gestión 2025 en riesgo.','Critico','Urgente','Cerrado',1,'2025-12-05 07:30:00','2025-12-05 07:35:00','2025-12-05 09:00:00','2025-12-05 09:20:00'),
('INC-2025-00015',6,3,4,3,4,'Interno','Tres certificados digitales vencidos en Cochabamba.','Alto','Alta','Cerrado',2,'2025-12-08 09:00:00','2025-12-08 09:20:00','2025-12-08 12:00:00','2025-12-08 12:30:00'),
('INC-2025-00016',4,1,2,1,2,'Interno','Formulario 605 rechazado por período ya declarado.','Medio','Media','Cerrado',4,'2025-12-10 10:30:00','2025-12-10 11:00:00','2025-12-10 14:00:00','2025-12-10 14:30:00'),
('INC-2025-00017',2,4,1,4,3,'Interno','SIAT no disponible en El Alto. Fin de año crítico.','Critico','Urgente','Cerrado',1,'2025-12-12 13:00:00','2025-12-12 13:05:00','2025-12-12 14:30:00','2025-12-12 15:00:00'),
('INC-2025-00018',10,5,6,2,5,'Interno','Sin conexión a servidores SIN en Santa Cruz Equipetrol.','Alto','Alta','Cerrado',2,'2025-12-15 08:30:00','2025-12-15 08:40:00','2025-12-15 11:00:00','2025-12-15 11:20:00'),
('INC-2025-00019',1,1,1,1,2,'Interno','Token inválido. Cierre tributario fin de año.','Critico','Urgente','Cerrado',1,'2025-12-18 09:00:00','2025-12-18 09:10:00','2025-12-18 10:00:00','2025-12-18 10:15:00'),
('INC-2025-00020',11,2,6,3,4,'Interno','Red LAN Sopocachi caída. 8 equipos sin conectividad.','Alto','Alta','Cerrado',2,'2025-12-19 10:00:00','2025-12-19 10:15:00','2025-12-19 13:00:00','2025-12-19 13:30:00'),
('INC-2025-00021',5,1,1,1,3,'Interno','Facturas rechazadas masivamente por CUFD expirado.','Critico','Urgente','Cerrado',1,'2025-12-22 08:00:00','2025-12-22 08:10:00','2025-12-22 09:00:00','2025-12-22 09:20:00'),
('INC-2025-00022',8,3,3,2,5,'Interno','Padrón biométrico sin sincronizar en Quillacollo.','Medio','Media','Cerrado',4,'2025-12-23 14:00:00','2025-12-23 14:30:00','2025-12-23 17:00:00','2025-12-23 17:30:00'),
('INC-2025-00023',7,1,7,1,1,'Interno','Acceso no autorizado detectado. IP bloqueada.','Critico','Urgente','Cerrado',1,'2025-12-26 16:30:00','2025-12-26 16:35:00','2025-12-26 17:30:00','2025-12-26 17:45:00'),
('INC-2025-00024',9,4,1,4,2,'Interno','Error de validación en datos de factura cierre gestión.','Alto','Alta','Cerrado',2,'2025-12-27 11:00:00','2025-12-27 11:20:00','2025-12-27 14:00:00','2025-12-27 14:30:00'),
('INC-2025-00025',3,1,1,2,3,'Interno','SIAT intermitente último día hábil del año.','Critico','Urgente','Cerrado',1,'2025-12-30 08:00:00','2025-12-30 08:05:00','2025-12-30 10:00:00','2025-12-30 10:20:00'),
('INC-2025-00026',6,2,4,1,4,'Interno','Certificado vencido en Sopocachi fin de año.','Medio','Media','Cerrado',4,'2025-12-30 13:00:00','2025-12-30 13:30:00','2025-12-30 16:00:00','2025-12-30 16:30:00'),
('INC-2025-00027',1,3,1,3,5,'Interno','Token inválido en Cochabamba. Cierre de gestión.','Alto','Alta','Cerrado',2,'2025-12-31 09:00:00','2025-12-31 09:10:00','2025-12-31 11:00:00','2025-12-31 11:30:00'),

-- ENERO 2026 (10 tickets)
('INC-2026-00001',4,1,2,1,2,'Interno','Formulario 200 enero 2026 no se puede enviar.','Medio','Media','Cerrado',4,'2026-01-05 09:00:00','2026-01-05 09:30:00','2026-01-05 12:00:00','2026-01-05 12:30:00'),
('INC-2026-00002',10,3,6,2,3,'Interno','Sin conexión a servidores SIN en CBB inicio de año.','Alto','Alta','Cerrado',2,'2026-01-07 08:30:00','2026-01-07 08:45:00','2026-01-07 11:00:00','2026-01-07 11:30:00'),
('INC-2026-00003',1,2,1,3,4,'Interno','Error 403 en envío de facturas enero Sopocachi.','Alto','Alta','Cerrado',2,'2026-01-09 10:00:00','2026-01-09 10:15:00','2026-01-09 12:30:00','2026-01-09 13:00:00'),
('INC-2026-00004',6,1,4,1,2,'Interno','Certificado digital vencido tras el año nuevo.','Medio','Media','Cerrado',4,'2026-01-12 09:00:00','2026-01-12 09:30:00','2026-01-12 13:00:00','2026-01-12 13:30:00'),
('INC-2026-00005',11,4,6,4,5,'Interno','Fallo de red LAN en El Alto inicio de gestión.','Alto','Alta','Cerrado',2,'2026-01-14 11:00:00','2026-01-14 11:15:00','2026-01-14 14:00:00','2026-01-14 14:30:00'),
('INC-2026-00006',2,1,1,1,3,'Interno','Caída del SIAT en La Paz Ballivián por 2 horas.','Critico','Urgente','Cerrado',1,'2026-01-16 14:00:00','2026-01-16 14:05:00','2026-01-16 16:00:00','2026-01-16 16:15:00'),
('INC-2026-00007',8,3,3,2,4,'Interno','Padrón biométrico desactualizado en Cochabamba.','Bajo','Baja','Cerrado',8,'2026-01-20 10:00:00','2026-01-20 10:30:00','2026-01-20 15:00:00','2026-01-20 15:30:00'),
('INC-2026-00008',5,2,1,3,2,'Interno','Facturas rechazadas por código actividad inválido.','Medio','Media','Cerrado',4,'2026-01-22 09:30:00','2026-01-22 10:00:00','2026-01-22 13:00:00','2026-01-22 13:30:00'),
('INC-2026-00009',9,1,1,1,3,'Interno','Error de validación en monto de factura electrónica.','Bajo','Baja','Cerrado',8,'2026-01-25 15:00:00','2026-01-25 15:20:00','2026-01-25 17:00:00','2026-01-25 17:20:00'),
('INC-2026-00010',7,1,7,1,1,'Interno','Intento de acceso sospechoso desde IP externa.','Critico','Urgente','Cerrado',1,'2026-01-28 16:00:00','2026-01-28 16:05:00','2026-01-28 17:00:00','2026-01-28 17:15:00'),

-- FEBRERO 2026 (14 tickets)
('INC-2026-00011',1,1,1,1,2,'Interno','Error 403 repetitivo en envío de paquetes al SIAT.','Alto','Alta','Cerrado',2,'2026-02-02 08:30:00','2026-02-02 08:45:00','2026-02-02 11:00:00','2026-02-02 11:30:00'),
('INC-2026-00012',3,2,1,2,3,'Interno','SIAT caído en Sopocachi. Técnicos trabajando.','Critico','Urgente','Cerrado',1,'2026-02-04 09:00:00','2026-02-04 09:10:00','2026-02-04 10:30:00','2026-02-04 10:45:00'),
('INC-2026-00013',6,3,4,3,4,'Interno','Certificados vencidos en dos equipos de Quillacollo.','Medio','Media','Cerrado',4,'2026-02-06 10:00:00','2026-02-06 10:30:00','2026-02-06 14:00:00','2026-02-06 14:30:00'),
('INC-2026-00014',4,1,2,1,2,'Interno','Formulario 400 rechazado en la distrital Ballivián.','Medio','Media','Cerrado',4,'2026-02-10 09:30:00','2026-02-10 10:00:00','2026-02-10 13:00:00','2026-02-10 13:30:00'),
('INC-2026-00015',10,5,6,2,5,'Interno','Sin conexión WAN en Santa Cruz Equipetrol.','Alto','Alta','Cerrado',2,'2026-02-12 08:00:00','2026-02-12 08:15:00','2026-02-12 10:30:00','2026-02-12 11:00:00'),
('INC-2026-00016',2,4,1,4,3,'Interno','SIAT intermitente en El Alto. Dos equipos afectados.','Alto','Alta','Cerrado',2,'2026-02-14 11:00:00','2026-02-14 11:15:00','2026-02-14 14:00:00','2026-02-14 14:30:00'),
('INC-2026-00017',11,1,6,1,4,'Interno','Fallo de switch en piso 3 sede central La Paz.','Alto','Alta','Cerrado',2,'2026-02-17 09:00:00','2026-02-17 09:20:00','2026-02-17 12:00:00','2026-02-17 12:30:00'),
('INC-2026-00018',5,2,1,3,2,'Interno','Factura rechazada por NIT del comprador inválido.','Bajo','Baja','Cerrado',8,'2026-02-19 14:00:00','2026-02-19 14:30:00','2026-02-19 16:30:00','2026-02-19 17:00:00'),
('INC-2026-00019',8,3,3,2,5,'Interno','Sincronización padrón biométrico fallida en CBB.','Medio','Media','Cerrado',4,'2026-02-21 10:00:00','2026-02-21 10:30:00','2026-02-21 14:00:00','2026-02-21 14:30:00'),
('INC-2026-00020',7,1,7,1,1,'Interno','Acceso no autorizado. Se bloqueó cuenta afectada.','Critico','Urgente','Cerrado',1,'2026-02-24 16:00:00','2026-02-24 16:05:00','2026-02-24 17:00:00','2026-02-24 17:10:00'),
('INC-2026-00021',1,4,1,4,3,'Interno','Token inválido en El Alto. Renovado exitosamente.','Alto','Alta','Cerrado',2,'2026-02-25 08:30:00','2026-02-25 08:45:00','2026-02-25 10:30:00','2026-02-25 11:00:00'),
('INC-2026-00022',9,1,1,1,2,'Interno','Validación fallida en campo fecha de factura.','Bajo','Baja','Cerrado',8,'2026-02-26 11:00:00','2026-02-26 11:30:00','2026-02-26 14:00:00','2026-02-26 14:30:00'),
('INC-2026-00023',3,2,1,2,3,'Interno','SIAT lento durante pico de declaraciones febrero.','Medio','Media','Cerrado',4,'2026-02-27 09:00:00','2026-02-27 09:30:00','2026-02-27 12:00:00','2026-02-27 12:30:00'),
('INC-2026-00024',6,3,4,3,4,'Interno','Certificado vencido impide firma de facturas en CBB.','Alto','Alta','Cerrado',2,'2026-02-28 13:00:00','2026-02-28 13:20:00','2026-02-28 16:00:00','2026-02-28 16:30:00'),

-- MARZO 2026 (9 tickets)
('INC-2026-00025',1,1,1,1,2,'Interno','Error 403 en Ballivián. CUFD no válido para el día.','Alto','Alta','Resuelto',2,'2026-03-03 09:00:00','2026-03-03 09:15:00','2026-03-03 11:30:00',NULL),
('INC-2026-00026',10,2,6,2,3,'Interno','Sin conexión a servidores SIN en Sopocachi.','Alto','Alta','Resuelto',2,'2026-03-05 08:00:00','2026-03-05 08:15:00','2026-03-05 10:30:00',NULL),
('INC-2026-00027',4,3,2,3,4,'Interno','Error al enviar formulario 200 mes de marzo.','Medio','Media','Resuelto',4,'2026-03-07 10:00:00','2026-03-07 10:30:00','2026-03-07 14:00:00',NULL),
('INC-2026-00028',6,1,4,1,2,'Interno','Certificado digital vencido en equipo de facturación.','Medio','Media','Resuelto',4,'2026-03-10 09:00:00','2026-03-10 09:30:00','2026-03-10 13:00:00',NULL),
('INC-2026-00029',2,4,1,4,3,'Interno','Caída del SIAT en El Alto. Afecta turno mañana.','Critico','Urgente','Resuelto',1,'2026-03-12 07:45:00','2026-03-12 07:50:00','2026-03-12 09:30:00',NULL),
('INC-2026-00030',11,3,6,2,5,'Interno','Red LAN caída en distrital CBB piso 1.','Alto','Alta','Resuelto',2,'2026-03-15 11:00:00','2026-03-15 11:20:00','2026-03-15 14:00:00',NULL),
('INC-2026-00031',5,2,1,3,2,'Interno','Factura rechazada por código actividad no habilitado.','Medio','Media','Resuelto',4,'2026-03-18 14:00:00','2026-03-18 14:30:00','2026-03-18 17:00:00',NULL),
('INC-2026-00032',8,1,3,1,4,'Interno','Padrón no sincroniza tras actualización del sistema.','Medio','Media','En_Proceso',4,'2026-03-22 10:00:00','2026-03-22 10:30:00',NULL,NULL),
('INC-2026-00033',7,1,7,1,1,'Interno','Acceso sospechoso detectado en servidor de auditoría.','Critico','Urgente','En_Proceso',1,'2026-03-28 15:00:00','2026-03-28 15:05:00',NULL,NULL),

-- ABRIL 2026 (tickets recientes — algunos abiertos)
('INC-2026-00034',1,1,1,1,2,'Interno','Error 403 token al iniciar semana de declaraciones.','Alto','Alta','Resuelto',2,'2026-04-02 08:30:00','2026-04-02 08:45:00','2026-04-02 10:30:00',NULL),
('INC-2026-00035',3,2,1,2,3,'Interno','SIAT caído en Sopocachi por mantenimiento no programado.','Critico','Urgente','Resuelto',1,'2026-04-05 09:00:00','2026-04-05 09:10:00','2026-04-05 11:00:00',NULL),
('INC-2026-00036',10,3,6,3,4,'Interno','Sin acceso a servidores SIN desde distrital CBB.','Alto','Alta','En_Proceso',2,'2026-04-08 10:00:00','2026-04-08 10:15:00',NULL,NULL),
('INC-2026-00037',6,1,4,1,NULL,'Interno','Certificado vencido reportado por técnico de turno.','Medio','Media','Abierto',4,'2026-04-10 09:00:00',NULL,NULL,NULL),
('INC-2026-00038',4,4,2,4,NULL,'Interno','Formulario 605 rechazado en El Alto. Error desconocido.','Medio','Media','Abierto',4,'2026-04-12 14:00:00',NULL,NULL,NULL),
('INC-2026-00039',2,2,1,2,3,'Interno','SIAT intermitente en Sopocachi. Reportado por equipo.','Alto','Alta','En_Proceso',2,'2026-04-15 08:00:00','2026-04-15 08:10:00',NULL,NULL),
('INC-2026-00040',9,1,1,1,NULL,'Interno','Error de validación en monto. Campo negativo detectado.','Bajo','Baja','Abierto',8,'2026-04-18 11:00:00',NULL,NULL,NULL);

-- Restaurar el trigger de código automático
DELIMITER $$
CREATE TRIGGER trg_codigo_incidente
BEFORE INSERT ON incidentes
FOR EACH ROW
BEGIN
    DECLARE next_num INT;
    SELECT COUNT(*) + 1 INTO next_num FROM incidentes;
    SET NEW.codigo = CONCAT('INC-', YEAR(NOW()), '-', LPAD(next_num, 5, '0'));
END$$
DELIMITER ;
