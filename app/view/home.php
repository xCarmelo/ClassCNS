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

</style>

<div class="home-hero">
    <img src="<?= $base ?>/public/assets/logo.jpg" alt="Logo" style="width:90px; border-radius:50%; box-shadow:0 2px 12px #222; margin-bottom:20px;">
    <h1>Bienvenido a Carpintería San José</h1>
    <p>Gestión escolar moderna, intuitiva y segura.<br>Administra estudiantes, materias, asistencia y más desde un solo lugar.</p>
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

<?php include 'footer.php'; ?>
