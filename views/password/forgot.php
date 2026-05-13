<link rel="stylesheet" href="<?= asset('css/stylelogin.css') ?>">
 
<div class="login-wrapper"
     style="background-image:
     linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)),
     url('<?= asset("imgs/6.png") ?>');">
 
    <div class="login-card">
 
        <h2>
            <i class="fa fa-key"></i>
            Recuperar Contraseña
        </h2>
 
        <p style="color:#ccc; font-size:14px; text-align:center; margin-bottom:20px;">
            Ingresa tu correo y te enviaremos un enlace para crear una nueva contraseña.
        </p>
 
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success" style="border-radius:6px; margin-bottom:16px;">
                <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
 
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="border-radius:6px; margin-bottom:16px;">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
 
        <form action="<?= url('password/send-link') ?>" method="POST">
 
            <div class="form-floating">
                <i class="fa fa-envelope input-icon"></i>
                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Correo electrónico"
                    required
                    autofocus
                >
                <label>Correo electrónico</label>
            </div>
 
            <button type="submit" class="btn-custom">
                <i class="fa fa-paper-plane"></i>
                Enviar enlace
            </button>
 
            <div class="links-container">
                <a href="<?= url('login') ?>">← Volver al inicio de sesión</a>
            </div>
 
        </form>
    </div>
</div>