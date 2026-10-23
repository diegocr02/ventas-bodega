<?php
declare(strict_types=1);

use App\Controllers\ProductoController;
use App\Controllers\VentaController;
use App\Repositories\ProductoRepository;
use App\Repositories\VentaRepository;
use App\Security\Csrf;
use App\Services\ProductoService;
use App\Services\VentaService;

define('APP_ROOT', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = APP_ROOT . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR
        . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

try {
    session_set_cookie_params([
        'httponly' => true,
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    if (session_start() !== true) {
        throw new RuntimeException('No se pudo iniciar la sesión.');
    }

    require APP_ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
    $pdo = createDatabaseConnection(APP_ROOT);
    $productoRepository = new ProductoRepository($pdo);
    $ventaRepository = new VentaRepository($pdo);
    $productoService = new ProductoService($productoRepository);
    $ventaService = new VentaService($pdo, $productoRepository, $ventaRepository);
    $productoController = new ProductoController($productoService);
    $ventaController = new VentaController($ventaService, $productoService);

    $action = $_GET['action'] ?? 'productos.lista';
    if (!is_string($action)) {
        http_response_code(400);
        throw new RuntimeException('La acción solicitada no es válida.');
    }

    switch ($action) {
        case 'productos.lista':
            $productoController->lista();
            break;
        case 'productos.nuevo':
            $productoController->nuevo();
            break;
        case 'productos.guardar':
            $productoController->guardar();
            break;
        case 'ventas.nueva':
            $ventaController->nueva();
            break;
        case 'ventas.registrar':
            $ventaController->registrar();
            break;
        case 'ventas.delDia':
            $ventaController->delDia();
            break;
        default:
            http_response_code(404);
            echo 'Página no encontrada.';
    }
} catch (Throwable $exception) {
    error_log((string) $exception);
    if (http_response_code() < 400) {
        http_response_code(500);
    }
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }
    echo '<!doctype html><html lang="es"><meta charset="utf-8">'
        . '<title>Error</title><p>Error interno. Intente de nuevo.</p></html>';
}
