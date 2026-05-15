<?php $editando = isset($servicio); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-<?= $editando ? 'edit' : 'plus' ?> me-2 text-primary"></i>
        <?= $editando ? 'Editar Servicio' : 'Nuevo Servicio' ?>
    </h4>
    <a href="<?= url('admin/servicios') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Volver
    </a>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="max-width:480px;">
    <div class="card-body p-4">
        <form method="POST" action="<?= url($editando ? 'admin/servicios/actualizar' : 'admin/servicios/crear') ?>">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $servicio['idServicio'] ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre del servicio <span class="text-danger">*</span></label>
                <input type="text" name="nombre" class="form-control"
                       value="<?= htmlspecialchars($servicio['nombre'] ?? '') ?>"
                       placeholder="Ej: Masaje relajante 60min" required>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Precio (Bs.) <span class="text-danger">*</span></label>
                <input type="number" name="precio" class="form-control" step="0.01" min="0"
                       value="<?= htmlspecialchars($servicio['precio'] ?? '') ?>"
                       placeholder="0.00" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i><?= $editando ? 'Actualizar' : 'Crear servicio' ?>
                </button>
                <a href="<?= url('admin/servicios') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>