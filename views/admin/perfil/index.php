<div class="mb-4">
    <h4 class="fw-bold mb-0"><i class="fas fa-user-circle me-2 text-primary"></i>Mi Perfil</h4>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-1"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="row g-4">

    <!-- Datos personales -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Datos personales</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('admin/perfil/actualizar') ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control"
                                   value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido paterno <span class="text-danger">*</span></label>
                            <input type="text" name="paterno" class="form-control"
                                   value="<?= htmlspecialchars($usuario['paterno']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellido materno</label>
                            <input type="text" name="materno" class="form-control"
                                   value="<?= htmlspecialchars($usuario['materno'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono</label>
                            <input type="text" name="telefono" class="form-control"
                                   value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">CI</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['ci']) ?>" disabled>
                            <small class="text-muted">No editable</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
                            <small class="text-muted">No editable</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($usuario['rol'] ?? '-') ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Miembro desde</label>
                            <input type="text" class="form-control"
                                   value="<?= date('d/m/Y', strtotime($usuario['fechaRegistro'])) ?>" disabled>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Guardar cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cambiar contraseña -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0"><i class="fas fa-lock me-2 text-primary"></i>Cambiar contraseña</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= url('admin/perfil/password') ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña actual</label>
                        <input type="password" name="password_actual" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nueva contraseña</label>
                        <input type="password" name="password_nueva" id="pNueva" class="form-control"
                               minlength="6" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirmar nueva contraseña</label>
                        <input type="password" name="password_confirmar" id="pConfirmar" class="form-control"
                               minlength="6" required>
                        <div id="matchMsg" class="small mt-1" style="display:none;"></div>
                    </div>
                    <button type="submit" class="btn btn-warning px-4" id="btnPassword">
                        <i class="fas fa-key me-2"></i>Cambiar contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
const p1 = document.getElementById('pNueva');
const p2 = document.getElementById('pConfirmar');
const msg = document.getElementById('matchMsg');
const btn = document.getElementById('btnPassword');
[p1, p2].forEach(el => el.addEventListener('input', () => {
    if (!p2.value) { msg.style.display = 'none'; return; }
    msg.style.display = 'block';
    if (p1.value === p2.value) {
        msg.textContent = '✔ Las contraseñas coinciden';
        msg.style.color = '#10b981';
        btn.disabled = false;
    } else {
        msg.textContent = '✘ No coinciden';
        msg.style.color = '#ef4444';
        btn.disabled = true;
    }
}));
</script>