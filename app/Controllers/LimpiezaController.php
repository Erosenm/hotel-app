<?php
require_once __DIR__ . '/../helpers/auth.php';

class LimpiezaController
{
    // ─── Listado de tareas ────────────────────────────────────────────────────
    public function index()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $tareas = $conn->query("
            SELECT tl.*,
                   h.numero AS habitacion_numero, h.piso,
                   t.nombre AS tipo_habitacion,
                   u.nombre AS asignado_nombre, u.paterno AS asignado_paterno
            FROM tarea_limpieza tl
            JOIN habitacion h         ON tl.idHabitacion_FK = h.idHabitacion
            JOIN tipo_habitacion t    ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            LEFT JOIN usuario u       ON tl.idUsuario_FK = u.idUsuario
            ORDER BY
                FIELD(tl.estado, 'Pendiente', 'En proceso', 'Completada'),
                tl.fechaAsignacion DESC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Habitaciones que necesitan limpieza (Limpieza u Ocupada)
        $habitacionesSucias = $conn->query("
            SELECT h.*, t.nombre AS tipo, eh.nombre AS estado_hab
            FROM habitacion h
            JOIN tipo_habitacion t    ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            JOIN estado_habitacion eh ON h.idEstadoHabitacion_FK = eh.idEstado
            WHERE eh.nombre IN ('Limpieza', 'Ocupada', 'Disponible', 'Reservada')
            ORDER BY FIELD(eh.nombre,'Limpieza','Ocupada','Reservada','Disponible'), h.numero ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Personal de limpieza
        $personal = $conn->query("
            SELECT u.idUsuario, u.nombre, u.paterno
            FROM usuario u
            JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            JOIN rol r          ON ur.idRol    = r.idRol
            WHERE r.nombre = 'Limpieza' AND u.estado = 'Activo'
            ORDER BY u.nombre ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $stats['pendientes']  = count(array_filter($tareas, fn($t) => $t['estado'] === 'Pendiente'));
        $stats['en_proceso']  = count(array_filter($tareas, fn($t) => $t['estado'] === 'En proceso'));
        $stats['completadas'] = count(array_filter($tareas, fn($t) => $t['estado'] === 'Completada'));
        $stats['sucias']      = count($habitacionesSucias);

        ob_start();
        include __DIR__ . '/../../views/admin/limpieza/index.php';
        $content = ob_get_clean();
        $title = "Limpieza | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    // ─── Crear tarea ──────────────────────────────────────────────────────────
    public function crear()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $idHabitacion = $_POST['idHabitacion'] ?? null;
        $idUsuario    = $_POST['idUsuario']    ?? null;
        $observaciones = trim($_POST['observaciones'] ?? '');

        if (!$idHabitacion) {
            $_SESSION['error'] = 'Selecciona una habitación.';
            header('Location: ' . url('admin/limpieza')); exit();
        }

        $conn->prepare("
            INSERT INTO tarea_limpieza (idHabitacion_FK, idUsuario_FK, observaciones, estado)
            VALUES (?, ?, ?, 'Pendiente')
        ")->execute([$idHabitacion, $idUsuario ?: null, $observaciones]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Creó tarea de limpieza para habitación ID $idHabitacion", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Tarea de limpieza creada correctamente.";
        header('Location: ' . url('admin/limpieza')); exit();
    }

    // ─── Cambiar estado ───────────────────────────────────────────────────────
    public function estado()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id     = $_GET['id']     ?? null;
        $estado = $_GET['estado'] ?? null;
        $permitidos = ['Pendiente', 'En proceso', 'Completada'];

        if (!$id || !in_array($estado, $permitidos)) {
            header('Location: ' . url('admin/limpieza')); exit();
        }

        $conn->prepare("UPDATE tarea_limpieza SET estado = ? WHERE idTarea = ?")->execute([$estado, $id]);

        // Si se completa, marcar habitación como Disponible
        if ($estado === 'Completada') {
            $conn->prepare("UPDATE tarea_limpieza SET fechaCompletada = NOW() WHERE idTarea = ?")->execute([$id]);

            $habStmt = $conn->prepare("SELECT idHabitacion_FK FROM tarea_limpieza WHERE idTarea = ?");
            $habStmt->execute([$id]);
            $idHab = $habStmt->fetchColumn();

            $idDisponible = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Disponible' LIMIT 1")->fetchColumn();
            $conn->prepare("UPDATE habitacion SET idEstadoHabitacion_FK = ? WHERE idHabitacion = ?")
                 ->execute([$idDisponible, $idHab]);
        }

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Cambió estado de tarea limpieza ID $id a $estado", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Tarea actualizada a: $estado";
        header('Location: ' . url('admin/limpieza')); exit();
    }
}