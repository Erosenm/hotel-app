<?php
require_once __DIR__ . '/../helpers/auth.php';

class ReporteController
{
    public function index()
    {
        require_admin();
        require __DIR__ . '/../../config/database.php';

        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');

        // Ingresos por período
        $ingresos = $conn->prepare("
            SELECT COALESCE(SUM(p.monto), 0) AS total,
                   COUNT(p.idPago) AS cantidad,
                   mp.nombre AS metodo,
                   COALESCE(SUM(CASE WHEN mp.nombre='Efectivo' THEN p.monto ELSE 0 END), 0) AS efectivo,
                   COALESCE(SUM(CASE WHEN mp.nombre='Tarjeta'  THEN p.monto ELSE 0 END), 0) AS tarjeta,
                   COALESCE(SUM(CASE WHEN mp.nombre='QR'       THEN p.monto ELSE 0 END), 0) AS qr
            FROM pago p
            JOIN estado_pago ep ON p.idEstadoPago_FK = ep.idEstado
            JOIN metodo_pago mp ON p.idMetodoPago_FK = mp.idMetodoPago
            WHERE ep.nombre = 'Pagado'
              AND DATE(p.fechaPago) BETWEEN ? AND ?
            GROUP BY mp.nombre
        ");
        $ingresos->execute([$desde, $hasta]);
        $ingresos = $ingresos->fetchAll(PDO::FETCH_ASSOC);

        $totalIngresos = array_sum(array_column($ingresos, 'total'));

        // Reservas por período
        $reservas = $conn->prepare("
            SELECT er.nombre AS estado, COUNT(*) AS total
            FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            WHERE DATE(r.fechaInicio) BETWEEN ? AND ?
            GROUP BY er.nombre
        ");
        $reservas->execute([$desde, $hasta]);
        $reservasPeriodo = $reservas->fetchAll(PDO::FETCH_ASSOC);

        // Habitación más reservada
        $habMasReservada = $conn->prepare("
            SELECT h.numero, t.nombre AS tipo, COUNT(*) AS total_reservas
            FROM reserva r
            JOIN habitacion h ON r.idHabitacion_FK = h.idHabitacion
            JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            WHERE DATE(r.fechaInicio) BETWEEN ? AND ?
            GROUP BY h.idHabitacion
            ORDER BY total_reservas DESC LIMIT 5
        ");
        $habMasReservada->execute([$desde, $hasta]);
        $habitacionesTop = $habMasReservada->fetchAll(PDO::FETCH_ASSOC);

        // Últimos pagos del período
        $pagos = $conn->prepare("
            SELECT p.monto, p.fechaPago,
                   ep.nombre AS estado, mp.nombre AS metodo,
                   u.nombre AS cliente_nombre, u.paterno AS cliente_paterno,
                   r.fechaInicio, r.fechaFin,
                   rc.numero AS recibo
            FROM pago p
            JOIN estado_pago ep  ON p.idEstadoPago_FK = ep.idEstado
            JOIN metodo_pago mp  ON p.idMetodoPago_FK = mp.idMetodoPago
            JOIN reserva r       ON p.idReserva_FK    = r.idReserva
            JOIN usuario u       ON r.idUsuario_FK    = u.idUsuario
            LEFT JOIN recibo rc  ON rc.idPago_FK      = p.idPago
            WHERE ep.nombre = 'Pagado' AND DATE(p.fechaPago) BETWEEN ? AND ?
            ORDER BY p.fechaPago DESC
        ");
        $pagos->execute([$desde, $hasta]);
        $pagosDetalle = $pagos->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/reportes/index.php';
        $content = ob_get_clean();
        $title = "Reportes | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }
}