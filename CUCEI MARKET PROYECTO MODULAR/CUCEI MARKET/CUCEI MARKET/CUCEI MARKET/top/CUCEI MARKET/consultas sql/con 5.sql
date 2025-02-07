CREATE TABLE usuarios (
    id INT(10) NOT NULL AUTO_INCREMENT,
    nombre_completo VARCHAR(100) NULL,
    correo VARCHAR(100) NULL UNIQUE,
    clave VARCHAR(255) NULL,  -- La contraseña será almacenada de forma segura (con hash)
    rol ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',  -- 'admin' o 'usuario'
    estado ENUM('pendiente', 'aprobado', 'rechazado') DEFAULT 'pendiente',
    PRIMARY KEY (id)
);
