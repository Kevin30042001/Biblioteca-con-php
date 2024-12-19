<?php
namespace App\Models;

class Usuario extends EntidadBase {
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
            $sql = "INSERT INTO usuarios (nombre, email, tipo, fecha_creacion, fecha_actualizacion) 
                    VALUES (:nombre, :email, :tipo, :fecha_creacion, :fecha_actualizacion)";
            
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                ':nombre' => $datos['nombre'],
                ':email' => $datos['email'],
                ':tipo' => $datos['tipo'],
                ':fecha_creacion' => date('Y-m-d H:i:s'),
                ':fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            if (!$resultado) {
                throw new \Exception("Error al crear el usuario");
            }

            return true;
        } catch (\Exception $e) {
            error_log("Error en crear usuario: " . $e->getMessage());
            throw $e;
        }
    }

    public function actualizar($id, $datos) {
        try {
            $sql = "UPDATE usuarios SET 
                    nombre = :nombre,
                    email = :email,
                    tipo = :tipo,
                    fecha_actualizacion = :fecha_actualizacion
                    WHERE id = :id";

            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([
                ':id' => $id,
                ':nombre' => $datos['nombre'],
                ':email' => $datos['email'],
                ':tipo' => $datos['tipo'],
                ':fecha_actualizacion' => date('Y-m-d H:i:s')
            ]);

            if (!$resultado) {
                throw new \Exception("Error al actualizar el usuario");
            }

            return true;
        } catch (\Exception $e) {
            error_log("Error en actualizar usuario: " . $e->getMessage());
            throw $e;
        }
    }

    public function eliminar($id) {
        try {
            $sql = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $resultado = $stmt->execute([':id' => $id]);

            if (!$resultado) {
                throw new \Exception("Error al eliminar el usuario");
            }

            return true;
        } catch (\Exception $e) {
            error_log("Error en eliminar usuario: " . $e->getMessage());
            throw $e;
        }
    }

    public function listarTodos() {
        try {
            $sql = "SELECT * FROM usuarios";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error en listar usuarios: " . $e->getMessage());
            throw $e;
        }
    }

    public function obtenerPrestamos($usuarioId) {
        try {
            $sql = "SELECT p.*, l.titulo as libro_titulo, l.autor as libro_autor 
                    FROM prestamos p 
                    JOIN libros l ON p.libro_id = l.id 
                    WHERE p.usuario_id = :usuario_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':usuario_id' => $usuarioId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error en obtener préstamos: " . $e->getMessage());
            throw $e;
        }
    }
}