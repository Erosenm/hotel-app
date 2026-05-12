<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin | Hotel' ?></title>
    <?php include __DIR__ . '/../components/header.php'; ?>
    <link rel="stylesheet" href="<?= asset('css/cssAdmin.css/styleAdmin.css') ?>">
    
</head>
<body>
 
<!-- Overlay para cerrar sidebar en móvil -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
 
<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <a class="sidebar-brand" href="<?= url('adminpanel') ?>">
        <span>🏨 Hotel Admin</span>
        <button class="sidebar-close" id="sidebarClose" title="Cerrar menú">✕</button>
    </a>
 
    <div class="sidebar-user">
        <strong><?= htmlspecialchars($_SESSION['usuario']['nombre'] ?? '') ?></strong>
        <?= htmlspecialchars($_SESSION['usuario']['rol'] ?? '') ?>
    </div>
 
    <nav class="sidebar-nav">
        <div class="sidebar-label">Principal</div>
        <a href="<?= url('adminpanel') ?>"
           class="<?= !str_contains($_SERVER['REQUEST_URI'], 'admin/') && str_contains($_SERVER['REQUEST_URI'], 'adminpanel') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
 
        <div class="sidebar-label">Gestión</div>
        <?php if ($_SESSION['usuario']['rol'] === 'Administrador'): ?>
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
        </a>
 
        <?php if ($_SESSION['usuario']['rol'] === 'Administrador'): ?>
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
        <?= $content ?>
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