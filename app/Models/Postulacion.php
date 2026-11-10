<?php
class Postulacion
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function crear($empleoId, $candidatoId, $mensaje = null)
    {
        $sql = 'INSERT INTO postulaciones (empleo_id, candidato_id, mensaje, estado)
                VALUES (:empleo_id, :candidato_id, :mensaje, "pendiente")';
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            'empleo_id' => (int) $empleoId,
            'candidato_id' => (int) $candidatoId,
            'mensaje' => $mensaje,
        ]);
    }

    public function yaPostulado($empleoId, $candidatoId)
    {
        $sql = 'SELECT COUNT(*) FROM postulaciones WHERE empleo_id = :empleo_id AND candidato_id = :candidato_id';
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute([
            'empleo_id' => (int) $empleoId,
            'candidato_id' => (int) $candidatoId,
        ]);
        return (int) $consulta->fetchColumn() > 0;
    }

    public function obtenerIdsPostuladosPorCandidato($candidatoId)
    {
        $sql = 'SELECT empleo_id FROM postulaciones WHERE candidato_id = :candidato_id';
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['candidato_id' => (int) $candidatoId]);
        return $consulta->fetchAll(PDO::FETCH_COLUMN);
    }

    public function obtenerPorEmpresa($empresaId)
    {
        $sql = 'SELECT p.*, e.titulo AS empleo_titulo, e.categoria, e.tipo AS empleo_tipo,
                       u.nombre AS candidato_nombre, u.correo AS candidato_correo,
                       u.telefono AS candidato_telefono, u.fecha_nacimiento, u.es_menor,
                       u.tutor_nombre, u.tutor_telefono, u.tutor_relacion
                FROM postulaciones p
                INNER JOIN empleos e ON e.id = p.empleo_id
                INNER JOIN usuarios u ON u.id = p.candidato_id
                WHERE e.empresa_id = :empresa_id
                ORDER BY p.fecha_postulacion DESC';
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['empresa_id' => (int) $empresaId]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorCandidato($candidatoId)
    {
        $sql = 'SELECT p.*, e.titulo AS empleo_titulo, e.categoria, e.tipo AS empleo_tipo,
                       e.salario, e.ubicacion, u.nombre AS empresa_nombre
                FROM postulaciones p
                INNER JOIN empleos e ON e.id = p.empleo_id
                INNER JOIN usuarios u ON u.id = e.empresa_id
                WHERE p.candidato_id = :candidato_id
                ORDER BY p.fecha_postulacion DESC';
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['candidato_id' => (int) $candidatoId]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cambiarEstado($postulacionId, $nuevoEstado)
    {
        $estadosValidos = ['pendiente', 'revision', 'entrevista', 'contratado', 'rechazado'];
        if (!in_array($nuevoEstado, $estadosValidos, true)) {
            return false;
        }
        $sql = 'UPDATE postulaciones SET estado = :estado WHERE id = :id';
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute([
            'estado' => $nuevoEstado,
            'id' => (int) $postulacionId,
        ]);
    }

    public function contarPorEstado()
    {
        $sql = 'SELECT estado, COUNT(*) as total FROM postulaciones GROUP BY estado';
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
