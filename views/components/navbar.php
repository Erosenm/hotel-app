<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<link rel="stylesheet" href="<?= asset('css/styleNavbar.css') ?>">

<header id="main-header">
    <div class="logo">REAL PLAZA HOTEL</div>

    <nav>
        <ul>
            <li><a href="<?= url('/') ?>">Inicio</a></li>
            <li><a href="<?= url('habitaciones') ?>">Habitaciones</a></li>
        </ul>
    </nav>

    <?php if (!empty($_SESSION['usuario'])): ?>
        <?php $u = $_SESSION['usuario']; ?>
        <div class="user-area" id="userArea">
            <button class="btn-user" id="btnUser">
                👤 <?= htmlspecialchars($u['nombre']) ?>
                <span class="user-role">(<?= htmlspecialchars($u['rol']) ?>)</span>
                <span id="dropArrow">▾</span>
            </button>
            <div class="nav-dropdown" id="navDrop">
                <?php if (in_array($u['rol'], ['Administrador', 'Recepcionista'])): ?>
                    <a href="<?= url('adminpanel') ?>" class="nav-drop-item admin">
                        🛠 Panel Admin
                    </a>
                <?php endif; ?>
                <a href="<?= url('cliente/dashboard') ?>" class="nav-drop-item">
                    🏠 Mi Panel
                </a>
                <a href="<?= url('cliente/reservas') ?>" class="nav-drop-item">
                    📅 Mis Reservas
                </a>
                <a href="<?= url('cliente/perfil') ?>" class="nav-drop-item">
                    👤 Mi Perfil
                </a>
                <a href="<?= url('logout') ?>" class="nav-drop-item logout">
                    🚪 Cerrar sesión
                </a>
            </div>
        </div>
    <?php else: ?>
        <a class="btn" href="<?= url('login') ?>">Iniciar Sesión</a>
    <?php endif; ?>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnUser  = document.getElementById('btnUser');
    const navDrop  = document.getElementById('navDrop');
    const dropArrow= document.getElementById('dropArrow');
    if (!btnUser || !navDrop) return;
    btnUser.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = navDrop.classList.contains('nav-drop-open');
        navDrop.classList.toggle('nav-drop-open', !isOpen);
        dropArrow.textContent = isOpen ? '▾' : '▴';
    });
    document.addEventListener('click', function () {
        navDrop.classList.remove('nav-drop-open');
        dropArrow.textContent = '▾';
    });
    navDrop.addEventListener('click', function (e) { e.stopPropagation(); });
});
</script>