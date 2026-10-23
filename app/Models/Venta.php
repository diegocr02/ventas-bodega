<?php
declare(strict_types=1);

namespace App\Models;

final class Venta
{
    public function __construct(
        public readonly int $id,
        public readonly int $producto_id,
        public readonly string $nombre_producto,
        public readonly int $cantidad,
        public readonly string $precio_unitario,
        public readonly string $total,
        public readonly string $fecha_hora,
    ) {
    }
}
