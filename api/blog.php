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
            // Obtener posts del blog
            if (isset($_GET['id'])) {
                // Obtener post específico
                $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($post) {
                    echo json_encode($post);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Post no encontrado']);
                }
            } else {
                // Obtener todos los posts
                $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY fecha_publicacion DESC");
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($posts);
            }
            break;
            
        case 'POST':
            // Crear nuevo post
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            // Generar slug automáticamente
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['titulo'])));
            $slug = trim($slug, '-');
            
            $stmt = $pdo->prepare("
                INSERT INTO blog_posts (titulo, excerpt, contenido, imagen, categoria, autor, fecha_publicacion, etiquetas, estado, slug, vistas, popularidad) 
                VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, 0, ?)
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['excerpt'],
                $data['contenido'],
                $data['imagen'],
                $data['categoria'],
                $data['autor'],
                $data['etiquetas'],
                $data['estado'] ?? 'borrador',
                $slug,
                $data['popularidad'] ?? 5.0
            ]);
            
            $id = $pdo->lastInsertId();
            echo json_encode(['id' => $id, 'mensaje' => 'Post creado exitosamente']);
            break;
            
        case 'PUT':
            // Actualizar post existente
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de post requerido']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            // Generar slug automáticamente
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['titulo'])));
            $slug = trim($slug, '-');
            
            $stmt = $pdo->prepare("
                UPDATE blog_posts 
                SET titulo = ?, excerpt = ?, contenido = ?, imagen = ?, categoria = ?, autor = ?, etiquetas = ?, estado = ?, slug = ?, popularidad = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['excerpt'],
                $data['contenido'],
                $data['imagen'],
                $data['categoria'],
                $data['autor'],
                $data['etiquetas'],
                $data['estado'] ?? 'borrador',
                $slug,
                $data['popularidad'] ?? 5.0,
                $_GET['id']
            ]);
            
            echo json_encode(['mensaje' => 'Post actualizado exitosamente']);
            break;
            
        case 'DELETE':
            // Eliminar post
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de post requerido']);
                break;
            }
            
            $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            echo json_encode(['mensaje' => 'Post eliminado exitosamente']);
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


