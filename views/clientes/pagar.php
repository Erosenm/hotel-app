<style>
.metodo-card {
    border: 2px solid #e9ecef;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    flex: 1;
    min-width: 100px;
}
.metodo-card:hover { border-color: #0f3460; background: #f0f4ff; }
.metodo-card.seleccionado { border-color: #0f3460; background: #e8eeff; }
.metodo-card input { display: none; }
.metodo-card i { font-size: 2rem; margin-bottom: 8px; display: block; color: #0f3460; }
</style>
 
<!-- Header -->
<div style="background:linear-gradient(135deg,#1a1a2e,#0f3460);padding:40px 20px 30px;color:#fff;">
    <div style="max-width:620px;margin:0 auto;">
        <a href="<?= url('cliente/reservas') ?>" style="color:#a0b4d0;font-size:.85rem;text-decoration:none;">
            <i class="fas fa-arrow-left me-2"></i>Mis reservas
        </a>
        <h2 class="fw-bold mt-2 mb-0">Pagar reserva</h2>
        <p style="color:#a0b4d0;">Completa tu pago de forma segura</p>
    </div>
</div>
 
<div style="max-width:620px;margin:0 auto;padding:30px 20px;">
 
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
 
    <!-- Resumen de la reserva -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius:14px;overflow:hidden;">
        <div class="card-header fw-semibold" style="background:#f8f9fa;">
            <i class="fas fa-calendar-check me-2 text-primary"></i>Resumen de tu reserva
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-6">
                    <div class="text-muted small">Habitación</div>
                    <div class="fw-semibold">
                        Nº <?= htmlspecialchars($reserva['habitacion_numero']) ?>
                        · Piso <?= htmlspecialchars($reserva['piso']) ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-muted small">Tipo</div>
                    <div class="fw-semibold"><?= htmlspecialchars($reserva['tipo_habitacion']) ?></div>
                </div>
                <div class="col-6">
                    <div class="text-muted small">Entrada</div>
                    <div class="fw-semibold"><?= date('d/m/Y', strtotime($reserva['fechaInicio'])) ?></div>
                </div>
                <div class="col-6">
                    <div class="text-muted small">Salida</div>
                    <div class="fw-semibold"><?= date('d/m/Y', strtotime($reserva['fechaFin'])) ?></div>
                </div>
                <?php
                    $dias = (new DateTime($reserva['fechaInicio']))->diff(new DateTime($reserva['fechaFin']))->days;
                    $pendiente = max(0, $reserva['precioTotal'] - $reserva['monto_pagado']);
                ?>
                <div class="col-6">
                    <div class="text-muted small">Noches</div>
                    <div class="fw-semibold"><?= $dias ?></div>
                </div>
                <div class="col-6">
                    <div class="text-muted small">Estado</div>
                    <span class="badge bg-success"><?= htmlspecialchars($reserva['estado_reserva']) ?></span>
                </div>
            </div>
 
            <hr>
 
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">Total reserva</span>
                <span class="fw-semibold">Bs. <?= number_format($reserva['precioTotal'], 2) ?></span>
            </div>
            <?php if ($reserva['monto_pagado'] > 0): ?>
            <div class="d-flex justify-content-between align-items-center mt-1">
                <span class="text-muted">Ya pagado</span>
                <span class="text-success fw-semibold">- Bs. <?= number_format($reserva['monto_pagado'], 2) ?></span>
            </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                <span class="fw-bold fs-5">Total a pagar</span>
                <span class="fw-bold fs-4" style="color:#0f3460;">Bs. <?= number_format($pendiente, 2) ?></span>
            </div>
        </div>
    </div>
 
    <!-- Formulario de pago -->
    <div class="card border-0 shadow-sm" style="border-radius:14px;">
        <div class="card-header fw-semibold" style="background:#f8f9fa;">
            <i class="fas fa-credit-card me-2 text-primary"></i>Método de pago
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('cliente/pagar') ?>" id="formPago">
                <input type="hidden" name="idReserva" value="<?= $reserva['idReserva'] ?>">
 
                <!-- Métodos -->
                <div class="d-flex gap-3 mb-4 flex-wrap">
                    <?php foreach ($metodos as $m):
                        $iconos = ['Efectivo' => 'fa-money-bill-wave', 'Tarjeta' => 'fa-credit-card', 'QR' => 'fa-qrcode'];
                        $colores = ['Efectivo' => 'text-success', 'Tarjeta' => 'text-primary', 'QR' => 'text-warning'];
                        $icono  = $iconos[$m['nombre']]  ?? 'fa-circle';
                        $color  = $colores[$m['nombre']] ?? 'text-secondary';
                    ?>
                    <label class="metodo-card" for="met<?= $m['idMetodoPago'] ?>">
                        <input type="radio" name="idMetodoPago" id="met<?= $m['idMetodoPago'] ?>"
                               value="<?= $m['idMetodoPago'] ?>" required>
                        <i class="fas <?= $icono ?> <?= $color ?>"></i>
                        <div class="fw-semibold small"><?= htmlspecialchars($m['nombre']) ?></div>
                        <?php if ($m['nombre'] === 'Efectivo'): ?>
                            <div class="text-muted" style="font-size:.75rem;">Pago en recepción</div>
                        <?php elseif ($m['nombre'] === 'Tarjeta'): ?>
                            <div class="text-muted" style="font-size:.75rem;">Débito / Crédito</div>
                        <?php elseif ($m['nombre'] === 'QR'): ?>
                            <div class="text-muted" style="font-size:.75rem;">Transferencia QR</div>
                        <?php endif; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
 
                <!-- Info contextual por método -->
                <div id="infoMetodo" class="alert alert-info d-none mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="textoMetodo"></span>
                </div>
 
                <!-- Botón de pago -->
                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5" id="btnPagar" disabled>
                    <i class="fas fa-lock me-2"></i>Confirmar pago · Bs. <?= number_format($pendiente, 2) ?>
                </button>
 
                <p class="text-center text-muted small mt-3">
                    <i class="fas fa-shield-alt me-1"></i>
                    Tu reserva está protegida. Puedes cancelar desde "Mis reservas".
                </p>
            </form>
        </div>
    </div>
 
</div>
 
<script>
const infoTextos = {
    'Efectivo': 'Pagarás en efectivo al llegar al hotel. El recibo se generará al momento del check-in.',
    'Tarjeta':  'Presenta tu tarjeta de débito o crédito en recepción. Aceptamos Visa, Mastercard y American Express.',
    'QR':       'Realiza la transferencia por QR a nuestra cuenta bancaria y presenta el comprobante en recepción.'
};
 
document.querySelectorAll('input[name="idMetodoPago"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        // Resaltar tarjeta seleccionada
        document.querySelectorAll('.metodo-card').forEach(c => c.classList.remove('seleccionado'));
        this.closest('.metodo-card').classList.add('seleccionado');
 
        // Mostrar info contextual
        const nombre = this.closest('.metodo-card').querySelector('.fw-semibold').textContent.trim();
        const info   = document.getElementById('infoMetodo');
        const texto  = document.getElementById('textoMetodo');
        texto.textContent = infoTextos[nombre] || '';
        info.classList.remove('d-none');
 
        // Habilitar botón
        document.getElementById('btnPagar').disabled = false;
    });
});
</script>