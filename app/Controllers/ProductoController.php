<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Exceptions\CodigoDuplicadoException;
use App\Exceptions\ValidacionException;
use App\Services\ProductoService;
use Throwable;

final class ProductoController
{
    public function __construct(private readonly ProductoService $service)
    {
    }

    public function lista(): void
    {
        $this->requiereMetodo('GET');
        $this->render('productos/lista.php', [
            'productos' => $this->service->listar(),
            'mensaje' => $this->getString('mensaje'),
            'tipoMensaje' => 'success',
        ], 'Productos');
    }

    public function nuevo(): void
    {
        $this->requiereMetodo('GET');
        $this->render('productos/nuevo.php', [
            'valores' => [],
            'mensaje' => '',
            'tipoMensaje' => 'error',
        ], 'Nuevo producto');
    }

    public function guardar(): void
    {
        $this->requiereMetodo('POST');
        $valores = [
            'codigo' => '',
            'nombre' => '',
            'categoria' => '',
            'precio_venta' => '',
            'stock' => '',
            'stock_minimo' => '',
        ];

        try {
            foreach (array_keys($valores) as $campo) {
                $valores[$campo] = $this->postString($campo);
            }
            $this->service->registrar(
                $valores['codigo'],
                $valores['nombre'],
                $valores['categoria'],
                $valores['precio_venta'],
                $this->parseUnsignedInt($valores['stock'], 'El stock'),
                $this->parseUnsignedInt($valores['stock_minimo'], 'El stock mínimo')
            );
            header('Location: ?action=productos.lista&mensaje=Producto+registrado+correctamente');
            exit;
        } catch (CodigoDuplicadoException | ValidacionException $exception) {
            $this->render('productos/nuevo.php', [
                'valores' => $valores,
                'mensaje' => $exception->getMessage(),
                'tipoMensaje' => 'error',
            ], 'Nuevo producto');
        } catch (Throwable $exception) {
            error_log((string) $exception);
            http_response_code(500);
            $this->render('productos/nuevo.php', [
                'valores' => $valores,
                'mensaje' => 'No se pudo registrar el producto. Intente nuevamente.',
                'tipoMensaje' => 'error',
            ], 'Nuevo producto');
        }
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
            throw new ValidacionException("{$label} debe ser un número entero no negativo.");
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
