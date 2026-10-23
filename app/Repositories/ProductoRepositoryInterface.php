<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Producto;

interface ProductoRepositoryInterface
{
    /**
     * @return Producto[]
     */
    public function findAll(): array;

    public function findById(int $id): ?Producto;

    public function findByCodigo(string $codigo): ?Producto;

    public function findByIdForUpdate(int $id): ?Producto;

    public function save(
        string $codigo,
        string $nombre,
        string $categoria,
        string $precio_venta,
        int $stock,
        int $stock_minimo,
    ): void;

    public function updateStock(int $id, int $nuevoStock): void;
}
