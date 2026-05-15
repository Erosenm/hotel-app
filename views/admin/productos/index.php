<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">
        <i class="fas fa-box me-2 text-primary"></i>Inventario de Productos
    </h4>
    <a href="<?= url('admin/productos/crear') ?>" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>Nuevo Producto
    </a>
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

<!-- Alerta stock bajo -->
<?php if ($stats['bajo_stock'] > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
    <i class="fas fa-exclamation-triangle fs-5"></i>
    <div>
        <strong><?= $stats['bajo_stock'] ?> producto(s) con stock bajo o agotado.</strong>
        Revisa el inventario y realiza un ajuste.
    </div>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-secondary"><?= $stats['total'] ?></div>
            <div class="text-muted small">Total productos</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-success"><?= $stats['activos'] ?></div>
            <div class="text-muted small">Activos</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-warning"><?= $stats['bajo_stock'] ?></div>
            <div class="text-muted small">Stock bajo</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="fs-4 fw-bold text-danger"><?= $stats['sin_stock'] ?></div>
            <div class="text-muted small">Sin stock</div>
        </div>
    </div>
</div>

<!-- Filtro por categoría -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-sm btn-primary filtro-cat active" data-cat="todos">Todos</button>
            <?php foreach ($categorias as $cat): ?>
                <button class="btn btn-sm btn-outline-secondary filtro-cat" data-cat="<?= $cat['idCategoria'] ?>">
                    <?= htmlspecialchars($cat['nombre']) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Tabla -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="tablaProductos">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Unidad</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $p):
                    $stockClase = '';
                    if ($p['stock'] == 0) $stockClase = 'text-danger fw-bold';
                    elseif ($p['stock'] <= $p['stockMinimo']) $stockClase = 'text-warning fw-bold';
                    else $stockClase = 'text-success';
                ?>
                <tr data-cat="<?= $p['idCategoria_FK'] ?>">
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($p['nombre']) ?></div>
                        <?php if ($p['descripcion']): ?>
                            <small class="text-muted"><?= htmlspecialchars(substr($p['descripcion'], 0, 50)) ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['categoria'] ?? '—') ?></span></td>
                    <td class="fw-semibold">Bs. <?= number_format($p['precio'], 2) ?></td>
                    <td>
                        <span class="<?= $stockClase ?>">
                            <?= $p['stock'] ?>
                            <?php if ($p['stock'] == 0): ?>
                                <i class="fas fa-times-circle ms-1"></i>
                            <?php elseif ($p['stock'] <= $p['stockMinimo']): ?>
                                <i class="fas fa-exclamation-triangle ms-1"></i>
                            <?php endif; ?>
                        </span>
                        <small class="text-muted d-block">Mín: <?= $p['stockMinimo'] ?></small>
                    </td>
                    <td><small class="text-muted"><?= htmlspecialchars($p['unidad']) ?></small></td>
                    <td>
                        <span class="badge bg-<?= $p['estado'] === 'Activo' ? 'success' : 'secondary' ?>">
                            <?= $p['estado'] ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <!-- Ajustar stock -->
                        <button class="btn btn-sm btn-outline-info me-1"
                                onclick="abrirAjusteStock(<?= $p['idProducto'] ?>, '<?= htmlspecialchars($p['nombre']) ?>', <?= $p['stock'] ?>)"
                                title="Ajustar stock">
                            <i class="fas fa-boxes"></i>
                        </button>
                        <a href="<?= url('admin/productos/editar?id=' . $p['idProducto']) ?>"
                           class="btn btn-sm btn-outline-primary me-1" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($p['estado'] === 'Activo'): ?>
                        <a href="<?= url('admin/productos/eliminar?id=' . $p['idProducto']) ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Desactivar este producto?')" title="Desactivar">
                            <i class="fas fa-ban"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal ajuste de stock -->
<div class="modal fade" id="modalStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title fw-semibold"><i class="fas fa-boxes me-2"></i>Ajustar Stock</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?= url('admin/productos/stock') ?>">
                <div class="modal-body">
                    <input type="hidden" name="id" id="stockId">
                    <p>Producto: <strong id="stockNombre"></strong></p>
                    <p>Stock actual: <strong id="stockActual"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipo de movimiento</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" value="entrada" id="tipoEntrada" checked>
                                <label class="form-check-label text-success fw-semibold" for="tipoEntrada">
                                    <i class="fas fa-arrow-up me-1"></i>Entrada
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" value="salida" id="tipoSalida">
                                <label class="form-check-label text-danger fw-semibold" for="tipoSalida">
                                    <i class="fas fa-arrow-down me-1"></i>Salida
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cantidad</label>
                        <input type="number" name="cantidad" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar ajuste</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirAjusteStock(id, nombre, stock) {
    document.getElementById('stockId').value      = id;
    document.getElementById('stockNombre').textContent = nombre;
    document.getElementById('stockActual').textContent = stock;
    new bootstrap.Modal(document.getElementById('modalStock')).show();
}

document.querySelectorAll('.filtro-cat').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filtro-cat').forEach(b => b.classList.remove('active','btn-primary'));
        document.querySelectorAll('.filtro-cat').forEach(b => b.classList.add('btn-outline-secondary'));
        this.classList.add('active','btn-primary');
        this.classList.remove('btn-outline-secondary');

        const cat = this.dataset.cat;
        document.querySelectorAll('#tablaProductos tbody tr').forEach(tr => {
            tr.style.display = (cat === 'todos' || tr.dataset.cat === cat) ? '' : 'none';
        });
    });
});
</script>