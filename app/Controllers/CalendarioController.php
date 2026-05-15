<?php
require_once __DIR__ . '/../helpers/auth.php';

class CalendarioController
{
    public function index()
    {
        require_recepcionista();
        require __DIR__ . '/../../config/database.php';

        // Reservas activas para el calendario (próximos 60 días)
        $reservas = $conn->query("
            SELECT
                r.idReserva,
                r.fechaInicio,
                r.fechaFin,
                r.precioTotal,
                er.nombre AS estado,
                u.nombre  AS cliente_nombre,
                u.paterno AS cliente_paterno,
                h.numero  AS habitacion_numero,
                h.piso,
                t.nombre  AS tipo
            FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            JOIN usuario u         ON r.idUsuario_FK       = u.idUsuario
            JOIN habitacion h      ON r.idHabitacion_FK    = h.idHabitacion
            JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            WHERE er.nombre NOT IN ('Cancelada', 'No show')
              AND r.fechaFin >= CURDATE()
            ORDER BY r.fechaInicio ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Todas las habitaciones
        $habitaciones = $conn->query("
            SELECT h.*, t.nombre AS tipo, eh.nombre AS estado_hab
            FROM habitacion h
            JOIN tipo_habitacion t    ON h.idTipoHabitacion_FK  = t.idTipoHabitacion
            JOIN estado_habitacion eh ON h.idEstadoHabitacion_FK = eh.idEstado
            ORDER BY h.piso ASC, h.numero ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/calendario.php';
        $content = ob_get_clean();
        $title = "Calendario | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }
}