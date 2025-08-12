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
            // Obtener elementos del footer
            if (isset($_GET['id'])) {
                // Obtener elemento específico
                $stmt = $pdo->prepare("SELECT * FROM footer_items WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $elemento = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($elemento) {
                    echo json_encode($elemento);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Elemento no encontrado']);
                }
            } else {
                // Obtener todos los elementos
                $stmt = $pdo->query("SELECT * FROM footer_items ORDER BY orden ASC");
                $elementos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($elementos);
            }
            break;
            
        case 'POST':
            // Crear nuevo elemento
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO footer_items (titulo, contenido, enlace, texto_enlace, orden) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['contenido'],
                $data['enlace'] ?? '#',
                $data['texto_enlace'] ?? 'Leer más',
                $data['orden'] ?? 1
            ]);
            
            $id = $pdo->lastInsertId();
            echo json_encode(['id' => $id, 'mensaje' => 'Elemento del footer creado exitosamente']);
            break;
            
        case 'PUT':
            // Actualizar elemento existente
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de elemento requerido']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            $stmt = $pdo->prepare("
                UPDATE footer_items 
                SET titulo = ?, contenido = ?, enlace = ?, texto_enlace = ?, orden = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['titulo'],
                $data['contenido'],
                $data['enlace'] ?? '#',
                $data['texto_enlace'] ?? 'Leer más',
                $data['orden'] ?? 1,
                $_GET['id']
            ]);
            
            echo json_encode(['mensaje' => 'Elemento del footer actualizado exitosamente']);
            break;
            
        case 'DELETE':
            // Eliminar elemento
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de elemento requerido']);
                break;
            }
            
            $stmt = $pdo->prepare("DELETE FROM footer_items WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            echo json_encode(['mensaje' => 'Elemento del footer eliminado exitosamente']);
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


