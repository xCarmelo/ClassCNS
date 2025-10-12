<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="/app/view/home.php">
      <img src="/public/assets/São José Design Católico.jpg" alt="Logo" width="40" height="40" class="d-inline-block align-top rounded-circle me-2">
      <span style="font-size:1.5rem; font-weight:bold;">Carpintería San José</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuHamburguesa" aria-controls="menuHamburguesa" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="menuHamburguesa">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <div class="nav-item dropdown">
              <a href="/app/controller/getStudentController.php" class="nav-link fs-5 fw-bold dropdown-toggle" id="estudiantesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-lines-fill"></i> Estudiantes
              </a>
              <ul class="dropdown-menu" aria-labelledby="estudiantesDropdown">
                <li><a class="dropdown-item" href="/app/controller/getStudentController.php"><i class="bi bi-list-ul"></i> Listado</a></li>
                <li><a class="dropdown-item" href="/app/controller/asistenciaController.php"><i class="bi bi-calendar-check"></i> Asistencia</a></li>
                <li><a class="dropdown-item" href="/app/controller/getAsuntoController.php"><i class="bi bi-chat-dots-fill"></i> Asuntos</a></li>
                <li><a class="dropdown-item" href="/app/controller/NotasController.php"><i class="bi bi-clipboard-check"></i> Calificaciones</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/app/controller/compareStudentsController.php"><i class="bi bi-shuffle"></i> Comparar estudiantes</a></li>
              </ul>
            </div>
        </li>
        <li class="nav-item">
          <a href="/app/controller/getSeccionController.php" class="nav-link fs-5 fw-bold"><i class="bi bi-diagram-3-fill"></i> Sección</a>
        </li>
        <li class="nav-item dropdown">
          <a href="/app/controller/getMateriaController.php" class="nav-link fs-5 fw-bold dropdown-toggle" id="materiaDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-journal-bookmark-fill"></i> Materia
          </a>
          <ul class="dropdown-menu" aria-labelledby="materiaDropdown">
            <li><a class="dropdown-item" href="/app/controller/getMateriaController.php"><i class="bi bi-bar-chart-line-fill"></i> Lista</a></li>
            <li><a class="dropdown-item" href="/app/controller/getIndicadorDeLogroController.php"><i class="bi bi-bar-chart-line-fill"></i> Indicadores</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a href="/app/controller/getAudioController.php" class="nav-link fs-5 fw-bold"><i class="bi bi-volume-up-fill"></i> Texto a voz</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

