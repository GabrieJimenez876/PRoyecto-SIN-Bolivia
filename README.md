# SIGI-SIN — Sistema Integral de Gestión de Incidentes
### Servicio de Impuestos Nacionales · Bolivia
Materia: Programación en Internet | Fase 2: Base de Datos

---

## Tecnologías utilizadas

| Capa | Tecnología |
|------|-----------|
| Servidor local | Laragon Pro (Apache + MySQL) |
| Backend | PHP 8.x |
| Base de datos | MySQL 8.x (phpMyAdmin) |
| Frontend | HTML5, CSS3, JavaScript |
| Gráficos | Chart.js (CDN) |
| Iconos | Tabler Icons (SVG inline) |
| Fuente | Inter (Google Fonts) |

---

## Estructura del proyecto

```
SIGI-SIN-Fase2/
├── index.php              → Dashboard principal con KPIs y registro rápido
├── login.php              → Inicio de sesión con autenticación segura
├── logout.php             → Cierre de sesión
├── tickets.php            → Gestión y seguimiento de todos los tickets
├── conocimiento.php       → Base de conocimientos (KEDB) con 12 artículos
├── usuarios.php           → Administración del personal técnico
├── reportes.php           → Gráficas y tabla de eficiencia por distrital
├── configuracion.php      → Ajustes de seguridad, notificaciones y apariencia
├── logs.php               → Bitácora de auditoría del sistema
├── setup_passwords.php    → Script de configuración inicial (eliminar tras usar)
├── main.js                → JavaScript compartido (tema, artículos KEDB)
├── styles.css             → Hoja de estilos con soporte claro/oscuro
├── database_sigi_sin.sql  → Script SQL completo para phpMyAdmin
│
├── includes/
│   ├── db.php             → Conexión PDO a MySQL
│   ├── auth.php           → Manejo de sesiones y autenticación
│   └── sidebar.php        → Barra lateral reutilizable
│
└── api/
    ├── nuevo_incidente.php       → Endpoint AJAX: crear ticket
    ├── actualizar_estado.php     → Endpoint AJAX: cambiar estado
    └── buscar_contribuyente.php  → Endpoint AJAX: buscar por NIT
```

---

## Instalación paso a paso

### Requisitos previos
- Laragon instalado y corriendo (Apache + MySQL activos)
- Navegador web moderno
- phpMyAdmin accesible en `http://localhost/phpmyadmin`

---


### Paso 1 — Ingresar al sistema

```
http://localhost/SIGI-SIN-Fase2/login.php
```

---

## Credenciales de acceso

| Nombre | Correo | Contraseña | Nivel |
|--------|--------|-----------|-------|
| Gabriel Isaac Jimenez Tarqui | `gabriel.jimenez@sin.gob.bo` | `sGabrielSIN#24` | Administrador |
| Pamela Esther Canaza Luna | `pamela.canaza@sin.gob.bo` | `PamelaC@2026` | Nivel 3 |
| Jorge Luis Huanca Alarcon | `jorge.huanca@sin.gob.bo` | `JHuanca*SIN1` | Nivel 2 |
| Luis Donato Quispe Ramirez | `luis.quispe@sin.gob.bo` | `sin2026` | Nivel 1 |
| Juan Carlos Apaza Quispe | `juan.apaza@sin.gob.bo` | `Apaza2026#TI` | Nivel 1 |

---

## Modelo de base de datos

El modelo sigue una arquitectura de **modelo estrella** orientada a inteligencia de negocio:

```
[contribuyentes] ──┐
[tecnicos]        ─┤
[tipos_incidente] ─┼──► [incidentes] ◄── [distritales] ──► [departamentos]
[modulos_sin]     ─┘         │
                         [auditoria_log]
```

Cada incidente registra: tipo, severidad, prioridad, SLA en horas, distrital, módulo afectado, origen (externo/interno) y fechas de cada etapa del ciclo de vida.

---

## Cambiar el tema visual (claro/oscuro)

El cambio de tema está disponible únicamente en **Configuración** para mantener la interfaz limpia. El tema elegido se guarda automáticamente en el navegador y se aplica en todas las páginas.

---

## Notas de seguridad

- Las contraseñas se almacenan con hash **bcrypt** (costo 12)
- Los intentos de acceso fallidos quedan registrados en `auditoria_log`
- Todas las entradas de usuario pasan por `htmlspecialchars()` y consultas preparadas PDO
- El sistema bloquea rutas protegidas si no hay sesión activa (redirige a login)

---

## Autoría

Proyecto académico — Universidad Salesiana de Bolivia  
Carrera: Ingeniería de Sistemas  
Semestre: Octavo Semestre  
Sistema: SIGI — Gestión de Incidentes SIN Bolivia  
Año: 2026
