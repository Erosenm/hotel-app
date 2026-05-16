<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Hotel' ?></title>
    <?php include __DIR__ . '/../components/header.php'; ?>
</head>
<body style="margin:0; padding:0;">
    <?= $content ?? '' ?>
</body>
</html>