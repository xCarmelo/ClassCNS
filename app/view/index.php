<?php include 'header.php'; ?>

<link rel="stylesheet" href="<?= $base ?>/public/cssB/bootstrap.min.css">
<link href="<?= $base ?>/public/cssB/bootstrap-icons-1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="<?= $base ?>/public/css/styles.css">
<link rel="icon" type="image/png" href="<?= $base ?>/public/assets/logo-pestaña.png">

<style>
/* En Home, reducir el margen inferior del navbar */
nav.navbar { margin-bottom: 0rem !important; }
.home-hero {
    background: linear-gradient(135deg, #2c3e50 60%, #2980b9 100%);
    color: #fff;
    padding: 60px 0 40px 0;
    text-align: center;
    border-radius: 0 0 40px 40px;
    box-shadow: 0 8px 32px rgba(44,62,80,0.15);
}
.home-hero h1 {
    font-size: 3rem;
    font-weight: 700;
    margin-bottom: 20px;
    letter-spacing: 2px;
}
.home-hero p {
    font-size: 1.3rem;
    margin-bottom: 30px;
}
.home-cards {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
    margin-top: 40px;
}
.home-card {
    background: #fff;
    color: #2c3e50;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(44,62,80,0.10);
    padding: 30px 25px;
    width: 320px;
    transition: transform 0.2s, box-shadow 0.2s;
    text-align: left;
    position: relative;
}
.home-card:hover {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 8px 32px rgba(44,62,80,0.18);
}
.home-card i {
    font-size: 2.5rem;
    color: #2980b9;
    margin-bottom: 15px;
}
.home-card h3 {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 10px;
}
.home-card p {
    font-size: 1rem;
    margin-bottom: 15px;
}
.home-card a {
    color: #fff;
    background: #2980b9;
    padding: 8px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;
}
.home-card a:hover {
    background: #2c3e50;
}

/* Botón de vaciar base de datos */
.vaciar-db-container {
    display: flex;
    justify-content: center;
    margin: 40px 0;
}
.btn-vaciar-db {
    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
}
.btn-vaciar-db:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
    background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
}
.btn-vaciar-db:active {
    transform: translateY(0);
}
.btn-vaciar-db i {
    font-size: 1.3rem;
}

/* Estilos para modales */
.modal-danger .modal-header {
    background-color: #e74c3c;
    color: white;
}
.modal-warning .modal-header {
    background-color: #f39c12;
    color: white;
}
</style>

<div class="home-hero">
    <img src="/public/assets/logo.jpg" alt="Logo" style="width:90px; border-radius:50%; box-shadow:0 2px 12px #222; margin-bottom:20px;">
    <h1>Bienvenido a Mi Bitácora Digital</h1>
    <p>Gestión escolar moderna, intuitiva y segura.<br>Administra estudiantes, materias, asistencia y más desde un solo lugar.</p>
</div>

<div class="vaciar-db-container">
    <button type="button" class="btn-vaciar-db" data-bs-toggle="modal" data-bs-target="#modalConfirmarVaciado">
        <i class="bi bi-database-x"></i> Vaciar Base de Datos
    </button>
</div>

<div class="home-cards">
    <div class="home-card">
        <i class="bi bi-person-lines-fill"></i>
        <h3>Estudiantes</h3>
        <p>Consulta, edita y gestiona la información de los estudiantes.</p>
        <a href="/app/controller/getStudentController.php">Ir a Estudiantes</a>
    </div>
    <div class="home-card">
        <i class="bi bi-journal-bookmark-fill"></i>
        <h3>Materias</h3>
        <p>Administra las materias y asignaturas de tu institución.</p>
        <a href="/app/controller/getMateriaController.php">Ir a Materias</a>
    </div>
    <div class="home-card">
        <i class="bi bi-clipboard-check"></i>
        <h3>Calificaciones</h3>
        <p>Revisa y gestiona las notas y evaluaciones de los estudiantes.</p>
        <a href="/app/controller/NotasController.php">Ir a Calificaciones</a>
    </div>
    <div class="home-card">
        <i class="bi bi-calendar-check"></i>
        <h3>Asistencia</h3>
        <p>Registra y consulta la asistencia de los estudiantes.</p>
        <a href="/app/view/asistencia.php">Ir a Asistencia</a>
    </div>
</div>

<!-- Modal de Confirmación para Vaciar Base de Datos -->
<div class="modal fade modal-danger" id="modalConfirmarVaciado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">⚠️ Confirmar Vaciado de Base de Datos</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> ADVERTENCIA CRÍTICA</h4>
                    <p class="mb-0">
                        Esta acción <strong>ELIMINARÁ PERMANENTEMENTE</strong> todos los datos del sistema:
                    </p>
                </div>
                <ul class="list-group mb-3">
                    <li class="list-group-item list-group-item-danger">
                        <i class="bi bi-person-fill-x"></i> Todos los estudiantes
                    </li>
                    <li class="list-group-item list-group-item-danger">
                        <i class="bi bi-journal-x"></i> Todas las materias y asuntos
                    </li>
                    <li class="list-group-item list-group-item-danger">
                        <i class="bi bi-clipboard-x"></i> Todas las calificaciones
                    </li>
                    <li class="list-group-item list-group-item-danger">
                        <i class="bi bi-calendar-x"></i> Toda la asistencia
                    </li>
                    <li class="list-group-item list-group-item-danger">
                        <i class="bi bi-graph-up-arrow"></i> Todos los indicadores y criterios
                    </li>
                </ul>
                <p class="text-danger fw-bold">
                    <i class="bi bi-shield-exclamation"></i> Esta operación NO se puede deshacer. 
                    Se recomienda hacer una copia de seguridad primero.
                </p>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="confirmCheckbox">
                    <label class="form-check-label" for="confirmCheckbox">
                        Confirmo que entiendo que esta acción eliminará todos los datos permanentemente.
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarVaciado" disabled>
                    <i class="bi bi-database-x"></i> Sí, vaciar base de datos
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resultado del Vaciado -->
<div class="modal fade modal-warning" id="modalResultadoVaciado" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalResultadoTitulo"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalResultadoMensaje"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
// Versión mejorada con manejo de errores
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirmCheckbox');
    const btnConfirmarVaciado = document.getElementById('btnConfirmarVaciado');
    
    if (confirmCheckbox && btnConfirmarVaciado) {
        confirmCheckbox.addEventListener('change', function() {
            btnConfirmarVaciado.disabled = !this.checked;
        });
    }
    
    // Manejar el clic en el botón de confirmación
    if (btnConfirmarVaciado) {
        btnConfirmarVaciado.addEventListener('click', async function() {
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
            
            try {
                // Hacer petición POST
                const response = await fetch('/app/controller/VBD.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        confirm: 'true',
                        timestamp: Date.now()
                    })
                });
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
                }
                
                const data = await response.json();
                
                // Ocultar modal de confirmación
                const modalConfirmar = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarVaciado'));
                if (modalConfirmar) modalConfirmar.hide();
                
                // Mostrar resultado
                mostrarResultado(data);
                
               
                
            } catch (error) {
                console.error('Error en la solicitud:', error);
                mostrarError(error);
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    }
    
    // Función para mostrar resultado
    function mostrarResultado(data) {
        const titulo = document.getElementById('modalResultadoTitulo');
        const mensaje = document.getElementById('modalResultadoMensaje');
        
        if (!titulo || !mensaje) return;
        
        if (data.status === 'success' || data.status === 'warning') {
            const isSuccess = data.status === 'success';
            titulo.textContent = isSuccess ? '✅ Base de datos vaciada' : '⚠️ Base de datos vaciada con advertencias';
            titulo.className = isSuccess ? 'modal-title text-success' : 'modal-title text-warning';
            
            let detallesHTML = '';
            if (data.details && typeof data.details === 'object') {
                detallesHTML = '<div class="mt-3"><h6>Detalles:</h6><ul class="list-group">';
                for (const [key, value] of Object.entries(data.details)) {
                    detallesHTML += `<li class="list-group-item"><strong>${key}:</strong> ${value}</li>`;
                }
                detallesHTML += '</ul></div>';
            }
            
            mensaje.innerHTML = `
                <div class="alert ${isSuccess ? 'alert-success' : 'alert-warning'}">
                    <h5><i class="bi ${isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'}"></i> ${data.message}</h5>
                    ${data.resumen ? `<p class="mb-0">${data.resumen}</p>` : ''}
                </div>
                ${detallesHTML}
            `;
        } else {
            titulo.textContent = '❌ Error al vaciar base de datos';
            titulo.className = 'modal-title text-danger';
            mensaje.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="bi bi-exclamation-triangle-fill"></i> Error</h5>
                    <p class="mb-0">${data.message || 'Error desconocido.'}</p>
                    ${data.details ? `<p class="mt-2 mb-0"><small><strong>Detalles:</strong> ${data.details}</small></p>` : ''}
                </div>
            `;
        }
        
        // Mostrar modal
        const modalResultado = new bootstrap.Modal(document.getElementById('modalResultadoVaciado'));
        modalResultado.show();
    }
    
    // Función para mostrar error de conexión
    function mostrarError(error) {
        const modalConfirmar = bootstrap.Modal.getInstance(document.getElementById('modalConfirmarVaciado'));
        if (modalConfirmar) modalConfirmar.hide();
        
        const titulo = document.getElementById('modalResultadoTitulo');
        const mensaje = document.getElementById('modalResultadoMensaje');
        
        if (!titulo || !mensaje) return;
        
        titulo.textContent = '❌ Error de conexión';
        titulo.className = 'modal-title text-danger';
        mensaje.innerHTML = `
            <div class="alert alert-danger">
                <h5><i class="bi bi-exclamation-triangle-fill"></i> Error de conexión</h5>
                <p class="mb-0">No se pudo conectar con el servidor.</p>
                <p class="mt-2 mb-0"><small><strong>Detalles:</strong> ${error.message}</small></p>
                <p class="mt-1 mb-0"><small>Verifica tu conexión y que el servidor esté funcionando.</small></p>
            </div>
        `;
        
        const modalResultado = new bootstrap.Modal(document.getElementById('modalResultadoVaciado'));
        modalResultado.show();
    }
});
</script>