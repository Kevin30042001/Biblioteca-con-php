<?php
namespace App\Models;

abstract class EntidadBase {
    protected $id;
    protected $fechaCreacion;
    protected $fechaActualizacion;

    public function __construct() {
        $this->fechaCreacion = new \DateTime();
        $this->fechaActualizacion = new \DateTime();
    }

    abstract public function validar(): bool;

    public function getId() {
        return $this->id;
    }

    public function getFechaCreacion() {
        return $this->fechaCreacion;
    }

    public function getFechaActualizacion() {
        return $this->fechaActualizacion;
    }
}
