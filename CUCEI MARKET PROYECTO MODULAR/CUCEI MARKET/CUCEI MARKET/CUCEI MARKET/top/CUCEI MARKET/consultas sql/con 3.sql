ALTER TABLE usuarios
ADD COLUMN rol ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario';
