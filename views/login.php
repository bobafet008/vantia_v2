<?php require __DIR__ . '/partials/header.php'; ?>

<section class="auth-layout">
  <div class="auth-info">
    <span class="etiqueta">VANTIA · ACCESO</span>
    <h1>Bienvenido de nuevo.</h1>
    <p>Ingresa a tu cuenta para acceder a las oportunidades laborales o administrar tus vacantes y postulantes.</p>
    
    <div class="demo-box">
      <h3>Cuentas de demostración (Clic para probar):</h3>
      
      <div class="demo-card" onclick="cargarDemo('contacto@techsol.com', 'empresa123', false)">
        <div class="demo-info">
          <strong>Empresa:</strong>
          <code>contacto@techsol.com</code>
        </div>
        <div class="demo-botones">
          <button type="button" class="btn-demo" onclick="event.stopPropagation(); cargarDemo('contacto@techsol.com', 'empresa123', false)">Rellenar</button>
          <button type="button" class="btn-demo btn-demo-directo" onclick="event.stopPropagation(); cargarDemo('contacto@techsol.com', 'empresa123', true)">Entrar →</button>
        </div>
      </div>

      <div class="demo-card" onclick="cargarDemo('carlos@correo.com', 'candidato123', false)">
        <div class="demo-info">
          <strong>Joven Mayor (20 años):</strong>
          <code>carlos@correo.com</code>
        </div>
        <div class="demo-botones">
          <button type="button" class="btn-demo" onclick="event.stopPropagation(); cargarDemo('carlos@correo.com', 'candidato123', false)">Rellenar</button>
          <button type="button" class="btn-demo btn-demo-directo" onclick="event.stopPropagation(); cargarDemo('carlos@correo.com', 'candidato123', true)">Entrar →</button>
        </div>
      </div>

      <div class="demo-card" onclick="cargarDemo('lucia@correo.com', 'candidato123', false)">
        <div class="demo-info">
          <strong>Joven Menor (17 años + Tutor):</strong>
          <code>lucia@correo.com</code>
        </div>
        <div class="demo-botones">
          <button type="button" class="btn-demo" onclick="event.stopPropagation(); cargarDemo('lucia@correo.com', 'candidato123', false)">Rellenar</button>
          <button type="button" class="btn-demo btn-demo-directo" onclick="event.stopPropagation(); cargarDemo('lucia@correo.com', 'candidato123', true)">Entrar →</button>
        </div>
      </div>

      <div class="demo-card" onclick="cargarDemo('admin@vantia.com', 'admin123', false)">
        <div class="demo-info">
          <strong>Administrador:</strong>
          <code>admin@vantia.com</code>
        </div>
        <div class="demo-botones">
          <button type="button" class="btn-demo" onclick="event.stopPropagation(); cargarDemo('admin@vantia.com', 'admin123', false)">Rellenar</button>
          <button type="button" class="btn-demo btn-demo-directo" onclick="event.stopPropagation(); cargarDemo('admin@vantia.com', 'admin123', true)">Entrar →</button>
        </div>
      </div>
    </div>
  </div>

  <div class="form-card">
    <h2>Iniciar sesión</h2>
    <p class="texto-suave">Ingresa tus credenciales registradas o usa una cuenta demo.</p>

    <?php if (!empty($error)): ?>
      <div class="mensaje-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="index.php?pagina=validar_login" method="post" id="formLogin">
      <input type="hidden" name="tipo" value="<?= e($tipo ?? '') ?>">

      <label for="correo">Correo electrónico</label>
      <input id="correo" name="correo" type="email" required maxlength="150" value="<?= e($correo ?? ($tipo === 'admin' ? 'admin@vantia.com' : '')) ?>" placeholder="ejemplo@correo.com">

      <label for="contrasena">Contraseña</label>
      <input id="contrasena" name="contrasena" type="password" required placeholder="Tu contraseña" value="<?= $tipo === 'admin' ? 'admin123' : '' ?>">

      <button class="boton ancho" type="submit" id="btnSubmitLogin">Ingresar a Vantia</button>
    </form>

    <div class="auth-footer-links">
      <p>¿Aún no tienes cuenta?</p>
      <div class="enlaces-registro">
        <a href="index.php?pagina=registro_empleado">Registrarme como joven / empleado</a>
        <span>·</span>
        <a href="index.php?pagina=registro_empresa">Registrar mi empresa</a>
      </div>

      <div style="margin-top:12px; padding-top:8px; border-top:1px dashed var(--linea); text-align:center;">
        <button type="button" class="btn-demo" style="padding:4px 10px; font-size:11px; background:#f0f2f5; color:#4a5568; border-color:#cbd5e0;" onclick="cargarDemo('admin@vantia.com', 'admin123', true)">
          Acceso Administrador
        </button>
      </div>
    </div>
  </div>
</section>

<script>
function cargarDemo(correo, password, autoSubmit) {
  const inputCorreo = document.getElementById('correo');
  const inputPass = document.getElementById('contrasena');
  const form = document.getElementById('formLogin');

  inputCorreo.value = correo;
  inputPass.value = password;

  inputCorreo.style.transition = 'all 0.3s';
  inputPass.style.transition = 'all 0.3s';
  inputCorreo.style.borderColor = '#d5a347';
  inputPass.style.borderColor = '#d5a347';
  inputCorreo.style.boxShadow = '0 0 8px rgba(213, 163, 71, 0.4)';
  inputPass.style.boxShadow = '0 0 8px rgba(213, 163, 71, 0.4)';

  setTimeout(() => {
    inputCorreo.style.boxShadow = '';
    inputPass.style.boxShadow = '';
  }, 1200);

  if (autoSubmit) {
    form.submit();
  } else {
    inputCorreo.focus();
  }
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
