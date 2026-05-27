<?php
require_once __DIR__ . '/../helpers/auth.php';

class ReciboController
{
    // ─── Ver recibo (admin o cliente dueño) ──────────────────────────────────
    public function ver()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['usuario'])) {
            header('Location: ' . url('login')); exit();
        }
        require __DIR__ . '/../../config/database.php';

        $id        = $_GET['id'] ?? null;
        $idUsuario = $_SESSION['usuario']['id'];
        $rol       = $_SESSION['usuario']['rol'] ?? '';

        if (!$id) { header('Location: ' . url('/')); exit(); }

        // Cargar recibo con todos los datos
        $stmt = $conn->prepare("
            SELECT
                rc.idRecibo, rc.numero, rc.fecha, rc.total,
                p.monto, p.fechaPago, p.comprobante,
                mp.nombre  AS metodo_pago,
                ep.nombre  AS estado_pago,
                r.idReserva, r.fechaInicio, r.fechaFin,
                r.cantidadPersonas, r.precioTotal,
                h.numero   AS hab_numero, h.piso,
                t.nombre   AS tipo_habitacion, t.precioBase,
                u.nombre   AS cliente_nombre, u.paterno AS cliente_paterno,
                u.email    AS cliente_email, u.ci AS cliente_ci,
                u.telefono AS cliente_telefono,
                u.idUsuario AS id_cliente
            FROM recibo rc
            JOIN pago p         ON rc.idPago_FK        = p.idPago
            JOIN estado_pago ep ON p.idEstadoPago_FK   = ep.idEstado
            JOIN metodo_pago mp ON p.idMetodoPago_FK   = mp.idMetodoPago
            JOIN reserva r      ON p.idReserva_FK      = r.idReserva
            JOIN habitacion h   ON r.idHabitacion_FK   = h.idHabitacion
            JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            JOIN usuario u      ON r.idUsuario_FK      = u.idUsuario
            WHERE rc.idRecibo = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $recibo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$recibo) {
            http_response_code(404);
            include __DIR__ . '/../../public/404.php'; exit();
        }

        // Verificar acceso — cliente solo puede ver sus propios recibos
        if (!in_array($rol, ['Administrador','Recepcionista','Gerente','Contador'])) {
            if ($recibo['id_cliente'] != $idUsuario) {
                http_response_code(403);
                echo '<p>Acceso no autorizado.</p>'; exit();
            }
        }

        // Servicios de la reserva
        $svcStmt = $conn->prepare("
            SELECT s.nombre, rs.cantidad, rs.precioUnitario,
                   (rs.cantidad * rs.precioUnitario) AS subtotal
            FROM reserva_servicio rs
            JOIN servicio s ON rs.idServicio = s.idServicio
            WHERE rs.idReserva = ?
        ");
        $svcStmt->execute([$recibo['idReserva']]);
        $servicios = $svcStmt->fetchAll(PDO::FETCH_ASSOC);

        $noches         = (new DateTime($recibo['fechaInicio']))->diff(new DateTime($recibo['fechaFin']))->days;
        $totalServicios = array_sum(array_column($servicios, 'subtotal'));
        $subtotalHab    = $noches * $recibo['precioBase'];

        // Si es descarga, enviar como PDF imprimible con header
        $descarga = isset($_GET['descargar']);

        if ($descarga) {
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: inline; filename="Recibo-' . $recibo['numero'] . '.html"');
        }

        include __DIR__ . '/../../views/recibo/ver.php';
        exit();
    }
}