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
            // Obtener noticias
            if (isset($_GET['id'])) {
                // Obtener noticia específica
                $stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($noticia) {
                    echo json_encode($noticia);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Noticia no encontrada']);
                }
            } else {
                // Obtener todas las noticias
                $stmt = $pdo->query("SELECT * FROM noticias ORDER BY fecha_publicacion DESC");
                $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($noticias);
            }
            break;
            
        case 'POST':
            // Crear nueva noticia
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO noticias (titulo, resumen, contenido, imagen, categoria, autor, fecha_publicacion, enlace, estado, color_categoria) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['resumen'],
                $data['contenido'],
                $data['imagen'],
                $data['categoria'],
                $data['autor'],
                $data['enlace'] ?? '#',
                $data['estado'] ?? 'borrador',
                $data['color_categoria'] ?? '#2196f3'
            ]);
            
            $id = $pdo->lastInsertId();
            echo json_encode(['id' => $id, 'mensaje' => 'Noticia creada exitosamente']);
            break;
            
        case 'PUT':
            // Actualizar noticia existente
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de noticia requerido']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            $stmt = $pdo->prepare("
                UPDATE noticias 
                SET titulo = ?, resumen = ?, contenido = ?, imagen = ?, categoria = ?, autor = ?, enlace = ?, estado = ?, color_categoria = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['resumen'],
                $data['contenido'],
                $data['imagen'],
                $data['categoria'],
                $data['autor'],
                $data['enlace'] ?? '#',
                $data['estado'] ?? 'borrador',
                $data['color_categoria'] ?? '#2196f3',
                $_GET['id']
            ]);
            
            echo json_encode(['mensaje' => 'Noticia actualizada exitosamente']);
            break;
            
        case 'DELETE':
            // Eliminar noticia
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de noticia requerido']);
                break;
            }
            
            $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            echo json_encode(['mensaje' => 'Noticia eliminada exitosamente']);
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


