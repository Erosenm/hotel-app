<style>
    /* Estilos visuales inyectados sin alterar la estructura original */
    .card {
        border-radius: 12px !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0,0,0,0.05) !important;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
    }
    .bg-opacity-10 {
        background-color: rgba(0, 0, 0, 0.04) !important;
    }
    .card-header {
        border-bottom: 1px solid #f8f9fa !important;
        padding: 1.25rem 1.5rem !important;
    }
    /* Estilización de las tablas originales */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table thead th {
        background-color: #f8f9fa !important;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1.2rem 1rem;
        border-bottom: none;
    }
    .table tbody td {
        vertical-align: middle;
        padding: 1.2rem 1rem;
        color: #495057;
        border-bottom: 1px solid #f2f2f2;
        font-size: 0.9rem;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,0.015) !important;
    }
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
        border-radius: 8px;
    }
    .fw-bold {
        font-weight: 700 !important;
        letter-spacing: -0.3px;
    }
</style>

<div class="row g-4 mb-4">
    <?php if ($_SESSION['usuario']['rol'] === 'Administrador'): ?>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="fas fa-users fa-lg text-primary"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $stats['total_usuarios'] ?></div>
                    <div class="text-muted small">Usuarios totales</div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="fas fa-bed fa-lg text-success"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $stats['habitaciones_disponibles'] ?></div>
                    <div class="text-muted small">Habitaciones disponibles</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                    <i class="fas fa-clock fa-lg text-warning"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $stats['reservas_pendientes'] ?></div>
                    <div class="text-muted small">Reservas pendientes</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                    <i class="fas fa-calendar-check fa-lg text-info"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $stats['reservas_confirmadas'] ?></div>
                    <div class="text-muted small">Reservas confirmadas</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="fas fa-credit-card fa-lg text-success"></i>
                </div>
                <div>
                    <div class="fs-5 fw-bold text-success">Bs. <?= number_format($stats['monto_hoy'], 2) ?></div>
                    <div class="text-muted small">Cobrado hoy</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                    <i class="fas fa-dollar-sign fa-lg text-primary"></i>
                </div>
                <div>
                    <div class="fs-5 fw-bold text-primary">Bs. <?= number_format($stats['monto_mes'], 2) ?></div>
                    <div class="text-muted small">Cobrado este mes</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                    <i class="fas fa-receipt fa-lg text-success"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $stats['pagos_hoy'] ?></div>
                    <div class="text-muted small">Pagos hoy</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                    <i class="fas fa-hourglass-half fa-lg text-warning"></i>
                </div>
                <div>
                    <div class="fs-4 fw-bold"><?= $stats['pagos_pendientes'] ?></div>
                    <div class="text-muted small">Pagos pendientes</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-credit-card me-2 text-success"></i>Últimos pagos
        </h5>
        <a href="<?= url('admin/pagos') ?>" class="btn btn-sm btn-outline-success">Ver todos</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Cliente</th>
                    <th>Monto</th>
                    <th>Método</th>
                    <th>Recibo</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($ultimos_pagos)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        <i class="fas fa-receipt me-1"></i> Sin pagos registrados aún
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($ultimos_pagos as $p):
                    $colores = [
                        'Pagado'      => 'success',
                        'Pendiente'   => 'warning',
                        'Cancelado'   => 'danger',
                        'Reembolsado' => 'info',
                        'Parcial'     => 'secondary',
                    ];
                    $color = $colores[$p['estado_pago']] ?? 'secondary';
                    $iconos = ['Efectivo' => 'fa-money-bill-wave', 'Tarjeta' => 'fa-credit-card', 'QR' => 'fa-qrcode'];
                    $icono  = $iconos[$p['metodo_pago']] ?? 'fa-circle';
                ?>
                <tr>
                    <td>
                        <span class="badge bg-primary me-1">
                            <?= strtoupper(substr($p['cliente_nombre'] ?? '?', 0, 1)) ?>
                        </span>
                        <?= htmlspecialchars(($p['cliente_nombre'] ?? '') . ' ' . ($p['cliente_paterno'] ?? '')) ?>
                    </td>
                    <td class="fw-bold">Bs. <?= number_format($p['monto'], 2) ?></td>
                    <td>
                        <i class="fas <?= $icono ?> me-1 text-muted"></i>
                        <?= htmlspecialchars($p['metodo_pago'] ?? '-') ?>
                    </td>
                    <td>
                        <?php if (!empty($p['recibo_numero'])): ?>
                            <span class="badge bg-light text-dark border">
                                <?= htmlspecialchars($p['recibo_numero']) ?>
                            </span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><small><?= date('d/m/Y H:i', strtotime($p['fechaPago'])) ?></small></td>
                    <td><span class="badge bg-<?= $color ?>"><?= htmlspecialchars($p['estado_pago']) ?></span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($_SESSION['usuario']['rol'] === 'Administrador'): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold"><i class="fas fa-users me-2 text-primary"></i>Usuarios recientes</h5>
        <a href="<?= url('admin/usuarios') ?>" class="btn btn-sm btn-outline-primary">Ver todos</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>CI</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (array_slice($usuarios, 0, 8) as $u): ?>
                <tr>
                    <td>
                        <span class="badge bg-primary me-2"><?= strtoupper(substr($u['nombre'], 0, 1)) ?></span>
                        <?= htmlspecialchars($u['nombre'] . ' ' . $u['paterno']) ?>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= htmlspecialchars($u['ci']) ?></td>
                    <td><span class="badge bg-secondary"><?= $u['rol'] ?? 'Sin rol' ?></span></td>
                    <td>
                        <?php $color = $u['estado'] === 'Activo' ? 'success' : ($u['estado'] === 'Suspendido' ? 'warning' : 'danger') ?>
                        <span class="badge bg-<?= $color ?>"><?= $u['estado'] ?></span>
                    </td>
                    <td class="text-end">
                        <a href="<?= url('admin/usuarios/editar?id=' . $u['idUsuario']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>