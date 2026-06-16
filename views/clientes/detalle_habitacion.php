<link rel="stylesheet" href="<?= asset('css/cssCliente/clientedetallehabitacion.css') ?>">

<div class="detalle-container">
    <!-- Breadcrumb -->
    <nav class="detalle-breadcrumb">
        <ol>
            <li><a href="<?= url('/') ?>">Inicio</a></li>
            <li><a href="<?= url('habitaciones') ?>">Habitaciones</a></li>
            <li class="active">Habitación <?= htmlspecialchars($habitacion['numero']) ?></li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Columna izquierda: Galería -->
        <div class="col-lg-7">
            <div class="detalle-galeria">
                <?php if (!empty($imagenes)): ?>
                    <div class="detalle-galeria-principal">
                        <img id="imgPrincipal" src="<?= asset($imagenes[0]['rutaImagen']) ?>" alt="Habitación">
                        <span class="detalle-galeria-badge"><?= htmlspecialchars($habitacion['tipo']) ?></span>
                    </div>
                    
                    <?php if (count($imagenes) > 1): ?>
                        <div class="detalle-galeria-thumbs">
                            <?php foreach ($imagenes as $i => $img): ?>
                                <img src="<?= asset($img['rutaImagen']) ?>"
                                     class="detalle-galeria-thumb <?= $i === 0 ? 'activa' : '' ?>"
                                     onclick="cambiarFoto(this, '<?= asset($img['rutaImagen']) ?>')"
                                     alt="Foto <?= $i+1 ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="detalle-galeria-principal">
                        <div class="detalle-galeria-principal-placeholder">
                            <i class="fas fa-bed"></i>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna derecha: Información -->
        <div class="col-lg-5">
            <div class="detalle-card">
                <span class="detalle-tipo"><?= htmlspecialchars($habitacion['tipo']) ?></span>
                <h1 class="detalle-titulo">Habitación <?= htmlspecialchars($habitacion['numero']) ?></h1>
                <p class="detalle-piso">
                    <i class="fas fa-building"></i> Piso <?= $habitacion['piso'] ?>
                </p>

                <div class="detalle-precio-estado">
                    <div class="detalle-precio">
                        <span class="monto">Bs. <?= number_format($habitacion['precio'], 2) ?></span>
                        <span class="periodo">/ noche</span>
                    </div>
                    <?php if ($habitacion['estado'] === 'Disponible'): ?>
                        <span class="detalle-estado-badge disponible">
                            <i class="fas fa-check-circle"></i> Disponible
                        </span>
                    <?php else: ?>
                        <span class="detalle-estado-badge no-disponible">
                            <i class="fas fa-times-circle"></i> No disponible
                        </span>
                    <?php endif; ?>
                </div>

                <p class="detalle-descripcion">
                    <?= htmlspecialchars($habitacion['tipo_desc'] ?? 'Habitación confortable con todas las comodidades para tu estadía.') ?>
                </p>

                <div class="detalle-amenities">
                    <div class="detalle-amenities-titulo">
                        <i class="fas fa-star"></i> Lo que incluye
                    </div>
                    <div class="detalle-amenities-grid">
                        <div class="detalle-amenity-item">
                            <i class="fas fa-wifi"></i> WiFi con cable
                        </div>
                        <div class="detalle-amenity-item">
                            <i class="fas fa-bath"></i> Baño privado con ducha
                        </div>
                        <div class="detalle-amenity-item">
                            <i class="fas fa-snowflake"></i> Aire acondicionado
                        </div>
                        <div class="detalle-amenity-item">
                            <i class="fas fa-concierge-bell"></i> Servicio a la habitación
                        </div>
                        <?php if (stripos($habitacion['tipo'], 'suite') !== false): ?>
                            <div class="detalle-amenity-item">
                                <i class="fas fa-hot-tub"></i> Jacuzzi privado
                            </div>
                        <?php endif; ?>
                        <div class="detalle-amenity-item">
                            <i class="fas fa-tv"></i> TV pantalla plana
                        </div>
                        <div class="detalle-amenity-item">
                            <i class="fas fa-coffee"></i> Cafetera / Tetera
                        </div>
                    </div>
                </div>

                <div class="detalle-actions">
                    <?php if ($habitacion['estado'] === 'Disponible'): ?>
                        <?php if (!empty($_SESSION['usuario'])): ?>
                            <a href="<?= url('reservar?id=' . $habitacion['idHabitacion']) ?>"
                               class="detalle-btn-primary">
                                <i class="fas fa-calendar-plus"></i> Reservar esta habitación
                            </a>
                        <?php else: ?>
                            <a href="<?= url('login') ?>" class="detalle-btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Inicia sesión para reservar
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <button class="detalle-btn-primary" disabled>
                            <i class="fas fa-ban"></i> No disponible ahora
                        </button>
                    <?php endif; ?>

                    <a href="<?= url('habitaciones') ?>" class="detalle-btn-secondary">
                        <i class="fas fa-arrow-left"></i> Ver más habitaciones
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cambiarFoto(thumb, src) {
    const principal = document.getElementById('imgPrincipal');
    if (principal) {
        principal.src = src;
    }
    document.querySelectorAll('.detalle-galeria-thumb').forEach(t => t.classList.remove('activa'));
    thumb.classList.add('activa');
}
</script>