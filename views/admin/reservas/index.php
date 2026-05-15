<link rel="stylesheet" href="<?= url('public/css/cssAdmin/stylereserva/index.css') ?>">

<div class="res-header">
    <div class="res-title">
        <i class="fas fa-calendar-check"></i>
        <span>Gestión de Reservas</span>
    </div>
    <a href="<?= url('admin/reservas/crear') ?>" class="res-btn-primary">
        <i class="fas fa-plus"></i> Nueva Reserva
    </a>
</div>

<!-- STATS PREMIUM (con emojis y barra de progreso) -->
<div class="res-stats-row">
    <?php
    $totalReservas = $stats['total'];
    $statsConfig = [
        'total' => ['label' => 'Total', 'icon' => 'fa-calendar-alt', 'emoji' => '', 'color' => 'total', 'value' => $stats['total']],
        'pendientes' => ['label' => 'Pendientes', 'icon' => 'fa-clock', 'emoji' => '', 'color' => 'pendiente', 'value' => $stats['pendientes']],
        'confirmadas' => ['label' => 'Confirmadas', 'icon' => 'fa-check-circle', 'emoji' => '', 'color' => 'confirmada', 'value' => $stats['confirmadas']],
        'canceladas' => ['label' => 'Canceladas', 'icon' => 'fa-times-circle', 'emoji' => '', 'color' => 'cancelada', 'value' => $stats['canceladas']]
    ];
    
    foreach ($statsConfig as $key => $cfg):
        $count = $cfg['value'];
        $pct = $totalReservas > 0 ? round(($count / $totalReservas) * 100) : 0;
    ?>
    <div class="res-stat-card <?= $cfg['color'] ?>">
        <div class="res-stat-top">
            <div class="res-stat-icon"><i class="fas <?= $cfg['icon'] ?>"></i></div>
            <span class="res-stat-emoji"><?= $cfg['emoji'] ?></span>
        </div>
        <div class="res-stat-value"><?= $count ?></div>
        <div class="res-stat-label"><?= $cfg['label'] ?></div>
        <div class="res-stat-bar-track">
            <div class="res-stat-bar-fill" style="width:<?= $pct ?>%"></div>
        </div>
        <div class="res-stat-pct"><?= $pct ?>% del total</div>
    </div>
    <?php endforeach; ?>
</div>

<!-- TABLA PREMIUM -->
<div class="res-table-card">
    <div class="table-responsive">
        <table class="res-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Habitación</th>
                    <th>Tipo</th>
                    <th>Entrada</th>
                    <th>Salida</th>
                    <th>Noches</th>
                    <th>Total</th>
                    <th>Personas</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($reservas)): ?>
                <tr>
                    <td colspan="10">
                        <div class="res-empty">
                            <i class="fas fa-calendar-times"></i>
                            <p>No hay reservas registradas</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($reservas as $r): ?>
                <?php
                    $colorClass = match($r['estado_reserva']) {
                        'Pendiente' => 'pendiente',
                        'Confirmada' => 'confirmada',
                        'Cancelada' => 'cancelada',
                        default => 'pendiente'
                    };
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="client-initial">
                                <?= strtoupper(substr($r['cliente_nombre'] ?? '?', 0, 1)) ?>
                            </span>
                            <div>
                                <div class="client-name">
                                    <?= htmlspecialchars($r['cliente_nombre'] . ' ' . $r['cliente_paterno']) ?>
                                </div>
                                <div class="client-email">
                                    <?= htmlspecialchars($r['cliente_email'] ?? '') ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="room-number">N° <?= htmlspecialchars($r['habitacion_numero']) ?></div>
                        <div class="room-floor">Piso <?= $r['habitacion_piso'] ?></div>
                    </td>
                    <td>
                        <span class="room-type-badge">
                            <i class="fas fa-door-open"></i>
                            <?= htmlspecialchars($r['tipo_habitacion'] ?? '-') ?>
                        </span>
                    </td>
                    <td class="res-fecha"><i class="fas fa-calendar-alt"></i><?= date('d/m/Y', strtotime($r['fechaInicio'])) ?></td>
                    <td class="res-fecha"><i class="fas fa-calendar-alt"></i><?= date('d/m/Y', strtotime($r['fechaFin'])) ?></td>
                    <td><span class="noches-badge"><?= $r['noches'] ?> noches</span></td>
                    <td><strong class="total-amount">Bs. <?= number_format($r['total'] ?? 0, 2) ?></strong></td>
                    <td class="res-personas"><i class="fas fa-users"></i> <?= $r['cantidadPersonas'] ?></td>
                    <td>
                        <span class="res-estado-badge <?= $colorClass ?>">
                            <?= $r['estado_reserva'] ?>
                        </span>
                    </td>
                    <td>
                        <div class="res-actions">
                            <?php if ($r['estado_reserva'] === 'Pendiente'): ?>
                                <a href="<?= url('admin/reservas/checkin?id=' . $r['idReserva']) ?>"
                                   class="res-action-btn checkin"
                                   onclick="return confirm('¿Realizar check-in?')">
                                    <i class="fas fa-sign-in-alt"></i> Check-in
                                </a>
                                <a href="<?= url('admin/reservas/estado?id=' . $r['idReserva'] . '&estado=Cancelada') ?>"
                                   class="res-action-btn cancel"
                                   onclick="return confirm('¿Cancelar esta reserva?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php elseif ($r['estado_reserva'] === 'Confirmada'): ?>
                                <a href="<?= url('admin/reservas/checkout?id=' . $r['idReserva']) ?>"
                                   class="res-action-btn checkout"
                                   onclick="return confirm('¿Realizar check-out?')">
                                    <i class="fas fa-sign-out-alt"></i> Check-out
                                </a>
                                <button class="res-action-btn service"
                                        onclick="abrirServicios(<?= $r['idReserva'] ?>)">
                                    <i class="fas fa-concierge-bell"></i>
                                </button>
                                <a href="<?= url('admin/reservas/estado?id=' . $r['idReserva'] . '&estado=Cancelada') ?>"
                                   class="res-action-btn outline-cancel"
                                   onclick="return confirm('¿Cancelar?')">
                                    <i class="fas fa-ban"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="res-table-footer">
        <i class="fas fa-chart-line"></i>
        Total: <strong><?= count($reservas) ?></strong> reservas registradas
    </div>
</div>

<!-- MODAL PREMIUM -->
<div class="modal fade res-modal" id="modalServicio" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-concierge-bell me-2" style="color: var(--res-accent);"></i>
                    Agregar Servicio
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/reservas/servicio') ?>">
                <div class="modal-body">
                    <input type="hidden" name="idReserva" id="servicioIdReserva">
                    <div class="mb-3">
                        <label class="form-label">Servicio</label>
                        <select name="idServicio" class="form-select" required>
                            <option value="">— Seleccionar servicio —</option>
                            <?php foreach ($serviciosDisp as $sv): ?>
                                <option value="<?= $sv['idServicio'] ?>">
                                    <?= htmlspecialchars($sv['nombre']) ?> — Bs. <?= number_format($sv['precio'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="res-btn-primary" style="padding: 0.5rem 1.2rem;">Agregar servicio</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirServicios(idReserva) {
    document.getElementById('servicioIdReserva').value = idReserva;
    new bootstrap.Modal(document.getElementById('modalServicio')).show();
}
</script>