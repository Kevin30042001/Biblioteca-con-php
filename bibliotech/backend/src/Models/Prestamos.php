<?php
namespace App\Models;

class Prestamo extends EntidadBase {
    private $db;

    public function __construct($db) {
        parent::__construct();
        $this->db = $db;
    }

    public function validar(): bool {
        return true;
    }

    public function crear($datos) {
        try {
            $this->db->beginTransaction();
    
            // Verificar disponibilidad del libro
            $stmt = $this->db->prepare("SELECT disponible FROM libros WHERE id = :libro_id");
            $stmt->execute([':libro_id' => $datos['libro_id']]);
            $libro = $stmt->fetch(\PDO::FETCH_ASSOC);
    
            if (!$libro['disponible']) {
                throw new \Exception("El libro no está disponible");
            }
    
            // Crear el préstamo con el usuario_id explícito
            $sql = "INSERT INTO prestamos (
                        libro_id, 
                        usuario_id, 
                        fecha_prestamo, 
                        fecha_devolucion, 
                        devuelto
                    ) VALUES (
                        :libro_id,
                        :usuario_id,
                        :fecha_prestamo,
                        :fecha_devolucion,
                        0
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':libro_id' => $datos['libro_id'],
                ':usuario_id' => $datos['usuario_id'], // Asegurarse de que este valor llegue correctamente
                ':fecha_prestamo' => date('Y-m-d'),
                ':fecha_devolucion' => date('Y-m-d', strtotime('+15 days'))
            ]);
    
            // Actualizar estado del libro
            $sql = "UPDATE libros SET disponible = 0 WHERE id = :libro_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':libro_id' => $datos['libro_id']]);
    
            $this->db->commit();
            return true;
    
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error en crear préstamo: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerPorUsuario($usuarioId) {
        try {
            $sql = "SELECT p.*, l.titulo as libro_titulo, 
                    DATE_FORMAT(p.fecha_prestamo, '%d/%m/%Y') as fecha_prestamo,
                    DATE_FORMAT(p.fecha_devolucion, '%d/%m/%Y') as fecha_devolucion,
                    CASE WHEN p.devuelto = 1 THEN 'Devuelto' ELSE 'Prestado' END as estado
                    FROM prestamos p 
                    INNER JOIN libros l ON p.libro_id = l.id 
                    WHERE p.usuario_id = :usuario_id 
                    ORDER BY p.fecha_prestamo DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':usuario_id' => $usuarioId]);
            $prestamos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            error_log('Préstamos encontrados: ' . print_r($prestamos, true));
            return $prestamos;
        } catch (\Exception $e) {
            error_log('Error en obtenerPorUsuario: ' . $e->getMessage());
            throw $e;
        }
    }

    public function devolver($id) {
        try {
            $this->db->beginTransaction();

            // Actualizar el préstamo
            $sql = "UPDATE prestamos SET devuelto = 1, fecha_actualizacion = :fecha WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':fecha' => date('Y-m-d H:i:s')
            ]);

            // Obtener el libro_id
            $sql = "SELECT libro_id FROM prestamos WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $prestamo = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Actualizar disponibilidad del libro
            $sql = "UPDATE libros SET disponible = 1 WHERE id = :libro_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':libro_id' => $prestamo['libro_id']]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}