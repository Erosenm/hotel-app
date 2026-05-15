<?php
require_once __DIR__ . '/../helpers/auth.php';

class ServicioController
{
    public function index()
    {
        require_recepcionista();
        require __DIR__ . '/../../config/database.php';

        $servicios = $conn->query("SELECT * FROM servicio ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
        $total     = $conn->query("SELECT COUNT(*) FROM servicio")->fetchColumn();

        ob_start();
        include __DIR__ . '/../../views/admin/servicios/index.php';
        $content = ob_get_clean();
        $title = "Servicios | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    public function create()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();

        ob_start();
        include __DIR__ . '/../../views/admin/servicios/form.php';
        $content = ob_get_clean();
        $title = "Nuevo Servicio | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    public function store()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $nombre = trim($_POST['nombre'] ?? '');
        $precio = $_POST['precio']     ?? null;

        if (empty($nombre) || !$precio) {
            $_SESSION['error'] = 'Completa todos los campos.';
            header('Location: ' . url('admin/servicios/crear')); exit();
        }

        $conn->prepare("INSERT INTO servicio (nombre, precio) VALUES (?, ?)")->execute([$nombre, $precio]);
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Creó servicio: $nombre", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Servicio '$nombre' creado correctamente.";
        header('Location: ' . url('admin/servicios')); exit();
    }

    public function edit()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . url('admin/servicios')); exit(); }

        $stmt = $conn->prepare("SELECT * FROM servicio WHERE idServicio = ? LIMIT 1");
        $stmt->execute([$id]);
        $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$servicio) { header('Location: ' . url('admin/servicios')); exit(); }

        ob_start();
        include __DIR__ . '/../../views/admin/servicios/form.php';
        $content = ob_get_clean();
        $title = "Editar Servicio | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    public function update()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id     = $_POST['id']     ?? null;
        $nombre = trim($_POST['nombre'] ?? '');
        $precio = $_POST['precio'] ?? null;

        if (!$id || empty($nombre) || !$precio) {
            $_SESSION['error'] = 'Datos incompletos.';
            header('Location: ' . url('admin/servicios')); exit();
        }

        $conn->prepare("UPDATE servicio SET nombre=?, precio=? WHERE idServicio=?")->execute([$nombre, $precio, $id]);
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Actualizó servicio ID $id: $nombre", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Servicio actualizado correctamente.";
        header('Location: ' . url('admin/servicios')); exit();
    }

    public function delete()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . url('admin/servicios')); exit(); }

        $conn->prepare("DELETE FROM servicio WHERE idServicio = ?")->execute([$id]);
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Eliminó servicio ID $id", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Servicio eliminado.";
        header('Location: ' . url('admin/servicios')); exit();
    }
}