<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-credit-card me-2 text-primary"></i>Gestión de Pagos
    </h4>
    <a href="<?= url('admin/pagos/crear') ?>" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Registrar Pago
    </a>
</div>
 
<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
 
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
 
<!-- STATS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-secondary"><?= $stats['total'] ?></div>
            <div class="text-muted small">Total Pagos</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-success"><?= $stats['pagados'] ?></div>
            <div class="text-muted small">Pagados</div>
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
            <div class="fs-4 fw-bold text-primary">Bs. <?= number_format($stats['monto'], 2) ?></div>
            <div class="text-muted small">Monto Cobrado</div>
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
                        <th>Reserva</th>
                        <th>Monto</th>
                        <th>Método</th>
                        <th>Fecha</th>
                        <th>Recibo</th>
                        <th>Estado</th>
                        <?php if (($_SESSION['usuario']['rol'] ?? '') === 'Administrador'): ?>
                        <th class="text-end">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($pagos)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-receipt fa-2x mb-2 d-block"></i>
                            No hay pagos registrados
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pagos as $p):
                        $colores = [
                            'Pagado'      => 'success',
                            'Pendiente'   => 'warning',
                            'Cancelado'   => 'danger',
                            'Reembolsado' => 'info',
                            'Parcial'     => 'secondary',
                        ];
                        $color = $colores[$p['estado_pago']] ?? 'secondary';
                    ?>
                    <tr>
                        <td>
                            <span class="badge bg-primary me-1">
                                <?= strtoupper(substr($p['cliente_nombre'] ?? '?', 0, 1)) ?>
                            </span>
                            <?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_paterno']) ?>
                            <br>
                            <small class="text-muted"><?= htmlspecialchars($p['cliente_email'] ?? '') ?></small>
                        </td>
                        <td>
                            <small class="text-muted"><?= htmlspecialchars(substr($p['reserva_codigo'] ?? '', 0, 8)) ?>...</small>
                            <br>
                            <small><?= htmlspecialchars($p['fechaInicio'] ?? '') ?> → <?= htmlspecialchars($p['fechaFin'] ?? '') ?></small>
                        </td>
                        <td class="fw-bold">Bs. <?= number_format($p['monto'], 2) ?></td>
                        <td>
                            <?php
                                $iconos = ['Efectivo' => 'fa-money-bill-wave', 'Tarjeta' => 'fa-credit-card', 'QR' => 'fa-qrcode'];
                                $icono = $iconos[$p['metodo_pago']] ?? 'fa-circle';
                            ?>
                            <i class="fas <?= $icono ?> me-1 text-muted"></i>
                            <?= htmlspecialchars($p['metodo_pago'] ?? '-') ?>
                        </td>
                        <td>
                            <small><?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?></small>
                        </td>
                        <td>
                            <?php if ($p['recibo_numero']): ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-receipt me-1"></i><?= htmlspecialchars($p['recibo_numero']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?= $color ?>">
                                <?= htmlspecialchars($p['estado_pago'] ?? '-') ?>
                            </span>
                        </td>
                        <?php if (($_SESSION['usuario']['rol'] ?? '') === 'Administrador'): ?>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    Estado
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <?php foreach (['Pagado','Pendiente','Cancelado','Reembolsado','Parcial'] as $est): ?>
                                    <?php if ($est !== $p['estado_pago']): ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= url('admin/pagos/estado') ?>?id=<?= $p['idPago'] ?>&estado=<?= urlencode($est) ?>">
                                            <?= $est ?>
                                        </a>
                                    </li>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>