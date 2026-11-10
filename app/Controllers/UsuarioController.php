<?php
class UsuarioController
{
    public function registroEmpleado()
    {
        vista('registro', ['tipo' => 'empleado']);
    }

    public function registroEmpresa()
    {
        vista('registro', ['tipo' => 'empresa']);
    }

    public function guardar()
    {
        $tipo = $_POST['tipo'] ?? '';
        $nombre = trim($_POST['nombre'] ?? '');
        $correo = trim($_POST['correo'] ?? '');
        $contrasena = $_POST['contrasena'] ?? '';
        $telefono = trim($_POST['telefono'] ?? '');

        // Validación inicial común
        if ($tipo !== 'empleado' && $tipo !== 'empresa') {
            $error = 'Selecciona un tipo de cuenta válido.';
        } elseif ($nombre === '' || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $error = 'Completa el nombre y proporciona un correo válido.';
        } elseif (strlen($contrasena) < 6) {
            $error = 'La contraseña debe tener al menos 6 caracteres.';
        }

        $datosUsuario = [
            'nombre' => $nombre,
            'correo' => $correo,
            'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
            'tipo' => $tipo,
            'telefono' => $telefono ?: null,
            'fecha_nacimiento' => null,
            'es_menor' => 0,
            'tutor_nombre' => null,
            'tutor_telefono' => null,
            'tutor_relacion' => null,
            'nit' => null,
            'roe' => null,
            'descripcion_empresa' => null,
        ];

        // Validaciones específicas según el tipo de usuario
        if (!isset($error)) {
            if ($tipo === 'empresa') {
                $nit = trim($_POST['nit'] ?? '');
                $roe = trim($_POST['roe'] ?? '');
                $descripcion = trim($_POST['descripcion_empresa'] ?? '');

                if ($roe === '') {
                    $error = 'El número de registro ROE es obligatorio para la empresa.';
                } else {
                    $datosUsuario['nit'] = $nit ?: null;
                    $datosUsuario['roe'] = $roe;
                    $datosUsuario['descripcion_empresa'] = $descripcion ?: null;
                }
            } else {
                // Empleado / Candidato
                $fechaNacimiento = $_POST['fecha_nacimiento'] ?? '';
                if ($fechaNacimiento === '') {
                    $error = 'Ingresa tu fecha de nacimiento.';
                } else {
                    $datosUsuario['fecha_nacimiento'] = $fechaNacimiento;
                    
                    // Cálculo de edad para protección de menores
                    $nac = new DateTime($fechaNacimiento);
                    $hoy = new DateTime();
                    $edad = $hoy->diff($nac)->y;

                    if ($edad < 18) {
                        $datosUsuario['es_menor'] = 1;
                        $tutorNombre = trim($_POST['tutor_nombre'] ?? '');
                        $tutorTel = trim($_POST['tutor_telefono'] ?? '');
                        $tutorRel = trim($_POST['tutor_relacion'] ?? '');

                        if ($tutorNombre === '' || $tutorTel === '') {
                            $error = 'Al ser menor de 18 años, es obligatorio ingresar el nombre y teléfono de tu tutor(a) legal.';
                        } else {
                            $datosUsuario['tutor_nombre'] = $tutorNombre;
                            $datosUsuario['tutor_telefono'] = $tutorTel;
                            $datosUsuario['tutor_relacion'] = $tutorRel ?: 'Tutor legal';
                        }
                    }
                }
            }
        }

        if (isset($error)) {
            vista('registro', [
                'tipo' => $tipo,
                'error' => $error,
                'form' => $_POST,
            ]);
            return;
        }

        require __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../Models/Usuario.php';
        $modelo = new Usuario($conexion);

        try {
            $modelo->crear($datosUsuario);
            $nuevoId = (int) $conexion->lastInsertId();

            // Iniciar sesión automáticamente tras el registro
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $nuevoId;
            $_SESSION['usuario'] = $nombre;
            $_SESSION['correo'] = $correo;
            $_SESSION['tipo'] = $tipo;

            header('Location: index.php?pagina=' . ($tipo === 'empresa' ? 'panel_empresa' : 'trabajos'));
            exit;
        } catch (PDOException $errorBase) {
            vista('registro', [
                'tipo' => $tipo,
                'error' => 'Ese correo ya está registrado en la plataforma.',
                'form' => $_POST,
            ]);
        }
    }
}
