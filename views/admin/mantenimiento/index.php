<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylemantenimiento/index.css') ?>">

<div class="mant-header">
    <div class="mant-title">
        <i class="fas fa-tools"></i>
        <span>Mantenimiento</span>
    </div>
    <?php if (in_array($_SESSION['usuario']['rol'], ['Administrador','Recepcionista'])): ?>
    <button class="mant-btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaIncidencia">
        <i class="fas fa-plus"></i> Reportar incidencia
    </button>
    <?php endif; ?>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="mant-alert mant-alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="mant-alert mant-alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if ($stats['urgentes'] > 0): ?>
<div class="mant-alert mant-alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <div><strong><?= $stats['urgentes'] ?> incidencia(s) urgente(s)</strong> sin resolver. Atención inmediata requerida.</div>
</div>
<?php endif; ?>

<!-- STATS PREMIUM CON BARRAS Y EMOJIS -->
<?php
$totalIncidencias = $stats['total'];
$statsConfig = [
    'total' => ['label' => 'Total incidencias', 'icon' => 'fa-clipboard-list', 'emoji' => '', 'color' => 'total', 'value' => $stats['total']],
    'pendientes' => ['label' => 'Pendientes', 'icon' => 'fa-clock', 'emoji' => '', 'color' => 'pendientes', 'value' => $stats['pendientes']],
    'enproceso' => ['label' => 'En proceso', 'icon' => 'fa-spinner', 'emoji' => '', 'color' => 'enproceso', 'value' => $stats['en_proceso']],
    'resueltas' => ['label' => 'Resueltas', 'icon' => 'fa-check-circle', 'emoji' => '', 'color' => 'resueltas', 'value' => $stats['resueltas']]
];
?>

<div class="mant-stats-row">
    <?php foreach ($statsConfig as $key => $cfg):
        $pct = $totalIncidencias > 0 ? round(($cfg['value'] / $totalIncidencias) * 100) : 0;
    ?>
    <div class="mant-stat-card <?= $cfg['color'] ?>">
        <div class="mant-stat-top">
            <div class="mant-stat-icon"><i class="fas <?= $cfg['icon'] ?>"></i></div>
            <span class="mant-stat-emoji"><?= $cfg['emoji'] ?></span>
        </div>
        <div class="mant-stat-value"><?= $cfg['value'] ?></div>
        <div class="mant-stat-label"><?= $cfg['label'] ?></div>
        <div class="mant-stat-bar-track">
            <div class="mant-stat-bar-fill" style="width:<?= $pct ?>%"></div>
        </div>
        <div class="mant-stat-pct"><?= $pct ?>% del total</div>
    </div>
    <?php endforeach; ?>
</div>

<!-- KANBAN BOARD -->
<div class="mant-kanban">
    <?php
    $columnas = [
        'Pendiente' => ['color' => '#f59e0b', 'bg' => '#fffbeb', 'icon' => 'fa-clock'],
        'En proceso' => ['color' => '#06b6d4', 'bg' => '#ecfeff', 'icon' => 'fa-spinner'],
        'Resuelta' => ['color' => '#10b981', 'bg' => '#ecfdf5', 'icon' => 'fa-check-circle'],
    ];
    $prioridadColor = ['Baja'=>'baja', 'Media'=>'media', 'Alta'=>'alta', 'Urgente'=>'urgente'];

    foreach ($columnas as $estado => $cfg):
        $items = array_filter($incidencias, fn($i) => $i['estado'] === $estado);
    ?>
    <div class="mant-column">
        <div class="mant-column-header">
            <h6>
                <i class="fas <?= $cfg['icon'] ?>" style="color: <?= $cfg['color'] ?>;"></i>
                <?= $estado ?>
                <span class="mant-column-badge"><?= count($items) ?></span>
            </h6>
        </div>
        <div class="mant-column-body">
            <?php if (empty($items)): ?>
                <div class="mant-empty-column">
                    <i class="fas fa-inbox"></i>
                    <p>Sin incidencias</p>
                </div>
            <?php endif; ?>
            
            <?php foreach ($items as $inc):
                $pc = $prioridadColor[$inc['prioridad']] ?? 'baja';
            ?>
            <div class="mant-card">
                <div class="mant-card-header">
                    <span class="mant-card-title"><?= htmlspecialchars($inc['titulo']) ?></span>
                    <span class="mant-prioridad-badge <?= $pc ?>"><?= $inc['prioridad'] ?></span>
                </div>
                
                <?php if ($inc['habitacion_numero']): ?>
                <div class="mant-card-room">
                    <i class="fas fa-bed"></i> Hab. <?= $inc['habitacion_numero'] ?> — Piso <?= $inc['piso'] ?>
                </div>
                <?php endif; ?>
                
                <?php if ($inc['descripcion']): ?>
                <div class="mant-card-desc">
                    <?= htmlspecialchars(substr($inc['descripcion'], 0, 60)) ?>...
                </div>
                <?php endif; ?>
                
                <?php if ($inc['asignado_nombre']): ?>
                <div class="mant-card-assignee">
                    <i class="fas fa-user-cog"></i>
                    <?= htmlspecialchars($inc['asignado_nombre'] . ' ' . $inc['asignado_paterno']) ?>
                </div>
                <?php else: ?>
                <div class="mant-card-unassigned">
                    <i class="fas fa-user-slash"></i> Sin asignar
                </div>
                <?php endif; ?>
                
                <div class="mant-card-date">
                    <i class="far fa-calendar-alt"></i>
                    <?= date('d/m/Y H:i', strtotime($inc['fechaCreacion'])) ?>
                </div>

                <div class="mant-card-actions">
                    <?php if ($inc['estado'] === 'Pendiente'): ?>
                        <a href="<?= url('admin/mantenimiento/estado?id=' . $inc['idIncidencia'] . '&estado=En+proceso') ?>"
                           class="mant-btn-sm mant-btn-outline-info">
                            <i class="fas fa-play"></i> Iniciar
                        </a>
                    <?php elseif ($inc['estado'] === 'En proceso'): ?>
                        <a href="<?= url('admin/mantenimiento/estado?id=' . $inc['idIncidencia'] . '&estado=Resuelta') ?>"
                           class="mant-btn-sm mant-btn-success"
                           onclick="return confirm('¿Marcar como resuelta?\n\nLa incidencia se marcará como completada.')">
                            <i class="fas fa-check"></i> Resolver
                        </a>
                    <?php endif; ?>
                    
                    <?php if (in_array($_SESSION['usuario']['rol'], ['Administrador','Recepcionista']) && $inc['estado'] !== 'Resuelta'): ?>
                        <button class="mant-btn-sm mant-btn-outline-secondary"
                                onclick="abrirAsignar(<?= $inc['idIncidencia'] ?>, '<?= htmlspecialchars(addslashes($inc['titulo'])) ?>')">
                            <i class="fas fa-user-tag"></i> Asignar
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- MODAL NUEVA INCIDENCIA (mejorado) -->
<div class="modal fade mant-modal" id="modalNuevaIncidencia" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-tools"></i>
                    Reportar Incidencia
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/mantenimiento/crear') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-heading"></i> Título <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="titulo" class="form-control" required
                               placeholder="Ej: Fuga de agua en baño">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-align-left"></i> Descripción
                        </label>
                        <textarea name="descripcion" class="form-control" rows="3"
                                  placeholder="Describe el problema con detalle (incluye ubicación específica si es necesario)..."></textarea>
                        <small class="text-muted" style="font-size: 0.7rem;">
                            <i class="fas fa-info-circle"></i> Cuanto más detalle, más rápido se resolverá
                        </small>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-flag"></i> Prioridad
                            </label>
                            <select name="prioridad" class="form-select">
                                <option value="Baja"> Baja - Puede esperar</option>
                                <option value="Media" selected> Media - Atención normal</option>
                                <option value="Alta"> Alta - Atención prioritaria</option>
                                <option value="Urgente"> Urgente - Atención inmediata</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <i class="fas fa-bed"></i> Habitación
                            </label>
                            <select name="idHabitacion" class="form-select">
                                <option value="">— Sin habitación —</option>
                                <?php foreach ($habitaciones as $h): ?>
                                    <option value="<?= $h['idHabitacion'] ?>">
                                        N° <?= htmlspecialchars($h['numero']) ?> (<?= htmlspecialchars($h['tipo']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted" style="font-size: 0.7rem;">Opcional, si aplica a una habitación específica</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                <i class="fas fa-user-cog"></i> Asignar a
                            </label>
                            <select name="idAsignado" class="form-select">
                                <option value="">— Sin asignar —</option>
                                <?php foreach ($personal as $p): ?>
                                    <option value="<?= $p['idUsuario'] ?>">
                                        👤 <?= htmlspecialchars($p['nombre'] . ' ' . $p['paterno']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted" style="font-size: 0.7rem;">Puedes asignar más tarde si lo prefieres</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Reportar incidencia
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL ASIGNAR (mejorado) -->
<div class="modal fade mant-modal" id="modalAsignar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-user-tag"></i>
                    Asignar Incidencia
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/mantenimiento/asignar') ?>">
                <input type="hidden" name="id" id="asignarId">
                <div class="modal-body">
                    <div class="mant-modal-info">
                        <p><i class="fas fa-tools me-1"></i> Incidencia:</p>
                        <p><strong id="asignarTitulo" style="color: var(--mant-accent);"></strong></p>
                    </div>
                    
                    <label class="form-label">
                        <i class="fas fa-user"></i> Asignar a
                    </label>
                    <select name="idAsignado" class="form-select" required>
                        <option value="">— Sin asignar —</option>
                        <?php foreach ($personal as $p): ?>
                            <option value="<?= $p['idUsuario'] ?>">
                                👤 <?= htmlspecialchars($p['nombre'] . ' ' . $p['paterno']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted" style="font-size: 0.7rem; margin-top: 0.5rem; display: block;">
                        <i class="fas fa-info-circle"></i> La persona asignada recibirá notificación
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Asignar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirAsignar(id, titulo) {
    document.getElementById('asignarId').value = id;
    document.getElementById('asignarTitulo').textContent = titulo;
    new bootstrap.Modal(document.getElementById('modalAsignar')).show();
}
</script>