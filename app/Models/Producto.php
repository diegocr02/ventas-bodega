<?php
declare(strict_types=1);

namespace App\Models;

final class Producto
{
    public function __construct(
        public int $id,
        public string $codigo,
        public string $nombre,
        public string $categoria,
        public string $precio_venta,
        public int $stock,
        public int $stock_minimo,
    ) {
    }

    public function tieneStockBajo(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}
