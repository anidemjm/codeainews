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
            // Obtener banners
            if (isset($_GET['id'])) {
                // Obtener banner específico
                $stmt = $pdo->prepare("SELECT * FROM banners WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $banner = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($banner) {
                    echo json_encode($banner);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Banner no encontrado']);
                }
            } else {
                // Obtener todos los banners
                $stmt = $pdo->query("SELECT * FROM banners ORDER BY orden ASC");
                $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($banners);
            }
            break;
            
        case 'POST':
            // Crear nuevo banner
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO banners (titulo, descripcion, imagen, enlace, orden, estado) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['descripcion'],
                $data['imagen'],
                $data['enlace'] ?? '#',
                $data['orden'] ?? 1,
                $data['estado'] ?? 'activo'
            ]);
            
            $id = $pdo->lastInsertId();
            echo json_encode(['id' => $id, 'mensaje' => 'Banner creado exitosamente']);
            break;
            
        case 'PUT':
            // Actualizar banner existente
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de banner requerido']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            $stmt = $pdo->prepare("
                UPDATE banners 
                SET titulo = ?, descripcion = ?, imagen = ?, enlace = ?, orden = ?, estado = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['descripcion'],
                $data['imagen'],
                $data['enlace'] ?? '#',
                $data['orden'] ?? 1,
                $data['estado'] ?? 'activo',
                $_GET['id']
            ]);
            
            echo json_encode(['mensaje' => 'Banner actualizado exitosamente']);
            break;
            
        case 'DELETE':
            // Eliminar banner
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de banner requerido']);
                break;
            }
            
            $stmt = $pdo->prepare("DELETE FROM banners WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            echo json_encode(['mensaje' => 'Banner eliminado exitosamente']);
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





