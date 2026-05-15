<style>
.hab-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: transform .25s, box-shadow .25s;
}
.hab-card:hover { transform: translateY(-6px); box-shadow: 0 10px 35px rgba(0,0,0,0.15); }
.hab-card img { width:100%; height:220px; object-fit:cover; }
.hab-placeholder { width:100%; height:220px; background:#f1f3f5; display:flex; align-items:center; justify-content:center; }
.precio-tag { background:#0f3460; color:#fff; display:inline-block; padding:4px 12px; border-radius:20px; font-size:.85rem; font-weight:600; }
.filtros-bar { background:#fff; border-radius:16px; box-shadow:0 2px 15px rgba(0,0,0,0.08); padding:20px 24px; margin-bottom:30px; }
</style>

<div class="page-top-space"  style="background:linear-gradient(135deg,#1a1a2e,#0f3460);padding:40px 20px;color:#fff;text-align:center;">
    <p style="color:#a0b4d0;font-size:.75rem;letter-spacing:.2em;text-transform:uppercase;">ALOJAMIENTO</p>
    <h1 style="font-size:2.2rem;font-weight:700;color:#FFFFFF">Nuestras Habitaciones</h1>
    <p style="color:#a0b4d0;">Encuentra el espacio perfecto para tu estadía</p>
</div>

<div style="max-width:1200px;margin:0 auto;padding:30px 20px;">

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="filtros-bar">
        <form method="GET" action="<?= url('habitaciones') ?>" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Tipo de habitación</label>
                <select name="tipo" class="form-select form-select-sm">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= $t['idTipoHabitacion'] ?>" <?= ($_GET['tipo'] ?? '') == $t['idTipoHabitacion'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Precio máximo (Bs.)</label>
                <input type="number" name="precio" class="form-control form-control-sm"
                       placeholder="Ej: 500" value="<?= htmlspecialchars($_GET['precio'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Fecha entrada</label>
                <input type="date" name="entrada" class="form-control form-control-sm"
                       min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_GET['entrada'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Fecha salida</label>
                <input type="date" name="salida" class="form-control form-control-sm"
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($_GET['salida'] ?? '') ?>">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
        <?php if (!empty($_GET['tipo']) || !empty($_GET['precio']) || !empty($_GET['entrada'])): ?>
            <div class="mt-2">
                <a href="<?= url('habitaciones') ?>" class="text-muted small">
                    <i class="fas fa-times me-1"></i>Limpiar filtros
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold text-muted mb-0">
            <?= count($habitaciones) ?> habitacion<?= count($habitaciones) != 1 ? 'es' : '' ?> disponible<?= count($habitaciones) != 1 ? 's' : '' ?>
        </h6>
    </div>

    <?php if (empty($habitaciones)): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="fas fa-search fa-3x text-muted mb-3 d-block"></i>
            <h6 class="text-muted">No encontramos habitaciones con esos filtros</h6>
            <a href="<?= url('habitaciones') ?>" class="btn btn-outline-primary mt-2">Ver todas</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($habitaciones as $h): ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="hab-card bg-white">
                    <?php if (!empty($h['imagen'])): ?>
                        <img src="<?= asset($h['imagen']) ?>" alt="Habitación <?= htmlspecialchars($h['numero']) ?>">
                    <?php else: ?>
                        <div class="hab-placeholder">
                            <i class="fas fa-bed fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="fw-bold mb-0"><?= htmlspecialchars($h['tipo']) ?></h5>
                                <p class="text-muted small mb-0">Habitación <?= htmlspecialchars($h['numero']) ?> · Piso <?= $h['piso'] ?></p>
                            </div>
                            <span class="precio-tag">Bs. <?= number_format($h['precio'], 2) ?>/noche</span>
                        </div>
                        <p class="text-muted small mb-3" style="min-height:36px;">
                            <?= htmlspecialchars(mb_substr($h['tipo_desc'] ?? '', 0, 80)) ?>...
                        </p>
                        <div class="d-flex gap-2">
                            <a href="<?= url('habitaciones/detalle?id=' . $h['idHabitacion']) ?>"
                               class="btn btn-outline-secondary btn-sm flex-grow-1">
                                <i class="fas fa-eye me-1"></i>Ver detalle
                            </a>
                            <?php if (!empty($_SESSION['usuario'])): ?>
                                <a href="<?= url('reservar?id=' . $h['idHabitacion']) ?>"
                                   class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="fas fa-calendar-plus me-1"></i>Reservar
                                </a>
                            <?php else: ?>
                                <a href="<?= url('login') ?>" class="btn btn-primary btn-sm flex-grow-1">
                                    <i class="fas fa-sign-in-alt me-1"></i>Iniciar sesión
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>