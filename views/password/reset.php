<link rel="stylesheet" href="<?= asset('css/stylelogin.css') ?>">
 
<div class="login-wrapper"
     style="background-image:
     linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)),
     url('<?= asset("imgs/6.png") ?>');">
 
    <div class="login-card">
 
        <h2>
            <i class="fa fa-lock"></i>
            Nueva Contraseña
        </h2>
 
        <p style="color:#ccc; font-size:14px; text-align:center; margin-bottom:20px;">
            Elige una contraseña segura de al menos 6 caracteres.
        </p>
 
        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="border-radius:6px; margin-bottom:16px;">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
 
        <form action="<?= url('password/update') ?>" method="POST">
 
            <!-- Token oculto -->
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
 
            <!-- Nueva contraseña -->
            <div class="form-floating">
                <i class="fa fa-lock input-icon"></i>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Nueva contraseña"
                    minlength="6"
                    required
                >
                <label>Nueva contraseña</label>
            </div>
 
            <!-- Confirmar contraseña -->
            <div class="form-floating">
                <i class="fa fa-lock input-icon"></i>
                <input
                    type="password"
                    name="password_confirm"
                    id="password_confirm"
                    class="form-control"
                    placeholder="Confirmar contraseña"
                    minlength="6"
                    required
                >
                <label>Confirmar contraseña</label>
            </div>
 
            <!-- Indicador visual de coincidencia -->
            <p id="match-msg" style="font-size:13px; margin-top:-10px; margin-bottom:12px; display:none;"></p>
 
            <button type="submit" class="btn-custom" id="btn-submit">
                <i class="fa fa-check"></i>
                Guardar contraseña
            </button>
 
            <div class="links-container">
                <a href="<?= url('login') ?>">← Volver al inicio de sesión</a>
            </div>
 
        </form>
    </div>
</div>
 
<script>
// Validación visual en tiempo real
const p1  = document.getElementById('password');
const p2  = document.getElementById('password_confirm');
const msg = document.getElementById('match-msg');
const btn = document.getElementById('btn-submit');
 
function checkMatch() {
    if (p2.value === '') { msg.style.display = 'none'; return; }
    msg.style.display = 'block';
    if (p1.value === p2.value) {
        msg.textContent = '✔ Las contraseñas coinciden';
        msg.style.color = '#4caf50';
        btn.disabled = false;
    } else {
        msg.textContent = '✘ Las contraseñas no coinciden';
        msg.style.color = '#f44336';
        btn.disabled = true;
    }
}
 
p1.addEventListener('input', checkMatch);
p2.addEventListener('input', checkMatch);
</script>