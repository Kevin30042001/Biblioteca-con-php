<?php
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Manejar las solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
// Obtener la conexión a la base de datos
$db = Database::getInstance()->getConnection();

// Obtener la ruta y el método de la solicitud
$requestMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Eliminar elementos vacíos
$uri = array_values(array_filter($uri));

// Router básico
try {
    if ($uri[0] === 'libros') {
        $libroController = new App\Controllers\LibroController($db);

        switch ($requestMethod) {
            case 'GET':
                if (isset($_GET['search'])) {
                    $termino = $_GET['search'];
                    $tipo = $_GET['type'] ?? 'titulo';
                    error_log("Recibida búsqueda: término = $termino, tipo = $tipo");
                    echo $libroController->buscar($termino, $tipo);
                } elseif (isset($uri[1])) {
                    echo $libroController->obtener($uri[1]);
                } else {
                    echo $libroController->index();
                }
                break;

            case 'POST':
                if (isset($uri[1]) && $uri[1] === 'prestar') {
                    $datos = json_decode(file_get_contents('php://input'), true);
                    echo $libroController->prestarLibro($datos['libro_id'], $datos['usuario_id']);
                } else {
                    $datos = json_decode(file_get_contents('php://input'), true);
                    echo $libroController->crear($datos);
                }
                break;

            case 'PUT':
                if (isset($uri[1])) {
                    if ($uri[1] === 'devolver') {
                        $datos = json_decode(file_get_contents('php://input'), true);
                        echo $libroController->devolverLibro($datos['libro_id']);
                    } else {
                        $datos = json_decode(file_get_contents('php://input'), true);
                        echo $libroController->actualizar($uri[1], $datos);
                    }
                }
                break;

            case 'DELETE':
                if (isset($uri[1])) {
                    echo $libroController->eliminar($uri[1]);
                }
                break;

            case 'OPTIONS':
                http_response_code(200);
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    } elseif ($uri[0] === 'usuarios') {
        $usuarioController = new App\Controllers\UsuarioController($db);
        
        switch ($requestMethod) {
            case 'GET':
                if (isset($uri[1])) {
                    if (isset($uri[2]) && $uri[2] === 'prestamos') {
                        echo $usuarioController->obtenerPrestamos($uri[1]);
                    } else {
                        echo $usuarioController->obtener($uri[1]);
                    }
                } else {
                    echo $usuarioController->index();
                }
                break;
            
            case 'POST':
                $datos = json_decode(file_get_contents('php://input'), true);
                echo $usuarioController->crear($datos);
                break;
            
            case 'PUT':
                if (isset($uri[1])) {
                    $datos = json_decode(file_get_contents('php://input'), true);
                    echo $usuarioController->actualizar($uri[1], $datos);
                }
                break;
            
            case 'DELETE':
                if (isset($uri[1])) {
                    echo $usuarioController->eliminar($uri[1]);
                }
                break;
                
            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
