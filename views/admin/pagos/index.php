<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-credit-card me-2 text-primary"></i>Gestión de Pagos
    </h4>
    <a href="<?= url('admin/pagos/crear') ?>" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Registrar Pago
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-1"></i> <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-1"></i> <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php
// Comprobantes pendientes de revisión
$pendientesQR = array_filter($pagos, fn($p) => $p['estado_pago'] === 'Pendiente' && !empty($p['comprobante']));
?>

<?php if (!empty($pendientesQR)): ?>
<div class="alert alert-warning border-warning mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="fas fa-exclamation-triangle text-warning fs-5"></i>
        <strong>Comprobantes QR pendientes de revisión (<?= count($pendientesQR) ?>)</strong>
    </div>
    <div class="row g-3">
        <?php foreach ($pendientesQR as $p): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-warning shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-semibold small"><?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_paterno']) ?></div>
                            <div class="text-muted" style="font-size:.75rem;"><?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?></div>
                        </div>
                        <span class="fw-bold text-primary">Bs. <?= number_format($p['monto'], 2) ?></span>
                    </div>

                    <!-- Imagen del comprobante -->
                    <?php
                        $ext = strtolower(pathinfo($p['comprobante'], PATHINFO_EXTENSION));
                        $rutaPublica = asset($p['comprobante']);
                    ?>
                    <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                        <img src="<?= htmlspecialchars($rutaPublica) ?>"
                             alt="Comprobante"
                             class="img-fluid rounded mb-2"
                             style="max-height:140px; width:100%; object-fit:cover; cursor:pointer;"
                             onclick="verComprobante('<?= htmlspecialchars($rutaPublica) ?>')">
                    <?php elseif ($ext === 'pdf'): ?>
                        <a href="<?= htmlspecialchars($rutaPublica) ?>" target="_blank"
                           class="btn btn-outline-secondary btn-sm w-100 mb-2">
                            <i class="fas fa-file-pdf me-1"></i>Ver PDF del comprobante
                        </a>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <a href="<?= url('admin/pagos/estado') ?>?id=<?= $p['idPago'] ?>&estado=Pagado"
                           class="btn btn-success btn-sm flex-fill"
                           onclick="return confirm('¿Aprobar este pago de Bs. <?= number_format($p['monto'], 2) ?>?')">
                            <i class="fas fa-check me-1"></i>Aprobar
                        </a>
                        <a href="<?= url('admin/pagos/estado') ?>?id=<?= $p['idPago'] ?>&estado=Cancelado"
                           class="btn btn-danger btn-sm flex-fill"
                           onclick="return confirm('¿Rechazar este comprobante?')">
                            <i class="fas fa-times me-1"></i>Rechazar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
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
                        <th>Comprobante</th>
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
                        <td colspan="9" class="text-center text-muted py-4">
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
                                $icono  = $iconos[$p['metodo_pago']] ?? 'fa-circle';
                            ?>
                            <i class="fas <?= $icono ?> me-1 text-muted"></i>
                            <?= htmlspecialchars($p['metodo_pago'] ?? '-') ?>
                        </td>
                        <td>
                            <?php if (!empty($p['comprobante'])): ?>
                                <?php
                                    $ext2 = strtolower(pathinfo($p['comprobante'], PATHINFO_EXTENSION));
                                    $url2 = asset($p['comprobante']);
                                ?>
                                <?php if (in_array($ext2, ['jpg','jpeg','png'])): ?>
                                    <img src="<?= htmlspecialchars($url2) ?>"
                                         alt="Comprobante"
                                         style="width:44px;height:44px;object-fit:cover;border-radius:6px;cursor:pointer;border:1px solid #ddd;"
                                         onclick="verComprobante('<?= htmlspecialchars($url2) ?>')">
                                <?php else: ?>
                                    <a href="<?= htmlspecialchars($url2) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
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
                            <?php if ($p['estado_pago'] === 'Pendiente' && !empty($p['comprobante'])): ?>
                                <a href="<?= url('admin/pagos/estado') ?>?id=<?= $p['idPago'] ?>&estado=Pagado"
                                   class="btn btn-success btn-sm me-1"
                                   onclick="return confirm('¿Aprobar este pago?')">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="<?= url('admin/pagos/estado') ?>?id=<?= $p['idPago'] ?>&estado=Cancelado"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Rechazar este comprobante?')">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php else: ?>
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
                            <?php endif; ?>
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

<!-- Modal ver comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="fas fa-image me-2"></i>Comprobante de pago</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-2">
                <img id="imgComprobante" src="" alt="Comprobante" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<script>
function verComprobante(url) {
    document.getElementById('imgComprobante').src = url;
    new bootstrap.Modal(document.getElementById('modalComprobante')).show();
}
</script>