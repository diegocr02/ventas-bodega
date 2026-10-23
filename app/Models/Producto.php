<?php
declare(strict_types=1);

namespace App\Models;

final class Producto
{
    public function __construct(
        public readonly int $id,
        public readonly string $codigo,
        public readonly string $nombre,
        public readonly string $categoria,
        public readonly string $precio_venta,
        public readonly int $stock,
        public readonly int $stock_minimo,
    ) {
    }

    public function tieneStockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}
