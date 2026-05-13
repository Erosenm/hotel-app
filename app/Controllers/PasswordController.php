<?php
require_once __DIR__ . '/../../app/Mail/Mailer.php';
 
class PasswordController
{
    // ─── PASO 1: Mostrar formulario "ingresa tu email" ────────────────────────
    public function forgot()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
 
        ob_start();
        include __DIR__ . '/../../views/password/forgot.php';
        $content = ob_get_clean();
 
        $title = 'Recuperar Contraseña | Hotel Real Plaza';
        include __DIR__ . '/../../views/layouts/auth_layout.php';
    }
 
    // ─── PASO 2: Recibir email y enviar el link ───────────────────────────────
    public function sendLink()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $email = trim($_POST['email'] ?? '');
 
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Ingresa un correo electrónico válido.';
            header('Location: ' . url('password/forgot'));
            exit();
        }
 
        // Verificar que el email existe en la BD
        $stmt = $conn->prepare('SELECT idUsuario, nombre, paterno FROM usuario WHERE email = ? AND estado = "Activo" LIMIT 1');
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
 
        // Por seguridad, mostramos el mismo mensaje exista o no el email
        // (evita que alguien descubra qué emails están registrados)
        if (!$usuario) {
            $_SESSION['success'] = 'Si ese correo está registrado, recibirás un enlace en breve.';
            header('Location: ' . url('password/forgot'));
            exit();
        }
 
        // Generar token único de 64 caracteres
        $token  = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+30 minutes'));
 
        // Eliminar tokens anteriores de ese email
        $conn->prepare('DELETE FROM password_reset WHERE email = ?')->execute([$email]);
 
        // Guardar token nuevo
        $conn->prepare('INSERT INTO password_reset (email, token, expira) VALUES (?, ?, ?)')
             ->execute([$email, $token, $expira]);
 
        // Construir el link de reseteo
        $link = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
              . '://' . $_SERVER['HTTP_HOST']
              . url('password/reset') . '?token=' . $token;
 
        $nombre = $usuario['nombre'] . ' ' . $usuario['paterno'];
 
        // Enviar email
        $enviado = Mailer::enviarRecuperacion($email, $nombre, $link);
 
        if ($enviado) {
            $_SESSION['success'] = 'Te enviamos un enlace a <strong>' . htmlspecialchars($email) . '</strong>. Revisa tu bandeja (y la carpeta de spam).';
        } else {
            $_SESSION['error'] = 'Hubo un problema al enviar el correo. Intenta de nuevo.';
        }
 
        header('Location: ' . url('password/forgot'));
        exit();
    }
 
    // ─── PASO 3: Mostrar formulario "nueva contraseña" ───────────────────────
    public function reset()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $token = trim($_GET['token'] ?? '');
 
        if (empty($token)) {
            $_SESSION['error'] = 'Enlace inválido.';
            header('Location: ' . url('login'));
            exit();
        }
 
        // Verificar que el token existe, no está usado y no expiró
        $stmt = $conn->prepare('
            SELECT * FROM password_reset
            WHERE token = ? AND usado = 0 AND expira > NOW()
            LIMIT 1
        ');
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$reset) {
            $_SESSION['error'] = 'El enlace ha expirado o ya fue usado. Solicita uno nuevo.';
            header('Location: ' . url('password/forgot'));
            exit();
        }
 
        ob_start();
        include __DIR__ . '/../../views/password/reset.php';
        $content = ob_get_clean();
 
        $title = 'Nueva Contraseña | Hotel Real Plaza';
        include __DIR__ . '/../../views/layouts/auth_layout.php';
    }
 
    // ─── PASO 4: Guardar la nueva contraseña ─────────────────────────────────
    public function update()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';
 
        $token    = trim($_POST['token']    ?? '');
        $password = $_POST['password']      ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
 
        // Validaciones
        if (empty($token) || empty($password) || empty($confirm)) {
            $_SESSION['error'] = 'Completa todos los campos.';
            header('Location: ' . url('password/reset') . '?token=' . urlencode($token));
            exit();
        }
 
        if ($password !== $confirm) {
            $_SESSION['error'] = 'Las contraseñas no coinciden.';
            header('Location: ' . url('password/reset') . '?token=' . urlencode($token));
            exit();
        }
 
        if (strlen($password) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
            header('Location: ' . url('password/reset') . '?token=' . urlencode($token));
            exit();
        }
 
        // Verificar token nuevamente
        $stmt = $conn->prepare('
            SELECT * FROM password_reset
            WHERE token = ? AND usado = 0 AND expira > NOW()
            LIMIT 1
        ');
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);
 
        if (!$reset) {
            $_SESSION['error'] = 'El enlace expiró. Solicita uno nuevo.';
            header('Location: ' . url('password/forgot'));
            exit();
        }
 
        // Actualizar contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $conn->prepare('UPDATE usuario SET password = ? WHERE email = ?')
             ->execute([$hash, $reset['email']]);
 
        // Marcar token como usado
        $conn->prepare('UPDATE password_reset SET usado = 1 WHERE token = ?')
             ->execute([$token]);
 
        // Bitácora
        $u = $conn->prepare('SELECT idUsuario FROM usuario WHERE email = ? LIMIT 1');
        $u->execute([$reset['email']]);
        $uid = $u->fetchColumn();
        if ($uid) {
            $conn->prepare('INSERT INTO bitacora (accion, idUsuario_FK) VALUES (?, ?)')
                 ->execute(['Cambió su contraseña via recuperación', $uid]);
        }
 
        $_SESSION['success'] = '¡Contraseña actualizada! Ya puedes iniciar sesión.';
        header('Location: ' . url('login'));
        exit();
    }
}