<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylehabitaciones/editar.css') ?>">

<div class="">
    <div class="d-flex align-items-center gap-2">
        <h4 class="fw-bold mb-0 text-dark">
            <i class="fas fa-edit text-primary me-2"></i>Editar Habitación
        </h4>
    </div>
    <a href="<?= url('admin/habitaciones') ?>" class="btn btn-outline-secondary btn-volver">
        <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="edit-container">
    
    <div class="room-summary-card">
        <div class="summary-icon">
            <i class="fas fa-door-closed"></i>
        </div>
        <h5 class="room-title">Habitación <?= htmlspecialchars($habitacion['numero']) ?></h5>
        <p class="room-subtitle">Piso <?= htmlspecialchars($habitacion['piso']) ?></p>
        
        <hr class="my-4 text-muted">
        
        <div class="summary-details">
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-bed me-2"></i>Tipo</span>
                <span class="detail-value">—</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-money-bill-wave me-2"></i>Precio base</span>
                <span class="detail-value text-success fw-bold">Bs. 0.00</span>
            </div>
        </div>
        
        <div class="summary-alert mt-4">
            <i class="fas fa-info-circle text-primary mb-2 fs-5"></i>
            <p class="mb-0">Puedes actualizar el tipo, estado e imágenes. El número y piso son únicos.</p>
        </div>
    </div>

    <div class="form-main-card">
        <form action="<?= url('admin/habitaciones/editar') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $habitacion['idHabitacion'] ?>">

            <div class="form-section">
                <h6 class="section-title"><i class="fas fa-info-circle"></i> DATOS BÁSICOS</h6>
                
                <div class="row g-4 mt-1">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">N° Habitación</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-door-open input-icon"></i>
                            <input type="text" name="numero" class="form-control custom-input" value="<?= htmlspecialchars($habitacion['numero']) ?>" required>
                        </div>
                        <small class="text-muted lock-text mt-1 d-block"><i class="fas fa-lock me-1"></i> No modificable</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Piso</label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-building input-icon"></i>
                            <input type="number" name="piso" class="form-control custom-input" value="<?= htmlspecialchars($habitacion['piso']) ?>" min="1" required>
                        </div>
                        <small class="text-muted lock-text mt-1 d-block"><i class="fas fa-lock me-1"></i> No modificable</small>
                    </div>

                    <div class="col-md-6 mt-4">
                        <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-bed input-icon"></i>
                            <select name="tipo" class="form-select custom-select" required>
                                <option value="">Seleccionar tipo</option>
                                <?php foreach ($tipos as $t): ?>
                                    <option value="<?= $t['idTipoHabitacion'] ?>" <?= $habitacion['idTipoHabitacion_FK'] == $t['idTipoHabitacion'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['nombre']) ?> — Bs. <?= number_format($t['precioBase'], 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mt-4">
                        <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="fas fa-dot-circle input-icon"></i>
                            <select name="estado" class="form-select custom-select" required>
                                <option value="">Seleccionar estado</option>
                                <?php foreach ($estados as $e): ?>
                                    <option value="<?= $e['idEstado'] ?>" <?= $habitacion['idEstadoHabitacion_FK'] == $e['idEstado'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($e['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="section-divider">

            <?php if (!empty($imagenes)): ?>
            <div class="form-section">
                <h6 class="section-title"><i class="fas fa-images"></i> IMÁGENES ACTUALES</h6>
                <div class="current-images-grid mt-3">
                    <?php foreach ($imagenes as $img): ?>
                    <div class="image-box">
                        <img src="<?= asset($img['rutaImagen']) ?>" alt="Imagen habitación">
                        <a href="<?= url('admin/habitaciones/imagen/eliminar?idImagen=' . $img['idImagen'] . '&idHabitacion=' . $habitacion['idHabitacion']) ?>"
                           class="btn-delete-image"
                           onclick="return confirm('¿Eliminar esta imagen?')"
                           title="Eliminar imagen">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <hr class="section-divider">
            <?php endif; ?>

            <div class="form-section">
                <h6 class="section-title"><i class="fas fa-cloud-upload-alt"></i> AGREGAR NUEVAS IMÁGENES</h6>
                <p class="text-muted small mb-3">Seleccionar imágenes (máx. 5 MB c/u)</p>
                
                <div class="upload-drag-zone" onclick="document.getElementById('inputImagenes').click()">
                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                    <p class="upload-title">Haz clic para seleccionar o arrastra archivos aquí</p>
                    <p class="upload-subtitle">JPG • PNG • WEBP • GIF</p>
                    <input type="file" name="imagenes[]" id="inputImagenes" accept="image/jpeg,image/png,image/webp,image/gif" multiple style="display: none;">
                </div>
                
                <div id="previewContainer" class="preview-grid mt-3"></div>
            </div>

            <div class="form-actions mt-5">
                <button type="submit" class="btn btn-primary btn-guardar">
                    <i class="fas fa-save me-2"></i>Guardar Cambios
                </button>
                <a href="<?= url('admin/habitaciones') ?>" class="btn btn-light btn-cancelar">Cancelar</a>
            </div>

        </form>
    </div>
</div>

<script>
document.getElementById('inputImagenes').addEventListener('change', function () {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    
    Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.className = 'preview-box';
            
            const img = document.createElement('img');
            img.src = e.target.result;
            
            const badge = document.createElement('span');
            badge.textContent = 'Nueva';
            badge.className = 'preview-badge';
            
            wrap.appendChild(img);
            wrap.appendChild(badge);
            container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
});
</script>