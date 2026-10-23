<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Producto;
use PDO;

final class ProductoRepository implements ProductoRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return Producto[]
     */
    public function findAll(): array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, codigo, nombre, categoria, precio_venta, stock, stock_minimo
             FROM producto ORDER BY nombre, id'
        );
        $statement->execute();

        return array_map(
            fn (array $row): Producto => $this->hydrate($row),
            $statement->fetchAll()
        );
    }

    public function findById(int $id): ?Producto
    {
        $statement = $this->pdo->prepare(
            'SELECT id, codigo, nombre, categoria, precio_venta, stock, stock_minimo
             FROM producto WHERE id = :id'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function findByCodigo(string $codigo): ?Producto
    {
        $statement = $this->pdo->prepare(
            'SELECT id, codigo, nombre, categoria, precio_venta, stock, stock_minimo
             FROM producto WHERE codigo = :codigo'
        );
        $statement->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $statement->execute();
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function findByIdForUpdate(int $id): ?Producto
    {
        $statement = $this->pdo->prepare(
            'SELECT id, codigo, nombre, categoria, precio_venta, stock, stock_minimo
             FROM producto WHERE id = :id FOR UPDATE'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return $row === false ? null : $this->hydrate($row);
    }

    public function save(
        string $codigo,
        string $nombre,
        string $categoria,
        string $precio_venta,
        int $stock,
        int $stock_minimo,
    ): void {
        $statement = $this->pdo->prepare(
            'INSERT INTO producto (codigo, nombre, categoria, precio_venta, stock, stock_minimo)
             VALUES (:codigo, :nombre, :categoria, :precio_venta, :stock, :stock_minimo)'
        );
        $statement->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $statement->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $statement->bindValue(':categoria', $categoria, PDO::PARAM_STR);
        $statement->bindValue(':precio_venta', $precio_venta, PDO::PARAM_STR);
        $statement->bindValue(':stock', $stock, PDO::PARAM_INT);
        $statement->bindValue(':stock_minimo', $stock_minimo, PDO::PARAM_INT);
        $statement->execute();
    }

    public function updateStock(int $id, int $nuevoStock): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE producto SET stock = :stock WHERE id = :id'
        );
        $statement->bindValue(':stock', $nuevoStock, PDO::PARAM_INT);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() !== 1) {
            throw new \RuntimeException('No se pudo actualizar el stock del producto.');
        }
    }

    /**
     * @param array<string, int|string> $row
     */
    private function hydrate(array $row): Producto
    {
        return new Producto(
            (int) $row['id'],
            (string) $row['codigo'],
            (string) $row['nombre'],
            (string) $row['categoria'],
            (string) $row['precio_venta'],
            (int) $row['stock'],
            (int) $row['stock_minimo'],
        );
    }
}
