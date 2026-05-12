<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-clipboard-list me-2 text-primary"></i>Bitácora del Sistema
    </h4>
</div>

<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-primary"><?= $stats['total'] ?></div>
            <div class="text-muted small">Total registros</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-success"><?= $stats['hoy'] ?></div>
            <div class="text-muted small">Hoy</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-warning"><?= $stats['semana'] ?></div>
            <div class="text-muted small">Últimos 7 días</div>
        </div>
    </div>
</div>

<!-- FILTROS -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('admin/bitacora') ?>" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold small">Buscar usuario</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="usuario" class="form-control"
                           placeholder="Nombre, apellido o email..."
                           value="<?= htmlspecialchars($filtroUsuario) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Fecha</label>
                <input type="date" name="fecha" class="form-control"
                       value="<?= htmlspecialchars($filtroFecha) ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="<?= url('admin/bitacora') ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- TABLA -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Acción</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($registros)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-clipboard fa-2x mb-2 d-block"></i>
                            No hay registros en la bitácora
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($registros as $i => $b): ?>
                    <tr>
                        <td class="text-muted small"><?= $b['idBitacora'] ?></td>
                        <td>
                            <span class="badge bg-primary me-1">
                                <?= strtoupper(substr($b['usuario_nombre'] ?? '?', 0, 1)) ?>
                            </span>
                            <?= htmlspecialchars($b['usuario_nombre'] . ' ' . $b['usuario_paterno']) ?>
                            <br>
                            <small class="text-muted"><?= htmlspecialchars($b['usuario_email'] ?? '') ?></small>
                        </td>
                        <td>
                            <?php
                                $rolColor = [
                                    'Administrador' => 'danger',
                                    'Recepcionista' => 'warning',
                                    'Cliente'       => 'info'
                                ];
                                $rc = $rolColor[$b['rol']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $rc ?>">
                                <?= htmlspecialchars($b['rol'] ?? 'Sin rol') ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-dark"><?= htmlspecialchars($b['accion']) ?></span>
                        </td>
                        <td>
                            <i class="fas fa-clock text-muted me-1"></i>
                            <?= date('d/m/Y H:i:s', strtotime($b['fechaHora'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 d-flex justify-content-between">
        <small class="text-muted">
            Mostrando <?= count($registros) ?> de <?= $stats['total'] ?> registros
        </small>
        <?php if (!empty($filtroUsuario) || !empty($filtroFecha)): ?>
            <small class="text-warning">
                <i class="fas fa-filter me-1"></i>Filtros activos
            </small>
        <?php endif; ?>
    </div>
</div>