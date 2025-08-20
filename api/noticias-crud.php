<?php
/**
 * API para operaciones CRUD de noticias
 * Endpoint: /api/noticias-crud.php
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
            // Obtener noticia por ID
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $conn->prepare("SELECT n.*, c.nombre as categoria_nombre 
                                      FROM noticias n 
                                      LEFT JOIN categorias c ON n.categoria_id = c.id 
                                      WHERE n.id = ?");
                $stmt->execute([$id]);
                $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($noticia) {
                    echo json_encode(['success' => true, 'data' => $noticia]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Noticia no encontrada']);
                }
            } else {
                // Obtener todas las noticias
                $stmt = $conn->query("SELECT n.*, c.nombre as categoria_nombre 
                                    FROM noticias n 
                                    LEFT JOIN categorias c ON n.categoria_id = c.id 
                                    ORDER BY n.fecha_creacion DESC");
                $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $noticias]);
            }
            break;
            
        case 'POST':
            // Crear nueva noticia
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                $data = $_POST;
            }
            
            $titulo = $data['titulo'] ?? '';
            $contenido = $data['contenido'] ?? '';
            $imagen = $data['imagen'] ?? '';
            $categoria_id = $data['categoria_id'] ?? null;
            $activo = $data['activo'] ?? true;
            
            if (empty($titulo) || empty($contenido)) {
                echo json_encode(['success' => false, 'message' => 'Título y contenido son obligatorios']);
                exit;
            }
            
            $stmt = $conn->prepare("INSERT INTO noticias (titulo, contenido, imagen, categoria_id, activo) 
                                  VALUES (?, ?, ?, ?, ?) RETURNING id");
            $stmt->execute([$titulo, $contenido, $imagen, $categoria_id, $activo]);
            $id = $stmt->fetchColumn();
            
            echo json_encode(['success' => true, 'message' => 'Noticia creada exitosamente', 'id' => $id]);
            break;
            
        case 'PUT':
            // Actualizar noticia existente
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID de noticia es obligatorio']);
                exit;
            }
            
            $titulo = $data['titulo'] ?? '';
            $contenido = $data['contenido'] ?? '';
            $imagen = $data['imagen'] ?? '';
            $categoria_id = $data['categoria_id'] ?? null;
            $activo = $data['activo'] ?? true;
            
            if (empty($titulo) || empty($contenido)) {
                echo json_encode(['success' => false, 'message' => 'Título y contenido son obligatorios']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE noticias 
                                  SET titulo = ?, contenido = ?, imagen = ?, categoria_id = ?, activo = ? 
                                  WHERE id = ?");
            $result = $stmt->execute([$titulo, $contenido, $imagen, $categoria_id, $activo, $id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Noticia actualizada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar la noticia']);
            }
            break;
            
        case 'DELETE':
            // Eliminar noticia
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID de noticia es obligatorio']);
                exit;
            }
            
            $stmt = $conn->prepare("DELETE FROM noticias WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Noticia eliminada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar la noticia']);
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
