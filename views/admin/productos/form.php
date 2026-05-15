<?php $editando = isset($producto); ?>

<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleproductos/form.css') ?>">

<div class="prod-form-container">
    <!-- Header -->
    <div class="prod-form-header">
        <div class="prod-form-title">
            <i class="fas fa-<?= $editando ? 'edit' : 'plus-circle' ?>"></i>
            <span><?= $editando ? 'Editar Producto' : 'Nuevo Producto' ?></span>
            <?php if ($editando): ?>
                <span class="prod-form-status-badge">
                    <i class="fas fa-<?= $producto['estado'] === 'Activo' ? 'check-circle' : 'pause-circle' ?>"></i>
                    <?= $producto['estado'] ?>
                </span>
            <?php endif; ?>
        </div>
        <a href="<?= url('admin/productos') ?>" class="prod-form-btn-back">
            <i class="fas fa-arrow-left"></i>
            Volver al inventario
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="prod-form-alert prod-form-alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Formulario Card -->
    <div class="prod-form-card">
        <div class="prod-form-card-body">
            <form method="POST" action="<?= url($editando ? 'admin/productos/actualizar' : 'admin/productos/crear') ?>">
                <?php if ($editando): ?>
                    <input type="hidden" name="id" value="<?= $producto['idProducto'] ?>">
                <?php endif; ?>

                <!-- Nombre del producto -->
                <div class="prod-form-group">
                    <label class="prod-form-label">
                        <i class="fas fa-tag"></i>
                        Nombre del producto
                        <span class="required">*</span>
                    </label>
                    <input type="text" name="nombre" class="prod-form-input"
                           value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" 
                           placeholder="Ej: Coca Cola 500ml"
                           required>
                </div>

                <!-- Descripción -->
                <div class="prod-form-group">
                    <label class="prod-form-label">
                        <i class="fas fa-align-left"></i>
                        Descripción
                    </label>
                    <textarea name="descripcion" class="prod-form-textarea" 
                              rows="3" 
                              placeholder="Descripción detallada del producto..."><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
                    <div class="prod-form-help">
                        <i class="fas fa-info-circle"></i>
                        Descripción opcional, se mostrará en la lista de productos
                    </div>
                </div>

                <!-- Categoría y Unidad (2 columnas) -->
                <div class="prod-form-row">
                    <div class="prod-form-group">
                        <label class="prod-form-label">
                            <i class="fas fa-folder"></i>
                            Categoría
                            <span class="required">*</span>
                        </label>
                        <select name="idCategoria" class="prod-form-select" required>
                            <option value="">— Seleccionar categoría —</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['idCategoria'] ?>"
                                    <?= (isset($producto) && $producto['idCategoria_FK'] == $c['idCategoria']) ? 'selected' : '' ?>>
                                    <i class="fas fa-tag"></i> <?= htmlspecialchars($c['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="prod-form-group">
                        <label class="prod-form-label">
                            <i class="fas fa-cube"></i>
                            Unidad de medida
                        </label>
                        <select name="unidad" class="prod-form-select">
                            <optgroup label="Unidades comunes">
                                <?php 
                                $unidades = ['unidad', 'botella', 'lata', 'vaso', 'copa', 'porción', 'plato', 'bolsa', 'prenda', 'sesión', 'viaje'];
                                foreach ($unidades as $u): ?>
                                    <option value="<?= $u ?>" <?= (($producto['unidad'] ?? 'unidad') === $u) ? 'selected' : '' ?>>
                                        📦 <?= ucfirst($u) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Peso/Volumen">
                                <?php 
                                $unidadesPeso = ['kg', 'g', 'lb', 'litro', 'ml'];
                                foreach ($unidadesPeso as $u): ?>
                                    <option value="<?= $u ?>" <?= (($producto['unidad'] ?? 'unidad') === $u) ? 'selected' : '' ?>>
                                        ⚖️ <?= strtoupper($u) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <div class="prod-form-help">
                            <i class="fas fa-lightbulb"></i>
                            Ej: unidad, botella, kg, litro
                        </div>
                    </div>
                </div>

                <!-- Precio, Stock y Stock mínimo (3 columnas) -->
                <div class="prod-form-row" style="grid-template-columns: repeat(3, 1fr);">
                    <div class="prod-form-group">
                        <label class="prod-form-label">
                            <i class="fas fa-dollar-sign"></i>
                            Precio
                            <span class="required">*</span>
                        </label>
                        <div class="prod-form-price-wrapper">
                            <input type="number" name="precio" class="prod-form-input" 
                                   step="0.01" min="0"
                                   value="<?= htmlspecialchars($producto['precio'] ?? '') ?>" 
                                   placeholder="0.00"
                                   required>
                        </div>
                    </div>

                    <div class="prod-form-group">
                        <label class="prod-form-label">
                            <i class="fas fa-boxes"></i>
                            Stock actual
                        </label>
                        <input type="number" name="stock" class="prod-form-input" 
                               min="0"
                               value="<?= htmlspecialchars($producto['stock'] ?? 0) ?>">
                        <div class="prod-form-stock-info">
                            <span><i class="fas fa-info-circle"></i> Stock disponible</span>
                            <?php if (isset($producto) && $producto['stock'] <= $producto['stockMinimo'] && $producto['stock'] > 0): ?>
                                <span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Stock bajo</span>
                            <?php elseif (isset($producto) && $producto['stock'] == 0): ?>
                                <span class="text-danger"><i class="fas fa-times-circle"></i> Agotado</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="prod-form-group">
                        <label class="prod-form-label">
                            <i class="fas fa-flag-checkered"></i>
                            Stock mínimo
                        </label>
                        <input type="number" name="stockMinimo" class="prod-form-input" 
                               min="0"
                               value="<?= htmlspecialchars($producto['stockMinimo'] ?? 5) ?>">
                        <div class="prod-form-help">
                            <i class="fas fa-bell"></i>
                            Alerta cuando el stock llegue a este nivel
                        </div>
                    </div>
                </div>

                <!-- Estado (solo en edición) -->
                <?php if ($editando): ?>
                    <div class="prod-form-group">
                        <label class="prod-form-label">
                            <i class="fas fa-toggle-on"></i>
                            Estado del producto
                        </label>
                        <select name="estado" class="prod-form-select">
                            <option value="Activo" <?= ($producto['estado'] === 'Activo') ? 'selected' : '' ?>>
                                <i class="fas fa-check-circle text-success"></i> Activo - Visible en el sistema
                            </option>
                            <option value="Inactivo" <?= ($producto['estado'] === 'Inactivo') ? 'selected' : '' ?>>
                                <i class="fas fa-ban text-danger"></i> Inactivo - Oculto en el sistema
                            </option>
                        </select>
                        <div class="prod-form-help">
                            <i class="fas fa-eye-slash"></i>
                            Los productos inactivos no aparecerán en reservas ni ventas
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Separador visual -->
                <div class="prod-form-separator"></div>

                <!-- Botones de acción -->
                <div class="prod-form-actions">
                    <button type="submit" class="prod-form-btn-primary">
                        <i class="fas fa-save"></i>
                        <?= $editando ? 'Actualizar producto' : 'Crear producto' ?>
                    </button>
                    <a href="<?= url('admin/productos') ?>" class="prod-form-btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-formatear precio al escribir
document.querySelector('input[name="precio"]')?.addEventListener('input', function(e) {
    let value = this.value.replace(/[^0-9.]/g, '');
    if (value) {
        let parts = value.split('.');
        if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
        if (parts[1] && parts[1].length > 2) value = parts[0] + '.' + parts[1].substring(0, 2);
        this.value = value;
    }
});

// Validar que stock mínimo no sea mayor que stock actual (solo sugerencia visual)
document.querySelector('input[name="stockMinimo"]')?.addEventListener('change', function() {
    const stock = parseInt(document.querySelector('input[name="stock"]')?.value) || 0;
    const stockMin = parseInt(this.value) || 0;
    if (stockMin > stock && stock > 0) {
        this.style.borderColor = 'var(--prod-form-warning)';
        this.title = 'El stock mínimo es mayor que el stock actual';
    } else {
        this.style.borderColor = '';
    }
});
</script>