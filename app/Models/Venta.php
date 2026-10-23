<?php
declare(strict_types=1);

namespace App\Models;

final class Venta
{
    public function __construct(
        public int $id,
        public int $producto_id,
        public string $nombre_producto,
        public int $cantidad,
        public string $precio_unitario,
        public string $total,
        public string $fecha_hora,
    ) {
    }
}
