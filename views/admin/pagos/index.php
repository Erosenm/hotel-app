<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylepagos/index.css') ?>">
<div class="pagos-header">
    <div class="pagos-title">
        <i class="fas fa-credit-card"></i>
        <span>Gestión de Pagos</span>
    </div>
    <a href="<?= url('admin/pagos/crear') ?>" class="pagos-btn-primary">
        <i class="fas fa-plus"></i> Registrar Pago
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="pagos-alert pagos-alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i> <?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="pagos-alert pagos-alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php
$pendientesQR = array_filter($pagos, fn($p) => $p['estado_pago'] === 'Pendiente' && !empty($p['comprobante']));
?>

<?php if (!empty($pendientesQR)): ?>
<div class="pagos-pendientes-card">
    <div class="pagos-pendientes-header">
        <i class="fas fa-exclamation-triangle"></i>
        <strong>Comprobantes QR pendientes de revisión (<?= count($pendientesQR) ?>)</strong>
    </div>
    <div class="pagos-pendientes-grid">
        <?php foreach ($pendientesQR as $p): ?>
        <div class="pagos-pendiente-item">
            <div class="pagos-pendiente-header">
                <div>
                    <div class="pagos-pendiente-cliente">
                        <?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_paterno']) ?>
                    </div>
                    <div class="pagos-pendiente-fecha">
                        <i class="far fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?>
                    </div>
                </div>
                <div class="pagos-pendiente-monto">Bs. <?= number_format($p['monto'], 2) ?></div>
            </div>

            <?php
                $ext = strtolower(pathinfo($p['comprobante'], PATHINFO_EXTENSION));
                $rutaPublica = asset($p['comprobante']);
            ?>
            <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                <img src="<?= htmlspecialchars($rutaPublica) ?>"
                     alt="Comprobante"
                     class="pagos-pendiente-img"
                     onclick="verComprobante('<?= htmlspecialchars($rutaPublica) ?>')">
            <?php elseif ($ext === 'pdf'): ?>
                <a href="<?= htmlspecialchars($rutaPublica) ?>" target="_blank"
                   class="pagos-btn-success w-100 justify-content-center" style="margin-bottom: 0.75rem;">
                    <i class="fas fa-file-pdf"></i> Ver PDF
                </a>
            <?php endif; ?>

            <div class="pagos-pendiente-actions">
                <a href="<?= url('admin/pagos/estado') ?>?id=<?= $p['idPago'] ?>&estado=Pagado"
                   class="pagos-btn-success flex-fill justify-content-center"
                   onclick="return confirm('¿Aprobar este pago de Bs. <?= number_format($p['monto'], 2) ?>?')">
                    <i class="fas fa-check"></i> Aprobar
                </a>
                <a href="<?= url('admin/pagos/estado') ?>?id=<?= $p['idPago'] ?>&estado=Cancelado"
                   class="pagos-btn-danger flex-fill justify-content-center"
                   onclick="return confirm('¿Rechazar este comprobante?')">
                    <i class="fas fa-times"></i> Rechazar
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- STATS PREMIUM -->
<div class="pagos-stats-row">
    <?php
    $statsConfig = [
        'total' => ['label' => 'Total Pagos', 'icon' => 'fa-receipt', 'emoji' => '', 'color' => 'total', 'value' => $stats['total']],
        'pagados' => ['label' => 'Pagados', 'icon' => 'fa-check-circle', 'emoji' => '', 'color' => 'pagados', 'value' => $stats['pagados']],
        'pendientes' => ['label' => 'Pendientes', 'icon' => 'fa-clock', 'emoji' => '', 'color' => 'pendientes', 'value' => $stats['pendientes']],
        'monto' => ['label' => 'Monto Cobrado', 'icon' => 'fa-dollar-sign', 'emoji' => '', 'color' => 'monto', 'value' => 'Bs. ' . number_format($stats['monto'], 2)]
    ];
    
    foreach ($statsConfig as $key => $cfg):
    ?>
    <div class="pagos-stat-card <?= $cfg['color'] ?>">
        <div class="pagos-stat-top">
            <div class="pagos-stat-icon"><i class="fas <?= $cfg['icon'] ?>"></i></div>
            <span class="pagos-stat-emoji"><?= $cfg['emoji'] ?></span>
        </div>
        <div class="pagos-stat-value"><?= $cfg['value'] ?></div>
        <div class="pagos-stat-label"><?= $cfg['label'] ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- TABLA PREMIUM -->
<div class="pagos-table-card">
    <div class="table-responsive">
        <table class="pagos-table">
            <thead>
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
                    <td colspan="9">
                        <div class="pagos-empty">
                            <i class="fas fa-receipt"></i>
                            <p>No hay pagos registrados</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($pagos as $p):
                    $colorClass = match($p['estado_pago']) {
                        'Pagado' => 'success',
                        'Pendiente' => 'warning',
                        'Cancelado' => 'danger',
                        'Reembolsado' => 'info',
                        'Parcial' => 'secondary',
                        default => 'secondary'
                    };
                    
                    $iconos = ['Efectivo' => 'fa-money-bill-wave', 'Tarjeta' => 'fa-credit-card', 'QR' => 'fa-qrcode'];
                    $iconoMetodo = $iconos[$p['metodo_pago']] ?? 'fa-circle';
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="pagos-client-initial">
                                <?= strtoupper(substr($p['cliente_nombre'] ?? '?', 0, 1)) ?>
                            </span>
                            <div>
                                <div class="pagos-client-name">
                                    <?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_paterno']) ?>
                                </div>
                                <div class="pagos-client-email">
                                    <?= htmlspecialchars($p['cliente_email'] ?? '') ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="pagos-reserva-codigo">
                            <?= htmlspecialchars(substr($p['reserva_codigo'] ?? '', 0, 8)) ?>...
                        </span>
                        <div class="pagos-reserva-fechas">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <?= htmlspecialchars($p['fechaInicio'] ?? '') ?> → <?= htmlspecialchars($p['fechaFin'] ?? '') ?>
                        </div>
                    </td>
                    <td class="pagos-monto">Bs. <?= number_format($p['monto'], 2) ?></td>
                    <td>
                        <span class="pagos-metodo">
                            <i class="fas <?= $iconoMetodo ?>"></i>
                            <?= htmlspecialchars($p['metodo_pago'] ?? '-') ?>
                        </span>
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
                                     class="pagos-comprobante-img"
                                     onclick="verComprobante('<?= htmlspecialchars($url2) ?>')">
                            <?php else: ?>
                                <a href="<?= htmlspecialchars($url2) ?>" target="_blank" class="pagos-btn-success" style="padding: 0.4rem 0.75rem;">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small">
                        <i class="far fa-calendar-alt me-1"></i>
                        <?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?>
                    </td>
                    <td>
                        <?php if ($p['recibo_numero']): ?>
                            <a href="<?= url('recibo/ver?id=' . $p['idRecibo']) ?>"
                               target="_blank"
                               class="pagos-recibo text-decoration-none" title="Ver recibo">
                                <i class="fas fa-receipt me-1"></i>
                                <?= htmlspecialchars($p['recibo_numero']) ?>
                                <i class="fas fa-external-link-alt ms-1" style="font-size:.65rem;opacity:.6;"></i>
                            </a>
                        <?php else: ?>
                            <span class="text-muted small">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="pagos-estado-badge <?= $colorClass ?>">
                            <?= htmlspecialchars($p['estado_pago'] ?? '-') ?>
                        </span>
                    </td>
                    <?php if (($_SESSION['usuario']['rol'] ?? '') === 'Administrador'): ?>
                    <td class="text-end">
                        <div class="pagos-actions">
                            <?php if ($p['estado_pago'] === 'Pendiente' && !empty($p['comprobante'])): ?>
                                <a href="<?= url('admin/pagos/estado') ?>?id=<?= $p['idPago'] ?>&estado=Pagado"
                                   class="pagos-btn-icon success"
                                   onclick="return confirm('¿Aprobar este pago?')"
                                   title="Aprobar">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="<?= url('admin/pagos/estado') ?>?id=<?= $p['idPago'] ?>&estado=Cancelado"
                                   class="pagos-btn-icon danger"
                                   onclick="return confirm('¿Rechazar este comprobante?')"
                                   title="Rechazar">
                                    <i class="fas fa-times"></i>
                                </a>
                            <?php else: ?>
                                <div class="dropdown pagos-dropdown">
                                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-exchange-alt me-1"></i>Estado
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
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="pagos-table-footer" style="padding: 1rem 1.3rem; border-top: 1px solid var(--pagos-border); background: #f8fafc; display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--pagos-muted);">
        <i class="fas fa-chart-line"></i>
        Total: <strong><?= count($pagos) ?></strong> pagos registrados
    </div>
</div>

<!-- MODAL COMPROBANTE -->
<div class="modal fade pagos-modal" id="modalComprobante" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-image me-2" style="color: var(--pagos-accent);"></i>
                    Comprobante de pago
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-3">
                <img id="imgComprobante" src="" alt="Comprobante" class="img-fluid rounded" style="max-height: 500px;">
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