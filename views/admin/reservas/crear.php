<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-calendar-plus me-2 text-primary"></i>Nueva Reserva
    </h4>
    <a href="<?= url('admin/reservas') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Volver
    </a>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form method="POST" action="<?= url('admin/reservas/crear') ?>" id="formReserva">
<div class="row g-4">

    <!-- ── CLIENTE ── -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold" style="background:#f8f9fa;">
                <i class="fas fa-user me-2 text-primary"></i>Cliente
            </div>
            <div class="card-body">

                <!-- Buscar por CI -->
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Buscar por CI</label>
                        <div class="input-group">
                            <input type="text" id="buscarCI" class="form-control" placeholder="Ej: 12345678">
                            <button type="button" class="btn btn-primary" onclick="buscarCliente()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-1 text-center text-muted fw-semibold">ó</div>
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Seleccionar de la lista</label>
                        <select class="form-select" id="selectCliente" onchange="seleccionarCliente(this)">
                            <option value="">— Elegir cliente —</option>
                            <?php foreach ($clientes as $c): ?>
                                <option value="<?= $c['idUsuario'] ?>"
                                    data-nombre="<?= htmlspecialchars($c['nombre'] . ' ' . $c['paterno']) ?>"
                                    data-ci="<?= htmlspecialchars($c['ci']) ?>"
                                    data-email="<?= htmlspecialchars($c['email']) ?>"
                                    data-telefono="<?= htmlspecialchars($c['telefono'] ?? '') ?>">
                                    <?= htmlspecialchars($c['paterno'] . ' ' . $c['nombre']) ?> — CI: <?= htmlspecialchars($c['ci']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Info cliente encontrado -->
                <div id="infoCliente" class="alert alert-info d-none">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <span class="text-muted small">Nombre</span>
                            <div class="fw-semibold" id="clienteNombre">—</div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted small">CI</span>
                            <div class="fw-semibold" id="clienteCI">—</div>
                        </div>
                        <div class="col-md-3">
                            <span class="text-muted small">Email</span>
                            <div class="fw-semibold" id="clienteEmail">—</div>
                        </div>
                        <div class="col-md-2">
                            <span class="text-muted small">Teléfono</span>
                            <div class="fw-semibold" id="clienteTelefono">—</div>
                        </div>
                    </div>
                </div>

                <div id="clienteNoEncontrado" class="alert alert-warning d-none">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cliente no encontrado. <a href="<?= url('admin/usuarios/crear') ?>">¿Crear nuevo cliente?</a>
                </div>

                <input type="hidden" name="idCliente" id="idCliente">
            </div>
        </div>
    </div>

    <!-- ── HABITACIÓN ── -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#f8f9fa;">
                <i class="fas fa-bed me-2 text-primary"></i>Habitación
            </div>
            <div class="card-body">
                <?php if (empty($habitaciones)): ?>
                    <div class="alert alert-warning">No hay habitaciones disponibles en este momento.</div>
                <?php else: ?>
                <div class="row g-2">
                    <?php foreach ($habitaciones as $h): ?>
                    <div class="col-6 col-md-4">
                        <label class="card border rounded p-2 text-center w-100 habitacion-card" style="cursor:pointer;" for="hab<?= $h['idHabitacion'] ?>">
                            <input type="radio" name="idHabitacion" id="hab<?= $h['idHabitacion'] ?>"
                                   value="<?= $h['idHabitacion'] ?>"
                                   data-precio="<?= $h['precioBase'] ?>"
                                   data-tipo="<?= htmlspecialchars($h['tipo']) ?>"
                                   style="display:none;"
                                   onchange="habitacionSeleccionada(this)">
                            <div class="fw-bold fs-5 text-primary">N° <?= htmlspecialchars($h['numero']) ?></div>
                            <div class="text-muted small">Piso <?= $h['piso'] ?></div>
                            <div class="badge bg-info mt-1"><?= htmlspecialchars($h['tipo']) ?></div>
                            <div class="fw-semibold text-success mt-1">Bs. <?= number_format($h['precioBase'], 0) ?>/noche</div>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── FECHAS Y PERSONAS ── -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold" style="background:#f8f9fa;">
                <i class="fas fa-calendar me-2 text-primary"></i>Fechas y personas
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Fecha de entrada <span class="text-danger">*</span></label>
                    <input type="date" name="fechaInicio" id="fechaInicio" class="form-control"
                           min="<?= date('Y-m-d') ?>" required onchange="verificarDisponibilidad()">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Fecha de salida <span class="text-danger">*</span></label>
                    <input type="date" name="fechaFin" id="fechaFin" class="form-control"
                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required onchange="verificarDisponibilidad()">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Cantidad de personas</label>
                    <input type="number" name="cantidadPersonas" class="form-control" min="1" max="10" value="1">
                </div>

                <!-- Resumen de precio -->
                <div id="resumenPrecio" class="d-none">
                    <hr>
                    <div id="alertaDisponibilidad"></div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Habitación</span>
                        <span id="resHab">—</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Precio/noche</span>
                        <span id="resPrecio">—</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Noches</span>
                        <span id="resNoches">—</span>
                    </div>
                    <div class="d-flex justify-content-between mt-2 pt-2 border-top fw-bold fs-5">
                        <span>Total</span>
                        <span id="resTotal" class="text-primary">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── BOTÓN GUARDAR ── -->
    <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary btn-lg px-5" id="btnGuardar" disabled>
            <i class="fas fa-calendar-check me-2"></i>Crear Reserva
        </button>
        <p class="text-muted small mt-2">La reserva quedará en estado <strong>Confirmada</strong> automáticamente.</p>
    </div>

</div>
</form>

<style>
.habitacion-card.seleccionada { border-color: #0d6efd !important; background: #e8f0ff; }
.habitacion-card:hover { border-color: #0d6efd; }
</style>

<script>
let habitacionActual = null;

function buscarCliente() {
    const ci = document.getElementById('buscarCI').value.trim();
    if (!ci) return;

    fetch('<?= url('admin/reservas/buscar-cliente') ?>?ci=' + encodeURIComponent(ci))
        .then(r => r.json())
        .then(data => {
            if (data) {
                mostrarCliente(data);
                document.getElementById('clienteNoEncontrado').classList.add('d-none');
            } else {
                document.getElementById('infoCliente').classList.add('d-none');
                document.getElementById('clienteNoEncontrado').classList.remove('d-none');
                document.getElementById('idCliente').value = '';
            }
            validarFormulario();
        });
}

function seleccionarCliente(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    mostrarCliente({
        idUsuario: opt.value,
        nombre: opt.dataset.nombre,
        ci: opt.dataset.ci,
        email: opt.dataset.email,
        telefono: opt.dataset.telefono
    });
}

function mostrarCliente(data) {
    document.getElementById('clienteNombre').textContent   = data.nombre + (data.paterno ? ' ' + data.paterno : '') || data.nombre;
    document.getElementById('clienteCI').textContent       = data.ci;
    document.getElementById('clienteEmail').textContent    = data.email;
    document.getElementById('clienteTelefono').textContent = data.telefono || '—';
    document.getElementById('idCliente').value             = data.idUsuario;
    document.getElementById('infoCliente').classList.remove('d-none');
    document.getElementById('clienteNoEncontrado').classList.add('d-none');
    validarFormulario();
}

function habitacionSeleccionada(radio) {
    habitacionActual = { id: radio.value, precio: radio.dataset.precio, tipo: radio.dataset.tipo };
    document.querySelectorAll('.habitacion-card').forEach(c => c.classList.remove('seleccionada'));
    radio.closest('.habitacion-card').classList.add('seleccionada');
    verificarDisponibilidad();
}

function verificarDisponibilidad() {
    if (!habitacionActual) return;
    const fi = document.getElementById('fechaInicio').value;
    const ff = document.getElementById('fechaFin').value;
    if (!fi || !ff || ff <= fi) return;

    document.getElementById('resumenPrecio').classList.remove('d-none');

    fetch(`<?= url('admin/reservas/disponibilidad') ?>?idHabitacion=${habitacionActual.id}&fechaInicio=${fi}&fechaFin=${ff}`)
        .then(r => r.json())
        .then(data => {
            const alerta = document.getElementById('alertaDisponibilidad');
            if (data.disponible) {
                alerta.innerHTML = '<div class="alert alert-success py-2"><i class="fas fa-check-circle me-1"></i>Disponible</div>';
                document.getElementById('resHab').textContent    = 'N° — ' + habitacionActual.tipo;
                document.getElementById('resPrecio').textContent = 'Bs. ' + parseFloat(data.precio).toFixed(2);
                document.getElementById('resNoches').textContent = data.noches + ' noche(s)';
                document.getElementById('resTotal').textContent  = 'Bs. ' + parseFloat(data.total).toFixed(2);
            } else {
                alerta.innerHTML = '<div class="alert alert-danger py-2"><i class="fas fa-times-circle me-1"></i>' + data.mensaje + '</div>';
            }
            validarFormulario(data.disponible);
        });
}

function validarFormulario(disponible = true) {
    const cliente = document.getElementById('idCliente').value;
    const hab     = document.querySelector('input[name="idHabitacion"]:checked');
    const fi      = document.getElementById('fechaInicio').value;
    const ff      = document.getElementById('fechaFin').value;
    document.getElementById('btnGuardar').disabled = !(cliente && hab && fi && ff && ff > fi && disponible);
}

document.getElementById('buscarCI').addEventListener('keydown', e => {
    if (e.key === 'Enter') { e.preventDefault(); buscarCliente(); }
});
</script>