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
function addIncident(){ const userIndex=document.getElementById('userSelect').value; if(userIndex===''){alert('Seleccione usuario'); return;} const users=getUsers(); const user=users[userIndex]; const categoria=document.getElementById('categoria').value; const prioridad=document.getElementById('prioridad').value; const desc=document.getElementById('descripcion').value; const incidents=getIncidents(); const id=Math.floor(Math.random()*90000)+10000; incidents.push({id,user:user.name,categoria,prioridad,descripcion:desc,estado:'⏳ Abierto'}); saveIncidents(incidents); loadIncidents(); document.getElementById('incidentForm').reset(); }
function loadIncidents(){ const tbody=document.querySelector('#incidentTable tbody'); if(!tbody) return; tbody.innerHTML=''; getIncidents().forEach(i=>{ const row=tbody.insertRow(); row.innerHTML=`<td>#${i.id}</td><td>${i.user}</td><td>${i.categoria}</td><td><span class="badge ${i.prioridad==='Alta'?'high':'med'}">${i.prioridad}</span></td><td>${i.estado}</td>`; }); updateStats(); }
function updateStats(){ const incidents=getIncidents(); const open=incidents.filter(i=>i.estado.includes('Abierto')).length; const counter=document.getElementById('count-open'); if(counter) counter.innerText=open; const ucount=document.getElementById('count-users'); if(ucount) ucount.innerText=getUsers().length; }

function initData(){ if(!localStorage.getItem('users')){ fetch('data.json').then(r=>r.json()).then(d=>{ if(d.users) localStorage.setItem('users',JSON.stringify(d.users)); if(d.incidents) localStorage.setItem('incidents',JSON.stringify(d.incidents)); loadUsers(); loadIncidents(); }); } else{ loadUsers(); loadIncidents(); } }

// attach listeners on DOM ready
function initSupportChart(){
    const canvas = document.getElementById('supportChart');
    if(!canvas) return;
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Resueltos', 'En Proceso', 'Pendientes'],
            datasets: [{
                data: [65,20,15],
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
});
