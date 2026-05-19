<style>
.perfil-card { border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); }
.avatar-circle {
    width:90px; height:90px; border-radius:50%;
    background:linear-gradient(135deg,#1a1a2e,#0f3460);
    display:flex; align-items:center; justify-content:center;
    font-size:2rem; font-weight:700; color:#fff;
}
.strength-bar { height:6px; border-radius:10px; background:#e0e0e0; overflow:hidden; }
.strength-fill { height:100%; border-radius:10px; width:0%; transition:width .4s, background .4s; }
</style>

<div style="background:linear-gradient(135deg,#1a1a2e,#0f3460);padding:40px 20px 30px;color:#fff;">
    <div style="max-width:700px;margin:0 auto;">
        <a href="<?= url('cliente/dashboard') ?>" style="color:#a0b4d0;font-size:.85rem;text-decoration:none;">
            <i class="fas fa-arrow-left me-2"></i>Mi panel
        </a>
        <h2 class="fw-bold mt-2 mb-0">Mi Perfil</h2>
        <p style="color:#a0b4d0;">Administra tu información personal</p>
    </div>
</div>

<div style="max-width:700px;margin:0 auto;padding:30px 20px;">

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Avatar -->
    <div class="perfil-card p-4 mb-4 d-flex align-items-center gap-4">
        <div class="avatar-circle">
            <?= strtoupper(mb_substr($usuario['nombre'], 0, 1)) ?>
        </div>
        <div>
            <h4 class="fw-bold mb-0"><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['paterno']) ?></h4>
            <p class="text-muted mb-0"><?= htmlspecialchars($usuario['email']) ?></p>
            <span class="badge bg-success mt-1"><?= $usuario['estado'] ?></span>
        </div>
    </div>

    <!-- Info no editable -->
    <div class="perfil-card p-4 mb-4">
        <h6 class="fw-bold text-muted mb-3" style="font-size:.75rem;letter-spacing:.1em;text-transform:uppercase;">
            Información de cuenta
        </h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label text-muted small">CI / Carnet</label>
                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($usuario['ci']) ?>" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Correo electrónico</label>
                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label text-muted small">Miembro desde</label>
                <input type="text" class="form-control bg-light"
                       value="<?= date('d/m/Y', strtotime($usuario['fechaRegistro'])) ?>" disabled>
            </div>
        </div>
    </div>

    <!-- Formulario editable -->
    <div class="perfil-card p-4">
        <h6 class="fw-bold text-muted mb-3" style="font-size:.75rem;letter-spacing:.1em;text-transform:uppercase;">
            Editar información
        </h6>
        <form action="<?= url('cliente/perfil') ?>" method="POST">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nombre *</label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Paterno *</label>
                    <input type="text" name="paterno" class="form-control"
                           value="<?= htmlspecialchars($usuario['paterno']) ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Materno</label>
                    <input type="text" name="materno" class="form-control"
                           value="<?= htmlspecialchars($usuario['materno'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                           placeholder="+591 7XXXXXXX">
                </div>

                <div class="col-12">
                    <hr class="my-1">
                    <p class="text-muted small mb-2">
                        <i class="fas fa-lock me-1"></i>Cambiar contraseña <span class="text-muted fw-normal">(opcional)</span>
                    </p>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Contraseña actual</label>
                    <input type="password" name="password_actual" class="form-control mb-3"
                           placeholder="Ingresa tu contraseña actual">

                    <label class="form-label fw-semibold">Nueva contraseña</label>
                    <input type="password" name="password" id="pwInput" class="form-control"
                           placeholder="Mínimo 8 caracteres">
                    <div class="strength-bar mt-2">
                        <div class="strength-fill" id="pwBar"></div>
                    </div>
                    <small id="pwText" class="fw-semibold" style="font-size:.75rem;"></small>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Confirmar nueva contraseña</label>
                    <input type="password" name="password_confirm" id="pwConfirm" class="form-control" placeholder="Repite la nueva contraseña">
                    <small id="pwMatch" class="fw-semibold" style="font-size:.75rem;"></small>
                </div>

                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary px-5 py-2">
                        <i class="fas fa-save me-2"></i>Guardar cambios
                    </button>
                </div>

            </div>
        </form>
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
    if (!v) { pwBar.style.width='0%'; pwText.textContent=''; return; }
    let s = 0;
    if (v.length >= 8)           s++;
    if (/[A-Z]/.test(v))         s++;
    if (/[a-z]/.test(v))         s++;
    if (/[0-9]/.test(v))         s++;
    if (/[^A-Za-z0-9]/.test(v))  s++;
    const l = levels[s-1] ?? levels[0];
    pwBar.style.width      = l.w;
    pwBar.style.background = l.color;
    pwText.textContent     = l.label;
    pwText.style.color     = l.color;
    checkMatch();
});

pwConfirm.addEventListener('input', checkMatch);

function checkMatch() {
    if (!pwConfirm.value) { pwMatch.textContent = ''; return; }
    if (pwInput.value === pwConfirm.value) {
        pwMatch.textContent = '✔ Coinciden';
        pwMatch.style.color = '#28a745';
    } else {
        pwMatch.textContent = '✘ No coinciden';
        pwMatch.style.color = '#dc3545';
    }
}
</script>