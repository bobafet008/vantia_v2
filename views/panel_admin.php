<?php require __DIR__ . '/partials/header.php'; ?>

<section class="pagina-cabecera">
  <div class="contenedor">
    <span class="etiqueta">PANEL GENERAL DE CONTROL</span>
    <h1>Administración de Vantia</h1>
    <p>Supervisión de empresas verificadas, jóvenes registrados y métricas de la plataforma unificada.</p>
    
    <div style="margin-top:1rem;">
      <a href="index.php?pagina=trabajos" class="boton boton-sm" style="background:#e2f5e8; color:#1f7a46;">Bolsa pública de empleos →</a>
    </div>
  </div>
</section>

<section class="contenedor seccion">
  <?php if (!empty($exito)): ?><div class="mensaje-exito" style="margin-bottom:1.5rem;"><?= e($exito) ?></div><?php endif; ?>
  <?php if (!empty($error)): ?><div class="mensaje-error" style="margin-bottom:1.5rem;"><?= e($error) ?></div><?php endif; ?>

  <!-- Tarjetas de Estadísticas Globales -->
  <div class="tarjetas cuatro" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem; margin-bottom:2.5rem;">
    <div class="tarjeta">
      <span class="numero">01 · EMPRESAS</span>
      <h2><?= (int) $totalEmpresas ?></h2>
      <p>Organizaciones con NIT y ROE.</p>
    </div>
    <div class="tarjeta">
      <span class="numero">02 · JÓVENES</span>
      <h2><?= (int) $totalCandidatos ?></h2>
      <p>Candidatos registrados.</p>
    </div>
    <div class="tarjeta">
      <span class="numero">03 · VACANTES</span>
      <h2><?= (int) $totalEmpleos ?></h2>
      <p>Ofertas de empleo registradas.</p>
    </div>
    <div class="tarjeta">
      <span class="numero">04 · POSTULACIONES</span>
      <h2><?= (int) $totalPostulaciones ?></h2>
      <p>Aplicaciones procesadas.</p>
    </div>
  </div>

  <!-- Tabla de Usuarios Registrados en la Base Unificada -->
  <div class="bloque-panel">
    <div class="titulo-seccion">
      <div>
        <span class="etiqueta">BASE DE DATOS UNIFICADA</span>
        <h2>Gestión de Usuarios y Validación</h2>
      </div>
    </div>

    <div class="tabla-contenedor">
      <table class="tabla-panel">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre / Razón Social</th>
            <th>Tipo</th>
            <th>Correo</th>
            <th>Datos Fiscales / Tutor</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($usuarios as $u): ?>
            <tr>
              <td>#<?= (int) $u['id'] ?></td>
              <td><strong><?= e($u['nombre']) ?></strong></td>
              <td>
                <span class="tipo-badge <?= e($u['tipo']) ?>">
                  <?= ucfirst(e($u['tipo'])) ?>
                </span>
              </td>
              <td>
                <a href="mailto:<?= e($u['correo']) ?>" class="enlace-contacto"><?= e($u['correo']) ?></a>
                <?php if (!empty($u['telefono'])): ?>
                  <div>Tel: <?= e($u['telefono']) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($u['tipo'] === 'empresa'): ?>
                  <div>NIT: <code><?= e($u['nit'] ?? 'S/N') ?></code></div>
                  <div>ROE: <code><?= e($u['roe'] ?? 'S/N') ?></code></div>
                <?php elseif ($u['tipo'] === 'empleado'): ?>
                  <?php if (!empty($u['es_menor'])): ?>
                    <span class="badge-menor">Menor de edad</span>
                    <div style="font-size:12px; margin-top:4px;">
                      Tutor: <?= e($u['tutor_nombre'] ?? 'N/D') ?> (<?= e($u['tutor_telefono'] ?? '-') ?>)
                    </div>
                  <?php else: ?>
                    <span class="badge-mayor">Mayor de edad</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="texto-suave">Superadministrador</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($u['tipo'] === 'empresa'): ?>
                  <?php if (!empty($u['verificada'])): ?>
                    <span class="etiqueta-verificada">Verificada</span>
                  <?php else: ?>
                    <span class="badge-menor" style="background:#fee2e2; color:#991b1b;">Pendiente</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="texto-suave">Activo</span>
                <?php endif; ?>
              </td>
              <td>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                  <?php if ($u['tipo'] === 'empresa'): ?>
                    <form action="index.php?pagina=toggle_verificar_empresa" method="post" style="display:inline;">
                      <input type="hidden" name="usuario_id" value="<?= (int) $u['id'] ?>">
                      <button type="submit" class="boton-sm <?= !empty($u['verificada']) ? 'boton-outline-oscuro' : 'boton-exito' ?>">
                        <?= !empty($u['verificada']) ? 'Quitar verificación' : 'Verificar NIT' ?>
                      </button>
                    </form>
                  <?php endif; ?>

                  <?php if ((int)$u['id'] !== (int)($_SESSION['usuario_id'] ?? 0)): ?>
                    <form action="index.php?pagina=eliminar_usuario" method="post" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario y todos sus datos?');">
                      <input type="hidden" name="usuario_id" value="<?= (int) $u['id'] ?>">
                      <button type="submit" class="boton-sm boton-peligro">Eliminar</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
