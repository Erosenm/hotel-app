<link rel="stylesheet" href="<?= asset('css/stylelogin.css') ?>">

<div class="login-wrapper" 
     style="background-image: 
     linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)),
     url('<?= asset("imgs/6.png") ?>');">

    <div class="login-card">
        
        <h2>
            <i class="fa fa-hotel"></i>
            Bienvenido al Hotel
        </h2>

        <form action="<?= url('/login') ?>" method="POST">

            <!-- Email -->
            <div class="form-floating">
                <i class="fa fa-envelope input-icon"></i>
                <input 
                    type="email" 
                    name="email" 
                    class="form-control" 
                    placeholder="Correo electrónico"
                    required
                >
                <label>Correo electrónico</label>
            </div>

            <!-- Password -->
            <div class="form-floating">
                <i class="fa fa-lock input-icon"></i>
                <input 
                    type="password" 
                    name="password" 
                    class="form-control" 
                    placeholder="Contraseña"
                    required
                >
                <label>Contraseña</label>
            </div>

            <!-- Botón -->
            <button type="submit" class="btn-custom">
                <i class="fa fa-sign-in-alt"></i>
                Iniciar Sesión
            </button>

            <!-- Links -->
            <div class="links-container">
                <a href="#">¿Olvidaste tu contraseña?</a>
                <a href="<?= url('/register') ?>">Crear una cuenta</a>
            </div>

        </form>
    </div>
</div>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>

    <script>
        console.error("<?= addslashes($_SESSION['error']) ?>");
    </script>

    <?php unset($_SESSION['error']); ?>
<?php endif; ?>