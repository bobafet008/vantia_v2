<?php require __DIR__ . '/partials/header.php'; ?>

<section class="pagina-cabecera">
  <div class="contenedor">
    <span class="etiqueta">OPORTUNIDADES LABORALES VERIFICADAS</span>
    <h1>Explora trabajos para jóvenes.</h1>
    <p>Vacantes activas publicadas por empresas formales en Bolivia con condiciones claras de trabajo.</p>
  </div>
</section>

<section class="contenedor seccion">
  <?php if (!empty($mensajeExito)): ?>
    <div class="mensaje-exito" style="margin-bottom:2rem;"><?= e($mensajeExito) ?></div>
  <?php endif; ?>

  <?php if (!empty($mensajeError)): ?>
    <div class="mensaje-error" style="margin-bottom:2rem;"><?= e($mensajeError) ?></div>
  <?php endif; ?>

  <!-- Buscador y filtro interactivo -->
  <div class="barra-filtro" style="margin-bottom: 2rem;">
    <input type="text" id="buscadorEmpleos" placeholder="Buscar por título, categoría o ciudad (ej. Sopocachi, Ventas, Jr.)..." class="input-filtro">
    <button type="button" id="btnLimpiarFiltro" class="boton-sm boton-outline-oscuro" onclick="limpiarFiltro()">Limpiar</button>
  </div>

  <?php if (empty($empleos)): ?>
    <div class="aviso">Por el momento no hay vacantes activas. Vuelve pronto para ver nuevas oportunidades.</div>
  <?php else: ?>
    <div class="grilla-empleos" id="contenedorEmpleos">
      <?php foreach ($empleos as $empleo): ?>
        <?php 
          $yaPostulado = in_array((int)$empleo['id'], $postuladosIds ?? [], true);
        ?>
        <article class="trabajo card-empleo" data-texto="<?= strtolower(e($empleo['titulo'] . ' ' . $empleo['categoria'] . ' ' . $empleo['ubicacion'] . ' ' . $empleo['empresa'])) ?>">
          <div class="trabajo-header">
            <span class="etiqueta-tipo"><?= e(str_replace('_', ' ', strtoupper($empleo['tipo']))) ?></span>
            <span class="etiqueta-cat"><?= e($empleo['categoria']) ?></span>
            <?php if (!empty($empleo['verificada'])): ?>
              <span class="etiqueta-verificada">Empresa Verificada</span>
            <?php endif; ?>
          </div>

          <h2><?= e($empleo['titulo']) ?></h2>
          <p class="empresa-nombre"><strong><?= e($empleo['empresa']) ?></strong></p>

          <p class="trabajo-desc"><?= nl2br(e($empleo['descripcion'])) ?></p>

          <?php if (!empty($empleo['requisitos'])): ?>
            <p class="trabajo-dato"><strong>Requisitos:</strong> <?= nl2br(e($empleo['requisitos'])) ?></p>
          <?php endif; ?>

          <div class="trabajo-meta">
            <?php if (!empty($empleo['ubicacion'])): ?>
              <span>Ubicación: <?= e($empleo['ubicacion']) ?></span>
            <?php endif; ?>
            <?php if (!empty($empleo['salario'])): ?>
              <span>Salario: <?= e($empleo['salario']) ?></span>
            <?php endif; ?>
          </div>

          <div class="trabajo-acciones">
            <?php if (!empty($_SESSION['usuario_id']) && ($_SESSION['tipo'] ?? '') === 'empleado'): ?>
              <?php if ($yaPostulado): ?>
                <span class="badge-postulado">Ya te has postulado</span>
              <?php else: ?>
                <form action="index.php?pagina=postular" method="post" class="form-postular">
                  <input type="hidden" name="empleo_id" value="<?= (int) $empleo['id'] ?>">
                  <input type="text" name="mensaje" class="input-mensaje-postular" placeholder="Mensaje breve de presentación (opcional)">
                  <button type="submit" class="boton boton-sm">Postularme ahora</button>
                </form>
              <?php endif; ?>
            <?php elseif (empty($_SESSION['usuario_id'])): ?>
              <a href="index.php?pagina=login&tipo=empleado" class="boton boton-sm boton-oscuro">Inicia sesión para postularte →</a>
            <?php elseif (($_SESSION['tipo'] ?? '') === 'empresa'): ?>
              <a href="index.php?pagina=panel_empresa" class="boton boton-sm boton-oscuro">Ir a mi Panel de Empresa →</a>
            <?php elseif (($_SESSION['tipo'] ?? '') === 'admin'): ?>
              <a href="index.php?pagina=panel_admin" class="boton boton-sm boton-oscuro">Ir al Panel de Administración →</a>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const buscador = document.getElementById('buscadorEmpleos');
  const cards = document.querySelectorAll('.card-empleo');

  if (buscador) {
    buscador.addEventListener('input', function() {
      const term = this.value.toLowerCase().trim();
      cards.forEach(card => {
        const texto = card.getAttribute('data-texto') || '';
        if (texto.includes(term)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }
});

function limpiarFiltro() {
  const buscador = document.getElementById('buscadorEmpleos');
  if (buscador) {
    buscador.value = '';
    buscador.dispatchEvent(new Event('input'));
  }
}
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
