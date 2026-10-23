<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\StockInsuficienteException;
use App\Exceptions\ValidacionException;
use App\Services\ProductoService;
use App\Services\VentaService;
use Throwable;

final class VentaController
{
    public function __construct(
        private readonly VentaService $service,
        private readonly ProductoService $productoService,
    ) {
    }

    public function nueva(): void
    {
        $this->requiereMetodo('GET');
        $this->render('ventas/nueva.php', [
            'productos' => $this->productoService->listar(),
            'valores' => [],
            'mensaje' => '',
            'tipoMensaje' => 'error',
        ], 'Registrar venta');
    }

    public function registrar(): void
    {
        $this->requiereMetodo('POST');
        $valores = [
            'producto_id' => '',
            'cantidad' => '',
        ];

        try {
            $valores['producto_id'] = $this->postString('producto_id');
            $valores['cantidad'] = $this->postString('cantidad');
            $productoId = $this->parseUnsignedInt($valores['producto_id'], 'El producto');
            $cantidad = $this->parseUnsignedInt($valores['cantidad'], 'La cantidad');
            $this->service->registrar($productoId, $cantidad);
            header('Location: ?action=ventas.delDia&mensaje=Venta+registrada+correctamente');
            exit;
        } catch (StockInsuficienteException | ValidacionException $exception) {
            $this->render('ventas/nueva.php', [
                'productos' => $this->productoService->listar(),
                'valores' => $valores,
                'mensaje' => $exception->getMessage(),
                'tipoMensaje' => 'error',
            ], 'Registrar venta');
        } catch (Throwable $exception) {
            error_log((string) $exception);
            http_response_code(500);
            $this->render('ventas/nueva.php', [
                'productos' => $this->productoService->listar(),
                'valores' => $valores,
                'mensaje' => 'No se pudo registrar la venta. Intente nuevamente.',
                'tipoMensaje' => 'error',
            ], 'Registrar venta');
        }
    }

    public function delDia(): void
    {
        $this->requiereMetodo('GET');
        $this->render('ventas/del_dia.php', [
            'ventas' => $this->service->listarDelDia(),
            'totalDia' => $this->service->totalDelDia(),
            'mensaje' => $this->getString('mensaje'),
            'tipoMensaje' => 'success',
        ], 'Ventas del día');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function render(string $view, array $data, string $title): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require APP_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view;
        $content = (string) ob_get_clean();
        require APP_ROOT . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'layout.php';
    }

    private function postString(string $key): string
    {
        $value = $_POST[$key] ?? null;
        if (!is_string($value)) {
            throw new ValidacionException("El campo {$key} es obligatorio y debe ser texto.");
        }

        return trim($value);
    }

    private function parseUnsignedInt(string $value, string $label): int
    {
        $parsed = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 0, 'max_range' => 4294967295],
        ]);
        if ($parsed === false) {
            throw new ValidacionException("{$label} debe ser un número entero válido.");
        }

        return $parsed;
    }

    private function getString(string $key): string
    {
        $value = $_GET[$key] ?? '';

        return is_string($value) ? $value : '';
    }

    private function requiereMetodo(string $metodo): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== $metodo) {
            http_response_code(405);
            header('Allow: ' . $metodo);
            echo 'Método no permitido.';
            exit;
        }
    }
}
