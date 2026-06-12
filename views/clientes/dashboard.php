<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php $u = $_SESSION['usuario']; ?>

<link rel="stylesheet" href="<?= asset('css/cssCliente/clientedashboard.css') ?>">

<!-- Hero Section -->
<div class="client-hero">
    <div class="client-hero-content">
        <div class="client-hero-badge">Hotel Real Plaza & Convention Center</div>
        <h1>Bienvenido de vuelta, <?= htmlspecialchars(explode(' ', $u['nombre'])[0]) ?></h1>
        <p class="client-hero-sub">Tu espacio de descanso en el corazón de Bolivia</p>
    </div>
</div>

<div class="client-main">
    <!-- Alertas -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="client-alert client-alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?= $_SESSION['success'] ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="client-alert client-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= $_SESSION['error'] ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="client-stats-grid">
        <div class="client-stat">
            <div class="client-stat-number total"><?= $stats['total'] ?? 0 ?></div>
            <div class="client-stat-label">Total reservas</div>
        </div>
        <div class="client-stat">
            <div class="client-stat-number confirmadas"><?= $stats['confirmadas'] ?? 0 ?></div>
            <div class="client-stat-label">Confirmadas</div>
        </div>
        <div class="client-stat">
            <div class="client-stat-number pendientes"><?= $stats['pendientes'] ?? 0 ?></div>
            <div class="client-stat-label">Pendientes</div>
        </div>
        <div class="client-stat">
            <div class="client-stat-number completadas"><?= $stats['completadas'] ?? 0 ?></div>
            <div class="client-stat-label">Completadas</div>
        </div>
        <div class="client-stat">
            <div class="client-stat-number gastado">Bs. <?= number_format($stats['total_gastado'] ?? 0, 2) ?></div>
            <div class="client-stat-label">Total pagado</div>
        </div>
    </div>

    <!-- Próxima reserva destacada -->
    <?php if (!empty($proximaReserva)): ?>
    <div class="client-next-card">
        <div class="client-next-content">
            <div class="client-next-icon">
                <i class="fas fa-calendar-star"></i>
            </div>
            <div class="client-next-info">
                <div class="client-next-label">Próxima estancia</div>
                <div class="client-next-title">
                    Habitación <?= htmlspecialchars($proximaReserva['hab_numero']) ?> — <?= htmlspecialchars($proximaReserva['tipo']) ?>
                </div>
                <div class="client-next-dates">
                    <i class="far fa-calendar-alt me-1"></i>
                    <?= date('d/m/Y', strtotime($proximaReserva['fechaInicio'])) ?> → <?= date('d/m/Y', strtotime($proximaReserva['fechaFin'])) ?>
                </div>
            </div>
            <span class="client-next-badge <?= strtolower($proximaReserva['estado']) ?>">
                <?= $proximaReserva['estado'] ?>
            </span>
            <a href="<?= url('cliente/reservas') ?>" class="client-next-btn">
                Detalles <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Acciones rápidas -->
    <div class="client-section">
        <div class="client-section-header">
            <div class="client-section-title">
                <i class="fas fa-bolt"></i>
                <h5>Acciones rápidas</h5>
            </div>
        </div>
        <div class="client-actions-grid">
            <a href="<?= url('habitaciones') ?>" class="client-action">
                <div class="client-action-icon blue"><i class="fas fa-bed"></i></div>
                <span>Ver habitaciones</span>
            </a>
            <a href="<?= url('cliente/reservas') ?>" class="client-action">
                <div class="client-action-icon green"><i class="fas fa-calendar-alt"></i></div>
                <span>Mis reservas</span>
            </a>
            <a href="<?= url('cliente/perfil') ?>" class="client-action">
                <div class="client-action-icon teal"><i class="fas fa-user-circle"></i></div>
                <span>Mi perfil</span>
            </a>
            <a href="<?= url('logout') ?>" class="client-action">
                <div class="client-action-icon red"><i class="fas fa-sign-out-alt"></i></div>
                <span>Cerrar sesión</span>
            </a>
        </div>
    </div>

    <!-- Reservas recientes -->
    <div class="client-section">
        <div class="client-section-header">
            <div class="client-section-title">
                <i class="fas fa-history"></i>
                <h5>Reservas recientes</h5>
            </div>
            <a href="<?= url('cliente/reservas') ?>" class="client-section-link">Ver todas →</a>
        </div>

        <?php if (empty($reservasRecientes)): ?>
            <div class="client-empty">
                <i class="fas fa-calendar-plus"></i>
                <h6>Aún no tienes reservas</h6>
                <p>Descubre nuestras habitaciones y vive una experiencia única en el Hotel Real Plaza.</p>
                <a href="<?= url('habitaciones') ?>" class="btn">
                    <i class="fas fa-search"></i> Explorar habitaciones
                </a>
            </div>
        <?php else: ?>
            <div class="client-reservas-grid">
                <?php foreach ($reservasRecientes as $r): ?>
                <?php
                    $badgeClass = match($r['estado_reserva']) {
                        'Pendiente' => 'pendiente',
                        'Confirmada' => 'confirmada',
                        'Cancelada' => 'cancelada',
                        'Completada' => 'completada',
                        default => 'completada'
                    };
                    $dias = (new DateTime($r['fechaInicio']))->diff(new DateTime($r['fechaFin']))->days;
                ?>
                <div class="client-reserva">
                    <div class="client-reserva-img">
                        <?php if (!empty($r['imagen'])): ?>
                            <img src="<?= asset($r['imagen']) ?>" alt="Habitación">
                        <?php else: ?>
                            <div class="client-reserva-img-placeholder">
                                <i class="fas fa-bed fa-2x text-muted"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="client-reserva-content">
                        <div class="client-reserva-header">
                            <span class="client-reserva-hab">Hab. <?= htmlspecialchars($r['habitacion_numero']) ?> — <?= htmlspecialchars($r['tipo_habitacion']) ?></span>
                            <span class="client-reserva-badge <?= $badgeClass ?>"><?= $r['estado_reserva'] ?></span>
                        </div>
                        <div class="client-reserva-dates">
                            <i class="far fa-calendar-alt"></i>
                            <?= date('d/m/Y', strtotime($r['fechaInicio'])) ?> → <?= date('d/m/Y', strtotime($r['fechaFin'])) ?>
                            <span class="text-muted">(<?= $dias ?> noche<?= $dias != 1 ? 's' : '' ?>)</span>
                        </div>
                        <div class="client-reserva-price">Bs. <?= number_format($r['precioTotal'], 2) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="client-view-all">
                <a href="<?= url('cliente/reservas') ?>" class="btn-outline">
                    Ver todas mis reservas <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>