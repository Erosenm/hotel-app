<link rel="stylesheet" href="<?= asset('css/cssCliente/clienteperfil.css') ?>">

<!-- Portada (imagen 4.png) -->
<div class="perfil-portada">
    <img src="<?= asset('imgs/4.png') ?>" alt="Portada del perfil">
    <div class="perfil-portada-overlay"></div>
</div>

<div class="perfil-main">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="perfil-alert perfil-alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?= $_SESSION['success'] ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="perfil-alert perfil-alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= $_SESSION['error'] ?></span>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Tarjeta principal: Avatar + Info -->
    <div class="perfil-card">
        <div class="perfil-header-card">
            <div class="perfil-avatar">
                <?= strtoupper(mb_substr($usuario['nombre'], 0, 1)) ?>
            </div>
            <div class="perfil-info">
                <h3><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['paterno']) ?></h3>
                <p><i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($usuario['email']) ?></p>
                <span class="badge-estado">
                    <i class="fas fa-check-circle"></i> <?= $usuario['estado'] ?? 'Activo' ?>
                </span>
            </div>
        </div>

        <div class="perfil-body">
            <!-- Información de cuenta (no editable) -->
            <div class="perfil-section-title">
                <i class="fas fa-id-card me-1"></i> Información de cuenta
            </div>
            <div class="perfil-readonly-grid">
                <div class="perfil-readonly-item">
                    <label>CI / Carnet</label>
                    <div class="valor"><?= htmlspecialchars($usuario['ci']) ?></div>
                </div>
                <div class="perfil-readonly-item">
                    <label>Correo electrónico</label>
                    <div class="valor"><?= htmlspecialchars($usuario['email']) ?></div>
                </div>
                <div class="perfil-readonly-item">
                    <label>Miembro desde</label>
                    <div class="valor"><?= date('d/m/Y', strtotime($usuario['fechaRegistro'])) ?></div>
                </div>
                <div class="perfil-readonly-item">
                    <label>Rol</label>
                    <div class="valor"><?= htmlspecialchars($usuario['rol'] ?? 'Cliente') ?></div>
                </div>
            </div>

            <!-- Formulario editable -->
            <div class="perfil-section-title">
                <i class="fas fa-edit me-1"></i> Editar información personal
            </div>
            <form action="<?= url('cliente/perfil') ?>" method="POST">
                <div class="perfil-form-grid">
                    <div class="perfil-form-group">
                        <label>Nombre *</label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                    </div>
                    <div class="perfil-form-group">
                        <label>Apellido paterno *</label>
                        <input type="text" name="paterno" class="form-control"
                               value="<?= htmlspecialchars($usuario['paterno']) ?>" required>
                    </div>
                    <div class="perfil-form-group">
                        <label>Apellido materno</label>
                        <input type="text" name="materno" class="form-control"
                               value="<?= htmlspecialchars($usuario['materno'] ?? '') ?>">
                    </div>
                    <div class="perfil-form-group" style="grid-column: span 1;">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                               placeholder="+591 7XXXXXXX">
                    </div>
                </div>

                <!-- CAMBIAR CONTRASEÑA -->
                <div class="perfil-password-section">
                    <div class="info-text">
                        <i class="fas fa-lock"></i> Cambiar contraseña <span class="text-muted">(opcional)</span>
                    </div>

                    <div class="perfil-password-grid">
                        <!-- Contraseña actual -->
                        <div class="perfil-password-group">
                            <label>Contraseña actual</label>
                            <input type="password" name="password_actual" class="form-control"
                                   placeholder="Ingresa tu contraseña actual">
                        </div>

                        <!-- Nueva contraseña -->
                        <div class="perfil-password-group">
                            <label>Nueva contraseña</label>
                            <input type="password" name="password" id="pwInput" class="form-control"
                                   placeholder="Mínimo 8 caracteres">
                            
                            <div class="perfil-strength">
                                <div class="perfil-strength-bar">
                                    <div class="perfil-strength-fill" id="pwBar"></div>
                                </div>
                                <div class="perfil-strength-text" id="pwText">Mínimo 8 caracteres</div>
                            </div>
                        </div>

                        <!-- Confirmar nueva contraseña -->
                        <div class="perfil-password-group">
                            <label>Confirmar nueva contraseña</label>
                            <input type="password" name="password_confirm" id="pwConfirm" class="form-control"
                                   placeholder="Repite la nueva contraseña">
                            <div class="perfil-match-text" id="pwMatch"></div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="perfil-btn-save">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </form>
        </div>
    </div>

    <!-- Decoración final -->
    <div class="perfil-deco">
        <span class="perfil-deco-line">
            <i class="fas fa-hotel"></i>
            Hotel Real Plaza & Convention Center
            <i class="fas fa-hotel"></i>
        </span>
    </div>
</div>

<script>
const pwInput   = document.getElementById('pwInput');
const pwConfirm = document.getElementById('pwConfirm');
const pwBar     = document.getElementById('pwBar');
const pwText    = document.getElementById('pwText');
const pwMatch   = document.getElementById('pwMatch');

const levels = [
    { label:'Muy débil',  color:'#e74c3c', w:'20%'  },
    { label:'Débil',      color:'#e67e22', w:'40%'  },
    { label:'Regular',    color:'#f1c40f', w:'60%'  },
    { label:'Fuerte',     color:'#2ecc71', w:'80%'  },
    { label:'Muy fuerte', color:'#27ae60', w:'100%' },
];

pwInput.addEventListener('input', () => {
    const v = pwInput.value;
    if (!v) { 
        pwBar.style.width = '0%'; 
        pwText.textContent = 'Mínimo 8 caracteres';
        pwText.style.color = '#94a3b8';
        return; 
    }
    let s = 0;
    if (v.length >= 8)           s++;
    if (/[A-Z]/.test(v))         s++;
    if (/[a-z]/.test(v))         s++;
    if (/[0-9]/.test(v))         s++;
    if (/[^A-Za-z0-9]/.test(v))  s++;
    const idx = Math.min(Math.max(0, s - 1), levels.length - 1);
    const l = levels[idx] || levels[0];
    pwBar.style.width      = l.w;
    pwBar.style.background = l.color;
    pwText.textContent     = l.label;
    pwText.style.color     = l.color;
    checkMatch();
});

pwConfirm.addEventListener('input', checkMatch);

function checkMatch() {
    if (!pwConfirm.value) { 
        pwMatch.textContent = ''; 
        pwMatch.className = 'perfil-match-text';
        return; 
    }
    if (pwInput.value === pwConfirm.value) {
        pwMatch.textContent = '✔ Las contraseñas coinciden';
        pwMatch.className = 'perfil-match-text success';
    } else {
        pwMatch.textContent = '✘ No coinciden';
        pwMatch.className = 'perfil-match-text error';
    }
}
</script>