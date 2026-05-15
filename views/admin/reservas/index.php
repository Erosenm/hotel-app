<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-calendar-check me-2 text-primary"></i>Gestión de Reservas
    </h4>
    <a href="<?= url('admin/reservas/crear') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Nueva Reserva
    </a>
</div>

<!-- STATS -->
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
            <div class="fs-4 fw-bold text-success"><?= $stats['confirmadas'] ?></div>
            <div class="text-muted small">Confirmadas</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-danger"><?= $stats['canceladas'] ?></div>
            <div class="text-muted small">Canceladas</div>
        </div>
    </div>
</div>

<!-- TABLA -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
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
                        <td colspan="10" class="text-center text-muted py-4">
                            <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                            No hay reservas registradas
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($reservas as $r): ?>
                    <?php
                        $colores = [
                            'Pendiente'  => 'warning',
                            'Confirmada' => 'success',
                            'Cancelada'  => 'danger'
                        ];
                        $color = $colores[$r['estado_reserva']] ?? 'secondary';
                    ?>
                    <tr>
                        <td>
                            <span class="badge bg-primary me-1">
                                <?= strtoupper(substr($r['cliente_nombre'] ?? '?', 0, 1)) ?>
                            </span>
                            <?= htmlspecialchars($r['cliente_nombre'] . ' ' . $r['cliente_paterno']) ?>
                            <br>
                            <small class="text-muted"><?= htmlspecialchars($r['cliente_email'] ?? '') ?></small>
                        </td>
                        <td>
                            <strong>N° <?= htmlspecialchars($r['habitacion_numero']) ?></strong>
                            <br>
                            <small class="text-muted">Piso <?= $r['habitacion_piso'] ?></small>
                        </td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <?= htmlspecialchars($r['tipo_habitacion'] ?? '-') ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($r['fechaInicio'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($r['fechaFin'])) ?></td>
                        <td class="text-center"><?= $r['noches'] ?></td>
                        <td><strong>Bs. <?= number_format($r['total'] ?? 0, 2) ?></strong></td>
                        <td class="text-center"><?= $r['cantidadPersonas'] ?></td>
                        <td>
                            <span class="badge bg-<?= $color ?>">
                                <?= $r['estado_reserva'] ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <?php if ($r['estado_reserva'] === 'Pendiente'): ?>
                                <a href="<?= url('admin/reservas/checkin?id=' . $r['idReserva']) ?>"
                                   class="btn btn-sm btn-success mb-1" title="Check-in"
                                   onclick="return confirm('¿Realizar check-in?')">
                                    <i class="fas fa-sign-in-alt"></i> Check-in
                                </a>
                                <a href="<?= url('admin/reservas/estado?id=' . $r['idReserva'] . '&estado=Cancelada') ?>"
                                   class="btn btn-sm btn-danger mb-1" title="Cancelar"
                                   onclick="return confirm('¿Cancelar esta reserva?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php elseif ($r['estado_reserva'] === 'Confirmada'): ?>
                                <a href="<?= url('admin/reservas/checkout?id=' . $r['idReserva']) ?>"
                                   class="btn btn-sm btn-primary mb-1" title="Check-out"
                                   onclick="return confirm('¿Realizar check-out?')">
                                    <i class="fas fa-sign-out-alt"></i> Check-out
                                </a>
                                <button class="btn btn-sm btn-outline-info mb-1"
                                        onclick="abrirServicios(<?= $r['idReserva'] ?>)" title="Agregar servicio">
                                    <i class="fas fa-concierge-bell"></i>
                                </button>
                                <a href="<?= url('admin/reservas/estado?id=' . $r['idReserva'] . '&estado=Cancelada') ?>"
                                   class="btn btn-sm btn-outline-danger mb-1"
                                   onclick="return confirm('¿Cancelar?')">
                                    <i class="fas fa-ban"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0">
        <small class="text-muted">Total: <?= count($reservas) ?> reservas</small>
    </div>
</div>
<!-- Modal agregar servicio -->
<div class="modal fade" id="modalServicio" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="fas fa-concierge-bell me-2"></i>Agregar Servicio</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/reservas/servicio') ?>">
                <div class="modal-body">
                    <input type="hidden" name="idReserva" id="servicioIdReserva">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Servicio</label>
                        <select name="idServicio" class="form-select" required>
                            <option value="">— Seleccionar servicio —</option>
                            <?php
                            $serviciosDisp = $conn->query("SELECT * FROM servicio ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($serviciosDisp as $sv): ?>
                                <option value="<?= $sv['idServicio'] ?>">
                                    <?= htmlspecialchars($sv['nombre']) ?> — Bs. <?= number_format($sv['precio'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar servicio</button>
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