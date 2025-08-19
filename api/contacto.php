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
            // Obtener mensajes de contacto
            if (isset($_GET['id'])) {
                // Obtener mensaje específico
                $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $mensaje = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($mensaje) {
                    echo json_encode($mensaje);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Mensaje no encontrado']);
                }
            } else {
                // Obtener todos los mensajes
                $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY fecha_envio DESC");
                $mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($mensajes);
            }
            break;
            
        case 'PUT':
            // Actualizar estado del mensaje
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de mensaje requerido']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['estado'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Estado requerido']);
                break;
            }
            
            $stmt = $pdo->prepare("UPDATE contact_messages SET estado = ? WHERE id = ?");
            $stmt->execute([$data['estado'], $_GET['id']]);
            
            echo json_encode(['mensaje' => 'Estado del mensaje actualizado exitosamente']);
            break;
            
        case 'DELETE':
            // Eliminar mensaje
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'ID de mensaje requerido']);
                break;
            }
            
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            
            echo json_encode(['mensaje' => 'Mensaje eliminado exitosamente']);
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




