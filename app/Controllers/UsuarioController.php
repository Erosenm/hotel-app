<?php
require_once __DIR__ . '/../helpers/auth.php';

class UsuarioController
{
    // ── LISTAR ────────────────────────────────────────────
    public function index()
    {
        require_admin();
        require __DIR__ . '/../../config/database.php';

        $stmt = $conn->query("
            SELECT u.*, r.nombre AS rol
            FROM usuario u
            LEFT JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            LEFT JOIN rol r ON ur.idRol = r.idRol
            ORDER BY u.fechaRegistro DESC
        ");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $roles    = $conn->query("SELECT * FROM rol")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/usuarios/index.php';
        $content = ob_get_clean();

        $title = "Usuarios | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    // ── FORMULARIO CREAR ──────────────────────────────────
    public function create()
    {
        require_admin();
        require __DIR__ . '/../../config/database.php';

        $roles = $conn->query("SELECT * FROM rol")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/usuarios/crear.php';
        $content = ob_get_clean();

        $title = "Crear Usuario | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    // ── GUARDAR NUEVO ─────────────────────────────────────
public function store()
{
    require_admin();
    if (session_status() === PHP_SESSION_NONE) session_start();
    require __DIR__ . '/../../config/database.php';

    $nombre           = trim($_POST['nombre']           ?? '');
    $paterno          = trim($_POST['paterno']          ?? '');
    $materno          = trim($_POST['materno']          ?? '');
    $ci               = trim($_POST['ci']               ?? '');
    $email            = trim($_POST['email']            ?? '');
    $telefono         = trim($_POST['telefono']         ?? '');
    $password         = $_POST['password']              ?? '';
    $rol              = $_POST['rol']                   ?? '';
    $cargo            = trim($_POST['cargo']            ?? '');
    $fechaContratacion= trim($_POST['fechaContratacion']?? '');
    $salario          = trim($_POST['salario']          ?? '');

    if (empty($nombre) || empty($paterno) || empty($ci) || empty($email) || empty($password) || empty($rol)) {
        $_SESSION['error'] = "Todos los campos obligatorios deben completarse";
        header("Location: " . url('admin/usuarios/crear'));
        exit();
    }

    try {
        $check = $conn->prepare("SELECT idUsuario FROM usuario WHERE email = ? OR ci = ?");
        $check->execute([$email, $ci]);
        if ($check->fetch()) {
            $_SESSION['error'] = "Ya existe un usuario con ese email o CI";
            header("Location: " . url('admin/usuarios/crear'));
            exit();
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $conn->prepare("
            INSERT INTO usuario (codigo, ci, nombre, paterno, materno, email, telefono, password)
            VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?)
        ")->execute([$ci, $nombre, $paterno, $materno, $email, $telefono, $hash]);

        $idUsuario = $conn->lastInsertId();

        // Asignar rol
        $conn->prepare("INSERT INTO usuario_rol (idUsuario, idRol) VALUES (?, ?)")
             ->execute([$idUsuario, $rol]);

        // Si es Administrador o Recepcionista → crear registro en empleado
        $stmtRol = $conn->prepare("SELECT nombre FROM rol WHERE idRol = ?");
        $stmtRol->execute([$rol]);
        $nombreRol = $stmtRol->fetchColumn();

        if (in_array($nombreRol, ['Administrador', 'Recepcionista', 'Limpieza', 'Mantenimiento', 'Gerente', 'Contador'])) {
            $conn->prepare("
                INSERT INTO empleado (codigo, cargo, fechaContratacion, salario, idUsuario_FK)
                VALUES (UUID(), ?, ?, ?, ?)
            ")->execute([
                $cargo ?: $nombreRol,
                $fechaContratacion ?: null,
                $salario           ?: null,
                $idUsuario
            ]);
        }

        $idAdmin = $_SESSION['usuario']['id'];
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Creó usuario ID $idUsuario ($email) con rol $nombreRol", $idAdmin]);

        $_SESSION['success'] = "Usuario creado correctamente";
        header("Location: " . url('admin/usuarios'));
        exit();

    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: " . url('admin/usuarios/crear'));
        exit();
    }
}

    // ── FORMULARIO EDITAR ─────────────────────────────────
    public function edit()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: " . url('admin/usuarios'));
            exit();
        }

        $stmt = $conn->prepare("
            SELECT u.*, ur.idRol
            FROM usuario u
            LEFT JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            WHERE u.idUsuario = ?
        ");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            header("Location: " . url('admin/usuarios'));
            exit();
        }

        $roles = $conn->query("SELECT * FROM rol")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/usuarios/editar.php';
        $content = ob_get_clean();

        $title = "Editar Usuario | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    // ── ACTUALIZAR ────────────────────────────────────────
    public function update()
{
    require_admin();
    if (session_status() === PHP_SESSION_NONE) session_start();
    require __DIR__ . '/../../config/database.php';

    $id       = $_POST['id']       ?? null;
    $nombre   = trim($_POST['nombre']   ?? '');
    $paterno  = trim($_POST['paterno']  ?? '');
    $materno  = trim($_POST['materno']  ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $estado   = $_POST['estado']   ?? 'Activo';
    $rol      = $_POST['rol']      ?? '';
    $password = $_POST['password'] ?? '';

    if (!$id || empty($nombre) || empty($paterno) || empty($rol)) {
        $_SESSION['error'] = "Campos obligatorios faltantes";
        header("Location: " . url('admin/usuarios/editar?id=' . $id));
        exit();
    }

    try {
        // Si mandó nueva contraseña la actualizamos, si no la dejamos igual
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $conn->prepare("
                UPDATE usuario
                SET nombre = ?, paterno = ?, materno = ?, telefono = ?, estado = ?, password = ?
                WHERE idUsuario = ?
            ")->execute([$nombre, $paterno, $materno, $telefono, $estado, $hash, $id]);
        } else {
            $conn->prepare("
                UPDATE usuario
                SET nombre = ?, paterno = ?, materno = ?, telefono = ?, estado = ?
                WHERE idUsuario = ?
            ")->execute([$nombre, $paterno, $materno, $telefono, $estado, $id]);
        }

        $conn->prepare("DELETE FROM usuario_rol WHERE idUsuario = ?")->execute([$id]);
        $conn->prepare("INSERT INTO usuario_rol (idUsuario, idRol) VALUES (?, ?)")->execute([$id, $rol]);

        $idAdmin = $_SESSION['usuario']['id'];
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Editó usuario ID $id", $idAdmin]);

        $_SESSION['success'] = "Usuario actualizado correctamente";
        header("Location: " . url('admin/usuarios'));
        exit();

    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: " . url('admin/usuarios/editar?id=' . $id));
        exit();
    }
}

    // ── CAMBIAR ESTADO ────────────────────────────────────
    public function cambiarEstado()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id     = $_GET['id']     ?? null;
        $estado = $_GET['estado'] ?? null;

        $permitidos = ['Activo', 'Suspendido', 'Inactivo'];
        if (!$id || !in_array($estado, $permitidos)) {
            header("Location: " . url('admin/usuarios'));
            exit();
        }

        $conn->prepare("UPDATE usuario SET estado = ? WHERE idUsuario = ?")
             ->execute([$estado, $id]);

        $idAdmin = $_SESSION['usuario']['id'];
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Cambió estado de usuario ID $id a $estado", $idAdmin]);

        $_SESSION['success'] = "Estado actualizado a $estado";
        header("Location: " . url('admin/usuarios'));
        exit();
    }
}