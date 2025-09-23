use sistema_ventas;

ALTER TABLE clientes 
ADD COLUMN dni VARCHAR(8) UNIQUE AFTER id,
ADD COLUMN direccion VARCHAR(200) NULL AFTER telefono,
MODIFY COLUMN email VARCHAR(100) NULL;

select * from asistencia
select * from usuarios
select * from clientes
DESCRIBE productos;
drop TABLE ordenes_servicio 
ADD COLUMN cliente_nombre VARCHAR(100) NOT NULL AFTER cliente_id;

DESCRIBE ordenes_servicio

-- 1. Eliminar la clave foránea
ALTER TABLE ordenes_servicio DROP FOREIGN KEY ordenes_servicio_ibfk_1;

-- 2. Permitir cliente_id NULL y quitar la restricción
ALTER TABLE ordenes_servicio 
MODIFY COLUMN cliente_id INT NULL;

-- 3. (Opcional) Puedes dejar el campo o eliminarlo si ya no lo usas
-- ALTER TABLE ordenes_servicio DROP COLUMN cliente_id;

ALTER TABLE ordenes_servicio MODIFY COLUMN cliente_id INT NULL;

select * from ordenes_servicio
select * from clientes
ALTER TABLE productos MODIFY COLUMN stock INT DEFAULT 0;

ALTER TABLE ventas 
ADD COLUMN cliente_nombre VARCHAR(100) NULL AFTER cliente_id,
ADD COLUMN cliente_dni VARCHAR(8) NULL AFTER cliente_nombre,
ADD COLUMN cliente_telefono VARCHAR(15) NULL AFTER cliente_dni;

ALTER TABLE clientes 
ADD COLUMN dni VARCHAR(8) UNIQUE NULL,
ADD INDEX idx_dni (dni);

DESCRIBE detalle_venta;
SELECT * FROM detalle_venta WHERE venta_id = 11;

CREATE TABLE detalle_orden_repuestos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    orden_id INT,
    producto_id INT,
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    FOREIGN KEY (orden_id) REFERENCES ordenes_servicio(id),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

DESCRIBE clientes;
DESCRIBE ordenes_servicio;
ALTER TABLE ordenes_servicio
DROP COLUMN cliente_telefono;

DESCRIBE ventas;
DESCRIBE detalle_venta;

ALTER TABLE clientes 
ADD COLUMN tipo_documento ENUM('dni', 'ruc', 'ce', 'pasaporte') DEFAULT 'dni',
ADD COLUMN numero_documento VARCHAR(20) NOT NULL,
ADD UNIQUE KEY unique_doc (tipo_documento, numero_documento);

ALTER TABLE clientes 
DROP INDEX dni;

ALTER TABLE clientes 
CHANGE COLUMN dni numero_documento VARCHAR(20) NOT NULL;

ALTER TABLE clientes 
ADD COLUMN tipo_documento ENUM('dni', 'ruc', 'ce', 'pasaporte') DEFAULT 'dni',
ADD UNIQUE KEY unique_doc (tipo_documento, numero_documento);

UPDATE clientes 
SET tipo_documento = 'dni' 
WHERE numero_documento IS NOT NULL AND LENGTH(numero_documento) = 8;

SET SQL_SAFE_UPDATES = 1;#opara el udpate, activado 1 descativar 0