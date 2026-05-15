<?php
require_once __DIR__ . '/../helpers/auth.php';

class ProductoController
{
    // ─── Listado ──────────────────────────────────────────────────────────────
    public function index()
    {
        require_recepcionista();
        require __DIR__ . '/../../config/database.php';

        $productos = $conn->query("
            SELECT p.*, c.nombre AS categoria
            FROM producto p
            LEFT JOIN categoria_producto c ON p.idCategoria_FK = c.idCategoria
            ORDER BY p.estado DESC, c.nombre ASC, p.nombre ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $categorias = $conn->query("SELECT * FROM categoria_producto ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

        $stats['total']     = $conn->query("SELECT COUNT(*) FROM producto")->fetchColumn();
        $stats['activos']   = $conn->query("SELECT COUNT(*) FROM producto WHERE estado = 'Activo'")->fetchColumn();
        $stats['bajo_stock']= $conn->query("SELECT COUNT(*) FROM producto WHERE stock <= stockMinimo AND estado = 'Activo'")->fetchColumn();
        $stats['sin_stock'] = $conn->query("SELECT COUNT(*) FROM producto WHERE stock = 0 AND estado = 'Activo'")->fetchColumn();

        ob_start();
        include __DIR__ . '/../../views/admin/productos/index.php';
        $content = ob_get_clean();
        $title = "Productos | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    // ─── Formulario crear ─────────────────────────────────────────────────────
    public function create()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $categorias = $conn->query("SELECT * FROM categoria_producto ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/productos/form.php';
        $content = ob_get_clean();
        $title = "Nuevo Producto | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    // ─── Guardar nuevo ────────────────────────────────────────────────────────
    public function store()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $nombre      = trim($_POST['nombre']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio      = $_POST['precio']           ?? null;
        $stock       = (int)($_POST['stock']      ?? 0);
        $stockMinimo = (int)($_POST['stockMinimo']?? 5);
        $unidad      = trim($_POST['unidad']      ?? 'unidad');
        $idCategoria = $_POST['idCategoria']      ?? null;

        if (empty($nombre) || !$precio || !$idCategoria) {
            $_SESSION['error'] = 'Completa todos los campos obligatorios.';
            header('Location: ' . url('admin/productos/crear')); exit();
        }

        $conn->prepare("
            INSERT INTO producto (nombre, descripcion, precio, stock, stockMinimo, unidad, idCategoria_FK, estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Activo')
        ")->execute([$nombre, $descripcion, $precio, $stock, $stockMinimo, $unidad, $idCategoria]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Creó producto: $nombre", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Producto '$nombre' creado correctamente.";
        header('Location: ' . url('admin/productos')); exit();
    }

    // ─── Formulario editar ────────────────────────────────────────────────────
    public function edit()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . url('admin/productos')); exit(); }

        $stmt = $conn->prepare("SELECT * FROM producto WHERE idProducto = ? LIMIT 1");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) { header('Location: ' . url('admin/productos')); exit(); }

        $categorias = $conn->query("SELECT * FROM categoria_producto ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include __DIR__ . '/../../views/admin/productos/form.php';
        $content = ob_get_clean();
        $title = "Editar Producto | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }

    // ─── Actualizar ───────────────────────────────────────────────────────────
    public function update()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id          = $_POST['id']               ?? null;
        $nombre      = trim($_POST['nombre']      ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio      = $_POST['precio']           ?? null;
        $stock       = (int)($_POST['stock']      ?? 0);
        $stockMinimo = (int)($_POST['stockMinimo']?? 5);
        $unidad      = trim($_POST['unidad']      ?? 'unidad');
        $idCategoria = $_POST['idCategoria']      ?? null;
        $estado      = $_POST['estado']           ?? 'Activo';

        if (!$id || empty($nombre) || !$precio) {
            $_SESSION['error'] = 'Datos incompletos.';
            header('Location: ' . url('admin/productos')); exit();
        }

        $conn->prepare("
            UPDATE producto SET nombre=?, descripcion=?, precio=?, stock=?, stockMinimo=?, unidad=?, idCategoria_FK=?, estado=?
            WHERE idProducto=?
        ")->execute([$nombre, $descripcion, $precio, $stock, $stockMinimo, $unidad, $idCategoria, $estado, $id]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Actualizó producto ID $id: $nombre", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Producto actualizado correctamente.";
        header('Location: ' . url('admin/productos')); exit();
    }

    // ─── Ajustar stock ────────────────────────────────────────────────────────
    public function ajustarStock()
    {
        require_recepcionista();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id       = $_POST['id']       ?? null;
        $cantidad = (int)($_POST['cantidad'] ?? 0);
        $tipo     = $_POST['tipo']     ?? 'entrada'; // entrada o salida

        if (!$id || $cantidad <= 0) {
            $_SESSION['error'] = 'Datos inválidos.';
            header('Location: ' . url('admin/productos')); exit();
        }

        $stmt = $conn->prepare("SELECT stock, nombre FROM producto WHERE idProducto = ? LIMIT 1");
        $stmt->execute([$id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tipo === 'salida' && $prod['stock'] < $cantidad) {
            $_SESSION['error'] = 'Stock insuficiente. Stock actual: ' . $prod['stock'];
            header('Location: ' . url('admin/productos')); exit();
        }

        $nuevoStock = $tipo === 'entrada' ? $prod['stock'] + $cantidad : $prod['stock'] - $cantidad;
        $conn->prepare("UPDATE producto SET stock = ? WHERE idProducto = ?")->execute([$nuevoStock, $id]);

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Ajuste de stock ($tipo) en producto '{$prod['nombre']}': $cantidad unidades. Stock: {$prod['stock']} → $nuevoStock", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Stock ajustado correctamente. Nuevo stock: $nuevoStock";
        header('Location: ' . url('admin/productos')); exit();
    }

    // ─── Eliminar (desactivar) ────────────────────────────────────────────────
    public function delete()
    {
        require_admin();
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $id = $_GET['id'] ?? null;
        if (!$id) { header('Location: ' . url('admin/productos')); exit(); }

        $conn->prepare("UPDATE producto SET estado = 'Inactivo' WHERE idProducto = ?")->execute([$id]);
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Desactivó producto ID $id", $_SESSION['usuario']['id']]);

        $_SESSION['success'] = "Producto desactivado correctamente.";
        header('Location: ' . url('admin/productos')); exit();
    }
}