<link rel="stylesheet" href="<?= asset('css/cssCliente/clientereservar.css') ?>">

<div class="reservar-container">
    <!-- Breadcrumb -->
    <nav class="reservar-breadcrumb">
        <ol>
            <li><a href="<?= url('/') ?>">Inicio</a></li>
            <li><a href="<?= url('habitaciones') ?>">Habitaciones</a></li>
            <li class="active">Reservar</li>
        </ol>
    </nav>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="reservar-alert reservar-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= $_SESSION['error'] ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Columna izquierda: Formulario -->
        <div class="col-lg-7">
            <div class="reservar-card">
                <h4 class="reservar-card-titulo">
                    <i class="fas fa-calendar-plus"></i> Completa tu reserva
                </h4>
                <p class="reservar-card-sub">Selecciona tus fechas y cantidad de huéspedes</p>

                <form action="<?= url('reservar') ?>" method="POST">
                    <input type="hidden" name="idHabitacion" value="<?= $habitacion['idHabitacion'] ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="reservar-form-group">
                                <label>Fecha de entrada <span class="required">*</span></label>
                                <input type="date" name="fechaInicio" id="fechaInicio" class="form-control"
                                       min="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="reservar-form-group">
                                <label>Fecha de salida <span class="required">*</span></label>
                                <input type="date" name="fechaFin" id="fechaFin" class="form-control"
                                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="reservar-form-group">
                                <label>Cantidad de personas <span class="required">*</span></label>
                                <select name="cantidadPersonas" class="form-select" required>
                                    <option value="1">1 persona</option>
                                    <option value="2" selected>2 personas</option>
                                    <option value="3">3 personas</option>
                                    <option value="4">4 personas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de precio -->
                    <div class="reservar-resumen" id="calculoBox">
                        <div class="reservar-resumen-titulo">
                            <i class="fas fa-calculator"></i> Resumen de precio
                        </div>
                        <div class="reservar-resumen-item">
                            <span>Precio por noche</span>
                            <span class="valor">Bs. <?= number_format($habitacion['precio'], 2) ?></span>
                        </div>
                        <div class="reservar-resumen-item">
                            <span>Noches</span>
                            <span class="valor" id="numNoches">—</span>
                        </div>
                        <hr class="reservar-resumen-divider">
                        <div class="reservar-resumen-total">
                            <span>Total estimado</span>
                            <span class="valor" id="totalPrecio">—</span>
                        </div>
                    </div>

                    <button type="submit" class="reservar-btn-primary">
                        <i class="fas fa-check-circle"></i> Confirmar reserva
                    </button>
                    <p class="reservar-nota">
                        <i class="fas fa-shield-alt"></i>
                        Tu reserva quedará en estado <strong>Pendiente</strong> hasta ser confirmada por recepción.
                    </p>
                </form>
            </div>
        </div>

        <!-- Columna derecha: Resumen y políticas -->
        <div class="col-lg-5">
            <div class="reservar-hab-resumen">
                <div class="tipo"><?= htmlspecialchars($habitacion['tipo']) ?></div>
                <div class="nombre">Habitación <?= htmlspecialchars($habitacion['numero']) ?></div>
                <div class="piso">Piso <?= $habitacion['piso'] ?></div>
                <div class="precio">
                    Bs. <?= number_format($habitacion['precio'], 2) ?>
                    <span>/ noche</span>
                </div>
            </div>

            <div class="reservar-politicas">
                <div class="reservar-politicas-titulo">
                    <i class="fas fa-file-contract" style="color: var(--reservar-gold); margin-right: 0.4rem;"></i>
                    Políticas de reserva
                </div>
                <div class="reservar-politica-item">
                    <i class="fas fa-check-circle success"></i>
                    Cancelación gratuita hasta 24h antes del check-in
                </div>
                <div class="reservar-politica-item">
                    <i class="fas fa-check-circle success"></i>
                    Check-in: 14:00 hrs · Check-out: 12:00 hrs
                </div>
                <div class="reservar-politica-item">
                    <i class="fas fa-check-circle success"></i>
                    Pago al momento del check-in en recepción
                </div>
                <div class="reservar-politica-item">
                    <i class="fas fa-info-circle info"></i>
                    La reserva se confirma en un plazo de 2 horas hábiles
                </div>
            </div>
        </div>
    </div>

    <!-- Decoración final -->
    <div class="reservar-deco">
        <span class="reservar-deco-line">
            <i class="fas fa-hotel"></i>
            Hotel Real Plaza & Convention Center
            <i class="fas fa-hotel"></i>
        </span>
    </div>
</div>

<script>
const precioNoche = <?= $habitacion['precio'] ?>;
const inEl  = document.getElementById('fechaInicio');
const outEl = document.getElementById('fechaFin');
const calcBox = document.getElementById('calculoBox');
const numNoches = document.getElementById('numNoches');
const totalPrecio = document.getElementById('totalPrecio');

function calcular() {
    const entrada = inEl.value;
    const salida  = outEl.value;
    if (!entrada || !salida) {
        calcBox.classList.remove('visible');
        return;
    }
    const diff = Math.round((new Date(salida) - new Date(entrada)) / 86400000);
    if (diff <= 0) { 
        calcBox.classList.remove('visible');
        return; 
    }
    numNoches.textContent = diff + ' noche' + (diff !== 1 ? 's' : '');
    totalPrecio.textContent = 'Bs. ' + (diff * precioNoche).toFixed(2);
    calcBox.classList.add('visible');
    
    // Actualizar min de fecha de salida
    const minOut = new Date(new Date(entrada).getTime() + 86400000);
    outEl.min = minOut.toISOString().split('T')[0];
}

inEl.addEventListener('change', calcular);
outEl.addEventListener('change', calcular);
</script>