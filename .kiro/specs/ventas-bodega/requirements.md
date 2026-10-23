# Requirements — Sistema de Ventas y Control de Stock

> Derivado de D0.md. Stack: PHP 8 OOP · MySQL (XAMPP) · PDO con sentencias preparadas · sin frameworks.

---

## RF1 — Gestión de productos

**CA-RF1.1**
CUANDO el cajero envíe el formulario de nuevo producto con código, nombre, categoría, precio de venta, stock actual y stock mínimo válidos,
EL SISTEMA DEBERÁ persistir el producto en la base de datos y mostrar el listado de productos actualizado.

**CA-RF1.2** *(RN4 — Código único)*
SI el cajero intenta registrar un producto cuyo código ya existe en el sistema,
ENTONCES EL SISTEMA DEBERÁ rechazar la operación y mostrar un mensaje que indique que el código ya está en uso, sin modificar ningún dato existente.

**CA-RF1.3** *(RN3 — Precio no negativo)*
SI el cajero envía el formulario de producto con un precio de venta negativo,
ENTONCES EL SISTEMA DEBERÁ rechazar la operación y mostrar un mensaje de error indicando que el precio no puede ser negativo.

**CA-RF1.4** *(RNF7 — Validación en servidor)*
SI el cajero envía el formulario de producto con algún campo obligatorio vacío o con un tipo de dato incorrecto,
ENTONCES EL SISTEMA DEBERÁ rechazar la operación en el servidor y devolver un mensaje descriptivo del campo inválido, sin persistir ningún dato.

---

## RF2 — Listado de productos con alerta de stock bajo

**CA-RF2.1**
CUANDO el cajero acceda al listado de productos,
EL SISTEMA DEBERÁ mostrar todos los productos registrados con su código, nombre, categoría, precio de venta, stock actual y stock mínimo.

**CA-RF2.2** *(RNF3 — Usabilidad mínima)*
CUANDO el cajero acceda al listado de productos y uno o más productos tengan stock actual menor o igual a su stock mínimo,
EL SISTEMA DEBERÁ resaltar visualmente esos productos de forma diferenciada respecto al resto (por ejemplo, fila en color distinto o ícono de alerta).

---

## RF3 — Registro de venta

**CA-RF3.1**
CUANDO el cajero seleccione un producto y envíe una cantidad válida en el formulario de venta,
EL SISTEMA DEBERÁ tomar el precio de venta vigente del producto, calcular el total (precio × cantidad), registrar la venta con fecha y hora del servidor, y decrementar el stock actual del producto en la cantidad vendida.

**CA-RF3.2** *(RN1 — Cantidad positiva)*
SI el cajero envía el formulario de venta con una cantidad menor o igual a cero,
ENTONCES EL SISTEMA DEBERÁ rechazar la operación y mostrar un mensaje indicando que la cantidad debe ser mayor que cero, sin registrar ninguna venta ni modificar el stock.

**CA-RF3.3** *(RN2 — Stock suficiente)*
SI el cajero envía el formulario de venta con una cantidad que supere el stock actual del producto,
ENTONCES EL SISTEMA DEBERÁ rechazar la operación y mostrar un mensaje informando el stock disponible, sin registrar ninguna venta ni modificar el stock.

**CA-RF3.4** *(RN5 — Atomicidad)*
SI ocurre un error en cualquier paso de la operación de venta (inserción del registro o actualización del stock),
ENTONCES EL SISTEMA DEBERÁ revertir toda la transacción de forma que ni la venta ni el descuento de stock queden aplicados parcialmente.

**CA-RF3.5** *(RNF7 — Validación en servidor)*
SI el cajero envía el formulario de venta con datos faltantes o de tipo incorrecto,
ENTONCES EL SISTEMA DEBERÁ validar los datos en el servidor antes de ejecutar cualquier operación sobre la base de datos y devolver un mensaje descriptivo del error.

---

## RF4 — Listado de ventas del día

**CA-RF4.1**
CUANDO el cajero acceda al listado de ventas del día,
EL SISTEMA DEBERÁ mostrar todas las ventas cuya fecha coincida con la fecha actual del servidor, incluyendo nombre del producto, cantidad, precio unitario, total de la línea y hora del registro.

**CA-RF4.2**
CUANDO el cajero acceda al listado de ventas del día,
EL SISTEMA DEBERÁ mostrar el total acumulado de todas las ventas del día como suma de los totales individuales.

---

## RNF1 — Integridad de datos

**CA-RNF1.1** *(RN5 — Atomicidad)*
CUANDO se ejecute la operación de registro de venta,
EL SISTEMA DEBERÁ encapsularla en una transacción de base de datos de forma que el INSERT en la tabla de ventas y el UPDATE del stock se confirmen o reviertan juntos.

---

## RNF2 — Persistencia

**CA-RNF2.1**
CUANDO el servidor se reinicie o se cierre el navegador,
EL SISTEMA DEBERÁ conservar todos los productos y ventas previamente registrados, sin pérdida de datos.

---

## RNF3 — Usabilidad mínima

**CA-RNF3.1**
CUANDO el sistema genere el listado de productos,
EL SISTEMA DEBERÁ aplicar un estilo visual diferenciado (por ejemplo, color de fila o ícono) a cada producto cuyo stock actual sea igual o menor a su stock mínimo, de modo que el cajero los identifique de un vistazo sin necesidad de comparar columnas manualmente.

---

## RNF4 — Consultas parametrizadas

**CA-RNF4.1**
CUANDO el sistema realice cualquier consulta o comando sobre la base de datos,
EL SISTEMA DEBERÁ usar exclusivamente PDO con sentencias preparadas y parámetros enlazados; queda prohibida la interpolación o concatenación directa de valores de usuario en cadenas SQL.

---

## RNF5 — Credenciales fuera del repositorio

**CA-RNF5.1**
CUANDO la aplicación se conecte a la base de datos,
EL SISTEMA DEBERÁ leer las credenciales (host, nombre de base de datos, usuario, contraseña) desde un archivo `.env` que no esté incluido en el repositorio de control de versiones.

---

## RNF6 — Escape de salida en vistas

**CA-RNF6.1**
CUANDO el sistema muestre en el navegador cualquier dato proveniente de la base de datos o de la entrada del usuario,
EL SISTEMA DEBERÁ escapar ese valor con `htmlspecialchars` antes de emitirlo en el HTML, para prevenir inyección de código en el cliente.

---

## RNF7 — Validación en servidor

**CA-RNF7.1**
CUANDO el sistema reciba datos de cualquier formulario,
EL SISTEMA DEBERÁ ejecutar la validación en el servidor (PHP) con independencia de cualquier validación que pueda existir en el cliente, antes de persistir o procesar los datos.

---

## Restricciones de alcance

Los siguientes elementos están fuera del alcance y no deben aparecer en ningún criterio de aceptación ni en ninguna implementación:

- Login, sesiones o control de acceso.
- Roles de usuario.
- Registro de clientes o compradores.
- Emisión de boletas, tickets o comprobantes electrónicos.
- Módulo de compras, proveedores o reposición de stock.
- Reportes históricos más allá del listado del día en curso.
- Múltiples sucursales.
- Agentes conversacionales o integración con modelos de lenguaje.
- Despliegue público en internet o en la nube.

---

## Trazabilidad Reglas de Negocio → Criterios de Aceptación

| Regla | Descripción | Criterios que la cubren |
|-------|-------------|-------------------------|
| RN1 | Cantidad vendida mayor que cero | CA-RF3.2 |
| RN2 | No vender más de lo que hay en stock | CA-RF3.3 |
| RN3 | Precio de venta no negativo | CA-RF1.3 |
| RN4 | Código de producto único | CA-RF1.2 |
| RN5 | Atomicidad de venta y descuento de stock | CA-RF3.4 · CA-RNF1.1 |
