<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Venta;

interface VentaRepositoryInterface
{
    public function save(
        int $producto_id,
        int $cantidad,
        string $precio_unitario,
        string $total,
    ): void;

    /**
     * @return Venta[]
     */
    public function findByFecha(string $fecha): array;

    public function sumTotalByFecha(string $fecha): string;
}
