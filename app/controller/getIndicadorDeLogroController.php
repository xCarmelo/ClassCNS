<?php
require_once __DIR__ . '/../model/indicadorL.php';
require_once __DIR__ . '/../model/corte.php';
require_once __DIR__ . '/../model/materia.php';
require_once __DIR__ . '/../model/seccion.php';
require_once __DIR__ . '/../model/enlace.php';
require_once __DIR__ . '/../model/Database.php';

class IndicadorLController {
    private $indicadorModel;
    private $corteModel;
    private $materiaModel;
    private $seccionModel;
    private $enlaceModel;

    public function __construct() {
        $this->indicadorModel = new IndicadorL();
        $this->corteModel     = new Corte();
        $this->materiaModel   = new Materia();
        $this->seccionModel   = new Seccion();
        $this->enlaceModel    = new Enlace();
    }

    // Mostrar lista de indicadores (NO tocar $_SESSION aquí)
    public function index() {
        $indicadores = $this->indicadorModel->getAll();
        $cortes      = $this->corteModel->getAll();
        $materias    = $this->materiaModel->getAll();
        $secciones   = $this->seccionModel->getAll();

        require __DIR__ . '/../view/indicadorL.php';
    }

    // Guardar nuevo indicador (con transacción)
    public function store($post) {
        $name      = $post['name'] ?? '';
        $anio      = isset($post['anio']) ? (int)$post['anio'] : date('Y');
        $idCorte   = isset($post['idCorte']) ? (int)$post['idCorte'] : null;
        $idMateria = isset($post['idMateria']) ? (int)$post['idMateria'] : null;
        $secciones = $post['secciones'] ?? [];

        $db  = Database::getInstance();
        $pdo = $db->getConnection();

        try {
            $pdo->beginTransaction();

            // create debe devolver el id insertado (int) o false
            $idIndicador = $this->indicadorModel->create($name, $anio, $idCorte, $idMateria);

            if (!$idIndicador || !is_numeric($idIndicador)) {
                throw new Exception('No se obtuvo el ID del indicador. Revisa IndicadorL::create().');
            }

            if (!empty($secciones)) {
                foreach ($secciones as $idSeccion) {
                    if (!$this->enlaceModel->create((int)$idIndicador, (int)$idSeccion)) {
                        throw new Exception("No se pudo crear enlace para idIndicador={$idIndicador} idSeccion={$idSeccion}");
                    }
                }
            }

            $pdo->commit();
            header("Location: getIndicadorDeLogroController.php?action=index");
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("Indicador store error: " . $e->getMessage());

            // Aseguramos sesión antes de escribir el flash (esto es POST)
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['flash_error'] = "Error al guardar indicador: " . $e->getMessage();

            header("Location: getIndicadorDeLogroController.php?action=index");
            exit;
        }
    }

    // Editar indicador (con transacción)
    public function update($post) {
        $id        = (int)($post['id'] ?? 0);
        $name      = $post['name'] ?? '';
        $anio      = isset($post['anio']) ? (int)$post['anio'] : date('Y');
        $idCorte   = isset($post['idCorte']) ? (int)$post['idCorte'] : null;
        $idMateria = isset($post['idMateria']) ? (int)$post['idMateria'] : null;
        $secciones = $post['secciones'] ?? [];

        $db  = Database::getInstance();
        $pdo = $db->getConnection();

        try {
            $pdo->beginTransaction();

            if ($this->indicadorModel->update($id, $name, $anio, $idCorte, $idMateria) === false) {
                throw new Exception("Fallo actualizando el indicador {$id}.");
            }

            $this->enlaceModel->deleteByIndicador($id);

            if (!empty($secciones)) {
                foreach ($secciones as $idSeccion) {
                    if (!$this->enlaceModel->create($id, (int)$idSeccion)) {
                        throw new Exception("No se pudo crear enlace para idIndicador={$id} idSeccion={$idSeccion}");
                    }
                }
            }

            $pdo->commit();
            header("Location: getIndicadorDeLogroController.php?action=index");
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("Indicador update error: " . $e->getMessage());

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['flash_error'] = "Error al actualizar indicador: " . $e->getMessage();

            header("Location: getIndicadorDeLogroController.php?action=index");
            exit;
        }
    }

    // Eliminar indicador
    public function delete($id) {
        $this->enlaceModel->deleteByIndicador($id);
        $this->indicadorModel->delete($id);
        header("Location: getIndicadorDeLogroController.php?action=index");
        exit;
    }
}

// Router
$controller = new IndicadorLController();
$action     = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $controller->store($_POST);
        break;
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') $controller->update($_POST);
        break;
    case 'delete':
        if (isset($_GET['id'])) $controller->delete((int)$_GET['id']);
        break;
    default:
        $controller->index();
}
