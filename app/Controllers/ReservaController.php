<?php
require_once __DIR__ . '/../helpers/auth.php';

class ReservaController
{
    public function index()
    {
        require_recepcionista();
        require __DIR__ . '/../../config/database.php';

        $stmt = $conn->query("
            SELECT
                r.*,
                er.nombre       AS estado_reserva,
                u.nombre        AS cliente_nombre,
                u.paterno       AS cliente_paterno,
                u.email         AS cliente_email,
                h.numero        AS habitacion_numero,
                h.piso          AS habitacion_piso,
                t.nombre        AS tipo_habitacion,
                t.precioBase    AS precio_noche,
                DATEDIFF(r.fechaFin, r.fechaInicio) AS noches,
                DATEDIFF(r.fechaFin, r.fechaInicio) * t.precioBase AS total
            FROM reserva r
            LEFT JOIN estado_reserva er  ON r.idEstadoReserva_FK  = er.idEstado
            LEFT JOIN usuario u          ON r.idUsuario_FK         = u.idUsuario
            LEFT JOIN habitacion h       ON r.idHabitacion_FK      = h.idHabitacion
            LEFT JOIN tipo_habitacion t  ON h.idTipoHabitacion_FK  = t.idTipoHabitacion
            LEFT JOIN empleado e         ON r.idEmpleado_FK        = e.idEmpleado
            ORDER BY r.fechaInicio DESC
        ");
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stats = [];
        $stats['total']       = $conn->query("SELECT COUNT(*) FROM reserva")->fetchColumn();
        $stats['pendientes']  = $conn->query("SELECT COUNT(*) FROM reserva r JOIN estado_reserva e ON r.idEstadoReserva_FK = e.idEstado WHERE e.nombre = 'Pendiente'")->fetchColumn();
        $stats['confirmadas'] = $conn->query("SELECT COUNT(*) FROM reserva r JOIN estado_reserva e ON r.idEstadoReserva_FK = e.idEstado WHERE e.nombre = 'Confirmada'")->fetchColumn();
        $stats['canceladas']  = $conn->query("SELECT COUNT(*) FROM reserva r JOIN estado_reserva e ON r.idEstadoReserva_FK = e.idEstado WHERE e.nombre = 'Cancelada'")->fetchColumn();

        ob_start();
        include __DIR__ . '/../../views/admin/reservas/index.php';
        $content = ob_get_clean();

        $title = "Reservas | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    public function cambiarEstado()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id         = $_GET['id']     ?? null;
        $nombreEstado = $_GET['estado'] ?? null;

        $permitidos = ['Pendiente', 'Confirmada', 'Cancelada'];
        if (!$id || !in_array($nombreEstado, $permitidos)) {
            header("Location: " . url('admin/reservas'));
            exit();
        }

        try {
            // Obtener idEstado por nombre
            $estStmt = $conn->prepare("SELECT idEstado FROM estado_reserva WHERE nombre = ? LIMIT 1");
            $estStmt->execute([$nombreEstado]);
            $estadoReserva = $estStmt->fetch(PDO::FETCH_ASSOC);

            if (!$estadoReserva) {
                $_SESSION['error'] = "Estado inválido";
                header("Location: " . url('admin/reservas'));
                exit();
            }

            $conn->prepare("UPDATE reserva SET idEstadoReserva_FK = ? WHERE idReserva = ?")
                 ->execute([$estadoReserva['idEstado'], $id]);

            // Actualizar estado habitación
            if ($nombreEstado === 'Confirmada') {
                $estHab = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Ocupada' LIMIT 1")->fetch();
                $conn->prepare("
                    UPDATE habitacion h
                    JOIN reserva r ON r.idHabitacion_FK = h.idHabitacion
                    SET h.idEstadoHabitacion_FK = ?
                    WHERE r.idReserva = ?
                ")->execute([$estHab['idEstado'], $id]);
            }

            if ($nombreEstado === 'Cancelada') {
                $estHab = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Disponible' LIMIT 1")->fetch();
                $conn->prepare("
                    UPDATE habitacion h
                    JOIN reserva r ON r.idHabitacion_FK = h.idHabitacion
                    SET h.idEstadoHabitacion_FK = ?
                    WHERE r.idReserva = ?
                ")->execute([$estHab['idEstado'], $id]);
            }

            $idAdmin = $_SESSION['usuario']['id'];
            $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                 ->execute(["Cambió estado de reserva ID $id a $nombreEstado", $idAdmin]);

            $_SESSION['success'] = "Reserva actualizada a: $nombreEstado";

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Error al actualizar la reserva";
        }

        header("Location: " . url('admin/reservas'));
        exit();
    }
}