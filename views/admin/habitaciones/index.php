<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylehabitaciones/index.css') ?>">

<!-- views/admin/habitaciones/index.php -->

<div class="hi-header">

    <div class="hi-title">
        <i class="fas fa-bed"></i>
        <span>Gestión de Habitaciones</span>
    </div>

    <?php if ($_SESSION['usuario']['rol'] === 'Administrador'): ?>
        <a href="<?= url('admin/habitaciones/crear') ?>" class="hi-btn-primary">
            <i class="fas fa-plus"></i>
            Nueva Habitación
        </a>
    <?php endif; ?>

</div>

<!-- =========================
     STATS
========================= -->

<div class="hi-stats-row">

<?php
$estados = [
    'Disponible'    => 'green',
    'Ocupada'       => 'rose',
    'Reservada'     => 'amber',
    'Mantenimiento' => 'violet',
    'Limpieza'      => 'cyan'
];

$totalHabitaciones = count($habitaciones);

foreach ($estados as $est => $color):

    $count = count(array_filter($habitaciones, fn($h) => $h['estado'] === $est));

    $porcentaje = $totalHabitaciones > 0
        ? ($count / $totalHabitaciones) * 100
        : 0;

    $icons = [
        'Disponible'    => 'fa-check-circle',
        'Ocupada'       => 'fa-bed',
        'Reservada'     => 'fa-calendar-check',
        'Mantenimiento' => 'fa-tools',
        'Limpieza'      => 'fa-broom'
    ];
?>

<div class="hi-stat-card <?= $color ?>">

    <div class="hi-stat-top">

        <div class="hi-stat-icon">
            <i class="fas <?= $icons[$est] ?>"></i>
        </div>

        <div class="hi-stat-emoji">
            <?= $count ?>
        </div>

    </div>

    <div class="hi-stat-value">
        <?= $count ?>
    </div>

    <div class="hi-stat-label">
        <?= $est ?>
    </div>

    <div class="hi-stat-bar-track">
        <div class="hi-stat-bar-fill" style="width: <?= $porcentaje ?>%"></div>
    </div>

    <div class="hi-stat-pct">
        <?= round($porcentaje) ?>% del total
    </div>

</div>

<?php endforeach; ?>

</div>

<!-- =========================
     TABLA
========================= -->

<div class="hi-table-card">

    <table class="hi-table">

        <thead>
            <tr>
                <th>Imagen</th>
                <th>N° Habitación</th>
                <th>Piso</th>
                <th>Tipo</th>
                <th>Precio/Noche</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>

        <?php if (empty($habitaciones)): ?>

            <tr>
                <td colspan="7">

                    <div class="hi-empty">
                        <i class="fas fa-bed"></i>
                        <p>No hay habitaciones registradas</p>
                    </div>

                </td>
            </tr>

        <?php else: ?>

            <?php foreach ($habitaciones as $h): ?>

            <?php
                $estadoClases = [
                    'Disponible'    => 'success',
                    'Ocupada'       => 'danger',
                    'Reservada'     => 'warning',
                    'Mantenimiento' => 'secondary',
                    'Limpieza'      => 'info'
                ];

                $estadoClass = $estadoClases[$h['estado']] ?? 'secondary';
            ?>

            <tr>

                <!-- Imagen -->
                <td>

                    <?php if (!empty($h['imagen_principal'])): ?>

                        <img
                            src="<?= asset($h['imagen_principal']) ?>"
                            alt="Habitación <?= htmlspecialchars($h['numero']) ?>"
                            class="hi-room-img"
                        >

                    <?php else: ?>

                        <div class="hi-room-img-placeholder">
                            <i class="fas fa-image"></i>
                        </div>

                    <?php endif; ?>

                </td>

                <!-- Número -->
                <td>
                    <span class="hi-room-num">
                        <?= htmlspecialchars($h['numero']) ?>
                    </span>
                </td>

                <!-- Piso -->
                <td>
                    <span class="hi-piso">
                        Piso <?= htmlspecialchars($h['piso']) ?>
                    </span>
                </td>

                <!-- Tipo -->
                <td>

                    <span class="hi-tipo-badge">
                        <?= htmlspecialchars($h['tipo'] ?? 'Sin tipo') ?>
                    </span>

                </td>

                <!-- Precio -->
                <td>

                    <span class="hi-precio">
                        Bs. <?= number_format($h['precio'] ?? 0, 2) ?>
                    </span>

                </td>

                <!-- Estado -->
                <td>

                    <span class="hi-estado-badge <?= $estadoClass ?>">
                        <?= htmlspecialchars($h['estado']) ?>
                    </span>

                </td>

                <!-- Acciones -->
                <td>

                    <div class="hi-actions">

                        <!-- Editar -->
                        <a
                            href="<?= url('admin/habitaciones/editar?id=' . $h['idHabitacion']) ?>"
                            class="hi-action-btn edit"
                            title="Editar"
                        >
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Dropdown -->
                        <div class="dropdown">

                            <button
                                class="hi-action-btn edit"
                                data-bs-toggle="dropdown"
                                title="Cambiar estado"
                            >
                                <i class="fas fa-exchange-alt"></i>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>
                                    <span class="dropdown-header">
                                        Cambiar estado
                                    </span>
                                </li>

                                <?php foreach (['Disponible','Mantenimiento','Limpieza','Ocupada'] as $est): ?>

                                    <?php if ($est !== $h['estado']): ?>

                                    <li>

                                        <a
                                            class="dropdown-item"
                                            href="<?= url('admin/habitaciones/estado?id=' . $h['idHabitacion'] . '&estado=' . urlencode($est)) ?>"
                                        >

                                            <?php
                                            $icons = [
                                                'Disponible'    => 'fa-check-circle text-success',
                                                'Mantenimiento' => 'fa-tools text-secondary',
                                                'Limpieza'      => 'fa-broom text-info',
                                                'Ocupada'       => 'fa-bed text-danger'
                                            ];
                                            ?>

                                            <i class="fas <?= $icons[$est] ?? 'fa-circle' ?> me-2"></i>

                                            <?= $est ?>

                                        </a>

                                    </li>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                            </ul>

                        </div>

                    </div>

                </td>

            </tr>

            <?php endforeach; ?>

        <?php endif; ?>

        </tbody>

    </table>

    <div class="hi-table-footer">

        <i class="fas fa-layer-group"></i>

        <span>
            Total:
            <strong><?= count($habitaciones) ?></strong>
            habitaciones
        </span>

    </div>

</div>