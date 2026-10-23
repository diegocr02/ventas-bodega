<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\CodigoDuplicadoException;
use App\Exceptions\ValidacionException;
use App\Models\Producto;
use App\Repositories\ProductoRepositoryInterface;
use PDOException;

final class ProductoService
{
    public function __construct(private ProductoRepositoryInterface $repo)
    {
    }

    public function registrar(
        string $codigo,
        string $nombre,
        string $categoria,
        string $precio_venta,
        int $stock,
        int $stock_minimo,
    ): void {
        $codigo = trim($codigo);
        $nombre = trim($nombre);
        $categoria = trim($categoria);
        $precio_venta = trim($precio_venta);

        if ($codigo === '' || mb_strlen($codigo) > 20) {
            throw new ValidacionException('El código es obligatorio y debe tener hasta 20 caracteres.');
        }
        if ($nombre === '' || mb_strlen($nombre) > 120) {
            throw new ValidacionException('El nombre es obligatorio y debe tener hasta 120 caracteres.');
        }
        if ($categoria === '' || mb_strlen($categoria) > 60) {
            throw new ValidacionException('La categoría es obligatoria y debe tener hasta 60 caracteres.');
        }
        if (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/', $precio_venta)) {
            throw new ValidacionException(
                'El precio debe ser un importe no negativo con hasta dos decimales.'
            );
        }
        if ($stock < 0 || $stock > 4294967295) {
            throw new ValidacionException('El stock debe ser un entero no negativo válido.');
        }
        if ($stock_minimo < 0 || $stock_minimo > 4294967295) {
            throw new ValidacionException('El stock mínimo debe ser un entero no negativo válido.');
        }
        if ($this->repo->findByCodigo($codigo) !== null) {
            throw new CodigoDuplicadoException("El código {$codigo} ya está en uso.");
        }

        [$parteEntera, $parteDecimal] = array_pad(explode('.', $precio_venta, 2), 2, '');
        $precioNormalizado = $parteEntera . '.' . str_pad($parteDecimal, 2, '0');
        try {
            $this->repo->save(
                $codigo,
                $nombre,
                $categoria,
                $precioNormalizado,
                $stock,
                $stock_minimo
            );
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000' && $this->repo->findByCodigo($codigo) !== null) {
                throw new CodigoDuplicadoException("El código {$codigo} ya está en uso.", 0, $exception);
            }

            throw $exception;
        }
    }

    /**
     * @return Producto[]
     */
    public function listar(): array
    {
        return $this->repo->findAll();
    }

    /**
     * Devuelve únicamente productos cuyo stock no supera el mínimo configurado.
     *
     * @return Producto[]
     */
    public function listarStockBajo(): array
    {
        return array_values(array_filter(
            $this->repo->findAll(),
            static fn (Producto $producto): bool => $producto->tieneStockBajo()
        ));
    }
}
