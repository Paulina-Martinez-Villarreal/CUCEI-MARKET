CREATE TABLE `productos` ( 
    `id` INT(10) NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(150) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
    `descripcion` TEXT NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
    `precio` DECIMAL(10,2) NOT NULL,
    `imagen` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
    `fecha_publicacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `categoria` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_0900_ai_ci',
    `condicion` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_0900_ai_ci', -- Nuevo campo para la condición
    PRIMARY KEY (`id`) USING BTREE
)
COLLATE='utf8mb4_0900_ai_ci'
ENGINE=InnoDB
AUTO_INCREMENT=6;
