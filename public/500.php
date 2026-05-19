<?php
require_once __DIR__ . '/../app/helpers.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Error del servidor | Hotel Real Plaza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #7f1d1d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            color: #fff;
        }
        .container-500 { text-align: center; padding: 40px 20px; max-width: 600px; }
        .number-500 {
            font-size: 8rem; font-weight: 900; line-height: 1;
            color: transparent; -webkit-text-stroke: 3px #ef4444;
            letter-spacing: -10px; margin-bottom: 10px;
        }
        .icon-500 {
            width: 100px; height: 100px;
            background: rgba(239,68,68,0.15);
            border: 2px solid #ef4444; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px; font-size: 2.5rem; color: #ef4444;
        }
        .divider { width: 60px; height: 3px; background: #ef4444; margin: 20px auto; border-radius: 2px; }
        .btn-home {
            background: #ef4444; color: #fff; border: none;
            padding: 14px 36px; border-radius: 8px; font-weight: 700;
            text-decoration: none; display: inline-block; transition: all .2s; margin: 6px;
        }
        .btn-home:hover { background: #dc2626; color: #fff; transform: translateY(-2px); }
        .btn-back {
            background: transparent; color: #fca5a5;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 14px 36px; border-radius: 8px; font-weight: 600;
            text-decoration: none; display: inline-block; transition: all .2s; margin: 6px;
        }
        .btn-back:hover { color: #fff; border-color: rgba(255,255,255,0.5); }
    </style>
</head>
<body>
    <div class="container-500">
        <div class="icon-500"><i class="fas fa-server"></i></div>
        <div class="number-500">500</div>
        <div class="divider"></div>
        <h2 class="fw-bold mb-2" style="font-size:1.6rem;">Error interno del servidor</h2>
        <p style="color:#fca5a5; max-width:400px; margin:0 auto 30px;">
            Algo salió mal en el servidor. Nuestro equipo fue notificado.
            Por favor intenta de nuevo en unos momentos.
        </p>
        <div>
            <a href="<?= url('/') ?>" class="btn-home"><i class="fas fa-home me-2"></i>Ir al inicio</a>
            <a href="javascript:history.back()" class="btn-back"><i class="fas fa-arrow-left me-2"></i>Volver</a>
        </div>
        <div style="margin-top:40px; color:rgba(255,255,255,0.2); font-size:.8rem;">
            © <?= date('Y') ?> Hotel Real Plaza
        </div>
    </div>
</body>
</html>