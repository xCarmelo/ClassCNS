<?php
// app/controller/criterioController.php
require_once __DIR__ . '/../model/criterio.php';
require_once __DIR__ . '/../model/Database.php';

class CriterioController {
    private $model;
    private $pdo;

    public function __construct() {
        $this->model = new Criterio();
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Guardar (reemplaza los criterios existentes del indicador por los enviados)
     * Acepta:
     *  - criterios como arrays: criterio[] y puntaje[]
     *  - o criterios como criterio1,puntaje1 ... criterio2,puntaje2 ...
     */
    public function store($post) {
        $idIndicador = isset($post['idIndicador']) ? (int)$post['idIndicador'] : 0;
        if ($idIndicador <= 0) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'idIndicador inválido']);
            exit;
        }

        try {
            $this->pdo->beginTransaction();

            // borrar existentes (reemplazo total)
            $this->model->deleteByIndicador($idIndicador);

            $items = [];

            // 1) formato array: criterio[] y puntaje[]
            if (isset($post['criterio']) && is_array($post['criterio'])) {
                foreach ($post['criterio'] as $k => $texto) {
                    $texto = trim($texto);
                    if ($texto === '') continue;
                    $punt = 0;
                    if (isset($post['puntaje'][$k])) {
                        $punt = $post['puntaje'][$k];
                    } elseif (isset($post['puntaje' . ($k+1)])) {
                        $punt = $post['puntaje' . ($k+1)];
                    }
                    $items[] = ['texto' => $texto, 'punt' => (float)$punt];
                }
            } else {
                // 2) formato individual: criterio1, puntaje1 ... criterio2, puntaje2 ...
                for ($i = 1; $i <= 3; $i++) {
                    $ck = "criterio{$i}";
                    $pk = "puntaje{$i}";
                    if (!isset($post[$ck])) continue;
                    $texto = trim($post[$ck]);
                    if ($texto === '') continue;
                    $punt = isset($post[$pk]) ? (float)$post[$pk] : 0;
                    $items[] = ['texto' => $texto, 'punt' => $punt];
                }
            }

            // insertar
            foreach ($items as $it) {
                $ok = $this->model->create($idIndicador, $it['texto'], $it['punt']);
                if (!$ok) {
                    throw new Exception('Error insertando criterio');
                }
            }

            $this->pdo->commit();
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true]);
            exit;
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            error_log("Criterio store error: " . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Obtener criterios de un indicador (GET)
     * devuelve JSON: array de {id, name, puntos, idIndicadorL}
     */
    public function get($id) {
        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([]);
            exit;
        }
        $rows = $this->model->getByIndicador($id);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($rows);
        exit;
    }

    /**
     * Eliminar criterio por id (opcional, útil para AJAX)
     */
    public function delete($id) {
        $id = (int)$id;
        if ($id <= 0) {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'id inválido']);
            exit;
        }
        $ok = $this->model->delete($id);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => (bool)$ok]);
        exit;
    }
}

/* Router simple */
$controller = new CriterioController();
$action = $_GET['action'] ?? '';

if ($action === 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->store($_POST);
} elseif ($action === 'get' && isset($_GET['id'])) {
    $controller->get($_GET['id']);
} elseif ($action === 'delete' && isset($_REQUEST['id'])) {
    $controller->delete($_REQUEST['id']);
} else {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'acción no encontrada']);
    exit;
}
