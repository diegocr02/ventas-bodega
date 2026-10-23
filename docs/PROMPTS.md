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

## Prompt 2 Corrige D0.md sin cambiar lo que ya está bien. Agrega RNF de seguridad: RNF5 consultas con PDO y sentencias preparadas, sin concatenar SQL; RNF6 credenciales en .env fuera del repositorio; RNF7 escapar toda salida en las vistas con htmlspecialchars; RNF8 validar los datos en el servidor. -Proceso actual: elimina «se suma el total vendido a mano»; el caso dice que no sabe cuánto vendió. -RNF3 (red local) y «número entero» de RN1: muévelos a Supuestos marcados SUPUESTO. -Fuera de alcance: agrega roles, facturación electrónica, agentes conversacionales y despliegue público. -Supuestos: elimina S2 y S5 por redundantes. -No agregues nada que no esté en el caso. Mantén RF1–RF4 y RN1–RN5 igual. 

## Prompt 3 Lee D0.md antes de escribir. Stack: PHP 8 orientado a objetos, MySQL de XAMPP, PDO con sentencias preparadas, sin frameworks. Genera requirements.md a partir de D0.md. 
TEN EN CUENTA: Contenido mínimo	Cómo se verifica 
-requirements.md: criterios de aceptación en formato EARS («CUANDO … EL SISTEMA DEBERÁ …»), identificados RF y RNF	Cada -RN de D0.md aparece en al menos un criterio 
-design.md: capas Model → Repository → Service → Controller → views, con qué hace y qué no hace cada una	
-Las cinco capas descritas design.md: tablas producto y venta con campos, tipos, claves y restricciones	Coinciden con los datos de prueba 
-design.md: contratos de clases con firmas tipadas; transacción para venta y descuento de stock; manejo de errores	Firmas -con tipos; transacción descrita 
-Al menos dos correcciones propias a lo que generó Kiro	Registradas en REGISTRO.md RN1–RN5 en al menos un criterio cada una. Sin login, clientes ni boletas. No inventes requisitos fuera de D0.md. No generes design.md ni tasks.md todavía. 
## Prompt 4
Corrige requirements.md sin cambiar lo que ya está bien. Falta la sección RNF3 — Usabilidad mínima: agrégala con su propio criterio de aceptación en formato EARS. Además, todos los criterios que describen errores o rechazos (CA-RF1.2, CA-RF1.3, CA-RF1.4, CA-RF3.2, CA-RF3.3, CA-RF3.4 y CA-RF3.5) deben reescribirse con el formato «SI … ENTONCES EL SISTEMA DEBERÁ …» en lugar de «CUANDO». 
 agrega [RN5] en CA-RNF1.1, porque describe la transacción de venta. Al final del documento agrega una tabla que relacione cada regla RN1 a RN5 con los criterios que la cubren. No agregues requisitos nuevos ni generes design.md ni tasks.md.
 ## Prompt 5
 