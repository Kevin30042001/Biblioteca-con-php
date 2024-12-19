<?php
namespace App\Controllers;

use App\Models\Libro;
use PDO;

class LibroController {
    private $db;
    private $libro;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->libro = new Libro($db);
    }

    public function index() {
        try {
            $libros = $this->libro->listarTodos();
            return json_encode([
                'status' => 'success',
                'data' => $libros
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
            $resultado = $this->libro->crear($datos);
            return json_encode([
                'status' => 'success',
                'message' => 'Libro creado exitosamente'
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
            $resultado = $this->libro->actualizar($id, $datos);
            return json_encode([
                'status' => 'success',
                'message' => 'Libro actualizado exitosamente'
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
            $resultado = $this->libro->eliminar($id);
            return json_encode([
                'status' => 'success',
                'message' => 'Libro eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function obtener($id) {
        try {
            $libro = $this->libro->leer($id);
            return json_encode([
                'status' => 'success',
                'data' => $libro
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }


    public function buscar($termino, $tipo) {
        try {
            // Usamos directamente el método del modelo
            $libros = $this->libro->buscar($termino, $tipo);
            
            error_log("Búsqueda realizada: termino=$termino, tipo=$tipo");
            error_log("Resultados encontrados: " . count($libros));
            
            return json_encode([
                'status' => 'success',
                'data' => $libros
            ]);
        } catch (\Exception $e) {
            error_log("Error en búsqueda: " . $e->getMessage());
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    // Agregar método para préstamos
    public function prestarLibro($libroId, $usuarioId) {
        try {
            $resultado = $this->libro->prestar($libroId, $usuarioId);
            return json_encode([
                'status' => 'success',
                'message' => 'Libro prestado exitosamente'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Agregar método para devoluciones
    public function devolverLibro($libroId) {
        try {
            $resultado = $this->libro->devolver($libroId);
            return json_encode([
                'status' => 'success',
                'message' => 'Libro devuelto exitosamente'
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}