<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editar Habitación</h4>
    <a href="<?= url('admin/habitaciones') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:650px">
    <div class="card-body p-4">
        <form action="<?= url('admin/habitaciones/editar') ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $habitacion['idHabitacion'] ?>">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">N° Habitación *</label>
                    <input type="text" name="numero" class="form-control"
                           value="<?= htmlspecialchars($habitacion['numero']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Piso *</label>
                    <input type="number" name="piso" class="form-control"
                           value="<?= htmlspecialchars($habitacion['piso']) ?>" min="1" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo *</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Seleccionar tipo</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['idTipoHabitacion'] ?>"
                                <?= $habitacion['idTipoHabitacion_FK'] == $t['idTipoHabitacion'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($t['nombre']) ?> — Bs. <?= number_format($t['precioBase'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Estado *</label>
                    <select name="estado" class="form-select" required>
                        <option value="">Seleccionar estado</option>
                        <?php foreach ($estados as $e): ?>
                            <option value="<?= $e['idEstado'] ?>"
                                <?= $habitacion['idEstadoHabitacion_FK'] == $e['idEstado'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (!empty($imagenes)): ?>
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-images me-1 text-primary"></i>Imágenes actuales
                    </label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($imagenes as $img): ?>
                        <div style="position:relative;width:110px;">
                            <img src="<?= asset($img['rutaImagen']) ?>"
                                 alt="Imagen habitación"
                                 style="width:110px;height:85px;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;">
                            <a href="<?= url('admin/habitaciones/imagen/eliminar?idImagen=' . $img['idImagen'] . '&idHabitacion=' . $habitacion['idHabitacion']) ?>"
                               class="btn btn-danger btn-sm"
                               style="position:absolute;top:3px;right:3px;padding:1px 5px;font-size:11px;line-height:1.4;"
                               onclick="return confirm('¿Eliminar esta imagen?')"
                               title="Eliminar imagen">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="col-12">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-cloud-upload-alt me-1 text-primary"></i>Agregar nuevas imágenes
                        <small class="text-muted fw-normal">(máx. 5 MB por imagen)</small>
                    </label>
                    <input type="file" name="imagenes[]" id="inputImagenes" class="form-control"
                           accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                    <div class="form-text">Las imágenes nuevas se agregarán a las existentes.</div>
                </div>

                <div class="col-12">
                    <div id="previewContainer" class="d-flex flex-wrap gap-2 mt-1"></div>
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
document.getElementById('inputImagenes').addEventListener('change', function () {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'position:relative;width:110px;';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:110px;height:85px;object-fit:cover;border-radius:6px;border:2px dashed #0d6efd;';
            const badge = document.createElement('span');
            badge.textContent = 'Nueva';
            badge.style.cssText = 'position:absolute;bottom:4px;left:4px;background:#0d6efd;color:#fff;font-size:10px;padding:1px 5px;border-radius:4px;';
            wrap.appendChild(img);
            wrap.appendChild(badge);
            container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
});
</script>