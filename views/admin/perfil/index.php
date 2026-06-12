<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleperfil/index.css') ?>">

<div class="perf-container">
    <!-- Header -->
    <div class="perf-header">
        <h4>
            <i class="fas fa-user-circle"></i>
            Mi Perfil
        </h4>
    </div>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="perf-alert perf-alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 0.7rem;"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="perf-alert perf-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 0.7rem;"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="perf-grid">
        <!-- Columna izquierda: Datos personales -->
        <div class="perf-card">
            <div class="perf-card-header">
                <h6>
                    <i class="far fa-id-card"></i> Información personal
                </h6>
            </div>
            <div class="perf-card-body">
                <!-- Avatar -->
                <div class="perf-avatar-row">
                    <div class="perf-avatar">
                        <?= strtoupper(substr($usuario['nombre'], 0, 1) . substr($usuario['paterno'], 0, 1)) ?>
                    </div>
                    <div class="perf-avatar-info">
                        <h5><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['paterno']) ?></h5>
                        <span class="perf-role-badge">
                            <i class="fas fa-<?= $usuario['rol'] === 'Administrador' ? 'shield-alt' : 'user' ?> me-1"></i>
                            <?= htmlspecialchars($usuario['rol'] ?? 'Usuario') ?>
                        </span>
                    </div>
                </div>

                <form method="POST" action="<?= url('admin/perfil/actualizar') ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="perf-form-group">
                                <label class="perf-label">Nombre</label>
                                <input type="text" name="nombre" class="perf-input"
                                       value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="perf-form-group">
                                <label class="perf-label">Apellido paterno</label>
                                <input type="text" name="paterno" class="perf-input"
                                       value="<?= htmlspecialchars($usuario['paterno']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="perf-form-group">
                                <label class="perf-label">Apellido materno</label>
                                <input type="text" name="materno" class="perf-input"
                                       value="<?= htmlspecialchars($usuario['materno'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="perf-form-group">
                                <label class="perf-label">Teléfono</label>
                                <input type="text" name="telefono" class="perf-input"
                                       value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                                <div class="perf-help">
                                    <i class="fas fa-info-circle"></i> Número de contacto
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="perf-form-group">
                                <label class="perf-label">CI</label>
                                <input type="text" class="perf-input" 
                                       value="<?= htmlspecialchars($usuario['ci']) ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="perf-form-group">
                                <label class="perf-label">Email</label>
                                <input type="text" class="perf-input" 
                                       value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="perf-info-row">
                                <span class="perf-info-label">Miembro desde</span>
                                <span class="perf-info-value">
                                    <?= date('d/m/Y', strtotime($usuario['fechaRegistro'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="perf-btn perf-btn-primary">
                                <i class="fas fa-save"></i> Guardar cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Columna derecha: Seguridad -->
        <div class="perf-card">
            <div class="perf-card-header">
                <h6>
                    <i class="fas fa-lock"></i> Seguridad
                </h6>
            </div>
            <div class="perf-card-body">
                <form method="POST" action="<?= url('admin/perfil/password') ?>" id="passwordForm">
                    <div class="perf-form-group">
                        <label class="perf-label">Contraseña actual</label>
                        <input type="password" name="password_actual" class="perf-input" required>
                        <div class="perf-help">
                            <i class="fas fa-shield-alt"></i> Requerida para confirmar cambios
                        </div>
                    </div>
                    
                    <div class="perf-form-group">
                        <label class="perf-label">Nueva contraseña</label>
                        <input type="password" name="password_nueva" id="pNueva" class="perf-input" minlength="6" required>
                        <div class="perf-strength" id="strengthBox">
                            <div class="perf-strength-bars">
                                <div class="perf-strength-bar" id="s1"></div>
                                <div class="perf-strength-bar" id="s2"></div>
                                <div class="perf-strength-bar" id="s3"></div>
                                <div class="perf-strength-bar" id="s4"></div>
                            </div>
                            <div class="perf-strength-text" id="strengthText">Mínimo 6 caracteres</div>
                        </div>
                    </div>
                    
                    <div class="perf-form-group">
                        <label class="perf-label">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmar" id="pConfirmar" class="perf-input" minlength="6" required>
                        <div id="matchMsg" class="perf-help" style="display: none;"></div>
                    </div>
                    
                    <button type="submit" class="perf-btn perf-btn-warning" id="btnPassword" disabled>
                        <i class="fas fa-undo-alt"></i> Actualizar contraseña
                    </button>
                </form>
                
                <div class="perf-recommend">
                    <i class="fas fa-shield-virus"></i>
                    Usa al menos 8 caracteres, combina mayúsculas, números y símbolos.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const p1 = document.getElementById('pNueva');
const p2 = document.getElementById('pConfirmar');
const msg = document.getElementById('matchMsg');
const btn = document.getElementById('btnPassword');

function checkStrength(password) {
    let strength = 0;
    if (password.length >= 6) strength = 1;
    if (password.length >= 8) strength = 2;
    if (password.length >= 8 && /[A-Z]/.test(password)) strength = 3;
    if (password.length >= 8 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) strength = 4;
    
    const segments = ['s1', 's2', 's3', 's4'];
    segments.forEach((id, i) => {
        const el = document.getElementById(id);
        if (i < strength) {
            el.classList.add('active');
        } else {
            el.classList.remove('active');
        }
    });
    
    const texts = ['', 'Débil', 'Regular', 'Fuerte', 'Muy fuerte'];
    document.getElementById('strengthText').textContent = strength > 0 ? texts[strength] : 'Mínimo 6 caracteres';
    return strength;
}

function validateMatch() {
    if (!p2.value) {
        msg.style.display = 'none';
        btn.disabled = true;
        return;
    }
    msg.style.display = 'flex';
    if (p1.value === p2.value && p1.value.length >= 6) {
        msg.innerHTML = '<i class="fas fa-check-circle"></i> Las contraseñas coinciden';
        msg.style.color = '#10b981';
        btn.disabled = false;
    } else {
        msg.innerHTML = '<i class="fas fa-times-circle"></i> No coinciden';
        msg.style.color = '#ef4444';
        btn.disabled = true;
    }
}

p1.addEventListener('input', () => {
    checkStrength(p1.value);
    validateMatch();
});

p2.addEventListener('input', validateMatch);
</script>