<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleUsuarios/editUsuario.css') ?>">

<!-- ══ Header ══ -->
<div class="eu-header">
    <div class="eu-title">
        <i class="fas fa-user-edit"></i>
        <span>Editar Usuario</span>
    </div>
    <a href="<?= url('admin/usuarios') ?>" class="eu-btn-back">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<!-- ══ Layout: info lateral + formulario ══ -->
<div class="eu-layout">

    <!-- ── Panel lateral: info del usuario ── -->
    <aside class="eu-sidebar-info">
        <div class="eu-avatar-big">
            <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
        </div>
        <div class="eu-info-name">
            <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['paterno']) ?>
        </div>
        <div class="eu-info-email"><?= htmlspecialchars($usuario['email']) ?></div>

        <?php
            $colorEstado = $usuario['estado'] === 'Activo' ? 'success' : ($usuario['estado'] === 'Suspendido' ? 'warning' : 'danger');
        ?>
        <span class="eu-info-badge <?= $colorEstado ?>"><?= htmlspecialchars($usuario['estado']) ?></span>

        <div class="eu-info-divider"></div>

        <div class="eu-info-row">
            <span class="eu-info-row-label"><i class="fas fa-id-badge"></i> CI</span>
            <span class="eu-info-row-val"><?= htmlspecialchars($usuario['ci']) ?></span>
        </div>
        <div class="eu-info-row">
            <span class="eu-info-row-label"><i class="fas fa-phone"></i> Teléfono</span>
            <span class="eu-info-row-val"><?= htmlspecialchars($usuario['telefono'] ?? '—') ?></span>
        </div>
        <div class="eu-info-row">
            <span class="eu-info-row-label"><i class="fas fa-user-shield"></i> Rol</span>
            <span class="eu-info-row-val"><?= htmlspecialchars($usuario['rol'] ?? '—') ?></span>
        </div>

        <div class="eu-info-divider"></div>

        <div class="eu-info-note">
            <i class="fas fa-lock"></i>
            El CI y el email son datos fijos y no se pueden modificar.
        </div>
    </aside>

    <!-- ── Formulario ── -->
    <div class="eu-card">
        <form action="<?= url('admin/usuarios/editar') ?>" method="POST">
            <input type="hidden" name="id" value="<?= $usuario['idUsuario'] ?>">

            <!-- Sección: datos personales -->
            <div class="eu-section-label">
                <i class="fas fa-id-card"></i> Datos personales
            </div>

            <div class="eu-row-3">
                <div class="eu-field">
                    <label class="eu-label">Nombre <span class="eu-req">*</span></label>
                    <input type="text" name="nombre" class="eu-input"
                           value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>
                <div class="eu-field">
                    <label class="eu-label">Paterno <span class="eu-req">*</span></label>
                    <input type="text" name="paterno" class="eu-input"
                           value="<?= htmlspecialchars($usuario['paterno']) ?>" required>
                </div>
                <div class="eu-field">
                    <label class="eu-label">Materno</label>
                    <input type="text" name="materno" class="eu-input"
                           value="<?= htmlspecialchars($usuario['materno'] ?? '') ?>">
                </div>
            </div>

            <div class="eu-row-2">
                <div class="eu-field">
                    <label class="eu-label">CI</label>
                    <div class="eu-input-icon">
                        <i class="fas fa-id-badge"></i>
                        <input type="text" class="eu-input eu-input-disabled has-icon"
                               value="<?= htmlspecialchars($usuario['ci']) ?>" disabled>
                    </div>
                    <span class="eu-hint"><i class="fas fa-lock"></i> No modificable</span>
                </div>
                <div class="eu-field">
                    <label class="eu-label">Teléfono</label>
                    <div class="eu-input-icon">
                        <i class="fas fa-phone"></i>
                        <input type="text" name="telefono" class="eu-input has-icon"
                               value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Sección: acceso -->
            <div class="eu-section-label" style="margin-top:.5rem">
                <i class="fas fa-lock"></i> Acceso al sistema
            </div>

            <div class="eu-row-1">
                <div class="eu-field">
                    <label class="eu-label">Email</label>
                    <div class="eu-input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" class="eu-input eu-input-disabled has-icon"
                               value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
                    </div>
                    <span class="eu-hint"><i class="fas fa-lock"></i> No modificable</span>
                </div>
            </div>

            <div class="eu-row-2">
                <div class="eu-field">
                    <label class="eu-label">Rol <span class="eu-req">*</span></label>
                    <div class="eu-input-icon">
                        <i class="fas fa-user-shield"></i>
                        <select name="rol" class="eu-input eu-select has-icon" required>
                            <option value="">Seleccionar rol</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['idRol'] ?>"
                                    <?= $usuario['idRol'] == $r['idRol'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="eu-field">
                    <label class="eu-label">Estado <span class="eu-req">*</span></label>
                    <div class="eu-input-icon">
                        <i class="fas fa-circle-dot"></i>
                        <select name="estado" class="eu-input eu-select has-icon" required>
                            <option value="Activo"     <?= $usuario['estado'] === 'Activo'     ? 'selected' : '' ?>>Activo</option>
                            <option value="Inactivo"   <?= $usuario['estado'] === 'Inactivo'   ? 'selected' : '' ?>>Inactivo</option>
                            <option value="Suspendido" <?= $usuario['estado'] === 'Suspendido' ? 'selected' : '' ?>>Suspendido</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="eu-row-1">
                <div class="eu-field">
                    <label class="eu-label">Nueva contraseña</label>
                    <div class="eu-input-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" name="password" id="passwordInput"
                               class="eu-input has-icon" placeholder="Dejar vacío para no cambiar">
                    </div>
                    <div class="eu-strength-wrap">
                        <div class="eu-strength-track">
                            <div id="strengthBar" class="eu-strength-bar"></div>
                        </div>
                        <small id="strengthText" class="eu-strength-text"></small>
                    </div>
                    <span class="eu-hint"><i class="fas fa-info-circle"></i> Solo completa si deseas cambiar la contraseña.</span>
                </div>
            </div>

            <!-- Submit -->
            <div class="eu-submit-row">
                <button type="submit" class="eu-btn-submit">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
                <a href="<?= url('admin/usuarios') ?>" class="eu-btn-cancel">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

</div><!-- /eu-layout -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('passwordInput');
    const bar   = document.getElementById('strengthBar');
    const text  = document.getElementById('strengthText');

    const reqs = {
        length:  { fn: function(v){ return v.length >= 8; } },
        upper:   { fn: function(v){ return /[A-Z]/.test(v); } },
        lower:   { fn: function(v){ return /[a-z]/.test(v); } },
        number:  { fn: function(v){ return /[0-9]/.test(v); } },
        special: { fn: function(v){ return /[^A-Za-z0-9]/.test(v); } },
    };

    const levels = [
        { label: 'Muy débil',  color: '#e74c3c', width: '20%'  },
        { label: 'Débil',      color: '#e67e22', width: '40%'  },
        { label: 'Regular',    color: '#f59e0b', width: '60%'  },
        { label: 'Fuerte',     color: '#10b981', width: '80%'  },
        { label: 'Muy fuerte', color: '#059669', width: '100%' },
    ];

    if (input) {
        input.addEventListener('input', function () {
            var val = input.value;
            if (val.length === 0) {
                bar.style.width = '0%';
                text.textContent = '';
                return;
            }
            var score = 0;
            for (var key in reqs) {
                if (reqs[key].fn(val)) score++;
            }
            var level = levels[score - 1] || levels[0];
            bar.style.width      = level.width;
            bar.style.background = level.color;
            text.textContent     = level.label;
            text.style.color     = level.color;
        });
    }
});
</script>