<link rel="stylesheet" href="<?= asset('css/cssCliente/clientereservas.css') ?>">

<!-- Hero Section -->
<div class="reservas-hero">
    <div class="reservas-hero-content">
        <h1>Mis Reservas</h1>
        <p>Historial y estado de todas tus reservas</p>
    </div>
</div>

<div class="reservas-main">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="reservas-alert reservas-alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?= $_SESSION['success'] ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="reservas-alert reservas-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= $_SESSION['error'] ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($reservas)): ?>
        <div class="reservas-empty">
            <i class="fas fa-calendar-times"></i>
            <h5>Aún no tienes reservas</h5>
            <p>Explora nuestras habitaciones y realiza tu primera reserva.</p>
            <a href="<?= url('habitaciones') ?>" class="btn-primary">
                <i class="fas fa-bed"></i> Ver habitaciones
            </a>
        </div>
    <?php else: ?>
        <!-- Filtros -->
        <div class="reservas-filtros">
            <button class="reservas-tab activo" onclick="filtrar('todos', this)">
                Todas (<?= count($reservas) ?>)
            </button>
            <?php
            $estados = array_unique(array_column($reservas, 'estado_reserva'));
            foreach ($estados as $est):
                $cnt = count(array_filter($reservas, fn($r) => $r['estado_reserva'] === $est));
            ?>
            <button class="reservas-tab" onclick="filtrar('<?= $est ?>', this)">
                <?= $est ?> (<?= $cnt ?>)
            </button>
            <?php endforeach; ?>
        </div>

        <div id="listaReservas">
            <?php foreach ($reservas as $r):
                $badgeClass = match($r['estado_reserva']) {
                    'Pendiente' => 'pendiente',
                    'Confirmada' => 'confirmada',
                    'Cancelada' => 'cancelada',
                    'Completada' => 'completada',
                    default => 'pendiente'
                };
                $dias = (new DateTime($r['fechaInicio']))->diff(new DateTime($r['fechaFin']))->days;
                $cancelable = in_array($r['estado_reserva'], ['Pendiente', 'Confirmada']);
                $pagable = in_array($r['estado_reserva'], ['Confirmada', 'Pendiente']);
                $montoPagado = (float)($r['monto_pagado'] ?? 0);
                $total = (float)$r['precioTotal'];
                $pendiente = max(0, $total - $montoPagado);
                $porcentaje = $total > 0 ? min(100, round(($montoPagado / $total) * 100)) : 0;
            ?>
            <div class="reservas-card" data-estado="<?= $r['estado_reserva'] ?>">
                <div class="reservas-card-inner">
                    <!-- Imagen -->
                    <?php if (!empty($r['imagen'])): ?>
                        <img src="<?= asset($r['imagen']) ?>" class="reservas-img" alt="Habitación">
                    <?php else: ?>
                        <div class="reservas-img-placeholder">
                            <i class="fas fa-bed fa-2x"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Contenido -->
                    <div class="reservas-content">
                        <div class="reservas-header">
                            <div>
                                <h3 class="reservas-titulo">
                                    Habitación <?= htmlspecialchars($r['habitacion_numero']) ?>
                                    <small>· <?= htmlspecialchars($r['tipo_habitacion']) ?></small>
                                </h3>
                                <div class="reservas-piso">Piso <?= $r['piso'] ?></div>
                            </div>
                            <span class="reservas-badge <?= $badgeClass ?>"><?= $r['estado_reserva'] ?></span>
                        </div>

                        <div class="reservas-fechas">
                            <span class="reservas-fecha">
                                <i class="fas fa-sign-in-alt text-success"></i>
                                Entrada: <strong><?= date('d/m/Y', strtotime($r['fechaInicio'])) ?></strong>
                            </span>
                            <span class="reservas-fecha">
                                <i class="fas fa-sign-out-alt text-danger"></i>
                                Salida: <strong><?= date('d/m/Y', strtotime($r['fechaFin'])) ?></strong>
                            </span>
                            <span class="reservas-fecha">
                                <i class="fas fa-moon text-primary"></i>
                                <?= $dias ?> noche<?= $dias != 1 ? 's' : '' ?>
                            </span>
                            <span class="reservas-fecha">
                                <i class="fas fa-users"></i>
                                <?= $r['cantidadPersonas'] ?> persona<?= $r['cantidadPersonas'] != 1 ? 's' : '' ?>
                            </span>
                        </div>

                        <!-- Barra de pago -->
                        <?php if ($pagable || $montoPagado > 0): ?>
                        <div class="reservas-pago-info">
                            <div class="reservas-pago-header">
                                <span class="reservas-pago-monto-pagado">
                                    Pagado: <strong>Bs. <?= number_format($montoPagado, 2) ?></strong>
                                </span>
                                <?php if ($pendiente > 0): ?>
                                <span class="reservas-pago-monto-pendiente">
                                    Pendiente: <strong>Bs. <?= number_format($pendiente, 2) ?></strong>
                                </span>
                                <?php else: ?>
                                <span class="reservas-pago-completo">
                                    <i class="fas fa-check-circle"></i> Pagado completo
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="reservas-pago-barra">
                                <div class="reservas-pago-barra-fill" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="reservas-footer">
                            <div class="reservas-precio">
                                Bs. <?= number_format($total, 2) ?>
                                <?php if (!empty($r['recibo_numero'])): ?>
                                    <span class="reservas-badge-small info">
                                        <i class="fas fa-receipt"></i> <?= htmlspecialchars($r['recibo_numero']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="reservas-actions">
                                <a href="<?= url('cliente/reservas/detalle?id=' . $r['idReserva']) ?>"
                                   class="reservas-btn reservas-btn-outline">
                                    <i class="fas fa-eye"></i> Ver detalle
                                </a>
                                
                                <?php if ($pagable && $pendiente > 0): ?>
                                    <a href="<?= url('cliente/pagar?id=' . $r['idReserva']) ?>"
                                       class="reservas-btn reservas-btn-success">
                                        <i class="fas fa-credit-card"></i> Pagar
                                        <span class="badge bg-white text-success ms-1">Bs. <?= number_format($pendiente, 2) ?></span>
                                    </a>
                                <?php endif; ?>

                                <?php if ($cancelable): ?>
                                    <a href="<?= url('cliente/reservas/cancelar?id=' . $r['idReserva']) ?>"
                                       class="reservas-btn reservas-btn-danger"
                                       onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                <?php endif; ?>
                                
                                <?php if (!empty($r['pago_estado']) && !empty($r['pago_metodo']) && $r['pago_metodo'] === 'QR'): ?>
                                    <?php if ($r['pago_estado'] === 'Pendiente'): ?>
                                        <span class="reservas-badge-small warning">
                                            <i class="fas fa-clock"></i> Comprobante en revisión
                                        </span>
                                    <?php elseif ($r['pago_estado'] === 'Cancelado'): ?>
                                        <span class="reservas-badge-small danger">
                                            <i class="fas fa-times-circle"></i> Comprobante rechazado
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php if (!empty($r['servicios_count']) && $r['servicios_count'] > 0): ?>
                                    <span class="reservas-badge-small info">
                                        <i class="fas fa-concierge-bell"></i> <?= $r['servicios_count'] ?> servicio(s)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="reservas-nueva">
            <a href="<?= url('habitaciones') ?>" class="btn-outline">
                <i class="fas fa-plus"></i> Nueva reserva
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
function filtrar(estado, btn) {
    document.querySelectorAll('.reservas-tab').forEach(b => b.classList.remove('activo'));
    btn.classList.add('activo');
    document.querySelectorAll('[data-estado]').forEach(card => {
        card.style.display = (estado === 'todos' || card.dataset.estado === estado) ? 'block' : 'none';
    });
}
</script>