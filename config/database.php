<?php
declare(strict_types=1);

function createDatabaseConnection(string $projectRoot): \PDO
{
    $environmentFile = $projectRoot . DIRECTORY_SEPARATOR . '.env';
    if (!is_file($environmentFile) || !is_readable($environmentFile)) {
        throw new \RuntimeException('No se pudo leer el archivo .env.');
    }

    $config = parse_ini_file($environmentFile, false, INI_SCANNER_RAW);
    if ($config === false) {
        throw new \RuntimeException('El archivo .env tiene un formato inválido.');
    }

    foreach (['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'] as $key) {
        if (!array_key_exists($key, $config)) {
            throw new \RuntimeException("Falta la variable {$key} en .env.");
        }
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $config['DB_HOST'],
        $config['DB_NAME']
    );

    return new \PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}
