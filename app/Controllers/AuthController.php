<?php
class AuthController
{
    public function formulario()
    {
        $tipo = $_GET['tipo'] ?? '';
        vista('login', ['tipo' => $tipo]);
    }

    public function validar()
    {
        require __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../Models/Usuario.php';

        $modelo = new Usuario($conexion);
        $correo = trim($_POST['correo'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $tipoRequerido = $_POST['tipo'] ?? '';

        if ($correo === '' || $contrasena === '') {
            vista('login', [
                'tipo' => $tipoRequerido,
                'error' => 'Por favor, ingresa tu correo y contraseña.',
                'correo' => $correo,
            ]);
            return;
        }

        $usuario = $modelo->buscarPorCorreo($correo);

        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            // Verificar si la empresa no ha sido aprobada aún
            if ($usuario['tipo'] === 'empresa' && isset($usuario['verificada']) && (int) $usuario['verificada'] === 0) {
                vista('login', [
                    'tipo' => $tipoRequerido,
                    'error' => 'Tu empresa aún está pendiente de revisión por el administrador.',
                    'correo' => $correo,
                ]);
                return;
            }

            session_regenerate_id(true);
            $_SESSION['usuario_id'] = (int) $usuario['id'];
            $_SESSION['usuario'] = $usuario['nombre'];
            $_SESSION['correo'] = $usuario['correo'];
            $_SESSION['tipo'] = $usuario['tipo'];

            if ($usuario['tipo'] === 'empresa') {
                header('Location: index.php?pagina=panel_empresa');
            } elseif ($usuario['tipo'] === 'admin') {
                header('Location: index.php?pagina=panel_admin');
            } else {
                header('Location: index.php?pagina=trabajos');
            }
            exit;
        }

        vista('login', [
            'tipo' => $tipoRequerido,
            'error' => 'Correo o contraseña incorrectos.',
            'correo' => $correo,
        ]);
    }

    public function salir()
    {
        $_SESSION = [];
        if (session_id() != '' || headers_sent()) {
            session_destroy();
        }
        header('Location: index.php?pagina=inicio');
        exit;
    }
}
