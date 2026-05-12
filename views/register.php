<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>

<link rel="stylesheet" href="<?= asset('css/styleRegistro.css') ?>">

<div class="register-wrapper" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('<?= asset('imgs/6.png') ?>');">

    <div class="register-card">

        <h2><i class="fa fa-user-plus"></i> Crea tu Cuenta</h2>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="<?= url('register') ?>" method="POST">
            
            <!-- Fila 1: Nombre + Paterno -->
            <div class="form-row">
                <div class="form-floating position-relative">
                    <i class="fa fa-user input-icon"></i>
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre" required>
                </div>
                <div class="form-floating position-relative">
                    <i class="fa fa-user input-icon"></i>
                    <input type="text" name="paterno" class="form-control" placeholder="Apellido Paterno" required>
                </div>
            </div>

            <!-- Fila 2: Materno + CI -->
            <div class="form-row">
                <div class="form-floating position-relative">
                    <i class="fa fa-user input-icon"></i>
                    <input type="text" name="materno" class="form-control" placeholder="Apellido Materno">
                </div>
                <div class="form-floating position-relative">
                    <i class="fa fa-id-card input-icon"></i>
                    <input type="text" name="ci" class="form-control" placeholder="Cédula de Identidad" required>
                </div>
            </div>

            <!-- Fila 3: Email + Teléfono -->
            <div class="form-row">
                <div class="form-floating position-relative">
                    <i class="fa fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
                </div>
                <div class="form-floating position-relative">
                    <i class="fa fa-phone input-icon"></i>
                    <input type="text" name="telefono" class="form-control" placeholder="Teléfono">
                </div>
            </div>

            <!-- Fecha Nacimiento (solo) -->
            <div class="form-floating position-relative">
                <i class="fa fa-calendar input-icon"></i>
                <input type="date" name="fechaNacimiento" class="form-control" 
                       max="<?= date('Y-m-d', strtotime('-18 years')) ?>"
                       min="<?= date('Y-m-d', strtotime('-100 years')) ?>" required>
            </div>

            <!-- Contraseña -->
            <div class="form-floating position-relative password-section">
                <i class="fa fa-lock input-icon"></i>
                <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Contraseña" required>

                <!-- Barra de fuerza -->
                <div class="strength-container">
                    <div class="strength-bar">
                        <div id="strengthFill"></div>
                    </div>
                    <span id="strengthLabel"></span>
                </div>

                <!-- Requisitos -->
                <ul class="password-requirements">
                    <li id="req1">⬜ Mínimo 8 caracteres</li>
                    <li id="req2">⬜ Una letra mayúscula</li>
                    <li id="req3">⬜ Una letra minúscula</li>
                    <li id="req4">⬜ Un número</li>
                    <li id="req5">⬜ Carácter especial (!@#$%)</li>
                </ul>
            </div>

            <!-- Botón -->
            <button type="submit" class="btn-custom">
                <i class="fa fa-user-plus"></i> Registrarse
            </button>

            <!-- Enlace login -->
            <div class="links-container">
                <a href="<?= url('login') ?>">¿Ya tienes cuenta? Inicia sesión</a>
            </div>

        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pwdInput = document.getElementById('passwordInput');
    const strengthFill = document.getElementById('strengthFill');
    const strengthLabel = document.getElementById('strengthLabel');
    const reqs = document.querySelectorAll('.password-requirements li');

    const checks = [
        { id: 'req1', test: v => v.length >= 8 },
        { id: 'req2', test: v => /[A-Z]/.test(v) },
        { id: 'req3', test: v => /[a-z]/.test(v) },
        { id: 'req4', test: v => /[0-9]/.test(v) },
        { id: 'req5', test: v => /[^A-Za-z0-9]/.test(v) }
    ];

    pwdInput.addEventListener('input', function() {
        const value = this.value;
        let score = 0;

        checks.forEach((check, index) => {
            const pass = check.test(value);
            reqs[index].innerHTML = pass ? '✅ ' : '⬜ ';
            reqs[index].classList.toggle('complete', pass);
            if (pass) score++;
        });

        // Strength bar
        const levels = [
            { w: '20%', c: '#ef4444', l: 'Muy débil' },
            { w: '40%', c: '#f59e0b', l: 'Débil' },
            { w: '60%', c: 'f39c12', l: 'Media' },
            { w: '80%', c: '#10b981', l: 'Fuerte' },
            { w: '100%', c: '#059669', l: 'Muy fuerte' }
        ];

        const level = levels[Math.min(score, 4)] || levels[0];
        strengthFill.style.width = level.w;
        strengthFill.style.background = level.c;
        strengthLabel.textContent = level.l;
        strengthLabel.style.color = level.c;
    });
});
</script>

