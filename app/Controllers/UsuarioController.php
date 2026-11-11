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
        } elseif ($telefono === '') {
            $error = 'El número de teléfono / WhatsApp es obligatorio.';
        }

        $hashContrasena = password_hash($contrasena, PASSWORD_DEFAULT);
        $esMenor = false;

        // Validaciones específicas según el tipo de usuario
        if (!isset($error)) {
            if ($tipo === 'empresa') {
                $representante = trim($_POST['representante'] ?? '');
                $nit = trim($_POST['nit'] ?? '');
                $descripcion = trim($_POST['descripcion_empresa'] ?? '');

                // Procesamiento del certificado / código QR ROE en formato de imagen
                $roePath = 'uploads/roe/roe_default.png';
                if (isset($_FILES['roe_archivo']) && $_FILES['roe_archivo']['error'] === UPLOAD_ERR_OK) {
                    $subido = subirArchivo('roe_archivo', 'roe', 'roe');
                    if ($subido === false) {
                        $error = 'El certificado ROE debe ser una imagen válida (JPG, PNG) y no superar los 5MB.';
                    } elseif ($subido !== null) {
                        $roePath = 'uploads/' . $subido;
                    }
                } elseif (!empty($_POST['roe_actual'])) {
                    $roePath = trim($_POST['roe_actual']);
                }

                if ($representante === '') {
                    $error = 'El nombre del representante de la empresa es obligatorio.';
                } elseif (empty($roePath)) {
                    $error = 'La imagen o código QR de ROE es obligatorio para la empresa.';
                } else {
                    $datosEmpresa = [
                        'nombre' => $nombre,
                        'representante' => $representante,
                        'nit' => $nit ?: null,
                        'roe' => $roePath,
                        'correo' => $correo,
                        'contrasena' => $hashContrasena,
                        'telefono' => $telefono,
                        'descripcion' => $descripcion ?: null,
                        'documento_legal_path' => null,
                        'verificada' => 1,
                    ];
                }
            } else {
                // Empleado / Candidato
                $fechaNacimiento = $_POST['fecha_nacimiento'] ?? '';
                if ($fechaNacimiento === '') {
                    $error = 'Ingresa tu fecha de nacimiento.';
                } else {
                    $nac = new DateTime($fechaNacimiento);
                    $hoy = new DateTime();
                    $edad = $hoy->diff($nac)->y;

                    if ($edad < 16) {
                        $error = 'Debes tener al menos 16 años para crear una cuenta en la plataforma.';
                    } elseif ($edad < 18) {
                        // Menor de 18 años -> tabla usuarios_men
                        $esMenor = true;
                        $tutorNombre = trim($_POST['tutor_nombre'] ?? '');
                        $tutorTel = trim($_POST['tutor_telefono'] ?? '');
                        $tutorRel = trim($_POST['tutor_relacion'] ?? '');

                        if ($tutorNombre === '' || $tutorTel === '') {
                            $error = 'Al tener entre 16 y 17 años, es obligatorio ingresar el nombre y teléfono de tu tutor(a) legal.';
                        } else {
                            $datosMenor = [
                                'nombre' => $nombre,
                                'correo' => $correo,
                                'contrasena' => $hashContrasena,
                                'telefono' => $telefono ?: null,
                                'fecha_nacimiento' => $fechaNacimiento,
                                'cv_path' => null,
                                'permiso_tutor_path' => null,
                            ];
                            $datosTutor = [
                                'nombre'   => $tutorNombre,
                                'telefono' => $tutorTel,
                                'relacion' => $tutorRel ?: 'Tutor legal',
                            ];
                        }
                    } else {
                        // Mayor de 18 años -> tabla usuarios_may
                        $datosMayor = [
                            'nombre' => $nombre,
                            'correo' => $correo,
                            'contrasena' => $hashContrasena,
                            'tipo' => 'empleado',
                            'telefono' => $telefono ?: null,
                            'fecha_nacimiento' => $fechaNacimiento,
                            'cv_path' => null,
                        ];
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

        if ($tipo === 'empresa') {
            require_once __DIR__ . '/../Models/Empresa.php';
            $modeloEmpresa = new Empresa($conexion);

            try {
                $modeloEmpresa->crear($datosEmpresa);
                $nuevoId = (int) $conexion->lastInsertId();

                session_regenerate_id(true);
                $_SESSION['usuario_id'] = $nuevoId;
                $_SESSION['usuario'] = $nombre;
                $_SESSION['correo'] = $correo;
                $_SESSION['tipo'] = 'empresa';
                $_SESSION['representante'] = $representante ?: null;

                header('Location: index.php?pagina=panel_empresa');
                exit;
            } catch (PDOException $errorBase) {
                vista('registro', [
                    'tipo' => 'empresa',
                    'error' => 'Ese correo ya está registrado en la plataforma.',
                    'form' => $_POST,
                ]);
                return;
            }
        } else {
            // Empleado: menor en usuarios_men o mayor en usuarios_may
            if ($esMenor) {
                require_once __DIR__ . '/../Models/UsuarioMen.php';
                $modeloMen = new UsuarioMen($conexion);

                try {
                    $nuevoId = $modeloMen->crearConTutor($datosMenor, $datosTutor);

                    session_regenerate_id(true);
                    $_SESSION['usuario_id'] = $nuevoId;
                    $_SESSION['usuario'] = $nombre;
                    $_SESSION['correo'] = $correo;
                    $_SESSION['tipo'] = 'empleado';
                    $_SESSION['es_menor'] = 1;
                    $_SESSION['tabla_usuario'] = 'usuarios_men';

                    header('Location: index.php?pagina=trabajos');
                    exit;
                } catch (PDOException $errorBase) {
                    vista('registro', [
                        'tipo' => 'empleado',
                        'error' => 'Ese correo ya está registrado en la plataforma.',
                        'form' => $_POST,
                    ]);
                    return;
                }
            } else {
                require_once __DIR__ . '/../Models/UsuarioMay.php';
                $modeloMay = new UsuarioMay($conexion);

                try {
                    $modeloMay->crear($datosMayor);
                    $nuevoId = (int) $conexion->lastInsertId();

                    session_regenerate_id(true);
                    $_SESSION['usuario_id'] = $nuevoId;
                    $_SESSION['usuario'] = $nombre;
                    $_SESSION['correo'] = $correo;
                    $_SESSION['tipo'] = 'empleado';
                    $_SESSION['es_menor'] = 0;
                    $_SESSION['tabla_usuario'] = 'usuarios_may';

                    header('Location: index.php?pagina=trabajos');
                    exit;
                } catch (PDOException $errorBase) {
                    vista('registro', [
                        'tipo' => 'empleado',
                        'error' => 'Ese correo ya está registrado en la plataforma.',
                        'form' => $_POST,
                    ]);
                    return;
                }
            }
        }
    }
}
