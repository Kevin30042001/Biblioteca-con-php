<?php
// config/database.php

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $this->conn = new PDO('sqlite:' . __DIR__ . '/../database.sqlite');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->initDatabase();
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    private function initDatabase() {
        $this->conn->exec('
            CREATE TABLE IF NOT EXISTS libros (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                titulo TEXT NOT NULL,
                autor TEXT NOT NULL,
                categoria TEXT NOT NULL,
                isbn TEXT UNIQUE,
                disponible INTEGER DEFAULT 1,
                fecha_creacion DATETIME,
                fecha_actualizacion DATETIME
            );

            CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nombre TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                tipo TEXT NOT NULL,
                prestamos_actuales INTEGER DEFAULT 0,
                fecha_creacion DATETIME,
                fecha_actualizacion DATETIME
            );

            CREATE TABLE IF NOT EXISTS prestamos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libro_id INTEGER,
    usuario_id INTEGER,
    fecha_prestamo DATE NOT NULL,
    fecha_devolucion DATE NOT NULL,
    devuelto INTEGER DEFAULT 0,
    fecha_creacion DATETIME,
    fecha_actualizacion DATETIME,
    FOREIGN KEY(libro_id) REFERENCES libros(id),
    FOREIGN KEY(usuario_id) REFERENCES usuarios(id)
);
        ');
    }
}