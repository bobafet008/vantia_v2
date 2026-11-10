<?php require __DIR__ . '/partials/header.php'; $tipo = $tipo ?? 'empleado'; ?>

<section class="auth-layout">
  <div class="auth-info">
    <span class="etiqueta">VANTIA · <?= $tipo === 'empresa' ? 'EMPRESAS VERIFICADAS' : 'JÓVENES Y TALENTO' ?></span>
    <h1><?= $tipo === 'empresa' ? 'Conecta con nuevo talento.' : 'Da el primer paso hacia tu empleo.' ?></h1>
    <p>
      <?= $tipo === 'empresa' 
          ? 'Registra los datos de tu empresa u organización, incluyendo NIT y ROE para publicar vacantes seguras.' 
          : 'Crea tu perfil laboral para postularte a ofertas de trabajo verificadas. Si eres menor de edad, te acompañamos con el registro de tu tutor legal.' ?>
    </p>

    <div class="nota-seguridad" style="margin-top:2rem;">
      <strong>Protección y Verificación</strong>
      <span>Vantia valida empresas y resguarda a los jóvenes postulantes en conformidad con la normativa laboral.</span>
    </div>
  </div>

  <div class="form-card">
    <h2><?= $tipo === 'empresa' ? 'Registro de empresa' : 'Registro de empleado / joven' ?></h2>
    <p class="texto-suave">Completa los datos para crear tu cuenta en la plataforma.</p>

    <?php if (!empty($error)): ?>
      <div class="mensaje-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="index.php?pagina=guardar_usuario" method="post" id="formRegistro">
      <input type="hidden" name="tipo" value="<?= e($tipo) ?>">

      <label for="nombre">Nombre <?= $tipo === 'empresa' ? 'de la empresa' : 'completo' ?> *</label>
      <input id="nombre" name="nombre" type="text" required maxlength="120" value="<?= e($form['nombre'] ?? '') ?>" placeholder="<?= $tipo === 'empresa' ? 'Ej. Soluciones Digitales SRL' : 'Ej. Juan Pérez' ?>">

      <label for="correo">Correo electrónico *</label>
      <input id="correo" name="correo" type="email" required maxlength="150" value="<?= e($form['correo'] ?? '') ?>" placeholder="ejemplo@correo.com">

      <label for="contrasena">Contraseña *</label>
      <input id="contrasena" name="contrasena" type="password" required minlength="6" placeholder="Mínimo 6 caracteres">

      <label for="telefono">Teléfono / WhatsApp</label>
      <input id="telefono" name="telefono" type="text" maxlength="30" value="<?= e($form['telefono'] ?? '') ?>" placeholder="Ej. 70012345">

      <?php if ($tipo === 'empresa'): ?>
        <label for="nit">Número de NIT</label>
        <input id="nit" name="nit" type="text" maxlength="50" value="<?= e($form['nit'] ?? '') ?>" placeholder="Ej. 1029384756">

        <label for="roe">Número de registro ROE (Obligatorio) *</label>
        <input id="roe" name="roe" type="text" required maxlength="50" value="<?= e($form['roe'] ?? '') ?>" placeholder="Ej. ROE-2026-BO">

        <label for="descripcion_empresa">Descripción de la empresa</label>
        <textarea id="descripcion_empresa" name="descripcion_empresa" rows="3" placeholder="A qué se dedica tu empresa y qué rubro maneja"><?= e($form['descripcion_empresa'] ?? '') ?></textarea>
      
      <?php else: ?>
        <label for="fecha_nacimiento">Fecha de nacimiento *</label>
        <input id="fecha_nacimiento" name="fecha_nacimiento" type="date" required value="<?= e($form['fecha_nacimiento'] ?? '') ?>">

        <!-- Sección condicional para Menor de Edad -->
        <div id="seccionTutor" class="caja-tutor" style="display: none;">
          <div class="aviso-tutor">
            <strong>Detección de menor de 18 años</strong>
            <span>La ley exige que los menores cuenten con la autorización de su padre, madre o tutor legal.</span>
          </div>

          <label for="tutor_nombre">Nombre del padre, madre o tutor *</label>
          <input id="tutor_nombre" name="tutor_nombre" type="text" maxlength="100" value="<?= e($form['tutor_nombre'] ?? '') ?>" placeholder="Nombre completo del tutor">

          <label for="tutor_telefono">Teléfono de contacto del tutor *</label>
          <input id="tutor_telefono" name="tutor_telefono" type="text" maxlength="30" value="<?= e($form['tutor_telefono'] ?? '') ?>" placeholder="Teléfono del tutor">

          <label for="tutor_relacion">Parentesco / Relación</label>
          <select id="tutor_relacion" name="tutor_relacion">
            <option value="Madre">Madre</option>
            <option value="Padre">Padre</option>
            <option value="Tutor legal">Tutor(a) legal</option>
          </select>
        </div>
      <?php endif; ?>

      <button class="boton ancho" type="submit" style="margin-top:1.5rem;">Crear cuenta e ingresar</button>
    </form>

    <div class="auth-footer-links">
      <p>¿Ya tienes una cuenta?</p>
      <a href="index.php?pagina=login">Iniciar sesión</a>
    </div>
  </div>
</section>

<?php if ($tipo === 'empleado'): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const inputFecha = document.getElementById('fecha_nacimiento');
  const seccionTutor = document.getElementById('seccionTutor');
  const inputTutorNombre = document.getElementById('tutor_nombre');
  const inputTutorTel = document.getElementById('tutor_telefono');

  function calcularEdad(fechaString) {
    if (!fechaString) return null;
    const nac = new Date(fechaString);
    const hoy = new Date();
    let edad = hoy.getFullYear() - nac.getFullYear();
    const m = hoy.getMonth() - nac.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < nac.getDate())) {
      edad--;
    }
    return edad;
  }

  function verificarMenor() {
    const edad = calcularEdad(inputFecha.value);
    if (edad !== null && edad < 18 && edad >= 14) {
      seccionTutor.style.display = 'block';
      inputTutorNombre.required = true;
      inputTutorTel.required = true;
    } else {
      seccionTutor.style.display = 'none';
      inputTutorNombre.required = false;
      inputTutorTel.required = false;
    }
  }

  inputFecha.addEventListener('change', verificarMenor);
  inputFecha.addEventListener('input', verificarMenor);
  verificarMenor();
});
</script>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
