<!-- views/admin/habitaciones/index.php -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="fas fa-bed me-2 text-primary"></i>Gestión de Habitaciones</h4>
    <a href="<?= url('admin/habitaciones/crear') ?>" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Nueva Habitación
    </a>
</div>

<div class="row g-3 mb-4">
    <?php
    $estados = ['Disponible' => 'success', 'Ocupada' => 'danger', 'Reservada' => 'warning', 'Mantenimiento' => 'secondary', 'Limpieza' => 'info'];
    foreach ($estados as $est => $color):
        $count = count(array_filter($habitaciones, fn($h) => $h['estado'] === $est));
    ?>
    <div class="col">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-<?= $color ?>"><?= $count ?></div>
            <div class="text-muted small"><?= $est ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Imagen</th>
                    <th>N° Hab.</th>
                    <th>Piso</th>
                    <th>Tipo</th>
                    <th>Precio/noche</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($habitaciones)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-bed fa-2x mb-2 d-block"></i>
                        No hay habitaciones registradas
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($habitaciones as $h): ?>
                <?php
                    $colores = [
                        'Disponible'   => 'success',
                        'Ocupada'      => 'danger',
                        'Reservada'    => 'warning',
                        'Mantenimiento'=> 'secondary',
                        'Limpieza'     => 'info'
                    ];
                    $color = $colores[$h['estado']] ?? 'secondary';
                ?>
                <tr>
                    <td>
                        <?php if (!empty($h['imagen_principal'])): ?>
                            <img src="<?= asset($h['imagen_principal']) ?>"
                                 alt="Habitación <?= htmlspecialchars($h['numero']) ?>"
                                 style="width:60px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;">
                        <?php else: ?>
                            <div style="width:60px;height:48px;border-radius:6px;background:#f1f3f5;display:flex;align-items:center;justify-content:center;border:1px dashed #dee2e6;">
                                <i class="fas fa-image text-muted" style="font-size:18px;"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($h['numero']) ?></strong></td>
                    <td>Piso <?= htmlspecialchars($h['piso']) ?></td>
                    <td>
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            <?= htmlspecialchars($h['tipo'] ?? 'Sin tipo') ?>
                        </span>
                    </td>
                    <td>Bs. <?= number_format($h['precio'] ?? 0, 2) ?></td>
                    <td>
                        <span class="badge bg-<?= $color ?>">
                            <?= $h['estado'] ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="<?= url('admin/habitaciones/editar?id=' . $h['idHabitacion']) ?>"
                           class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0">
        <small class="text-muted">Total: <?= count($habitaciones) ?> habitaciones</small>
    </div>
</div>