<?php

class GoogleAuthController
{
    // ─── Credenciales Google OAuth ────────────────────────────────────────────
    private static function config(): array
    {
    return require __DIR__ . '/../../config/google.php';
    }

    // ─── PASO 1: Redirigir a Google ───────────────────────────────────────────
    public function redirect()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $cfg = self::config();

        // State para proteger contra CSRF
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;

        $params = http_build_query([
            'client_id'     => $cfg['client_id'],
            'redirect_uri'  => $cfg['redirect_uri'],
            'response_type' => 'code',
            'scope'         => $cfg['scope'],
            'state'         => $state,
            'access_type'   => 'online',
            'prompt'        => 'select_account',
        ]);

        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
        exit();
    }

    // ─── PASO 2: Callback — Google nos devuelve el código ─────────────────────
    public function callback()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        // Verificar state (anti-CSRF)
        $state = $_GET['state'] ?? '';
        if (empty($state) || $state !== ($_SESSION['oauth_state'] ?? '')) {
            $_SESSION['error'] = 'Solicitud inválida. Intenta de nuevo.';
            header('Location: ' . url('login'));
            exit();
        }
        unset($_SESSION['oauth_state']);

        // Verificar que Google mandó el código
        $code = $_GET['code'] ?? '';
        if (empty($code)) {
            $_SESSION['error'] = 'No se recibió autorización de Google.';
            header('Location: ' . url('login'));
            exit();
        }

        // ── Intercambiar código por access token ──────────────────────────────
        $cfg      = self::config();
        $tokenData = self::getAccessToken($code, $cfg);

        if (!$tokenData || empty($tokenData['access_token'])) {
            $_SESSION['error'] = 'Error al conectar con Google. Intenta de nuevo.';
            header('Location: ' . url('login'));
            exit();
        }

        // ── Obtener datos del usuario de Google ───────────────────────────────
        $googleUser = self::getGoogleUser($tokenData['access_token']);

        if (!$googleUser || empty($googleUser['email'])) {
            $_SESSION['error'] = 'No se pudo obtener la información de Google.';
            header('Location: ' . url('login'));
            exit();
        }

        $googleId = $googleUser['sub'];
        $email    = $googleUser['email'];
        $nombre   = $googleUser['given_name']  ?? explode(' ', $googleUser['name'])[0] ?? 'Usuario';
        $paterno  = $googleUser['family_name'] ?? 'Google';

        // ── Buscar si ya existe el usuario ────────────────────────────────────
        $stmt = $conn->prepare('
            SELECT u.*, ur.idRol, r.nombre AS rol
            FROM usuario u
            LEFT JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
            LEFT JOIN rol r          ON ur.idRol    = r.idRol
            WHERE u.google_id = ? OR u.email = ?
            LIMIT 1
        ');
        $stmt->execute([$googleId, $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            // ── Usuario ya existe — actualizar google_id si no lo tenía ───────
            if (empty($usuario['google_id'])) {
                $conn->prepare('UPDATE usuario SET google_id = ? WHERE idUsuario = ?')
                     ->execute([$googleId, $usuario['idUsuario']]);
            }

            // Verificar estado
            if ($usuario['estado'] === 'Suspendido') {
                $_SESSION['error'] = 'Tu cuenta ha sido suspendida. Contacta al administrador.';
                header('Location: ' . url('login'));
                exit();
            }
            if ($usuario['estado'] === 'Inactivo') {
                $_SESSION['error'] = 'Tu cuenta está inactiva. Contacta al administrador.';
                header('Location: ' . url('login'));
                exit();
            }

        } else {
            // ── Usuario nuevo — crear cuenta automáticamente ──────────────────
            $ci = 'G-' . substr($googleId, 0, 10); // CI temporal basado en Google ID

            $conn->prepare('
                INSERT INTO usuario (codigo, ci, nombre, paterno, email, password, google_id, estado)
                VALUES (UUID(), ?, ?, ?, ?, ?, ?, "Activo")
            ')->execute([
                $ci,
                $nombre,
                $paterno,
                $email,
                password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), // password aleatorio
                $googleId,
            ]);

            $idUsuario = $conn->lastInsertId();

            // Asignar rol Cliente
            $rolStmt = $conn->prepare("SELECT idRol FROM rol WHERE nombre = 'Cliente' LIMIT 1");
            $rolStmt->execute();
            $rolCliente = $rolStmt->fetch(PDO::FETCH_ASSOC);

            if ($rolCliente) {
                $conn->prepare('INSERT INTO usuario_rol (idUsuario, idRol) VALUES (?, ?)')
                     ->execute([$idUsuario, $rolCliente['idRol']]);
            }

            // Recargar el usuario recién creado
            $stmt = $conn->prepare('
                SELECT u.*, ur.idRol, r.nombre AS rol
                FROM usuario u
                LEFT JOIN usuario_rol ur ON u.idUsuario = ur.idUsuario
                LEFT JOIN rol r          ON ur.idRol    = r.idRol
                WHERE u.idUsuario = ?
                LIMIT 1
            ');
            $stmt->execute([$idUsuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // ── Guardar sesión ─────────────────────────────────────────────────────
        $_SESSION['usuario'] = [
            'id'     => $usuario['idUsuario'],
            'nombre' => $usuario['nombre'] . ' ' . $usuario['paterno'],
            'email'  => $usuario['email'],
            'rol'    => $usuario['rol'] ?? 'Cliente',
            'estado' => $usuario['estado'],
        ];

        // Bitácora
        $conn->prepare("INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)")
             ->execute(['Inicio de sesión con Google', $usuario['idUsuario']]);

        header('Location: ' . url('adminpanel'));
        exit();
    }

    // ─── Intercambiar código por token ────────────────────────────────────────
    private static function getAccessToken(string $code, array $cfg): ?array
    {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'client_id'     => $cfg['client_id'],
                'client_secret' => $cfg['client_secret'],
                'redirect_uri'  => $cfg['redirect_uri'],
                'grant_type'    => 'authorization_code',
            ]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) : null;
    }

    // ─── Obtener datos del usuario de Google ──────────────────────────────────
    private static function getGoogleUser(string $accessToken): ?array
    {
        $ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $accessToken],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) : null;
    }
}