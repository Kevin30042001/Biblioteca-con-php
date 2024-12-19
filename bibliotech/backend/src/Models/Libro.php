<?php
namespace App\Models;
use App\Models\EntidadBase;
class Libro extends EntidadBase {
    private $titulo;
    private $autor;
    private $categoria;
    private $isbn;
    private $disponible;
    private $db;

    public function __construct($db) {
        parent::__construct();
        $this->db = $db;
        $this->disponible = true;
    }

    public function validar(): bool {
        if (empty($this->titulo) || empty($this->autor) || empty($this->isbn)) {
            throw new \Exception("Campos obligatorios incompletos");
        }
        return true;
    }

    // CRUD operations
    public function crear($datos) {
        $sql = "INSERT INTO libros (titulo, autor, categoria, isbn, disponible, fecha_creacion, fecha_actualizacion) 
                VALUES (:titulo, :autor, :categoria, :isbn, :disponible, :fecha_creacion, :fecha_actualizacion)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo' => $datos['titulo'],
            ':autor' => $datos['autor'],
            ':categoria' => $datos['categoria'],
            ':isbn' => $datos['isbn'],
            ':disponible' => 1,
            ':fecha_creacion' => date('Y-m-d H:i:s'),
            ':fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);
    }

    public function leer($id) {
        $sql = "SELECT * FROM libros WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE libros SET 
                titulo = :titulo, 
                autor = :autor, 
                categoria = :categoria, 
                isbn = :isbn,
                fecha_actualizacion = :fecha_actualizacion
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':titulo' => $datos['titulo'],
            ':autor' => $datos['autor'],
            ':categoria' => $datos['categoria'],
            ':isbn' => $datos['isbn'],
            ':fecha_actualizacion' => date('Y-m-d H:i:s')
        ]);
    }

    public function eliminar($id) {
        $sql = "DELETE FROM libros WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function listarTodos() {
        $sql = "SELECT * FROM libros";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function buscar($termino, $tipo = 'titulo') {
        try {
            $columna = match($tipo) {
                'autor' => 'autor',
                'categoria' => 'categoria',
                'isbn' => 'isbn',
                default => 'titulo'
            };
            
            $sql = "SELECT * FROM libros WHERE {$columna} LIKE :termino";
            error_log("SQL: " . $sql);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':termino' => "%{$termino}%"]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error en consulta: " . $e->getMessage());
            throw $e;
        }
    }

    // Método para préstamo
    public function prestar($usuarioId) {
        if (!$this->disponible) {
            throw new \Exception("El libro no está disponible");
        }

        try {
            $this->db->beginTransaction();
            
            // Actualizar estado del libro
            $sql = "UPDATE libros SET disponible = 0 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $this->id]);

            // Registrar préstamo
            $sql = "INSERT INTO prestamos (libro_id, usuario_id, fecha_prestamo, fecha_devolucion) 
                   VALUES (:libro_id, :usuario_id, :fecha_prestamo, :fecha_devolucion)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':libro_id' => $this->id,
                ':usuario_id' => $usuarioId,
                ':fecha_prestamo' => date('Y-m-d'),
                ':fecha_devolucion' => date('Y-m-d', strtotime('+15 days'))
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // Método para devolución
    public function devolver() {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE libros SET disponible = 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $this->id]);

            $sql = "UPDATE prestamos SET devuelto = 1 
                   WHERE libro_id = :libro_id AND devuelto = 0";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':libro_id' => $this->id]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}