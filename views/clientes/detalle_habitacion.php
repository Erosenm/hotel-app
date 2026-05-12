<style>
.galeria-principal { width:100%; height:420px; object-fit:cover; border-radius:16px; cursor:pointer; }
.galeria-thumb { width:80px; height:60px; object-fit:cover; border-radius:8px; cursor:pointer; border:2px solid transparent; transition:.2s; }
.galeria-thumb:hover, .galeria-thumb.activa { border-color:#0f3460; }
.detalle-card { border:none; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); }
.amenity-item { display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #f0f0f0; }
.amenity-item:last-child { border-bottom:none; }
</style>

<div style="max-width:1100px;margin:0 auto;padding:30px 20px;">
    <nav class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= url('/') ?>">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?= url('habitaciones') ?>">Habitaciones</a></li>
            <li class="breadcrumb-item active">Habitación <?= htmlspecialchars($habitacion['numero']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <div class="col-lg-7">
            <?php if (!empty($imagenes)): ?>
                <img id="imgPrincipal" src="<?= url($imagenes[0]['rutaImagen']) ?>" alt="Habitación" class="galeria-principal mb-3">
                <?php if (count($imagenes) > 1): ?>
                <div class="d-flex gap-2 flex-wrap">
                    <?php foreach ($imagenes as $i => $img): ?>
                        <img src="<?= url($img['rutaImagen']) ?>"
                             class="galeria-thumb <?= $i === 0 ? 'activa' : '' ?>"
                             onclick="cambiarFoto(this, '<?= url($img['rutaImagen']) ?>')"
                             alt="foto <?= $i+1 ?>">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <div style="width:100%;height:420px;background:#f1f3f5;border-radius:16px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-bed fa-5x text-muted"></i>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-5">
            <div class="detalle-card p-4 mb-3">
                <span style="font-size:.75rem;letter-spacing:.15em;text-transform:uppercase;color:#0f3460;font-weight:600;">
                    <?= htmlspecialchars($habitacion['tipo']) ?>
                </span>
                <h2 class="fw-bold mt-1 mb-1">Habitación <?= htmlspecialchars($habitacion['numero']) ?></h2>
                <p class="text-muted mb-3">Piso <?= $habitacion['piso'] ?></p>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div>
                        <span style="font-size:2rem;font-weight:700;color:#0f3460;">
                            Bs. <?= number_format($habitacion['precio'], 2) ?>
                        </span>
                        <span class="text-muted"> / noche</span>
                    </div>
                    <span class="badge bg-success fs-6"><?= htmlspecialchars($habitacion['estado']) ?></span>
                </div>
                <p class="text-muted mb-4"><?= htmlspecialchars($habitacion['tipo_desc'] ?? '') ?></p>

                <h6 class="fw-bold mb-3">Lo que incluye</h6>
                <div class="mb-4">
                    <div class="amenity-item"><i class="fas fa-wifi text-primary"></i> WiFi de alta velocidad</div>
                    <div class="amenity-item"><i class="fas fa-tv text-primary"></i> Televisor con cable</div>
                    <div class="amenity-item"><i class="fas fa-bath text-primary"></i> Baño privado con ducha</div>
                    <div class="amenity-item"><i class="fas fa-snowflake text-primary"></i> Aire acondicionado</div>
                    <div class="amenity-item"><i class="fas fa-concierge-bell text-primary"></i> Servicio a la habitación</div>
                    <?php if (stripos($habitacion['tipo'], 'suite') !== false): ?>
                    <div class="amenity-item"><i class="fas fa-hot-tub text-primary"></i> Jacuzzi privado</div>
                    <?php endif; ?>
                </div>

                <?php if ($habitacion['estado'] === 'Disponible'): ?>
                    <?php if (!empty($_SESSION['usuario'])): ?>
                        <a href="<?= url('reservar?id=' . $habitacion['idHabitacion']) ?>"
                           class="btn btn-primary w-100 py-3" style="border-radius:10px;">
                            <i class="fas fa-calendar-plus me-2"></i>Reservar esta habitación
                        </a>
                    <?php else: ?>
                        <a href="<?= url('login') ?>" class="btn btn-primary w-100 py-3" style="border-radius:10px;">
                            <i class="fas fa-sign-in-alt me-2"></i>Inicia sesión para reservar
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn btn-secondary w-100 py-3" disabled style="border-radius:10px;">
                        <i class="fas fa-ban me-2"></i>No disponible ahora
                    </button>
                <?php endif; ?>

                <a href="<?= url('habitaciones') ?>" class="btn btn-outline-secondary w-100 mt-2" style="border-radius:10px;">
                    <i class="fas fa-arrow-left me-2"></i>Ver más habitaciones
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function cambiarFoto(thumb, src) {
    document.getElementById('imgPrincipal').src = src;
    document.querySelectorAll('.galeria-thumb').forEach(t => t.classList.remove('activa'));
    thumb.classList.add('activa');
}
</script>