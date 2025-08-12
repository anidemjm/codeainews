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
            // Obtener información de contacto
            $stmt = $pdo->query("SELECT * FROM contact_info LIMIT 1");
            $contactInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($contactInfo) {
                echo json_encode($contactInfo);
            } else {
                echo json_encode(null);
            }
            break;
            
        case 'POST':
            // Crear nueva información de contacto
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            $stmt = $pdo->prepare("
                INSERT INTO contact_info (direccion, email, telefono, horarios, redes_sociales) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['direccion'] ?? '',
                $data['email'] ?? '',
                $data['telefono'] ?? '',
                $data['horarios'] ?? '',
                $data['redes_sociales'] ?? ''
            ]);
            
            $id = $pdo->lastInsertId();
            echo json_encode(['id' => $id, 'mensaje' => 'Información de contacto creada exitosamente']);
            break;
            
        case 'PUT':
            // Actualizar información de contacto existente
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                break;
            }
            
            // Verificar si ya existe información
            $stmt = $pdo->query("SELECT COUNT(*) FROM contact_info");
            $exists = $stmt->fetchColumn() > 0;
            
            if ($exists) {
                // Actualizar
                $stmt = $pdo->prepare("
                    UPDATE contact_info 
                    SET direccion = ?, email = ?, telefono = ?, horarios = ?, redes_sociales = ?
                ");
                
                $stmt->execute([
                    $data['direccion'] ?? '',
                    $data['email'] ?? '',
                    $data['telefono'] ?? '',
                    $data['horarios'] ?? '',
                    $data['redes_sociales'] ?? ''
                ]);
                
                echo json_encode(['mensaje' => 'Información de contacto actualizada exitosamente']);
            } else {
                // Crear
                $stmt = $pdo->prepare("
                    INSERT INTO contact_info (direccion, email, telefono, horarios, redes_sociales) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $data['direccion'] ?? '',
                    $data['email'] ?? '',
                    $data['telefono'] ?? '',
                    $data['horarios'] ?? '',
                    $data['redes_sociales'] ?? ''
                ]);
                
                $id = $pdo->lastInsertId();
                echo json_encode(['id' => $id, 'mensaje' => 'Información de contacto creada exitosamente']);
            }
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


