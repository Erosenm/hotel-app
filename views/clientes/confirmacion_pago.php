<style>
@keyframes checkAnim {
    0%   { transform: scale(0); opacity: 0; }
    60%  { transform: scale(1.15); }
    100% { transform: scale(1); opacity: 1; }
}
.check-circle {
    width: 90px; height: 90px;
    background: #198754;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 20px;
    animation: checkAnim .5s ease forwards;
}
.recibo-box {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: 14px;
    padding: 24px;
    max-width: 420px;
    margin: 0 auto;
}
</style>
 
<div style="background:linear-gradient(135deg,#1a1a2e,#0f3460);padding:50px 20px 40px;color:#fff;text-align:center;">
    <?php if (!empty($recibo['esQR'])): ?>
        <div class="check-circle" style="background:#f59e0b;">
            <i class="fas fa-clock fa-2x text-white"></i>
        </div>
        <h2 class="fw-bold mb-1">¡Comprobante enviado!</h2>
        <p style="color:#a0b4d0;max-width:440px;margin:0 auto;">
            Tu comprobante fue recibido. El hotel lo verificará pronto y recibirás
            un email cuando sea aprobado.
        </p>
    <?php else: ?>
        <div class="check-circle">
            <i class="fas fa-check fa-2x text-white"></i>
        </div>
        <h2 class="fw-bold mb-1">¡Pago registrado!</h2>
        <p style="color:#a0b4d0;max-width:400px;margin:0 auto;">
            Tu pago fue procesado correctamente. Guarda tu número de recibo.
        </p>
    <?php endif; ?>
</div>
 
<div style="max-width:560px;margin:0 auto;padding:36px 20px;">
 
    <!-- Recibo -->
    <div class="recibo-box mb-4">
        <div class="text-center mb-3">
            <i class="fas fa-receipt fa-2x text-success mb-2 d-block"></i>
            <div class="text-muted small">Número de recibo</div>
            <div class="fw-bold fs-3 text-dark"><?= htmlspecialchars($recibo['numero']) ?></div>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Monto pagado</span>
            <span class="fw-bold text-success fs-5">Bs. <?= number_format($recibo['monto'], 2) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Reserva Nº</span>
            <span class="fw-semibold"><?= htmlspecialchars($recibo['idReserva']) ?></span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-muted">Fecha y hora</span>
            <span class="fw-semibold"><?= htmlspecialchars($recibo['fecha']) ?></span>
        </div>
    </div>
 
    <!-- Aviso -->
    <?php if (!empty($recibo['esQR'])): ?>
    <div class="alert alert-warning text-center" style="border-radius:12px;">
        <i class="fas fa-hourglass-half me-2"></i>
        Tu comprobante está <strong>pendiente de verificación</strong>. Te notificaremos por email cuando sea aprobado.
    </div>
    <?php else: ?>
    <div class="alert alert-info text-center" style="border-radius:12px;">
        <i class="fas fa-info-circle me-2"></i>
        Presenta este número de recibo en recepción al hacer tu check-in.
    </div>
    <?php endif; ?>
 
    <!-- Acciones -->
    <div class="d-flex gap-3 justify-content-center mt-4 flex-wrap">
        <?php if (!empty($recibo['idRecibo']) && empty($recibo['esQR'])): ?>
        <a href="<?= url('recibo/ver?id=' . $recibo['idRecibo']) ?>"
           target="_blank" class="btn btn-success px-4">
            <i class="fas fa-file-pdf me-2"></i>Ver recibo / Imprimir
        </a>
        <?php endif; ?>
        <a href="<?= url('cliente/reservas') ?>" class="btn btn-primary px-4">
            <i class="fas fa-calendar-check me-2"></i>Ver mis reservas
        </a>
        <a href="<?= url('cliente/dashboard') ?>" class="btn btn-outline-secondary px-4">
            <i class="fas fa-home me-2"></i>Mi panel
        </a>
    </div>
 
</div>