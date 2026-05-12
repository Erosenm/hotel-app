<?php
class RegisterController
{
    public function register()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        ob_start();
        require __DIR__ . '/../../views/register.php';
        $content = ob_get_clean();

        $title = "Crear Cuenta | Hotel";
        require __DIR__ . '/../../views/layouts/auth_layout.php';
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        require __DIR__ . '/../../config/database.php';

        $nombre   = trim($_POST['nombre']   ?? '');
        $paterno  = trim($_POST['paterno']  ?? '');
        $materno  = trim($_POST['materno']  ?? '');
        $ci       = trim($_POST['ci']       ?? '');
        $email    = trim($_POST['email']    ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $password = $_POST['password']      ?? '';

        if (empty($nombre) || empty($paterno) || empty($ci) || empty($email) || empty($password)) {
            $_SESSION['error'] = "Campos obligatorios faltantes";
            header("Location: " . url('register'));
            exit();
        }

        try {
            $check = $conn->prepare("SELECT idUsuario FROM usuario WHERE email = ? OR ci = ?");
            $check->execute([$email, $ci]);
            if ($check->fetch()) {
                $_SESSION['error'] = "Ya existe un usuario con ese email o CI";
                header("Location: " . url('register'));
                exit();
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);

            $conn->prepare("
                INSERT INTO usuario (codigo, ci, nombre, paterno, materno, email, telefono, password)
                VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?)
            ")->execute([$ci, $nombre, $paterno, $materno, $email, $telefono, $hash]);

            $idUsuario = $conn->lastInsertId();

            // Buscar rol Cliente automáticamente
            $rolStmt = $conn->prepare("SELECT idRol FROM rol WHERE nombre = 'Cliente' LIMIT 1");
            $rolStmt->execute();
            $rolCliente = $rolStmt->fetch(PDO::FETCH_ASSOC);

            if (!$rolCliente) {
                throw new Exception("El rol 'Cliente' no existe en la base de datos");
            }

            //  Nueva estructura usuario_rol sin _FK
            $conn->prepare("INSERT INTO usuario_rol (idUsuario, idRol) VALUES (?, ?)")
                 ->execute([$idUsuario, $rolCliente['idRol']]);

            $_SESSION['success'] = "Cuenta creada correctamente, ya puedes iniciar sesión";
            header("Location: " . url('login'));
            exit();

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = "Error: " . $e->getMessage();
            header("Location: " . url('register'));
            exit();
        }
    }
}