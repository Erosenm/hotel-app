<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylelimpieza/index.css') ?>">

<div class="clean-header">
    <div class="clean-title">
        <i class="fas fa-broom"></i>
        <span>Control de Limpieza</span>
    </div>
    <button class="clean-btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaTarea">
        <i class="fas fa-plus"></i> Nueva tarea
    </button>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="clean-alert clean-alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="clean-alert clean-alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if ($stats['sucias'] > 0): ?>
<div class="clean-alert clean-alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    <div><strong><?= $stats['sucias'] ?> habitación(es) necesitan limpieza</strong> después de un check-out reciente.</div>
</div>
<?php endif; ?>

<!-- STATS PREMIUM CON BARRAS Y EMOJIS -->
<?php
$totalTareas = $stats['pendientes'] + $stats['en_proceso'] + $stats['completadas'];
$statsConfig = [
    'sucias' => ['label' => 'Necesitan limpieza', 'icon' => 'fa-broom', 'emoji' => '', 'color' => 'sucias', 'value' => $stats['sucias']],
    'pendientes' => ['label' => 'Tareas pendientes', 'icon' => 'fa-clock', 'emoji' => '', 'color' => 'pendientes', 'value' => $stats['pendientes']],
    'enproceso' => ['label' => 'En proceso', 'icon' => 'fa-spinner', 'emoji' => '', 'color' => 'enproceso', 'value' => $stats['en_proceso']],
    'completadas' => ['label' => 'Completadas', 'icon' => 'fa-check-circle', 'emoji' => '', 'color' => 'completadas', 'value' => $stats['completadas']]
];
?>

<div class="clean-stats-row">
    <?php foreach ($statsConfig as $key => $cfg):
        $pct = $totalTareas > 0 ? round(($cfg['value'] / $totalTareas) * 100) : 0;
    ?>
    <div class="clean-stat-card <?= $cfg['color'] ?>">
        <div class="clean-stat-top">
            <div class="clean-stat-icon"><i class="fas <?= $cfg['icon'] ?>"></i></div>
            <span class="clean-stat-emoji"><?= $cfg['emoji'] ?></span>
        </div>
        <div class="clean-stat-value"><?= $cfg['value'] ?></div>
        <div class="clean-stat-label"><?= $cfg['label'] ?></div>
        <div class="clean-stat-bar-track">
            <div class="clean-stat-bar-fill" style="width:<?= $pct ?>%"></div>
        </div>
        <div class="clean-stat-pct"><?= $pct ?>% del total</div>
    </div>
    <?php endforeach; ?>
</div>

<!-- KANBAN BOARD -->
<div class="clean-kanban">
    <?php
    $columnas = [
        'Pendiente' => ['color' => '#f59e0b', 'bg' => '#fffbeb', 'icon' => 'fa-clock'],
        'En proceso' => ['color' => '#06b6d4', 'bg' => '#ecfeff', 'icon' => 'fa-spinner'],
        'Completada' => ['color' => '#10b981', 'bg' => '#ecfdf5', 'icon' => 'fa-check-circle'],
    ];
    foreach ($columnas as $estado => $cfg):
        $tareasFiltradas = array_filter($tareas, fn($t) => $t['estado'] === $estado);
    ?>
    <div class="clean-column">
        <div class="clean-column-header">
            <h6>
                <i class="fas <?= $cfg['icon'] ?>" style="color: <?= $cfg['color'] ?>;"></i>
                <?= $estado ?>
                <span class="clean-column-badge"><?= count($tareasFiltradas) ?></span>
            </h6>
        </div>
        <div class="clean-column-body">
            <?php if (empty($tareasFiltradas)): ?>
                <div class="clean-empty-column">
                    <i class="fas fa-inbox"></i>
                    <p>Sin tareas</p>
                </div>
            <?php endif; ?>
            
            <?php foreach ($tareasFiltradas as $t): ?>
            <div class="clean-task">
                <div class="clean-task-header">
                    <span class="clean-task-room">N° <?= htmlspecialchars($t['habitacion_numero']) ?></span>
                    <span class="clean-task-floor">Piso <?= $t['piso'] ?></span>
                </div>
                <div class="clean-task-type">
                    <i class="fas fa-door-open"></i>
                    <?= htmlspecialchars($t['tipo_habitacion']) ?>
                </div>
                
                <?php if ($t['asignado_nombre']): ?>
                <div class="clean-task-user">
                    <i class="fas fa-user"></i>
                    <?= htmlspecialchars($t['asignado_nombre'] . ' ' . $t['asignado_paterno']) ?>
                </div>
                <?php endif; ?>
                
                <div class="clean-task-date">
                    <i class="far fa-calendar-alt"></i>
                    <?= date('d/m/Y H:i', strtotime($t['fechaAsignacion'])) ?>
                </div>
                
                <?php if ($t['observaciones']): ?>
                <div class="clean-task-obs">
                    <i class="fas fa-pen"></i> <?= htmlspecialchars($t['observaciones']) ?>
                </div>
                <?php endif; ?>
                
                <div class="clean-task-actions">
                    <?php if ($t['estado'] === 'Pendiente'): ?>
                        <a href="<?= url('admin/limpieza/estado?id=' . $t['idTarea'] . '&estado=En+proceso') ?>"
                           class="clean-btn-sm clean-btn-outline-info">
                            <i class="fas fa-play"></i> Iniciar
                        </a>
                    <?php elseif ($t['estado'] === 'En proceso'): ?>
                        <a href="<?= url('admin/limpieza/estado?id=' . $t['idTarea'] . '&estado=Completada') ?>"
                           class="clean-btn-sm clean-btn-success"
                           onclick="return confirm('¿Marcar como completada?\n\nLa habitación quedará disponible automáticamente.')">
                            <i class="fas fa-check"></i> Completar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- MODAL NUEVA TAREA -->
<div class="modal fade clean-modal" id="modalNuevaTarea" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-broom me-2" style="color: var(--clean-accent);"></i>
                    Nueva tarea de limpieza
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/limpieza/crear') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Habitación *</label>
                        <select name="idHabitacion" class="form-select" required>
                            <option value="">— Seleccionar habitación —</option>
                            <?php foreach ($habitacionesSucias as $h): ?>
                                <option value="<?= $h['idHabitacion'] ?>">
                                    N° <?= htmlspecialchars($h['numero']) ?> — Piso <?= $h['piso'] ?>
                                    (<?= htmlspecialchars($h['tipo']) ?>)
                                    <?= $h['estado_hab'] === 'Limpieza' ? '🚿 Prioritario' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted"> Habitaciones con estado "Limpieza" son prioritarias</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Asignar a personal</label>
                        <select name="idUsuario" class="form-select">
                            <option value="">— Sin asignar —</option>
                            <?php foreach ($personal as $p): ?>
                                <option value="<?= $p['idUsuario'] ?>">
                                    👤 <?= htmlspecialchars($p['nombre'] . ' ' . $p['paterno']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-2">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="2" 
                                  placeholder="Ej: Cambiar sábanas, limpiar baño, reponer amenities..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Crear tarea
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>