<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleUsuarios/CrearUsuario.css') ?>">

<?php
$roles = $roles ?? [];
?>

<!-- HEADER -->
<div class="cu-header">
    <div class="cu-title">
        <i class="fas fa-user-plus"></i>
        Nuevo Usuario
    </div>
    <a href="<?= url('admin/usuarios') ?>" class="cu-btn-back">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>

</div>
<!-- LAYOUT -->
<div class="cu-layout">

    <!-- SIDEBAR -->
    <aside class="cu-sidebar-help">
        <div class="cu-help-icon">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="cu-help-title">
            Registro de usuarios
        </div>
        <div class="cu-help-desc">
            Completa correctamente los datos
            para crear un nuevo usuario.
        </div>
        <div class="cu-help-divider"></div>
        <div class="cu-help-subtitle">
            <i class="fas fa-list-check"></i>
            Pasos
        </div>
        <div class="cu-help-steps">
            <div class="cu-step">
                <div class="cu-step-num">1</div>
                <div class="cu-step-text">
                    <strong>Datos personales</strong>
                    Ingresa nombre, apellidos y CI.
                </div>
            </div>
            <div class="cu-step">
                <div class="cu-step-num">2</div>
                <div class="cu-step-text">
                    <strong>Acceso</strong>
                    Configura email y contraseña.
                </div>
            </div>
            <div class="cu-step">
                <div class="cu-step-num">3</div>

                <div class="cu-step-text">
                    <strong>Rol</strong>
                    Selecciona permisos del usuario.
                </div>
            </div>
        </div>
        <div class="cu-help-divider"></div>
        <div class="cu-help-subtitle">
            <i class="fas fa-user-tag"></i>
            Roles comunes
        </div>
        <div class="cu-role-pill admin">
            <i class="fas fa-crown"></i>
            Administrador
        </div>
        <div class="cu-role-pill recep">
            <i class="fas fa-concierge-bell"></i>
            Recepcionista
        </div>
        <div class="cu-role-pill client">
            <i class="fas fa-user"></i>
            Cliente
        </div>
        <div class="cu-help-note">
            <i class="fas fa-circle-info"></i>
            <span>
                Los empleados requieren cargo,
                fecha de contratación y salario.
            </span>
        </div>
    </aside>

    <!-- CARD -->
    <div class="cu-card">
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="cu-alert error">
                <i class="fas fa-circle-exclamation"></i>
                <span>
                    <?= $_SESSION['error'] ?>
                </span>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- FORM -->
        <form
            action="<?= url('admin/usuarios/crear') ?>"
            method="POST"
            class="cu-form"
        >
            <!-- DATOS PERSONALES -->
            <div>
                <div class="cu-section-label">
                    <i class="fas fa-user"></i>
                    Información personal
                </div>
                <div class="cu-row-3">
                    <!-- NOMBRE -->
                    <div class="cu-field">
                        <label class="cu-label">
                            Nombre
                            <span class="cu-req">*</span>
                        </label>
                        <input
                            type="text"
                            name="nombre"
                            class="cu-input"
                            placeholder="Nombre"
                            required
                        >
                    </div>

                    <!-- PATERNO -->
                    <div class="cu-field">
                        <label class="cu-label">
                            Paterno
                            <span class="cu-req">*</span>
                        </label>
                        <input
                            type="text"
                            name="paterno"
                            class="cu-input"
                            placeholder="Apellido paterno"
                            required
                        >
                    </div>

                    <!-- MATERNO -->
                    <div class="cu-field">
                        <label class="cu-label">
                            Materno
                        </label>
                        <input
                            type="text"
                            name="materno"
                            class="cu-input"
                            placeholder="Apellido materno"
                        >
                    </div>
                </div>
                <div class="cu-row-2">
                    <!-- CI -->
                    <div class="cu-field">
                        <label class="cu-label">
                            CI
                            <span class="cu-req">*</span>
                        </label>
                        <input
                            type="text"
                            name="ci"
                            class="cu-input"
                            placeholder="Cédula de identidad"
                            required
                        >
                    </div>
                    <!-- TEL -->
                    <div class="cu-field">

                        <label class="cu-label">
                            Teléfono
                        </label>
                        <input
                            type="text"
                            name="telefono"
                            class="cu-input"
                            placeholder="Teléfono"
                        >
                    </div>
                </div>
            </div>
            <!-- ACCESO -->
            <div>
                <div class="cu-section-label">
                    <i class="fas fa-lock"></i>
                    Datos de acceso
                </div>
                <div class="cu-row-2">
                    <!-- EMAIL -->
                    <div class="cu-field">
                        <label class="cu-label">
                            Email
                            <span class="cu-req">*</span>
                        </label>
                        <div class="cu-input-icon">
                            <i class="fas fa-envelope"></i>
                            <input
                                type="email"
                                name="email"
                                class="cu-input has-icon"
                                placeholder="correo@ejemplo.com"
                                required
                            >
                        </div>
                    </div>

                    <!-- PASSWORD -->
                    <div class="cu-field">
                        <label class="cu-label">
                            Contraseña
                            <span class="cu-req">*</span>
                        </label>
                        <div class="cu-input-icon">
                            <i class="fas fa-lock"></i>
                            <input
                                type="password"
                                name="password"
                                id="passwordInput"
                                class="cu-input has-icon"
                                placeholder="Contraseña"
                                required
                            >
                        </div>
                        <div class="cu-strength-wrap">
                            <div class="cu-strength-track">
                                <div
                                    id="strengthBar"
                                    class="cu-strength-bar"
                                ></div>
                            </div>
                            <small
                                id="strengthText"
                                class="cu-strength-text"
                            ></small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ROL -->
            <div>
                <div class="cu-section-label">
                    <i class="fas fa-user-gear"></i>
                    Configuración
                </div>
                <div class="cu-row-2">
                    <div class="cu-field">
                        <label class="cu-label">
                            Rol
                            <span class="cu-req">*</span>
                        </label>
                        <select
                            name="rol"
                            id="selectRol"
                            class="cu-input cu-select"
                            required
                        >
                            <option value="">
                                Seleccionar rol
                            </option>
                            <?php foreach ($roles as $r): ?>
                                <option
                                    value="<?= $r['idRol'] ?>"
                                    data-nombre="<?= htmlspecialchars($r['nombre']) ?>"
                                >
                                    <?= htmlspecialchars($r['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <!-- EMPLEADO -->
            <div id="seccionEmpleado" style="display:none;">
                <hr class="cu-divider">
                <div class="cu-section-label">
                    <i class="fas fa-id-badge"></i>
                    Datos del empleado
                </div>
                <div class="cu-row-3">
                    <!-- CARGO -->
                    <div class="cu-field">
                        <label class="cu-label">
                            Cargo
                        </label>
                        <input
                            type="text"
                            name="cargo"
                            id="cargo"
                            class="cu-input"
                            placeholder="Ej: Recepcionista"
                        >
                    </div>
                    <!-- FECHA -->
                    <div class="cu-field">
                        <label class="cu-label">
                            Fecha contratación
                        </label>
                        <input
                            type="date"
                            name="fechaContratacion"
                            id="fechaContratacion"
                            class="cu-input"
                            max="<?= date('Y-m-d') ?>"
                        >
                    </div>
                    <!-- SALARIO -->
                    <div class="cu-field">
                        <label class="cu-label">
                            Salario (Bs.)
                        </label>
                        <input
                            type="number"
                            name="salario"
                            id="salario"
                            class="cu-input"
                            placeholder="3500.00"
                            step="0.01"
                            min="0"
                        >
                    </div>
                </div>
            </div>
            <!-- BOTONES -->
            <div class="cu-submit-row">
                <button type="submit" class="cu-btn-submit">
                    <i class="fas fa-save"></i>
                    Guardar Usuario
                </button>
                <a
                    href="<?= url('admin/usuarios') ?>"
                    class="cu-btn-cancel"
                >
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ROLES EMPLEADO
    const selectRol =
        document.getElementById('selectRol');

    const seccionEmpleado =
        document.getElementById('seccionEmpleado');

    const cargo =
        document.getElementById('cargo');

    const fechaC =
        document.getElementById('fechaContratacion');

    const salario =
        document.getElementById('salario');

    const rolesEmpleado = [
        'Administrador',
        'Recepcionista',
        'Gerente',
        'Contador',
        'Limpieza',
        'Mantenimiento'
    ];

    function verificarRol() {

        const nombreRol =
            selectRol.options[
                selectRol.selectedIndex
            ]?.dataset.nombre ?? '';

        const esEmpleado =
            rolesEmpleado.includes(nombreRol);

        seccionEmpleado.style.display =
            esEmpleado ? 'block' : 'none';

        cargo.required = esEmpleado;
        fechaC.required = esEmpleado;
        salario.required = esEmpleado;
    }

    if (selectRol) {
        selectRol.addEventListener('change', verificarRol);
        verificarRol();
    }

    // FUERZA PASSWORD
    const input =
        document.getElementById('passwordInput');

    const bar =
        document.getElementById('strengthBar');

    const text =
        document.getElementById('strengthText');

    const levels = [
        {
            label: 'Muy débil',
            color: '#ef4444',
            width: '20%'
        },
        {
            label: 'Débil',
            color: '#f97316',
            width: '40%'
        },
        {
            label: 'Regular',
            color: '#facc15',
            width: '60%'
        },
        {
            label: 'Fuerte',
            color: '#22c55e',
            width: '80%'
        },
        {
            label: 'Muy fuerte',
            color: '#16a34a',
            width: '100%'
        },
    ];

    if (input) {

        input.addEventListener('input', function () {

            const val = input.value;

            if (!val) {

                bar.style.width = '0%';
                text.textContent = '';

                return;
            }

            let score = 0;

            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const level =
                levels[score - 1] ?? levels[0];

            bar.style.width =
                level.width;

            bar.style.background =
                level.color;

            text.textContent =
                level.label;

            text.style.color =
                level.color;

        });

    }

});
</script>