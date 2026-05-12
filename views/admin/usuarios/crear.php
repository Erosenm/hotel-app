<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="fas fa-user-plus me-2 text-primary"></i>Nuevo Usuario</h4>
    <a href="<?= url('admin/usuarios') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-body p-4">

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="<?= url('admin/usuarios/crear') ?>" method="POST">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Paterno *</label>
                    <input type="text" name="paterno" class="form-control" placeholder="Apellido Paterno" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Materno</label>
                    <input type="text" name="materno" class="form-control" placeholder="Apellido Materno">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">CI *</label>
                    <input type="text" name="ci" class="form-control" placeholder="Cédula de Identidad" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="telefono" class="form-control" placeholder="Teléfono">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Email *</label>
                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contraseña *</label>
                    <input type="password" name="password" id="passwordInput"
                           class="form-control" placeholder="Contraseña" required>
                    <div style="margin-top:8px;">
                        <div style="height:6px;border-radius:10px;background:#e0e0e0;overflow:hidden;">
                            <div id="strengthBar" style="height:100%;width:0%;border-radius:10px;transition:width .4s,background .4s;"></div>
                        </div>
                        <small id="strengthText" style="font-size:.75rem;margin-top:4px;display:block;font-weight:600;"></small>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Rol *</label>
                    <select name="rol" id="selectRol" class="form-select" required>
                        <option value="">Seleccionar rol</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['idRol'] ?>" data-nombre="<?= htmlspecialchars($r['nombre']) ?>">
                                <?= htmlspecialchars($r['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- CAMPOS EMPLEADO — se muestran solo si es Administrador o Recepcionista -->
                <div id="seccionEmpleado" style="display:none;" class="col-12">
                    <hr>
                    <h6 class="fw-bold text-muted mb-3" style="font-size:.75rem;letter-spacing:.1em;text-transform:uppercase;">
                        <i class="fas fa-id-badge me-1 text-primary"></i>Datos del Empleado
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cargo</label>
                            <input type="text" name="cargo" id="cargo" class="form-control" placeholder="Ej: Recepcionista">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha de contratación</label>
                            <input type="date" name="fechaContratacion" id="fechaContratacion"
                                   class="form-control" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Salario (Bs.)</label>
                            <input type="number" name="salario" id="salario"
                                   class="form-control" placeholder="Ej: 3500.00" step="0.01" min="0">
                        </div>
                    </div>
                </div>

                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Guardar Usuario
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
// Mostrar/ocultar sección empleado según el rol
const selectRol      = document.getElementById('selectRol');
const seccionEmpleado= document.getElementById('seccionEmpleado');
const cargo          = document.getElementById('cargo');
const fechaC         = document.getElementById('fechaContratacion');
const salario        = document.getElementById('salario');

const rolesEmpleado = ['Administrador', 'Recepcionista'];

selectRol.addEventListener('change', function () {
    const nombreRol = this.options[this.selectedIndex].dataset.nombre ?? '';
    const esEmpleado = rolesEmpleado.includes(nombreRol);

    seccionEmpleado.style.display = esEmpleado ? 'block' : 'none';
    cargo.required          = esEmpleado;
    fechaC.required         = esEmpleado;
    salario.required        = esEmpleado;
});

// Fuerza de contraseña
const input = document.getElementById('passwordInput');
const bar   = document.getElementById('strengthBar');
const text  = document.getElementById('strengthText');
const levels = [
    { label:'Muy débil',  color:'#e74c3c', width:'20%'  },
    { label:'Débil',      color:'#e67e22', width:'40%'  },
    { label:'Regular',    color:'#f1c40f', width:'60%'  },
    { label:'Fuerte',     color:'#2ecc71', width:'80%'  },
    { label:'Muy fuerte', color:'#27ae60', width:'100%' },
];
input.addEventListener('input', function () {
    const val = input.value;
    if (!val) { bar.style.width='0%'; text.textContent=''; return; }
    let score = 0;
    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[a-z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;
    const level = levels[score-1] ?? levels[0];
    bar.style.width      = level.width;
    bar.style.background = level.color;
    text.textContent     = level.label;
    text.style.color     = level.color;
});
</script>