<style>
    .hero {
        background: linear-gradient(var(--overlay), var(--overlay)),
                    url('<?= asset('imgs/3.png') ?>') no-repeat center/cover !important;
    }
    .relaxation {
        background: url('<?= asset('imgs/hotel2.jpg') ?>') no-repeat center/cover fixed !important;
    }
    .testimonials {
        background: linear-gradient(var(--overlay), var(--overlay)),
                    url('<?= asset('imgs/recepcion.jpg') ?>') no-repeat center/cover !important;
    }
</style>
<!-- 2. Hero Section -->
    <section class="hero">
        <h1 class="reveal">UNA EXPERIENCIA ÚNICA DONDE ALOJARSE</h1>
        <div class="booking-bar reveal">
            <div>
                <label>ENTRADA / SALIDA</label>
                <input type="text" placeholder="Jun 12 - Jun 18">
            </div>
            <div>
                <label>ADULTOS</label>
                <select><option>2 Adultos</option></select>
            </div>
            <div>
                <label>NIÑOS</label>
                <select><option>0 Niños</option></select>
            </div>
            <button class="btn">Buscar</button>
        </div>
    </section>

    <!-- 3. Sobre Nosotros -->
    <section class="about">
        <div class="about-text reveal">
            <span style="font-family: var(--sans-header); font-size: 0.7rem; letter-spacing: 0.2em; color: var(--text-sub);">BIENVENIDOS</span>
            <h2>Elegancia en el corazón de la ciudad</h2>
            <p>Hotel Real Plaza redefine el lujo contemporáneo con un enfoque minimalista y sofisticado. Cada rincón ha sido diseñado para ofrecer paz y serenidad a nuestros huéspedes más exigentes.</p>
            <p>Descubra un refugio donde el servicio excepcional se encuentra con la arquitectura atemporal.</p>
            <a href="#" class="read-more" style="margin-top: 20px; display: inline-block;">NUESTRA HISTORIA</a>
        </div>
        <div class="about-grid reveal">
            <div class="img-hover-container">
                <img src="<?= asset('imgs/3.png') ?>" alt="Interior">
            </div>
            <div class="img-hover-container">
                <img src="<?= asset('imgs/hotel2.jpg') ?>" alt="Detalle">
            </div>
        </div>
    </section>

    <!-- 4. Sección Relajación -->
    <section class="relaxation">
        <h2 class="reveal">Disfruta de una experiencia de relajación totalmente inmersiva</h2>
    </section>

    <!-- 5. Habitaciones -->
    <section class="rooms">
        <span class="reveal" style="font-family: var(--sans-header); font-size: 0.7rem; letter-spacing: 0.2em; color: var(--text-sub);">ALOJAMIENTO</span>
        <h2 class="reveal" style="font-size: 3rem; margin-top: 10px;">Suites & Habitaciones</h2>
        <div class="rooms-grid">
            <div class="room-card reveal">
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/1.png') ?>" alt="Suite">
                </div>
                <h3>Suite Junior</h3>
                <span>Desde $250 / Noche</span>
            </div>
            <div class="room-card reveal">
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/2.png') ?>" alt="Deluxe">
                </div>
                <h3>Habitación Deluxe</h3>
                <span>Desde $380 / Noche</span>
            </div>
            <div class="room-card reveal">
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/3.png') ?>" alt="Penthouse">
                </div>
                <h3>Real Plaza Penthouse</h3>
                <span>Desde $750 / Noche</span>
            </div>
        </div>
        <button class="btn reveal">Ver todas las habitaciones</button>
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
                <h4>BAR EXCLUSIVO</h4>
                <p>Coctelería de autor y ambiente selecto.</p>
            </div>
            <div class="fac-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M2 6c.6.5 1.2 1 2.5 1C5.8 7 7 6 7 6s1.2-1 2.5-1c1.3 0 2.5 1 2.5 1s1.2 1 2.5 1c1.3 0 2.5-1 2.5-1s1.2-1 2.5-1c1.3 0 2.5 1 2.5 1"></path><path d="M2 12c.6.5 1.2 1 2.5 1 1.3 0 2.5-1 2.5-1s1.2-1 2.5-1c1.3 0 2.5 1 2.5 1s1.2 1 2.5 1c1.3 0 2.5-1 2.5-1s1.2-1 2.5-1c1.3 0 2.5 1 2.5 1"></path></svg>
                <h4>PISCINA INFINITY</h4>
                <p>Vistas panorámicas y relajación total.</p>
            </div>
        </div>
    </section>

    <!-- 7. Servicios Locales (Zig-Zag) -->
    <section class="services-z">
        <div class="z-row reveal">
            <div class="z-img img-hover-container">
                <img src="<?= asset('imgs/recepcion.jpg') ?>" alt="Restaurante">
            </div>
            <div class="z-content">
                <h2>Gastronomía Estrellada</h2>
                <p>Nuestro restaurante "Plaza Gourmet" ofrece una fusión de sabores locales e internacionales, elaborados por chefs de renombre en un entorno minimalista.</p>
                <a href="#" class="read-more">DESCUBRIR MENÚ</a>
            </div>
        </div>
        <div class="z-row reveal">
            <div class="z-img img-hover-container">
                <img src="<?= asset('imgs/picina.jpeg') ?>" alt="Piscina">
            </div>
            <div class="z-content">
                <h2>Arte & Cultura</h2>
                <p>Ubicados a pasos de las galerías más importantes. Ofrecemos tours privados y acceso exclusivo a eventos culturales de la ciudad.</p>
                <a href="#" class="read-more">LEER MÁS</a>
            </div>
        </div>
    </section>

    <!-- 8. Testimonios -->
    <section class="testimonials reveal">
        <blockquote>
            "Una estancia inolvidable. El diseño del hotel y la atención al detalle superaron todas mis expectativas. Un verdadero oasis de calma."
        </blockquote>
        <cite>ELENA RODRÍGUEZ, MADRID</cite>
    </section>

    <!-- 9. Noticias y Eventos -->
    <section class="news">
        <h2 class="reveal" style="text-align: center; margin-bottom: 50px;">Noticias & Eventos</h2>
        <div class="news-grid">
            <div class="news-card reveal">
                <span class="date-tag">11 DE DICIEMBRE</span>
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/4.png') ?>" alt="Yoga">
                </div>
                <h3>Mañanas de Yoga en la Terraza</h3>
                <a href="#" class="read-more">LEER MÁS</a>
            </div>
            <div class="news-card reveal">
                <span class="date-tag">15 DE DICIEMBRE</span>
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/5.png') ?>" alt="Vino">
                </div>
                <h3>Cata de Vinos de Reserva</h3>
                <a href="#" class="read-more">LEER MÁS</a>
            </div>
            <div class="news-card reveal">
                <span class="date-tag">20 DE DICIEMBRE</span>
                <div class="img-hover-container">
                    <img src="<?= asset('imgs/6.png') ?>" alt="Jazz">
                </div>
                <h3>Noche de Jazz en Directo</h3>
                <a href="#" class="read-more">LEER MÁS</a>
            </div>
        </div>
    </section>

    <!-- 10. Calendario / Disponibilidad -->
    <section class="availability-section">
        <div class="reveal">
            <h2>Consulta Disponibilidad</h2>
            <p style="margin: 20px 0; color: var(--text-sub);">Seleccione sus fechas preferidas para su próxima estancia en el Real Plaza Hotel. Garantizamos el mejor precio en reservas directas.</p>
            <button class="btn">Ver Calendario Completo</button>
        </div>
        <div class="cal-mockup reveal">
            <div class="month">
                <h5>DICIEMBRE 2023</h5>
                <div class="days">
                    <span>Lu</span><span>Ma</span><span>Mi</span><span>Ju</span><span>Vi</span><span>Sa</span><span>Do</span>
                    <span>1</span><span>2</span><span>3</span><span>4</span><span style="color:var(--accent); font-weight:bold;">5</span><span>6</span><span>7</span>
                    <span>...</span>
                </div>
            </div>
            <div class="month">
                <h5>ENERO 2024</h5>
                <div class="days">
                    <span>Lu</span><span>Ma</span><span>Mi</span><span>Ju</span><span>Vi</span><span>Sa</span><span>Do</span>
                    <span>1</span><span>2</span><span>3</span><span>4</span><span>5</span><span>6</span><span>7</span>
                    <span>...</span>
                </div>
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
    <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['usuario']) && !empty($_SESSION['usuario']);
$nombreReal = $isLoggedIn ? $_SESSION['usuario']['nombre'] : 'Visitante';
$emailReal  = $isLoggedIn ? $_SESSION['usuario']['email']  : '';
$usuarioId  = $isLoggedIn ? (string)$_SESSION['usuario']['id'] : '';
$sessionIdN8N = $isLoggedIn ? 'hotel_real_' . $usuarioId : 'guest_' . session_id();

$jsMetadata = json_encode([
    'logeado' => $isLoggedIn,
    'nombre'  => $nombreReal,
    'email'   => $emailReal,
    'user_id' => $usuarioId
]);

$mensajeFinal = $isLoggedIn
    ? "Hola {$nombreReal}, Bienvenido de nuevo al Hotel Real Plaza. En que puedo ayudarte?"
    : "Bienvenido al Hotel Real Plaza. Para reservar necesitas iniciar sesion. En que te puedo ayudar?";
?>

<link href="https://cdn.jsdelivr.net/npm/@n8n/chat/dist/style.css" rel="stylesheet" />
<style>
  :root {
    --chat--color--primary: #35f939 !important;
    --chat--toggle--background: #35f939 !important;
    --chat--header--background: #17c7e6 !important;
  }
</style>

<script type="module">
  import { createChat } from 'https://cdn.jsdelivr.net/npm/@n8n/chat/dist/chat.bundle.es.js';

  createChat({
    webhookUrl: 'http://localhost:5678/webhook/35a1c34d-e20b-4be3-8293-22d888afa5fc/chat',
    showWelcomeScreen: false,
    allowTextSelection: true,
    sessionId: '<?php echo $sessionIdN8N; ?>',
    title: 'Hotel Real Plaza',
    metadata: <?php echo $jsMetadata; ?>,
    initialMessages: [<?php echo json_encode($mensajeFinal); ?>],
    i18n: {
      en: {
        welcomeMessage: 'Bienvenido',
        inputPlaceholder: 'Escribe aqui...'
      }
    }
  });
</script>

</body>
</html>