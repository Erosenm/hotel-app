<style>
.reserva-card { border:none; border-radius:14px; box-shadow:0 2px 15px rgba(0,0,0,0.07); overflow:hidden; margin-bottom:16px; }
.reserva-img { width:130px; min-height:110px; object-fit:cover; }
.reserva-placeholder { width:130px; min-height:110px; background:#f1f3f5; display:flex; align-items:center; justify-content:center; }
.tab-btn { border:none; background:none; padding:10px 20px; border-radius:30px; font-weight:600; color:#6c757d; transition:.2s; cursor:pointer; }
.tab-btn.activo { background:#0f3460; color:#fff; }
.barra-pago { height: 6px; border-radius: 10px; background: #e9ecef; overflow: hidden; }
.barra-pago-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #198754, #20c997); transition: width .4s; }
</style>
 
<div class="page-top-space" style="background:linear-gradient(135deg,#1a1a2e,#0f3460);padding:40px 20px 30px;color:#fff;">
    <div style="max-width:900px;margin:0 auto;">
        <a href="<?= url('cliente/dashboard') ?>" style="color:#a0b4d0;font-size:.85rem;text-decoration:none;">
        </a>
        <h2 style="color:#FFFFFF;" class="fw-bold mt-2 mb-0">Mis Reservas</h2>
        <p style="color:#a0b4d0;">Historial y estado de todas tus reservas</p>
    </div>
</div>
 
<div style="max-width:900px;margin:0 auto;padding:30px 20px;">
 
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
 
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
 
    <?php if (empty($reservas)): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="fas fa-calendar-times fa-4x text-muted mb-3 d-block"></i>
            <h5 class="text-muted">Aún no tienes reservas</h5>
            <p class="text-muted small">Explora nuestras habitaciones y realiza tu primera reserva.</p>
            <a href="<?= url('habitaciones') ?>" class="btn btn-primary px-5 mt-2">
                <i class="fas fa-bed me-2"></i>Ver habitaciones
            </a>
        </div>
    <?php else: ?>
 
        <!-- Filtro por estado -->
        <div class="d-flex flex-wrap gap-2 mb-4 bg-white p-2 rounded-pill shadow-sm" style="width:fit-content;">
            <button class="tab-btn activo" onclick="filtrar('todos', this)">
                Todas (<?= count($reservas) ?>)
            </button>
            <?php
            $estados = array_unique(array_column($reservas, 'estado_reserva'));
            foreach ($estados as $est):
                $cnt = count(array_filter($reservas, fn($r) => $r['estado_reserva'] === $est));
            ?>
            <button class="tab-btn" onclick="filtrar('<?= $est ?>', this)">
                <?= $est ?> (<?= $cnt ?>)
            </button>
            <?php endforeach; ?>
        </div>
 
        <div id="listaReservas">
            <?php foreach ($reservas as $r):
                $colores = [
                    'Pendiente'  => 'warning',
                    'Confirmada' => 'success',
                    'Cancelada'  => 'danger',
                    'Completada' => 'secondary',
                    'No show'    => 'danger',
                ];
                $badge      = $colores[$r['estado_reserva']] ?? 'secondary';
                $dias       = (new DateTime($r['fechaInicio']))->diff(new DateTime($r['fechaFin']))->days;
                $cancelable = in_array($r['estado_reserva'], ['Pendiente', 'Confirmada']);
                $pagable    = in_array($r['estado_reserva'], ['Confirmada', 'Pendiente']);
                $montoPagado= (float)($r['monto_pagado'] ?? 0);
                $total      = (float)$r['precioTotal'];
                $pendiente  = max(0, $total - $montoPagado);
                $porcentaje = $total > 0 ? min(100, round(($montoPagado / $total) * 100)) : 0;
            ?>
            <div class="reserva-card bg-white" data-estado="<?= $r['estado_reserva'] ?>">
                <div class="d-flex">
 
                    <!-- Imagen -->
                    <?php if (!empty($r['imagen'])): ?>
                        <img src="<?= asset($r['imagen']) ?>" class="reserva-img" alt="Hab">
                    <?php else: ?>
                        <div class="reserva-placeholder">
                            <i class="fas fa-bed fa-2x text-muted"></i>
                        </div>
                    <?php endif; ?>
 
                    <!-- Info -->
                    <div class="p-3 flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold mb-0">
                                    Habitación <?= htmlspecialchars($r['habitacion_numero']) ?>
                                    <span class="text-muted fw-normal fs-6">· <?= htmlspecialchars($r['tipo_habitacion']) ?></span>
                                </h5>
                                <p class="text-muted small mb-0">Piso <?= $r['piso'] ?></p>
                            </div>
                            <span class="badge bg-<?= $badge ?> fs-6"><?= $r['estado_reserva'] ?></span>
                        </div>
 
                        <div class="d-flex flex-wrap gap-3 mt-2">
                            <span class="text-muted small">
                                <i class="fas fa-sign-in-alt me-1 text-success"></i>
                                <strong>Entrada:</strong> <?= date('d/m/Y', strtotime($r['fechaInicio'])) ?>
                            </span>
                            <span class="text-muted small">
                                <i class="fas fa-sign-out-alt me-1 text-danger"></i>
                                <strong>Salida:</strong> <?= date('d/m/Y', strtotime($r['fechaFin'])) ?>
                            </span>
                            <span class="text-muted small">
                                <i class="fas fa-moon me-1 text-primary"></i>
                                <?= $dias ?> noche<?= $dias != 1 ? 's' : '' ?>
                            </span>
                            <span class="text-muted small">
                                <i class="fas fa-users me-1"></i>
                                <?= $r['cantidadPersonas'] ?> persona<?= $r['cantidadPersonas'] != 1 ? 's' : '' ?>
                            </span>
                        </div>
 
                        <!-- Barra de pago -->
                        <?php if ($pagable || $montoPagado > 0): ?>
                        <div class="mt-2">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Pagado: <strong class="text-success">Bs. <?= number_format($montoPagado, 2) ?></strong></span>
                                <?php if ($pendiente > 0): ?>
                                <span class="text-muted">Pendiente: <strong class="text-danger">Bs. <?= number_format($pendiente, 2) ?></strong></span>
                                <?php else: ?>
                                <span class="text-success fw-bold"><i class="fas fa-check-circle me-1"></i>Pagado completo</span>
                                <?php endif; ?>
                            </div>
                            <div class="barra-pago">
                                <div class="barra-pago-fill" style="width:<?= $porcentaje ?>%"></div>
                            </div>
                        </div>
                        <?php endif; ?>
 
                        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                            <span style="font-size:1.1rem;font-weight:700;color:#0f3460;">
                                Bs. <?= number_format($total, 2) ?>
                                <?php if (!empty($r['recibo_numero'])): ?>
                                    <span class="badge bg-light text-dark border ms-2" style="font-size:.7rem;">
                                        <i class="fas fa-receipt me-1"></i><?= htmlspecialchars($r['recibo_numero']) ?>
                                    </span>
                                <?php endif; ?>
                            </span>
 
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="<?= url('cliente/reservas/detalle?id=' . $r['idReserva']) ?>"
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>Ver detalle
                                </a>
                                <?php if ($pagable && $pendiente > 0): ?>
                                    <a href="<?= url('cliente/pagar?id=' . $r['idReserva']) ?>"
                                       class="btn btn-success btn-sm px-3">
                                        <i class="fas fa-credit-card me-1"></i>Pagar
                                        <span class="badge bg-white text-success ms-1">Bs. <?= number_format($pendiente, 2) ?></span>
                                    </a>
                                <?php endif; ?>
 
                                <?php if ($cancelable): ?>
                                    <a href="<?= url('cliente/reservas/cancelar?id=' . $r['idReserva']) ?>"
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('¿Estás seguro de cancelar esta reserva?')">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($r['pago_estado']) && !empty($r['pago_metodo']) && $r['pago_metodo'] === 'QR'): ?>
                                    <?php if ($r['pago_estado'] === 'Pendiente'): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>Comprobante en revisión
                                        </span>
                                    <?php elseif ($r['pago_estado'] === 'Cancelado'): ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>Comprobante rechazado
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if (!empty($r['servicios_count']) && $r['servicios_count'] > 0): ?>
                                    <span class="badge bg-info">
                                        <i class="fas fa-concierge-bell me-1"></i><?= $r['servicios_count'] ?> servicio(s)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
 
                </div>
            </div>
            <?php endforeach; ?>
        </div>
 
        <div class="text-center mt-4">
            <a href="<?= url('habitaciones') ?>" class="btn btn-outline-primary px-4">
                <i class="fas fa-plus me-2"></i>Nueva reserva
            </a>
        </div>
 
    <?php endif; ?>
</div>
 
<script>
function filtrar(estado, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('activo'));
    btn.classList.add('activo');
    document.querySelectorAll('[data-estado]').forEach(card => {
        card.style.display = (estado === 'todos' || card.dataset.estado === estado) ? 'block' : 'none';
    });
}
</script>