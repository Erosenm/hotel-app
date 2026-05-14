<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylehabitaciones/index.css') ?>">

<!-- ══ Header ══ -->
<div class="hi-header">
    <div class="hi-title">
        <i class="fas fa-bed"></i>
        <span>Gestión de Habitaciones</span>
    </div>
    <?php if ($_SESSION['usuario']['rol'] === 'Administrador'): ?>
    <a href="<?= url('admin/habitaciones/crear') ?>" class="hi-btn-primary">
        <i class="fas fa-plus"></i> Nueva Habitación
    </a>
    <?php endif; ?>
</div>

<!-- ══ Cards de estado ══ -->
<div class="hi-stats-row">
<?php
$estadosCfg = [
    'Disponible'    => ['color' => 'green',  'icon' => 'fa-check-circle',    'emoji' => ''],
    'Ocupada'       => ['color' => 'rose',   'icon' => 'fa-door-closed',     'emoji' => ''],
    'Reservada'     => ['color' => 'amber',  'icon' => 'fa-calendar-check',  'emoji' => ''],
    'Mantenimiento' => ['color' => 'violet', 'icon' => 'fa-wrench',          'emoji' => ''],
    'Limpieza'      => ['color' => 'cyan',   'icon' => 'fa-broom',           'emoji' => ''],
];
foreach ($estadosCfg as $est => $cfg):
    $count = count(array_filter($habitaciones, fn($h) => $h['estado'] === $est));
    $total = count($habitaciones);
    $pct   = $total > 0 ? round(($count / $total) * 100) : 0;
?>
<div class="hi-stat-card <?= $cfg['color'] ?>">
    <div class="hi-stat-top">
        <div class="hi-stat-icon"><i class="fas <?= $cfg['icon'] ?>"></i></div>
        <span class="hi-stat-emoji"><?= $cfg['emoji'] ?></span>
    </div>
    <div class="hi-stat-value"><?= $count ?></div>
    <div class="hi-stat-label"><?= $est ?></div>
    <div class="hi-stat-bar-track">
        <div class="hi-stat-bar-fill" style="width:<?= $pct ?>%"></div>
    </div>
    <div class="hi-stat-pct"><?= $pct ?>% del total</div>
</div>
<?php endforeach; ?>
</div>

<!-- ══ Tabla ══ -->
<div class="hi-table-card">
    <table class="hi-table">
        <thead>
            <tr>
                <th>Imagen</th>
                <th>N° Hab.</th>
                <th>Piso</th>
                <th>Tipo</th>
                <th>Precio/noche</th>
                <th>Estado</th>
                <th style="text-align:center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($habitaciones)): ?>
            <tr>
                <td colspan="7">
                    <div class="hi-empty">
                        <i class="fas fa-bed"></i>
                        <p>No hay habitaciones registradas</p>
                    </div>
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($habitaciones as $h):
                $colores = [
                    'Disponible'    => 'success',
                    'Ocupada'       => 'danger',
                    'Reservada'     => 'warning',
                    'Mantenimiento' => 'secondary',
                    'Limpieza'      => 'info',
                ];
                $color = $colores[$h['estado']] ?? 'secondary';
            ?>
            <tr>
                <td>
                    <?php if (!empty($h['imagen_principal'])): ?>
                        <img src="<?= asset($h['imagen_principal']) ?>"
                             alt="Hab. <?= htmlspecialchars($h['numero']) ?>"
                             class="hi-room-img">
                    <?php else: ?>
                        <div class="hi-room-img-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td><span class="hi-room-num"><?= htmlspecialchars($h['numero']) ?></span></td>
                <td><span class="hi-piso">Piso <?= htmlspecialchars($h['piso']) ?></span></td>
                <td><span class="hi-tipo-badge"><?= htmlspecialchars($h['tipo'] ?? 'Sin tipo') ?></span></td>
                <td><span class="hi-precio">Bs. <?= number_format($h['precio'] ?? 0, 2) ?></span></td>
                <td><span class="hi-estado-badge <?= $color ?>"><?= $h['estado'] ?></span></td>
                <td>
                    <div class="hi-actions">
                        <a href="<?= url('admin/habitaciones/editar?id=' . $h['idHabitacion']) ?>"
                           class="hi-action-btn edit" title="Editar habitación">
                            <i class="fas fa-pen"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="hi-table-footer">
        <i class="fas fa-layer-group"></i>
        Total: <strong><?= count($habitaciones) ?></strong> habitaciones registradas
    </div>
</div>