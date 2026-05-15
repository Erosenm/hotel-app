<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php $u = $_SESSION['usuario']; ?>

<style>
.cliente-hero {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    padding: 50px 40px 40px;
    color: #fff;
}-->
.stat-card {
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform .2s;
}
.stat-card:hover { transform: translateY(-4px); }
.reserva-card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.07);
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
}
.reserva-card:hover { transform: translateY(-3px); box-shadow: 0 6px 25px rgba(0,0,0,0.12); }
</style>

<!-- Hero bienvenida -->
    <div class="cliente-hero page-top-space">
    <div style="max-width:1200px;margin:0 auto;">
        <p style="color:#a0b4d0;font-size:.8rem;letter-spacing:.15em;text-transform:uppercase;margin-bottom:6px;">Bienvenido de vuelta</p>
        <h1 style="color:#FFFFFF;font-size:2rem;font-weight:700;margin-bottom:4px;"><?= htmlspecialchars($u['nombre']) ?></h1>
        <p style="color:#a0b4d0;margin:0;">Hotel Real Plaza — Tu espacio de descanso</p>
    </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:30px 20px;">

    <!-- Alertas -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card bg-white">
                <div style="font-size:2rem;font-weight:700;color:#0f3460;"><?= $stats['total'] ?? 0 ?></div>
                <div class="text-muted small mt-1">Total reservas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card bg-white">
                <div style="font-size:2rem;font-weight:700;color:#28a745;"><?= $stats['confirmadas'] ?? 0 ?></div>
                <div class="text-muted small mt-1">Confirmadas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card bg-white">
                <div style="font-size:2rem;font-weight:700;color:#ffc107;"><?= $stats['pendientes'] ?? 0 ?></div>
                <div class="text-muted small mt-1">Pendientes</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card bg-white">
                <div style="font-size:2rem;font-weight:700;color:#6c757d;"><?= $stats['completadas'] ?? 0 ?></div>
                <div class="text-muted small mt-1">Completadas</div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="fw-bold mb-3"><i class="fas fa-bolt me-2 text-warning"></i>Acciones rápidas</h5>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= url('habitaciones') ?>" class="text-decoration-none">
                <div class="stat-card bg-white d-flex flex-column align-items-center gap-2">
                    <i class="fas fa-bed" style="font-size:1.8rem;color:#0f3460;"></i>
                    <span class="fw-semibold text-dark small">Ver habitaciones</span>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= url('cliente/reservas') ?>" class="text-decoration-none">
                <div class="stat-card bg-white d-flex flex-column align-items-center gap-2">
                    <i class="fas fa-calendar-alt" style="font-size:1.8rem;color:#28a745;"></i>
                    <span class="fw-semibold text-dark small">Mis reservas</span>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= url('cliente/perfil') ?>" class="text-decoration-none">
                <div class="stat-card bg-white d-flex flex-column align-items-center gap-2">
                    <i class="fas fa-user-edit" style="font-size:1.8rem;color:#17a2b8;"></i>
                    <span class="fw-semibold text-dark small">Mi perfil</span>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="<?= url('logout') ?>" class="text-decoration-none">
                <div class="stat-card bg-white d-flex flex-column align-items-center gap-2">
                    <i class="fas fa-sign-out-alt" style="font-size:1.8rem;color:#dc3545;"></i>
                    <span class="fw-semibold text-dark small">Cerrar sesión</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Reservas recientes -->
    <h5 class="fw-bold mb-3"><i class="fas fa-history me-2 text-primary"></i>Reservas recientes</h5>

    <?php if (empty($reservasRecientes)): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="fas fa-calendar-plus fa-3x text-muted mb-3 d-block"></i>
            <h6 class="text-muted">Aún no tienes reservas</h6>
            <a href="<?= url('habitaciones') ?>" class="btn btn-primary mt-3 px-4">
                <i class="fas fa-search me-2"></i>Explorar habitaciones
            </a>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($reservasRecientes as $r): ?>
            <?php
                $colores = [
                    'Pendiente'  => ['badge' => 'warning'],
                    'Confirmada' => ['badge' => 'success'],
                    'Cancelada'  => ['badge' => 'danger'],
                    'Completada' => ['badge' => 'secondary'],
                    'No show'    => ['badge' => 'danger'],
                ];
                $c    = $colores[$r['estado_reserva']] ?? ['badge' => 'secondary'];
                $dias = (new DateTime($r['fechaInicio']))->diff(new DateTime($r['fechaFin']))->days;
            ?>
            <div class="col-12 col-md-6">
                <div class="reserva-card bg-white">
                    <div class="d-flex">
                        <?php if (!empty($r['imagen'])): ?>
                            <img src="<?= asset($r['imagen']) ?>" alt="Hab" style="width:110px;object-fit:cover;">
                        <?php else: ?>
                            <div style="width:110px;background:#f1f3f5;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-bed fa-2x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="p-3 flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <strong>Hab. <?= htmlspecialchars($r['habitacion_numero']) ?> — <?= htmlspecialchars($r['tipo_habitacion']) ?></strong>
                                <span class="badge bg-<?= $c['badge'] ?>"><?= $r['estado_reserva'] ?></span>
                            </div>
                            <p class="text-muted small mb-1">
                                <i class="fas fa-calendar me-1"></i>
                                <?= date('d/m/Y', strtotime($r['fechaInicio'])) ?> → <?= date('d/m/Y', strtotime($r['fechaFin'])) ?>
                                (<?= $dias ?> noche<?= $dias != 1 ? 's' : '' ?>)
                            </p>
                            <p class="fw-bold mb-0" style="color:#0f3460;">Bs. <?= number_format($r['precioTotal'], 2) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="<?= url('cliente/reservas') ?>" class="btn btn-outline-primary px-4">
                Ver todas mis reservas <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    <?php endif; ?>

</div>