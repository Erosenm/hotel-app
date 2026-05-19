<?php
require_once __DIR__ . '/../helpers/auth.php';

class HabitacionController
{
    public function index()
    {
        require_recepcionista();
        require __DIR__ . '/../../config/database.php';

        $stmt = $conn->query("
            SELECT h.*, 
                   t.nombre AS tipo, 
                   t.precioBase AS precio,
                   e.nombre AS estado,
                   (SELECT rutaImagen FROM habitacion_imagen WHERE idHabitacion_FK = h.idHabitacion LIMIT 1) AS imagen_principal
            FROM habitacion h
            LEFT JOIN tipo_habitacion t    ON h.idTipoHabitacion_FK    = t.idTipoHabitacion
            LEFT JOIN estado_habitacion e  ON h.idEstadoHabitacion_FK  = e.idEstado
            ORDER BY h.piso, h.numero
        ");
        $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tipos   = $conn->query("SELECT * FROM tipo_habitacion")->fetchAll(PDO::FETCH_ASSOC);
        $estados = $conn->query("SELECT * FROM estado_habitacion")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/habitaciones/index.php';
        $content = ob_get_clean();

        $title = "Habitaciones | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    public function create()
    {
        require_admin();
        require __DIR__ . '/../../config/database.php';

        $tipos   = $conn->query("SELECT * FROM tipo_habitacion")->fetchAll(PDO::FETCH_ASSOC);
        $estados = $conn->query("SELECT * FROM estado_habitacion")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/habitaciones/crear.php';
        $content = ob_get_clean();

        $title = "Nueva Habitación | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    public function store()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $numero  = trim($_POST['numero']  ?? '');
        $piso    = trim($_POST['piso']    ?? '');
        $tipo    = $_POST['tipo']         ?? '';
        $estado  = $_POST['estado']       ?? '';

        if (empty($numero) || empty($piso) || empty($tipo) || empty($estado)) {
            $_SESSION['error'] = "Todos los campos son obligatorios";
            header("Location: " . url('admin/habitaciones/crear'));
            exit();
        }

        try {
            $check = $conn->prepare("SELECT idHabitacion FROM habitacion WHERE numero = ?");
            $check->execute([$numero]);
            if ($check->fetch()) {
                $_SESSION['error'] = "Ya existe una habitación con ese número";
                header("Location: " . url('admin/habitaciones/crear'));
                exit();
            }

            $conn->prepare("
                INSERT INTO habitacion (codigo, numero, piso, idTipoHabitacion_FK, idEstadoHabitacion_FK)
                VALUES (UUID(), ?, ?, ?, ?)
            ")->execute([$numero, $piso, $tipo, $estado]);

            $idHabitacion = $conn->lastInsertId();

            // Subir imágenes si se enviaron
            if (!empty($_FILES['imagenes']['name'][0])) {
                $this->subirImagenes($conn, $idHabitacion, $_FILES['imagenes']);
            }

            $idAdmin = $_SESSION['usuario']['id'];
            $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                 ->execute(["Creó habitación número $numero piso $piso", $idAdmin]);

            $_SESSION['success'] = "Habitación creada correctamente";
            header("Location: " . url('admin/habitaciones'));
            exit();

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header("Location: " . url('admin/habitaciones/crear'));
            exit();
        }
    }

    public function edit()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id = $_GET['id'] ?? null;
        if (!$id) { header("Location: " . url('admin/habitaciones')); exit(); }

        $stmt = $conn->prepare("
            SELECT h.*, 
                   t.nombre AS tipo_nombre,
                   e.nombre AS estado_nombre
            FROM habitacion h
            LEFT JOIN tipo_habitacion t   ON h.idTipoHabitacion_FK   = t.idTipoHabitacion
            LEFT JOIN estado_habitacion e ON h.idEstadoHabitacion_FK = e.idEstado
            WHERE h.idHabitacion = ?
        ");
        $stmt->execute([$id]);
        $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$habitacion) { header("Location: " . url('admin/habitaciones')); exit(); }

        // Cargar imágenes existentes
        $stmtImg = $conn->prepare("SELECT * FROM habitacion_imagen WHERE idHabitacion_FK = ?");
        $stmtImg->execute([$id]);
        $imagenes = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

        $tipos   = $conn->query("SELECT * FROM tipo_habitacion")->fetchAll(PDO::FETCH_ASSOC);
        $estados = $conn->query("SELECT * FROM estado_habitacion")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/habitaciones/editar.php';
        $content = ob_get_clean();

        $title = "Editar Habitación | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    public function update()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id     = $_POST['id']     ?? null;
        $numero = trim($_POST['numero'] ?? '');
        $piso   = trim($_POST['piso']   ?? '');
        $tipo   = $_POST['tipo']   ?? '';
        $estado = $_POST['estado'] ?? '';

        if (!$id || empty($numero) || empty($piso) || empty($tipo) || empty($estado)) {
            $_SESSION['error'] = "Todos los campos son obligatorios";
            header("Location: " . url('admin/habitaciones/editar?id=' . $id));
            exit();
        }

        try {
            $conn->prepare("
                UPDATE habitacion
                SET numero = ?, piso = ?, idTipoHabitacion_FK = ?, idEstadoHabitacion_FK = ?
                WHERE idHabitacion = ?
            ")->execute([$numero, $piso, $tipo, $estado, $id]);

            // Subir nuevas imágenes si se enviaron
            if (!empty($_FILES['imagenes']['name'][0])) {
                $this->subirImagenes($conn, $id, $_FILES['imagenes']);
            }

            $idAdmin = $_SESSION['usuario']['id'];
            $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
                 ->execute(["Editó habitación ID $id número $numero", $idAdmin]);

            $_SESSION['success'] = "Habitación actualizada correctamente";
            header("Location: " . url('admin/habitaciones'));
            exit();

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header("Location: " . url('admin/habitaciones/editar?id=' . $id));
            exit();
        }
    }

    public function eliminarImagen()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $idImagen     = $_GET['idImagen']     ?? null;
        $idHabitacion = $_GET['idHabitacion'] ?? null;

        if (!$idImagen || !$idHabitacion) {
            header("Location: " . url('admin/habitaciones'));
            exit();
        }

        $stmt = $conn->prepare("SELECT rutaImagen FROM habitacion_imagen WHERE idImagen = ?");
        $stmt->execute([$idImagen]);
        $img = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($img) {
            $rutaFisica = __DIR__ . '/../../public/uploads/habitaciones/' . basename($img['rutaImagen']);
            if (file_exists($rutaFisica)) {
                unlink($rutaFisica);
            }
            $conn->prepare("DELETE FROM habitacion_imagen WHERE idImagen = ?")->execute([$idImagen]);
        }

        $_SESSION['success'] = "Imagen eliminada correctamente";
        header("Location: " . url('admin/habitaciones/editar?id=' . $idHabitacion));
        exit();
    }

    private function subirImagenes($conn, $idHabitacion, $archivos)
    {
        $dirDestino = __DIR__ . '/../../public/uploads/habitaciones/';
        if (!is_dir($dirDestino)) {
            mkdir($dirDestino, 0755, true);
        }

        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxTamano       = 5 * 1024 * 1024;

        foreach ($archivos['tmp_name'] as $i => $tmpName) {
            if ($archivos['error'][$i] !== UPLOAD_ERR_OK) continue;
            if (!in_array($archivos['type'][$i], $tiposPermitidos))  continue;
            if ($archivos['size'][$i] > $maxTamano)                  continue;

            $ext         = pathinfo($archivos['name'][$i], PATHINFO_EXTENSION);
            $nombreFinal = uniqid('hab_', true) . '.' . strtolower($ext);
            $rutaFisica  = $dirDestino . $nombreFinal;

            if (move_uploaded_file($tmpName, $rutaFisica)) {
                $rutaDB = 'uploads/habitaciones/' . $nombreFinal;
                $conn->prepare("INSERT INTO habitacion_imagen (rutaImagen, idHabitacion_FK) VALUES (?, ?)")
                     ->execute([$rutaDB, $idHabitacion]);
            }
        }
    }

    // ─── Cambiar estado rápido ────────────────────────────────────────────────
    public function cambiarEstado()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id     = $_GET['id']     ?? null;
        $estado = $_GET['estado'] ?? null;
        $permitidos = ['Disponible', 'Mantenimiento', 'Limpieza', 'Ocupada', 'Reservada'];

        if (!$id || !in_array($estado, $permitidos)) {
            header('Location: ' . url('admin/habitaciones')); exit();
        }

        $idEstado = $conn->prepare("SELECT idEstado FROM estado_habitacion WHERE nombre = ? LIMIT 1");
        $idEstado->execute([$estado]);
        $idEstado = $idEstado->fetchColumn();

        if (!$idEstado) {
            $_SESSION['error'] = 'Estado no válido.';
            header('Location: ' . url('admin/habitaciones')); exit();
        }

        $conn->prepare("UPDATE habitacion SET idEstadoHabitacion_FK = ? WHERE idHabitacion = ?")
             ->execute([$idEstado, $id]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Cambió estado de habitación ID $id a $estado", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Estado de habitación actualizado a: $estado";
        header('Location: ' . url('admin/habitaciones')); exit();
    }

}