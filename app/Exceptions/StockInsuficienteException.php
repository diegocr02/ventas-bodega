<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class StockInsuficienteException extends RuntimeException
{
    public function __construct(
        private readonly int $stockDisponible,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getStockDisponible(): int
    {
        return $this->stockDisponible;
    }
}
