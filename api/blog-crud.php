<?php
/**
 * API para operaciones CRUD de blog posts
 * Endpoint: /api/blog-crud.php
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
            // Obtener blog post por ID
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $stmt = $conn->prepare("SELECT * FROM blog_posts WHERE id = ?");
                $stmt->execute([$id]);
                $post = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($post) {
                    echo json_encode(['success' => true, 'data' => $post]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Post no encontrado']);
                }
            } else {
                // Obtener todos los posts
                $stmt = $conn->query("SELECT * FROM blog_posts ORDER BY fecha_creacion DESC");
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $posts]);
            }
            break;
            
        case 'POST':
            // Crear nuevo blog post
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                $data = $_POST;
            }
            
            $titulo = $data['titulo'] ?? '';
            $contenido = $data['contenido'] ?? '';
            $excerpt = $data['excerpt'] ?? '';
            $imagen = $data['imagen'] ?? '';
            $slug = $data['slug'] ?? '';
            $categoria = $data['categoria'] ?? '';
            $tags = $data['tags'] ?? '';
            $activo = $data['activo'] ?? true;
            
            if (empty($titulo) || empty($contenido)) {
                echo json_encode(['success' => false, 'message' => 'Título y contenido son obligatorios']);
                exit;
            }
            
            // Generar slug si no se proporciona
            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $titulo)));
            }
            
            $stmt = $conn->prepare("INSERT INTO blog_posts (titulo, contenido, excerpt, imagen, slug, categoria, tags, activo) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?) RETURNING id");
            $stmt->execute([$titulo, $contenido, $excerpt, $imagen, $slug, $categoria, $tags, $activo]);
            $id = $stmt->fetchColumn();
            
            echo json_encode(['success' => true, 'message' => 'Post creado exitosamente', 'id' => $id]);
            break;
            
        case 'PUT':
            // Actualizar blog post existente
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID de post es obligatorio']);
                exit;
            }
            
            $titulo = $data['titulo'] ?? '';
            $contenido = $data['contenido'] ?? '';
            $excerpt = $data['excerpt'] ?? '';
            $imagen = $data['imagen'] ?? '';
            $slug = $data['slug'] ?? '';
            $categoria = $data['categoria'] ?? '';
            $tags = $data['tags'] ?? '';
            $activo = $data['activo'] ?? true;
            
            if (empty($titulo) || empty($contenido)) {
                echo json_encode(['success' => false, 'message' => 'Título y contenido son obligatorios']);
                exit;
            }
            
            $stmt = $conn->prepare("UPDATE blog_posts 
                                  SET titulo = ?, contenido = ?, excerpt = ?, imagen = ?, slug = ?, categoria = ?, tags = ?, activo = ? 
                                  WHERE id = ?");
            $result = $stmt->execute([$titulo, $contenido, $excerpt, $imagen, $slug, $categoria, $tags, $activo, $id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Post actualizado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el post']);
            }
            break;
            
        case 'DELETE':
            // Eliminar blog post
            $id = $_GET['id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'ID de post es obligatorio']);
                exit;
            }
            
            $stmt = $conn->prepare("DELETE FROM blog_posts WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Post eliminado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar el post']);
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
