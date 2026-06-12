<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylecategorias/styleca.css') ?>">

<div class="cat-header">
    <div class="cat-title">
        <i class="fas fa-tags"></i>
        <span>Categorías de Productos</span>
    </div>
    <button class="cat-btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
        <i class="fas fa-plus"></i> Nueva categoría
    </button>
</div>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="cat-alert cat-alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_SESSION['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="cat-alert cat-alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Tabla de categorías -->
<div class="cat-table-card">
    <div class="table-responsive">
        <table class="cat-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Productos</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($categorias)): ?>
                <tr>
                    <td colspan="4">
                        <div class="cat-empty">
                            <i class="fas fa-tags"></i>
                            <p>No hay categorías registradas</p>
                            <button class="cat-btn-primary mt-2" style="padding: 0.5rem 1rem;" data-bs-toggle="modal" data-bs-target="#modalCrear">
                                <i class="fas fa-plus"></i> Crear primera categoría
                            </button>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($categorias as $c): ?>
                <tr>
                    <td>
                        <div class="cat-nombre">
                            <div class="cat-nombre-icon">
                                <i class="fas fa-folder"></i>
                            </div>
                            <span class="cat-nombre-text"><?= htmlspecialchars($c['nombre']) ?></span>
                        </div>
                    </td>
                    <td>
                        <?php if (!empty($c['descripcion'])): ?>
                            <div class="cat-descripcion">
                                <i class="fas fa-align-left"></i>
                                <?= htmlspecialchars($c['descripcion']) ?>
                            </div>
                        <?php else: ?>
                            <span class="text-muted" style="font-size: 0.75rem;">
                                <i class="fas fa-minus-circle"></i> Sin descripción
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <span class="cat-productos-badge <?= $c['total_productos'] > 0 ? 'primary' : 'secondary' ?>">
                            <i class="fas fa-<?= $c['total_productos'] > 0 ? 'box' : 'folder-open' ?>"></i>
                            <?= $c['total_productos'] ?> <?= $c['total_productos'] == 1 ? 'producto' : 'productos' ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="cat-actions">
                            <button class="cat-action-btn primary"
                                    onclick="editarCategoria(<?= $c['idCategoria'] ?>, '<?= htmlspecialchars(addslashes($c['nombre'])) ?>', '<?= htmlspecialchars(addslashes($c['descripcion'] ?? '')) ?>')"
                                    title="Editar categoría">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($c['total_productos'] == 0): ?>
                                <a href="<?= url('admin/categorias/eliminar?id=' . $c['idCategoria']) ?>"
                                   class="cat-action-btn danger"
                                   onclick="return confirm('¿Eliminar esta categoría?\n\nLos productos asociados se quedarán sin categoría.')"
                                   title="Eliminar categoría">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <button class="cat-action-btn danger" disabled 
                                        title="No se puede eliminar porque tiene <?= $c['total_productos'] ?> producto(s) asociado(s)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($categorias)): ?>
    <div class="cat-table-footer">
        <i class="fas fa-chart-line"></i>
        Total: <strong><?= count($categorias) ?></strong> categorías registradas
    </div>
    <?php endif; ?>
</div>

<!-- MODAL CREAR CATEGORÍA -->
<div class="modal fade cat-modal" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-plus-circle"></i>
                    Nueva Categoría
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/categorias/crear') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-tag"></i>
                            Nombre de la categoría
                            <span class="cat-required">*</span>
                        </label>
                        <input type="text" name="nombre" class="form-control" required 
                               placeholder="Ej: Bebidas, Alimentos, Limpieza">
                        <div class="cat-help-text">
                            <i class="fas fa-info-circle"></i>
                            Nombre único y descriptivo
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-align-left"></i>
                            Descripción
                        </label>
                        <textarea name="descripcion" class="form-control" rows="3" 
                                  placeholder="Descripción opcional de la categoría..."></textarea>
                        <div class="cat-help-text">
                            <i class="fas fa-lightbulb"></i>
                            Ayuda a identificar mejor la categoría
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Crear categoría
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR CATEGORÍA -->
<div class="modal fade cat-modal" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-edit"></i>
                    Editar Categoría
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/categorias/actualizar') ?>">
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-tag"></i>
                            Nombre de la categoría
                            <span class="cat-required">*</span>
                        </label>
                        <input type="text" name="nombre" id="editNombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-align-left"></i>
                            Descripción
                        </label>
                        <textarea name="descripcion" id="editDescripcion" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarCategoria(id, nombre, descripcion) {
    document.getElementById('editId').value = id;
    document.getElementById('editNombre').value = nombre;
    document.getElementById('editDescripcion').value = descripcion || '';
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>