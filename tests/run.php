<?php
declare(strict_types=1);

namespace {
    use App\Exceptions\StockInsuficienteException;
    use App\Exceptions\ValidacionException;
    use App\Models\Producto;
    use App\Models\Venta;
    use App\Repositories\ProductoRepositoryInterface;
    use App\Repositories\VentaRepositoryInterface;
    use App\Services\ProductoService;
    use App\Services\VentaService;

    define('PROJECT_ROOT', dirname(__DIR__));

    spl_autoload_register(static function (string $class): void {
        $prefix = 'App\\';
        if (!str_starts_with($class, $prefix)) {
            return;
        }

        $path = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR
            . str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix))) . '.php';
        if (is_file($path)) {
            require $path;
        }
    });

    /**
     * PDO de prueba que registra el estado de la transacción sin conectarse a una BD.
     */
    final class MemoryPdo extends PDO
    {
        public bool $active = false;
        public int $commits = 0;
        public int $rollbacks = 0;

        public function __construct()
        {
        }

        /**
         * Inicia una transacción simulada.
         */
        public function beginTransaction(): bool
        {
            $this->active = true;

            return true;
        }

        /**
         * Confirma la transacción simulada.
         */
        public function commit(): bool
        {
            $this->active = false;
            $this->commits++;

            return true;
        }

        /**
         * Revierte la transacción simulada.
         */
        public function rollBack(): bool
        {
            $this->active = false;
            $this->rollbacks++;

            return true;
        }

        /**
         * Indica si la transacción simulada sigue activa.
         */
        public function inTransaction(): bool
        {
            return $this->active;
        }
    }

    /**
     * Repositorio de productos mantenido en memoria para las pruebas de Services.
     */
    final class MemoryProductoRepository implements ProductoRepositoryInterface
    {
        /** @var array<int, Producto> */
        public array $productos = [];

        /**
         * Inicializa el repositorio con productos de prueba.
         *
         * @param Producto[] $productos
         */
        public function __construct(array $productos = [])
        {
            foreach ($productos as $producto) {
                $this->productos[$producto->id] = $producto;
            }
        }

        /**
         * Devuelve todos los productos guardados en memoria.
         *
         * @return Producto[]
         */
        public function findAll(): array
        {
            return array_values($this->productos);
        }

        /**
         * Busca un producto por identificador o devuelve null si no existe.
         */
        public function findById(int $id): ?Producto
        {
            return $this->productos[$id] ?? null;
        }

        /**
         * Busca un producto por código o devuelve null si no existe.
         */
        public function findByCodigo(string $codigo): ?Producto
        {
            foreach ($this->productos as $producto) {
                if ($producto->codigo === $codigo) {
                    return $producto;
                }
            }

            return null;
        }

        /**
         * Busca el producto bloqueado simulado para la operación de venta.
         */
        public function findByIdForUpdate(int $id): ?Producto
        {
            return $this->findById($id);
        }

        /**
         * Inserta un producto en la colección de prueba.
         */
        public function save(
            string $codigo,
            string $nombre,
            string $categoria,
            string $precio_venta,
            int $stock,
            int $stock_minimo,
        ): void {
            $id = $this->productos === [] ? 1 : max(array_keys($this->productos)) + 1;
            $this->productos[$id] = new Producto(
                $id,
                $codigo,
                $nombre,
                $categoria,
                $precio_venta,
                $stock,
                $stock_minimo
            );
        }

        /**
         * Sustituye el producto para reflejar el nuevo stock.
         */
        public function updateStock(int $id, int $nuevoStock): void
        {
            $producto = $this->findById($id);
            if ($producto === null) {
                throw new RuntimeException('Producto inexistente en repositorio falso.');
            }

            $this->productos[$id] = new Producto(
                $producto->id,
                $producto->codigo,
                $producto->nombre,
                $producto->categoria,
                $producto->precio_venta,
                $nuevoStock,
                $producto->stock_minimo
            );
        }
    }

    /**
     * Repositorio de ventas mantenido en memoria para las pruebas de Services.
     */
    final class MemoryVentaRepository implements VentaRepositoryInterface
    {
        /** @var array<int, array{producto_id:int, cantidad:int, precio_unitario:string, total:string}> */
        public array $ventas = [];

        /**
         * Guarda los datos de venta en memoria.
         */
        public function save(
            int $producto_id,
            int $cantidad,
            string $precio_unitario,
            string $total,
        ): void {
            $this->ventas[] = [
                'producto_id' => $producto_id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precio_unitario,
                'total' => $total,
            ];
        }

        /**
         * Devuelve las ventas de prueba.
         *
         * @return Venta[]
         */
        public function findByFecha(string $fecha): array
        {
            return [];
        }

        /**
         * Devuelve cero como total diario de prueba.
         */
        public function sumTotalByFecha(string $fecha): string
        {
            return '0.00';
        }
    }

    /**
     * Ejecuta una prueba y contabiliza su resultado.
     */
    function runTest(string $name, callable $test, int &$passed, int &$failed): void
    {
        try {
            $test();
            $passed++;
            echo "[OK] {$name}\n";
        } catch (Throwable $exception) {
            $failed++;
            echo "[FALLO] {$name}: {$exception->getMessage()}\n";
        }
    }

    /**
     * Verifica igualdad estricta de dos valores.
     */
    function assertSameValue(mixed $expected, mixed $actual, string $message = ''): void
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message !== '' ? $message : sprintf(
                'Se esperaba %s y se obtuvo %s.',
                var_export($expected, true),
                var_export($actual, true)
            ));
        }
    }

    /**
     * Verifica que la operación lance una excepción del tipo esperado.
     *
     * @param class-string<Throwable> $exceptionClass
     */
    function assertThrows(string $exceptionClass, callable $operation): Throwable
    {
        try {
            $operation();
        } catch (Throwable $exception) {
            if ($exception instanceof $exceptionClass) {
                return $exception;
            }

            throw new RuntimeException(
                "Se esperaba {$exceptionClass}, se recibió " . $exception::class . '.',
                0,
                $exception
            );
        }

        throw new RuntimeException("Se esperaba la excepción {$exceptionClass}.");
    }

    /**
     * Crea un producto para usarlo como dato aislado de prueba.
     */
    function producto(
        int $id,
        string $precio = '2.50',
        int $stock = 10,
        int $minimo = 3,
    ): Producto {
        return new Producto($id, "P{$id}", "Producto {$id}", 'Prueba', $precio, $stock, $minimo);
    }

    $passed = 0;
    $failed = 0;

    runTest('venta válida calcula total y descuenta stock', static function (): void {
        $pdo = new MemoryPdo();
        $productos = new MemoryProductoRepository([producto(1, '2.50', 10)]);
        $ventas = new MemoryVentaRepository();
        $service = new VentaService($pdo, $productos, $ventas);

        $service->registrar(1, 4);

        assertSameValue(6, $productos->findById(1)?->stock);
        assertSameValue('10.00', $ventas->ventas[0]['total']);
        assertSameValue('2.50', $ventas->ventas[0]['precio_unitario']);
        assertSameValue(1, $pdo->commits);
        assertSameValue(0, $pdo->rollbacks);
    }, $passed, $failed);

    foreach ([0, -2] as $cantidad) {
        runTest("rechaza cantidad {$cantidad}", static function () use ($cantidad): void {
            $pdo = new MemoryPdo();
            $productos = new MemoryProductoRepository([producto(1)]);
            $ventas = new MemoryVentaRepository();
            $service = new VentaService($pdo, $productos, $ventas);

            assertThrows(ValidacionException::class, static fn () => $service->registrar(1, $cantidad));
            assertSameValue(10, $productos->findById(1)?->stock);
            assertSameValue([], $ventas->ventas);
            assertSameValue(0, $pdo->commits);
        }, $passed, $failed);
    }

    runTest('stock insuficiente rechaza la venta sin descontar existencias', static function (): void {
        $pdo = new MemoryPdo();
        $productos = new MemoryProductoRepository([producto(1, '2.50', 3)]);
        $ventas = new MemoryVentaRepository();
        $service = new VentaService($pdo, $productos, $ventas);

        assertThrows(StockInsuficienteException::class, static fn () => $service->registrar(1, 4));
        assertSameValue(3, $productos->findById(1)?->stock);
        assertSameValue([], $ventas->ventas);
        assertSameValue(1, $pdo->rollbacks);
    }, $passed, $failed);

    runTest('producto inexistente rechaza la venta', static function (): void {
        $pdo = new MemoryPdo();
        $productos = new MemoryProductoRepository();
        $ventas = new MemoryVentaRepository();
        $service = new VentaService($pdo, $productos, $ventas);

        assertThrows(ValidacionException::class, static fn () => $service->registrar(99, 1));
        assertSameValue([], $ventas->ventas);
        assertSameValue(1, $pdo->rollbacks);
    }, $passed, $failed);

    runTest('producto con precio negativo rechaza la venta', static function (): void {
        $pdo = new MemoryPdo();
        $productos = new MemoryProductoRepository([producto(1, '-1.00', 10)]);
        $ventas = new MemoryVentaRepository();
        $service = new VentaService($pdo, $productos, $ventas);

        assertThrows(ValidacionException::class, static fn () => $service->registrar(1, 1));
        assertSameValue(10, $productos->findById(1)?->stock);
        assertSameValue([], $ventas->ventas);
        assertSameValue(1, $pdo->rollbacks);
    }, $passed, $failed);

    runTest('listado de stock bajo devuelve solo productos en o debajo del mínimo', static function (): void {
        $repository = new MemoryProductoRepository([
            producto(1, '2.50', 2, 3),
            producto(2, '2.50', 3, 3),
            producto(3, '2.50', 4, 3),
        ]);
        $service = new ProductoService($repository);
        $ids = array_map(
            static fn (Producto $item): int => $item->id,
            $service->listarStockBajo()
        );

        assertSameValue([1, 2], $ids);
    }, $passed, $failed);

    echo "\nResultado: {$passed} correctas; {$failed} fallidas.\n";
    exit($failed === 0 ? 0 : 1);
}
