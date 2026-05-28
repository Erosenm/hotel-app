<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// Badge de comprobantes QR pendientes
$comprobantes_pendientes = 0;
try {
    if (!isset($conn)) require __DIR__ . '/../../config/database.php';
    $comprobantes_pendientes = $conn->query("
        SELECT COUNT(*) FROM pago p
        JOIN estado_pago ep ON p.idEstadoPago_FK = ep.idEstado
        WHERE ep.nombre = 'Pendiente' AND p.comprobante IS NOT NULL
    ")->fetchColumn();
} catch (Exception $e) {
    $comprobantes_pendientes = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin | Hotel' ?></title>
    <link rel="icon" type="image/png" href="<?= asset('imgs/logo.jpg') ?>">
    <?php include __DIR__ . '/../components/header.php'; ?>
    <link rel="stylesheet" href="<?= asset('css/cssAdmin/styleAdmin.css') ?>">
    
</head>
<body>
 
<!-- Overlay para cerrar sidebar en móvil -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
 
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <a class="sidebar-brand" href="<?= url('adminpanel') ?>">
        <span>Hotel Admin</span>
        <button class="sidebar-close" id="sidebarClose" title="Cerrar menú">✕</button>
    </a>
 
    <div class="sidebar-user">
        <strong><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? '') ?></strong>
        <?= htmlspecialchars($_SESSION['usuario']['rol'] ?? '') ?>
    </div>
 
    <?php $rolActivo = $_SESSION['usuario']['rol'] ?? ''; ?>
    <nav class="sidebar-nav">

        <?php if (!in_array($rolActivo, ['Limpieza','Mantenimiento','Gerente','Contador'])): ?>
        <div class="sidebar-label">Principal</div>
        <a href="<?= url('adminpanel') ?>"
           class="<?= !str_contains($_SERVER['REQUEST_URI'], 'admin/') && str_contains($_SERVER['REQUEST_URI'], 'adminpanel') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <?php endif; ?>

        <?php if (in_array($rolActivo, ['Administrador','Recepcionista'])): ?>
        <div class="sidebar-label">Gestión</div>
        <?php if ($rolActivo === 'Administrador'): ?>
        <a href="<?= url('admin/usuarios') ?>"
            class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/usuarios') ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Usuarios
        </a>
        <?php endif; ?>
        <a href="<?= url('admin/habitaciones') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/habitaciones') ? 'active' : '' ?>">
            <i class="fas fa-bed"></i> Habitaciones
        </a>
        <a href="<?= url('admin/reservas') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/reservas') ? 'active' : '' ?>">
            <i class="fas fa-calendar-check"></i> Reservas
        </a>
        <a href="<?= url('admin/pagos') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/pagos') ? 'active' : '' ?>">
            <i class="fas fa-credit-card"></i> Pagos
            <?php if ($comprobantes_pendientes > 0): ?>
                <span class="badge bg-warning text-dark ms-auto"><?= $comprobantes_pendientes ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= url('admin/calendario') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/calendario') ? 'active' : '' ?>">
            <i class="fas fa-calendar-alt"></i> Calendario
        </a>
        <?php endif; ?>

        <?php if ($rolActivo === 'Administrador'): ?>
        <div class="sidebar-label">Inventario</div>
        <a href="<?= url('admin/productos') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/productos') ? 'active' : '' ?>">
            <i class="fas fa-box"></i> Productos
        </a>
        <a href="<?= url('admin/categorias') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/categorias') ? 'active' : '' ?>">
            <i class="fas fa-tags"></i> Categorías
        </a>
        <a href="<?= url('admin/servicios') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/servicios') ? 'active' : '' ?>">
            <i class="fas fa-concierge-bell"></i> Servicios
        </a>
        <?php endif; ?>

        <?php if (in_array($rolActivo, ['Administrador','Recepcionista','Limpieza'])): ?>
        <div class="sidebar-label">Operaciones</div>
        <a href="<?= url('admin/limpieza') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/limpieza') ? 'active' : '' ?>">
            <i class="fas fa-broom"></i> Limpieza
            <?php
            try {
                $nLimpieza = $conn->query("SELECT COUNT(*) FROM tarea_limpieza WHERE estado IN ('Pendiente','En proceso')")->fetchColumn();
                if ($nLimpieza > 0) echo '<span class="badge bg-info ms-auto">' . $nLimpieza . '</span>';
            } catch (Exception $e) {}
            ?>
        </a>
        <?php endif; ?>

        <?php if (in_array($rolActivo, ['Administrador','Mantenimiento'])): ?>
        <a href="<?= url('admin/mantenimiento') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/mantenimiento') ? 'active' : '' ?>">
            <i class="fas fa-tools"></i> Mantenimiento
        </a>
        <?php endif; ?>

        <?php if (in_array($rolActivo, ['Administrador','Gerente','Contador'])): ?>
        <div class="sidebar-label">Análisis</div>
        <a href="<?= url('admin/reportes') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/reportes') ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i> Reportes
        </a>
        <?php endif; ?>

        <div class="sidebar-label">Mi cuenta</div>
        <a href="<?= url('admin/perfil') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/perfil') ? 'active' : '' ?>">
            <i class="fas fa-user-circle"></i> Mi Perfil
        </a>

        <?php if ($rolActivo === 'Administrador'): ?>
        <div class="sidebar-label">Sistema</div>
        <a href="<?= url('admin/bitacora') ?>"
           class="<?= str_contains($_SERVER['REQUEST_URI'], 'admin/bitacora') ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i> Bitácora
        </a>
        <?php endif; ?>

    </nav>
 
    <div class="sidebar-footer">
        <a href="<?= url('logout') ?>">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </div>
</div>
 
<!-- CONTENIDO -->
<div class="main-content">
    <div class="top-bar">
        <div class="d-flex align-items-center">
            <!-- Hamburguesa solo en móvil -->
            <button class="btn-hamburger" id="btnHamburger" title="Abrir menú">
                <i class="fas fa-bars"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= url('adminpanel') ?>">Admin</a>
                    </li>
                    <li class="breadcrumb-item active"><?= $title ?? '' ?></li>
                </ol>
            </nav>
        </div>
        <span class="text-muted" style="font-size:0.85rem">
            <i class="fas fa-clock me-1"></i> <?= date('d/m/Y H:i') ?>
        </span>
    </div>
 
    <!-- Alertas flotantes -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible alert-floating">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
 
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible alert-floating">
            <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
 
    <div class="page-body">
        <?= $content ?? '' ?>
    </div>
</div>
 
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar        = document.getElementById('sidebar');
    const overlay        = document.getElementById('sidebarOverlay');
    const btnHamburger   = document.getElementById('btnHamburger');
    const btnClose       = document.getElementById('sidebarClose');
 
    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
 
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
 
    btnHamburger.addEventListener('click', openSidebar);
    btnClose.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
 
    // Cerrar sidebar al navegar en móvil
    document.querySelectorAll('.sidebar-nav a').forEach(function(link) {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) closeSidebar();
        });
    });
});
</script>
 
</body>
</html>