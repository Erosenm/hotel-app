<link rel="stylesheet" href="<?= url('public/css/cssAdmin/stylereserva/crear.css') ?>">

<div class="res-header d-flex justify-content-between align-items-center">
    <div class="res-title">
        <i class="fas fa-calendar-plus"></i>
        <span>Nueva Reserva</span>
    </div>
    <a href="<?= url('admin/reservas') ?>" class="res-btn-back">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="res-alert res-alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<form method="POST" action="<?= url('admin/reservas/crear') ?>" id="formReserva">
    <div class="row g-5">
        
        <!-- ── CLIENTE ── -->
        <div class="col-12">
            <div class="res-card">
                <div class="res-card-header">
                    <i class="fas fa-user me-2"></i>
                    Información del Cliente
                </div>
                <div class="res-card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="res-label">Buscar por CI</label>
                            <div class="res-input-group">
                                <input type="text" id="buscarCI" class="res-input" placeholder="Ej: 12345678">
                                <button type="button" class="res-btn-search" onclick="buscarCliente()">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="res-separator">
                                <div class="res-separator-line"></div>
                                <span class="res-separator-text">O</span>
                                <div class="res-separator-line"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-5">
                            <label class="res-label">Seleccionar de la lista</label>
                            <select class="res-select w-100" id="selectCliente" onchange="seleccionarCliente(this)">
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
                    <div id="infoCliente" class="res-cliente-info d-none">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="res-cliente-info-item">
                                    <span class="res-cliente-info-label">Nombre completo</span>
                                    <span class="res-cliente-info-value" id="clienteNombre">—</span>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="res-cliente-info-item">
                                    <span class="res-cliente-info-label">CI</span>
                                    <span class="res-cliente-info-value" id="clienteCI">—</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="res-cliente-info-item">
                                    <span class="res-cliente-info-label">Email</span>
                                    <span class="res-cliente-info-value" id="clienteEmail">—</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="res-cliente-info-item">
                                    <span class="res-cliente-info-label">Teléfono</span>
                                    <span class="res-cliente-info-value" id="clienteTelefono">—</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="clienteNoEncontrado" class="res-alert res-alert-danger d-none">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cliente no encontrado. <a href="<?= url('admin/usuarios/crear') ?>" class="fw-bold">¿Crear nuevo cliente?</a>
                    </div>

                    <input type="hidden" name="idCliente" id="idCliente">
                </div>
            </div>
        </div>

        <!-- ── HABITACIÓN Y FECHAS ── -->
        <div class="col-md-6">
            <div class="res-card">
                <div class="res-card-header">
                    <i class="fas fa-bed me-2"></i>
                    Seleccionar Habitación
                </div>
                <div class="res-card-body">
                    <?php if (empty($habitaciones)): ?>
                        <div class="res-alert res-alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No hay habitaciones disponibles en este momento.
                        </div>
                    <?php else: ?>
                        <div class="res-habitaciones-grid">
                            <?php foreach ($habitaciones as $h): ?>
                                <div class="res-habitacion-card" onclick="seleccionarHabitacion(this, <?= $h['idHabitacion'] ?>, <?= $h['precioBase'] ?>, '<?= htmlspecialchars($h['tipo']) ?>')">
                                    <input type="radio" name="idHabitacion" id="hab<?= $h['idHabitacion'] ?>"
                                           value="<?= $h['idHabitacion'] ?>"
                                           data-precio="<?= $h['precioBase'] ?>"
                                           data-tipo="<?= htmlspecialchars($h['tipo']) ?>"
                                           style="display:none;">
                                    <div class="res-habitacion-numero">N° <?= htmlspecialchars($h['numero']) ?></div>
                                    <div class="res-habitacion-piso">Piso <?= $h['piso'] ?></div>
                                    <div class="res-habitacion-tipo"><?= htmlspecialchars($h['tipo']) ?></div>
                                    <div class="res-habitacion-precio">Bs. <?= number_format($h['precioBase'], 0) ?>/noche</div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="res-card">
                <div class="res-card-header">
                    <i class="fas fa-calendar me-2"></i>
                    Fechas y Personas
                </div>
                <div class="res-card-body">
                    <div class="mb-4">
                        <label class="res-label">Fecha de entrada <span class="text-danger">*</span></label>
                        <input type="date" name="fechaInicio" id="fechaInicio" class="res-input w-100"
                               min="<?= date('Y-m-d') ?>" required onchange="verificarDisponibilidad()">
                    </div>
                    
                    <div class="mb-4">
                        <label class="res-label">Fecha de salida <span class="text-danger">*</span></label>
                        <input type="date" name="fechaFin" id="fechaFin" class="res-input w-100"
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required onchange="verificarDisponibilidad()">
                    </div>
                    
                    <div class="mb-4">
                        <label class="res-label">Cantidad de personas</label>
                        <input type="number" name="cantidadPersonas" class="res-input w-100" min="1" max="10" value="1">
                    </div>

                    <!-- Resumen de precio -->
                    <div id="resumenPrecio" class="d-none">
                        <div id="alertaDisponibilidad" class="res-alerta-disponibilidad"></div>
                        <div class="res-resumen">
                            <div class="res-resumen-item">
                                <span class="text-muted">Habitación</span>
                                <span id="resHab" class="fw-semibold">—</span>
                            </div>
                            <div class="res-resumen-item">
                                <span class="text-muted">Precio por noche</span>
                                <span id="resPrecio" class="fw-semibold">—</span>
                            </div>
                            <div class="res-resumen-item">
                                <span class="text-muted">Noches</span>
                                <span id="resNoches" class="fw-semibold">—</span>
                            </div>
                            <div class="res-resumen-total d-flex justify-content-between">
                                <span>Total a pagar</span>
                                <span id="resTotal" class="fw-bold">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── BOTÓN GUARDAR ── -->
        <div class="col-12 text-end">
            <button type="submit" class="res-btn-primary" id="btnGuardar" disabled>
                <i class="fas fa-calendar-check me-2"></i>
                Crear Reserva
            </button>
            <p class="text-muted small mt-2 mb-0">
                <i class="fas fa-info-circle me-1"></i>
                La reserva quedará en estado <strong class="text-success">Confirmada</strong> automáticamente.
            </p>
        </div>

    </div>
</form>

<script>
let habitacionActual = null;

function seleccionarHabitacion(element, id, precio, tipo) {
    // Remover selección de todas
    document.querySelectorAll('.res-habitacion-card').forEach(card => {
        card.classList.remove('seleccionada');
    });
    
    // Agregar selección a la actual
    element.classList.add('seleccionada');
    
    // Marcar el radio button interno
    const radio = element.querySelector('input[type="radio"]');
    if (radio) {
        radio.checked = true;
        habitacionActual = { id: id, precio: precio, tipo: tipo };
    }
    
    verificarDisponibilidad();
}

function buscarCliente() {
    const ci = document.getElementById('buscarCI').value.trim();
    if (!ci) return;

    fetch('<?= url('admin/reservas/buscar-cliente') ?>?ci=' + encodeURIComponent(ci))
        .then(r => r.json())
        .then(data => {
            if (data && data.idUsuario) {
                mostrarCliente(data);
                document.getElementById('clienteNoEncontrado').classList.add('d-none');
                document.getElementById('selectCliente').value = '';
            } else {
                document.getElementById('infoCliente').classList.add('d-none');
                document.getElementById('clienteNoEncontrado').classList.remove('d-none');
                document.getElementById('idCliente').value = '';
                document.getElementById('selectCliente').value = '';
            }
            validarFormulario();
        })
        .catch(err => {
            console.error('Error buscando cliente:', err);
            document.getElementById('infoCliente').classList.add('d-none');
            document.getElementById('clienteNoEncontrado').classList.remove('d-none');
            document.getElementById('idCliente').value = '';
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
    document.getElementById('clienteNombre').textContent = data.nombre;
    document.getElementById('clienteCI').textContent = data.ci;
    document.getElementById('clienteEmail').textContent = data.email;
    document.getElementById('clienteTelefono').textContent = data.telefono || '—';
    document.getElementById('idCliente').value = data.idUsuario;
    document.getElementById('infoCliente').classList.remove('d-none');
    document.getElementById('clienteNoEncontrado').classList.add('d-none');
    validarFormulario();
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
                alerta.innerHTML = '<div class="res-alerta-success"><i class="fas fa-check-circle me-1"></i>✓ Habitación disponible para las fechas seleccionadas</div>';
                document.getElementById('resHab').textContent = `N° — ${habitacionActual.tipo}`;
                document.getElementById('resPrecio').textContent = `Bs. ${parseFloat(data.precio).toFixed(2)}`;
                document.getElementById('resNoches').textContent = `${data.noches} noche(s)`;
                document.getElementById('resTotal').textContent = `Bs. ${parseFloat(data.total).toFixed(2)}`;
            } else {
                alerta.innerHTML = `<div class="res-alerta-danger"><i class="fas fa-times-circle me-1"></i>${data.mensaje}</div>`;
            }
            validarFormulario(data.disponible);
        });
}

function validarFormulario(disponible = true) {
    const cliente = document.getElementById('idCliente').value;
    const hab = document.querySelector('input[name="idHabitacion"]:checked');
    const fi = document.getElementById('fechaInicio').value;
    const ff = document.getElementById('fechaFin').value;
    const btn = document.getElementById('btnGuardar');
    
    const isReady = !!(cliente && hab && fi && ff && ff > fi && disponible);
    btn.disabled = !isReady;
}

document.getElementById('buscarCI').addEventListener('keydown', e => {
    if (e.key === 'Enter') { 
        e.preventDefault(); 
        buscarCliente(); 
    }
});

// Inicializar tooltips
document.querySelectorAll('.res-habitacion-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.res-habitacion-card').forEach(c => c.classList.remove('seleccionada'));
        this.classList.add('seleccionada');
    });
});
</script>