<link rel="stylesheet" href="<?= url('public/css/cssAdmin/stylereserva/index.css') ?>">

<div class="res-header">
    <h4 class="res-title">
        <i class="fas fa-calendar-check"></i>
        Gestión de Reservas
    </h4>

    <a href="<?= url('admin/reservas/crear') ?>" class="res-btn-primary">
        <i class="fas fa-plus"></i>
        Nueva Reserva
    </a>
</div>

<!-- STATS -->
<div class="res-stats-row">

    <?php
    $total = max($stats['total'], 1);

    $porcPend = ($stats['pendientes'] / $total) * 100;
    $porcConf = ($stats['confirmadas'] / $total) * 100;
    $porcCanc = ($stats['canceladas'] / $total) * 100;
    ?>

    <div class="res-stat-card total">
        <div class="res-stat-top">
            <div class="res-stat-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <span class="res-stat-emoji">📋</span>
        </div>

        <div class="res-stat-value"><?= $stats['total'] ?></div>
        <div class="res-stat-label">Total Reservas</div>

        <div class="res-stat-bar-track">
            <div class="res-stat-bar-fill" style="width:100%"></div>
        </div>

        <div class="res-stat-pct">100% del total</div>
    </div>

    <div class="res-stat-card pendiente">
        <div class="res-stat-top">
            <div class="res-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <span class="res-stat-emoji">⏳</span>
        </div>

        <div class="res-stat-value"><?= $stats['pendientes'] ?></div>
        <div class="res-stat-label">Pendientes</div>

        <div class="res-stat-bar-track">
            <div class="res-stat-bar-fill" style="width:<?= $porcPend ?>%"></div>
        </div>

        <div class="res-stat-pct"><?= round($porcPend) ?>% del total</div>
    </div>

    <div class="res-stat-card confirmada">
        <div class="res-stat-top">
            <div class="res-stat-icon">
                <i class="fas fa-check"></i>
            </div>
            <span class="res-stat-emoji">✅</span>
        </div>

        <div class="res-stat-value"><?= $stats['confirmadas'] ?></div>
        <div class="res-stat-label">Confirmadas</div>

        <div class="res-stat-bar-track">
            <div class="res-stat-bar-fill" style="width:<?= $porcConf ?>%"></div>
        </div>

        <div class="res-stat-pct"><?= round($porcConf) ?>% del total</div>
    </div>

    <div class="res-stat-card cancelada">
        <div class="res-stat-top">
            <div class="res-stat-icon">
                <i class="fas fa-times"></i>
            </div>
            <span class="res-stat-emoji">❌</span>
        </div>

        <div class="res-stat-value"><?= $stats['canceladas'] ?></div>
        <div class="res-stat-label">Canceladas</div>

        <div class="res-stat-bar-track">
            <div class="res-stat-bar-fill" style="width:<?= $porcCanc ?>%"></div>
        </div>

        <div class="res-stat-pct"><?= round($porcCanc) ?>% del total</div>
    </div>

</div>

<!-- TABLA -->
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
                    <th>Acciones</th>
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
                    $estadoClass = match($r['estado_reserva']) {
                        'Pendiente'  => 'pendiente',
                        'Confirmada' => 'confirmada',
                        'Cancelada'  => 'cancelada',
                        default       => 'pendiente'
                    };
                ?>

                <tr>

                    <!-- CLIENTE -->
                    <td>

                        <div class="d-flex align-items-center">

                            <div class="client-initial">
                                <?= strtoupper(substr($r['cliente_nombre'] ?? '?', 0, 1)) ?>
                            </div>

                            <div>

                                <div class="client-name">
                                    <?= htmlspecialchars($r['cliente_nombre'] . ' ' . $r['cliente_paterno']) ?>
                                </div>

                                <span class="client-email">
                                    <?= htmlspecialchars($r['cliente_email'] ?? '') ?>
                                </span>

                            </div>

                        </div>

                    </td>

                    <!-- HABITACIÓN -->
                    <td>

                        <span class="room-number">
                            N° <?= htmlspecialchars($r['habitacion_numero']) ?>
                        </span>

                        <span class="room-floor">
                            Piso <?= $r['habitacion_piso'] ?>
                        </span>

                    </td>

                    <!-- TIPO -->
                    <td>

                        <span class="room-type-badge">
                            <i class="fas fa-bed"></i>
                            <?= htmlspecialchars($r['tipo_habitacion'] ?? '-') ?>
                        </span>

                    </td>

                    <!-- ENTRADA -->
                    <td>

                        <div class="res-fecha">
                            <i class="fas fa-calendar-plus"></i>
                            <?= date('d/m/Y', strtotime($r['fechaInicio'])) ?>
                        </div>

                    </td>

                    <!-- SALIDA -->
                    <td>

                        <div class="res-fecha">
                            <i class="fas fa-calendar-minus"></i>
                            <?= date('d/m/Y', strtotime($r['fechaFin'])) ?>
                        </div>

                    </td>

                    <!-- NOCHES -->
                    <td>

                        <span class="noches-badge">
                            <?= $r['noches'] ?> noches
                        </span>

                    </td>

                    <!-- TOTAL -->
                    <td>

                        <span class="total-amount">
                            Bs. <?= number_format($r['total'] ?? 0, 2) ?>
                        </span>

                    </td>

                    <!-- PERSONAS -->
                    <td>

                        <div class="res-personas">
                            <i class="fas fa-users"></i>
                            <?= $r['cantidadPersonas'] ?>
                        </div>

                    </td>

                    <!-- ESTADO -->
                    <td>

                        <span class="res-estado-badge <?= $estadoClass ?>">
                            <?= $r['estado_reserva'] ?>
                        </span>

                    </td>

                    <!-- ACCIONES -->
                    <td>

                        <div class="res-actions">

                            <a href="<?= url('admin/reservas/detalle?id=' . $r['idReserva']) ?>"
                               class="res-action-btn outline-cancel"
                               title="Ver detalle">

                                <i class="fas fa-eye"></i>
                                Ver

                            </a>

                            <?php if ($r['estado_reserva'] === 'Pendiente'): ?>

                                <a href="<?= url('admin/reservas/checkin?id=' . $r['idReserva']) ?>"
                                   class="res-action-btn checkin"
                                   onclick="return confirm('¿Realizar check-in?')">

                                    <i class="fas fa-sign-in-alt"></i>
                                    Check-in

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

                                    <i class="fas fa-sign-out-alt"></i>
                                    Check-out

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
        <i class="fas fa-database"></i>
        Total:
        <strong><?= count($reservas) ?></strong>
        reservas registradas
    </div>

</div>

<!-- MODAL -->
<div class="modal fade res-modal" id="modalServicio" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h6 class="modal-title">
                    <i class="fas fa-concierge-bell me-2"></i>
                    Agregar Servicio
                </h6>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>

            <form method="POST"
                  action="<?= url('admin/reservas/servicio') ?>">

                <div class="modal-body">

                    <input type="hidden"
                           name="idReserva"
                           id="servicioIdReserva">

                    <div class="mb-3">

                        <label class="form-label">
                            Servicio
                        </label>

                        <select name="idServicio"
                                class="form-select"
                                required>

                            <option value="">
                                — Seleccionar servicio —
                            </option>

                            <?php
                            $serviciosDisp = $conn->query("SELECT * FROM servicio ORDER BY nombre")
                                                  ->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($serviciosDisp as $sv): ?>

                                <option value="<?= $sv['idServicio'] ?>">

                                    <?= htmlspecialchars($sv['nombre']) ?>
                                    — Bs. <?= number_format($sv['precio'], 2) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Cantidad
                        </label>

                        <input type="number"
                               name="cantidad"
                               class="form-control"
                               min="1"
                               value="1"
                               required>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        Agregar servicio

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
function abrirServicios(idReserva) {

    document.getElementById('servicioIdReserva').value = idReserva;

    new bootstrap.Modal(
        document.getElementById('modalServicio')
    ).show();
}
</script>