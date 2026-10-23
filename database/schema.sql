-- =============================================================
-- schema.sql  —  bodega_ventas
-- Motor : InnoDB | Charset : utf8mb4_unicode_ci
-- Ejecutar en phpMyAdmin o desde la CLI de MySQL/MariaDB.
-- Se puede re-ejecutar sin errores gracias a los DROP … IF EXISTS.
-- =============================================================

-- ------------------------------------------------------------
-- 1. Base de datos
-- ------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS bodega_ventas
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bodega_ventas;

-- ------------------------------------------------------------
-- 2. Eliminar tablas en orden inverso de dependencia
-- ------------------------------------------------------------
DROP TABLE IF EXISTS venta;
DROP TABLE IF EXISTS producto;

-- ------------------------------------------------------------
-- 3. Tabla producto
--    · codigo  UNIQUE                          (RN4)
--    · precio_venta  DECIMAL(10,2)  CHECK >= 0 (RN3)
--    · stock / stock_minimo  INT UNSIGNED      (>= 0 por tipo)
-- ------------------------------------------------------------
CREATE TABLE producto (
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    codigo        VARCHAR(20)   NOT NULL,
    nombre        VARCHAR(120)  NOT NULL,
    categoria     VARCHAR(60)   NOT NULL,
    precio_venta  DECIMAL(10,2) NOT NULL CHECK (precio_venta >= 0),
    stock         INT UNSIGNED  NOT NULL DEFAULT 0,
    stock_minimo  INT UNSIGNED  NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_codigo (codigo)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. Tabla venta
--    · FK producto_id -> producto(id)
--    · cantidad > 0                            (RN1)
--    · precio_unitario / total  DECIMAL(10,2)  (nunca FLOAT)
--    · Indice en fecha_hora para filtrar ventas del dia (RF4)
-- ------------------------------------------------------------
CREATE TABLE venta (
    id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    producto_id      INT UNSIGNED  NOT NULL,
    cantidad         INT UNSIGNED  NOT NULL CHECK (cantidad > 0),
    precio_unitario  DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    total            DECIMAL(10,2) NOT NULL CHECK (total >= 0),
    fecha_hora       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_venta_producto
        FOREIGN KEY (producto_id)
        REFERENCES producto(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    INDEX idx_venta_fecha_hora (fecha_hora)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
