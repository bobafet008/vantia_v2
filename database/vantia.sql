-- Base de datos unificada Vantia (Empleo Juvenil Verificado)
CREATE DATABASE IF NOT EXISTS vantia_empleabilidad_v2
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE vantia_empleabilidad_v2;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    tipo ENUM('empleado', 'empresa', 'admin') NOT NULL DEFAULT 'empleado',
    telefono VARCHAR(30) DEFAULT NULL,
    fecha_nacimiento DATE DEFAULT NULL,
    es_menor TINYINT(1) DEFAULT 0,
    tutor_nombre VARCHAR(100) DEFAULT NULL,
    tutor_telefono VARCHAR(30) DEFAULT NULL,
    tutor_relacion VARCHAR(50) DEFAULT NULL,
    cv_path VARCHAR(255) DEFAULT NULL,
    permiso_tutor_path VARCHAR(255) DEFAULT NULL,
    nit VARCHAR(50) DEFAULT NULL,
    roe VARCHAR(50) DEFAULT NULL,
    documento_legal_path VARCHAR(255) DEFAULT NULL,
    descripcion_empresa TEXT DEFAULT NULL,
    verificada TINYINT(1) NOT NULL DEFAULT 1,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS empleos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    categoria VARCHAR(80) NOT NULL,
    tipo ENUM('medio_tiempo', 'practicas', 'fines_semana', 'remoto') NOT NULL,
    descripcion TEXT NOT NULL,
    requisitos TEXT DEFAULT NULL,
    salario VARCHAR(50) DEFAULT NULL,
    ubicacion VARCHAR(120) DEFAULT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (empresa_id),
    CONSTRAINT fk_empleos_empresa FOREIGN KEY (empresa_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Postulaciones (Aplicaciones de candidatos a empleos)
CREATE TABLE IF NOT EXISTS postulaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleo_id INT NOT NULL,
    candidato_id INT NOT NULL,
    mensaje TEXT DEFAULT NULL,
    estado ENUM('pendiente', 'revision', 'entrevista', 'contratado', 'rechazado') NOT NULL DEFAULT 'pendiente',
    fecha_postulacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (empleo_id),
    INDEX (candidato_id),
    UNIQUE KEY uk_empleo_candidato (empleo_id, candidato_id),
    CONSTRAINT fk_postulacion_empleo FOREIGN KEY (empleo_id) REFERENCES empleos(id) ON DELETE CASCADE,
    CONSTRAINT fk_postulacion_candidato FOREIGN KEY (candidato_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Calificaciones
CREATE TABLE IF NOT EXISTS calificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empresa_id INT NOT NULL,
    candidato_id INT NOT NULL,
    puntuacion TINYINT NOT NULL CHECK (puntuacion BETWEEN 0 AND 5),
    comentario TEXT DEFAULT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_calif_empresa_candidato (empresa_id, candidato_id),
    CONSTRAINT fk_calif_empresa FOREIGN KEY (empresa_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_calif_candidato FOREIGN KEY (candidato_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Empresas
CREATE TABLE IF NOT EXISTS empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(150) NOT NULL,
    nit VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    descripcion TEXT,
    documento_legal_path VARCHAR(255),
    verificada TINYINT(1) NOT NULL DEFAULT 0,
    creada_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
