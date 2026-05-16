<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-calendar-check me-2 text-primary"></i>Detalle de Reserva #<?= $reserva['idReserva'] ?>
    </h4>
    <a href="<?= url('admin/reservas') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Volver
    </a>
</div>

<?php
$colores = ['Pendiente'=>'warning','Confirmada'=>'success','Cancelada'=>'danger','Completada'=>'primary','No show'=>'secondary'];
$badge   = $colores[$reserva['estado_reserva']] ?? 'secondary';
$noches  = (new DateTime($reserva['fechaInicio']))->diff(new DateTime($reserva['fechaFin']))->days;
?>

<div class="row g-4">

    <!-- Info principal -->
    <div class="col-lg-8">

        <!-- Datos reserva -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Información general</h6>
                <span class="badge bg-<?= $badge ?> fs-6"><?= htmlspecialchars($reserva['estado_reserva']) ?></span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="text-muted small">Cliente</div>
                        <div class="fw-semibold"><?= htmlspecialchars($reserva['cliente_nombre'] . ' ' . $reserva['cliente_paterno']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($reserva['cliente_email'] ?? '') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Habitación</div>
                        <div class="fw-semibold">N° <?= htmlspecialchars($reserva['habitacion_numero']) ?> — Piso <?= $reserva['habitacion_piso'] ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($reserva['tipo_habitacion']) ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Check-in</div>
                        <div class="fw-semibold"><?= date('d/m/Y', strtotime($reserva['fechaInicio'])) ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Check-out</div>
                        <div class="fw-semibold"><?= date('d/m/Y', strtotime($reserva['fechaFin'])) ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Noches</div>
                        <div class="fw-semibold"><?= $noches ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Personas</div>
                        <div class="fw-semibold"><?= $reserva['cantidadPersonas'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios -->
        <?php if (!empty($servicios)): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="fas fa-concierge-bell me-2 text-info"></i>Servicios adicionales</h6>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Servicio</th><th class="text-center">Cantidad</th><th class="text-end">Precio unit.</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($servicios as $s): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($s['nombre']) ?></td>
                        <td class="text-center"><?= $s['cantidad'] ?></td>
                        <td class="text-end">Bs. <?= number_format($s['precioUnitario'], 2) ?></td>
                        <td class="text-end fw-bold">Bs. <?= number_format($s['cantidad'] * $s['precioUnitario'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="fw-semibold text-end">Total servicios</td>
                            <td class="text-end fw-bold text-info">Bs. <?= number_format($totalServicios, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Agregar servicio -->
        <?php if (in_array($reserva['estado_reserva'], ['Confirmada', 'Pendiente'])): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="fas fa-plus me-2 text-success"></i>Agregar servicio</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('admin/reservas/servicio') ?>" class="row g-2 align-items-end">
                    <input type="hidden" name="idReserva" value="<?= $reserva['idReserva'] ?>">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Servicio</label>
                        <select name="idServicio" class="form-select" required>
                            <option value="">— Seleccionar —</option>
                            <?php foreach ($serviciosDisp as $sv): ?>
                                <option value="<?= $sv['idServicio'] ?>">
                                    <?= htmlspecialchars($sv['nombre']) ?> — Bs. <?= number_format($sv['precio'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-plus me-1"></i>Agregar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pagos -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="fas fa-credit-card me-2 text-primary"></i>Pagos registrados</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pagos)): ?>
                    <div class="text-center text-muted py-4">Sin pagos registrados</div>
                <?php else: ?>
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Método</th><th>Monto</th><th>Comprobante</th><th>Recibo</th><th>Estado</th><th>Fecha</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pagos as $p):
                        $pc = ['Pagado'=>'success','Pendiente'=>'warning','Cancelado'=>'danger'];
                        $pc = $pc[$p['estado']] ?? 'secondary';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($p['metodo']) ?></td>
                        <td class="fw-bold">Bs. <?= number_format($p['monto'], 2) ?></td>
                        <td>
                            <?php if (!empty($p['comprobante'])): ?>
                                <?php $ext = strtolower(pathinfo($p['comprobante'], PATHINFO_EXTENSION)); ?>
                                <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                                    <img src="<?= asset($p['comprobante']) ?>"
                                         style="width:44px;height:44px;object-fit:cover;border-radius:6px;cursor:pointer;"
                                         onclick="verImg('<?= asset($p['comprobante']) ?>')">
                                <?php else: ?>
                                    <a href="<?= asset($p['comprobante']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td><small class="text-muted"><?= htmlspecialchars($p['recibo'] ?? '—') ?></small></td>
                        <td><span class="badge bg-<?= $pc ?>"><?= htmlspecialchars($p['estado']) ?></span></td>
                        <td><small><?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Resumen derecha -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4" style="position:sticky;top:20px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="fas fa-calculator me-2 text-primary"></i>Resumen de cobro</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Habitación (<?= $noches ?> noches)</span>
                    <span>Bs. <?= number_format($reserva['precioTotal'] - $totalServicios, 2) ?></span>
                </div>
                <?php if ($totalServicios > 0): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Servicios</span>
                    <span>Bs. <?= number_format($totalServicios, 2) ?></span>
                </div>
                <?php endif; ?>
                <hr>
                <div class="d-flex justify-content-between mb-2 fw-semibold">
                    <span>Total reserva</span>
                    <span>Bs. <?= number_format($reserva['precioTotal'], 2) ?></span>
                </div>
                <?php if ($totalPagado > 0): ?>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Pagado</span>
                    <span>- Bs. <?= number_format($totalPagado, 2) ?></span>
                </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between pt-2 border-top fw-bold fs-5">
                    <span>Pendiente</span>
                    <span class="<?= $pendiente > 0 ? 'text-danger' : 'text-success' ?>">
                        Bs. <?= number_format($pendiente, 2) ?>
                    </span>
                </div>

                <?php if ($pendiente == 0): ?>
                    <div class="alert alert-success mt-3 mb-0 py-2 text-center">
                        <i class="fas fa-check-circle me-1"></i>Pagado completamente
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acciones rápidas -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="fas fa-bolt me-2 text-warning"></i>Acciones</h6>
            </div>
            <div class="card-body d-grid gap-2">
                <?php if ($reserva['estado_reserva'] === 'Pendiente'): ?>
                    <a href="<?= url('admin/reservas/checkin?id=' . $reserva['idReserva']) ?>"
                       class="btn btn-success" onclick="return confirm('¿Realizar check-in?')">
                        <i class="fas fa-sign-in-alt me-2"></i>Check-in
                    </a>
                <?php elseif ($reserva['estado_reserva'] === 'Confirmada'): ?>
                    <a href="<?= url('admin/reservas/checkout?id=' . $reserva['idReserva']) ?>"
                       class="btn btn-primary" onclick="return confirm('¿Realizar check-out?')">
                        <i class="fas fa-sign-out-alt me-2"></i>Check-out
                    </a>
                <?php endif; ?>
                <?php if (in_array($reserva['estado_reserva'], ['Pendiente','Confirmada'])): ?>
                    <a href="<?= url('admin/reservas/estado?id=' . $reserva['idReserva'] . '&estado=Cancelada') ?>"
                       class="btn btn-outline-danger" onclick="return confirm('¿Cancelar esta reserva?')">
                        <i class="fas fa-ban me-2"></i>Cancelar reserva
                    </a>
                <?php endif; ?>
                <a href="<?= url('admin/pagos/crear?idReserva=' . $reserva['idReserva']) ?>"
                   class="btn btn-outline-primary">
                    <i class="fas fa-plus me-2"></i>Registrar pago
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal imagen comprobante -->
<div class="modal fade" id="modalImg" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-semibold">Comprobante</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="imgModal" src="" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>
<script>
function verImg(url) {
    document.getElementById('imgModal').src = url;
    new bootstrap.Modal(document.getElementById('modalImg')).show();
}
</script>