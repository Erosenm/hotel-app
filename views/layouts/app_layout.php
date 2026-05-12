<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= $title ?? 'Hotel' ?></title>
    <!-- <link rel="icon" href="/hotel-app/public/img/logo.png"> -->
    
    <?php include __DIR__ . '/../components/header.php'; ?>

    <?php if (!empty($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link rel="stylesheet" href="<?= asset($style) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <link rel="stylesheet" href="<?= asset('css/sidebar.css') ?>">
</head>
<body>

    <?php include __DIR__ . '/../components/navbar.php'; ?>

    <div class="container-fluid p-0">
        <?= $content ?>
    </div>
    
     <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
   
</html> 