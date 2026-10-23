## Prompt 1 
La situacion actual de un minimarket  de barrio en San Vicente de Cañete vende abarrotes, lácteos, bebidas, conservas y productos de limpieza. Hoy anota las ventas en un cuaderno y revisa el stock mirando los estantes. Al final del día no sabe cuánto vendió, y varias veces se quedó sin productos de alta rotación porque nadie notó que se estaban acabando.
El dueño necesita una aplicación web interna, usada por el cajero, que permita:
(1) registrar productos con código, nombre, categoría, precio de venta, stock actual y stock mínimo;
(2) listar los productos y resaltar los que están en o por debajo de su stock mínimo;
(3) registrar una venta de un producto: el cajero elige el producto e indica la cantidad; el sistema toma el precio vigente, calcula el total, registra fecha y hora, y descuenta el stock;
(4) listar las ventas del día con el total vendido del día.
Reglas de negocio: la cantidad vendida debe ser mayor que cero; no se puede vender más de lo que hay en stock; el precio de venta no puede ser negativo; el código de producto es único; la venta y el descuento de stock ocurren juntos o no ocurre ninguno.
Requisitos obligatorios para aprobar: (1), (2) y (3). El requisito (4) completa la nota máxima del criterio de código.
Datos de prueba (obligatorios en seed.sql)
Código	Nombre	Categoría	Precio S/	Stock	Mínimo
P001	Arroz superior 5 kg	Abarrotes	22.50	20	5
P002	Azúcar rubia 1 kg	Abarrotes	4.20	30	10
P003	Aceite vegetal 1 L	Abarrotes	9.90	12	4
P004	Leche evaporada 400 g	Lácteos	4.00	48	12
P005	Fideo spaghetti 500 g	Abarrotes	3.50	25	8
P006	Atún en trozos 170 g	Conservas	6.50	3	6
P007	Gaseosa 1.5 L	Bebidas	7.00	18	6
P008	Detergente 1 kg	Limpieza	11.80	2	4

Tenemos como tarea : Redactar el D0.md de una aplicación web interna para registrar ventas y controlar el stock en el minimarket en san vicente, usada solo por el cajero, basándote únicamente en el caso del contexto

formato: Contenido mínimo de D0.md	Cómo se verifica
Problema en una frase; usuarios y qué hace cada uno; proceso actual y su dificultad	Coherente con el caso de la sección 2
Requisitos funcionales RF1… y no funcionales RNF1… (seguridad e integridad de datos)	Los cuatro requisitos del caso presentes
Reglas de negocio RN1…	Las cinco reglas del caso, sin omitir ninguna
Fuera de alcance; supuestos marcados como SUPUESTO	Sin login, clientes ni boletas

restricciones: que no invente nada, que no omita reglas y qué queda fuera de alcance.
## Prompt 2