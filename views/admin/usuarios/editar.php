<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="fas fa-user-edit me-2 text-primary"></i>Editar Usuario</h4>
    <a href="<?= url('admin/usuarios') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:700px">
    <div class="card-body p-4">
        <form action="<?= url('admin/usuarios/editar') ?>" method="POST">
            <input type="hidden" name="id" value="<?= $usuario['idUsuario'] ?>">

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
                    <label class="form-label fw-semibold">CI</label>
                    <input type="text" class="form-control bg-light"
                           value="<?= htmlspecialchars($usuario['ci']) ?>" disabled>
                    <div class="form-text">El CI no se puede modificar.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="telefono" class="form-control"
                           value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control bg-light"
                           value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
                    <div class="form-text">El email no se puede modificar.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Rol *</label>
                    <select name="rol" class="form-select" required>
                        <option value="">Seleccionar rol</option>
                        <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['idRol'] ?>"
                                <?= $usuario['idRol'] == $r['idRol'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Estado *</label>
                    <select name="estado" class="form-select" required>
                        <option value="Activo"     <?= $usuario['estado'] === 'Activo'     ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo"   <?= $usuario['estado'] === 'Inactivo'   ? 'selected' : '' ?>>Inactivo</option>
                        <option value="Suspendido" <?= $usuario['estado'] === 'Suspendido' ? 'selected' : '' ?>>Suspendido</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nueva contraseña</label>
                    <input type="password" name="password" id="passwordInput"
                           class="form-control" placeholder="Dejar vacío para no cambiar">
                    <div style="margin-top:8px;">
                        <div style="height:6px;border-radius:10px;background:#e0e0e0;overflow:hidden;">
                            <div id="strengthBar" style="height:100%;width:0%;border-radius:10px;transition:width 0.4s ease,background 0.4s ease;"></div>
                        </div>
                        <small id="strengthText" style="font-size:0.75rem;margin-top:4px;display:block;font-weight:600;"></small>
                    </div>
                    <div class="form-text">Solo completa si deseas cambiar la contraseña.</div>
                </div>

                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('passwordInput');
    const bar   = document.getElementById('strengthBar');
    const text  = document.getElementById('strengthText');

    const reqs = {
        length:  { fn: v => v.length >= 8 },
        upper:   { fn: v => /[A-Z]/.test(v) },
        lower:   { fn: v => /[a-z]/.test(v) },
        number:  { fn: v => /[0-9]/.test(v) },
        special: { fn: v => /[^A-Za-z0-9]/.test(v) },
    };

    const levels = [
        { label: 'Muy débil',  color: '#e74c3c', width: '20%'  },
        { label: 'Débil',      color: '#e67e22', width: '40%'  },
        { label: 'Regular',    color: '#f1c40f', width: '60%'  },
        { label: 'Fuerte',     color: '#2ecc71', width: '80%'  },
        { label: 'Muy fuerte', color: '#27ae60', width: '100%' },
    ];

    if (input) {
        input.addEventListener('input', function () {
            const val = input.value;
            if (val.length === 0) {
                bar.style.width = '0%';
                text.textContent = '';
                return;
            }
            let score = 0;
            for (const key in reqs) {
                if (reqs[key].fn(val)) score++;
            }
            const level = levels[score - 1] ?? levels[0];
            bar.style.width      = level.width;
            bar.style.background = level.color;
            text.textContent     = level.label;
            text.style.color     = level.color;
        });
    }
});
</script>