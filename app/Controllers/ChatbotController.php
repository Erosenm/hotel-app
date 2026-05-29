<?php
class ChatbotController
{
    public function handle()
    {
        require_once __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../Mail/mailer.php';

        header('Content-Type: application/json');

        $headers = getallheaders();
        $token = $headers['X-Chatbot-Token'] ?? $headers['x-chatbot-token'] ?? $_SERVER['HTTP_X_CHATBOT_TOKEN'] ?? '';
        if ($token !== 'hotel_real_plaza_secret') {
            echo json_encode(['ok' => false, 'error' => 'No autorizado']);
            exit();
        }

        $body = json_decode(file_get_contents('php://input'), true);
        error_log('ChatbotController body: ' . json_encode($body));
        $accion = $body['accion'] ?? '';

        if ($accion === 'crear_reserva') {
            $idHabitacion     = (int)($body['idHabitacion'] ?? 0) ?: null;
            $fechaInicio      = $body['fechaInicio']       ?? '';
            $fechaFin         = $body['fechaFin']          ?? '';
            $cantidadPersonas = $body['cantidadPersonas']  ?? 1;
            $idUsuario        = (int)($body['idUsuario'] ?? 0) ?: null;

            if (!$idHabitacion || !$fechaInicio || !$fechaFin || !$idUsuario) {
                echo json_encode(['ok' => false, 'error' => 'Faltan datos']);
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
                    echo json_encode(['ok' => false, 'error' => 'Habitación no disponible en esas fechas']);
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

                try {
                    $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                         ->execute(["Bot creó reserva ID $idReserva", $idUsuario]);
                } catch (PDOException $e) {
                    error_log('Bitacora error: ' . $e->getMessage());
                }

                $datosCliente = $conn->prepare("SELECT nombre, paterno, email FROM usuario WHERE idUsuario = ? LIMIT 1");
                $datosCliente->execute([$idUsuario]);
                $cli = $datosCliente->fetch(PDO::FETCH_ASSOC);

                $datosHab = $conn->prepare("SELECT h.numero, t.nombre AS tipo FROM habitacion h JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion WHERE h.idHabitacion = ? LIMIT 1");
                $datosHab->execute([$idHabitacion]);
                $hab = $datosHab->fetch(PDO::FETCH_ASSOC);

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

                echo json_encode([
                    'ok'          => true,
                    'idReserva'   => $idReserva,
                    'precioTotal' => $precioTotal,
                    'dias'        => $dias
                ]);

            } catch (PDOException $e) {
                echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
            }
            exit();
        }

        if ($accion === 'pedir_servicio') {
            $idReserva      = (int)($body['idReserva'] ?? 0) ?: null;
            $idServicio     = (int)($body['idServicio'] ?? 0) ?: null;
            $cantidad       = (int)($body['cantidad'] ?? 1);

            if (!$idReserva || !$idServicio) {
                echo json_encode(['ok' => false, 'error' => 'Faltan datos']);
                exit();
            }

            try {
                $precio = $conn->prepare("SELECT precio FROM servicio WHERE idServicio = ?");
                $precio->execute([$idServicio]);
                $precioUnitario = $precio->fetchColumn();

                $conn->prepare("
                    INSERT INTO reserva_servicio (idReserva, idServicio, cantidad, precioUnitario)
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE cantidad = cantidad + ?, precioUnitario = ?
                ")->execute([$idReserva, $idServicio, $cantidad, $precioUnitario, $cantidad, $precioUnitario]);

                echo json_encode(['ok' => true, 'mensaje' => 'Servicio solicitado correctamente']);
            } catch (PDOException $e) {
                echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
            }
            exit();
        }

        echo json_encode(['ok' => false, 'error' => 'Accion no reconocida']);
    }
}