# Design — Sistema de Ventas y Control de Stock

> Derivado de D0.md y requirements.md.  
> Stack: PHP 8 OOP · `declare(strict_types=1)` · MySQL XAMPP (base `bodega_ventas`, charset `utf8mb4`) · PDO con sentencias preparadas · sin frameworks.

---

## 1. Estructura de carpetas

```
ventas-bodega/
├── config/
│   └── database.php              # Fabrica la instancia PDO; lee credenciales desde .env
├── app/
│   ├── Models/
│   │   ├── Producto.php
│   │   └── Venta.php
│   ├── Repositories/
│   │   ├── ProductoRepository.php
│   │   └── VentaRepository.php
│   ├── Services/
│   │   ├── ProductoService.php
│   │   └── VentaService.php
│   ├── Exceptions/
│   │   ├── ValidacionException.php
│   │   ├── StockInsuficienteException.php
│   │   └── CodigoDuplicadoException.php
│   └── Controllers/
│       ├── ProductoController.php
│       └── VentaController.php
├── views/
│   ├── layout.php
│   ├── productos/
│   │   ├── lista.php
│   │   └── nuevo.php
│   └── ventas/
│       ├── nueva.php
│       └── del_dia.php
├── public/
│   └── index.php                 # Front controller; unico punto de entrada HTTP
├── .env                          # Credenciales (excluido del repositorio)
├── .env.example                  # Plantilla sin valores reales
├── .gitignore                    # Incluye .env
├── schema.sql                    # DDL: CREATE TABLE producto y venta
└── seed.sql                      # Datos de prueba P001-P008
```

---

## 2. Las cinco capas

### 2.1 Model — `app/Models/`

**Que hace:**
- Representa un unico registro del dominio como objeto PHP con tipado estricto.
- Expone propiedades de solo lectura declaradas en el constructor (promoted properties).
- Puede contener metodos de consulta de estado puros (sin efectos secundarios), como `tieneStockBajo()`.

**Que NO hace:**
- No se conecta a la base de datos ni conoce PDO.
- No valida reglas de negocio ni lanza excepciones de dominio.
- No accede a superglobales ni genera salida HTML.

---

### 2.2 Repository — `app/Repositories/`

**Que hace:**
- Encapsula toda la comunicacion con la base de datos mediante PDO y sentencias preparadas (RNF4).
- Traduce filas de resultado en objetos Model; devuelve `Model` o `array` de Models.
- Recibe la instancia `PDO` por inyeccion de dependencia en el constructor.
- Ejecuta SELECT, INSERT y UPDATE con parametros enlazados; nunca interpola valores de usuario en SQL.

**Que NO hace:**
- No valida reglas de negocio (eso corresponde al Service).
- No abre ni cierra transacciones; cuando el Service inicia una transaccion, el Repository usa la misma conexion ya en ese estado.
- No accede a `$_POST` / `$_GET` ni genera salida HTML.

---

### 2.3 Service — `app/Services/`

**Que hace:**
- Implementa las reglas de negocio RN1-RN5.
- Coordina los repositorios necesarios para completar un caso de uso.
- Gestiona la transaccion de venta (`beginTransaction` / `commit` / `rollBack`).
- Lanza excepciones de dominio cuando se viola una regla de negocio.

**Que NO hace:**
- No ejecuta SQL directamente; delega siempre en los repositorios.
- No accede a superglobales HTTP ni genera salida HTML.
- No formatea mensajes de interfaz; solo lanza excepciones con mensajes cortos.

---

### 2.4 Controller — `app/Controllers/`

**Que hace:**
- Lee datos de `$_POST` / `$_GET`.
- Realiza la validacion de formato y tipo en el servidor (RNF7) antes de invocar el Service.
- Llama al Service y captura las excepciones de dominio.
- Asigna variables y carga el archivo de vista correspondiente.

**Que NO hace:**
- No ejecuta consultas SQL directamente.
- No implementa reglas de negocio; solo delega en el Service.
- No genera HTML directamente; lo delega a las vistas.

---

### 2.5 Views — `views/`

**Que hace:**
- Genera el HTML final enviado al navegador.
- Escapa toda variable con `htmlspecialchars()` antes de emitirla (RNF6).
- Muestra mensajes de error o exito que el Controller preparo en variables locales.
- Aplica la clase CSS de alerta a cada fila de producto cuyo stock sea <= stock_minimo (RNF3).

**Que NO hace:**
- No accede a la base de datos.
- No contiene logica de negocio ni condiciones de validacion.
- No lee `$_POST` / `$_GET`.

---

## 3. Esquema de base de datos

### Creacion de la base de datos

```sql
CREATE DATABASE IF NOT EXISTS bodega_ventas
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE bodega_ventas;
```

---

### Tabla `producto`

| Campo          | Tipo            | PK / FK | NOT NULL | DEFAULT | CHECK / restriccion          | Descripcion                        |
|----------------|-----------------|---------|----------|---------|------------------------------|------------------------------------|
| `id`           | INT UNSIGNED    | PK      | SI       | —       | AUTO_INCREMENT               | Clave primaria                     |
| `codigo`       | VARCHAR(20)     | —       | SI       | —       | UNIQUE                       | Codigo legible P001… (RN4)         |
| `nombre`       | VARCHAR(120)    | —       | SI       | —       | —                            | Nombre del producto                |
| `categoria`    | VARCHAR(60)     | —       | SI       | —       | —                            | Texto libre (Abarrotes, Lacteos…)  |
| `precio_venta` | DECIMAL(10,2)   | —       | SI       | —       | precio_venta >= 0            | Precio de venta vigente (RN3)      |
| `stock`        | INT UNSIGNED    | —       | SI       | 0       | stock >= 0 (UNSIGNED)        | Unidades disponibles               |
| `stock_minimo` | INT UNSIGNED    | —       | SI       | 0       | stock_minimo >= 0 (UNSIGNED) | Umbral de alerta de stock bajo     |

```sql
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> `INT UNSIGNED` ya impone `>= 0` en `stock` y `stock_minimo`; se omite CHECK redundante.  
> Se usa `DECIMAL(10,2)` para `precio_venta`; nunca `FLOAT` ni `DOUBLE`.

---

### Tabla `venta`

| Campo             | Tipo            | PK / FK              | NOT NULL | DEFAULT           | CHECK                       | Descripcion                                              |
|-------------------|-----------------|----------------------|----------|-------------------|-----------------------------|----------------------------------------------------------|
| `id`              | INT UNSIGNED    | PK                   | SI       | —                 | AUTO_INCREMENT               | Clave primaria                                           |
| `producto_id`     | INT UNSIGNED    | FK → producto(id)    | SI       | —                 | —                           | Producto vendido                                         |
| `cantidad`        | INT UNSIGNED    | —                    | SI       | —                 | cantidad > 0                | Unidades vendidas (RN1)                                  |
| `precio_unitario` | DECIMAL(10,2)   | —                    | SI       | —                 | precio_unitario >= 0        | Precio vigente **al momento de la venta** (no el actual) |
| `total`           | DECIMAL(10,2)   | —                    | SI       | —                 | total >= 0                  | cantidad x precio_unitario, calculado en PHP             |
| `fecha_hora`      | DATETIME        | —                    | SI       | CURRENT_TIMESTAMP | —                           | Fecha y hora del servidor al registrar                   |

```sql
CREATE TABLE venta (
    id               INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    producto_id      INT UNSIGNED  NOT NULL,
    cantidad         INT UNSIGNED  NOT NULL CHECK (cantidad > 0),
    precio_unitario  DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    total            DECIMAL(10,2) NOT NULL CHECK (total >= 0),
    fecha_hora       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_venta_producto
        FOREIGN KEY (producto_id) REFERENCES producto(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> `precio_unitario` registra el precio al momento de la venta; un cambio posterior en `producto.precio_venta` no altera el historial.

---

## 4. Contratos de clases (firmas tipadas)

Todos los archivos PHP abren con `declare(strict_types=1);`.  
Los valores DECIMAL de la BD se leen como `string` para preservar precision exacta sin redondeo de punto flotante.

### 4.1 Models

```php
// app/Models/Producto.php
class Producto {
    public function __construct(
        public readonly int    $id,
        public readonly string $codigo,
        public readonly string $nombre,
        public readonly string $categoria,
        public readonly string $precio_venta,   // DECIMAL(10,2) leido como string
        public readonly int    $stock,
        public readonly int    $stock_minimo,
    ) {}

    public function tieneStockBajo(): bool {}   // retorna stock <= stock_minimo
}

// app/Models/Venta.php
class Venta {
    public function __construct(
        public readonly int    $id,
        public readonly int    $producto_id,
        public readonly string $nombre_producto, // desnormalizado para vistas
        public readonly int    $cantidad,
        public readonly string $precio_unitario, // DECIMAL como string
        public readonly string $total,           // DECIMAL como string
        public readonly string $fecha_hora,
    ) {}
}
```

---

### 4.2 Repositories

```php
// app/Repositories/ProductoRepository.php
class ProductoRepository {
    public function __construct(private readonly PDO $pdo) {}

    public function findAll(): array {}                            // Producto[]
    public function findById(int $id): ?Producto {}
    public function findByCodigo(string $codigo): ?Producto {}
    public function findByIdForUpdate(int $id): ?Producto {}       // SELECT ... FOR UPDATE
    public function save(
        string $codigo,
        string $nombre,
        string $categoria,
        string $precio_venta,
        int    $stock,
        int    $stock_minimo,
    ): void {}                                                     // INSERT
    public function updateStock(int $id, int $nuevoStock): void {} // UPDATE stock
}

// app/Repositories/VentaRepository.php
class VentaRepository {
    public function __construct(private readonly PDO $pdo) {}

    public function save(
        int    $producto_id,
        int    $cantidad,
        string $precio_unitario,
        string $total,
    ): void {}                                                     // INSERT
    public function findByFecha(string $fecha): array {}           // Venta[] filtradas por DATE(fecha_hora)
    public function sumTotalByFecha(string $fecha): string {}      // DECIMAL como string; total acumulado del dia
}
```

---

### 4.3 Services

```php
// app/Services/ProductoService.php
class ProductoService {
    public function __construct(
        private readonly ProductoRepository $repo,
    ) {}

    /** @throws ValidacionException|CodigoDuplicadoException */
    public function registrar(
        string $codigo,
        string $nombre,
        string $categoria,
        string $precio_venta,
        int    $stock,
        int    $stock_minimo,
    ): void {}

    /** @return Producto[] */
    public function listar(): array {}
}

// app/Services/VentaService.php
class VentaService {
    public function __construct(
        private readonly PDO                $pdo,
        private readonly ProductoRepository $productoRepo,
        private readonly VentaRepository    $ventaRepo,
    ) {}

    /** @throws ValidacionException|StockInsuficienteException */
    public function registrar(int $productoId, int $cantidad): void {}

    /** @return Venta[] */
    public function listarDelDia(): array {}

    public function totalDelDia(): string {} // DECIMAL como string
}
```

---

### 4.4 Exceptions

```php
// app/Exceptions/ValidacionException.php
class ValidacionException extends RuntimeException {}

// app/Exceptions/StockInsuficienteException.php
class StockInsuficienteException extends RuntimeException {
    public function __construct(
        private readonly int $stockDisponible,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
    public function getStockDisponible(): int {}
}

// app/Exceptions/CodigoDuplicadoException.php
class CodigoDuplicadoException extends RuntimeException {}
```

---

### 4.5 Controllers

```php
// app/Controllers/ProductoController.php
class ProductoController {
    public function __construct(
        private readonly ProductoService $service,
    ) {}

    public function lista(): void {}    // GET  — carga views/productos/lista.php
    public function nuevo(): void {}    // GET  — carga views/productos/nuevo.php
    public function guardar(): void {}  // POST — valida, llama service, redirige o muestra error
}

// app/Controllers/VentaController.php
class VentaController {
    public function __construct(
        private readonly VentaService $service,
    ) {}

    public function nueva(): void {}     // GET  — carga views/ventas/nueva.php con lista de productos
    public function registrar(): void {} // POST — valida, llama service, redirige o muestra error
    public function delDia(): void {}    // GET  — carga views/ventas/del_dia.php
}
```

---

### 4.6 Front controller

```
public/index.php
- Lee .env y carga config/database.php para obtener la instancia PDO.
- Aplica un autoloader PSR-4 simple o require manual de clases.
- Enruta por $_GET['action'] (p.ej. productos.lista, productos.guardar, ventas.nueva…).
- Instancia repositorios, servicios y controladores por composicion manual.
- Llama al metodo del controlador correspondiente.
```

---

## 5. Transaccion de registro de venta en `VentaService::registrar()`

Descripcion paso a paso (no es codigo de implementacion).

```
VentaService::registrar(int $productoId, int $cantidad): void
│
├─ PASO 1 — Validacion de formato antes de tocar la BD  [RN1, RNF7]
│     SI $cantidad <= 0
│     LANZA ValidacionException("La cantidad debe ser mayor que cero.")
│
├─ PASO 2 — Iniciar transaccion
│     $pdo->beginTransaction()
│
├─ PASO 3 — Leer producto con bloqueo de fila  [RN2]
│     $productoRepo->findByIdForUpdate($productoId)
│     → SELECT * FROM producto WHERE id = :id FOR UPDATE
│     SI el producto no existe
│     LANZA ValidacionException("Producto no encontrado.")
│
├─ PASO 4 — Validar stock suficiente  [RN2]
│     SI $producto->stock < $cantidad
│     LANZA StockInsuficienteException(
│               stockDisponible: $producto->stock,
│               message: "Stock insuficiente. Disponible: N unidades."
│           )
│
├─ PASO 5 — Calcular total
│     $total = bcmul($producto->precio_venta, (string)$cantidad, 2)
│     (bcmul preserva precision DECIMAL sin artefactos de punto flotante)
│
├─ PASO 6 — Insertar registro de venta  [RF3]
│     $ventaRepo->save(
│         producto_id:     $productoId,
│         cantidad:        $cantidad,
│         precio_unitario: $producto->precio_venta,  // precio vigente en este momento
│         total:           $total,
│     )
│
├─ PASO 7 — Decrementar stock
│     $productoRepo->updateStock(
│         $productoId,
│         $producto->stock - $cantidad
│     )
│
├─ PASO 8 — Confirmar transaccion  [RN5]
│     $pdo->commit()
│     Venta y descuento de stock quedan persistidos juntos.
│
└─ catch (Throwable $e)             [RN5]
      $pdo->rollBack()
      Ninguna modificacion queda aplicada parcialmente.
      Se relanza la excepcion para que VentaController la gestione.
```

> El `SELECT … FOR UPDATE` (paso 3) bloquea la fila durante la transaccion, evitando que dos peticiones concurrentes lean el mismo stock y generen una sobreventa. El `rollBack` en el bloque `catch` garantiza atomicidad completa (RN5 / CA-RF3.4 / CA-RNF1.1).

---

## 6. Manejo de errores y excepciones propias

| Excepcion                      | La lanza                          | La captura              | Mensaje que ve el cajero                                                  | Requisito cubierto  |
|--------------------------------|-----------------------------------|-------------------------|---------------------------------------------------------------------------|---------------------|
| `ValidacionException`          | `ProductoService` / `VentaService`| Controller respectivo   | Texto descriptivo del campo invalido (p. ej. "La cantidad debe ser mayor que cero.") | RN1, RN3, RNF7      |
| `StockInsuficienteException`   | `VentaService`                    | `VentaController`       | "Stock insuficiente. Disponible: N unidades."                             | RN2                 |
| `CodigoDuplicadoException`     | `ProductoService`                 | `ProductoController`    | "El codigo [X] ya esta en uso por otro producto."                         | RN4                 |
| `PDOException` (no esperada)   | PDO / Repository                  | Controller (catch Throwable) | "Error interno. Intente de nuevo." (sin exponer detalles tecnicos)    | RN5, RNF1           |

**Flujo de propagacion:**

```
Service (lanza excepcion)
  → Controller (captura, extrae mensaje)
    → Vista (emite mensaje escapado con htmlspecialchars)   [RNF6]
```

Ningun detalle tecnico (traza de pila, nombre de clase, SQL) llega al navegador. Los mensajes de error se pasan a la vista como variables escalares de tipo `string`.

---

## 7. Trazabilidad clase -> RF / RN

| Clase / archivo             | RF / RN que implementa o soporta                                             |
|-----------------------------|------------------------------------------------------------------------------|
| `Producto` (Model)          | RF1, RF2                                                                     |
| `Venta` (Model)             | RF3, RF4                                                                     |
| `ProductoRepository`        | RF1, RF2 · RNF4 (PDO preparado)                                              |
| `VentaRepository`           | RF3, RF4 · RNF4 (PDO preparado)                                              |
| `ProductoService`           | RF1 · RN3 (precio >= 0) · RN4 (codigo unico)                                 |
| `VentaService`              | RF3 · RN1 (cantidad > 0) · RN2 (stock suficiente) · RN5 (transaccion atomica)|
| `ProductoController`        | RF1, RF2 · RNF7 (validacion en servidor)                                     |
| `VentaController`           | RF3, RF4 · RNF7 (validacion en servidor)                                     |
| `views/productos/`          | RF1, RF2 · RNF3 (alerta stock bajo) · RNF6 (htmlspecialchars)               |
| `views/ventas/`             | RF3, RF4 · RNF6 (htmlspecialchars)                                           |
| `ValidacionException`       | RN1, RN3 · RNF7                                                              |
| `StockInsuficienteException`| RN2                                                                          |
| `CodigoDuplicadoException`  | RN4                                                                          |
| `config/database.php`       | RNF5 (credenciales en .env) · RNF4 (instancia PDO centralizada)              |
