<div>

    <link rel="stylesheet" href="<?= asset('css/styleHome.css') ?>">

    <!-- 11. Footer -->
    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h4 class="logo" style="color:#fff; margin-bottom: 20px;">REAL PLAZA</h4>
                <p>Lujo y serenidad en cada detalle. Su casa lejos de casa.</p>
            </div>
            <div class="footer-col">
                <h4>EXPLORAR</h4>
                <ul>
                    <li><a href="#">Habitaciones</a></li>
                    <li><a href="#">Restaurante</a></li>
                    <li><a href="#">Spa & Wellness</a></li>
                    <li><a href="#">Eventos</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>CONTACTO</h4>
                <p>Calle de la Elegancia 123,<br>Madrid, España</p>
                <p>T. +34 900 123 456</p>
                <p>E. info@hotelrealplaza.com</p>
            </div>
            <div class="footer-col newsletter">
                <h4>HOJA INFORMATIVA</h4>
                <p>Reciba ofertas exclusivas.</p>
                <input type="email" placeholder="Su correo electrónico">
                <button class="btn" style="background:#fff; color:var(--accent); width:100%;">Suscribirse</button>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 HOTEL REAL PLAZA. TODOS LOS DERECHOS RESERVADOS.</p>
            <p>POLÍTICA DE PRIVACIDAD / TÉRMINOS Y CONDICIONES</p>
        </div>
    </footer>
    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
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
    --chat--color--primary: #2C2C2C !important;
    --chat--color--primary-dark: #1A1A1A !important;
    --chat--color--primary-light: #4A4A4A !important;
    --chat--toggle--background: #2C2C2C !important;
    --chat--toggle--color: #FFFFFF !important;
    --chat--header--background: #1A1A1A !important;
    --chat--header--color: #FFFFFF !important;
    --chat--message--bot--background: #F9F8F6 !important;
    --chat--message--bot--color: #1A1A1A !important;
    --chat--message--user--background: #2C2C2C !important;
    --chat--message--user--color: #FFFFFF !important;
    --chat--input--background: #FFFFFF !important;
    --chat--input--border-color: #E5E5E5 !important;
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
</div>
