<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-tags me-2 text-primary"></i>Categorías de Productos
    </h4>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalCrear">
        <i class="fas fa-plus me-1"></i>Nueva categoría
    </button>
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

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Productos</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($categorias)): ?>
                <tr><td colspan="4" class="text-center text-muted py-4">Sin categorías registradas</td></tr>
            <?php else: ?>
            <?php foreach ($categorias as $c): ?>
            <tr>
                <td class="fw-semibold"><?= htmlspecialchars($c['nombre']) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($c['descripcion'] ?? '—') ?></td>
                <td class="text-center">
                    <span class="badge bg-<?= $c['total_productos'] > 0 ? 'primary' : 'secondary' ?>">
                        <?= $c['total_productos'] ?>
                    </span>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1"
                            onclick="editarCategoria(<?= $c['idCategoria'] ?>, '<?= htmlspecialchars(addslashes($c['nombre'])) ?>', '<?= htmlspecialchars(addslashes($c['descripcion'] ?? '')) ?>')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <?php if ($c['total_productos'] == 0): ?>
                    <a href="<?= url('admin/categorias/eliminar?id=' . $c['idCategoria']) ?>"
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('¿Eliminar esta categoría?')">
                        <i class="fas fa-trash"></i>
                    </a>
                    <?php else: ?>
                    <button class="btn btn-sm btn-outline-danger" disabled title="Tiene productos asociados">
                        <i class="fas fa-trash"></i>
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal crear -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="fas fa-plus me-2"></i>Nueva Categoría</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/categorias/crear') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej: Bebidas">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2" placeholder="Descripción opcional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal editar -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="fas fa-edit me-2"></i>Editar Categoría</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/categorias/actualizar') ?>">
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="editNombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" id="editDescripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editarCategoria(id, nombre, descripcion) {
    document.getElementById('editId').value          = id;
    document.getElementById('editNombre').value      = nombre;
    document.getElementById('editDescripcion').value = descripcion;
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>