<?php
/**
 * API para operaciones CRUD de categorías
 * Endpoint: /api/categorias-crud.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Obtener método HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    switch ($method) {
        case 'GET':
            // Obtener categoría por ID
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $conn->prepare("SELECT * FROM categorias WHERE id = ?");
                $stmt->execute([$id]);
                $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($categoria) {
                    echo json_encode(['success' => true, 'data' => $categoria]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Categoría no encontrada']);
                }
            } else {
                // Obtener todas las categorías
                $stmt = $conn->query("SELECT * FROM categorias ORDER BY orden, nombre");
                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $categorias]);
            }
            break;
            
        case 'POST':
            // Crear nueva categoría
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                $data = $_POST;
            }
            
            $nombre = $data['nombre'] ?? '';
            $slug = $data['slug'] ?? '';
            $descripcion = $data['descripcion'] ?? '';
            $orden = $data['orden'] ?? 0;
            
            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'Nombre es obligatorio']);
                exit;
            }
            
            // Generar slug si no se proporciona
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nombre)));
            }
            
            $stmt = $conn->prepare("INSERT INTO categorias (nombre, slug, descripcion, orden) 
                                  VALUES (?, ?, ?, ?) RETURNING id");
            $stmt->execute([$nombre, $slug, $descripcion, $orden]);
            $id = $stmt->fetchColumn();
            
            echo json_encode(['success' => true, 'message' => 'Categoría creada exitosamente', 'id' => $id]);
            break;
            
        case 'PUT':
            // Actualizar categoría existente
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID de categoría es obligatorio']);
                exit;
            }
            
            $nombre = $data['nombre'] ?? '';
            $slug = $data['slug'] ?? '';
            $descripcion = $data['descripcion'] ?? '';
            $orden = $data['orden'] ?? 0;
            
            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'Nombre es obligatorio']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE categorias 
                                  SET nombre = ?, slug = ?, descripcion = ?, orden = ? 
                                  WHERE id = ?");
            $result = $stmt->execute([$nombre, $slug, $descripcion, $orden, $id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Categoría actualizada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la categoría']);
            }
            break;
            
        case 'DELETE':
            // Eliminar categoría
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID de categoría es obligatorio']);
                exit;
            }
            
            // Verificar si hay noticias usando esta categoría
            $stmt = $conn->prepare("SELECT COUNT(*) FROM noticias WHERE categoria_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                echo json_encode(['success' => false, 'message' => 'No se puede eliminar: hay noticias usando esta categoría']);
                exit;
            }
            
            $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Categoría eliminada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar la categoría']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            break;
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
