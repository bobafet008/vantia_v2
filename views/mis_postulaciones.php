<?php require __DIR__ . '/partials/header.php'; ?>

<section class="pagina-cabecera">
  <div class="contenedor">
    <span class="etiqueta">PORTAL DEL CANDIDATO</span>
    <h1>Mis Postulaciones</h1>
    <p>Revisa el historial y el estado en tiempo real de tus solicitudes de empleo.</p>
    
    <div style="margin-top:1rem;">
      <a href="index.php?pagina=trabajos" class="boton boton-sm" style="background:#e2f5e8; color:#1f7a46;">Explorar más oportunidades →</a>
    </div>
  </div>
</section>

<section class="contenedor seccion">
  <?php if (!empty($mensajeExito)): ?>
    <div class="mensaje-exito" style="margin-bottom:2rem;"><?= e($mensajeExito) ?></div>
  <?php endif; ?>

  <?php if (!empty($mensajeError)): ?>
    <div class="mensaje-error" style="margin-bottom:2rem;"><?= e($mensajeError) ?></div>
  <?php endif; ?>

  <?php if (empty($postulaciones)): ?>
    <div class="aviso">
      Aún no te has postulado a ninguna vacante. 
      <br><br>
      <a href="index.php?pagina=trabajos" class="boton boton-sm">Explorar oportunidades de empleo</a>
    </div>
  <?php else: ?>
    <div class="grilla-empleos">
      <?php foreach ($postulaciones as $post): ?>
        <article class="trabajo">
          <div class="trabajo-header">
            <span class="etiqueta-tipo"><?= e(str_replace('_', ' ', strtoupper($post['empleo_tipo']))) ?></span>
            <span class="etiqueta-cat"><?= e($post['categoria']) ?></span>
            <span class="badge-estado badge-<?= e($post['estado']) ?>">
              Estado: <?= ucfirst(e($post['estado'])) ?>
            </span>
          </div>

          <h2><?= e($post['empleo_titulo']) ?></h2>
          <p class="empresa-nombre">Empresa: <strong><?= e($post['empresa_nombre']) ?></strong></p>

          <div class="trabajo-meta">
            <?php if (!empty($post['ubicacion'])): ?><span>Ubicación: <?= e($post['ubicacion']) ?></span><?php endif; ?>
            <?php if (!empty($post['salario'])): ?><span>Salario: <?= e($post['salario']) ?></span><?php endif; ?>
            <span>Fecha: <?= date('d/m/Y', strtotime($post['fecha_postulacion'])) ?></span>
          </div>

          <?php if (!empty($post['mensaje'])): ?>
            <p class="trabajo-dato"><strong>Tu mensaje enviado:</strong> <em>"<?= e($post['mensaje']) ?>"</em></p>
          <?php endif; ?>

          <div class="estado-explicacion">
            <?php if ($post['estado'] === 'pendiente'): ?>
              <small class="texto-suave">Tu solicitud fue enviada y está en espera de ser leída por la empresa.</small>
            <?php elseif ($post['estado'] === 'revision'): ?>
              <small class="texto-suave">La empresa está evaluando tu perfil laboral.</small>
            <?php elseif ($post['estado'] === 'entrevista'): ?>
              <small style="color: #065f46; font-weight:600; background:#d1fae5; padding:4px 8px; border-radius:3px; display:inline-block;">¡Felicitaciones! Has sido seleccionado para una entrevista. La empresa se contactará contigo.</small>
            <?php elseif ($post['estado'] === 'contratado'): ?>
              <small style="color: white; font-weight:bold; background:#10b981; padding:4px 8px; border-radius:3px; display:inline-block;">¡Contratado! Has superado el proceso de selección.</small>
            <?php elseif ($post['estado'] === 'rechazado'): ?>
              <small class="texto-suave">Gracias por participar. En esta oportunidad el puesto ha sido cubierto.</small>
            <?php endif; ?>
          </div>

          <!-- Botón de acción: retirar postulación si aún está pendiente -->
          <?php if ($post['estado'] === 'pendiente'): ?>
            <div style="margin-top:14px; padding-top:10px; border-top:1px dashed var(--linea);">
              <form action="index.php?pagina=cancelar_postulacion" method="post" onsubmit="return confirm('¿Estás seguro de que deseas retirar esta postulación?');">
                <input type="hidden" name="postulacion_id" value="<?= (int) $post['id'] ?>">
                <button type="submit" class="boton-sm boton-peligro">Retirar postulación</button>
              </form>
            </div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>

    <div style="margin-top:2.5rem; text-align:center;">
      <a href="index.php?pagina=trabajos" class="boton">Ver más oportunidades en Vantia</a>
    </div>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
