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

            <!-- Separador -->
            <div style="display:flex; align-items:center; margin: 16px 0;">
                <hr style="flex:1; border:none; border-top:1px solid #444;">
                <span style="color:#888; font-size:12px; padding: 0 12px;">O también</span>
                <hr style="flex:1; border:none; border-top:1px solid #444;">
            </div>

            <!-- Botón Google -->
            <a href="<?= url('auth/google') ?>" style="
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                background: #fff;
                color: #333;
                border: 1px solid #ddd;
                border-radius: 6px;
                padding: 12px;
                text-decoration: none;
                font-size: 15px;
                font-weight: 500;
                margin-bottom: 16px;
                transition: background 0.2s;
            " onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'">
                <svg width="20" height="20" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.36-8.16 2.36-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Continuar con Google
            </a>

            <!-- Links -->
            <div class="links-container">
                <a href="<?= url('password/forgot') ?>">¿Olvidaste tu contraseña?</a>
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