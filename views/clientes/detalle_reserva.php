
<?php
$reserva = $reserva ?? [];
$noches = $noches?? [];
$pendiente = $pendiente ?? [];
$totalServicios = $totalServicios ?? [];
$servicios = $servicios ?? [];
?>

<style>
.timeline-item { border-left: 2px solid #e9ecef; padding-left: 20px; position: relative; margin-bottom: 16px; }
.timeline-item::before { content:''; position:absolute; left:-7px; top:4px; width:12px; height:12px; border-radius:50%; background:#0f3460; border:2px solid #fff; box-shadow:0 0 0 2px #0f3460; }
.timeline-item.success::before { background:#10b981; box-shadow:0 0 0 2px #10b981; }
.timeline-item.warning::before { background:#f59e0b; box-shadow:0 0 0 2px #f59e0b; }
.timeline-item.danger::before  { background:#ef4444; box-shadow:0 0 0 2px #ef4444; }
</style>

<!-- Header -->
<div style="background:linear-gradient(135deg,#1a1a2e,#0f3460);padding:40px 20px 30px;color:#fff;">
    <div style="max-width:860px;margin:0 auto;">
        <a href="<?= url('cliente/reservas') ?>" style="color:#a0b4d0;font-size:.85rem;text-decoration:none;">
            <i class="fas fa-arrow-left me-2"></i>Mis reservas
        </a>
        <h2 class="fw-bold mt-2 mb-0">Detalle de Reserva</h2>
        <p style="color:#a0b4d0;">
            Habitación <?= htmlspecialchars($reserva['habitacion_numero']) ?> ·
            <?= date('d/m/Y', strtotime($reserva['fechaInicio'])) ?> →
            <?= date('d/m/Y', strtotime($reserva['fechaFin'])) ?>
        </p>
    </div>
</div>

<div style="max-width:860px;margin:0 auto;padding:30px 20px;">

<?php
$colores = ['Pendiente'=>'warning','Confirmada'=>'success','Cancelada'=>'danger','Completada'=>'primary','No show'=>'secondary'];
$badge   = $colores[$reserva['estado_reserva']] ?? 'secondary';
?>

<div class="row g-4">

    <!-- Columna izquierda -->
    <div class="col-lg-7">

        <!-- Info habitación -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <?php if (!empty($reserva['imagen'])): ?>
                    <img src="<?= asset($reserva['imagen']) ?>" class="w-100 rounded mb-3"
                         style="height:200px;object-fit:cover;">
                <?php endif; ?>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-bold mb-0">
                            Habitación N° <?= htmlspecialchars($reserva['habitacion_numero']) ?>
                            <span class="text-muted fw-normal fs-6">· <?= htmlspecialchars($reserva['tipo_habitacion']) ?></span>
                        </h5>
                        <p class="text-muted small mb-0">Piso <?= $reserva['piso'] ?></p>
                    </div>
                    <span class="badge bg-<?= $badge ?> fs-6"><?= htmlspecialchars($reserva['estado_reserva']) ?></span>
                </div>

                <hr>

                <div class="row g-3 text-center">
                    <div class="col-4">
                        <div class="text-muted small">Entrada</div>
                        <div class="fw-bold"><?= date('d/m/Y', strtotime($reserva['fechaInicio'])) ?></div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Salida</div>
                        <div class="fw-bold"><?= date('d/m/Y', strtotime($reserva['fechaFin'])) ?></div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Noches</div>
                        <div class="fw-bold"><?= $noches ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Personas</div>
                        <div class="fw-bold"><?= $reserva['cantidadPersonas'] ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Precio/noche</div>
                        <div class="fw-bold">Bs. <?= number_format($reserva['precioBase'], 2) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios agregados -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-semibold mb-0">
                    <i class="fas fa-concierge-bell me-2 text-info"></i>Servicios adicionales
                </h6>
                <?php if (in_array($reserva['estado_reserva'], ['Pendiente','Confirmada'])): ?>
                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalPedirServicio">
                    <i class="fas fa-plus me-1"></i>Solicitar servicio
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr><th>Servicio</th><th class="text-center">Cant.</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($servicios as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['nombre']) ?></td>
                        <td class="text-center"><?= $s['cantidad'] ?></td>
                        <td class="text-end fw-semibold">Bs. <?= number_format($s['subtotal'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="fw-semibold">Total servicios</td>
                            <td class="text-end fw-bold text-info">Bs. <?= number_format($totalServicios, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

<?php if (in_array($reserva['estado_reserva'], ['Pendiente','Confirmada'])): ?>
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-semibold mb-0">
                <i class="fas fa-concierge-bell me-2 text-info"></i>Servicios adicionales
            </h6>
            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalPedirServicio">
                <i class="fas fa-plus me-1"></i>Solicitar servicio
            </button>
        </div>
        <div class="card-body text-center text-muted py-4">
            <i class="fas fa-concierge-bell fa-2x mb-2 d-block text-muted opacity-50"></i>
            No tienes servicios adicionales en esta reserva.<br>
            <small>Puedes solicitar masajes, desayunos, lavandería y más.</small>
        </div>
    </div>
<?php endif; ?>

       

        <!-- Historial de pagos -->
        <?php if (!empty($pagos)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0">
                    <i class="fas fa-history me-2 text-primary"></i>Historial de pagos
                </h6>
            </div>
            <div class="card-body">
                <?php foreach ($pagos as $p):
                    $cls = ['Pagado'=>'success','Pendiente'=>'warning','Cancelado'=>'danger'];
                    $c   = $cls[$p['estado']] ?? 'secondary';
                ?>
                <div class="timeline-item <?= $c ?>">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">
                                Bs. <?= number_format($p['monto'], 2) ?>
                                <span class="badge bg-<?= $c ?> ms-2 small"><?= htmlspecialchars($p['estado']) ?></span>
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-credit-card me-1"></i><?= htmlspecialchars($p['metodo']) ?>
                                · <?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?>
                            </div>
                            <?php if ($p['recibo']): ?>
                                <div class="text-muted small">
                                    <i class="fas fa-receipt me-1"></i><?= htmlspecialchars($p['recibo']) ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($p['comprobante']) && $p['estado'] === 'Pendiente'): ?>
                                <div class="badge bg-warning text-dark mt-1">
                                    <i class="fas fa-clock me-1"></i>Comprobante en revisión
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- Columna derecha: resumen de cobro -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm" style="position:sticky;top:20px;">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="fas fa-calculator me-2 text-primary"></i>Resumen de cobro</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Habitación (<?= $noches ?> noches)</span>
                    <span>Bs. <?= number_format($noches * $reserva['precioBase'], 2) ?></span>
                </div>
                <?php if ($totalServicios > 0): ?>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Servicios adicionales</span>
                    <span>Bs. <?= number_format($totalServicios, 2) ?></span>
                </div>
                <?php endif; ?>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-semibold">Total reserva</span>
                    <span class="fw-semibold">Bs. <?= number_format($reserva['precioTotal'], 2) ?></span>
                </div>
                <?php if ($totalPagado > 0): ?>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Ya pagado</span>
                    <span>- Bs. <?= number_format($totalPagado, 2) ?></span>
                </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between pt-2 border-top">
                    <span class="fw-bold fs-5">Pendiente</span>
                    <span class="fw-bold fs-5 <?= $pendiente > 0 ? 'text-danger' : 'text-success' ?>">
                        Bs. <?= number_format($pendiente, 2) ?>
                    </span>
                </div>

                <?php if ($pendiente > 0 && in_array($reserva['estado_reserva'], ['Pendiente','Confirmada'])): ?>
                    <a href="<?= url('cliente/pagar?id=' . $reserva['idReserva']) ?>"
                       class="btn btn-success w-100 mt-3">
                        <i class="fas fa-credit-card me-2"></i>Pagar ahora
                    </a>
                <?php elseif ($pendiente == 0): ?>
                    <div class="alert alert-success mt-3 mb-0 py-2 text-center">
                        <i class="fas fa-check-circle me-1"></i>Pagado completamente
                    </div>
                <?php endif; ?>

                <?php if (in_array($reserva['estado_reserva'], ['Pendiente','Confirmada'])): ?>
                    <a href="<?= url('cliente/reservas/cancelar?id=' . $reserva['idReserva']) ?>"
                       class="btn btn-outline-danger w-100 mt-2"
                       onclick="return confirm('¿Cancelar esta reserva?')">
                        <i class="fas fa-times me-2"></i>Cancelar reserva
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
</div>

<!-- Modal solicitar servicio -->
<div class="modal fade" id="modalPedirServicio" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold">
                    <i class="fas fa-concierge-bell me-2 text-info"></i>Solicitar Servicio
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('cliente/reservas/servicio') ?>">
                <input type="hidden" name="idReserva" value="<?= $reserva['idReserva'] ?>">
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        El servicio será agregado a tu reserva y sumado al total a pagar.
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Servicio <span class="text-danger">*</span></label>
                        <select name="idServicio" id="selectServicio" class="form-select" required onchange="actualizarPrecio()">
                            <option value="">— Seleccionar servicio —</option>
                            <?php foreach ($serviciosDisp as $sv): ?>
                                <option value="<?= $sv['idServicio'] ?>"
                                        data-precio="<?= $sv['precio'] ?>">
                                    <?= htmlspecialchars($sv['nombre']) ?>
                                    — Bs. <?= number_format($sv['precio'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidadServicio"
                               class="form-control" min="1" max="10" value="1"
                               onchange="actualizarPrecio()">
                    </div>

                    <!-- Preview precio -->
                    <div id="previewPrecio" class="alert alert-info py-2 d-none">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal estimado:</span>
                            <strong id="txtSubtotal">Bs. 0.00</strong>
                        </div>
                    </div>

                    <div class="alert alert-warning py-2 small">
                        <i class="fas fa-info-circle me-1"></i>
                        El monto será añadido al total de tu reserva.
                        Un recepcionista confirmará la solicitud.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="fas fa-check me-1"></i>Solicitar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function actualizarPrecio() {
    const sel      = document.getElementById('selectServicio');
    const cant     = parseInt(document.getElementById('cantidadServicio').value) || 1;
    const precio   = parseFloat(sel.options[sel.selectedIndex]?.dataset.precio || 0);
    const preview  = document.getElementById('previewPrecio');
    const subtotal = document.getElementById('txtSubtotal');

    if (precio > 0) {
        preview.classList.remove('d-none');
        subtotal.textContent = 'Bs. ' + (precio * cant).toFixed(2);
    } else {
        preview.classList.add('d-none');
    }
}
</script>