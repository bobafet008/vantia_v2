<?php require __DIR__ . '/partials/header.php'; ?>

<?php
// Filtrar empresas donde el empleado ha sido contratado (donde trabaja)
$empresasContratado = [];
if (!empty($postulaciones)) {
    foreach ($postulaciones as $post) {
        if ($post['estado'] === 'contratado' && !empty($post['empresa_id'])) {
            $empresasContratado[$post['empresa_id']] = [
                'empresa_id'     => $post['empresa_id'],
                'empresa_nombre' => $post['empresa_nombre'],
                'empleo_titulo'  => $post['empleo_titulo'],
            ];
        }
    }
}
?>

<section class="pagina-cabecera">
  <div class="contenedor">
    <span class="etiqueta">PORTAL DEL EMPLEADO / CANDIDATO</span>
    <h1>Mis Postulaciones y Calificaciones</h1>
    <p>Revisa el estado de tus solicitudes y califica a las empresas donde trabajas.</p>
    
    <div style="display:flex; gap:10px; margin-top:1rem; flex-wrap:wrap;">
      <a href="#solicitudes" class="boton boton-sm">📋 Mis Postulaciones (<?= count($postulaciones ?? []) ?>)</a>
      <a href="#calificaciones" class="boton boton-sm boton-oscuro">⭐ Calificaciones a Empresas (<?= count($empresasContratado) ?>)</a>
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

  <!-- SECCIÓN 1: Solicitudes de Empleo -->
  <div id="solicitudes" class="bloque-panel" style="margin-bottom:3.5rem;">
    <div class="titulo-seccion">
      <div>
        <span class="etiqueta">HISTORIAL DE APLICACIONES</span>
        <h2>Estado de tus postulaciones</h2>
      </div>
    </div>

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
                <small style="color: white; font-weight:bold; background:#10b981; padding:4px 8px; border-radius:3px; display:inline-block;">¡Contratado! Has superado el proceso de selección y trabajas con esta empresa.</small>
              <?php elseif ($post['estado'] === 'rechazado'): ?>
                <small class="texto-suave">Gracias por participar. En esta oportunidad el puesto ha sido cubierto.</small>
              <?php endif; ?>
            </div>

            <!-- Si está contratado: enlace directo para calificar a su empresa -->
            <?php if ($post['estado'] === 'contratado' && !empty($post['empresa_id'])): ?>
              <div style="margin-top:14px; padding:10px 12px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:4px;">
                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
                  <div>
                    <strong style="color:#166534; font-size:12px;">🏢 Empresa donde trabajas</strong>
                    <?php if (isset($calificaciones[$post['empresa_id']])): ?>
                      <p style="margin:2px 0 0; font-size:12px; color:#14532d;">
                        Tu puntuación: <strong><?= (int)$calificaciones[$post['empresa_id']]['puntuacion'] ?> / 5 ⭐</strong>
                      </p>
                    <?php else: ?>
                      <p style="margin:2px 0 0; font-size:12px; color:#166534;">Aún no has calificado esta empresa.</p>
                    <?php endif; ?>
                  </div>
                  <a href="#calif-empresa-<?= (int)$post['empresa_id'] ?>" class="boton-sm" style="background:#16a34a; color:white; font-size:11px;">
                    <?= isset($calificaciones[$post['empresa_id']]) ? 'Editar nota' : '⭐ Calificar (0 a 5)' ?>
                  </a>
                </div>
              </div>
            <?php endif; ?>

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
    <?php endif; ?>
  </div>

  <!-- SECCIÓN 2: Calificaciones a Empresas donde Trabaja -->
  <div id="calificaciones" class="bloque-panel" style="padding-top:2rem; border-top:2px solid var(--linea);">
    <div class="titulo-seccion">
      <div>
        <span class="etiqueta">EVALUACIÓN DE EMPLEADORES</span>
        <h2>⭐ Calificaciones a la Empresa en donde trabajas</h2>
        <p class="texto-suave">Asigna un puntaje del <strong>0 al 5</strong> a tu empleador y déjanos tu opinión sobre el ambiente y cumplimiento de las condiciones pactadas.</p>
      </div>
    </div>

    <?php if (empty($empresasContratado)): ?>
      <div class="aviso" style="background:#f0f9ff; border-color:#bae6fd; color:#0369a1;">
        <strong style="display:block; margin-bottom:4px; font-size:13px;">ℹ️ Aún no tienes empresas donde trabajes registradas</strong>
        Esta sección de calificación se habilitará automáticamente en cuanto una empresa te seleccione como <strong>Contratado</strong>. Esto asegura que todas las puntuaciones sean de empleados reales.
      </div>
    <?php else: ?>
      <div class="grilla-empleos">
        <?php foreach ($empresasContratado as $emp): 
          $calif = $calificaciones[$emp['empresa_id']] ?? null;
          $puntuacion = $calif !== null ? (int)$calif['puntuacion'] : null;
        ?>
          <article class="trabajo" id="calif-empresa-<?= (int)$emp['empresa_id'] ?>" style="border-top: 3px solid #d5a347; background:#ffffff;">
            <div class="trabajo-header">
              <span class="etiqueta-verificada">🏢 Lugar de Trabajo Activo</span>
              <?php if ($puntuacion !== null): ?>
                <span class="badge-estado badge-contratado">Tu nota: <?= $puntuacion ?> / 5 ⭐</span>
              <?php else: ?>
                <span class="badge-estado badge-pendiente">Pendiente de calificar</span>
              <?php endif; ?>
            </div>

            <h3 style="font: normal 22px Georgia,serif; color: var(--azul); margin: 8px 0 4px;"><?= e($emp['empresa_nombre']) ?></h3>
            <p style="font-size: 13px; color: var(--gris); margin-bottom: 14px;">Puesto contratado: <strong><?= e($emp['empleo_titulo']) ?></strong></p>

            <form action="index.php?pagina=guardar_calificacion" method="post" style="display:flex; flex-direction:column; gap:10px;">
              <input type="hidden" name="empresa_id" value="<?= (int)$emp['empresa_id'] ?>">

              <label style="font-size: 12px; font-weight: bold; color: var(--azul); margin-top:2px;">
                Puntaje asignado a la empresa (del 0 al 5) *:
              </label>

              <!-- Selector interactivo de puntaje 0 a 5 -->
              <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 6px;">
                <?php for ($i = 0; $i <= 5; $i++): ?>
                  <label style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 8px 4px; border: 1px solid <?= ($puntuacion === $i) ? '#d5a347' : '#d5d5d0' ?>; background: <?= ($puntuacion === $i) ? '#fbf6ec' : '#fafafa' ?>; border-radius: 4px; cursor: pointer; text-align: center; transition: all 0.2s;">
                    <input type="radio" name="puntuacion" value="<?= $i ?>" <?= ($puntuacion === $i) ? 'checked' : ($puntuacion === null && $i === 5 ? 'checked' : '') ?> required>
                    <span style="font-size: 14px; font-weight: bold; margin-top: 2px; color: var(--azul);"><?= $i ?></span>
                    <span style="font-size: 10px; color: var(--gris);"><?= ($i === 0 ? '0 pts' : $i . '★') ?></span>
                  </label>
                <?php endfor; ?>
              </div>

              <label for="comentario_<?= (int)$emp['empresa_id'] ?>" style="font-size: 12px; font-weight: bold; color: var(--azul); margin-top: 6px;">
                Comentario sobre el trato, ambiente laboral y cumplimiento (opcional):
              </label>
              <textarea id="comentario_<?= (int)$emp['empresa_id'] ?>" name="comentario" rows="3" style="width: 100%; padding: 8px 10px; border: 1px solid #d5d5d0; border-radius: 4px; font: 12px Arial;" placeholder="¿Cómo es el ambiente de trabajo? ¿Pagan puntualmente? ¿Respetan los horarios acordados?"><?= e($calif['comentario'] ?? '') ?></textarea>

              <div style="margin-top: 6px;">
                <button type="submit" class="boton-sm" style="background: var(--dorado); color: var(--azul); font-weight: bold; padding: 8px 16px; border:none; cursor:pointer;">
                  <?= $puntuacion !== null ? 'Actualizar mi Calificación' : 'Guardar Calificación (0 a 5)' ?>
                </button>
              </div>
            </form>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <div style="margin-top:2.5rem; text-align:center;">
    <a href="index.php?pagina=trabajos" class="boton">Ver más oportunidades en Vantia</a>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
