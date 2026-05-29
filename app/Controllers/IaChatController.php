<?php
class IaChatController
{
    public function index()
    {
        require_admin();
        require_once __DIR__ . '/../../config/database.php';

        $mensajesPorDia = $conn->query("
            SELECT DATE(fecha) as dia, COUNT(*) as total
            FROM ia_mensaje
            WHERE fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(fecha)
            ORDER BY dia ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $usuariosActivos = $conn->query("
            SELECT u.nombre, u.paterno, COUNT(m.idMensaje) as total
            FROM ia_mensaje m
            JOIN usuario u ON m.idUsuario_FK = u.idUsuario
            WHERE m.idUsuario_FK IS NOT NULL
            GROUP BY m.idUsuario_FK
            ORDER BY total DESC
            LIMIT 5
        ")->fetchAll(PDO::FETCH_ASSOC);

        $totalMensajes = $conn->query("SELECT COUNT(*) FROM ia_mensaje")->fetchColumn();
        $mensajesHoy = $conn->query("SELECT COUNT(*) FROM ia_mensaje WHERE DATE(fecha) = CURDATE()")->fetchColumn();

        $ultimasConversaciones = $conn->query("
            SELECT m.mensajeUsuario, m.respuestaIA, m.fecha, u.nombre, u.paterno
            FROM ia_mensaje m
            LEFT JOIN usuario u ON m.idUsuario_FK = u.idUsuario
            ORDER BY m.fecha DESC
            LIMIT 10
        ")->fetchAll(PDO::FETCH_ASSOC);

        $layout = 'admin';
        $title = 'Dashboard IA Chat';
        $styles = ['css/cssAdmin/styleAdmin.css'];

        ob_start();
        include __DIR__ . '/../../views/admin/ia_chat/index.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../views/layouts/admin_layout.php';
    }
}