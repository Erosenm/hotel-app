
<?php
$reservas = $reservas ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-plus-circle me-2 text-primary"></i>Registrar Pago
    </h4>
    <a href="<?= url('admin/pagos') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Volver
    </a>
</div>
 
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
 
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?= url('admin/pagos/crear') ?>" id="formPago">
 
            <!-- Selección de Reserva -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Reserva <span class="text-danger">*</span></label>
                <select name="idReserva" class="form-select" id="selectReserva" required>
                    <option value="">— Seleccione una reserva —</option>
                    <?php foreach ($reservas as $r): ?>
                    <option value="<?= $r['idReserva'] ?>"
                        data-total="<?= $r['precioTotal'] ?>"
                        data-pagado="<?= $r['monto_pagado'] ?>"
                        <?= isset($preseleccionado) && $preseleccionado == $r['idReserva'] ? 'selected' : '' ?>"
                        data-pendiente="<?= max(0, $r['precioTotal'] - $r['monto_pagado']) ?>">
                        [<?= htmlspecialchars(substr($r['codigo'], 0, 8)) ?>]
                        <?= htmlspecialchars($r['cliente_nombre'] . ' ' . $r['cliente_paterno']) ?>
                        — Hab. <?= htmlspecialchars($r['habitacion_numero'] ?? '-') ?>
                        (<?= $r['fechaInicio'] ?> → <?= $r['fechaFin'] ?>)
                        | Bs. <?= number_format($r['precioTotal'], 2) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
 
            <!-- Info de la reserva seleccionada -->
            <div class="card bg-light border-0 mb-3 d-none" id="infoReserva">
                <div class="card-body py-2">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="small text-muted">Total Reserva</div>
                            <div class="fw-bold text-dark" id="infoTotal">—</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Ya Pagado</div>
                            <div class="fw-bold text-success" id="infoPagado">—</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Pendiente</div>
                            <div class="fw-bold text-danger" id="infoPendiente">—</div>
                        </div>
                    </div>