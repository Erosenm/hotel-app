<?php
require_once __DIR__ . '/../helpers/auth.php';

class PerfilController
{
    public function index()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id   = $_SESSION['usuario']['id'];
        $stmt = $conn->prepare("
            SELECT u.*, r.nombre AS rol
            FROM usuario u
            LEFT JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            LEFT JOIN rol r          ON ur.idRol    = r.idRol
            WHERE u.idUsuario = ? LIMIT 1
        ");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/perfil/index.php';
        $content = ob_get_clean();
        $title = "Mi Perfil | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    public function actualizar()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id       = $_SESSION['usuario']['id'];
        $nombre   = trim($_POST['nombre']   ?? '');
        $paterno  = trim($_POST['paterno']  ?? '');
        $materno  = trim($_POST['materno']  ?? '');
        $telefono = trim($_POST['telefono'] ?? '');

        if (empty($nombre) || empty($paterno)) {
            $_SESSION['error'] = 'Nombre y apellido son obligatorios.';
            header('Location: ' . url('admin/perfil')); exit();
        }

        $conn->prepare("UPDATE usuario SET nombre=?, paterno=?, materno=?, telefono=? WHERE idUsuario=?")
             ->execute([$nombre, $paterno, $materno, $telefono, $id]);

        $_SESSION['usuario']['nombre'] = $nombre . ' ' . $paterno;
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Actualizó su perfil", $id]);

        $_SESSION['success'] = "Perfil actualizado correctamente.";
        header('Location: ' . url('admin/perfil')); exit();
    }

    public function cambiarPassword()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id          = $_SESSION['usuario']['id'];
        $actual      = $_POST['password_actual']   ?? '';
        $nueva       = $_POST['password_nueva']    ?? '';
        $confirmar   = $_POST['password_confirmar'] ?? '';

        if (empty($actual) || empty($nueva) || empty($confirmar)) {
            $_SESSION['error'] = 'Completa todos los campos de contraseña.';
            header('Location: ' . url('admin/perfil')); exit();
        }

        if ($nueva !== $confirmar) {
            $_SESSION['error'] = 'Las contraseñas nuevas no coinciden.';
            header('Location: ' . url('admin/perfil')); exit();
        }

        if (strlen($nueva) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
            header('Location: ' . url('admin/perfil')); exit();
        }

        $stmt = $conn->prepare("SELECT password FROM usuario WHERE idUsuario = ? LIMIT 1");
        $stmt->execute([$id]);
        $hashActual = $stmt->fetchColumn();

        if (!password_verify($actual, $hashActual)) {
            $_SESSION['error'] = 'La contraseña actual es incorrecta.';
            header('Location: ' . url('admin/perfil')); exit();
        }

        $conn->prepare("UPDATE usuario SET password = ? WHERE idUsuario = ?")
             ->execute([password_hash($nueva, PASSWORD_DEFAULT), $id]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Cambió su contraseña", $id]);

        $_SESSION['success'] = "Contraseña cambiada correctamente.";
        header('Location: ' . url('admin/perfil')); exit();
    }
}