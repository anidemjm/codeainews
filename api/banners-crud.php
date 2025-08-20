<?php
/**
 * API para operaciones CRUD de banners
 * Endpoint: /api/banners-crud.php
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
            // Obtener banner por ID
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $conn->prepare("SELECT * FROM banners WHERE id = ?");
                $stmt->execute([$id]);
                $banner = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($banner) {
                    echo json_encode(['success' => true, 'data' => $banner]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Banner no encontrado']);
                }
            } else {
                // Obtener todos los banners
                $stmt = $conn->query("SELECT * FROM banners ORDER BY orden, id");
                $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $banners]);
            }
            break;
            
        case 'POST':
            // Crear nuevo banner
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                $data = $_POST;
            }
            
            $titulo = $data['titulo'] ?? '';
            $imagen = $data['imagen'] ?? '';
            $enlace = $data['enlace'] ?? '';
            $orden = $data['orden'] ?? 0;
            $activo = $data['activo'] ?? true;
            
            if (empty($titulo) || empty($imagen)) {
                echo json_encode(['success' => false, 'message' => 'Título e imagen son obligatorios']);
                exit;
            }
            
            $stmt = $conn->prepare("INSERT INTO banners (titulo, imagen, enlace, orden, activo) 
                                  VALUES (?, ?, ?, ?, ?) RETURNING id");
            $stmt->execute([$titulo, $imagen, $enlace, $orden, $activo]);
            $id = $stmt->fetchColumn();
            
            echo json_encode(['success' => true, 'message' => 'Banner creado exitosamente', 'id' => $id]);
            break;
            
        case 'PUT':
            // Actualizar banner existente
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID de banner es obligatorio']);
                exit;
            }
            
            $titulo = $data['titulo'] ?? '';
            $imagen = $data['imagen'] ?? '';
            $enlace = $data['enlace'] ?? '';
            $orden = $data['orden'] ?? 0;
            $activo = $data['activo'] ?? true;
            
            if (empty($titulo) || empty($imagen)) {
                echo json_encode(['success' => false, 'message' => 'Título e imagen son obligatorios']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE banners 
                                  SET titulo = ?, imagen = ?, enlace = ?, orden = ?, activo = ? 
                                  WHERE id = ?");
            $result = $stmt->execute([$titulo, $imagen, $enlace, $orden, $activo, $id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Banner actualizado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el banner']);
            }
            break;
            
        case 'DELETE':
            // Eliminar banner
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID de banner es obligatorio']);
                exit;
            }
            
            $stmt = $conn->prepare("DELETE FROM banners WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Banner eliminado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el banner']);
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
