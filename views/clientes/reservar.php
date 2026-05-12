<style>
.reservar-card { border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); }
.resumen-hab { background:linear-gradient(135deg,#1a1a2e,#0f3460); color:#fff; border-radius:16px; padding:24px; }
.precio-calculo { background:#f8f9fa; border-radius:12px; padding:16px; }
</style>

<div style="max-width:900px;margin:0 auto;padding:30px 20px;">
    <nav class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= url('habitaciones') ?>">Habitaciones</a></li>
            <li class="breadcrumb-item active">Reservar</li>
        </ol>
    </nav>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="reservar-card p-4">
                <h4 class="fw-bold mb-1"><i class="fas fa-calendar-plus me-2 text-primary"></i>Completa tu reserva</h4>
                <p class="text-muted small mb-4">Selecciona tus fechas y cantidad de huéspedes</p>

                <form action="<?= url('reservar') ?>" method="POST">
                    <input type="hidden" name="idHabitacion" value="<?= $habitacion['idHabitacion'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de entrada *</label>
                            <input type="date" name="fechaInicio" id="fechaInicio" class="form-control"
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fecha de salida *</label>
                            <input type="date" name="fechaFin" id="fechaFin" class="form-control"
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cantidad de personas *</label>
                            <select name="cantidadPersonas" class="form-select" required>
                                <option value="1">1 persona</option>
                                <option value="2" selected>2 personas</option>
                                <option value="3">3 personas</option>
                                <option value="4">4 personas</option>
                            </select>
                        </div>
                    </div>

                    <div class="precio-calculo mt-4" id="calculoBox" style="display:none;">
                        <h6 class="fw-bold mb-3"><i class="fas fa-calculator me-2 text-primary"></i>Resumen de precio</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Precio por noche</span>
                            <span>Bs. <?= number_format($habitacion['precio'], 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Noches</span>
                            <span id="numNoches">—</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total estimado</span>
                            <span id="totalPrecio" style="color:#0f3460;font-size:1.1rem;">—</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 mt-4" style="border-radius:10px;">
                        <i class="fas fa-check-circle me-2"></i>Confirmar reserva
                    </button>
                    <p class="text-muted text-center small mt-2">
                        <i class="fas fa-shield-alt me-1"></i>Tu reserva quedará <strong>Pendiente</strong> hasta ser confirmada por recepción.
                    </p>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="resumen-hab mb-3">
                <p style="color:#a0b4d0;font-size:.75rem;letter-spacing:.15em;text-transform:uppercase;margin-bottom:4px;">
                    <?= htmlspecialchars($habitacion['tipo']) ?>
                </p>
                <h4 class="fw-bold mb-1">Habitación <?= htmlspecialchars($habitacion['numero']) ?></h4>
                <p style="color:#a0b4d0;margin-bottom:16px;">Piso <?= $habitacion['piso'] ?></p>
                <div style="font-size:1.6rem;font-weight:700;">
                    Bs. <?= number_format($habitacion['precio'], 2) ?>
                    <span style="font-size:.9rem;font-weight:400;color:#a0b4d0;"> / noche</span>
                </div>
            </div>

            <div class="reservar-card p-4">
                <h6 class="fw-bold mb-3">Políticas de reserva</h6>
                <div class="d-flex align-items-start gap-2 mb-2">
                    <i class="fas fa-check-circle text-success mt-1"></i>
                    <span class="small text-muted">Cancelación gratuita hasta 24h antes del check-in</span>
                </div>
                <div class="d-flex align-items-start gap-2 mb-2">
                    <i class="fas fa-check-circle text-success mt-1"></i>
                    <span class="small text-muted">Check-in: 14:00 hrs · Check-out: 12:00 hrs</span>
                </div>
                <div class="d-flex align-items-start gap-2 mb-2">
                    <i class="fas fa-check-circle text-success mt-1"></i>
                    <span class="small text-muted">Pago al momento del check-in en recepción</span>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <i class="fas fa-info-circle text-primary mt-1"></i>
                    <span class="small text-muted">La reserva se confirma en un plazo de 2 horas hábiles</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const precioNoche = <?= $habitacion['precio'] ?>;
const inEl  = document.getElementById('fechaInicio');
const outEl = document.getElementById('fechaFin');

function calcular() {
    const entrada = inEl.value;
    const salida  = outEl.value;
    if (!entrada || !salida) return;
    const diff = Math.round((new Date(salida) - new Date(entrada)) / 86400000);
    if (diff <= 0) { document.getElementById('calculoBox').style.display='none'; return; }
    document.getElementById('numNoches').textContent  = diff + ' noche' + (diff !== 1 ? 's' : '');
    document.getElementById('totalPrecio').textContent = 'Bs. ' + (diff * precioNoche).toFixed(2);
    document.getElementById('calculoBox').style.display = 'block';
    outEl.min = new Date(new Date(entrada).getTime() + 86400000).toISOString().split('T')[0];
}

inEl.addEventListener('change', calcular);
outEl.addEventListener('change', calcular);
</script>