<?php
require_once __DIR__ . '/../helpers/auth.php';

class ReporteController
{
    public function index()
    {
        require_gerente();
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

    // ─── Exportar a CSV (abre en Excel) ──────────────────────────────────────
    public function exportarCSV()
    {
        require_gerente();
        require __DIR__ . '/../../config/database.php';

        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');
        $tipo  = $_GET['tipo']  ?? 'pagos'; // pagos | reservas | inventario

        switch ($tipo) {
            case 'reservas':
                $this->exportarReservasCSV($conn, $desde, $hasta);
                break;
            case 'inventario':
                $this->exportarInventarioCSV($conn);
                break;
            default:
                $this->exportarPagosCSV($conn, $desde, $hasta);
                break;
        }
    }

    private function exportarPagosCSV($conn, $desde, $hasta)
    {
        $stmt = $conn->prepare("
            SELECT
                p.codigo       AS codigo_pago,
                rc.numero      AS recibo,
                u.nombre       AS cliente_nombre,
                u.paterno      AS cliente_paterno,
                u.ci           AS ci,
                u.email        AS email,
                mp.nombre      AS metodo_pago,
                ep.nombre      AS estado_pago,
                p.monto        AS monto,
                p.fechaPago    AS fecha_pago,
                h.numero       AS habitacion,
                t.nombre       AS tipo_habitacion,
                r.fechaInicio  AS fecha_entrada,
                r.fechaFin     AS fecha_salida
            FROM pago p
            JOIN estado_pago ep  ON p.idEstadoPago_FK = ep.idEstado
            JOIN metodo_pago mp  ON p.idMetodoPago_FK = mp.idMetodoPago
            JOIN reserva r       ON p.idReserva_FK    = r.idReserva
            JOIN usuario u       ON r.idUsuario_FK    = u.idUsuario
            JOIN habitacion h    ON r.idHabitacion_FK = h.idHabitacion
            JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            LEFT JOIN recibo rc  ON rc.idPago_FK      = p.idPago
            WHERE ep.nombre = 'Pagado'
              AND DATE(p.fechaPago) BETWEEN ? AND ?
            ORDER BY p.fechaPago DESC
        ");
        $stmt->execute([$desde, $hasta]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "Reporte_Pagos_{$desde}_{$hasta}.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        // BOM para que Excel abra correctamente con tildes
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        // Encabezados
        fputcsv($out, [
            'Código Pago', 'N° Recibo', 'Nombre', 'Apellido', 'CI', 'Email',
            'Método', 'Estado', 'Monto (Bs.)', 'Fecha Pago',
            'Habitación', 'Tipo', 'Entrada', 'Salida'
        ], ';');

        foreach ($rows as $row) {
            fputcsv($out, array_values($row), ';');
        }

        // Totales
        $total = array_sum(array_column($rows, 'monto'));
        fputcsv($out, [], ';');
        fputcsv($out, ['', '', '', '', '', '', '', 'TOTAL', $total, '', '', '', '', ''], ';');

        fclose($out);
        exit();
    }

    private function exportarReservasCSV($conn, $desde, $hasta)
    {
        $stmt = $conn->prepare("
            SELECT
                r.codigo       AS codigo_reserva,
                u.nombre       AS cliente_nombre,
                u.paterno      AS cliente_paterno,
                u.ci           AS ci,
                u.email        AS email,
                h.numero       AS habitacion,
                t.nombre       AS tipo_habitacion,
                r.fechaInicio  AS entrada,
                r.fechaFin     AS salida,
                DATEDIFF(r.fechaFin, r.fechaInicio) AS noches,
                r.cantidadPersonas AS personas,
                r.precioTotal  AS total_bs,
                er.nombre      AS estado
            FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            JOIN usuario u         ON r.idUsuario_FK       = u.idUsuario
            JOIN habitacion h      ON r.idHabitacion_FK    = h.idHabitacion
            JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            WHERE DATE(r.fechaInicio) BETWEEN ? AND ?
            ORDER BY r.fechaInicio DESC
        ");
        $stmt->execute([$desde, $hasta]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filename = "Reporte_Reservas_{$desde}_{$hasta}.csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, [
            'Código', 'Nombre', 'Apellido', 'CI', 'Email',
            'Habitación', 'Tipo', 'Entrada', 'Salida', 'Noches',
            'Personas', 'Total (Bs.)', 'Estado'
        ], ';');

        foreach ($rows as $row) {
            fputcsv($out, array_values($row), ';');
        }

        fclose($out);
        exit();
    }

    private function exportarInventarioCSV($conn)
    {
        $rows = $conn->query("
            SELECT
                p.codigo       AS codigo,
                p.nombre       AS producto,
                c.nombre       AS categoria,
                p.precio       AS precio_bs,
                p.stock        AS stock_actual,
                p.stockMinimo  AS stock_minimo,
                p.unidad       AS unidad,
                p.estado       AS estado,
                CASE WHEN p.stock <= p.stockMinimo THEN 'BAJO' ELSE 'OK' END AS alerta_stock
            FROM producto p
            LEFT JOIN categoria_producto c ON p.idCategoria_FK = c.idCategoria
            ORDER BY c.nombre, p.nombre
        ")->fetchAll(PDO::FETCH_ASSOC);

        $filename = "Reporte_Inventario_" . date('Y-m-d') . ".csv";
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, [
            'Código', 'Producto', 'Categoría', 'Precio (Bs.)',
            'Stock Actual', 'Stock Mínimo', 'Unidad', 'Estado', 'Alerta'
        ], ';');

        foreach ($rows as $row) {
            fputcsv($out, array_values($row), ';');
        }

        fclose($out);
        exit();
    }

}