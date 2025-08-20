<?php
session_start();
header('Content-Type: application/json');

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

require_once '../config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Obtener categorías
            if (isset($_GET['id'])) {
                // Obtener categoría específica
                $stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($categoria) {
                    echo json_encode($categoria);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Categoría no encontrada']);
                }
            } else {
                // Obtener todas las categorías
                $stmt = $pdo->query("SELECT * FROM categorias ORDER BY orden ASC");
                $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($categorias);
            }
            break;
            
        case 'POST':
            // Crear nueva categoría
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            // Generar slug automáticamente
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['nombre'])));
            $slug = trim($slug, '-');
            
            $stmt = $pdo->prepare("
                INSERT INTO categorias (nombre, slug, color, descripcion, orden) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['nombre'],
                $slug,
                $data['color'] ?? '#2196f3',
                $data['descripcion'] ?? '',
                $data['orden'] ?? 1
            ]);
            
            $id = $pdo->lastInsertId();
            echo json_encode(['id' => $id, 'mensaje' => 'Categoría creada exitosamente']);
            break;
            
        case 'PUT':
            // Actualizar categoría existente
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de categoría requerido']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            // Generar slug automáticamente
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['nombre'])));
            $slug = trim($slug, '-');
            
            $stmt = $pdo->prepare("
                UPDATE categorias 
                SET nombre = ?, slug = ?, color = ?, descripcion = ?, orden = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['nombre'],
                $slug,
                $data['color'] ?? '#2196f3',
                $data['descripcion'] ?? '',
                $data['orden'] ?? 1,
                $_GET['id']
            ]);
            
            echo json_encode(['mensaje' => 'Categoría actualizada exitosamente']);
            break;
            
        case 'DELETE':
            // Eliminar categoría
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de categoría requerido']);
                break;
            }
            
            // Verificar si hay noticias o posts usando esta categoría
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM noticias WHERE categoria = (SELECT slug FROM categorias WHERE id = ?)");
            $stmt->execute([$_GET['id']]);
            $noticiasCount = $stmt->fetchColumn();
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE categoria = (SELECT slug FROM categorias WHERE id = ?)");
            $stmt->execute([$_GET['id']]);
            $postsCount = $stmt->fetchColumn();
            
            if ($noticiasCount > 0 || $postsCount > 0) {
                http_response_code(400);
                echo json_encode(['error' => 'No se puede eliminar la categoría porque está siendo usada por noticias o posts del blog']);
                break;
            }
            
            $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            echo json_encode(['mensaje' => 'Categoría eliminada exitosamente']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido']);
            break;
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>





