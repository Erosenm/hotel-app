[33mcommit 7b991b38b7b9e1ba7a927c52fae67e96d266b218[m[33m ([m[1;36mHEAD[m[33m -> [m[1;32mmain[m[33m, [m[1;31morigin/main[m[33m)[m
Author: Erosenm <erosenm21@gmail.com>
Date:   Tue May 12 00:19:05 2026 -0400

    actualizacion solo del dashboard.php que se integro css nada mas

[1mdiff --git a/.htaccess b/.htaccess[m
[1mnew file mode 100644[m
[1mindex 0000000..7259f93[m
[1m--- /dev/null[m
[1m+++ b/.htaccess[m
[36m@@ -0,0 +1,16 @@[m
[32m+[m[32mOptions -Indexes[m
[32m+[m[32mRewriteEngine On[m
[32m+[m
[32m+[m[32m# Base correcta[m
[32m+[m[32mRewriteBase /hotel-app/[m
[32m+[m
[32m+[m[32m# Si existe archivo o carpeta, no redirigir[m
[32m+[m[32mRewriteCond %{REQUEST_FILENAME} -f [OR][m
[32m+[m[32mRewriteCond %{REQUEST_FILENAME} -d[m
[32m+[m[32mRewriteRule ^ - [L][m
[32m+[m
[32m+[m[32m# Todo pasa al index principal[m
[32m+[m[32mRewriteRule ^ public/index.php [QSA,L][m
[32m+[m
[32m+[m[32m# Index por defecto[m
[32m+[m[32mDirectoryIndex public/index.php[m
\ No newline at end of file[m
[1mdiff --git a/app/Controllers/AdminPanelController.php b/app/Controllers/AdminPanelController.php[m
[1mnew file mode 100644[m
[1mindex 0000000..6568347[m
[1m--- /dev/null[m
[1m+++ b/app/Controllers/AdminPanelController.php[m
[36m@@ -0,0 +1,93 @@[m
[32m+[m[32m<?php[m
[32m+[m[32mrequire_once __DIR__ . '/../helpers/auth.php';[m
[32m+[m[41m [m
[32m+[m[32mclass AdminPanelController[m
[32m+[m[32m{[m
[32m+[m[32m    public function index()[m
[32m+[m[32m    {[m
[32m+[m[32m        require_recepcionista();[m
[32m+[m[32m        require __DIR__ . '/../../config/database.php';[m
[32m+[m[41m [m
[32m+[m[32m        $stmt = $conn->query("[m
[32m+[m[32m            SELECT u.*, r.nombre AS rol[m
[32m+[m[32m            FROM usuario u[m
[32m+[m[32m            LEFT JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario[m
[32m+[m[32m            LEFT JOIN rol r ON ur.idRol = r.idRol[m
[32m+[m[32m            ORDER BY u.fechaRegistro DESC[m
[32m+[m[32m        ");[m
[32m+[m[32m        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);[m
[32m+[m[41m [m
[32m+[m[32m        $stats = [];[m
[32m+[m[32m        $stats['total_usuarios']           = $conn->query("SELECT COUNT(*) FROM usuario")->fetchColumn();[m
[32m+[m[32m        $stats['usuarios_activos']         = $conn->query("SELECT COUNT(*) FROM usuario WHERE estado = 'Activo'")->fetchColumn();[m
[32m+[m[32m        $stats['total_habitaciones']       = $conn->query("SELECT COUNT(*) FROM habitacion")->fetchColumn();[m
[32m+[m[32m        $stats['habitaciones_disponibles'] = $conn->query("[m
[32m+[m[32m            SELECT COUNT(*) FROM habitacion h[m
[32m+[m[32m            JOIN estado_habitacion e ON h.idEstadoHabitacion_FK = e.idEstado[m
[32m+[m[32m            WHERE e.nombre = 'Disponible'[m
[32m+[m[32m        ")->fetchColumn();[m
[32m+[m[32m        $stats['reservas_pendientes']      = $conn->query("[m
[32m+[m[32m            SELECT COUNT(*) FROM reserva r[m
[32m+[m[32m            JOIN estado_reserva e ON r.idEstadoReserva_FK = e.idEstado[m
[32m+[m[32m            WHERE e.nombre = 'Pendiente'[m
[32m+[m[32m        ")->fetchColumn();[m
[32m+[m[32m        $stats['reservas_confirmadas']     = $conn->query("[m
[32m+[m[32m            SELECT COUNT(*) FROM reserva r[m
[32m+[m[32m            JOIN estado_reserva e ON r.idEstadoReserva_FK = e.idEstado[m
[32m+[m[32m            WHERE e.nombre = 'Confirmada'[m
[32m+[m[32m        ")->fetchColumn();[m
[32m+[m[41m [m
[32m+[m[32m        // ── STATS DE PAGOS ──────────────────────────────────[m
[32m+[m[32m        $stats['pagos_hoy']     = $conn->query("[m
[32m+[m[32m            SELECT COUNT(*) FROM pago p[m
[32m+[m[32m            JOIN estado_pago e ON p.idEstadoPago_FK = e.idEstado[m
[32m+[m[32m            WHERE e.nombre = 'Pagado' AND DATE(p.fechaPago) = CURDATE()[m
[32m+[m[