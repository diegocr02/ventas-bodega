<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Venta;
use PDO;

final class VentaRepository implements VentaRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function save(
        int $producto_id,
        int $cantidad,
        string $precio_unitario,
        string $total,
    ): void {
        $statement = $this->pdo->prepare(
            'INSERT INTO venta (producto_id, cantidad, precio_unitario, total)
             VALUES (:producto_id, :cantidad, :precio_unitario, :total)'
        );
        $statement->bindValue(':producto_id', $producto_id, PDO::PARAM_INT);
        $statement->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $statement->bindValue(':precio_unitario', $precio_unitario, PDO::PARAM_STR);
        $statement->bindValue(':total', $total, PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * @return Venta[]
     */
    public function findByFecha(string $fecha): array
    {
        $siguienteDia = (new \DateTimeImmutable($fecha))->modify('+1 day')->format('Y-m-d');
        $statement = $this->pdo->prepare(
            'SELECT v.id, v.producto_id, p.nombre AS nombre_producto, v.cantidad,
                    v.precio_unitario, v.total, v.fecha_hora
             FROM venta v
             INNER JOIN producto p ON p.id = v.producto_id
             WHERE v.fecha_hora >= :inicio AND v.fecha_hora < :fin
             ORDER BY v.fecha_hora DESC, v.id DESC'
        );
        $statement->bindValue(':inicio', $fecha . ' 00:00:00', PDO::PARAM_STR);
        $statement->bindValue(':fin', $siguienteDia . ' 00:00:00', PDO::PARAM_STR);
        $statement->execute();

        return array_map(
            static fn (array $row): Venta => new Venta(
                (int) $row['id'],
                (int) $row['producto_id'],
                (string) $row['nombre_producto'],
                (int) $row['cantidad'],
                (string) $row['precio_unitario'],
                (string) $row['total'],
                (string) $row['fecha_hora'],
            ),
            $statement->fetchAll()
        );
    }

    public function sumTotalByFecha(string $fecha): string
    {
        $siguienteDia = (new \DateTimeImmutable($fecha))->modify('+1 day')->format('Y-m-d');
        $statement = $this->pdo->prepare(
            'SELECT COALESCE(SUM(total), 0.00)
             FROM venta
             WHERE fecha_hora >= :inicio AND fecha_hora < :fin'
        );
        $statement->bindValue(':inicio', $fecha . ' 00:00:00', PDO::PARAM_STR);
        $statement->bindValue(':fin', $siguienteDia . ' 00:00:00', PDO::PARAM_STR);
        $statement->execute();

        return (string) $statement->fetchColumn();
    }
}
