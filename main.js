// Shared JavaScript used across all pages
function toggleTheme(){
    const body = document.body;
    const current = body.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    body.setAttribute('data-theme', next);
    localStorage.setItem('theme', next);
}

function initTheme(){
    const saved = localStorage.getItem('theme');
    if(saved) document.body.setAttribute('data-theme', saved);
}

function setPrimaryColor(color){
    document.documentElement.style.setProperty('--primary', color);
    localStorage.setItem('primaryColor', color);
}

function initPrimaryColor(){
    const saved = localStorage.getItem('primaryColor');
    if(saved) document.documentElement.style.setProperty('--primary', saved);
}

function goToNewUser(){
    window.location.href = 'nuevo_usuario.html';
}

function goToLogs(){
    window.location.href = 'logs.html';
}

function clearCache(){
    alert('Caché de incidentes resueltos limpiada');
}

// Knowledge base helpers (will only run if relevant elements exist)
const articles = {
    'error403': `
        <span class="category-tag">Facturación Electrónica</span>
        <h1>Error 403: Forbidden</h1>
        <hr style="margin: 1rem 0;">
        <p>Este error ocurre cuando el certificado digital ha expirado o el Cufd no es válido para el día actual.</p>
        <h3 style="margin-top: 1rem;">Pasos para la solución:</h3>
        <ol style="margin-left: 1.5rem; margin-top: 0.5rem;">
            <li>Verificar vigencia del Token en el portal SIAT.</li>
            <li>Sincronizar fecha y hora del servidor local con el servidor del SIN.</li>
            <li>Generar un nuevo código CUFD mediante el servicio de sincronización.</li>
        </ol>
        <div class="code-block">
            GET /api/v2/facturacion/sincronizacion/cufd<br>
            Authorization: Bearer [TOKEN_ACTUAL]
        </div>
    `,
    'token': `
        <h1>Drivers Token v3</h1>
        <p>Instrucciones para técnicos de Nivel 1.</p>
        <p>Descargar el paquete de drivers desde la intranet e instalar con permisos de administrador.</p>
    `,
    'error500': `
        <span class="category-tag">Servidor</span>
        <h1>Error 500: Internal Server Error</h1>
        <hr style="margin: 1rem 0;">
        <p>Este error indica un problema interno del servidor, generalmente relacionado con la base de datos o configuración del sistema.</p>
        <h3 style="margin-top: 1rem;">Causas comunes:</h3>
        <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
            <li>Conexión a base de datos fallida.</li>
            <li>Configuración incorrecta de parámetros del sistema.</li>
            <li>Problemas de memoria o recursos del servidor.</li>
        </ul>
        <h3>Solución:</h3>
        <ol style="margin-left: 1.5rem;">
            <li>Revisar logs del servidor para detalles específicos.</li>
            <li>Verificar conectividad a la base de datos.</li>
            <li>Reiniciar servicios relacionados.</li>
        </ol>
    `,
    'certificado': `
        <span class="category-tag">Certificación Digital</span>
        <h1>Certificado Digital Expirado</h1>
        <hr style="margin: 1rem 0;">
        <p>Los certificados digitales tienen una vigencia limitada y deben renovarse periódicamente.</p>
        <h3>Pasos para renovación:</h3>
        <ol style="margin-left: 1.5rem; margin-top: 0.5rem;">
            <li>Acceder al portal de certificación del SIN.</li>
            <li>Solicitar renovación del certificado.</li>
            <li>Instalar el nuevo certificado en el sistema.</li>
            <li>Actualizar configuraciones en la aplicación.</li>
        </ol>
        <div class="code-block">
            POST /api/v2/certificacion/renovar<br>
            Body: { "certificado_id": "ID_DEL_CERTIFICADO" }
        </div>
    `,
    'sincronizacion': `
        <span class="category-tag">Sincronización</span>
        <h1>Problemas de Sincronización con SIN</h1>
        <hr style="margin: 1rem 0;">
        <p>La sincronización con el Servicio de Impuestos Nacionales es crucial para mantener la integridad de los datos fiscales.</p>
        <h3>Errores comunes:</h3>
        <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
            <li>Diferencia de fecha/hora entre sistemas.</li>
            <li>Problemas de conectividad a internet.</li>
            <li>Certificados no válidos.</li>
        </ul>
        <h3>Soluciones:</h3>
        <ol style="margin-left: 1.5rem;">
            <li>Sincronizar reloj del sistema con NTP.</li>
            <li>Verificar conexión a internet y firewalls.</li>
            <li>Renovar certificados si es necesario.</li>
        </ol>
    `,
    'validacion': `
        <span class="category-tag">Validación</span>
        <h1>Errores de Validación de Datos</h1>
        <hr style="margin: 1rem 0;">
        <p>Los datos enviados al SIN deben cumplir con formatos específicos para ser aceptados.</p>
        <h3>Validaciones comunes:</h3>
        <ul style="margin-left: 1.5rem; margin-top: 0.5rem;">
            <li>NIT del contribuyente debe ser válido.</li>
            <li>Fechas deben estar en formato correcto.</li>
            <li>Montos deben ser numéricos positivos.</li>
        </ul>
        <h3>Corrección:</h3>
        <ol style="margin-left: 1.5rem;">
            <li>Revisar formato de datos de entrada.</li>
            <li>Usar herramientas de validación integradas.</li>
            <li>Consultar documentación del SIN para formatos.</li>
        </ol>
    `,
    'conexion': `
        <span class="category-tag">Conectividad</span>
        <h1>Pérdida de Conexión al SIN</h1>
        <hr style="margin: 1rem 0;">
        <p>Problemas de conectividad pueden impedir el envío de facturas electrónicas.</p>
        <h3>Diagnóstico:</h3>
        <ol style="margin-left: 1.5rem; margin-top: 0.5rem;">
            <li>Verificar conexión a internet.</li>
            <li>Probar ping a servidores del SIN.</li>
            <li>Revisar configuración de proxy/firewall.</li>
        </ol>
        <h3>Soluciones:</h3>
        <ul style="margin-left: 1.5rem;">
            <li>Configurar VPN si es necesario.</li>
            <li>Actualizar certificados de seguridad.</li>
            <li>Contactar soporte técnico del SIN.</li>
        </ul>
    `
};

function openArticle(id){
    const viewer = document.getElementById('articleViewer');
    const content = document.getElementById('articleContent');
    if(viewer && content){
        content.innerHTML = articles[id] || "<h1>Contenido en desarrollo</h1><p>Pronto se añadirán más detalles.</p>";
        viewer.style.display = 'block';
    }
}
function closeArticle(){
    const viewer = document.getElementById('articleViewer');
    if(viewer) viewer.style.display = 'none';
}
function filterCards(query){
    const cards = document.querySelectorAll('.kb-card');
    cards.forEach(card => {
        const title = card.querySelector('h3').innerText.toLowerCase();
        card.style.display = title.includes(query.toLowerCase()) ? 'block' : 'none';
    });
}

// mark active nav link
function markActiveLink(){
    const links = document.querySelectorAll('.nav-links a');
    const current = window.location.pathname.split('/').pop();
    links.forEach(a=>{
        if(a.getAttribute('href') === current){
            a.parentElement.classList.add('active');
        }
    });
}

// ---- data helpers ----
function getUsers(){ return JSON.parse(localStorage.getItem('users')||'[]'); }
function saveUsers(u){ localStorage.setItem('users',JSON.stringify(u)); }
function addUser(user){ const u=getUsers(); u.push(user); saveUsers(u); loadUsers(); }
function updateUser(i,user){ const u=getUsers(); u[i]=user; saveUsers(u); loadUsers(); }
function loadUsers(){ const tbody=document.querySelector('#userTable tbody'); if(!tbody) return; tbody.innerHTML=''; getUsers().forEach((u,i)=>{ const row=tbody.insertRow(); row.innerHTML=`<td>${u.name}</td><td>${u.role}</td><td><button onclick="editUser(${i})">✏️</button> <button onclick="showUserIncidents(${i})">📄</button></td>`; }); populateUserSelect(); updateStats(); }
function populateUserSelect(){ const sel=document.getElementById('userSelect'); if(!sel) return; sel.innerHTML='<option value="">--Seleccione usuario--</option>'; getUsers().forEach((u,i)=>sel.innerHTML+=`<option value="${i}">${u.name}</option>`); }
function showUserForm(){
    // redirect to dedicated page for creating user
    window.location.href = 'nuevo_usuario.html';
}
function editUser(i){ const u=getUsers()[i]; const name=prompt('Nombre',u.name); const role=prompt('Rol',u.role); if(name && role){ updateUser(i,{name,role}); } }
function promptNewUser(){ showUserForm(); }

function showUserIncidents(index){ const users=getUsers(); const user=users[index]; const incidents=getIncidents().filter(i=>i.user===user.name); alert('Incidentes de '+user.name+':\n'+JSON.stringify(incidents, null, 2)); }

function getIncidents(){ return JSON.parse(localStorage.getItem('incidents')||'[]'); }
function saveIncidents(arr){ localStorage.setItem('incidents',JSON.stringify(arr)); }
function addIncident(){ const userIndex=document.getElementById('userSelect').value; if(userIndex===''){alert('Seleccione usuario'); return;} const users=getUsers(); const user=users[userIndex]; const categoria=document.getElementById('categoria').value; const prioridad=document.getElementById('prioridad').value; const desc=document.getElementById('descripcion').value; const incidents=getIncidents(); const id=Math.floor(Math.random()*90000)+10000; incidents.push({id,user:user.name,categoria,prioridad,descripcion:desc,estado:'⏳ Abierto'}); saveIncidents(incidents); loadIncidents(); initSupportChart(); document.getElementById('incidentForm').reset(); }
function loadIncidents(){ const tbody=document.querySelector('#incidentTable tbody'); if(!tbody) return; tbody.innerHTML=''; getIncidents().forEach(i=>{ const row=tbody.insertRow(); row.innerHTML=`<td>#${i.id}</td><td>${i.user}</td><td>${i.categoria}</td><td><span class="badge ${i.prioridad==='Alta'?'high':'med'}">${i.prioridad}</span></td><td>${i.estado}</td>`; }); updateStats(); }
function updateStats(){ const incidents=getIncidents(); const open=incidents.filter(i=>i.estado.includes('Abierto')).length; const counter=document.getElementById('count-open'); if(counter) counter.innerText=open; const ucount=document.getElementById('count-users'); if(ucount) ucount.innerText=getUsers().length; }

function initData(){ if(!localStorage.getItem('users')){ fetch('data.json').then(r=>r.json()).then(d=>{ if(d.users) localStorage.setItem('users',JSON.stringify(d.users)); if(d.incidents) localStorage.setItem('incidents',JSON.stringify(d.incidents)); loadUsers(); loadIncidents(); }); } else{ loadUsers(); loadIncidents(); } }

// attach listeners on DOM ready
function initSupportChart(){
    const canvas = document.getElementById('supportChart');
    if(!canvas) return;
    const ctx = canvas.getContext('2d');
    const incidents = getIncidents();
    const resueltos = incidents.filter(i => i.estado.includes('Resuelto') || i.estado.includes('Cerrado')).length;
    const proceso = incidents.filter(i => i.estado.includes('Proceso')).length;
    const pendientes = incidents.filter(i => i.estado.includes('Abierto')).length;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Resueltos', 'En Proceso', 'Pendientes'],
            datasets: [{
                data: [resueltos, proceso, pendientes],
                backgroundColor: ['#2e7d32','#f9a825','#d32f2f'],
                borderWidth:0
            }]
        },
        options: {
            responsive:true,
            maintainAspectRatio:false,
            plugins:{legend:{position:'bottom'}}
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initPrimaryColor();
    markActiveLink();
    initData();
    initSupportChart();
    const themeBtn = document.querySelector('.btn-theme');
    if(themeBtn) themeBtn.addEventListener('click', toggleTheme);
    const searchInput = document.querySelector('input[placeholder*="Buscar"]') || document.querySelector('input[onkeyup]');
    if(searchInput && typeof searchInput.onkeyup === 'function') {
        searchInput.onkeyup = function(){ filterCards(this.value); };
    }
    // Initialize color picker if on personalization page
    const picker = document.getElementById('primaryColorPicker');
    if(picker){
        picker.addEventListener('input', e => setPrimaryColor(e.target.value));
        picker.value = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim();
    }
});
