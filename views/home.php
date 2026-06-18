<style>
    .hero {
        background: linear-gradient(var(--overlay), var(--overlay)),
                    url('<?= asset('imgs/3.png') ?>') no-repeat center/cover !important;
    }
    .relaxation {
        background: url('<?= asset('imgs/5.png') ?>') no-repeat center/cover fixed !important;
    }
    .testimonials {
        background: linear-gradient(var(--overlay), var(--overlay)),
                    url('<?= asset('imgs/recepcion.jpg') ?>') no-repeat center/cover !important;
    }
</style>

<!-- 2. Hero Section -->
    <section class="hero">
        <h1 class="reveal">UNA EXPERIENCIA ÚNICA DONDE ALOJARSE</h1>
    </section>

    <!-- 3. Sobre Nosotros -->
    <section class="about">
        <div class="about-text reveal">
            <span style="font-family: var(--sans-header); font-size: 0.7rem; letter-spacing: 0.2em; color: var(--text-sub);">BIENVENIDOS</span>
            <h2>Elegancia en el corazón de La Paz</h2>
            <p>Real Plaza Hotel & Convention Center redefine la hospitalidad boliviana con un lobby de doble altura, lámparas escultóricas y ambientes diseñados para el viajero más exigente.</p>
            <p>Desde nuestra imponente fachada de vidrio hasta cada detalle de su habitación, vivirá una estancia inigualable en el centro de la ciudad.</p>
        </div>
        <div class="about-grid reveal">
            <div class="img-hover-container">
                <img src="<?= asset('imgs/3.png') ?>" alt="Fachada Real Plaza Hotel">
            </div>
            <div class="img-hover-container">
                <img src="<?= asset('imgs/hotel2.jpg') ?>" alt="Edificio Real Plaza Hotel">
            </div>
        </div>
    </section>

    <!-- 4. Sección Relajación -->
    <section class="relaxation">
        <h2 class="reveal">Relájese en nuestra piscina cubierta con luz natural</h2>
    </section>

    <!-- 5. Habitaciones -->
    <section class="rooms">
        <span class="reveal" style="font-family: var(--sans-header); font-size: 0.7rem; letter-spacing: 0.2em; color: var(--text-sub);">ALOJAMIENTO</span>
        <h2 class="reveal" style="font-size: 3rem; margin-top: 10px;">Suites & Habitaciones</h2>
        <div class="rooms-grid">
            <div class="room-card reveal">
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/6.png') ?>" alt="Habitación doble">
                </div>
                <h3>Habitación Doble</h3>
                
            </div>
            <div class="room-card reveal">
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/1.png') ?>" alt="Lobby y áreas comunes">
                </div>
                <h3>Suite Ejecutiva</h3>
                
            </div>
            <div class="room-card reveal">
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/2.png') ?>" alt="Área de descanso junto a piscina">
                </div>
                <h3>Suite con Acceso a Piscina</h3>
            
            </div>
        </div>
    </section>

    <!-- 6. Instalaciones Principales -->
    <section class="facilities">
        <h2 class="reveal">Servicios de Clase Mundial</h2>
        <div class="facilities-grid reveal">
            <div class="fac-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                <h4>WIFI DE ALTA VELOCIDAD</h4>
                <p>Conexión premium en todo el hotel.</p>
            </div>
            <div class="fac-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
                <h4>APARCAMIENTO PRIVADO</h4>
                <p>Seguridad y confort para su vehículo.</p>
            </div>
            <div class="fac-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                <h4>BAR & RESTAURANTE</h4>
                <p>Gastronomía boliviana e internacional.</p>
            </div>
            <div class="fac-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M2 6c.6.5 1.2 1 2.5 1C5.8 7 7 6 7 6s1.2-1 2.5-1c1.3 0 2.5 1 2.5 1s1.2 1 2.5 1c1.3 0 2.5-1 2.5-1s1.2-1 2.5-1c1.3 0 2.5 1 2.5 1"></path><path d="M2 12c.6.5 1.2 1 2.5 1 1.3 0 2.5-1 2.5-1s1.2-1 2.5-1c1.3 0 2.5 1 2.5 1s1.2 1 2.5 1c1.3 0 2.5-1 2.5-1s1.2-1 2.5-1c1.3 0 2.5 1 2.5 1"></path></svg>
                <h4>PISCINA CUBIERTA</h4>
                <p>Piscina climatizada con luz natural y área de descanso.</p>
            </div>
        </div>
    </section>

    <!-- 7. Servicios Locales (Zig-Zag) -->
    <section class="services-z">
        <div class="z-row reveal">
            <div class="z-img img-hover-container">
                <img src="<?= asset('imgs/recepcion.jpg') ?>" alt="Lobby del hotel">
            </div>
            <div class="z-content">
                <h2>Un Lobby de Ensueño</h2>
                <p>Nuestro gran lobby con lámparas escultóricas doradas, escalera curva de madera y mármol, y arte en cada rincón, da la bienvenida a huéspedes de todo el mundo desde el primer instante.</p>
            </div>
        </div>
        <div class="z-row reveal">
            <div class="z-img img-hover-container">
                <img src="<?= asset('imgs/4.png') ?>" alt="Piscina cubierta">
            </div>
            <div class="z-content">
                <h2>Piscina & Área de Bienestar</h2>
                <p>Disfrute de nuestra piscina cubierta climatizada rodeada de vegetación tropical, ideal para relajarse durante todo el año sin importar el clima paceño.</p>
            </div>
        </div>
    </section>

    <!-- 8. Testimonios -->
    <section class="testimonials reveal">
        <blockquote>
            "Una estancia inolvidable. El lobby es espectacular, las habitaciones muy cómodas y la piscina cubierta fue una sorpresa increíble. El mejor hotel de La Paz sin duda."
        </blockquote>
        <cite>CARLOS MENDOZA, SANTA CRUZ</cite>
    </section>

    <!-- 9. Noticias y Eventos -->
    <section class="news">
        <h2 class="reveal" style="text-align: center; margin-bottom: 50px;">Noticias & Eventos</h2>
        <div class="news-grid">
            <div class="news-card reveal">
                <span class="date-tag">11 DE DICIEMBRE</span>
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/4.png') ?>" alt="Piscina cubierta">
                </div>
                <h3>Inauguración de la Temporada en la Piscina</h3>
            </div>
            <div class="news-card reveal">
                <span class="date-tag">15 DE DICIEMBRE</span>
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/2.png') ?>" alt="Área de descanso">
                </div>
                <h3>Noche de Relax con Cócteles en el Área Húmeda</h3>
            </div>
            <div class="news-card reveal">
                <span class="date-tag">20 DE DICIEMBRE</span>
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/1.png') ?>" alt="Lobby del hotel">
                </div>
                <h3>Recepción de Gala en el Gran Lobby</h3>
            </div>
        </div>
    </section>


    <script>
        window.addEventListener('scroll', () => {
            const header = document.getElementById('main-header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        const revealCallback = (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        };

        const revealObserver = new IntersectionObserver(revealCallback, { threshold: 0.15 });
        document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
    
</body>
</html>