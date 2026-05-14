<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleUsuarios/CrearUsuario.css') ?>">

<!-- ══ Header ══ -->
<div class="cu-header">
    <div class="cu-title">
        <i class="fas fa-user-plus"></i>
        <span>Nuevo Usuario</span>
    </div>
    <a href="<?= url('admin/usuarios') ?>" class="cu-btn-back">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<!-- ══ Layout: panel ayuda + formulario ══ -->
<div class="cu-layout">

    <!-- ── Panel lateral izquierdo ── -->
    <aside class="cu-sidebar-help">

        <div class="cu-help-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <div class="cu-help-title">Crear usuario</div>
        <div class="cu-help-desc">Completa el formulario para registrar un nuevo usuario en el sistema.</div>

        <div class="cu-help-divider"></div>

        <!-- Pasos -->
        <div class="cu-help-steps">
            <div class="cu-step">
                <div class="cu-step-num">1</div>
                <div class="cu-step-text">
                    <strong>Datos personales</strong>
                    Nombre, apellidos, CI y teléfono.
                </div>
            </div>
            <div class="cu-step">
                <div class="cu-step-num">2</div>
                <div class="cu-step-text">
                    <strong>Acceso</strong>
                    Email, contraseña y rol del usuario.
                </div>
            </div>
            <div class="cu-step">
                <div class="cu-step-num">3</div>
                <div class="cu-step-text">
                    <strong>Empleado</strong>
                    Si es Admin o Recepcionista, aparecen datos adicionales.
                </div>
            </div>
        </div>

        <div class="cu-help-divider"></div>

        <!-- Roles disponibles -->
        <div class="cu-help-subtitle"><i class="fas fa-user-shield"></i> Roles del sistema</div>
        <div class="cu-role-pill admin"><i class="fas fa-crown"></i> Administrador</div>
        <div class="cu-role-pill recep"><i class="fas fa-concierge-bell"></i> Recepcionista</div>
        <div class="cu-role-pill client"><i class="fas fa-user"></i> Cliente</div>

        <div class="cu-help-divider"></div>

        <div class="cu-help-note">
            <i class="fas fa-info-circle"></i>
            El CI y el email serán fijos una vez creado el usuario. No podrán modificarse.
        </div>

    </aside>

    <!-- ── Card del formulario ══ -->
    <div class="cu-card">

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="cu-alert error">
                <i class="fas fa-exclamation-circle"></i>
                <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="<?= url('admin/usuarios/crear') ?>" method="POST">
            <div class="cu-form">

                <!-- ── Sección: Datos personales ── -->
                <div class="cu-section-label">
                    <i class="fas fa-id-card"></i> Datos personales
                </div>

                <div class="cu-row-3">
                    <div class="cu-field">
                        <label class="cu-label">Nombre <span class="cu-req">*</span></label>
                        <input type="text" name="nombre" class="cu-input" placeholder="Nombre" required>
                    </div>
                    <div class="cu-field">
                        <label class="cu-label">Apellido Paterno <span class="cu-req">*</span></label>
                        <input type="text" name="paterno" class="cu-input" placeholder="Apellido Paterno" required>
                    </div>
                    <div class="cu-field">
                        <label class="cu-label">Apellido Materno</label>
                        <input type="text" name="materno" class="cu-input" placeholder="Apellido Materno">
                    </div>
                </div>

                <div class="cu-row-2">
                    <div class="cu-field">
                        <label class="cu-label">CI <span class="cu-req">*</span></label>
                        <div class="cu-input-icon">
                            <i class="fas fa-id-badge"></i>
                            <input type="text" name="ci" class="cu-input has-icon" placeholder="Cédula de Identidad" required>
                        </div>
                    </div>
                    <div class="cu-field">
                        <label class="cu-label">Teléfono</label>
                        <div class="cu-input-icon">
                            <i class="fas fa-phone"></i>
                            <input type="text" name="telefono" class="cu-input has-icon" placeholder="Teléfono">
                        </div>
                    </div>
                </div>

                <!-- ── Sección: Acceso ── -->
                <div class="cu-section-label" style="margin-top:.5rem">
                    <i class="fas fa-lock"></i> Acceso al sistema
                </div>

                <div class="cu-row-1">
                    <div class="cu-field">
                        <label class="cu-label">Email <span class="cu-req">*</span></label>
                        <div class="cu-input-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" class="cu-input has-icon" placeholder="Correo electrónico" required>
                        </div>
                    </div>
                </div>

                <div class="cu-row-2">
                    <div class="cu-field">
                        <label class="cu-label">Contraseña <span class="cu-req">*</span></label>
                        <div class="cu-input-icon">
                            <i class="fas fa-key"></i>
                            <input type="password" name="password" id="passwordInput"
                                   class="cu-input has-icon" placeholder="Contraseña" required>
                        </div>
                        <div class="cu-strength-wrap">
                            <div class="cu-strength-track">
                                <div id="strengthBar" class="cu-strength-bar"></div>
                            </div>
                            <small id="strengthText" class="cu-strength-text"></small>
                        </div>
                    </div>
                    <div class="cu-field">
                        <label class="cu-label">Rol <span class="cu-req">*</span></label>
                        <div class="cu-input-icon">
                            <i class="fas fa-user-shield"></i>
                            <select name="rol" id="selectRol" class="cu-input cu-select has-icon" required>
                                <option value="">Seleccionar rol</option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['idRol'] ?>" data-nombre="<?= htmlspecialchars($r['nombre']) ?>">
                                        <?= htmlspecialchars($r['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ── Sección empleado (condicional) ── -->
                <div id="seccionEmpleado" style="display:none;">
                    <div class="cu-divider"></div>
                    <div class="cu-section-label">
                        <i class="fas fa-briefcase"></i> Datos del Empleado
                    </div>
                    <div class="cu-row-3">
                        <div class="cu-field">
                            <label class="cu-label">Cargo</label>
                            <input type="text" name="cargo" id="cargo" class="cu-input" placeholder="Ej: Recepcionista">
                        </div>
                        <div class="cu-field">
                            <label class="cu-label">Fecha de contratación</label>
                            <input type="date" name="fechaContratacion" id="fechaContratacion"
                                   class="cu-input" max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="cu-field">
                            <label class="cu-label">Salario (Bs.)</label>
                            <div class="cu-input-icon">
                                <i class="fas fa-coins"></i>
                                <input type="number" name="salario" id="salario"
                                       class="cu-input has-icon" placeholder="3500.00" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ── Submit ── -->
                <div class="cu-submit-row">
                    <button type="submit" class="cu-btn-submit">
                        <i class="fas fa-save"></i> Guardar Usuario
                    </button>
                    <a href="<?= url('admin/usuarios') ?>" class="cu-btn-cancel">
                        Cancelar
                    </a>
                </div>

            </div>
        </form>
    </div>

</div><!-- /cu-layout -->

<script>
// Mostrar/ocultar sección empleado según el rol
const selectRol       = document.getElementById('selectRol');
const seccionEmpleado = document.getElementById('seccionEmpleado');
const cargo           = document.getElementById('cargo');
const fechaC          = document.getElementById('fechaContratacion');
const salario         = document.getElementById('salario');

const rolesEmpleado = ['Administrador', 'Recepcionista'];

selectRol.addEventListener('change', function () {
    const nombreRol  = this.options[this.selectedIndex].dataset.nombre ?? '';
    const esEmpleado = rolesEmpleado.includes(nombreRol);
    seccionEmpleado.style.display = esEmpleado ? 'block' : 'none';
    cargo.required    = esEmpleado;
    fechaC.required   = esEmpleado;
    salario.required  = esEmpleado;
});

// Fuerza de contraseña
const input  = document.getElementById('passwordInput');
const bar    = document.getElementById('strengthBar');
const text   = document.getElementById('strengthText');
const levels = [
    { label:'Muy débil',  color:'#e74c3c', width:'20%'  },
    { label:'Débil',      color:'#e67e22', width:'40%'  },
    { label:'Regular',    color:'#f59e0b', width:'60%'  },
    { label:'Fuerte',     color:'#10b981', width:'80%'  },
    { label:'Muy fuerte', color:'#059669', width:'100%' },
];
input.addEventListener('input', function () {
    const val = input.value;
    if (!val) { bar.style.width = '0%'; text.textContent = ''; return; }
    let score = 0;
    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[a-z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val))  score++;
    const level          = levels[score - 1] ?? levels[0];
    bar.style.width      = level.width;
    bar.style.background = level.color;
    text.textContent     = level.label;
    text.style.color     = level.color;
});
</script>