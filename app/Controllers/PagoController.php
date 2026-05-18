<?php
require_once __DIR__ . '/../helpers/auth.php';
 
class PagoController
{
    // ─────────────────────────────────────────────
    // GET /admin/pagos  →  listado de pagos
    // ─────────────────────────────────────────────
    public function index()
    {
        require_recepcionista();
        require __DIR__ . '/../../config/database.php';
 
        $stmt = $conn->query("
            SELECT
                p.*,
                ep.nombre           AS estado_pago,
                mp.nombre           AS metodo_pago,
                r.fechaInicio,
                r.fechaFin,
                r.codigo            AS reserva_codigo,
                u.nombre            AS cliente_nombre,
                u.paterno           AS cliente_paterno,
                u.email             AS cliente_email,
                rc.numero           AS recibo_numero,
                rc.idRecibo
            FROM pago p
            LEFT JOIN estado_pago  ep ON p.idEstadoPago_FK  = ep.idEstado
            LEFT JOIN metodo_pago  mp ON p.idMetodoPago_FK  = mp.idMetodoPago
            LEFT JOIN reserva       r  ON p.idReserva_FK     = r.idReserva
            LEFT JOIN usuario       u  ON r.idUsuario_FK     = u.idUsuario
            LEFT JOIN recibo        rc ON rc.idPago_FK       = p.idPago
            ORDER BY p.fechaPago DESC
        ");
        $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        // Stats
        $stats['total']      = $conn->query("SELECT COUNT(*) FROM pago")->fetchColumn();
        $stats['pagados']    = $conn->query("SELECT COUNT(*) FROM pago p JOIN estado_pago e ON p.idEstadoPago_FK = e.idEstado WHERE e.nombre = 'Pagado'")->fetchColumn();
        $stats['pendientes'] = $conn->query("SELECT COUNT(*) FROM pago p JOIN estado_pago e ON p.idEstadoPago_FK = e.idEstado WHERE e.nombre = 'Pendiente'")->fetchColumn();
        $stats['monto']      = $conn->query("SELECT COALESCE(SUM(monto),0) FROM pago p JOIN estado_pago e ON p.idEstadoPago_FK = e.idEstado WHERE e.nombre = 'Pagado'")->fetchColumn();
 
        ob_start();
        include __DIR__ . '/../../views/admin/pagos/index.php';
        $content = ob_get_clean();
 
        $title = "Pagos | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }
 
    // ─────────────────────────────────────────────
    // GET /admin/pagos/crear  →  formulario nuevo pago
    // ─────────────────────────────────────────────
    public function create()
    {
        require_recepcionista();
        require __DIR__ . '/../../config/database.php';
 
        // Reservas confirmadas sin pago pagado
        $reservas = $conn->query("
            SELECT
                r.idReserva,
                r.codigo,
                r.fechaInicio,
                r.fechaFin,
                r.precioTotal,
                u.nombre    AS cliente_nombre,
                u.paterno   AS cliente_paterno,
                h.numero    AS habitacion_numero,
                COALESCE(SUM(p.monto), 0) AS monto_pagado
            FROM reserva r
            LEFT JOIN usuario       u  ON r.idUsuario_FK    = u.idUsuario
            LEFT JOIN habitacion    h  ON r.idHabitacion_FK = h.idHabitacion
            LEFT JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            LEFT JOIN pago          p  ON p.idReserva_FK = r.idReserva
                AND p.idEstadoPago_FK = (SELECT idEstado FROM estado_pago WHERE nombre = 'Pagado' LIMIT 1)
            WHERE er.nombre IN ('Confirmada', 'Completada')
            GROUP BY r.idReserva
            HAVING monto_pagado < r.precioTotal OR r.precioTotal IS NULL
            ORDER BY r.fechaInicio DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
 
        $metodos = $conn->query("SELECT * FROM metodo_pago ORDER BY idMetodoPago")->fetchAll(PDO::FETCH_ASSOC);
        $preseleccionado = $_GET['idReserva'] ?? null;

        ob_start();
        include __DIR__ . '/../../views/admin/pagos/create.php';
        $content = ob_get_clean();
 
        $title = "Registrar Pago | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }
 
    // ─────────────────────────────────────────────
    // POST /admin/pagos/crear  →  guardar pago
    // ─────────────────────────────────────────────
    public function store()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $idReserva   = $_POST['idReserva']   ?? null;
        $monto       = $_POST['monto']       ?? null;
        $idMetodo    = $_POST['idMetodoPago'] ?? null;
 
        if (!$idReserva || !$monto || !$idMetodo) {
            $_SESSION['error'] = "Todos los campos son obligatorios.";
            header("Location: " . url('admin/pagos/crear'));
            exit();
        }
 
        try {
            // Estado "Pagado"
            $idEstPagado = $conn->query("SELECT idEstado FROM estado_pago WHERE nombre = 'Pagado' LIMIT 1")->fetchColumn();
 
            // Empleado del usuario en sesión
            $idEmpleado = $conn->prepare("SELECT idEmpleado FROM empleado WHERE idUsuario_FK = ? LIMIT 1");
            $idEmpleado->execute([$_SESSION['usuario']['id']]);
            $idEmpleado = $idEmpleado->fetchColumn();
 
            $conn->beginTransaction();
 
            // Insertar pago
            $stmt = $conn->prepare("
                INSERT INTO pago (monto, idEstadoPago_FK, idReserva_FK, idMetodoPago_FK, idEmpleado_FK)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$monto, $idEstPagado, $idReserva, $idMetodo, $idEmpleado ?: null]);
            $idPago = $conn->lastInsertId();
 
            // Generar recibo automático
            $numRecibo = 'REC-' . strtoupper(substr(uniqid(), -6));
            $conn->prepare("
                INSERT INTO recibo (numero, total, idPago_FK)
                VALUES (?, ?, ?)
            ")->execute([$numRecibo, $monto, $idPago]);
 
            // Verificar si la reserva quedó totalmente pagada
            $precioTotal  = $conn->prepare("SELECT precioTotal FROM reserva WHERE idReserva = ?");
            $precioTotal->execute([$idReserva]);
            $precioTotal  = $precioTotal->fetchColumn();
 
            $totalPagado  = $conn->prepare("
                SELECT COALESCE(SUM(monto),0) FROM pago
                WHERE idReserva_FK = ? AND idEstadoPago_FK = ?
            ");
            $totalPagado->execute([$idReserva, $idEstPagado]);
            $totalPagado  = $totalPagado->fetchColumn();
 
            if ($precioTotal && $totalPagado >= $precioTotal) {
                $idEstCompletada = $conn->query("SELECT idEstado FROM estado_reserva WHERE nombre = 'Completada' LIMIT 1")->fetchColumn();
                if ($idEstCompletada) {
                    $conn->prepare("UPDATE reserva SET idEstadoReserva_FK = ? WHERE idReserva = ?")
                         ->execute([$idEstCompletada, $idReserva]);
                }
            }
 
            // Bitácora
            $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                 ->execute(["Registró pago ID $idPago por Bs. $monto en reserva ID $idReserva", $_SESSION['usuario']['id']]);
 
            $conn->commit();
 
            $_SESSION['success'] = "Pago registrado correctamente. Recibo: $numRecibo";
            header("Location: " . url('admin/pagos'));
            exit();
 
        } catch (PDOException $e) {
            $conn->rollBack();
            error_log($e->getMessage());
            $_SESSION['error'] = "Error al registrar el pago.";
            header("Location: " . url('admin/pagos/crear'));
            exit();
        }
    }
 
    // ─────────────────────────────────────────────
    // GET /admin/pagos/estado  →  cambiar estado pago
    // ─────────────────────────────────────────────
    public function cambiarEstado()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $id     = $_GET['id']     ?? null;
        $estado = $_GET['estado'] ?? null;
 
        $permitidos = ['Pendiente', 'Pagado', 'Cancelado', 'Reembolsado', 'Parcial'];
        if (!$id || !in_array($estado, $permitidos)) {
            header("Location: " . url('admin/pagos'));
            exit();
        }
 
        try {
            $idEstado = $conn->prepare("SELECT idEstado FROM estado_pago WHERE nombre = ? LIMIT 1");
            $idEstado->execute([$estado]);
            $idEstado = $idEstado->fetchColumn();
 
            $conn->prepare("UPDATE pago SET idEstadoPago_FK = ? WHERE idPago = ?")
                 ->execute([$idEstado, $id]);
 
            // Si se aprueba, generar recibo automático y notificar al cliente
            if ($estado === 'Pagado') {
                $tieneRecibo = $conn->prepare("SELECT COUNT(*) FROM recibo WHERE idPago_FK = ?");
                $tieneRecibo->execute([$id]);
                $numRecibo = null;
                if ($tieneRecibo->fetchColumn() == 0) {
                    $montoStmt = $conn->prepare("SELECT monto FROM pago WHERE idPago = ? LIMIT 1");
                    $montoStmt->execute([$id]);
                    $montoVal  = $montoStmt->fetchColumn();
                    $numRecibo = 'REC-' . strtoupper(substr(uniqid(), -6));
                    $conn->prepare("INSERT INTO recibo (numero, total, idPago_FK) VALUES (?, ?, ?)")
                         ->execute([$numRecibo, $montoVal, $id]);
                } else {
                    $montoStmt = $conn->prepare("SELECT p.monto, r.numero FROM pago p LEFT JOIN recibo r ON r.idPago_FK = p.idPago WHERE p.idPago = ? LIMIT 1");
                    $montoStmt->execute([$id]);
                    $row = $montoStmt->fetch(PDO::FETCH_ASSOC);
                    $montoVal  = $row['monto'];
                    $numRecibo = $row['numero'];
                }
                // Email al cliente
                $clienteStmt = $conn->prepare("
                    SELECT u.email, u.nombre, u.paterno FROM pago p
                    JOIN reserva r ON p.idReserva_FK = r.idReserva
                    JOIN usuario u ON r.idUsuario_FK = u.idUsuario
                    WHERE p.idPago = ? LIMIT 1
                ");
                $clienteStmt->execute([$id]);
                $cli = $clienteStmt->fetch(PDO::FETCH_ASSOC);
                if ($cli) {
                    require_once __DIR__ . '/../../app/Mail/mailer.php';
                    Mailer::enviarPagoAprobado($cli['email'], $cli['nombre'] . ' ' . $cli['paterno'], $numRecibo ?? 'N/A', $montoVal);
                }
            }

            // Si se rechaza, notificar al cliente
            if ($estado === 'Cancelado') {
                $clienteStmt = $conn->prepare("
                    SELECT u.email, u.nombre, u.paterno FROM pago p
                    JOIN reserva r ON p.idReserva_FK = r.idReserva
                    JOIN usuario u ON r.idUsuario_FK = u.idUsuario
                    WHERE p.idPago = ? LIMIT 1
                ");
                $clienteStmt->execute([$id]);
                $cli = $clienteStmt->fetch(PDO::FETCH_ASSOC);
                if ($cli) {
                    require_once __DIR__ . '/../../app/Mail/mailer.php';
                    Mailer::enviarPagoRechazado($cli['email'], $cli['nombre'] . ' ' . $cli['paterno']);
                }
            }

            $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                 ->execute(["Cambió estado de pago ID $id a $estado", $_SESSION['usuario']['id']]);

            $_SESSION['success'] = "Estado del pago actualizado a: $estado";
 
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Error al actualizar el estado del pago.";
        }
 
        header("Location: " . url('admin/pagos'));
        exit();
    }
}