<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title><?= $title ?? 'Hotel' ?></title>
    <link rel="icon" type="image/png" href="<?= asset('imgs/logo.jpg') ?>">
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
        <?= $content ?? '' ?>
    </div>
    
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Bootstrap JS (necesario para modales) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
   
</html>