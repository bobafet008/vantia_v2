<?php
class HomeController
{
    public function inicio()
    {
        vista('inicio');
    }

    public function empleado()
    {
        vista('empleado');
    }

    public function empresa()
    {
        vista('empresa');
    }

    public function trabajos()
    {
        require __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../Models/Postulacion.php';

        $empleos = $conexion->query(
            "SELECT e.*, u.nombre AS empresa, u.nit, u.verificada
             FROM empleos e
             INNER JOIN usuarios u ON u.id = e.empresa_id
             WHERE e.activo = 1 AND u.tipo = 'empresa'
             ORDER BY e.creado_en DESC"
        )->fetchAll();

        $postuladosIds = [];
        if (!empty($_SESSION['usuario_id']) && ($_SESSION['tipo'] ?? '') === 'empleado') {
            $modeloPostulacion = new Postulacion($conexion);
            $postuladosIds = $modeloPostulacion->obtenerIdsPostuladosPorCandidato((int) $_SESSION['usuario_id']);
        }

        vista('trabajos', [
            'empleos' => $empleos,
            'postuladosIds' => $postuladosIds,
            'mensajeExito' => $_SESSION['flash_exito'] ?? '',
            'mensajeError' => $_SESSION['flash_error'] ?? '',
        ]);
        unset($_SESSION['flash_exito'], $_SESSION['flash_error']);
    }

    public function postular()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'empleado') {
            $_SESSION['flash_error'] = 'Debes iniciar sesión como empleado para poder postularte a las vacantes.';
            header('Location: index.php?pagina=login&tipo=empleado');
            exit;
        }

        $empleoId = (int) ($_POST['empleo_id'] ?? 0);
        $mensaje = trim($_POST['mensaje'] ?? '');
        $candidatoId = (int) $_SESSION['usuario_id'];

        if ($empleoId <= 0) {
            $_SESSION['flash_error'] = 'Vacante no válida.';
            header('Location: index.php?pagina=trabajos');
            exit;
        }

        require __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../Models/Postulacion.php';
        $modelo = new Postulacion($conexion);

        if ($modelo->yaPostulado($empleoId, $candidatoId)) {
            $_SESSION['flash_error'] = 'Ya te has postulado previamente a esta vacante.';
        } else {
            $modelo->crear($empleoId, $candidatoId, $mensaje ?: null);
            $_SESSION['flash_exito'] = '¡Tu postulación ha sido enviada con éxito! La empresa revisará tu perfil.';
        }

        header('Location: index.php?pagina=trabajos');
        exit;
    }

    public function misPostulaciones()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'empleado') {
            header('Location: index.php?pagina=login&tipo=empleado');
            exit;
        }

        require __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../Models/Postulacion.php';
        $modelo = new Postulacion($conexion);
        $postulaciones = $modelo->obtenerPorCandidato((int) $_SESSION['usuario_id']);

        vista('mis_postulaciones', [
            'postulaciones' => $postulaciones,
            'mensajeExito' => $_SESSION['flash_exito'] ?? '',
            'mensajeError' => $_SESSION['flash_error'] ?? '',
        ]);
        unset($_SESSION['flash_exito'], $_SESSION['flash_error']);
    }

    public function cancelarPostulacion()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'empleado') {
            header('Location: index.php?pagina=login&tipo=empleado');
            exit;
        }

        $postulacionId = (int) ($_POST['postulacion_id'] ?? 0);
        $candidatoId = (int) $_SESSION['usuario_id'];

        if ($postulacionId > 0) {
            require __DIR__ . '/../../config/database.php';
            $stmt = $conexion->prepare("DELETE FROM postulaciones WHERE id = ? AND candidato_id = ? AND estado = 'pendiente'");
            $stmt->execute([$postulacionId, $candidatoId]);

            if ($stmt->rowCount() > 0) {
                $_SESSION['flash_exito'] = 'Has retirado tu postulación correctamente.';
            } else {
                $_SESSION['flash_error'] = 'No fue posible retirar la postulación o ya está en proceso de revisión.';
            }
        }

        header('Location: index.php?pagina=mis_postulaciones');
        exit;
    }

    public function panelEmpresa()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'empresa') {
            header('Location: index.php?pagina=login&tipo=empresa');
            exit;
        }

        require __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../Models/Postulacion.php';

        $empresaId = (int) $_SESSION['usuario_id'];

        // Obtener vacantes de la empresa
        $consulta = $conexion->prepare('SELECT * FROM empleos WHERE empresa_id = ? ORDER BY creado_en DESC');
        $consulta->execute([$empresaId]);
        $empleos = $consulta->fetchAll();

        // Obtener postulantes a las vacantes de la empresa
        $modeloPostulacion = new Postulacion($conexion);
        $postulaciones = $modeloPostulacion->obtenerPorEmpresa($empresaId);

        vista('panel_empresa', [
            'empleos' => $empleos,
            'postulaciones' => $postulaciones,
            'exito' => $_SESSION['empleo_exito'] ?? '',
            'error' => $_SESSION['empleo_error'] ?? '',
        ]);
        unset($_SESSION['empleo_exito'], $_SESSION['empleo_error']);
    }

    public function guardarEmpleo()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'empresa') {
            header('Location: index.php?pagina=login&tipo=empresa');
            exit;
        }

        require __DIR__ . '/../../config/database.php';

        $titulo = trim($_POST['titulo'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $tipo = $_POST['tipo_empleo'] ?? '';
        $descripcion = trim($_POST['descripcion'] ?? '');
        $requisitos = trim($_POST['requisitos'] ?? '');
        $salario = trim($_POST['salario'] ?? '');
        $ubicacion = trim($_POST['ubicacion'] ?? '');
        $tiposValidos = ['medio_tiempo', 'practicas', 'fines_semana', 'remoto'];

        if ($titulo === '' || $categoria === '' || $descripcion === '' || !in_array($tipo, $tiposValidos, true)) {
            $_SESSION['empleo_error'] = 'Completa los campos obligatorios y selecciona un tipo de empleo válido.';
        } else {
            $consulta = $conexion->prepare(
                'INSERT INTO empleos (empresa_id, titulo, categoria, tipo, descripcion, requisitos, salario, ubicacion)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $consulta->execute([
                (int) $_SESSION['usuario_id'],
                $titulo,
                $categoria,
                $tipo,
                $descripcion,
                $requisitos ?: null,
                $salario ?: null,
                $ubicacion ?: null,
            ]);
            $_SESSION['empleo_exito'] = 'La vacante se publicó correctamente.';
        }

        header('Location: index.php?pagina=panel_empresa');
        exit;
    }

    public function alternarEmpleo()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'empresa') {
            header('Location: index.php?pagina=login&tipo=empresa');
            exit;
        }

        $empleoId = (int) ($_POST['empleo_id'] ?? 0);
        $empresaId = (int) $_SESSION['usuario_id'];

        if ($empleoId > 0) {
            require __DIR__ . '/../../config/database.php';
            $stmt = $conexion->prepare('UPDATE empleos SET activo = 1 - activo WHERE id = ? AND empresa_id = ?');
            $stmt->execute([$empleoId, $empresaId]);
            $_SESSION['empleo_exito'] = 'El estado de la vacante ha sido actualizado.';
        }

        header('Location: index.php?pagina=panel_empresa');
        exit;
    }

    public function eliminarEmpleo()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'empresa') {
            header('Location: index.php?pagina=login&tipo=empresa');
            exit;
        }

        $empleoId = (int) ($_POST['empleo_id'] ?? 0);
        $empresaId = (int) $_SESSION['usuario_id'];

        if ($empleoId > 0) {
            require __DIR__ . '/../../config/database.php';
            $stmt = $conexion->prepare('DELETE FROM empleos WHERE id = ? AND empresa_id = ?');
            $stmt->execute([$empleoId, $empresaId]);
            $_SESSION['empleo_exito'] = 'La vacante ha sido eliminada con éxito.';
        }

        header('Location: index.php?pagina=panel_empresa');
        exit;
    }

    public function cambiarEstadoPostulacion()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'empresa') {
            header('Location: index.php?pagina=login&tipo=empresa');
            exit;
        }

        $postulacionId = (int) ($_POST['postulacion_id'] ?? 0);
        $nuevoEstado = $_POST['estado'] ?? '';

        if ($postulacionId > 0 && $nuevoEstado !== '') {
            require __DIR__ . '/../../config/database.php';
            require_once __DIR__ . '/../Models/Postulacion.php';
            $modelo = new Postulacion($conexion);
            $modelo->cambiarEstado($postulacionId, $nuevoEstado);
            $_SESSION['empleo_exito'] = 'El estado del postulante ha sido actualizado.';
        }

        header('Location: index.php?pagina=panel_empresa#postulantes');
        exit;
    }

    public function panelAdmin()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
            header('Location: index.php?pagina=login&tipo=admin');
            exit;
        }

        require __DIR__ . '/../../config/database.php';
        require_once __DIR__ . '/../Models/Usuario.php';

        $modeloUsuario = new Usuario($conexion);
        $usuarios = $modeloUsuario->obtenerTodos();

        $totalEmpleos = (int) $conexion->query('SELECT COUNT(*) FROM empleos')->fetchColumn();
        $totalPostulaciones = (int) $conexion->query('SELECT COUNT(*) FROM postulaciones')->fetchColumn();
        $totalEmpresas = (int) $conexion->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'empresa'")->fetchColumn();
        $totalCandidatos = (int) $conexion->query("SELECT COUNT(*) FROM usuarios WHERE tipo = 'empleado'")->fetchColumn();

        vista('panel_admin', [
            'usuarios' => $usuarios,
            'totalEmpleos' => $totalEmpleos,
            'totalPostulaciones' => $totalPostulaciones,
            'totalEmpresas' => $totalEmpresas,
            'totalCandidatos' => $totalCandidatos,
            'exito' => $_SESSION['admin_exito'] ?? '',
            'error' => $_SESSION['admin_error'] ?? '',
        ]);
        unset($_SESSION['admin_exito'], $_SESSION['admin_error']);
    }

    public function toggleVerificarEmpresa()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
            header('Location: index.php?pagina=login&tipo=admin');
            exit;
        }

        $usuarioId = (int) ($_POST['usuario_id'] ?? 0);

        if ($usuarioId > 0) {
            require __DIR__ . '/../../config/database.php';
            $stmt = $conexion->prepare("UPDATE usuarios SET verificada = 1 - verificada WHERE id = ? AND tipo = 'empresa'");
            $stmt->execute([$usuarioId]);
            $_SESSION['admin_exito'] = 'El estado de verificación de la empresa ha sido actualizado.';
        }

        header('Location: index.php?pagina=panel_admin');
        exit;
    }

    public function eliminarUsuario()
    {
        if (empty($_SESSION['usuario_id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
            header('Location: index.php?pagina=login&tipo=admin');
            exit;
        }

        $usuarioId = (int) ($_POST['usuario_id'] ?? 0);
        $miId = (int) $_SESSION['usuario_id'];

        if ($usuarioId > 0 && $usuarioId !== $miId) {
            require __DIR__ . '/../../config/database.php';
            $stmt = $conexion->prepare('DELETE FROM usuarios WHERE id = ?');
            $stmt->execute([$usuarioId]);
            $_SESSION['admin_exito'] = 'El usuario ha sido eliminado correctamente del sistema.';
        } else {
            $_SESSION['admin_error'] = 'No puedes eliminar tu propia cuenta de administrador.';
        }

        header('Location: index.php?pagina=panel_admin');
        exit;
    }
}
