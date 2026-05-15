<?php
require_once __DIR__ . '/../helpers/auth.php';

class CategoriaController
{
    public function index()
    {
        require_admin();
        require __DIR__ . '/../../config/database.php';

        $categorias = $conn->query("
            SELECT c.*, COUNT(p.idProducto) AS total_productos
            FROM categoria_producto c
            LEFT JOIN producto p ON p.idCategoria_FK = c.idCategoria
            GROUP BY c.idCategoria
            ORDER BY c.nombre ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/categorias/index.php';
        $content = ob_get_clean();
        $title = "Categorías | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    public function store()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $nombre      = trim($_POST['nombre']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre es obligatorio.';
            header('Location: ' . url('admin/categorias')); exit();
        }

        $conn->prepare("INSERT INTO categoria_producto (nombre, descripcion) VALUES (?, ?)")
             ->execute([$nombre, $descripcion]);
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Creó categoría: $nombre", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Categoría '$nombre' creada.";
        header('Location: ' . url('admin/categorias')); exit();
    }

    public function update()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id          = $_POST['id']          ?? null;
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (!$id || empty($nombre)) {
            $_SESSION['error'] = 'Datos incompletos.';
            header('Location: ' . url('admin/categorias')); exit();
        }

        $conn->prepare("UPDATE categoria_producto SET nombre=?, descripcion=? WHERE idCategoria=?")
             ->execute([$nombre, $descripcion, $id]);
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Actualizó categoría ID $id: $nombre", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Categoría actualizada.";
        header('Location: ' . url('admin/categorias')); exit();
    }

    public function delete()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . url('admin/categorias')); exit(); }

        $tiene = $conn->prepare("SELECT COUNT(*) FROM producto WHERE idCategoria_FK = ?");
        $tiene->execute([$id]);
        if ($tiene->fetchColumn() > 0) {
            $_SESSION['error'] = 'No se puede eliminar: tiene productos asociados.';
            header('Location: ' . url('admin/categorias')); exit();
        }

        $conn->prepare("DELETE FROM categoria_producto WHERE idCategoria = ?")->execute([$id]);
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Eliminó categoría ID $id", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Categoría eliminada.";
        header('Location: ' . url('admin/categorias')); exit();
    }
}