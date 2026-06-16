<?php
$reserva = $reserva ?? [];
$noches = $noches?? [];
$pendiente = $pendiente ?? [];
$totalServicios = $totalServicios ?? [];
$servicios = $servicios ?? [];
$serviciosDisp = $serviciosDisp ?? [];
?>

<link rel="stylesheet" href="<?= asset('css/cssCliente/clientedetallereserva.css') ?>">

<!-- Hero -->
<div class="detalle-res-hero">
    <div class="detalle-res-hero-content">
        <a href="<?= url('cliente/reservas') ?>" class="detalle-res-hero-back">
            <i class="fas fa-arrow-left me-2"></i>Mis reservas
        </a>
        <h2>Detalle de Reserva</h2>
        <p>
            Habitación <?= htmlspecialchars($reserva['habitacion_numero']) ?> ·
            <?= date('d/m/Y', strtotime($reserva['fechaInicio'])) ?> →
            <?= date('d/m/Y', strtotime($reserva['fechaFin'])) ?>
        </p>
    </div>
</div>

<div class="detalle-res-main">

<?php
$colores = ['Pendiente'=>'pendiente','Confirmada'=>'confirmada','Cancelada'=>'cancelada','Completada'=>'completada','No show'=>'secondary'];
$badge   = $colores[$reserva['estado_reserva']] ?? 'secondary';
?>

<div class="row g-4">

    <!-- Columna izquierda -->
    <div class="col-lg-7">

        <!-- Info habitación -->
        <div class="detalle-res-card">
            <div class="detalle-res-card-body">
                <?php if (!empty($reserva['imagen'])): ?>
                    <img src="<?= asset($reserva['imagen']) ?>" class="detalle-res-hab-img" alt="Habitación">
                <?php endif; ?>
                
                <div class="detalle-res-hab-header">
                    <div>
                        <h5 class="detalle-res-hab-nombre">
                            Habitación N° <?= htmlspecialchars($reserva['habitacion_numero']) ?>
                            <small>· <?= htmlspecialchars($reserva['tipo_habitacion']) ?></small>
                        </h5>
                        <p class="detalle-res-hab-piso">Piso <?= $reserva['piso'] ?></p>
                    </div>
                    <span class="detalle-res-hab-badge <?= $badge ?>"><?= htmlspecialchars($reserva['estado_reserva']) ?></span>
                </div>

                <div class="detalle-res-fechas">
                    <div class="detalle-res-fecha-item">
                        <div class="label">Entrada</div>
                        <div class="valor"><?= date('d/m/Y', strtotime($reserva['fechaInicio'])) ?></div>
                    </div>
                    <div class="detalle-res-fecha-item">
                        <div class="label">Salida</div>
                        <div class="valor"><?= date('d/m/Y', strtotime($reserva['fechaFin'])) ?></div>
                    </div>
                    <div class="detalle-res-fecha-item">
                        <div class="label">Noches</div>
                        <div class="valor"><?= $noches ?></div>
                    </div>
                    <div class="detalle-res-fecha-item">
                        <div class="label">Personas</div>
                        <div class="valor"><?= $reserva['cantidadPersonas'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servicios adicionales -->
        <div class="detalle-res-card">
            <div class="detalle-res-card-header">
                <h6><i class="fas fa-concierge-bell"></i> Servicios adicionales</h6>
                <?php if (in_array($reserva['estado_reserva'], ['Pendiente','Confirmada'])): ?>
                <button class="detalle-res-btn-dark" data-bs-toggle="modal" data-bs-target="#modalPedirServicio">
                    <i class="fas fa-plus me-1"></i>Solicitar servicio
                </button>
                <?php endif; ?>
            </div>
            <div class="detalle-res-card-body">
                <?php if (!empty($servicios)): ?>
                <table class="detalle-res-tabla">
                    <thead>
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
                    <tfoot>
                        <tr>
                            <td colspan="2">Total servicios</td>
                            <td class="text-end" style="color: var(--detalle-res-info);">Bs. <?= number_format($totalServicios, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
                <?php else: ?>
                <div class="text-center text-muted py-3">
                    <i class="fas fa-concierge-bell fa-2x mb-2 d-block opacity-25"></i>
                    No tienes servicios adicionales en esta reserva.
                    <br><small>Puedes solicitar masajes, desayunos, lavandería y más.</small>
                    <?php if (in_array($reserva['estado_reserva'], ['Pendiente','Confirmada'])): ?>
                    <div class="mt-2">
                        <button class="detalle-res-btn-dark" data-bs-toggle="modal" data-bs-target="#modalPedirServicio">
                            <i class="fas fa-plus me-1"></i>Añadir servicio
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Historial de pagos -->
        <?php if (!empty($pagos)): ?>
        <div class="detalle-res-card">
            <div class="detalle-res-card-header">
                <h6><i class="fas fa-history"></i> Historial de pagos</h6>
            </div>
            <div class="detalle-res-card-body">
                <div class="detalle-res-timeline">
                    <?php foreach ($pagos as $p):
                        $cls = ['Pagado'=>'success','Pendiente'=>'warning','Cancelado'=>'danger'];
                        $c   = $cls[$p['estado']] ?? 'secondary';
                    ?>
                    <div class="detalle-res-timeline-item <?= $c ?>">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                            <div>
                                <div class="monto">
                                    Bs. <?= number_format($p['monto'], 2) ?>
                                    <span class="detalle-res-hab-badge <?= $c ?>" style="font-size:0.6rem;"><?= htmlspecialchars($p['estado']) ?></span>
                                </div>
                                <div class="meta">
                                    <i class="fas fa-credit-card me-1"></i><?= htmlspecialchars($p['metodo']) ?>
                                    · <?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?>
                                </div>
                                <?php if ($p['recibo']): ?>
                                    <div class="meta">
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
        </div>
        <?php endif; ?>

    </div>

    <!-- Columna derecha: resumen de cobro -->
    <div class="col-lg-5">
        <div class="detalle-res-card detalle-res-sticky">
            <div class="detalle-res-card-header">
                <h6><i class="fas fa-calculator"></i> Resumen de cobro</h6>
            </div>
            <div class="detalle-res-card-body">
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
                       class="detalle-res-btn-dark w-100 mt-3" style="text-align:center;padding:0.8rem;">
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

<!-- Decoración final -->
<div class="detalle-res-deco">
    <span class="detalle-res-deco-line">
        <i class="fas fa-hotel"></i>
        Hotel Real Plaza & Convention Center
        <i class="fas fa-hotel"></i>
    </span>
</div>

</div>

<!-- MODAL SOLICITAR SERVICIO -->
<div class="modal fade detalle-res-modal" id="modalPedirServicio" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="fas fa-concierge-bell"></i>Solicitar Servicio Adicional
                    </h5>
                    <p>Selecciona el servicio que deseas agregar a tu reserva</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="<?= url('cliente/reservas/servicio') ?>" id="formServicio">
                <input type="hidden" name="idReserva" value="<?= $reserva['idReserva'] ?>">
                <input type="hidden" name="idServicio" id="hiddenIdServicio" required>

                <div class="modal-body">
                    <?php if (empty($serviciosDisp)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2 d-block opacity-25"></i>
                            No hay servicios disponibles en este momento.
                        </div>
                    <?php else: ?>

                    <div class="detalle-res-modal-search">
                        <i class="fas fa-search"></i>
                        <input type="text" id="buscadorServicio" placeholder="Buscar servicio..." oninput="filtrarServicios()">
                    </div>

                    <div id="listaServicios">
                        <?php foreach ($serviciosDisp as $sv): ?>
                        <div class="detalle-res-servicio-card"
                             data-id="<?= $sv['idServicio'] ?>"
                             data-precio="<?= $sv['precio'] ?>"
                             data-nombre="<?= strtolower(htmlspecialchars($sv['nombre'])) ?>"
                             onclick="seleccionarServicio(this)">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:38px;height:38px;background:#e0f8fd;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                        <i class="fas fa-star text-info"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:.95rem;"><?= htmlspecialchars($sv['nombre']) ?></div>
                                        <div class="text-muted" style="font-size:.78rem;">Por unidad</div>
                                    </div>
                                </div>
                                <span class="precio-badge">Bs. <?= number_format($sv['precio'], 2) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <p id="sinResultados" class="text-center text-muted d-none py-2">
                        <i class="fas fa-search me-1"></i>Sin resultados
                    </p>

                    <div id="detalleSeleccion" class="d-none mt-3">
                        <hr>
                        <div class="row g-3 align-items-center">
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Cantidad</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(-1)">−</button>
                                    <input type="number" name="cantidad" id="cantidadServicio"
                                           class="form-control text-center fw-bold" min="1" max="10" value="1"
                                           oninput="actualizarPrecio()">
                                    <button type="button" class="btn btn-outline-secondary" onclick="cambiarCantidad(1)">+</button>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Subtotal estimado</label>
                                <div class="alert alert-info mb-0 py-2 text-center">
                                    <strong id="txtSubtotal" class="fs-5">Bs. 0.00</strong>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-warning py-2 small mt-3 mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            El monto será añadido al total de tu reserva.
                            Un recepcionista confirmará la solicitud.
                        </div>
                    </div>

                    <?php endif; ?>
                </div>

                <?php if (!empty($serviciosDisp)): ?>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="detalle-res-btn-dark px-4" id="btnSolicitar" disabled>
                        <i class="fas fa-check me-1"></i>Solicitar servicio
                    </button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
let precioSeleccionado = 0;

function seleccionarServicio(card) {
    document.querySelectorAll('.detalle-res-servicio-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    const id = card.dataset.id;
    const precio = parseFloat(card.dataset.precio);
    document.getElementById('hiddenIdServicio').value = id;
    precioSeleccionado = precio;
    document.getElementById('detalleSeleccion').classList.remove('d-none');
    document.getElementById('btnSolicitar').removeAttribute('disabled');
    document.getElementById('cantidadServicio').value = 1;
    actualizarPrecio();
}

function actualizarPrecio() {
    const cant = parseInt(document.getElementById('cantidadServicio').value) || 1;
    document.getElementById('txtSubtotal').textContent = 'Bs. ' + (precioSeleccionado * cant).toFixed(2);
}

function cambiarCantidad(delta) {
    const input = document.getElementById('cantidadServicio');
    const val = Math.min(10, Math.max(1, (parseInt(input.value) || 1) + delta));
    input.value = val;
    actualizarPrecio();
}

function filtrarServicios() {
    const q = document.getElementById('buscadorServicio').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.detalle-res-servicio-card');
    let visibles = 0;
    cards.forEach(c => {
        const match = c.dataset.nombre.includes(q);
        c.style.display = match ? '' : 'none';
        if (match) visibles++;
    });
    document.getElementById('sinResultados').classList.toggle('d-none', visibles > 0);
}

document.getElementById('modalPedirServicio').addEventListener('hidden.bs.modal', function() {
    document.querySelectorAll('.detalle-res-servicio-card').forEach(c => c.classList.remove('selected'));
    document.getElementById('hiddenIdServicio').value = '';
    document.getElementById('detalleSeleccion').classList.add('d-none');
    document.getElementById('btnSolicitar').setAttribute('disabled', true);
    document.getElementById('buscadorServicio').value = '';
    filtrarServicios();
    document.getElementById('cantidadServicio').value = 1;
    precioSeleccionado = 0;
});
</script>