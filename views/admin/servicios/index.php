<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleservicios/stylein.css') ?>">

<div class="serv-index-header">
    <div class="serv-index-title">
        <i class="fas fa-concierge-bell"></i>
        <h4>Servicios del Hotel</h4>
    </div>
    <a href="<?= url('admin/servicios/crear') ?>" class="serv-index-btn">
        <i class="fas fa-plus"></i> Nuevo Servicio
    </a>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="serv-index-alert serv-index-alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i>
        <span><?= htmlspecialchars($_SESSION['success']) ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 0.7rem;"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="serv-index-alert serv-index-alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i>
        <span><?= htmlspecialchars($_SESSION['error']) ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 0.7rem;"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="serv-index-table-card">
    <div class="table-responsive">
        <table class="serv-index-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Servicio</th>
                    <th>Precio</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($servicios)): ?>
                <tr>
                    <td colspan="4">
                        <div class="serv-index-empty">
                            <i class="fas fa-concierge-bell"></i>
                            <p>No hay servicios registrados</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($servicios as $i => $s): ?>
                <tr>
                    <td class="serv-index-number"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></td>
                    <td>
                        <div class="serv-index-nombre">
                            <span class="serv-index-nombre-icon">
                                <i class="fas fa-spa"></i>
                            </span>
                            <?= htmlspecialchars($s['nombre']) ?>
                        </div>
                    </td>
                    <td class="serv-index-precio">Bs. <?= number_format($s['precio'], 2) ?></td>
                    <td class="text-end">
                        <div class="serv-index-actions">
                            <a href="<?= url('admin/servicios/editar?id=' . $s['idServicio']) ?>"
                               class="serv-index-action serv-index-action-edit"
                               title="Editar servicio">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a href="<?= url('admin/servicios/eliminar?id=' . $s['idServicio']) ?>"
                               class="serv-index-action serv-index-action-delete"
                               onclick="return confirm('¿Eliminar este servicio?\n\nEsta acción no se puede deshacer.')"
                               title="Eliminar servicio">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($servicios)): ?>
    <div class="serv-index-footer">
        <i class="fas fa-concierge-bell"></i>
        Total: <strong><?= count($servicios) ?></strong> <?= count($servicios) == 1 ? 'servicio disponible' : 'servicios disponibles' ?>
    </div>
    <?php endif; ?>
</div>