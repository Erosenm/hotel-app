<?php
require_once __DIR__ . '/../helpers/auth.php';
 
class ClienteController
{
    public function dashboard()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $idUsuario = $_SESSION['usuario']['id'];
 
        $stmt = $conn->prepare("
            SELECT r.*,
                   h.numero AS habitacion_numero, h.piso,
                   t.nombre AS tipo_habitacion, t.precioBase,
                   er.nombre AS estado_reserva,
                   (SELECT rutaImagen FROM habitacion_imagen WHERE idHabitacion_FK = h.idHabitacion LIMIT 1) AS imagen
            FROM reserva r
            JOIN habitacion h       ON r.idHabitacion_FK      = h.idHabitacion
            JOIN tipo_habitacion t  ON h.idTipoHabitacion_FK  = t.idTipoHabitacion
            JOIN estado_reserva er  ON r.idEstadoReserva_FK   = er.idEstado
            WHERE r.idUsuario_FK = ?
            ORDER BY r.fechaInicio DESC
            LIMIT 5
        ");
        $stmt->execute([$idUsuario]);
        $reservasRecientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        $totales = $conn->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN er.nombre = 'Confirmada' THEN 1 ELSE 0 END) AS confirmadas,
                SUM(CASE WHEN er.nombre = 'Pendiente'  THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN er.nombre = 'Completada' THEN 1 ELSE 0 END) AS completadas
            FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            WHERE r.idUsuario_FK = ?
        ");
        $totales->execute([$idUsuario]);
        $stats = $totales->fetch(PDO::FETCH_ASSOC);
 
        ob_start();
        include __DIR__ . '/../../views/clientes/dashboard.php';
        $content = ob_get_clean();
 
        $title = "Mi Panel | Hotel Real Plaza";
        include __DIR__ . '/../../views/layouts/app_layout.php';
    }
 
    public function habitaciones()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $tipos = $conn->query("SELECT * FROM tipo_habitacion ORDER BY precioBase ASC")->fetchAll(PDO::FETCH_ASSOC);
 
        $filtroTipo     = $_GET['tipo']    ?? '';
        $filtroPrecio   = $_GET['precio']  ?? '';
        $filtroFechaIn  = $_GET['entrada'] ?? '';
        $filtroFechaOut = $_GET['salida']  ?? '';
 
        $where  = ["e.nombre = 'Disponible'"];
        $params = [];
 
        if (!empty($filtroTipo)) {
            $where[]  = "h.idTipoHabitacion_FK = ?";
            $params[] = $filtroTipo;
        }
        if (!empty($filtroPrecio)) {
            $where[]  = "t.precioBase <= ?";
            $params[] = $filtroPrecio;
        }
        if (!empty($filtroFechaIn) && !empty($filtroFechaOut)) {
            $where[] = "h.idHabitacion NOT IN (
                SELECT idHabitacion_FK FROM reserva r2
                JOIN estado_reserva er2 ON r2.idEstadoReserva_FK = er2.idEstado
                WHERE er2.nombre NOT IN ('Cancelada','Completada')
                AND r2.fechaInicio < ? AND r2.fechaFin > ?
            )";
            $params[] = $filtroFechaOut;
            $params[] = $filtroFechaIn;
        }
 
        $sql = "
            SELECT h.*,
                   t.nombre AS tipo, t.descripcion AS tipo_desc, t.precioBase AS precio,
                   e.nombre AS estado,
                   (SELECT rutaImagen FROM habitacion_imagen WHERE idHabitacion_FK = h.idHabitacion LIMIT 1) AS imagen
            FROM habitacion h
            JOIN tipo_habitacion t   ON h.idTipoHabitacion_FK  = t.idTipoHabitacion
            JOIN estado_habitacion e ON h.idEstadoHabitacion_FK = e.idEstado
            WHERE " . implode(' AND ', $where) . "
            ORDER BY t.precioBase ASC
        ";
 
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        ob_start();
        include __DIR__ . '/../../views/clientes/habitaciones.php';
        $content = ob_get_clean();
 
        $title = "Habitaciones | Hotel Real Plaza";
        include __DIR__ . '/../../views/layouts/app_layout.php';
    }
 
    public function detalleHabitacion()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: " . url('habitaciones')); exit(); }
 
        $stmt = $conn->prepare("
            SELECT h.*,
                   t.nombre AS tipo, t.descripcion AS tipo_desc, t.precioBase AS precio,
                   e.nombre AS estado
            FROM habitacion h
            JOIN tipo_habitacion t   ON h.idTipoHabitacion_FK  = t.idTipoHabitacion
            JOIN estado_habitacion e ON h.idEstadoHabitacion_FK = e.idEstado
            WHERE h.idHabitacion = ?
        ");
        $stmt->execute([$id]);
        $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$habitacion) { header("Location: " . url('habitaciones')); exit(); }
 
        $imgs = $conn->prepare("SELECT * FROM habitacion_imagen WHERE idHabitacion_FK = ?");
        $imgs->execute([$id]);
        $imagenes = $imgs->fetchAll(PDO::FETCH_ASSOC);
 
        ob_start();
        include __DIR__ . '/../../views/clientes/detalle_habitacion.php';
        $content = ob_get_clean();
 
        $title = "Habitación {$habitacion['numero']} | Hotel Real Plaza";
        include __DIR__ . '/../../views/layouts/app_layout.php';
    }
 
    public function reservar()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: " . url('habitaciones')); exit(); }
 
        $stmt = $conn->prepare("
            SELECT h.*, t.nombre AS tipo, t.precioBase AS precio, e.nombre AS estado
            FROM habitacion h
            JOIN tipo_habitacion t   ON h.idTipoHabitacion_FK  = t.idTipoHabitacion
            JOIN estado_habitacion e ON h.idEstadoHabitacion_FK = e.idEstado
            WHERE h.idHabitacion = ? AND e.nombre = 'Disponible'
        ");
        $stmt->execute([$id]);
        $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$habitacion) {
            $_SESSION['error'] = "Esta habitación no está disponible.";
            header("Location: " . url('habitaciones'));
            exit();
        }
 
        ob_start();
        include __DIR__ . '/../../views/clientes/reservar.php';
        $content = ob_get_clean();
 
        $title = "Reservar | Hotel Real Plaza";
        include __DIR__ . '/../../views/layouts/app_layout.php';
    }
 
    public function guardarReserva()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $idHabitacion     = $_POST['idHabitacion']     ?? null;
        $fechaInicio      = $_POST['fechaInicio']       ?? '';
        $fechaFin         = $_POST['fechaFin']          ?? '';
        $cantidadPersonas = $_POST['cantidadPersonas']  ?? 1;
        $idUsuario        = $_SESSION['usuario']['id'];
 
        if (!$idHabitacion || empty($fechaInicio) || empty($fechaFin)) {
            $_SESSION['error'] = "Completa todos los campos.";
            header("Location: " . url('reservar?id=' . $idHabitacion));
            exit();
        }
        if ($fechaInicio >= $fechaFin) {
            $_SESSION['error'] = "La fecha de salida debe ser posterior a la de entrada.";
            header("Location: " . url('reservar?id=' . $idHabitacion));
            exit();
        }
        if ($fechaInicio < date('Y-m-d')) {
            $_SESSION['error'] = "La fecha de entrada no puede ser en el pasado.";
            header("Location: " . url('reservar?id=' . $idHabitacion));
            exit();
        }
 
        try {
            $check = $conn->prepare("
                SELECT COUNT(*) FROM reserva r
                JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
                WHERE r.idHabitacion_FK = ?
                AND er.nombre NOT IN ('Cancelada','Completada')
                AND r.fechaInicio < ? AND r.fechaFin > ?
            ");
            $check->execute([$idHabitacion, $fechaFin, $fechaInicio]);
            if ($check->fetchColumn() > 0) {
                $_SESSION['error'] = "La habitación no está disponible en esas fechas.";
                header("Location: " . url('reservar?id=' . $idHabitacion));
                exit();
            }
 
            $precio = $conn->prepare("
                SELECT t.precioBase FROM habitacion h
                JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
                WHERE h.idHabitacion = ?
            ");
            $precio->execute([$idHabitacion]);
            $precioBase = $precio->fetchColumn();
 
            $dias        = (new DateTime($fechaInicio))->diff(new DateTime($fechaFin))->days;
            $precioTotal = $dias * $precioBase;
 
            $estadoId = $conn->query("SELECT idEstado FROM estado_reserva WHERE nombre = 'Pendiente' LIMIT 1")->fetchColumn();
 
            $conn->prepare("
                INSERT INTO reserva (codigo, fechaInicio, fechaFin, cantidadPersonas, precioTotal, idEstadoReserva_FK, idUsuario_FK, idHabitacion_FK)
                VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?)
            ")->execute([$fechaInicio, $fechaFin, $cantidadPersonas, $precioTotal, $estadoId, $idUsuario, $idHabitacion]);
 
            $idReserva = $conn->lastInsertId();
 
            $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                 ->execute(["Cliente realizó reserva ID $idReserva", $idUsuario]);

            // Email de confirmación al cliente
            $datosCliente = $conn->prepare("SELECT nombre, paterno, email FROM usuario WHERE idUsuario = ? LIMIT 1");
            $datosCliente->execute([$idUsuario]);
            $cli = $datosCliente->fetch(PDO::FETCH_ASSOC);
            $datosHab = $conn->prepare("SELECT h.numero, t.nombre AS tipo FROM habitacion h JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion WHERE h.idHabitacion = ? LIMIT 1");
            $datosHab->execute([$idHabitacion]);
            $hab = $datosHab->fetch(PDO::FETCH_ASSOC);
            require_once __DIR__ . '/../Mail/mailer.php';
            Mailer::enviarConfirmacionReserva(
                $cli['email'],
                $cli['nombre'] . ' ' . $cli['paterno'],
                $hab['numero'],
                $hab['tipo'],
                $fechaInicio,
                $fechaFin,
                $dias,
                $precioTotal
            );

            $_SESSION['success'] = "¡Reserva realizada! Estamos confirmando tu solicitud.";
            header("Location: " . url('cliente/reservas'));
            exit();
 
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Error al procesar la reserva. Intenta nuevamente.";
            header("Location: " . url('reservar?id=' . $idHabitacion));
            exit();
        }
    }
 
    // GET /cliente/reservas/detalle  →  detalle de reserva con servicios
    public function detalleReserva()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $idReserva = $_GET['id'] ?? null;
        $idUsuario = $_SESSION['usuario']['id'];

        if (!$idReserva) {
            header('Location: ' . url('cliente/reservas')); exit();
        }

        // Verificar que la reserva pertenece al cliente
        $stmt = $conn->prepare("
            SELECT r.*,
                h.numero AS habitacion_numero, h.piso,
                t.nombre AS tipo_habitacion, t.precioBase,
                er.nombre AS estado_reserva,
                (SELECT rutaImagen FROM habitacion_imagen WHERE idHabitacion_FK = h.idHabitacion LIMIT 1) AS imagen
            FROM reserva r
            JOIN habitacion h      ON r.idHabitacion_FK     = h.idHabitacion
            JOIN tipo_habitacion t  ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            JOIN estado_reserva er ON r.idEstadoReserva_FK  = er.idEstado
            WHERE r.idReserva = ? AND r.idUsuario_FK = ?
            LIMIT 1
        ");
        $stmt->execute([$idReserva, $idUsuario]);
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) {
            header('Location: ' . url('cliente/reservas')); exit();
        }

        // Servicios de la reserva
        $servicios = $conn->prepare("
            SELECT s.nombre, s.precio, rs.cantidad, rs.precioUnitario,
                   (rs.cantidad * rs.precioUnitario) AS subtotal
            FROM reserva_servicio rs
            JOIN servicio s ON rs.idServicio = s.idServicio
            WHERE rs.idReserva = ?
        ");
        $servicios->execute([$idReserva]);
        $servicios = $servicios->fetchAll(PDO::FETCH_ASSOC);

        // Pagos de la reserva
        $pagos = $conn->prepare("
            SELECT p.monto, p.fechaPago, p.comprobante,
                   ep.nombre AS estado, mp.nombre AS metodo,
                   rc.numero AS recibo
            FROM pago p
            JOIN estado_pago ep ON p.idEstadoPago_FK = ep.idEstado
            JOIN metodo_pago mp ON p.idMetodoPago_FK = mp.idMetodoPago
            LEFT JOIN recibo rc ON rc.idPago_FK = p.idPago
            WHERE p.idReserva_FK = ?
            ORDER BY p.fechaPago DESC
        ");
        $pagos->execute([$idReserva]);
        $pagos = $pagos->fetchAll(PDO::FETCH_ASSOC);

        $totalServicios = array_sum(array_column($servicios, 'subtotal'));
        $totalPagado    = array_sum(array_map(fn($p) => $p['estado'] === 'Pagado' ? $p['monto'] : 0, $pagos));
        $pendiente      = max(0, $reserva['precioTotal'] - $totalPagado);
        $noches         = (new DateTime($reserva['fechaInicio']))->diff(new DateTime($reserva['fechaFin']))->days;

        // Servicios disponibles para solicitar
        $serviciosDisp = $conn->query("SELECT * FROM servicio ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/clientes/detalle_reserva.php';
        $content = ob_get_clean();
        $title = "Detalle Reserva | Hotel Real Plaza";
        include __DIR__ . '/../../views/layouts/app_layout.php';
    }

    public function misReservas()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $idUsuario = $_SESSION['usuario']['id'];
 
        $stmt = $conn->prepare("
            SELECT
                r.*,
                h.numero AS habitacion_numero, h.piso,
                t.nombre AS tipo_habitacion, t.precioBase,
                er.nombre AS estado_reserva,
                (SELECT rutaImagen FROM habitacion_imagen WHERE idHabitacion_FK = h.idHabitacion LIMIT 1) AS imagen,
                COALESCE(SUM(CASE WHEN ep2.nombre = 'Pagado' THEN p.monto ELSE 0 END), 0) AS monto_pagado,
                rc.numero AS recibo_numero,
                ep2.nombre AS pago_estado,
                mp.nombre  AS pago_metodo,
                (SELECT COUNT(*) FROM reserva_servicio rs WHERE rs.idReserva = r.idReserva) AS servicios_count
            FROM reserva r
            JOIN habitacion h       ON r.idHabitacion_FK     = h.idHabitacion
            JOIN tipo_habitacion t  ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            JOIN estado_reserva er  ON r.idEstadoReserva_FK  = er.idEstado
            LEFT JOIN pago p        ON p.idReserva_FK = r.idReserva
            LEFT JOIN estado_pago ep2 ON p.idEstadoPago_FK = ep2.idEstado
            LEFT JOIN metodo_pago mp  ON p.idMetodoPago_FK  = mp.idMetodoPago
            LEFT JOIN recibo rc     ON rc.idPago_FK = p.idPago
            WHERE r.idUsuario_FK = ?
            GROUP BY r.idReserva, rc.numero, ep2.nombre, mp.nombre
            ORDER BY r.fechaInicio DESC
        ");
        $stmt->execute([$idUsuario]);
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
        // Métodos de pago para el modal
        $metodos = $conn->query("SELECT * FROM metodo_pago ORDER BY idMetodoPago")->fetchAll(PDO::FETCH_ASSOC);
 
        ob_start();
        include __DIR__ . '/../../views/clientes/mis_reservas.php';
        $content = ob_get_clean();
 
        $title = "Mis Reservas | Hotel Real Plaza";
        include __DIR__ . '/../../views/layouts/app_layout.php';
    }
 
    public function cancelarReserva()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $idReserva = $_GET['id'] ?? null;
        $idUsuario = $_SESSION['usuario']['id'];
 
        if (!$idReserva) { header("Location: " . url('cliente/reservas')); exit(); }
 
        $check = $conn->prepare("
            SELECT r.idReserva, er.nombre AS estado
            FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            WHERE r.idReserva = ? AND r.idUsuario_FK = ?
        ");
        $check->execute([$idReserva, $idUsuario]);
        $reserva = $check->fetch(PDO::FETCH_ASSOC);
 
        if (!$reserva || in_array($reserva['estado'], ['Cancelada','Completada'])) {
            $_SESSION['error'] = "Esta reserva no se puede cancelar.";
            header("Location: " . url('cliente/reservas'));
            exit();
        }
 
        $estadoId = $conn->query("SELECT idEstado FROM estado_reserva WHERE nombre = 'Cancelada' LIMIT 1")->fetchColumn();
        $conn->prepare("UPDATE reserva SET idEstadoReserva_FK = ? WHERE idReserva = ?")->execute([$estadoId, $idReserva]);
 
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Cliente canceló reserva ID $idReserva", $idUsuario]);
 
        $_SESSION['success'] = "Reserva cancelada correctamente.";
        header("Location: " . url('cliente/reservas'));
        exit();
    }
 
    // ─────────────────────────────────────────────
    // GET /cliente/pagar  →  formulario de pago del cliente
    // ─────────────────────────────────────────────
    public function pagar()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $idReserva = $_GET['id'] ?? null;
        $idUsuario = $_SESSION['usuario']['id'];
 
        if (!$idReserva) {
            header("Location: " . url('cliente/reservas'));
            exit();
        }
 
        // Verificar que la reserva pertenece al cliente y está confirmada
        $stmt = $conn->prepare("
            SELECT
                r.*,
                er.nombre AS estado_reserva,
                h.numero  AS habitacion_numero, h.piso,
                t.nombre  AS tipo_habitacion,
                COALESCE(SUM(p.monto), 0) AS monto_pagado
            FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            JOIN habitacion h      ON r.idHabitacion_FK    = h.idHabitacion
            JOIN tipo_habitacion t ON h.idTipoHabitacion_FK= t.idTipoHabitacion
            LEFT JOIN pago p ON p.idReserva_FK = r.idReserva
                AND p.idEstadoPago_FK = (SELECT idEstado FROM estado_pago WHERE nombre = 'Pagado' LIMIT 1)
            WHERE r.idReserva = ? AND r.idUsuario_FK = ?
            GROUP BY r.idReserva
        ");
        $stmt->execute([$idReserva, $idUsuario]);
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$reserva || !in_array($reserva['estado_reserva'], ['Confirmada', 'Pendiente'])) {
            $_SESSION['error'] = "Esta reserva no está disponible para pago.";
            header("Location: " . url('cliente/reservas'));
            exit();
        }
 
        $pendiente = max(0, $reserva['precioTotal'] - $reserva['monto_pagado']);
        if ($pendiente <= 0) {
            $_SESSION['error'] = "Esta reserva ya está completamente pagada.";
            header("Location: " . url('cliente/reservas'));
            exit();
        }
 
        $metodos = $conn->query("SELECT * FROM metodo_pago ORDER BY idMetodoPago")->fetchAll(PDO::FETCH_ASSOC);
 
        ob_start();
        include __DIR__ . '/../../views/clientes/pagar.php';
        $content = ob_get_clean();
 
        $title = "Pagar Reserva | Hotel Real Plaza";
        include __DIR__ . '/../../views/layouts/app_layout.php';
    }
 
    // ─────────────────────────────────────────────
    // POST /cliente/pagar  →  procesar pago del cliente
    // ─────────────────────────────────────────────
    public function procesarPago()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $idReserva = $_POST['idReserva']   ?? null;
        $idMetodo  = $_POST['idMetodoPago'] ?? null;
        $idUsuario = $_SESSION['usuario']['id'];
 
        if (!$idReserva || !$idMetodo) {
            $_SESSION['error'] = "Datos incompletos. Intenta nuevamente.";
            header("Location: " . url('cliente/reservas'));
            exit();
        }
 
        // Verificar que la reserva pertenece al cliente
        $stmt = $conn->prepare("
            SELECT r.*, er.nombre AS estado_reserva,
                   COALESCE(SUM(p.monto), 0) AS monto_pagado
            FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            LEFT JOIN pago p ON p.idReserva_FK = r.idReserva
                AND p.idEstadoPago_FK = (SELECT idEstado FROM estado_pago WHERE nombre = 'Pagado' LIMIT 1)
            WHERE r.idReserva = ? AND r.idUsuario_FK = ?
            GROUP BY r.idReserva
        ");
        $stmt->execute([$idReserva, $idUsuario]);
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$reserva || !in_array($reserva['estado_reserva'], ['Confirmada', 'Pendiente'])) {
            $_SESSION['error'] = "Reserva no válida para pago.";
            header("Location: " . url('cliente/reservas'));
            exit();
        }
 
        $monto = max(0, $reserva['precioTotal'] - $reserva['monto_pagado']);
        if ($monto <= 0) {
            $_SESSION['error'] = "Esta reserva ya está completamente pagada.";
            header("Location: " . url('cliente/reservas'));
            exit();
        }
 
        // Detectar si es QR
        $nombreMetodo = $conn->query("SELECT nombre FROM metodo_pago WHERE idMetodoPago = $idMetodo LIMIT 1")->fetchColumn();
        $esQR = ($nombreMetodo === 'QR');
 
        // Procesar comprobante si es QR
        $rutaComprobante = null;
        if ($esQR) {
            if (empty($_FILES['comprobante']['name'])) {
                $_SESSION['error'] = "Debes subir el comprobante de pago QR.";
                header("Location: " . url('cliente/pagar?id=' . $idReserva));
                exit();
            }
            $file    = $_FILES['comprobante'];
            $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            if (!in_array($ext, $allowed) || $file['size'] > 5 * 1024 * 1024) {
                $_SESSION['error'] = "Archivo inválido. Solo JPG, PNG o PDF hasta 5MB.";
                header("Location: " . url('cliente/pagar?id=' . $idReserva));
                exit();
            }
            $uploadDir = __DIR__ . '/../../public/uploads/comprobantes/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $nombreArchivo   = 'comp_' . time() . '_' . $idReserva . '.' . $ext;
            $rutaComprobante = 'uploads/comprobantes/' . $nombreArchivo;
            move_uploaded_file($file['tmp_name'], $uploadDir . $nombreArchivo);
        }
 
        try {
            $estadoNombre = $esQR ? 'Pendiente' : 'Pagado';
            $idEstPagado  = $conn->query("SELECT idEstado FROM estado_pago WHERE nombre = '$estadoNombre' LIMIT 1")->fetchColumn();
 
            $conn->beginTransaction();
 
            // Registrar pago
            $conn->prepare("
                INSERT INTO pago (monto, idEstadoPago_FK, idReserva_FK, idMetodoPago_FK, comprobante)
                VALUES (?, ?, ?, ?, ?)
            ")->execute([$monto, $idEstPagado, $idReserva, $idMetodo, $rutaComprobante]);
 
            $idPago = $conn->lastInsertId();
 
            // Generar recibo
            $numRecibo = 'REC-' . strtoupper(substr(uniqid(), -6));
            $conn->prepare("
                INSERT INTO recibo (numero, total, idPago_FK) VALUES (?, ?, ?)
            ")->execute([$numRecibo, $monto, $idPago]);
 
            // Marcar reserva como Completada
            $idEstCompletada = $conn->query("SELECT idEstado FROM estado_reserva WHERE nombre = 'Completada' LIMIT 1")->fetchColumn();
            if ($idEstCompletada) {
                $conn->prepare("UPDATE reserva SET idEstadoReserva_FK = ? WHERE idReserva = ?")
                     ->execute([$idEstCompletada, $idReserva]);
            }
 
            // Bitácora
            $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                 ->execute(["Cliente realizó pago ID $idPago de Bs. $monto por reserva ID $idReserva. Recibo: $numRecibo", $idUsuario]);
 
            $conn->commit();
 
            // Guardar datos del recibo en sesión para mostrarlo
            $_SESSION['recibo'] = [
                'numero'    => $numRecibo,
                'monto'     => $monto,
                'idReserva' => $idReserva,
                'fecha'     => date('d/m/Y H:i'),
                'esQR'      => $esQR,
            ];
 
            header("Location: " . url('cliente/pago/confirmacion'));
            exit();
 
        } catch (PDOException $e) {
            $conn->rollBack();
            error_log($e->getMessage());
            $_SESSION['error'] = "Error al procesar el pago. Intenta nuevamente.";
            header("Location: " . url('cliente/pagar?id=' . $idReserva));
            exit();
        }
    }
 
    // ─────────────────────────────────────────────
    // GET /cliente/pago/confirmacion  →  pantalla de éxito
    // ─────────────────────────────────────────────
    public function confirmacionPago()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
 
        if (empty($_SESSION['recibo'])) {
            header("Location: " . url('cliente/reservas'));
            exit();
        }
 
        $recibo = $_SESSION['recibo'];
        unset($_SESSION['recibo']); // limpiar para que no se pueda recargar
 
        ob_start();
        include __DIR__ . '/../../views/clientes/confirmacion_pago.php';
        $content = ob_get_clean();
 
        $title = "Pago Confirmado | Hotel Real Plaza";
        include __DIR__ . '/../../views/layouts/app_layout.php';
    }
 

    // POST /cliente/reservas/servicio  →  solicitar servicio adicional
    public function pedirServicio()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $idReserva  = $_POST['idReserva']  ?? null;
        $idServicio = $_POST['idServicio'] ?? null;
        $cantidad   = (int)($_POST['cantidad'] ?? 1);
        $idUsuario  = $_SESSION['usuario']['id'];

        if (!$idReserva || !$idServicio || $cantidad < 1) {
            $_SESSION['error'] = 'Datos incompletos.';
            header('Location: ' . url('cliente/reservas/detalle?id=' . $idReserva));
            exit();
        }

        // Verificar que la reserva pertenece al cliente y está activa
        $stmt = $conn->prepare("
            SELECT r.idReserva, er.nombre AS estado
            FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            WHERE r.idReserva = ? AND r.idUsuario_FK = ?
            AND er.nombre IN ('Pendiente', 'Confirmada')
            LIMIT 1
        ");
        $stmt->execute([$idReserva, $idUsuario]);
        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) {
            $_SESSION['error'] = 'No puedes agregar servicios a esta reserva.';
            header('Location: ' . url('cliente/reservas'));
            exit();
        }

        // Precio del servicio
        $precioStmt = $conn->prepare("SELECT precio, nombre FROM servicio WHERE idServicio = ? LIMIT 1");
        $precioStmt->execute([$idServicio]);
        $servicio = $precioStmt->fetch(PDO::FETCH_ASSOC);

        if (!$servicio) {
            $_SESSION['error'] = 'Servicio no encontrado.';
            header('Location: ' . url('cliente/reservas/detalle?id=' . $idReserva));
            exit();
        }

        // Insertar o actualizar cantidad
        $existe = $conn->prepare("SELECT cantidad FROM reserva_servicio WHERE idReserva = ? AND idServicio = ?");
        $existe->execute([$idReserva, $idServicio]);
        $existente = $existe->fetch(PDO::FETCH_ASSOC);

        if ($existente) {
            $conn->prepare("UPDATE reserva_servicio SET cantidad = cantidad + ? WHERE idReserva = ? AND idServicio = ?")
                 ->execute([$cantidad, $idReserva, $idServicio]);
        } else {
            $conn->prepare("INSERT INTO reserva_servicio (idReserva, idServicio, cantidad, precioUnitario) VALUES (?, ?, ?, ?)")
                 ->execute([$idReserva, $idServicio, $cantidad, $servicio['precio']]);
        }

        // Actualizar precio total de la reserva
        $conn->prepare("
            UPDATE reserva SET precioTotal = (
                SELECT t.precioBase * DATEDIFF(r2.fechaFin, r2.fechaInicio)
                FROM reserva r2
                JOIN habitacion h ON r2.idHabitacion_FK = h.idHabitacion
                JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
                WHERE r2.idReserva = ?
            ) + (
                SELECT COALESCE(SUM(rs.cantidad * rs.precioUnitario), 0)
                FROM reserva_servicio rs WHERE rs.idReserva = ?
            )
            WHERE idReserva = ?
        ")->execute([$idReserva, $idReserva, $idReserva]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Cliente solicitó servicio '{$servicio['nombre']}' x{$cantidad} en reserva #$idReserva", $idUsuario]);

        $_SESSION['success'] = "✅ Servicio '{$servicio['nombre']}' agregado correctamente a tu reserva.";
        header('Location: ' . url('cliente/reservas/detalle?id=' . $idReserva));
        exit();
    }

    public function perfil()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $idUsuario = $_SESSION['usuario']['id'];
        $stmt = $conn->prepare("SELECT * FROM usuario WHERE idUsuario = ?");
        $stmt->execute([$idUsuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
 
        ob_start();
        include __DIR__ . '/../../views/clientes/perfil.php';
        $content = ob_get_clean();
 
        $title = "Mi Perfil | Hotel Real Plaza";
        include __DIR__ . '/../../views/layouts/app_layout.php';
    }
 
    public function actualizarPerfil()
    {
        require_cliente();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $idUsuario = $_SESSION['usuario']['id'];
        $nombre    = trim($_POST['nombre']   ?? '');
        $paterno   = trim($_POST['paterno']  ?? '');
        $materno   = trim($_POST['materno']  ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $password  = $_POST['password']      ?? '';
 
        if (empty($nombre) || empty($paterno)) {
            $_SESSION['error'] = "Nombre y apellido son obligatorios.";
            header("Location: " . url('cliente/perfil'));
            exit();
        }
 
        $passwordActual = $_POST['password_actual'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validar contraseña si se quiere cambiar
        if (!empty($password)) {
            if (strlen($password) < 6) {
                $_SESSION['error'] = "La nueva contraseña debe tener al menos 6 caracteres.";
                header("Location: " . url('cliente/perfil')); exit();
            }
            if ($password !== $passwordConfirm) {
                $_SESSION['error'] = "Las contraseñas nuevas no coinciden.";
                header("Location: " . url('cliente/perfil')); exit();
            }
            if (empty($passwordActual)) {
                $_SESSION['error'] = "Debes ingresar tu contraseña actual para cambiarla.";
                header("Location: " . url('cliente/perfil')); exit();
            }
            // Verificar contraseña actual
            $hashActual = $conn->prepare("SELECT password FROM usuario WHERE idUsuario = ? LIMIT 1");
            $hashActual->execute([$idUsuario]);
            $hashActual = $hashActual->fetchColumn();
            if (!password_verify($passwordActual, $hashActual)) {
                $_SESSION['error'] = "La contraseña actual es incorrecta.";
                header("Location: " . url('cliente/perfil')); exit();
            }
        }

        try {
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $conn->prepare("UPDATE usuario SET nombre=?, paterno=?, materno=?, telefono=?, password=? WHERE idUsuario=?")
                     ->execute([$nombre, $paterno, $materno, $telefono, $hash, $idUsuario]);
            } else {
                $conn->prepare("UPDATE usuario SET nombre=?, paterno=?, materno=?, telefono=? WHERE idUsuario=?")
                     ->execute([$nombre, $paterno, $materno, $telefono, $idUsuario]);
            }
 
            $_SESSION['usuario']['nombre'] = $nombre . ' ' . $paterno;
 
            $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                 ->execute(["Actualizó su perfil", $idUsuario]);
 
            $_SESSION['success'] = "Perfil actualizado correctamente.";
            header("Location: " . url('cliente/perfil'));
            exit();
 
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error al actualizar.";
            header("Location: " . url('cliente/perfil'));
            exit();
        }
    }
}