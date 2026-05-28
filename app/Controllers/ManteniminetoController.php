<?php
require_once __DIR__ . '/../helpers/auth.php';

class MantenimientoController
{
    // ─── Listado de incidencias ───────────────────────────────────────────────
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require_once __DIR__ . '/../helpers/auth.php';

        $roles = ['Administrador', 'Recepcionista', 'Mantenimiento'];
        if (empty($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], $roles)) {
            header('Location: ' . url('login')); exit();
        }

        require __DIR__ . '/../../config/database.php';

        $rolActivo = $_SESSION['usuario']['rol'];
        $idUsuario = $_SESSION['usuario']['id'];

        // Si es Mantenimiento, solo ve sus asignadas + las pendientes sin asignar
        if ($rolActivo === 'Mantenimiento') {
            $stmt = $conn->prepare("
                SELECT i.*,
                    h.numero AS habitacion_numero, h.piso,
                    u.nombre AS reportado_nombre, u.paterno AS reportado_paterno,
                    a.nombre AS asignado_nombre, a.paterno AS asignado_paterno
                FROM incidencia i
                LEFT JOIN habitacion h ON i.idHabitacion_FK = h.idHabitacion
                LEFT JOIN usuario u    ON i.idUsuario_FK    = u.idUsuario
                LEFT JOIN usuario a    ON i.idAsignado_FK   = a.idUsuario
                WHERE i.idAsignado_FK = ? OR i.idAsignado_FK IS NULL
                ORDER BY FIELD(i.prioridad,'Urgente','Alta','Media','Baja'), i.fechaCreacion DESC
            ");
            $stmt->execute([$idUsuario]);
        } else {
            $stmt = $conn->query("
                SELECT i.*,
                    h.numero AS habitacion_numero, h.piso,
                    u.nombre AS reportado_nombre, u.paterno AS reportado_paterno,
                    a.nombre AS asignado_nombre, a.paterno AS asignado_paterno
                FROM incidencia i
                LEFT JOIN habitacion h ON i.idHabitacion_FK = h.idHabitacion
                LEFT JOIN usuario u    ON i.idUsuario_FK    = u.idUsuario
                LEFT JOIN usuario a    ON i.idAsignado_FK   = a.idUsuario
                ORDER BY FIELD(i.prioridad,'Urgente','Alta','Media','Baja'), i.fechaCreacion DESC
            ");
        }
        $incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Habitaciones para el formulario
        $habitaciones = $conn->query("
            SELECT h.idHabitacion, h.numero, h.piso, t.nombre AS tipo
            FROM habitacion h
            JOIN tipo_habitacion t ON h.idTipoHabitacion_FK = t.idTipoHabitacion
            ORDER BY h.numero ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Personal de mantenimiento para asignar
        $personal = $conn->query("
            SELECT u.idUsuario, u.nombre, u.paterno
            FROM usuario u
            JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            JOIN rol r          ON ur.idRol    = r.idRol
            WHERE r.nombre = 'Mantenimiento' AND u.estado = 'Activo'
            ORDER BY u.nombre ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $stats['total']      = count($incidencias);
        $stats['pendientes'] = count(array_filter($incidencias, fn($i) => $i['estado'] === 'Pendiente'));
        $stats['en_proceso'] = count(array_filter($incidencias, fn($i) => $i['estado'] === 'En proceso'));
        $stats['resueltas']  = count(array_filter($incidencias, fn($i) => $i['estado'] === 'Resuelta'));
        $stats['urgentes']   = count(array_filter($incidencias, fn($i) => $i['prioridad'] === 'Urgente' && $i['estado'] !== 'Resuelta'));

        ob_start();
        include __DIR__ . '/../../views/admin/mantenimiento/index.php';
        $content = ob_get_clean();
        $title = "Mantenimiento | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    // ─── Crear incidencia ─────────────────────────────────────────────────────
    public function crear()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $titulo       = trim($_POST['titulo']       ?? '');
        $descripcion  = trim($_POST['descripcion']  ?? '');
        $prioridad    = $_POST['prioridad']         ?? 'Media';
        $idHabitacion = $_POST['idHabitacion']      ?? null;
        $idAsignado   = $_POST['idAsignado']        ?? null;
        $idUsuario    = $_SESSION['usuario']['id'];

        if (empty($titulo)) {
            $_SESSION['error'] = 'El título es obligatorio.';
            header('Location: ' . url('admin/mantenimiento')); exit();
        }

        $conn->prepare("
            INSERT INTO incidencia (titulo, descripcion, prioridad, idHabitacion_FK, idUsuario_FK, idAsignado_FK)
            VALUES (?, ?, ?, ?, ?, ?)
        ")->execute([$titulo, $descripcion, $prioridad, $idHabitacion ?: null, $idUsuario, $idAsignado ?: null]);

        $idIncidencia = $conn->lastInsertId();

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Reportó incidencia #$idIncidencia: $titulo", $idUsuario]);

        // Si hay habitación, ponerla en Mantenimiento
        if ($idHabitacion) {
            $idEstMant = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Mantenimiento' LIMIT 1")->fetchColumn();
            $conn->prepare("UPDATE habitacion SET idEstadoHabitacion_FK = ? WHERE idHabitacion = ?")
                 ->execute([$idEstMant, $idHabitacion]);
        }

        $_SESSION['success'] = "Incidencia reportada correctamente.";
        header('Location: ' . url('admin/mantenimiento')); exit();
    }

    // ─── Cambiar estado ───────────────────────────────────────────────────────
    public function estado()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id     = $_GET['id']     ?? null;
        $estado = $_GET['estado'] ?? null;
        $permitidos = ['Pendiente', 'En proceso', 'Resuelta'];

        if (!$id || !in_array($estado, $permitidos)) {
            header('Location: ' . url('admin/mantenimiento')); exit();
        }

        $fechaResolucion = $estado === 'Resuelta' ? date('Y-m-d H:i:s') : null;
        $conn->prepare("UPDATE incidencia SET estado = ?, fechaResolucion = ? WHERE idIncidencia = ?")
             ->execute([$estado, $fechaResolucion, $id]);

        // Si se resuelve y tiene habitación asociada, liberarla
        if ($estado === 'Resuelta') {
            $habStmt = $conn->prepare("SELECT idHabitacion_FK FROM incidencia WHERE idIncidencia = ?");
            $habStmt->execute([$id]);
            $idHab = $habStmt->fetchColumn();
            if ($idHab) {
                $idDisponible = $conn->query("SELECT idEstado FROM estado_habitacion WHERE nombre = 'Disponible' LIMIT 1")->fetchColumn();
                $conn->prepare("UPDATE habitacion SET idEstadoHabitacion_FK = ? WHERE idHabitacion = ?")
                     ->execute([$idDisponible, $idHab]);
            }
        }

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Cambió incidencia #$id a estado: $estado", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Incidencia actualizada a: $estado";
        header('Location: ' . url('admin/mantenimiento')); exit();
    }

    // ─── Asignar personal ─────────────────────────────────────────────────────
    public function asignar()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id         = $_POST['id']         ?? null;
        $idAsignado = $_POST['idAsignado'] ?? null;

        if (!$id) { header('Location: ' . url('admin/mantenimiento')); exit(); }

        $conn->prepare("UPDATE incidencia SET idAsignado_FK = ?, estado = 'En proceso' WHERE idIncidencia = ?")
             ->execute([$idAsignado ?: null, $id]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Asignó incidencia #$id a usuario ID $idAsignado", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Incidencia asignada correctamente.";
        header('Location: ' . url('admin/mantenimiento')); exit();
    }
}