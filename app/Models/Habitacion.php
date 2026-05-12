<?php

class Habitacion {
    private $db;

    public function __construct($conexion) {
        $this->db = $conexion;
    }

    public function obtenerDisponibles() {
        
        $sql = "SELECT h.idHabitacion, h.numero, h.piso, t.nombre AS tipo, t.precioBase 
                FROM habitacion h
                JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
                WHERE h.idEstadoHabitacion_FK = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}