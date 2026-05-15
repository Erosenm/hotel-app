<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-broom me-2 text-primary"></i>Control de Limpieza
    </h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaTarea">
        <i class="fas fa-plus me-1"></i>Nueva tarea
    </button>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if ($stats['sucias'] > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-exclamation-triangle fs-5"></i>
    <div><strong><?= $stats['sucias'] ?> habitación(es) necesitan limpieza</strong> después de un check-out reciente.</div>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-danger"><?= $stats['sucias'] ?></div>
            <div class="text-muted small">Necesitan limpieza</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-warning"><?= $stats['pendientes'] ?></div>
            <div class="text-muted small">Tareas pendientes</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-info"><?= $stats['en_proceso'] ?></div>
            <div class="text-muted small">En proceso</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-success"><?= $stats['completadas'] ?></div>
            <div class="text-muted small">Completadas</div>
        </div>
    </div>
</div>

<!-- Kanban de tareas -->
<div class="row g-3">
    <?php
    $columnas = [
        'Pendiente'  => ['color' => 'warning', 'icon' => 'fa-clock'],
        'En proceso' => ['color' => 'info',    'icon' => 'fa-spinner'],
        'Completada' => ['color' => 'success',  'icon' => 'fa-check-circle'],
    ];
    foreach ($columnas as $estado => $cfg):
        $tareasFiltradas = array_filter($tareas, fn($t) => $t['estado'] === $estado);
    ?>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-<?= $cfg['color'] ?> bg-opacity-10 border-0">
                <h6 class="fw-semibold mb-0 text-<?= $cfg['color'] ?>">
                    <i class="fas <?= $cfg['icon'] ?> me-2"></i><?= $estado ?>
                    <span class="badge bg-<?= $cfg['color'] ?> ms-2"><?= count($tareasFiltradas) ?></span>
                </h6>
            </div>
            <div class="card-body p-2" style="min-height:200px;">
                <?php if (empty($tareasFiltradas)): ?>
                    <div class="text-center text-muted py-4 small">Sin tareas</div>
                <?php endif; ?>
                <?php foreach ($tareasFiltradas as $t): ?>
                <div class="card mb-2 border shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold">Hab. N° <?= htmlspecialchars($t['habitacion_numero']) ?></span>
                            <small class="text-muted">Piso <?= $t['piso'] ?></small>
                        </div>
                        <div class="text-muted small mb-2"><?= htmlspecialchars($t['tipo_habitacion']) ?></div>
                        <?php if ($t['asignado_nombre']): ?>
                            <div class="small mb-2">
                                <i class="fas fa-user me-1 text-muted"></i>
                                <?= htmlspecialchars($t['asignado_nombre'] . ' ' . $t['asignado_paterno']) ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($t['observaciones']): ?>
                            <div class="small text-muted mb-2 fst-italic"><?= htmlspecialchars($t['observaciones']) ?></div>
                        <?php endif; ?>
                        <div class="small text-muted mb-2">
                            <?= date('d/m/Y H:i', strtotime($t['fechaAsignacion'])) ?>
                        </div>
                        <!-- Acciones -->
                        <div class="d-flex gap-1 flex-wrap">
                            <?php if ($t['estado'] === 'Pendiente'): ?>
                                <a href="<?= url('admin/limpieza/estado?id=' . $t['idTarea'] . '&estado=En+proceso') ?>"
                                   class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-play me-1"></i>Iniciar
                                </a>
                            <?php elseif ($t['estado'] === 'En proceso'): ?>
                                <a href="<?= url('admin/limpieza/estado?id=' . $t['idTarea'] . '&estado=Completada') ?>"
                                   class="btn btn-sm btn-success"
                                   onclick="return confirm('¿Marcar como completada? La habitación quedará Disponible.')">
                                    <i class="fas fa-check me-1"></i>Completar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal nueva tarea -->
<div class="modal fade" id="modalNuevaTarea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="fas fa-broom me-2"></i>Nueva tarea de limpieza</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/limpieza/crear') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Habitación <span class="text-danger">*</span></label>
                        <select name="idHabitacion" class="form-select" required>
                            <option value="">— Seleccionar habitación —</option>
                            <?php foreach ($habitacionesSucias as $h): ?>
                                <option value="<?= $h['idHabitacion'] ?>">
                                    N° <?= htmlspecialchars($h['numero']) ?> — Piso <?= $h['piso'] ?>
                                    (<?= htmlspecialchars($h['tipo']) ?>) [<?= htmlspecialchars($h['estado_hab']) ?>]
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Se muestran todas las habitaciones. Las marcadas [Limpieza] son prioritarias.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Asignar a (opcional)</label>
                        <select name="idUsuario" class="form-select">
                            <option value="">— Sin asignar —</option>
                            <?php foreach ($personal as $p): ?>
                                <option value="<?= $p['idUsuario'] ?>">
                                    <?= htmlspecialchars($p['nombre'] . ' ' . $p['paterno']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2"
                                  placeholder="Ej: Cambiar sábanas, limpiar baño..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear tarea</button>
                </div>
            </form>
        </div>
    </div>
</div>