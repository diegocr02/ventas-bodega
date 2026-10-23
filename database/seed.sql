-- =============================================================
-- seed.sql  —  Datos de prueba obligatorios (P001-P008)
-- Ejecutar DESPUES de schema.sql.
-- Caso: minimarket San Vicente de Canete.
-- =============================================================

USE bodega_ventas;

-- Limpiar datos previos antes de insertar (idempotente)
DELETE FROM venta;
DELETE FROM producto;

-- Reiniciar auto_increment
ALTER TABLE producto AUTO_INCREMENT = 1;

-- ------------------------------------------------------------
-- 8 productos del caso (valores exactos del enunciado)
-- Codigo | Nombre                  | Categoria | Precio | Stock | Min
-- ------------------------------------------------------------
INSERT INTO producto (codigo, nombre, categoria, precio_venta, stock, stock_minimo) VALUES
    ('P001', 'Arroz superior 5 kg',    'Abarrotes', 22.50,  20,  5),
    ('P002', 'Azucar rubia 1 kg',      'Abarrotes',  4.20,  30, 10),
    ('P003', 'Aceite vegetal 1 L',     'Abarrotes',  9.90,  12,  4),
    ('P004', 'Leche evaporada 400 g',  'Lacteos',    4.00,  48, 12),
    ('P005', 'Fideo spaghetti 500 g',  'Abarrotes',  3.50,  25,  8),
    ('P006', 'Atun en trozos 170 g',   'Conservas',  6.50,   3,  6),
    ('P007', 'Gaseosa 1.5 L',          'Bebidas',    7.00,  18,  6),
    ('P008', 'Detergente 1 kg',        'Limpieza',  11.80,   2,  4);

-- Verificacion rapida (debe devolver 8):
-- SELECT COUNT(*) FROM producto;
--
-- Productos en o por debajo del stock minimo (deben aparecer resaltados en RF2):
-- P006: stock 3  <= minimo 6
-- P008: stock 2  <= minimo 4
-- SELECT codigo, nombre, stock, stock_minimo
--   FROM producto
--  WHERE stock <= stock_minimo;
