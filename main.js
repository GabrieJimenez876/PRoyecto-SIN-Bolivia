// main.js — SIGI-SIN Bolivia

// ── Aplicar tema ANTES del primer render (evita parpadeo) ──
(function () {
    var saved = localStorage.getItem('sigi_theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);
    document.documentElement.classList.add('no-transitions');
    window.addEventListener('load', function () {
        setTimeout(function () {
            document.documentElement.classList.remove('no-transitions');
        }, 50);
    });
})();

// ── Tema claro / oscuro ────────────────────────────────────
function toggleTheme() {
    var root    = document.documentElement;
    var current = root.getAttribute('data-theme') || 'light';
    var next    = current === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    localStorage.setItem('sigi_theme', next);
}

function initTheme() {
    var saved = localStorage.getItem('sigi_theme');
    if (saved) document.documentElement.setAttribute('data-theme', saved);
}

// ── Marcar enlace activo en sidebar ───────────────────────
function markActiveLink() {
    var current = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.nav-links a').forEach(function (a) {
        if (a.getAttribute('href') === current) {
            a.closest('li').classList.add('active');
        }
    });
}

// ── Idioma (Español / English) ─────────────────────────────
function setLang(lang) {
    localStorage.setItem('sigi_lang', lang);
    document.documentElement.setAttribute('data-lang', lang);
    document.querySelectorAll('.lang-btn').forEach(function (b) {
        b.classList.toggle('active', b.dataset.lang === lang);
    });
    applyLang(lang);
}

function applyLang(lang) {
    // Reemplaza texto de elementos con data-es / data-en
    document.querySelectorAll('[data-es][data-en]').forEach(function (el) {
        el.textContent = lang === 'en' ? el.dataset.en : el.dataset.es;
    });
}

// ── Glosario: marca términos técnicos con tooltip bilingüe ─
function g(termEn, termEs, definicion) {
    return '<span class="glos" data-def="' + termEs + ' \u2014 ' + definicion + '">' + termEn + '</span>';
}

// ── KEDB: artículos con glosario integrado ─────────────────
var articles = {

    'error403':
        '<span class="category-tag">Facturación Electrónica</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Error 403: ' + g('Token','Clave','Dispositivo físico o código que certifica la identidad digital del usuario') + ' o CUFD Inválido</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>Este error se produce cuando el certificado digital ha expirado o el código CUFD no es válido para la fecha actual en el sistema ' + g('SIAT','SIAT','Sistema de Impuestos y Administración Tributaria del SIN') + '.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Pasos para la resolución:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Verificar vigencia del ' + g('Token','Clave','Dispositivo físico o código que certifica la identidad digital del usuario') + ' en el portal SIAT.</li>' +
        '<li>Sincronizar fecha y hora del ' + g('Server','servidor','Computadora central que provee servicios a otros equipos en la red') + ' local con el ' + g('NTP','Network Time Protocol','Protocolo de red para sincronizar relojes de sistemas informáticos') + ' del SIN.</li>' +
        '<li>Generar un nuevo código CUFD mediante el servicio de sincronización.</li>' +
        '<li>Reiniciar el servicio de facturación electrónica.</li>' +
        '</ol>' +
        '<div class="code-block">GET /api/v2/facturacion/sincronizacion/cufd<br>Authorization: Bearer [TOKEN_ACTUAL]</div>',

    'token':
        '<span class="category-tag">Seguridad Digital</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Reinstalación de ' + g('Driver','Controlador','Programa que permite al sistema operativo comunicarse con un dispositivo de hardware') + ' Token v3</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>Procedimiento estándar para actualizar los controladores de firma digital en equipos ' + g('Windows','Windows','Sistema operativo desarrollado por Microsoft') + ' 10/11.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Pasos:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Desinstalar ' + g('Driver','Controlador','Programa que permite al sistema operativo comunicarse con un dispositivo de hardware') + ' anteriores desde el Panel de Control.</li>' +
        '<li>Descargar el paquete de ' + g('Driver','Controlador','Programa que permite al sistema operativo comunicarse con un dispositivo de hardware') + ' v3 desde la intranet del SIN.</li>' +
        '<li>Ejecutar el instalador con permisos de administrador.</li>' +
        '<li>Reiniciar el equipo y verificar reconocimiento del ' + g('Token','Clave','Dispositivo físico o código que certifica la identidad digital del usuario') + '.</li>' +
        '</ol>',


    'error500':
        '<span class="category-tag">Servidor</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Error 500: Internal ' + g('Server','Servidor','Computadora central que procesa peticiones y provee servicios a otros equipos en la red') + ' Error</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>Indica un fallo interno del ' + g('Server','Servidor','Computadora central que procesa peticiones y provee servicios a otros equipos en la red') + ', generalmente relacionado con la ' + g('Database','Base de Datos','Sistema estructurado que almacena y organiza la información para su consulta') + ' o configuración del SIAT.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Causas comunes:</h3>' +
        '<ul style="margin-left:1.5rem;line-height:2;">' +
        '<li>Conexión a ' + g('Database','Base de Datos','Sistema estructurado que almacena y organiza la información para su consulta') + ' fallida o tiempo de espera excedido.</li>' +
        '<li>Parámetros de configuración incorrectos.</li>' +
        '<li>Falta de memoria o recursos en el ' + g('Server','Servidor','Computadora central que procesa peticiones y provee servicios a otros equipos en la red') + '.</li>' +
        '</ul>' +
        '<h3 style="margin:1rem 0 .5rem;">Solución:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Revisar ' + g('Log','Registro / Bitácora','Archivo que guarda automáticamente el historial de eventos y errores de un sistema') + ' del servidor para identificar el error específico.</li>' +
        '<li>Verificar conectividad y disponibilidad de la ' + g('Database','Base de Datos','Sistema estructurado que almacena y organiza la información para su consulta') + '.</li>' +
        '<li>Reiniciar los servicios afectados con permisos de administrador.</li>' +
        '</ol>',


    'certificado':
        '<span class="category-tag">Certificación Digital</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Certificado Digital Expirado</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>Los ' + g('Certificate','Certificado Digital','Archivo electrónico que verifica la identidad de una persona para operaciones en línea') + ' tienen vigencia limitada y deben renovarse antes de su vencimiento.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Proceso de renovación:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Acceder al portal de certificación del SIN con usuario autorizado.</li>' +
        '<li>Solicitar la renovación presentando la documentación requerida.</li>' +
        '<li>Descargar e instalar el nuevo ' + g('Certificate','Certificado Digital','Archivo electrónico que verifica la identidad de una persona para operaciones en línea') + ' en el sistema.</li>' +
        '<li>Actualizar la configuración en la aplicación de facturación.</li>' +
        '</ol>' +
        '<div class="code-block">POST /api/v2/certificacion/renovar<br>Body: { "certificado_id": "ID_DEL_CERTIFICADO" }</div>',

    'sincronizacion':
        '<span class="category-tag">Sincronización</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Problemas de ' + g('Sync','Sincronización','Proceso de alinear datos o configuraciones entre dos o más sistemas') + ' con SIN</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>La sincronización correcta con los ' + g('Server','Servidor','Computadora central que procesa peticiones y provee servicios a otros equipos en la red') + ' del SIN es esencial para la integridad de los datos fiscales.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Errores frecuentes:</h3>' +
        '<ul style="margin-left:1.5rem;line-height:2;">' +
        '<li>Diferencia de fecha/hora entre el sistema local y el ' + g('Server','Servidor','Computadora central que procesa peticiones y provee servicios a otros equipos en la red') + '.</li>' +
        '<li>Problemas de conectividad o ' + g('Firewall','Cortafuegos','Sistema de seguridad que controla y filtra el tráfico de red según reglas definidas') + ' bloqueando puertos.</li>' +
        '<li>' + g('Certificate','Certificado Digital','Archivo electrónico que verifica la identidad de una persona para operaciones en línea') + ' no válidos o expirados.</li>' +
        '</ul>' +
        '<h3 style="margin:1rem 0 .5rem;">Soluciones:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Sincronizar el reloj con un servidor NTP confiable.</li>' +
        '<li>Verificar la conexión y revisar reglas del ' + g('Firewall','Cortafuegos','Sistema de seguridad que controla y filtra el tráfico de red según reglas definidas') + '.</li>' +
        '<li>Renovar ' + g('Certificate','Certificado Digital','Archivo electrónico que verifica la identidad de una persona para operaciones en línea') + ' antes de re-sincronizar.</li>' +
        '</ol>',

    'conexion':
        '<span class="category-tag">Conectividad</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Pérdida de Conexión a ' + g('Server','Servidor','Computadora central que procesa peticiones y provee servicios a otros equipos en la red') + ' SIN</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>La falta de conectividad impide el envío de facturas electrónicas.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Diagnóstico:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Verificar conexión a internet desde el equipo afectado.</li>' +
        '<li>Probar ' + g('Ping','Prueba de conectividad','Comando que verifica si un equipo puede comunicarse con otro en la red') + ' a los servidores del SIN.</li>' +
        '<li>Revisar proxy y ' + g('Firewall','Cortafuegos','Sistema de seguridad que controla y filtra el tráfico de red según reglas definidas') + '.</li>' +
        '</ol>' +
        '<h3 style="margin:1rem 0 .5rem;">Soluciones:</h3>' +
        '<ul style="margin-left:1.5rem;line-height:2;">' +
        '<li>Configurar ' + g('VPN','Red Privada Virtual','Conexión cifrada que permite acceder a la red interna de una organización de forma remota') + '.</li>' +
        '<li>Actualizar certificados del navegador.</li>' +
        '<li>Escalar al equipo de infraestructura si el problema persiste.</li>' +
        '</ul>',

    'declaracion':
        '<span class="category-tag">Declaraciones Juradas</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Error en Envío de Declaración Jurada</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>Ocurre cuando no se pueden enviar formularios tributarios (Form. 200, 400, 605) a través del portal del SIN.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Causas frecuentes:</h3>' +
        '<ul style="margin-left:1.5rem;line-height:2;">' +
        '<li>Período ya declarado anteriormente (duplicado).</li>' +
        '<li>NIT inhabilitado o con observaciones en el ' + g('System','Sistema','Conjunto de programas y componentes que trabajan juntos para cumplir una función') + '.</li>' +
        '<li>' + g('Session','Sesión','Período activo de conexión de un usuario al sistema, desde que inicia hasta que cierra sesión') + ' expirada en el portal.</li>' +
        '<li>Campos obligatorios del formulario incompletos.</li>' +
        '</ul>' +
        '<h3 style="margin:1rem 0 .5rem;">Solución:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Verificar el estado del NIT en el padrón biométrico.</li>' +
        '<li>Cerrar ' + g('Session','Sesión','Período activo de conexión de un usuario al sistema, desde que inicia hasta que cierra sesión') + ' y volver a ingresar.</li>' +
        '<li>Revisar que todos los campos del formulario estén correctamente llenados.</li>' +
        '<li>Si el período ya fue declarado, solicitar rectificatoria al área correspondiente.</li>' +
        '</ol>',

    'padron':
        '<span class="category-tag">Padrón Biométrico</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Fallo de Sincronización del Padrón</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>Los datos del padrón biométrico no sincronizan correctamente con el ' + g('Server','Servidor','Computadora central que procesa peticiones y provee servicios a otros equipos en la red') + ' central del SIN.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Síntomas:</h3>' +
        '<ul style="margin-left:1.5rem;line-height:2;">' +
        '<li>El NIT existe pero no aparece en el ' + g('System','Sistema','Conjunto de programas y componentes que trabajan juntos para cumplir una función') + '.</li>' +
        '<li>' + g('Data','Datos','Información almacenada en un sistema') + ' desactualizados.</li>' +
        '<li>Lectura biométrica no coincide con el registro.</li>' +
        '</ul>' +
        '<h3 style="margin:1rem 0 .5rem;">Pasos de solución:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Verificar conectividad con el ' + g('Server','Servidor','Computadora central que procesa peticiones y provee servicios a otros equipos en la red') + ' central del padrón.</li>' +
        '<li>Ejecutar el proceso de sincronización manual desde el módulo de administración.</li>' +
        '<li>Si persiste, escalar al equipo de ' + g('Database','Base de Datos','Sistema estructurado que almacena y organiza la información para su consulta') + '.</li>' +
        '<li>En caso de datos incorrectos, solicitar actualización presencial en la distrital.</li>' +
        '</ol>',

    'factura_rechazada':
        '<span class="category-tag">Facturación Electrónica</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Factura Electrónica Rechazada por el SIN</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>La factura fue emitida pero el ' + g('SIAT','SIAT','Sistema de Administración Tributaria del SIN') + ' la rechazó durante la validación en línea.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Causas más comunes:</h3>' +
        '<ul style="margin-left:1.5rem;line-height:2;">' +
        '<li>CUFD vencido o no generado para el día actual.</li>' +
        '<li>NIT del comprador inválido o sin actividad.</li>' +
        '<li>Código de actividad económica no habilitado.</li>' +
        '<li>Monto o descuento mal calculado.</li>' +
        '</ul>' +
        '<h3 style="margin:1rem 0 .5rem;">Solución:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Revisar el mensaje de ' + g('Error','Error','Mensaje del sistema que indica que algo no funciona o no cumple el formato esperado') + ' devuelto por el SIAT.</li>' +
        '<li>Regenerar el CUFD del día si está vencido.</li>' +
        '<li>Corregir el NIT del comprador y reenviar la factura.</li>' +
        '<li>Consultar la tabla de códigos de rechazo en la documentación del SIAT v2.</li>' +
        '</ol>' +
        '<div class="code-block">GET /api/v2/facturacion/codigos-rechazo<br>Authorization: Bearer [TOKEN_ACTUAL]</div>',

    'validacion':
        '<span class="category-tag">Validación de Datos</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Errores de Formato y ' + g('Validation','Validación','Proceso automático de verificar que los datos ingresados cumplen con las reglas establecidas') + '</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>Los ' + g('Data','Datos','Información que se envía al sistema') + ' enviados deben cumplir formatos específicos para ser aceptados.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Validaciones más comunes:</h3>' +
        '<ul style="margin-left:1.5rem;line-height:2;">' +
        '<li>NIT debe ser numérico y válido en el padrón.</li>' +
        '<li>Fechas deben estar en formato <strong>YYYY-MM-DD</strong>.</li>' +
        '<li>Montos deben ser numéricos positivos con máximo 2 decimales.</li>' +
        '<li>Código de actividad económica debe existir en el catálogo del SIN.</li>' +
        '</ul>' +
        '<h3 style="margin:1rem 0 .5rem;">Corrección:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Revisar el campo específico que indica el mensaje de ' + g('Error','Error','Mensaje del sistema que indica que algo no funciona o no cumple el formato esperado') + '.</li>' +
        '<li>Usar herramientas de ' + g('Validation','Validación','Proceso automático de verificar que los datos ingresados cumplen con las reglas establecidas') + ' antes de enviar.</li>' +
        '<li>Consultar la documentación oficial del SIN.</li>' +
        '</ol>',

    'red_lan':
        '<span class="category-tag">Infraestructura</span>' +
        '<h1 style="margin:1rem 0 .5rem;">Problema de Red ' + g('LAN','Red de Área Local','Red que conecta equipos dentro de un mismo edificio o sede institucional') + '</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>Fallo de la ' + g('Network','Red','Conjunto de equipos informáticos interconectados que comparten recursos e información') + ' interna.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Diagnóstico inicial:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Identificar si el problema afecta a uno o varios equipos.</li>' +
        '<li>Verificar estado de ' + g('Switch','Conmutador','Dispositivo de red que conecta múltiples equipos dentro de una red local') + ' y ' + g('Router','Enrutador','Dispositivo que dirige el tráfico de datos entre la red local e internet') + '.</li>' +
        '<li>Revisar cables de red.</li>' +
        '<li>Probar ' + g('Ping','Prueba de conectividad','Comando que verifica si un equipo puede comunicarse con otro en la red') + '.</li>' +
        '</ol>' +
        '<h3 style="margin:1rem 0 .5rem;">Soluciones:</h3>' +
        '<ul style="margin-left:1.5rem;line-height:2;">' +
        '<li>Reiniciar equipos de red.</li>' +
        '<li>Revisar configuración ' + g('DHCP','Protocolo Config. Dinámica','Protocolo que asigna automáticamente direcciones IP a los equipos de una red') + '.</li>' +
        '<li>Reemplazar cable dañado.</li>' +
        '<li>Escalar si es problema de ' + g('WAN','Red de Área Amplia','Red que conecta múltiples ubicaciones geográficas') + '.</li>' +
        '</ul>',

    'acceso_no_autorizado':
        '<span class="category-tag">Seguridad</span>' +
        '<h1 style="margin:1rem 0 .5rem;">' + g('Unauthorized Access','Acceso No Autorizado','Acción realizada sin los permisos necesarios del sistema') + '</h1>' +
        '<hr style="margin-bottom:1rem;border-color:var(--border);">' +
        '<p>Intentos sospechosos en el ' + g('System','Sistema','Conjunto de programas y componentes que trabajan juntos para cumplir una función') + '.</p>' +
        '<h3 style="margin:1rem 0 .5rem;">Acciones:</h3>' +
        '<ol style="margin-left:1.5rem;line-height:2;">' +
        '<li>Revisar ' + g('Audit','Auditoría','Proceso de revisar y registrar todas las acciones realizadas en un sistema') + '.</li>' +
        '<li>Bloquear la cuenta.</li>' +
        '<li>Notificar al administrador.</li>' +
        '</ol>' +
        '<h3 style="margin:1rem 0 .5rem;">Seguimiento:</h3>' +
        '<ul style="margin-left:1.5rem;line-height:2;">' +
        '<li>Registrar dirección IP.</li>' +
        '<li>Verificar ' + g('Credentials','Credenciales','Conjunto de usuario y contraseña que permite identificarse y acceder a un sistema') + '.</li>' +
        '<li>Restablecer contraseña.</li>' +
        '</ul>'
};

function openArticle(id) {
    var viewer  = document.getElementById('articleViewer');
    var content = document.getElementById('articleContent');
    if (viewer && content) {
        // Limpiar glosario anterior
        var oldGlossary = viewer.querySelector('.article-glossary-section');
        if (oldGlossary) {
            oldGlossary.remove();
        }
        
        content.innerHTML = articles[id] || '<h2>Contenido en desarrollo</h2><p>Este artículo estará disponible próximamente.</p>';
        viewer.style.display = 'block';
        
        // Construir glosario con solo los términos usados en este artículo
        buildArticleGlossary(viewer, content);
    }
}

function buildArticleGlossary(viewer, content) {
    // Obtener términos únicos del artículo (sin duplicados)
    var glossaryTerms = {};
    content.querySelectorAll('.glos').forEach(function (term) {
        var termEn = term.textContent; // Término en inglés
        var definition = term.getAttribute('data-def'); // "TermEs — Definición"
        if (definition) {
            var parts = definition.split(' \u2014 ');
            var termEs = parts[0] || '';
            var termDef = parts[1] || definition;
            // Guardar con clave única para evitar duplicados
            glossaryTerms[termEn] = {
                es: termEs,
                def: termDef
            };
        }
    });
    
    // Si hay términos, construir la sección de glosario
    if (Object.keys(glossaryTerms).length > 0) {
        // Ordenar términos alfabéticamente
        var sortedTerms = Object.keys(glossaryTerms).sort();
        
        var glossarySection = document.createElement('div');
        glossarySection.className = 'article-glossary-section';
        
        var title = document.createElement('h4');
        title.textContent = 'Términos utilizados';
        glossarySection.appendChild(title);
        
        var grid = document.createElement('div');
        grid.className = 'article-glossary-grid';
        
        // Agregar solo los términos que se usan
        sortedTerms.forEach(function (termEn) {
            var item = document.createElement('div');
            item.className = 'article-glossary-item';
            
            var label = document.createElement('div');
            label.className = 'term-label';
            // Formato: "Server (Servidor)"
            label.textContent = termEn + ' (' + glossaryTerms[termEn].es + ')';
            
            var definition = document.createElement('div');
            definition.className = 'term-definition';
            definition.textContent = glossaryTerms[termEn].def;
            
            item.appendChild(label);
            item.appendChild(definition);
            grid.appendChild(item);
        });
        
        glossarySection.appendChild(grid);
        viewer.appendChild(glossarySection);
    }
}

function closeArticle() {
    var viewer = document.getElementById('articleViewer');
    if (viewer) viewer.style.display = 'none';
}

function filterCards(query) {
    document.querySelectorAll('.kb-card').forEach(function (card) {
        var text = card.innerText.toLowerCase();
        card.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
}

// ── Inicialización ─────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    initTheme();
    markActiveLink();

    // Idioma guardado
    var savedLang = localStorage.getItem('sigi_lang') || 'es';
    document.documentElement.setAttribute('data-lang', savedLang);
    document.querySelectorAll('.lang-btn').forEach(function (b) {
        b.classList.toggle('active', b.dataset.lang === savedLang);
    });
    applyLang(savedLang);

    // Color picker (solo en configuración)
    var picker = document.getElementById('primaryColorPicker');
    if (picker) {
        var savedColor = localStorage.getItem('sigi_color');
        if (savedColor) {
            document.documentElement.style.setProperty('--primary', savedColor);
            picker.value = savedColor;
        }
        picker.addEventListener('input', function (e) {
            document.documentElement.style.setProperty('--primary', e.target.value);
            localStorage.setItem('sigi_color', e.target.value);
        });
    }
});
