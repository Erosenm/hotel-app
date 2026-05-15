<link rel="stylesheet" href="<?= asset('css/cssAdmin/stylehabitaciones/crear.css') ?>">

<!-- ══ Header ══ -->
<div class="chb-header">
    <div class="chb-title">
        <i class="fas fa-plus-circle"></i>
        <span>Nueva Habitación</span>
    </div>
    <a href="<?= url('admin/habitaciones') ?>" class="chb-btn-back">
        <i class="fas fa-arrow-left"></i> Volver
    </a>
</div>

<!-- ══ Layout: panel ayuda + formulario ══ -->
<div class="chb-layout">

    <!-- ── Panel lateral izquierdo ── -->
    <aside class="chb-sidebar-help">

        <div class="chb-help-icon">
            <i class="fas fa-door-open"></i>
        </div>
        <div class="chb-help-title">Registrar habitación</div>
        <div class="chb-help-desc">Completa los datos básicos de la habitación para agregarla al sistema.</div>

        <div class="chb-help-divider"></div>

        <!-- Información de tipos -->
        <div class="chb-help-subtitle"><i class="fas fa-list"></i> Tipos disponibles</div>
        <div class="chb-info-box">
            <p class="chb-info-text">Los tipos están predefinidos en el sistema. Cada uno tiene características y precio base diferentes.</p>
            <div class="chb-type-example">
                <i class="fas fa-check-circle"></i> Simple
            </div>
            <div class="chb-type-example">
                <i class="fas fa-check-circle"></i> Doble
            </div>
            <div class="chb-type-example">
                <i class="fas fa-check-circle"></i> Suite
            </div>
        </div>

        <div class="chb-help-divider"></div>

        <!-- Estados -->
        <div class="chb-help-subtitle"><i class="fas fa-circle-info"></i> Estados iniciales</div>
        <div class="chb-status-item green">
            <span class="chb-status-dot"></span> Disponible
        </div>
        <div class="chb-status-item red">
            <span class="chb-status-dot"></span> Ocupada
        </div>
        <div class="chb-status-item gray">
            <span class="chb-status-dot"></span> Mantenimiento
        </div>

        <div class="chb-help-divider"></div>

        <div class="chb-help-note">
            <i class="fas fa-lightbulb"></i>
            Puedes agregar hasta 5 imágenes por habitación. Las imágenes deben ser PNG, JPG, WEBP o GIF.
        </div>

    </aside>

    <!-- ── Card del formulario ══ -->
    <div class="chb-card">

        <form action="<?= url('admin/habitaciones/crear') ?>" method="POST" enctype="multipart/form-data">
            <div class="chb-form">

                <!-- ── Sección: Datos básicos ── -->
                <div class="chb-section-label">
                    <i class="fas fa-info-circle"></i> Datos básicos
                </div>

                <div class="chb-row-2">
                    <div class="chb-field">
                        <label class="chb-label">N° Habitación <span class="chb-req">*</span></label>
                        <div class="chb-input-icon">
                            <i class="fas fa-door-open"></i>
                            <input type="text" name="numero" class="chb-input has-icon" placeholder="Ej: 101" required>
                        </div>
                    </div>
                    <div class="chb-field">
                        <label class="chb-label">Piso <span class="chb-req">*</span></label>
                        <div class="chb-input-icon">
                            <i class="fas fa-building"></i>
                            <input type="number" name="piso" class="chb-input has-icon" placeholder="Ej: 1" min="1" required>
                        </div>
                    </div>
                </div>

                <div class="chb-row-2">
                    <div class="chb-field">
                        <label class="chb-label">Tipo <span class="chb-req">*</span></label>
                        <div class="chb-input-icon">
                            <i class="fas fa-door-closed"></i>
                            <select name="tipo" class="chb-input chb-select has-icon" required>
                                <option value="">Seleccionar tipo</option>
                                <?php foreach ($tipos as $t): ?>
                                    <option value="<?= $t['idTipoHabitacion'] ?>">
                                        <?= htmlspecialchars($t['nombre']) ?> — Bs. <?= number_format($t['precioBase'], 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="chb-field">
                        <label class="chb-label">Estado <span class="chb-req">*</span></label>
                        <div class="chb-input-icon">
                            <i class="fas fa-circle-dot"></i>
                            <select name="estado" class="chb-input chb-select has-icon" required>
                                <option value="">Seleccionar estado</option>
                                <?php foreach ($estados as $e): ?>
                                    <option value="<?= $e['idEstado'] ?>">
                                        <?= htmlspecialchars($e['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ── Sección: Imágenes ── -->
                <div class="chb-section-label" style="margin-top:.5rem">
                    <i class="fas fa-images"></i> Imágenes de la habitación
                </div>

                <div class="chb-row-1">
                    <div class="chb-field">
                        <label class="chb-label">Seleccionar imágenes <span class="chb-hint">(máx. 5 MB c/u)</span></label>
                        <div class="chb-file-input-wrap">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <input type="file" name="imagenes[]" id="inputImagenes" class="chb-file-input"
                                   accept="image/jpeg,image/png,image/webp,image/gif" multiple>
                            <label for="inputImagenes" class="chb-file-label">
                                Haz clic para seleccionar o arrastra archivos aquí
                                <span class="chb-file-formats">JPG • PNG • WEBP • GIF</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="chb-preview-container" id="previewContainer"></div>

                <!-- ── Submit ── -->
                <div class="chb-submit-row">
                    <button type="submit" class="chb-btn-submit">
                        <i class="fas fa-save"></i> Guardar Habitación
                    </button>
                    <a href="<?= url('admin/habitaciones') ?>" class="chb-btn-cancel">
                        Cancelar
                    </a>
                </div>

            </div>
        </form>
    </div>

</div><!-- /chb-layout -->

<script>
document.getElementById('inputImagenes').addEventListener('change', function () {
    const container = document.getElementById('previewContainer');
    container.innerHTML = '';
    if (this.files.length === 0) return;

    Array.from(this.files).slice(0, 5).forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const wrap = document.createElement('div');
            wrap.className = 'chb-preview-item';
            const img = document.createElement('img');
            img.src = e.target.result;
            const badge = document.createElement('span');
            badge.className = 'chb-preview-badge';
            badge.textContent = idx + 1;
            wrap.appendChild(img);
            wrap.appendChild(badge);
            container.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
});
</script>