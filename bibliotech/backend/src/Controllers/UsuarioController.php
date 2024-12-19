<?php
namespace App\Controllers;

use App\Models\Usuario;
use PDO;

class UsuarioController {
    private $db;
    private $usuario;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->usuario = new Usuario($db);
    }

    // Método faltante: obtener
    public function obtener($id) {
        try {
            $sql = "SELECT * FROM usuarios WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($usuario) {
                return json_encode([
                    'status' => 'success',
                    'data' => $usuario
                ]);
            } else {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Usuario no encontrado'
                ]);
            }
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Método faltante: obtenerPrestamos
    public function obtenerPrestamos($id) {
        try {
            $sql = "SELECT p.*, l.titulo as libro_titulo 
                    FROM prestamos p 
                    INNER JOIN libros l ON p.libro_id = l.id 
                    WHERE p.usuario_id = :usuario_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':usuario_id' => $id]);
            $prestamos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            error_log('Préstamos encontrados para usuario ' . $id . ': ' . print_r($prestamos, true));
            
            return json_encode([
                'status' => 'success',
                'data' => $prestamos
            ]);
        } catch (\Exception $e) {
            error_log('Error en obtenerPrestamos: ' . $e->getMessage());
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    // Los demás métodos que ya tenías (index, crear, actualizar, eliminar)
    public function index() {
        try {
            $usuarios = $this->usuario->listarTodos();
            return json_encode([
                'status' => 'success',
                'data' => $usuarios
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function crear($datos) {
        try {
            $resultado = $this->usuario->crear($datos);
            return json_encode([
                'status' => 'success',
                'message' => 'Usuario creado exitosamente'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actualizar($id, $datos) {
        try {
            $resultado = $this->usuario->actualizar($id, $datos);
            return json_encode([
                'status' => 'success',
                'message' => 'Usuario actualizado exitosamente'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminar($id) {
        try {
            $resultado = $this->usuario->eliminar($id);
            return json_encode([
                'status' => 'success',
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}