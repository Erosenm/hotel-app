<?php $editando = isset($producto); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-<?= $editando ? 'edit' : 'plus' ?> me-2 text-primary"></i>
        <?= $editando ? 'Editar Producto' : 'Nuevo Producto' ?>
    </h4>
    <a href="<?= url('admin/productos') ?>" class="btn btn-outline-secondary btn-sm">
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

<div class="card border-0 shadow-sm" style="max-width:680px;">
    <div class="card-body p-4">
        <form method="POST" action="<?= url($editando ? 'admin/productos/actualizar' : 'admin/productos/crear') ?>">
            <?php if ($editando): ?>
                <input type="hidden" name="id" value="<?= $producto['idProducto'] ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control"
                           value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                    <select name="idCategoria" class="form-select" required>
                        <option value="">— Seleccionar —</option>
                        <?php foreach ($categorias as $c): ?>
                            <option value="<?= $c['idCategoria'] ?>"
                                <?= (isset($producto) && $producto['idCategoria_FK'] == $c['idCategoria']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Unidad</label>
                    <select name="unidad" class="form-select">
                        <?php foreach (['unidad','botella','lata','vaso','copa','porción','bolsa','prenda','sesión','viaje','kg','litro'] as $u): ?>
                            <option value="<?= $u ?>" <?= (($producto['unidad'] ?? 'unidad') === $u) ? 'selected' : '' ?>>
                                <?= $u ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Precio (Bs.) <span class="text-danger">*</span></label>
                    <input type="number" name="precio" class="form-control" step="0.01" min="0"
                           value="<?= htmlspecialchars($producto['precio'] ?? '') ?>" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stock actual</label>
                    <input type="number" name="stock" class="form-control" min="0"
                           value="<?= htmlspecialchars($producto['stock'] ?? 0) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Stock mínimo</label>
                    <input type="number" name="stockMinimo" class="form-control" min="0"
                           value="<?= htmlspecialchars($producto['stockMinimo'] ?? 5) ?>">
                    <small class="text-muted">Alerta cuando llegue a este nivel</small>
                </div>

                <?php if ($editando): ?>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="Activo"   <?= ($producto['estado'] === 'Activo')   ? 'selected' : '' ?>>Activo</option>
                        <option value="Inactivo" <?= ($producto['estado'] === 'Inactivo') ? 'selected' : '' ?>>Inactivo</option>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i><?= $editando ? 'Actualizar' : 'Crear producto' ?>
                    </button>
                    <a href="<?= url('admin/productos') ?>" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>