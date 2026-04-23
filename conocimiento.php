<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
requireAuth();
$paginaActiva = 'conocimiento';
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Base de Conocimientos | SIGI — SIN Bolivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<main class="main-content">
    <div class="page-header">
        <div>
            <h1>Base de Conocimientos (KEDB)</h1>
            <p>Repositorio de soluciones documentadas para incidentes recurrentes del SIN</p>
        </div>
        
    </div>

    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-body">
            <label class="form-label">Buscar solución por palabra clave</label>
            <input type="text" placeholder="Ej: Error 403, token, certificado, SIAT..."
                   oninput="filterCards(this.value)" style="max-width:500px;">
        </div>
    </div>

    <div class="kb-grid">
        <div class="kb-card" onclick="openArticle('error403')">
            <span class="category-tag">Facturación Electrónica</span>
            <h3>Error 403: Token o CUFD Inválido</h3>
            <p>Guía para resolver el rechazo de tokens en el servicio web de facturación del SIAT.</p>
        </div>
        <div class="kb-card" onclick="openArticle('token')">
            <span class="category-tag">Seguridad Digital</span>
            <h3>Reinstalación de Drivers de Token v3</h3>
            <p>Procedimiento para la actualización de controladores de firma digital en Windows 10/11.</p>
        </div>
        <div class="kb-card" onclick="openArticle('error500')">
            <span class="category-tag">Servidor</span>
            <h3>Error 500: Internal Server Error</h3>
            <p>Diagnóstico y resolución de errores internos en el servidor del sistema SIAT.</p>
        </div>
        <div class="kb-card" onclick="openArticle('certificado')">
            <span class="category-tag">Certificación Digital</span>
            <h3>Certificado Digital Expirado</h3>
            <p>Proceso completo para renovar certificados digitales en el sistema del SIN.</p>
        </div>
        <div class="kb-card" onclick="openArticle('sincronizacion')">
            <span class="category-tag">Sincronización</span>
            <h3>Problemas de Sincronización con SIN</h3>
            <p>Soluciones para mantener la sincronización correcta con los servidores del SIN.</p>
        </div>
        <div class="kb-card" onclick="openArticle('conexion')">
            <span class="category-tag">Conectividad</span>
            <h3>Pérdida de Conexión a Servidores SIN</h3>
            <p>Diagnóstico y restauración de conectividad con los servidores centrales del SIN.</p>
        </div>
        <div class="kb-card" onclick="openArticle('declaracion')">
            <span class="category-tag">Declaraciones Juradas</span>
            <h3>Error en Envío de Declaración Jurada</h3>
            <p>Corrección de errores al llenar y enviar formularios tributarios (Form. 200, 400, etc.).</p>
        </div>
        <div class="kb-card" onclick="openArticle('padron')">
            <span class="category-tag">Padrón Biométrico</span>
            <h3>Fallo de Sincronización del Padrón</h3>
            <p>Solución cuando los datos biométricos del contribuyente no sincronizan correctamente.</p>
        </div>
        <div class="kb-card" onclick="openArticle('factura_rechazada')">
            <span class="category-tag">Facturación Electrónica</span>
            <h3>Factura Electrónica Rechazada por el SIN</h3>
            <p>Causas y solución cuando el SIN rechaza una factura ya emitida por el contribuyente.</p>
        </div>
        <div class="kb-card" onclick="openArticle('validacion')">
            <span class="category-tag">Validación de Datos</span>
            <h3>Errores de Formato y Validación de Datos</h3>
            <p>Corrección de campos con formato incorrecto: NIT, fechas, montos y códigos de actividad.</p>
        </div>
        <div class="kb-card" onclick="openArticle('red_lan')">
            <span class="category-tag">Infraestructura</span>
            <h3>Problema de Red LAN en la Distrital</h3>
            <p>Procedimiento para diagnosticar y restaurar la red interna de una distrital del SIN.</p>
        </div>
        <div class="kb-card" onclick="openArticle('acceso_no_autorizado')">
            <span class="category-tag">Seguridad</span>
            <h3>Intento de Acceso No Autorizado</h3>
            <p>Protocolo de respuesta ante intentos fallidos repetidos o accesos sospechosos al sistema.</p>
        </div>
    </div>

    <!-- ── Glosario de términos técnicos EN → ES ── -->
    <div class="card" style="margin-top:2rem;">
        <div class="card-header">
            <h2 class="card-title">Glosario de Términos Técnicos (EN &#8594; ES)</h2>
        </div>
        <div class="card-body">
            <p style="font-size:.83rem;color:var(--text-muted);margin-bottom:1rem;">
                Vocabulario en inglés normalizado en soporte técnico informático. Al abrir cualquier
                artículo del KEDB, los términos subrayados muestran su traducción al pasar el cursor.
            </p>
            <div class="glossary-grid">
                <div class="glossary-term"><div class="term-en">Server</div><div class="term-es">Servidor</div><div class="term-def">Computadora central que procesa peticiones y provee servicios a otros equipos en la red.</div></div>
                <div class="glossary-term"><div class="term-en">Network</div><div class="term-es">Red</div><div class="term-def">Conjunto de equipos informáticos interconectados que comparten recursos e información.</div></div>
                <div class="glossary-term"><div class="term-en">Error</div><div class="term-es">Error</div><div class="term-def">Mensaje del sistema que indica que algo no funciona o no cumple el formato esperado.</div></div>
                <div class="glossary-term"><div class="term-en">Ticket</div><div class="term-es">Solicitud / Incidente</div><div class="term-def">Registro formal de un problema técnico que requiere atención y seguimiento.</div></div>
                <div class="glossary-term"><div class="term-en">System</div><div class="term-es">Sistema</div><div class="term-def">Conjunto de programas y componentes que trabajan juntos para cumplir una función.</div></div>
                <div class="glossary-term"><div class="term-en">Update</div><div class="term-es">Actualización</div><div class="term-def">Proceso de instalar una nueva versión de un programa para corregir errores o agregar mejoras.</div></div>
                <div class="glossary-term"><div class="term-en">Driver</div><div class="term-es">Controlador</div><div class="term-def">Programa que permite al sistema operativo comunicarse con un dispositivo de hardware.</div></div>
                <div class="glossary-term"><div class="term-en">Token</div><div class="term-es">Token / Clave</div><div class="term-def">Dispositivo físico o código digital que certifica la identidad de un usuario.</div></div>
                <div class="glossary-term"><div class="term-en">Firewall</div><div class="term-es">Cortafuegos</div><div class="term-def">Sistema de seguridad que controla y filtra el tráfico de red según reglas definidas.</div></div>
                <div class="glossary-term"><div class="term-en">Database</div><div class="term-es">Base de Datos</div><div class="term-def">Sistema estructurado que almacena y organiza la información para su consulta.</div></div>
                <div class="glossary-term"><div class="term-en">Log</div><div class="term-es">Registro / Bitácora</div><div class="term-def">Archivo que guarda automáticamente el historial de eventos y errores de un sistema.</div></div>
                <div class="glossary-term"><div class="term-en">Sync</div><div class="term-es">Sincronización</div><div class="term-def">Proceso de alinear datos o configuraciones entre dos o más sistemas.</div></div>
                <div class="glossary-term"><div class="term-en">VPN</div><div class="term-es">Red Privada Virtual</div><div class="term-def">Conexión cifrada que permite acceder a la red interna de una organización de forma remota.</div></div>
                <div class="glossary-term"><div class="term-en">Ping</div><div class="term-es">Prueba de conectividad</div><div class="term-def">Comando que verifica si un equipo puede comunicarse con otro en la red.</div></div>
                <div class="glossary-term"><div class="term-en">LAN</div><div class="term-es">Red de Área Local</div><div class="term-def">Red que conecta equipos dentro de un mismo edificio o sede institucional.</div></div>
                <div class="glossary-term"><div class="term-en">WAN</div><div class="term-es">Red de Área Amplia</div><div class="term-def">Red que conecta múltiples sedes o ubicaciones geográficamente distantes.</div></div>
                <div class="glossary-term"><div class="term-en">Router</div><div class="term-es">Enrutador</div><div class="term-def">Dispositivo que dirige el tráfico de datos entre la red local e internet.</div></div>
                <div class="glossary-term"><div class="term-en">Switch</div><div class="term-es">Conmutador</div><div class="term-def">Dispositivo de red que conecta múltiples equipos dentro de una red local.</div></div>
                <div class="glossary-term"><div class="term-en">Certificate</div><div class="term-es">Certificado Digital</div><div class="term-def">Archivo electrónico que verifica la identidad de una persona para operaciones en línea.</div></div>
                <div class="glossary-term"><div class="term-en">Session</div><div class="term-es">Sesión</div><div class="term-def">Período activo de conexión de un usuario al sistema, desde que inicia hasta que cierra sesión.</div></div>
                <div class="glossary-term"><div class="term-en">Validation</div><div class="term-es">Validación</div><div class="term-def">Proceso automático de verificar que los datos ingresados cumplen con las reglas establecidas.</div></div>
                <div class="glossary-term"><div class="term-en">Credentials</div><div class="term-es">Credenciales</div><div class="term-def">Conjunto de usuario y contraseña que permite identificarse y acceder a un sistema.</div></div>
                <div class="glossary-term"><div class="term-en">Audit</div><div class="term-es">Auditoría</div><div class="term-def">Proceso de revisar y registrar todas las acciones realizadas en un sistema.</div></div>
                <div class="glossary-term"><div class="term-en">DHCP</div><div class="term-es">Protocolo Config. Dinámica</div><div class="term-def">Protocolo que asigna automáticamente direcciones IP a los equipos de una red.</div></div>
            </div>
        </div>
    </div>
</main>

<!-- Article viewer panel -->
<div id="articleViewer" class="article-viewer">
    <span class="close-btn" onclick="closeArticle()">✖ CERRAR</span>
    <div id="articleContent"></div>
</div>

<script src="main.js"></script>
</body>
</html>
