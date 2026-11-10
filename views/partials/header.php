<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>VANTIA | Empleo Juvenil Verificado</title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="site-header">
  <nav class="contenedor menu">
    <a class="logo" href="index.php?pagina=inicio">VANTIA<span>.</span></a>
    
    <div class="links">
      <a href="index.php?pagina=inicio">Inicio</a>
      <a href="index.php?pagina=trabajos">Oportunidades</a>
      
      <?php if (!empty($_SESSION['usuario_id'])): ?>
        <?php if ($_SESSION['tipo'] === 'empresa'): ?>
          <a href="index.php?pagina=panel_empresa"><strong>Panel Empresa</strong></a>
        <?php elseif ($_SESSION['tipo'] === 'empleado'): ?>
          <a href="index.php?pagina=mis_postulaciones"><strong>Mis Postulaciones</strong></a>
        <?php elseif ($_SESSION['tipo'] === 'admin'): ?>
          <a href="index.php?pagina=panel_admin"><strong>Panel Admin</strong></a>
        <?php endif; ?>
      <?php else: ?>
        <a href="index.php?pagina=empleado">Jóvenes</a>
        <a href="index.php?pagina=empresa">Empresas</a>
      <?php endif; ?>
    </div>

    <div class="auth-bar">
      <?php if (!empty($_SESSION['usuario_id'])): ?>
        <span class="usuario-tag">
          <?= e($_SESSION['usuario'] ?? '') ?> 
          <small class="tipo-badge <?= e($_SESSION['tipo']) ?>"><?= ucfirst(e($_SESSION['tipo'])) ?></small>
        </span>
        <a class="boton-sm boton-outline" href="index.php?pagina=salir">Cerrar sesión</a>
      <?php else: ?>
        <a class="boton-sm" href="index.php?pagina=login">Iniciar sesión</a>
        <a class="boton-sm boton-destacado" href="index.php?pagina=registro_empleado">Registrarse</a>
      <?php endif; ?>
    </div>
  </nav>
</header>
<main>