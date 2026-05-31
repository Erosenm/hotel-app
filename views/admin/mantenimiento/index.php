<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-tools me-2 text-primary"></i>Mantenimiento
    </h4>
    <?php if (in_array($_SESSION['usuario']['rol'], ['Administrador','Recepcionista'])): ?>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaIncidencia">
        <i class="fas fa-plus me-1"></i>Reportar incidencia
    </button>
    <?php endif; ?>
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

<?php if ($stats['urgentes'] > 0): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-exclamation-triangle fs-5"></i>
    <div><strong><?= $stats['urgentes'] ?> incidencia(s) urgente(s)</strong> sin resolver. Atención inmediata requerida.</div>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-secondary"><?= $stats['total'] ?></div>
            <div class="text-muted small">Total</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-warning"><?= $stats['pendientes'] ?></div>
            <div class="text-muted small">Pendientes</div>
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
            <div class="fs-4 fw-bold text-success"><?= $stats['resueltas'] ?></div>
            <div class="text-muted small">Resueltas</div>
        </div>
    </div>
</div>

<!-- Kanban -->
<div class="row g-3">
    <?php
    $columnas = [
        'Pendiente'  => ['color' => 'warning', 'icon' => 'fa-clock'],
        'En proceso' => ['color' => 'info',    'icon' => 'fa-spinner'],
        'Resuelta'   => ['color' => 'success',  'icon' => 'fa-check-circle'],
    ];
    $prioridadColor = ['Baja'=>'secondary','Media'=>'primary','Alta'=>'warning','Urgente'=>'danger'];

    foreach ($columnas as $estado => $cfg):
        $items = array_filter($incidencias, fn($i) => $i['estado'] === $estado);
    ?>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-<?= $cfg['color'] ?> bg-opacity-10 border-0">
                <h6 class="fw-semibold mb-0">
                    <i class="fas <?= $cfg['icon'] ?> me-2"></i><?= $estado ?>
                    <span class="badge bg-<?= $cfg['color'] ?> ms-2"><?= count($items) ?></span>
                </h6>
            </div>
            <div class="card-body p-2" style="min-height:200px;">
                <?php if (empty($items)): ?>
                    <div class="text-center text-muted py-4 small">Sin incidencias</div>
                <?php endif; ?>
                <?php foreach ($items as $inc):
                    $pc = $prioridadColor[$inc['prioridad']] ?? 'secondary';
                ?>
                <div class="card mb-2 border shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-bold small"><?= htmlspecialchars($inc['titulo']) ?></span>
                            <span class="badge bg-<?= $pc ?> ms-1"><?= $inc['prioridad'] ?></span>
                        </div>
                        <?php if ($inc['habitacion_numero']): ?>
                        <div class="text-muted small mb-1">
                            <i class="fas fa-bed me-1"></i>Hab. <?= $inc['habitacion_numero'] ?> — Piso <?= $inc['piso'] ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($inc['descripcion']): ?>
                        <div class="text-muted small mb-2 fst-italic"><?= htmlspecialchars(substr($inc['descripcion'], 0, 60)) ?>...</div>
                        <?php endif; ?>
                        <?php if ($inc['asignado_nombre']): ?>
                        <div class="small mb-2">
                            <i class="fas fa-user-cog me-1 text-muted"></i>
                            <?= htmlspecialchars($inc['asignado_nombre'] . ' ' . $inc['asignado_paterno']) ?>
                        </div>
                        <?php else: ?>
                        <div class="small text-warning mb-2"><i class="fas fa-user-slash me-1"></i>Sin asignar</div>
                        <?php endif; ?>
                        <div class="text-muted small mb-2">
                            <?= date('d/m/Y H:i', strtotime($inc['fechaCreacion'])) ?>
                        </div>

                        <!-- Acciones -->
                        <div class="d-flex gap-1 flex-wrap">
                            <?php if ($inc['estado'] === 'Pendiente'): ?>
                                <a href="<?= url('admin/mantenimiento/estado?id=' . $inc['idIncidencia'] . '&estado=En+proceso') ?>"
                                   class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-play me-1"></i>Iniciar
                                </a>
                            <?php elseif ($inc['estado'] === 'En proceso'): ?>
                                <a href="<?= url('admin/mantenimiento/estado?id=' . $inc['idIncidencia'] . '&estado=Resuelta') ?>"
                                   class="btn btn-sm btn-success"
                                   onclick="return confirm('¿Marcar como resuelta?')">
                                    <i class="fas fa-check me-1"></i>Resolver
                                </a>
                            <?php endif; ?>
                            <?php if (in_array($_SESSION['usuario']['rol'], ['Administrador','Recepcionista']) && $inc['estado'] !== 'Resuelta'): ?>
                                <button class="btn btn-sm btn-outline-secondary"
                                        onclick="abrirAsignar(<?= $inc['idIncidencia'] ?>, '<?= htmlspecialchars(addslashes($inc['titulo'])) ?>')">
                                    <i class="fas fa-user-tag"></i>
                                </button>
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

<!-- Modal nueva incidencia -->
<div class="modal fade" id="modalNuevaIncidencia" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="fas fa-tools me-2"></i>Reportar Incidencia</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/mantenimiento/crear') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" class="form-control" required
                               placeholder="Ej: Fuga de agua en baño">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"
                                  placeholder="Describe el problema con detalle"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Prioridad</label>
                            <select name="prioridad" class="form-select">
                                <option value="Baja">Baja</option>
                                <option value="Media" selected>Media</option>
                                <option value="Alta">Alta</option>
                                <option value="Urgente">🔴 Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Habitación (opcional)</label>
                            <select name="idHabitacion" class="form-select">
                                <option value="">— Sin habitación —</option>
                                <?php foreach ($habitaciones as $h): ?>
                                    <option value="<?= $h['idHabitacion'] ?>">
                                        N° <?= htmlspecialchars($h['numero']) ?> (<?= htmlspecialchars($h['tipo']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Asignar a (opcional)</label>
                            <select name="idAsignado" class="form-select">
                                <option value="">— Sin asignar —</option>
                                <?php foreach ($personal as $p): ?>
                                    <option value="<?= $p['idUsuario'] ?>">
                                        <?= htmlspecialchars($p['nombre'] . ' ' . $p['paterno']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Reportar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal asignar -->
<div class="modal fade" id="modalAsignar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="fas fa-user-tag me-2"></i>Asignar Incidencia</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/mantenimiento/asignar') ?>">
                <input type="hidden" name="id" id="asignarId">
                <div class="modal-body">
                    <p>Incidencia: <strong id="asignarTitulo"></strong></p>
                    <label class="form-label fw-semibold">Asignar a</label>
                    <select name="idAsignado" class="form-select" required>
                        <option value="">— Sin asignar —</option>
                        <?php foreach ($personal as $p): ?>
                            <option value="<?= $p['idUsuario'] ?>">
                                <?= htmlspecialchars($p['nombre'] . ' ' . $p['paterno']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirAsignar(id, titulo) {
    document.getElementById('asignarId').value       = id;
    document.getElementById('asignarTitulo').textContent = titulo;
    new bootstrap.Modal(document.getElementById('modalAsignar')).show();
}
</script>