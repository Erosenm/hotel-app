<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../app/helpers.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Página no encontrada | Hotel Real Plaza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            color: #fff;
        }
        .container-404 {
            text-align: center;
            padding: 40px 20px;
            max-width: 600px;
        }
        .number-404 {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            color: transparent;
            -webkit-text-stroke: 3px #c8a96e;
            letter-spacing: -10px;
            margin-bottom: 10px;
        }
        .icon-404 {
            width: 100px;
            height: 100px;
            background: rgba(200, 169, 110, 0.15);
            border: 2px solid #c8a96e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2.5rem;
            color: #c8a96e;
        }
        .btn-home {
            background: #c8a96e;
            color: #1a1a2e;
            border: none;
            padding: 14px 36px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            transition: all .2s;
            margin: 6px;
        }
        .btn-home:hover {
            background: #b8915a;
            color: #1a1a2e;
            transform: translateY(-2px);
        }
        .btn-back {
            background: transparent;
            color: #a0b4d0;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 14px 36px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
            transition: all .2s;
            margin: 6px;
        }
        .btn-back:hover {
            color: #fff;
            border-color: rgba(255,255,255,0.5);
        }
        .divider {
            width: 60px;
            height: 3px;
            background: #c8a96e;
            margin: 20px auto;
            border-radius: 2px;
        }
        .links-rapidos a {
            color: #a0b4d0;
            text-decoration: none;
            font-size: .9rem;
            margin: 0 12px;
            transition: color .2s;
        }
        .links-rapidos a:hover { color: #c8a96e; }
    </style>
</head>
<body>
    <div class="container-404">

        <div class="icon-404">
            <i class="fas fa-map-signs"></i>
        </div>

        <div class="number-404">404</div>

        <div class="divider"></div>

        <h2 class="fw-bold mb-2" style="font-size:1.6rem;">Página no encontrada</h2>
        <p style="color:#a0b4d0; max-width:400px; margin:0 auto 30px;">
            La página que buscas no existe o fue movida.
            Puedes volver al inicio o navegar desde el menú.
        </p>

        <div class="mb-4">
            <a href="<?= url('/') ?>" class="btn-home">
                <i class="fas fa-home me-2"></i>Ir al inicio
            </a>
            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>

        <div class="links-rapidos">
            <a href="<?= url('habitaciones') ?>"><i class="fas fa-bed me-1"></i>Habitaciones</a>
            <a href="<?= url('login') ?>"><i class="fas fa-sign-in-alt me-1"></i>Iniciar sesión</a>
            <?php if (!empty($_SESSION['usuario'])): ?>
                <?php if (in_array($_SESSION['usuario']['rol'], ['Administrador','Recepcionista'])): ?>
                    <a href="<?= url('adminpanel') ?>"><i class="fas fa-cog me-1"></i>Panel admin</a>
                <?php else: ?>
                    <a href="<?= url('cliente/dashboard') ?>"><i class="fas fa-user me-1"></i>Mi panel</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div style="margin-top:40px; color:rgba(255,255,255,0.2); font-size:.8rem;">
            © <?= date('Y') ?> Hotel Real Plaza
        </div>

    </div>
</body>
</html>