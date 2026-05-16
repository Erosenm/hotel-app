<?php
require_once __DIR__ . '/../helpers/auth.php';
 
class AdminPanelController
{
    public function index()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();

        // Gerente y Contador solo ven reportes
        $rolActual = $_SESSION['usuario']['rol'] ?? '';
        if (in_array($rolActual, ['Gerente', 'Contador'])) {
            header('Location: ' . url('admin/reportes'));
            exit();
        }

        require __DIR__ . '/../../config/database.php';
 
        $stmt = $conn->query("
            SELECT u.*, r.nombre AS rol
            FROM usuario u
            LEFT JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            LEFT JOIN rol r ON ur.idRol = r.idRol
            ORDER BY u.fechaRegistro DESC
        ");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $stats = [];
        $stats['total_usuarios']           = $conn->query("SELECT COUNT(*) FROM usuario")->fetchColumn();
        $stats['usuarios_activos']         = $conn->query("SELECT COUNT(*) FROM usuario WHERE estado = 'Activo'")->fetchColumn();
        $stats['total_habitaciones']       = $conn->query("SELECT COUNT(*) FROM habitacion")->fetchColumn();
        $stats['habitaciones_disponibles'] = $conn->query("
            SELECT COUNT(*) FROM habitacion h
            JOIN estado_habitacion e ON h.idEstadoHabitacion_FK = e.idEstado
            WHERE e.nombre = 'Disponible'
        ")->fetchColumn();
        $stats['reservas_pendientes']      = $conn->query("
            SELECT COUNT(*) FROM reserva r
            JOIN estado_reserva e ON r.idEstadoReserva_FK = e.idEstado
            WHERE e.nombre = 'Pendiente'
        ")->fetchColumn();
        $stats['reservas_confirmadas']     = $conn->query("
            SELECT COUNT(*) FROM reserva r
            JOIN estado_reserva e ON r.idEstadoReserva_FK = e.idEstado
            WHERE e.nombre = 'Confirmada'
        ")->fetchColumn();
 
        // ── STATS DE PAGOS ──────────────────────────────────
        $stats['pagos_hoy']     = $conn->query("
            SELECT COUNT(*) FROM pago p
            JOIN estado_pago e ON p.idEstadoPago_FK = e.idEstado
            WHERE e.nombre = 'Pagado' AND DATE(p.fechaPago) = CURDATE()
        ")->fetchColumn();
        $stats['monto_hoy']     = $conn->query("
            SELECT COALESCE(SUM(monto), 0) FROM pago p
            JOIN estado_pago e ON p.idEstadoPago_FK = e.idEstado
            WHERE e.nombre = 'Pagado' AND DATE(p.fechaPago) = CURDATE()
        ")->fetchColumn();
        $stats['monto_mes']     = $conn->query("
            SELECT COALESCE(SUM(monto), 0) FROM pago p
            JOIN estado_pago e ON p.idEstadoPago_FK = e.idEstado
            WHERE e.nombre = 'Pagado'
              AND MONTH(p.fechaPago) = MONTH(CURDATE())
              AND YEAR(p.fechaPago)  = YEAR(CURDATE())
        ")->fetchColumn();
        $stats['pagos_pendientes'] = $conn->query("
            SELECT COUNT(*) FROM pago p
            JOIN estado_pago e ON p.idEstadoPago_FK = e.idEstado
            WHERE e.nombre = 'Pendiente'
        ")->fetchColumn();
 
        // Últimos 5 pagos para la tabla del dashboard
        $ultimos_pagos = $conn->query("
            SELECT
                p.monto,
                p.fechaPago,
                ep.nombre  AS estado_pago,
                mp.nombre  AS metodo_pago,
                u.nombre   AS cliente_nombre,
                u.paterno  AS cliente_paterno,
                rc.numero  AS recibo_numero
            FROM pago p
            LEFT JOIN estado_pago ep ON p.idEstadoPago_FK = ep.idEstado
            LEFT JOIN metodo_pago mp ON p.idMetodoPago_FK = mp.idMetodoPago
            LEFT JOIN reserva      r  ON p.idReserva_FK   = r.idReserva
            LEFT JOIN usuario      u  ON r.idUsuario_FK   = u.idUsuario
            LEFT JOIN recibo       rc ON rc.idPago_FK     = p.idPago
            ORDER BY p.fechaPago DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
 
        $roles = $conn->query("SELECT idRol, nombre FROM rol")->fetchAll(PDO::FETCH_ASSOC);

        // Ingresos últimos 6 meses para gráfica
        $ingresos_meses = $conn->query("
            SELECT DATE_FORMAT(p.fechaPago, '%Y-%m') AS mes,
                   DATE_FORMAT(p.fechaPago, '%b %Y') AS mes_label,
                   COALESCE(SUM(p.monto), 0) AS total
            FROM pago p
            JOIN estado_pago ep ON p.idEstadoPago_FK = ep.idEstado
            WHERE ep.nombre = 'Pagado'
              AND p.fechaPago >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY mes, mes_label
            ORDER BY mes ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Reservas por estado para gráfica torta
        $reservas_estado = $conn->query("
            SELECT er.nombre AS estado, COUNT(*) AS total
            FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            GROUP BY er.nombre
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Habitaciones por estado
        $habitaciones_estado = $conn->query("
            SELECT eh.nombre AS estado, COUNT(*) AS total
            FROM habitacion h
            JOIN estado_habitacion eh ON h.idEstadoHabitacion_FK = eh.idEstado
            GROUP BY eh.nombre
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Stock bajo
        $productos_bajo_stock = $conn->query("
            SELECT nombre, stock, stockMinimo
            FROM producto
            WHERE stock <= stockMinimo AND estado = 'Activo'
            ORDER BY stock ASC LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);
 
        ob_start();
        include __DIR__ . '/../../views/admin/dashboard.php';
        $content = ob_get_clean();
 
        $title = "Panel Admin | Hotel";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }
}