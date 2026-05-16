<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<link rel="stylesheet" href="<?= asset('css/styleNavbar.css') ?>">

<?php
$current = $_SERVER['REQUEST_URI'];

$isClientPage =
    str_contains($current, 'cliente/dashboard') ||
    str_contains($current, 'cliente/reservas') ||
    str_contains($current, 'cliente/perfil') ||
    str_contains($current, 'habitaciones');
?>

<header id="main-header" class="<?= $isClientPage ? 'scrolled' : '' ?>">
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
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Panel Admin</span>
                    </a>
                <?php endif; ?>

                <a href="<?= url('cliente/dashboard') ?>" class="nav-drop-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Mi Panel</span>
                </a>

                <a href="<?= url('cliente/reservas') ?>" class="nav-drop-item">
                <i class="fas fa-calendar-check"></i>
                <span>Mis Reservas</span>
                </a>

                <a href="<?= url('cliente/perfil') ?>" class="nav-drop-item">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
                </a>

                <a href="<?= url('logout') ?>" class="nav-drop-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar sesión</span>
                </a>

            </div>
        </div>
    <?php else: ?>
        <a class="btn" href="<?= url('login') ?>">Iniciar Sesión</a>
    <?php endif; ?>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const btnUser   = document.getElementById('btnUser');
    const navDrop   = document.getElementById('navDrop');
    const dropArrow = document.getElementById('dropArrow');

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

    navDrop.addEventListener('click', function (e) {
        e.stopPropagation();
    });

});
</script>