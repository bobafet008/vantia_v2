</main>
<footer class="site-footer">
  <div class="contenedor footer-inner">
    <div>
      <a class="logo" href="index.php?pagina=inicio">VANTIA<span>.</span></a>
      <p>Oportunidades con confianza para jóvenes de Bolivia.</p>
    </div>

    <div class="footer-admin-acceso">
      <?php if (!empty($_SESSION['usuario_id']) && ($_SESSION['tipo'] ?? '') === 'admin'): ?>
        <a href="index.php?pagina=panel_admin" class="boton-admin-footer">
          Admin
        </a>
      <?php else: ?>
        <a href="index.php?pagina=login&tipo=admin" class="boton-admin-footer">
          Admin
        </a>
      <?php endif; ?>
    </div>
  </div>
</footer>
</body>
</html>
