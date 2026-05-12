<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Nueva Habitación</h4>
    <a href="<?= url('admin/habitaciones') ?>" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver
    </a>
</div>

<div class="card border-0 shadow-sm" style="max-width:650px">
    <div class="card-body p-4">
        <form action="<?= url('admin/habitaciones/crear') ?>" method="POST" enctype="multipart/form-data">
            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">N° Habitación *</label>
                    <input type="text" name="numero" class="form-control" placeholder="Ej: 101" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Piso *</label>
                    <input type="number" name="piso" class="form-control" placeholder="Ej: 1" min="1" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo *</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Seleccionar tipo</option>
                        <?php foreach ($tipos as $t): ?>
                            <option value="<?= $t['idTipoHabitacion'] ?>">
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
                            <option value="<?= $e['idEstado'] ?>">
                                <?= htmlspecialchars($e['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-images me-1 text-primary"></i>Imágenes de la habitación
                        <small class="text-muted fw-normal">(opcional, máx. 5 MB por imagen)</small>
                    </label>
                    <input type="file" name="imagenes[]" id="inputImagenes" class="form-control"
                           accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                    <div class="form-text">Puedes seleccionar varias imágenes a la vez. Formatos: JPG, PNG, WEBP, GIF.</div>
                </div>

                <div class="col-12">
                    <div id="previewContainer" class="d-flex flex-wrap gap-2 mt-1"></div>
                </div>

                <div class="col-12 pt-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save me-2"></i>Guardar Habitación
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
            wrap.style.cssText = 'position:relative;width:100px;height:80px;';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.cssText = 'width:100%;height:100%;object-fit:cover;border-radius:6px;border:1px solid #dee2e6;';
            wrap.appendChild(img);
            container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
});
</script>