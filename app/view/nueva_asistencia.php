<?php include 'header.php'; ?>

<div class="container">
    <h2>Registrar Asistencia Masiva</h2>
    <form method="post" action="../controller/asistenciaController.php">
        <div class="row mb-3">
            <div class="col">
                <label>Sección:</label>
                <select name="idSeccion" class="form-control" required>
                    <!-- Opciones dinámicas de sección -->
                </select>
            </div>
            <div class="col">
                <label>Corte:</label>
                <select name="idCorte" class="form-control" required>
                    <!-- Opciones dinámicas de corte -->
                </select>
            </div>
            <div class="col">
                <label>Materia:</label>
                <select name="idMateria" class="form-control" required>
                    <!-- Opciones dinámicas de materia -->
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label>Nombre del Tema:</label>
            <input type="text" name="nombreDelTema" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Fecha:</label>
            <input type="date" name="Fecha" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Tipo de Asistencia:</label>
            <select name="idTipoAsistencia" class="form-control" required>
                <!-- Opciones dinámicas de tipoAsistencia -->
            </select>
        </div>
        <div class="mb-3">
            <label>Selecciona estudiantes (puedes seleccionar varios):</label>
            <div style="max-height:300px; overflow-y:auto; border:1px solid #ccc; border-radius:8px; padding:10px;">
                <!-- Opciones dinámicas de estudiantes con checkbox -->
            </div>
        </div>
        <button type="submit" name="nueva_asistencia_masiva" class="btn btn-success">Registrar Asistencia</button>
    </form>
</div>

<?php include 'footer.php'; ?>
