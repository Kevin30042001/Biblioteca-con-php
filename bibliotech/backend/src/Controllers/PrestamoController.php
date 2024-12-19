<?php
namespace App\Controllers;

use App\Models\Prestamo;
use PDO;

class PrestamoController {
    private $db;
    private $prestamo;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->prestamo = new Prestamo($db);
    }

    public function crear($datos) {
        try {
            error_log('Datos recibidos: ' . print_r($datos, true));
            $resultado = $this->prestamo->crear($datos);
            error_log('Préstamo creado correctamente');
            return json_encode([
                'status' => 'success',
                'message' => 'Préstamo registrado exitosamente'
            ]);
        } catch (\Exception $e) {
            error_log('Error al crear préstamo: ' . $e->getMessage());
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function obtenerPrestamos($usuarioId) {
        try {
            error_log('Consultando préstamos del usuario: ' . $usuarioId);
            $prestamos = $this->prestamo->obtenerPorUsuario($usuarioId);
            error_log('Préstamos encontrados: ' . print_r($prestamos, true));
            return json_encode([
                'status' => 'success',
                'data' => $prestamos
            ]);
        } catch (\Exception $e) {
            error_log('Error al obtener préstamos: ' . $e->getMessage());
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}