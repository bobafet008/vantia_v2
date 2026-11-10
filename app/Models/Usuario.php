<?php
class Usuario
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function crear($datos)
    {
        $sql = 'INSERT INTO usuarios (
                    nombre, correo, contrasena, tipo, telefono, fecha_nacimiento,
                    es_menor, tutor_nombre, tutor_telefono, tutor_relacion,
                    nit, roe, descripcion_empresa
                ) VALUES (
                    :nombre, :correo, :contrasena, :tipo, :telefono, :fecha_nacimiento,
                    :es_menor, :tutor_nombre, :tutor_telefono, :tutor_relacion,
                    :nit, :roe, :descripcion_empresa
                )';
        $consulta = $this->conexion->prepare($sql);
        return $consulta->execute($datos);
    }

    public function buscarPorCorreo($correo)
    {
        $sql = 'SELECT * FROM usuarios WHERE correo = :correo LIMIT 1';
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['correo' => $correo]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $sql = 'SELECT * FROM usuarios WHERE id = :id LIMIT 1';
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['id' => (int) $id]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerPorTipo($tipo)
    {
        $sql = 'SELECT * FROM usuarios WHERE tipo = :tipo ORDER BY fecha_registro DESC';
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(['tipo' => $tipo]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodos()
    {
        $sql = 'SELECT * FROM usuarios ORDER BY fecha_registro DESC';
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
