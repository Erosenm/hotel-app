<?php
require_once __DIR__ . '/../helpers/auth.php';

class ReservaController
{
    // ─── Listado de reservas ──────────────────────────────────────────────────
    public function index()
    {
        require_recepcionista();
        require __DIR__ . '/../../config/database.php';

        $stmt = $conn->query("
            SELECT r.*,
                er.nombre    AS estado_reserva,
                u.nombre     AS cliente_nombre,
                u.paterno    AS cliente_paterno,
                u.email      AS cliente_email,
                h.numero     AS habitacion_numero,
                h.piso       AS habitacion_piso,
                t.nombre     AS tipo_habitacion,
                t.precioBase AS precio_noche,
                DATEDIFF(r.fechaFin, r.fechaInicio) AS noches,
                DATEDIFF(r.fechaFin, r.fechaInicio) * t.precioBase AS total
            FROM reserva r
            LEFT JOIN estado_reserva er  ON r.idEstadoReserva_FK = er.idEstado
            LEFT JOIN usuario u          ON r.idUsuario_FK        = u.idUsuario
            LEFT JOIN habitacion h       ON r.idHabitacion_FK     = h.idHabitacion
            LEFT JOIN tipo_habitacion t  ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            LEFT JOIN empleado e         ON r.idEmpleado_FK       = e.idEmpleado
            ORDER BY r.fechaInicio DESC
        ");
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // ─── Formulario nueva reserva ─────────────────────────────────────────────
    public function create()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $habitaciones = $conn->query("
            SELECT h.*, t.nombre AS tipo, t.precioBase, eh.nombre AS estado
            FROM habitacion h
            JOIN tipo_habitacion t    ON h.idTipoHabitacion_FK  = t.idTipoHabitacion
            JOIN estado_habitacion eh ON h.idEstadoHabitacion_FK = eh.idEstado
            WHERE eh.nombre = 'Disponible'
            ORDER BY h.piso ASC, h.numero ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $clientes = $conn->query("
            SELECT u.idUsuario, u.nombre, u.paterno, u.ci, u.email, u.telefono
            FROM usuario u
            JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            JOIN rol r          ON ur.idRol    = r.idRol
            WHERE r.nombre = 'Cliente' AND u.estado = 'Activo'
            ORDER BY u.paterno ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/reservas/crear.php';
        $content = ob_get_clean();

        $title = "Nueva Reserva | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    // ─── Buscar cliente por CI (AJAX) ─────────────────────────────────────────
    public function buscarCliente()
    {
        require __DIR__ . '/../../config/database.php';
        header('Content-Type: application/json');

        $ci = trim($_GET['ci'] ?? '');
        if (empty($ci)) { echo json_encode(null); exit(); }

        $stmt = $conn->prepare("
            SELECT u.idUsuario, u.nombre, u.paterno, u.materno, u.ci, u.email, u.telefono
            FROM usuario u
            JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            JOIN rol r          ON ur.idRol    = r.idRol
            WHERE u.ci = ? AND r.nombre = 'Cliente' AND u.estado = 'Activo'
            LIMIT 1
        ");
        $stmt->execute([$ci]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC) ?: null);
        exit();
    }

    // ─── Verificar disponibilidad (AJAX) ──────────────────────────────────────
    public function verificarDisponibilidad()
    {
        require __DIR__ . '/../../config/database.php';
        header('Content-Type: application/json');

        $idHabitacion = $_GET['idHabitacion'] ?? null;
        $fechaInicio  = $_GET['fechaInicio']  ?? null;
        $fechaFin     = $_GET['fechaFin']     ?? null;

        if (!$idHabitacion || !$fechaInicio || !$fechaFin) {
            echo json_encode(['disponible' => false]); exit();
        }

        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM reserva r
            JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
            WHERE r.idHabitacion_FK = ?
            AND er.nombre NOT IN ('Cancelada','Completada','No show')
            AND (? BETWEEN r.fechaInicio AND r.fechaFin
              OR ? BETWEEN r.fechaInicio AND r.fechaFin
              OR r.fechaInicio BETWEEN ? AND ?)
        ");
        $stmt->execute([$idHabitacion, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin]);
        $conflictos = $stmt->fetchColumn();

        $hab = $conn->prepare("
            SELECT h.numero, t.nombre AS tipo, t.precioBase
            FROM habitacion h JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            WHERE h.idHabitacion = ?
        ");
        $hab->execute([$idHabitacion]);
        $habitacion = $hab->fetch(PDO::FETCH_ASSOC);

        $noches = max(1, (new DateTime($fechaInicio))->diff(new DateTime($fechaFin))->days);
        $total  = $noches * ($habitacion['precioBase'] ?? 0);

        echo json_encode([
            'disponible' => $conflictos == 0,
            'mensaje'    => $conflictos > 0 ? 'No disponible en esas fechas' : 'Disponible',
            'noches'     => $noches,
            'precio'     => $habitacion['precioBase'] ?? 0,
            'total'      => $total,
        ]);
        exit();
    }

    // ─── Guardar reserva ──────────────────────────────────────────────────────
    public function store()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $idCliente    = $_POST['idCliente']        ?? null;
        $idHabitacion = $_POST['idHabitacion']     ?? null;
        $fechaInicio  = $_POST['fechaInicio']       ?? null;
        $fechaFin     = $_POST['fechaFin']          ?? null;
        $personas     = (int)($_POST['cantidadPersonas'] ?? 1);

        if (!$idCliente || !$idHabitacion || !$fechaInicio || !$fechaFin) {
            $_SESSION['error'] = 'Completa todos los campos obligatorios.';
            header('Location: ' . url('admin/reservas/crear')); exit();
        }

        if ($fechaFin <= $fechaInicio) {
            $_SESSION['error'] = 'La fecha de salida debe ser posterior a la entrada.';
            header('Location: ' . url('admin/reservas/crear')); exit();
        }

        try {
            // Verificar disponibilidad
            $stmt = $conn->prepare("
                SELECT COUNT(*) FROM reserva r
                JOIN estado_reserva er ON r.idEstadoReserva_FK = er.idEstado
                WHERE r.idHabitacion_FK = ?
                AND er.nombre NOT IN ('Cancelada','Completada','No show')
                AND (? BETWEEN r.fechaInicio AND r.fechaFin
                  OR ? BETWEEN r.fechaInicio AND r.fechaFin
                  OR r.fechaInicio BETWEEN ? AND ?)
            ");
            $stmt->execute([$idHabitacion, $fechaInicio, $fechaFin, $fechaInicio, $fechaFin]);
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['error'] = 'La habitación no está disponible en esas fechas.';
                header('Location: ' . url('admin/reservas/crear')); exit();
            }

            // Calcular precio
            $precioBase = $conn->prepare("SELECT t.precioBase FROM habitacion h JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion WHERE h.idHabitacion = ?");
            $precioBase->execute([$idHabitacion]);
            $precio = $precioBase->fetchColumn();
            $noches = (new DateTime($fechaInicio))->diff(new DateTime($fechaFin))->days;
            $total  = $noches * $precio;

            // Empleado del recepcionista logueado
            $idUsuarioActual = $_SESSION['usuario']['id'];
            $empStmt = $conn->prepare("SELECT idEmpleado FROM empleado WHERE idUsuario_FK = ? LIMIT 1");
            $empStmt->execute([$idUsuarioActual]);
            $idEmpleado = $empStmt->fetchColumn() ?: null;

            // Estado Confirmada (reserva presencial)
            $idEstado = $conn->query("SELECT idEstado FROM estado_reserva WHERE nombre = 'Confirmada' LIMIT 1")->fetchColumn();

            $conn->beginTransaction();

            $conn->prepare("
                INSERT INTO reserva (fechaInicio, fechaFin, cantidadPersonas, precioTotal, idEstadoReserva_FK, idUsuario_FK, idHabitacion_FK, idEmpleado_FK)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ")->execute([$fechaInicio, $fechaFin, $personas, $total, $idEstado, $idCliente, $idHabitacion, $idEmpleado]);

            $idReserva = $conn->lastInsertId();

            // Marcar habitación como Reservada
            $idEstHab = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Reservada' LIMIT 1")->fetchColumn();
            $conn->prepare("UPDATE habitacion SET idEstadoHabitacion_FK = ? WHERE idHabitacion = ?")
                 ->execute([$idEstHab, $idHabitacion]);

            $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                 ->execute(["Reserva #$idReserva creada por recepcionista para cliente ID $idCliente", $idUsuarioActual]);

            $conn->commit();

            // Email de confirmación al cliente
            $datosCliente = $conn->prepare("SELECT nombre, paterno, email FROM usuario WHERE idUsuario = ? LIMIT 1");
            $datosCliente->execute([$idCliente]);
            $cli = $datosCliente->fetch(PDO::FETCH_ASSOC);
            $datosHab = $conn->prepare("SELECT h.numero, t.nombre AS tipo FROM habitacion h JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion WHERE h.idHabitacion = ? LIMIT 1");
            $datosHab->execute([$idHabitacion]);
            $hab = $datosHab->fetch(PDO::FETCH_ASSOC);
            require_once __DIR__ . '/../../app/Mail/mailer.php';
            Mailer::enviarConfirmacionReserva(
                $cli['email'],
                $cli['nombre'] . ' ' . $cli['paterno'],
                $hab['numero'],
                $hab['tipo'],
                $fechaInicio,
                $fechaFin,
                $noches,
                $total
            );

            $_SESSION['success'] = "✅ Reserva #$idReserva creada correctamente para el cliente.";
            header('Location: ' . url('admin/reservas'));

        } catch (PDOException $e) {
            $conn->rollBack();
            error_log($e->getMessage());
            $_SESSION['error'] = 'Error al crear la reserva: ' . $e->getMessage();
            header('Location: ' . url('admin/reservas/crear'));
        }
        exit();
    }

    // ─── Cambiar estado ───────────────────────────────────────────────────────
    public function cambiarEstado()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id           = $_GET['id']     ?? null;
        $nombreEstado = $_GET['estado'] ?? null;

        $permitidos = ['Pendiente','Confirmada','Cancelada','Completada','No show'];
        if (!$id || !in_array($nombreEstado, $permitidos)) {
            header("Location: " . url('admin/reservas')); exit();
        }

        try {
            $estStmt = $conn->prepare("SELECT idEstado FROM estado_reserva WHERE nombre = ? LIMIT 1");
            $estStmt->execute([$nombreEstado]);
            $estadoReserva = $estStmt->fetch(PDO::FETCH_ASSOC);

            if (!$estadoReserva) {
                $_SESSION['error'] = "Estado inválido";
                header("Location: " . url('admin/reservas')); exit();
            }

            $conn->prepare("UPDATE reserva SET idEstadoReserva_FK = ? WHERE idReserva = ?")
                 ->execute([$estadoReserva['idEstado'], $id]);

            if ($nombreEstado === 'Confirmada') {
                $estHab = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Ocupada' LIMIT 1")->fetch();
                $conn->prepare("UPDATE habitacion h JOIN reserva r ON r.idHabitacion_FK = h.idHabitacion SET h.idEstadoHabitacion_FK = ? WHERE r.idReserva = ?")
                     ->execute([$estHab['idEstado'], $id]);
            }

            if (in_array($nombreEstado, ['Cancelada','Completada','No show'])) {
                $estHab = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Disponible' LIMIT 1")->fetch();
                $conn->prepare("UPDATE habitacion h JOIN reserva r ON r.idHabitacion_FK = h.idHabitacion SET h.idEstadoHabitacion_FK = ? WHERE r.idReserva = ?")
                     ->execute([$estHab['idEstado'], $id]);
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

    // ─── Check-in ─────────────────────────────────────────────────────────────
    public function checkin()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . url('admin/reservas')); exit(); }

        // Estado Confirmada → Ocupada (habitación)
        $idEstOcupada = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Ocupada' LIMIT 1")->fetchColumn();
        $conn->prepare("UPDATE habitacion h JOIN reserva r ON r.idHabitacion_FK = h.idHabitacion SET h.idEstadoHabitacion_FK = ? WHERE r.idReserva = ?")
             ->execute([$idEstOcupada, $id]);

        // Reserva → Confirmada
        $idEstConf = $conn->query("SELECT idEstado FROM estado_reserva WHERE nombre = 'Confirmada' LIMIT 1")->fetchColumn();
        $conn->prepare("UPDATE reserva SET idEstadoReserva_FK = ? WHERE idReserva = ?")->execute([$idEstConf, $id]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Check-in realizado en reserva ID $id", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "✅ Check-in realizado. Habitación marcada como Ocupada.";
        header('Location: ' . url('admin/reservas')); exit();
    }

    // ─── Check-out ────────────────────────────────────────────────────────────
    public function checkout()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . url('admin/reservas')); exit(); }

        // Habitación → Limpieza
        $idEstLimpieza = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Limpieza' LIMIT 1")->fetchColumn();
        $conn->prepare("UPDATE habitacion h JOIN reserva r ON r.idHabitacion_FK = h.idHabitacion SET h.idEstadoHabitacion_FK = ? WHERE r.idReserva = ?")
             ->execute([$idEstLimpieza, $id]);

        // Reserva → Completada
        $idEstComp = $conn->query("SELECT idEstado FROM estado_reserva WHERE nombre = 'Completada' LIMIT 1")->fetchColumn();
        $conn->prepare("UPDATE reserva SET idEstadoReserva_FK = ? WHERE idReserva = ?")->execute([$idEstComp, $id]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Check-out realizado en reserva ID $id", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "✅ Check-out realizado. Habitación enviada a Limpieza.";
        header('Location: ' . url('admin/reservas')); exit();
    }

    // ─── Agregar servicio a reserva ───────────────────────────────────────────
    public function agregarServicio()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $idReserva  = $_POST['idReserva']  ?? null;
        $idServicio = $_POST['idServicio'] ?? null;
        $cantidad   = (int)($_POST['cantidad'] ?? 1);

        if (!$idReserva || !$idServicio || $cantidad < 1) {
            $_SESSION['error'] = 'Datos incompletos.';
            header('Location: ' . url('admin/reservas')); exit();
        }

        $precioStmt = $conn->prepare("SELECT precio FROM servicio WHERE idServicio = ? LIMIT 1");
        $precioStmt->execute([$idServicio]);
        $precio = $precioStmt->fetchColumn();

        // Insertar o actualizar cantidad
        $existe = $conn->prepare("SELECT cantidad FROM reserva_servicio WHERE idReserva = ? AND idServicio = ?");
        $existe->execute([$idReserva, $idServicio]);
        $existente = $existe->fetch(PDO::FETCH_ASSOC);

        if ($existente) {
            $conn->prepare("UPDATE reserva_servicio SET cantidad = cantidad + ? WHERE idReserva = ? AND idServicio = ?")
                 ->execute([$cantidad, $idReserva, $idServicio]);
        } else {
            $conn->prepare("INSERT INTO reserva_servicio (idReserva, idServicio, cantidad, precioUnitario) VALUES (?, ?, ?, ?)")
                 ->execute([$idReserva, $idServicio, $cantidad, $precio]);
        }

        // Actualizar precio total de reserva
        $conn->prepare("
            UPDATE reserva SET precioTotal = (
                SELECT precioBase * DATEDIFF(r2.fechaFin, r2.fechaInicio)
                FROM reserva r2 JOIN habitacion h ON r2.idHabitacion_FK = h.idHabitacion
                JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
                WHERE r2.idReserva = ?
            ) + (
                SELECT COALESCE(SUM(rs.cantidad * rs.precioUnitario), 0)
                FROM reserva_servicio rs WHERE rs.idReserva = ?
            ) WHERE idReserva = ?
        ")->execute([$idReserva, $idReserva, $idReserva]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Agregó servicio ID $idServicio a reserva ID $idReserva", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Servicio agregado a la reserva correctamente.";
        header('Location: ' . url('admin/reservas')); exit();
    }

}