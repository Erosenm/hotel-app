<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleUsuarios/editUsuario.css') ?>">

<?php
$usuario = $usuario ?? [];
$roles   = $roles ?? [];

$estadoClass = match($usuario['estado'] ?? '') {
    'Activo'     => 'success',
    'Inactivo'   => 'warning',
    'Suspendido' => 'danger',
    default      => 'warning'
};
?>

<div class="eu-header">

    <div>
        <h4 class="eu-title">
            <i class="fas fa-user-edit"></i>
            Editar Usuario
        </h4>
    </div>

    <a href="<?= url('admin/usuarios') ?>" class="eu-btn-back">
        <i class="fas fa-arrow-left"></i>
        Volver
    </a>

</div>

<div class="eu-layout">

    <!-- SIDEBAR -->
    <aside class="eu-sidebar-info">

        <div class="eu-avatar-big">
            <?= strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1)) ?>
        </div>

        <div class="eu-info-name">
            <?= htmlspecialchars(($usuario['nombre'] ?? '') . ' ' . ($usuario['paterno'] ?? '')) ?>
        </div>

        <div class="eu-info-email">
            <?= htmlspecialchars($usuario['email'] ?? '') ?>
        </div>

        <div class="eu-info-badge <?= $estadoClass ?>">
            <?= htmlspecialchars($usuario['estado'] ?? 'Activo') ?>
        </div>

        <div class="eu-info-divider"></div>

        <div class="eu-info-row">
            <div class="eu-info-row-label">
                <i class="fas fa-id-card"></i>
                CI
            </div>

            <div class="eu-info-row-val">
                <?= htmlspecialchars($usuario['ci'] ?? '-') ?>
            </div>
        </div>

        <div class="eu-info-row">
            <div class="eu-info-row-label">
                <i class="fas fa-phone"></i>
                Teléfono
            </div>

            <div class="eu-info-row-val">
                <?= htmlspecialchars($usuario['telefono'] ?? '-') ?>
            </div>
        </div>

        <div class="eu-info-divider"></div>

        <div class="eu-info-note">
            <i class="fas fa-info-circle"></i>

            <span>
                Puedes actualizar la información del usuario,
                cambiar su rol o modificar su contraseña.
            </span>
        </div>

    </aside>

    <!-- FORM -->
    <div class="eu-card">

        <form action="<?= url('admin/usuarios/editar') ?>" method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= $usuario['idUsuario'] ?>"
            >

            <div class="eu-section-label">
                <i class="fas fa-user"></i>
                Información personal
            </div>

            <div class="eu-row-3">

                <!-- NOMBRE -->
                <div class="eu-field">

                    <label class="eu-label">
                        Nombre
                        <span class="eu-req">*</span>
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="eu-input"
                        value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>"
                        required
                    >

                </div>

                <!-- PATERNO -->
                <div class="eu-field">

                    <label class="eu-label">
                        Paterno
                        <span class="eu-req">*</span>
                    </label>

                    <input
                        type="text"
                        name="paterno"
                        class="eu-input"
                        value="<?= htmlspecialchars($usuario['paterno'] ?? '') ?>"
                        required
                    >

                </div>

                <!-- MATERNO -->
                <div class="eu-field">

                    <label class="eu-label">
                        Materno
                    </label>

                    <input
                        type="text"
                        name="materno"
                        class="eu-input"
                        value="<?= htmlspecialchars($usuario['materno'] ?? '') ?>"
                    >

                </div>

            </div>

            <div class="eu-row-2">

                <!-- CI -->
                <div class="eu-field">

                    <label class="eu-label">
                        CI
                    </label>

                    <input
                        type="text"
                        class="eu-input eu-input-disabled"
                        value="<?= htmlspecialchars($usuario['ci'] ?? '') ?>"
                        disabled
                    >

                    <div class="eu-hint">
                        <i class="fas fa-lock"></i>
                        El CI no se puede modificar
                    </div>

                </div>

                <!-- TELÉFONO -->
                <div class="eu-field">

                    <label class="eu-label">
                        Teléfono
                    </label>

                    <input
                        type="text"
                        name="telefono"
                        class="eu-input"
                        value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                    >

                </div>

            </div>

            <div class="eu-row-2">

                <!-- EMAIL -->
                <div class="eu-field">

                    <label class="eu-label">
                        Email
                    </label>

                    <input
                        type="email"
                        class="eu-input eu-input-disabled"
                        value="<?= htmlspecialchars($usuario['email'] ?? '') ?>"
                        disabled
                    >

                    <div class="eu-hint">
                        <i class="fas fa-lock"></i>
                        El email no se puede modificar
                    </div>

                </div>

                <!-- ROL -->
                <div class="eu-field">

                    <label class="eu-label">
                        Rol
                        <span class="eu-req">*</span>
                    </label>

                    <select
                        name="rol"
                        id="selectRolEdit"
                        class="eu-input eu-select"
                        required
                    >

                        <option value="">
                            Seleccionar rol
                        </option>

                        <?php foreach ($roles as $r): ?>

                            <option
                                value="<?= $r['idRol'] ?>"
                                data-nombre="<?= htmlspecialchars($r['nombre']) ?>"
                                <?= ($usuario['idRol'] ?? '') == $r['idRol'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($r['nombre']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

            <div class="eu-row-2">

                <!-- ESTADO -->
                <div class="eu-field">

                    <label class="eu-label">
                        Estado
                        <span class="eu-req">*</span>
                    </label>

                    <select
                        name="estado"
                        class="eu-input eu-select"
                        required
                    >

                        <option
                            value="Activo"
                            <?= ($usuario['estado'] ?? '') === 'Activo' ? 'selected' : '' ?>
                        >
                            Activo
                        </option>

                        <option
                            value="Inactivo"
                            <?= ($usuario['estado'] ?? '') === 'Inactivo' ? 'selected' : '' ?>
                        >
                            Inactivo
                        </option>

                        <option
                            value="Suspendido"
                            <?= ($usuario['estado'] ?? '') === 'Suspendido' ? 'selected' : '' ?>
                        >
                            Suspendido
                        </option>

                    </select>

                </div>

            </div>

            <!-- EMPLEADO -->
            <div id="seccionEmpleado">

                <div class="eu-section-label">
                    <i class="fas fa-id-badge"></i>
                    Datos del empleado
                </div>

                <div class="eu-row-3">

                    <!-- CARGO -->
                    <div class="eu-field">

                        <label class="eu-label">
                            Cargo
                        </label>

                        <input
                            type="text"
                            name="cargo"
                            id="cargo"
                            class="eu-input"
                            value="<?= htmlspecialchars($usuario['cargo'] ?? '') ?>"
                            placeholder="Ej: Recepcionista"
                        >

                    </div>

                    <!-- FECHA -->
                    <div class="eu-field">

                        <label class="eu-label">
                            Fecha contratación
                        </label>

                        <input
                            type="date"
                            name="fechaContratacion"
                            id="fechaContratacion"
                            class="eu-input"
                            value="<?= htmlspecialchars($usuario['fechaContratacion'] ?? '') ?>"
                            max="<?= date('Y-m-d') ?>"
                        >

                    </div>

                    <!-- SALARIO -->
                    <div class="eu-field">

                        <label class="eu-label">
                            Salario (Bs.)
                        </label>

                        <input
                            type="number"
                            name="salario"
                            id="salario"
                            class="eu-input"
                            value="<?= htmlspecialchars($usuario['salario'] ?? '') ?>"
                            placeholder="Ej: 3500.00"
                            step="0.01"
                            min="0"
                        >

                    </div>

                </div>

            </div>

            <!-- PASSWORD -->
            <div class="eu-section-label" style="margin-top:1.5rem;">
                <i class="fas fa-lock"></i>
                Seguridad
            </div>

            <div class="eu-row-1">

                <div class="eu-field">

                    <label class="eu-label">
                        Nueva contraseña
                    </label>

                    <input
                        type="password"
                        name="password"
                        id="passwordInput"
                        class="eu-input"
                        placeholder="Dejar vacío para no cambiar"
                    >

                    <div class="eu-strength-wrap">

                        <div class="eu-strength-track">
                            <div
                                id="strengthBar"
                                class="eu-strength-bar"
                            ></div>
                        </div>

                        <small
                            id="strengthText"
                            class="eu-strength-text"
                        ></small>

                    </div>

                    <div class="eu-hint">
                        <i class="fas fa-info-circle"></i>
                        Completa solo si deseas cambiar la contraseña
                    </div>

                </div>

            </div>

            <!-- BOTONES -->
            <div class="eu-submit-row">

                <button
                    type="submit"
                    class="eu-btn-submit"
                >
                    <i class="fas fa-save"></i>
                    Guardar Cambios
                </button>

                <a
                    href="<?= url('admin/usuarios') ?>"
                    class="eu-btn-cancel"
                >
                    Cancelar
                </a>

            </div>

        </form>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // =========================
    // PASSWORD
    // =========================

    const input = document.getElementById('passwordInput');
    const bar   = document.getElementById('strengthBar');
    const text  = document.getElementById('strengthText');

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
        }
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

            const level = levels[score - 1] ?? levels[0];

            bar.style.width      = level.width;
            bar.style.background = level.color;

            text.textContent = level.label;
            text.style.color = level.color;

        });

    }

    // =========================
    // EMPLEADO
    // =========================

    const selectRol = document.getElementById('selectRolEdit');

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

        cargo.required   = esEmpleado;
        fechaC.required  = esEmpleado;
        salario.required = esEmpleado;
    }

    if (selectRol) {

        selectRol.addEventListener(
            'change',
            verificarRol
        );

        verificarRol();

    }

});
</script>