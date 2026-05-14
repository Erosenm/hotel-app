<?php
require_once __DIR__ . '/../helpers/auth.php';

class AuthController
{
    public function login()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['usuario'])) {
            header("Location: " . url('adminpanel'));
            exit();
        }

        ob_start();
        include __DIR__ . '/../../views/login.php';
        $content = ob_get_clean();

        $title = "Iniciar Sesión";
        include __DIR__ . '/../../views/layouts/auth_layout.php';
    }

    public function authenticate()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        // CAPTCHA
        $captchaRespuesta = $_POST['g-recaptcha-response'] ?? '';
        $claveSecreta     = '6LdPmuosAAAAAOaq9Bx18Mg_A0zrmDOT0LWaC9UM';

        if (empty($captchaRespuesta)) {
            $_SESSION['error'] = "Por favor marca la casilla No soy un robot.";
            header("Location: " . url('login'));
            exit();
        }

        $verificacion = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify"
            . "?secret={$claveSecreta}"
            . "&response={$captchaRespuesta}"
            . "&remoteip={$_SERVER['REMOTE_ADDR']}"
        );
        $datosCaptcha = json_decode($verificacion, true);

        if (!$datosCaptcha['success']) {
            $_SESSION['error'] = "Captcha inválido. Inténtalo de nuevo.";
            header("Location: " . url('login'));
            exit();
        }

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Completa todos los campos.";
            header("Location: " . url('login'));
            exit();
        }

        $stmt = $conn->prepare("
            SELECT u.*, ur.idRol, r.nombre AS rol
            FROM usuario u
            LEFT JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            LEFT JOIN rol r          ON ur.idRol    = r.idRol
            WHERE u.email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $_SESSION['error'] = "Correo o contraseña incorrectos.";
            header("Location: " . url('login'));
            exit();
        }

        if (!password_verify($password, $usuario['password'])) {
            $_SESSION['error'] = "Correo o contraseña incorrectos.";
            header("Location: " . url('login'));
            exit();
        }

        if ($usuario['estado'] === 'Suspendido') {
            $_SESSION['error'] = "Tu cuenta ha sido suspendida. Contacta al administrador.";
            header("Location: " . url('login'));
            exit();
        }

        if ($usuario['estado'] === 'Inactivo') {
            $_SESSION['error'] = "Tu cuenta está inactiva. Contacta al administrador.";
            header("Location: " . url('login'));
            exit();
        }

        $_SESSION['usuario'] = [
            'id'     => $usuario['idUsuario'],
            'nombre' => $usuario['nombre'] . ' ' . $usuario['paterno'],
            'email'  => $usuario['email'],
            'rol'    => $usuario['rol']   ?? 'Cliente',
            'estado' => $usuario['estado'],
        ];

        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(["Inicio de sesión", $usuario['idUsuario']]);

        header("Location: " . url('adminpanel'));
        exit();
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario'])) {
            require __DIR__ . '/../../config/database.php';

            $stmt = $conn->prepare("
                INSERT INTO bitacora (accion, idUsuario_FK)
                VALUES (?, ?)
            ");
            $stmt->execute([
                "Cerró sesión",
                $_SESSION['usuario']['id']
            ]);
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header("Location: " . url('login'));
        exit();
    }
}