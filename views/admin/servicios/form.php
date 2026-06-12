<?php $editando = isset($servicio); ?>

<link rel="stylesheet" href="<?= asset('css/cssAdmin/styleservicios/form.css') ?>">

<div class="serv-form-container">
    <!-- Header -->
    <div class="serv-header">
        <div class="serv-title">
            <i class="fas fa-<?= $editando ? 'edit' : 'spa' ?>"></i>
            <span><?= $editando ? 'Editar Servicio' : 'Nuevo Servicio' ?></span>
            <?php if ($editando): ?>
                <span style="font-size: 0.7rem; background: var(--serv-accent-light); padding: 0.2rem 0.6rem; border-radius: 20px; color: var(--serv-accent);">
                    <i class="fas fa-<?= $servicio['estado'] ?? 'check-circle' ?>"></i>
                    ID: <?= $servicio['idServicio'] ?>
                </span>
            <?php endif; ?>
        </div>
        <a href="<?= url('admin/servicios') ?>" class="serv-btn-back">
            <i class="fas fa-arrow-left"></i>
            Volver a servicios
        </a>
    </div>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="serv-alert serv-alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Formulario Card -->
    <div class="serv-card">
        <div class="serv-card-header">
            <h3>
                <i class="fas fa-concierge-bell"></i>
                <?= $editando ? 'Información del servicio' : 'Datos del nuevo servicio' ?>
            </h3>
        </div>
        <div class="serv-card-body">
            <form method="POST" action="<?= url($editando ? 'admin/servicios/actualizar' : 'admin/servicios/crear') ?>">
                <?php if ($editando): ?>
                    <input type="hidden" name="id" value="<?= $servicio['idServicio'] ?>">
                <?php endif; ?>

                <!-- Nombre del servicio -->
                <div class="serv-form-group">
                    <label class="serv-form-label">
                        <i class="fas fa-tag"></i>
                        Nombre del servicio
                        <span class="required">*</span>
                    </label>
                    <input type="text" name="nombre" class="serv-form-input"
                           value="<?= htmlspecialchars($servicio['nombre'] ?? '') ?>"
                           placeholder="Ej: Masaje relajante 60min, Cena romántica, Spa completo..."
                           required>
                    <div class="serv-help-text">
                        <i class="fas fa-info-circle"></i>
                        Nombre descriptivo y fácil de identificar
                    </div>
                </div>

                <!-- Precio -->
                <div class="serv-form-group">
                    <label class="serv-form-label">
                        <i class="fas fa-dollar-sign"></i>
                        Precio del servicio
                        <span class="required">*</span>
                    </label>
                    <div class="serv-price-wrapper">
                        <input type="number" name="precio" class="serv-form-input" 
                               step="0.01" min="0"
                               value="<?= htmlspecialchars($servicio['precio'] ?? '') ?>" 
                               placeholder="0.00"
                               required>
                    </div>
                    <div class="serv-help-text">
                        <i class="fas fa-lightbulb"></i>
                        Precio en Bolivianos (Bs.) - Puede incluir decimales
                    </div>
                </div>

                <!-- Separador visual -->
                <div class="serv-separator"></div>

                <!-- Vista previa en tiempo real (opcional) -->
                <div class="serv-preview" id="previewServicio" style="display: none;">
                    <p>
                        <i class="fas fa-eye"></i> Vista previa:<br>
                        <strong id="previewNombre">—</strong> — 
                        <strong id="previewPrecio">Bs. 0.00</strong>
                    </p>
                </div>

                <!-- Botones de acción -->
                <div class="serv-form-actions">
                    <button type="submit" class="serv-btn-primary">
                        <i class="fas fa-save"></i>
                        <?= $editando ? 'Actualizar servicio' : 'Crear servicio' ?>
                    </button>
                    <a href="<?= url('admin/servicios') ?>" class="serv-btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Vista previa en tiempo real (opcional, no afecta funcionamiento)
const nombreInput = document.querySelector('input[name="nombre"]');
const precioInput = document.querySelector('input[name="precio"]');
const previewDiv = document.getElementById('previewServicio');
const previewNombre = document.getElementById('previewNombre');
const previewPrecio = document.getElementById('previewPrecio');

function actualizarPreview() {
    const nombre = nombreInput?.value.trim();
    const precio = precioInput?.value;
    
    if (nombre || (precio && parseFloat(precio) > 0)) {
        previewDiv.style.display = 'block';
        previewNombre.textContent = nombre || '[Nombre del servicio]';
        const precioNum = parseFloat(precio) || 0;
        previewPrecio.textContent = `Bs. ${precioNum.toFixed(2)}`;
    } else {
        previewDiv.style.display = 'none';
    }
}

nombreInput?.addEventListener('input', actualizarPreview);
precioInput?.addEventListener('input', actualizarPreview);

// Formatear precio al escribir (solo números y decimales)
precioInput?.addEventListener('input', function(e) {
    let value = this.value.replace(/[^0-9.]/g, '');
    if (value) {
        let parts = value.split('.');
        if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
        if (parts[1] && parts[1].length > 2) value = parts[0] + '.' + parts[1].substring(0, 2);
        this.value = value;
    }
});

// Limpiar si está vacío
precioInput?.addEventListener('blur', function() {
    if (!this.value || parseFloat(this.value) === 0) {
        this.value = '';
    }
});

// Trigger inicial
actualizarPreview();
</script>