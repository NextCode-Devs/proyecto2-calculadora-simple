CREATE DATABASE IF NOT EXISTS finanzas_personales;
USE finanzas_personales;

-- Tabla de usuarios (para futura implementación de login)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de transacciones
CREATE TABLE transacciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL, -- Para asociar con usuarios en el futuro
    tipo ENUM('ingreso', 'gasto') NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    nota TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de configuración
CREATE TABLE configuraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    clave VARCHAR(50) NOT NULL,
    valor VARCHAR(255) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    UNIQUE KEY (usuario_id, clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Configuración global (para usuarios no autenticados)
INSERT INTO configuraciones (clave, valor) VALUES 
('limite_gastos', '1000'),
('moneda', '$'),
('aviso_gastos', '1');