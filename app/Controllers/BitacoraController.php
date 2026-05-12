<?php
require_once __DIR__ . '/../helpers/auth.php';

class BitacoraController
{
    public function index()
    {
        require_admin();
        require __DIR__ . '/../../config/database.php';

        $filtroUsuario = $_GET['usuario'] ?? '';
        $filtroFecha   = $_GET['fecha']   ?? '';

        $where  = [];
        $params = [];

        if (!empty($filtroUsuario)) {
            $where[]  = "(u.nombre LIKE ? OR u.paterno LIKE ? OR u.email LIKE ?)";
            $params[] = "%$filtroUsuario%";
            $params[] = "%$filtroUsuario%";
            $params[] = "%$filtroUsuario%";
        }

        if (!empty($filtroFecha)) {
            $where[]  = "DATE(b.fechaHora) = ?";
            $params[] = $filtroFecha;
        }

        $whereSQL = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $stmt = $conn->prepare("
            SELECT
                b.idBitacora,
                b.accion,
                b.fechaHora,
                u.nombre  AS usuario_nombre,
                u.paterno AS usuario_paterno,
                u.email   AS usuario_email,
                r.nombre  AS rol
            FROM bitacora b
            LEFT JOIN usuario u     ON b.idUsuario_FK = u.idUsuario
            LEFT JOIN usuario_rol ur ON u.idUsuario   = ur.idUsuario
            LEFT JOIN rol r          ON ur.idRol      = r.idRol
            $whereSQL
            ORDER BY b.fechaHora DESC
        ");
        $stmt->execute($params);
        $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stats = [];
        $stats['total']  = $conn->query("SELECT COUNT(*) FROM bitacora")->fetchColumn();
        $stats['hoy']    = $conn->query("SELECT COUNT(*) FROM bitacora WHERE DATE(fechaHora) = CURDATE()")->fetchColumn();
        $stats['semana'] = $conn->query("SELECT COUNT(*) FROM bitacora WHERE fechaHora >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

        ob_start();
        include __DIR__ . '/../../views/admin/bitacora/index.php';
        $content = ob_get_clean();

        $title = "Bitácora | Admin";
        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }
}