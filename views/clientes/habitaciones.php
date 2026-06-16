<link rel="stylesheet" href="<?= asset('css/cssCliente/clientehabitaciones.css') ?>">

<!-- Hero -->
<div class="hab-hero">
    <div class="hab-hero-content">
        <div class="hab-hero-badge">Hotel Real Plaza & Convention Center</div>
        <h1>Nuestras Habitaciones</h1>
        <p>Encuentra el espacio perfecto para tu estadía</p>
    </div>
</div>

<div class="hab-main">
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="hab-alert hab-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= $_SESSION['error'] ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Filtros -->
    <div class="hab-filtros">
        <form method="GET" action="<?= url('habitaciones') ?>" class="hab-filtros-grid">
            <div class="hab-filtro-group">
                <label>Tipo de habitación</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($tipos as $t): ?>
                        <option value="<?= $t['idTipoHabitacion'] ?>" <?= ($_GET['tipo'] ?? '') == $t['idTipoHabitacion'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="hab-filtro-group">
                <label>Precio máx. (Bs.)</label>
                <input type="number" name="precio" class="form-control"
                       placeholder="Ej: 500" value="<?= htmlspecialchars($_GET['precio'] ?? '') ?>">
            </div>
            <div class="hab-filtro-group">
                <label>Fecha entrada</label>
                <input type="date" name="entrada" class="form-control"
                       min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($_GET['entrada'] ?? '') ?>">
            </div>
            <div class="hab-filtro-group">
                <label>Fecha salida</label>
                <input type="date" name="salida" class="form-control"
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($_GET['salida'] ?? '') ?>">
            </div>
            <div class="hab-filtro-group">
                <button type="submit" class="hab-filtro-btn">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </form>
        <?php if (!empty($_GET['tipo']) || !empty($_GET['precio']) || !empty($_GET['entrada']) || !empty($_GET['salida'])): ?>
            <a href="<?= url('habitaciones') ?>" class="hab-filtro-clear">
                <i class="fas fa-times"></i> Limpiar filtros
            </a>
        <?php endif; ?>
    </div>

    <!-- Resultados -->
    <div class="hab-resultados">
        <h6>
            <strong><?= count($habitaciones) ?></strong> 
            habitación<?= count($habitaciones) != 1 ? 'es' : '' ?> disponible<?= count($habitaciones) != 1 ? 's' : '' ?>
        </h6>
        <span class="badge-vista">
            <i class="fas fa-th-large"></i> Vista en grid
        </span>
    </div>

    <?php if (empty($habitaciones)): ?>
        <div class="hab-empty">
            <i class="fas fa-search"></i>
            <h6>No encontramos habitaciones con esos filtros</h6>
            <a href="<?= url('habitaciones') ?>" class="btn-outline">
                <i class="fas fa-undo"></i> Ver todas las habitaciones
            </a>
        </div>
    <?php else: ?>
        <div class="hab-grid">
            <?php foreach ($habitaciones as $h): ?>
            <div class="hab-card">
                <div class="hab-card-img">
                    <?php if (!empty($h['imagen'])): ?>
                        <img src="<?= asset($h['imagen']) ?>" alt="Habitación <?= htmlspecialchars($h['numero']) ?>">
                    <?php else: ?>
                        <div class="hab-card-img-placeholder">
                            <i class="fas fa-bed fa-3x"></i>
                        </div>
                    <?php endif; ?>
                    <span class="hab-card-badge"><?= htmlspecialchars($h['tipo']) ?></span>
                </div>
                <div class="hab-card-body">
                    <div class="hab-card-header">
                        <h5 class="hab-card-tipo">
                            Habitación <?= htmlspecialchars($h['numero']) ?>
                            <small>Piso <?= $h['piso'] ?></small>
                        </h5>
                        <div class="hab-card-precio">
                            <span class="numero">Bs. <?= number_format($h['precio'], 2) ?></span>
                            <span class="label">/ noche</span>
                        </div>
                    </div>
                    <p class="hab-card-desc">
                        <?= htmlspecialchars(mb_substr($h['tipo_desc'] ?? '', 0, 80)) ?>...
                    </p>
                    <div class="hab-card-actions">
                        <a href="<?= url('habitaciones/detalle?id=' . $h['idHabitacion']) ?>"
                           class="hab-btn hab-btn-outline">
                            <i class="fas fa-eye"></i> Ver detalle
                        </a>
                        <?php if (!empty($_SESSION['usuario'])): ?>
                            <a href="<?= url('reservar?id=' . $h['idHabitacion']) ?>"
                               class="hab-btn hab-btn-primary">
                                <i class="fas fa-calendar-plus"></i> Reservar
                            </a>
                        <?php else: ?>
                            <a href="<?= url('login') ?>" class="hab-btn hab-btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Iniciar sesión
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Decoración final -->
    <div class="hab-deco">
        <span class="hab-deco-line">
            <i class="fas fa-hotel"></i>
            Hotel Real Plaza & Convention Center
            <i class="fas fa-hotel"></i>
        </span>
    </div>
</div>