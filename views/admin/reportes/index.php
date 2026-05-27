<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-chart-line me-2 text-primary"></i>Reportes
    </h4>
    <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-print me-1"></i>Imprimir reporte
    </button>
</div>

<!-- Filtro de fechas -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?= htmlspecialchars($desde) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?= htmlspecialchars($hasta) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i>Filtrar
                </button>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Exportar</label>
                <div class="d-flex gap-2">
                    <a href="<?= url('admin/reportes/exportar') ?>?desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>&tipo=pagos"
                       class="btn btn-success btn-sm flex-fill" title="Exportar pagos a Excel">
                        <i class="fas fa-file-excel me-1"></i>Pagos
                    </a>
                    <a href="<?= url('admin/reportes/exportar') ?>?desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>&tipo=reservas"
                       class="btn btn-info btn-sm flex-fill text-white" title="Exportar reservas a Excel">
                        <i class="fas fa-file-excel me-1"></i>Reservas
                    </a>
                    <a href="<?= url('admin/reportes/exportar') ?>?tipo=inventario"
                       class="btn btn-warning btn-sm flex-fill" title="Exportar inventario a Excel">
                        <i class="fas fa-file-excel me-1"></i>Stock
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Resumen ingresos -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center py-4">
            <div class="text-muted small mb-1">Total ingresos del período</div>
            <div class="fs-3 fw-bold text-primary">Bs. <?= number_format($totalIngresos, 2) ?></div>
        </div>
    </div>
    <?php foreach ($ingresos as $ing): ?>
    <div class="col-md">
        <div class="card border-0 shadow-sm text-center py-4">
            <div class="text-muted small mb-1"><?= htmlspecialchars($ing['metodo']) ?></div>
            <div class="fs-5 fw-bold">Bs. <?= number_format($ing['total'], 2) ?></div>
            <div class="text-muted small"><?= $ing['cantidad'] ?> pago(s)</div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mb-4">

    <!-- Reservas por estado -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3"><h6 class="fw-semibold mb-0">Reservas por estado</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Estado</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($reservasPeriodo as $r):
                        $col = ['Pendiente'=>'warning','Confirmada'=>'success','Cancelada'=>'danger','Completada'=>'primary','No show'=>'secondary'];
                    ?>
                    <tr>
                        <td><span class="badge bg-<?= $col[$r['estado']] ?? 'secondary' ?>"><?= htmlspecialchars($r['estado']) ?></span></td>
                        <td class="text-end fw-semibold"><?= $r['total'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Habitaciones más reservadas -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3"><h6 class="fw-semibold mb-0">Top habitaciones más reservadas</h6></div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light"><tr><th>Habitación</th><th>Tipo</th><th class="text-end">Reservas</th></tr></thead>
                    <tbody>
                    <?php foreach ($habitacionesTop as $h): ?>
                    <tr>
                        <td class="fw-semibold">N° <?= htmlspecialchars($h['numero']) ?></td>
                        <td><span class="badge bg-info"><?= htmlspecialchars($h['tipo']) ?></span></td>
                        <td class="text-end fw-bold text-primary"><?= $h['total_reservas'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Detalle de pagos -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-semibold mb-0">Detalle de pagos — <?= date('d/m/Y', strtotime($desde)) ?> al <?= date('d/m/Y', strtotime($hasta)) ?></h6>
        <span class="badge bg-primary"><?= count($pagosDetalle) ?> pagos</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Cliente</th><th>Fechas reserva</th><th>Método</th><th>Recibo</th><th>Fecha pago</th><th class="text-end">Monto</th></tr>
                </thead>
                <tbody>
                <?php if (empty($pagosDetalle)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No hay pagos en este período</td></tr>
                <?php else: ?>
                <?php foreach ($pagosDetalle as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['cliente_nombre'] . ' ' . $p['cliente_paterno']) ?></td>
                    <td><small><?= date('d/m/Y', strtotime($p['fechaInicio'])) ?> → <?= date('d/m/Y', strtotime($p['fechaFin'])) ?></small></td>
                    <td><?= htmlspecialchars($p['metodo']) ?></td>
                    <td><small class="text-muted"><?= htmlspecialchars($p['recibo'] ?? '—') ?></small></td>
                    <td><small><?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?></small></td>
                    <td class="text-end fw-bold">Bs. <?= number_format($p['monto'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="fw-bold text-end">TOTAL</td>
                        <td class="text-end fw-bold text-primary fs-6">Bs. <?= number_format($totalIngresos, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .sidebar-overlay, .btn, form, nav { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>