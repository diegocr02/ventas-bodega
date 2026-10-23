<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\StockInsuficienteException;
use App\Exceptions\ValidacionException;
use App\Models\Venta;
use App\Repositories\ProductoRepositoryInterface;
use App\Repositories\VentaRepositoryInterface;
use PDO;
use Throwable;

final class VentaService
{
    public function __construct(
        private PDO $pdo,
        private ProductoRepositoryInterface $productoRepo,
        private VentaRepositoryInterface $ventaRepo,
    ) {
    }

    public function registrar(int $productoId, int $cantidad): void
    {
        if ($productoId <= 0) {
            throw new ValidacionException('Seleccione un producto válido.');
        }
        if ($cantidad <= 0) {
            throw new ValidacionException('La cantidad debe ser mayor que cero.');
        }

        $this->pdo->beginTransaction();

        try {
            $producto = $this->productoRepo->findByIdForUpdate($productoId);
            if ($producto === null) {
                throw new ValidacionException('El producto seleccionado no existe.');
            }
            if ($producto->stock < $cantidad) {
                throw new StockInsuficienteException(
                    $producto->stock,
                    "Stock insuficiente. Disponible: {$producto->stock} unidades."
                );
            }
            if (!preg_match('/^\d{1,8}\.\d{2}$/', $producto->precio_venta)) {
                throw new ValidacionException('El precio del producto no puede ser negativo o inválido.');
            }

            $centavosUnitarios = (int) str_replace('.', '', $producto->precio_venta);
            $maximoCentavos = 9999999999;
            if ($centavosUnitarios > 0 && $cantidad > intdiv($maximoCentavos, $centavosUnitarios)) {
                throw new ValidacionException('El total de la venta excede el límite permitido.');
            }
            $totalCentavos = $centavosUnitarios * $cantidad;
            $total = intdiv($totalCentavos, 100) . '.' . str_pad(
                (string) ($totalCentavos % 100),
                2,
                '0',
                STR_PAD_LEFT
            );

            $this->ventaRepo->save(
                $productoId,
                $cantidad,
                $producto->precio_venta,
                $total
            );
            $this->productoRepo->updateStock($productoId, $producto->stock - $cantidad);
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * @return Venta[]
     */
    public function listarDelDia(): array
    {
        return $this->ventaRepo->findByFecha(date('Y-m-d'));
    }

    public function totalDelDia(): string
    {
        return $this->ventaRepo->sumTotalByFecha(date('Y-m-d'));
    }
}
